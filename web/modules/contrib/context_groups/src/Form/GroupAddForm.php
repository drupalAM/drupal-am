<?php

namespace Drupal\context_groups\Form;

use Drupal\context\ContextManager;
use Drupal\context\Entity\Context;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\StatusMessages;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GroupDeleteForm.
 *
 * @package Drupal\context_groups\Form
 */
class GroupAddForm extends FormBase {

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * The Context module context manager.
   *
   * @var \Drupal\context\ContextManager
   */
  protected $contextManager;

  /**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQuery;
  /**
   * The context where context group will be created.
   *
   * @var Context
   */
  protected $context;

  /**
   * GroupAddForm constructor.
   *
   * @param EntityTypeManager $entityTypeManager
   *   Entity type manager.
   * @param ContextManager $contextManager
   *   Context manager.
   */
  public function __construct(EntityTypeManager $entityTypeManager, ContextManager $contextManager, QueryFactory $entityQuery) {
    $this->entityTypeManager = $entityTypeManager;
    $this->contextManager = $contextManager;
    $this->entityQuery = $entityQuery;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      // Load the service required to construct this class.
      $container->get('entity_type.manager'),
      $container->get('context.manager'),
      $container->get('entity.query')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'context-group-add-group';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->saveGroup($form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $context = NULL, $theme = NULL) {
    $this->context = $this->entityTypeManager->getStorage('context')->load($context);
    // Group name field.
    $form['group_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Group name'),
      '#required' => TRUE,
    ];

    $form['name'] = [
      '#type' => 'machine_name',
      '#title' => $this->t('Machine name'),
      '#machine_name' => [
        'source' => ['group_name'],
        'exists' => [$this, 'groupExists'],
      ],
    ];

    $form['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Class'),
      '#descriptions' => $this->t('Set classes to group. Separate multiple classes with space.'),
    ];

    $form['region'] = [
      '#type' => 'select',
      '#title' => $this->t('Select region'),
      '#options' => $this->getRegionsList($theme),
      '#description' => $this->t('Select the region where this group should be inserted.'),
    ];

    $form['context_id'] = [
      '#type' => 'hidden',
      '#value' => $context,
    ];

    $form['theme'] = [
      '#type' => 'hidden',
      '#value' => $theme,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add group'),
      '#button_type' => 'primary',
      '#ajax' => [
        'callback' => '::submitFormAjax',
        'wrapper' => 'context-group-add-group',
        'effect' => 'fade',
      ],
    ];

    return $form;
  }

  /**
   * Handle when the form is submitted through AJAX.
   *
   * @return AjaxResponse
   *   Return AjaxResponse object.
   */
  public function submitFormAjax(&$form, FormStateInterface $form_state) {
    $contextForm = $this->contextManager->getForm($this->context, 'edit');

    $response = new AjaxResponse();

    if ($form_state->getErrors()) {
      $output[] = StatusMessages::renderMessages(NULL);
      $output[] = $form;

      // Ajax commands.
      $response->addCommand(new RemoveCommand('.messages.messages--error'));
      $response->addCommand(new ReplaceCommand('[id^="context-group-add-group"]', $output));
    }
    else {
      $response->addCommand(new CloseModalDialogCommand());
      $response->addCommand(new ReplaceCommand('#context-reactions', $contextForm['reactions']));
    }

    return $response;
  }

  /**
   * Get list of regions in theme.
   *
   * @param string $theme
   *   Theme name.
   *
   * @return array
   *   List of regions.
   */
  private function getRegionsList($theme) {
    return system_region_list($theme, REGIONS_ALL);
  }

  /**
   * Check to see if group with the specified name already exists .
   *
   * @param string $name
   *   The machine name to check for.
   * @param array $element
   *   Machine name form element.
   * @param FormStateInterface $form_state
   *   Formstate object.
   *
   * @return bool
   *   True or false.
   */
  public function groupExists($name, array $element, FormStateInterface $form_state) {
    // Get all contexts.
    $query = $this->entityQuery->get('context');
    $context_ids = $query->execute();
    $context_storage = $this->entityTypeManager->getStorage('context');

    foreach ($context_ids as $cid) {
      $context = $context_storage->load($cid);
      $groups = $context->getThirdPartySetting('context_groups', $name);
      if (!empty($groups)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Save group to database.
   *
   * @param FormStateInterface $form_state
   *   FormState object.
   */
  private function saveGroup(FormStateInterface $form_state) {
    // Load current context.
    $cid = $form_state->getValue('context_id');
    $context = $this->entityTypeManager->getStorage('context')->load($cid);

    // Get data of context group.
    $data['name'] = $form_state->getValue('name');
    $data['label'] = $form_state->getValue('group_name');
    $data['region'] = $form_state->getValue('region');
    $data['parent'] = '';
    $data['weight'] = 0;
    $data['theme'] = $form_state->getValue('theme');
    $data['class'] = $form_state->getValue('class');

    // Save group to context.
    $context->setThirdPartySetting('context_groups', $form_state->getValue('name'), $data);

    // Save context.
    $context->save();
  }

}
