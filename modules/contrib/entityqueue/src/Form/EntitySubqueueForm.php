<?php

namespace Drupal\entityqueue\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\inline_entity_form\Plugin\Field\FieldWidget\InlineEntityFormBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the entity subqueue edit forms.
 */
class EntitySubqueueForm extends ContentEntityForm {

  /**
   * The entity being used by this form.
   *
   * @var \Drupal\entityqueue\EntitySubqueueInterface
   */
  protected $entity;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('logger.factory')->get('entityqueue')
    );
  }

  /**
   * Constructs a EntitySubqueueForm.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(EntityManagerInterface $entity_manager, LoggerInterface $logger) {
    parent::__construct($entity_manager);

    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    // Reverse the items in the admin form if the queue uses the 'Reverse order
    // in admin view' option.
    if ($this->entity->getQueue()->getReverseInAdmin()) {
      $subqueue_items = $this->entity->get('items');
      $items_values = $subqueue_items->getValue();
      $subqueue_items->setValue(array_reverse($items_values));
    }

    $form = parent::form($form, $form_state);

    $form['#title'] = $this->t('Edit subqueue %label', ['%label' => $this->entity->label()]);

    // Since the form has ajax buttons, the $wrapper_id will change each time
    // one of those buttons is clicked. Therefore the whole form has to be
    // replaced, otherwise the buttons will have the old $wrapper_id and will
    // only work on the first click.
    if ($form_state->has('subqueue_form_wrapper_id')) {
      $wrapper_id = $form_state->get('subqueue_form_wrapper_id');
    }
    else {
      $wrapper_id = Html::getUniqueId($this->getFormId() . '-wrapper');
    }

    $form_state->set('subqueue_form_wrapper_id', $wrapper_id);
    $form['#prefix'] = '<div id="' . $wrapper_id . '">';
    $form['#suffix'] = '</div>';

    // @todo Consider creating a 'Machine name' field widget.
    $form['name'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\entityqueue\Entity\EntitySubqueue::load',
        'source' => ['title', 'widget', 0, 'value'],
      ),
      '#disabled' => !$this->entity->isNew(),
      '#weight' => -5,
      '#access' => !$this->entity->getQueue()->getHandlerPlugin()->hasAutomatedSubqueues(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);

    $actions['reverse'] = [
      '#type' => 'submit',
      '#value' => $this->t('Reverse'),
      '#submit' => ['::submitAction'],
      '#op' => 'reverse',
      '#ajax' => [
        'callback' => '::subqueueActionAjaxForm',
        'wrapper' => $form_state->get('subqueue_form_wrapper_id'),
      ],
    ];

    $actions['shuffle'] = [
      '#type' => 'submit',
      '#value' => $this->t('Shuffle'),
      '#submit' => ['::submitAction'],
      '#op' => 'shuffle',
      '#ajax' => [
        'callback' => '::subqueueActionAjaxForm',
        'wrapper' => $form_state->get('subqueue_form_wrapper_id'),
      ],
    ];

    $actions['clear'] = [
      '#type' => 'submit',
      '#value' => $this->t('Clear'),
      '#submit' => ['::submitAction'],
      '#op' => 'clear',
      '#ajax' => [
        'callback' => '::subqueueActionAjaxForm',
        'wrapper' => $form_state->get('subqueue_form_wrapper_id'),
      ],
    ];

    return $actions;
  }

  /**
   * Submit callback for the 'reverse', 'shuffle' and 'clear' actions.
   */
  public static function submitAction(array &$form, FormStateInterface $form_state) {
    $trigger = $form_state->getTriggeringElement();
    $op = $trigger['#op'];

    // Check if we have a form element for the 'items' field.
    $path = array_merge($form['#parents'], ['items']);
    $key_exists = NULL;
    NestedArray::getValue($form_state->getValues(), $path, $key_exists);

    if ($key_exists) {
      // Remove any user input for the 'items' element in order to allow the
      // values set below to be applied.
      $user_input = $form_state->getUserInput();
      NestedArray::setValue($user_input, $path, NULL);
      $form_state->setUserInput($user_input);

      $entity = $form_state->getFormObject()->getEntity();
      $items_widget = $form_state->getFormObject()->getFormDisplay($form_state)->getRenderer('items');

      $subqueue_items = $entity->get('items');
      $items_widget->extractFormValues($subqueue_items, $form, $form_state);
      $items_values = $subqueue_items->getValue();

      // Revert the effect of the 'Reverse order in admin view' option.
      if ($entity->getQueue()->getReverseInAdmin()) {
        $items_values = array_reverse($items_values);
      }

      switch ($op) {
        case 'reverse':
          $subqueue_items->setValue(array_reverse($items_values));
          break;

        case 'shuffle':
          shuffle($items_values);
          $subqueue_items->setValue($items_values);
          break;

        case 'clear':
          $subqueue_items->setValue(NULL);
          break;
      }

      // Handle 'inline_entity_form' widgets separately because they have a
      // custom form state storage for the current state of the referenced
      // entities.
      if (\Drupal::moduleHandler()->moduleExists('inline_entity_form') && $items_widget instanceof InlineEntityFormBase) {
        $items_form_element = NestedArray::getValue($form, $path);
        $ief_id = $items_form_element['widget']['#ief_id'];

        $entities = $form_state->get(['inline_entity_form', $ief_id, 'entities']);

        if (isset($entities)) {
          $form_state->set(['inline_entity_form', $ief_id, 'entities'], []);

          switch ($op) {
            case 'reverse':
              $entities = array_reverse($entities);
              break;

            case 'shuffle':
              shuffle($entities);
              break;

            case 'clear':
              $entities = [];
              break;
          }

          foreach ($entities as $delta => $item) {
            $item['_weight'] = $delta;
            $form_state->set(['inline_entity_form', $ief_id, 'entities', $delta], $item);
          }
        }
      }

      $form_state->getFormObject()->setEntity($entity);

      $form_state->setRebuild();
    }
  }

  /**
   * AJAX callback; Returns the entire form element.
   */
  public static function subqueueActionAjaxForm(array &$form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $subqueue = $this->entity;

    // Revert the effect of the 'Reverse order in admin view' option.
    if ($subqueue->getQueue()->getReverseInAdmin()) {
      $subqueue_items = $subqueue->get('items');
      $items_values = $subqueue_items->getValue();
      $subqueue_items->setValue(array_reverse($items_values));
    }

    $status = $subqueue->save();

    $edit_link = $subqueue->toLink($this->t('Edit'), 'edit-form')->toString();
    if ($status == SAVED_UPDATED) {
      drupal_set_message($this->t('The entity subqueue %label has been updated.', ['%label' => $subqueue->label()]));
      $this->logger->notice('The entity subqueue %label has been updated.', ['%label' => $subqueue->label(), 'link' => $edit_link]);
    }
    else {
      drupal_set_message($this->t('The entity subqueue %label has been added.', ['%label' => $subqueue->label()]));
      $this->logger->notice('The entity subqueue %label has been added.', ['%label' => $subqueue->label(), 'link' =>  $edit_link]);
    }

    $queue = $subqueue->getQueue();
    if ($queue->getHandlerPlugin()->supportsMultipleSubqueues()) {
      $form_state->setRedirectUrl($queue->toUrl('subqueue-list'));
    }
    else {
      $form_state->setRedirectUrl($queue->toUrl('collection'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function updateChangedTime(EntityInterface $entity) {
    // @todo Remove this method when Drupal 8.2.x is no longer supported.
    if ($entity->getEntityType()->isSubclassOf(EntityChangedInterface::class)) {
      $entity->setChangedTime(REQUEST_TIME);
    }
  }

}
