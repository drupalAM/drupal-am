<?php

namespace Drupal\entityqueue\Form;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface;
use Drupal\Core\Entity\EntityTypeRepositoryInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Form\FormStateInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base form for entity queue edit forms.
 */
class EntityQueueForm extends BundleEntityFormBase {

  /**
   * The entity being used by this form.
   *
   * @var \Drupal\entityqueue\EntityQueueInterface
   */
  protected $entity;

  /**
   * The entity type repository.
   *
   * @var \Drupal\Core\Entity\EntityTypeRepositoryInterface
   */
  protected $entityTypeRepository;

  /**
   * The entity queue handler plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $entityQueueHandlerManager;

  /**
   * Selection manager service.
   *
   * @var \Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface
   */
  protected $selectionManager;

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
      $container->get('entity_type.repository'),
      $container->get('plugin.manager.entityqueue.handler'),
      $container->get('plugin.manager.entity_reference_selection'),
      $container->get('logger.factory')->get('entityqueue')
    );
  }

  /**
   * Constructs a EntityQueueForm.
   *
   * @param \Drupal\Core\Entity\EntityTypeRepositoryInterface
   *   The entity type repository.
   * @param \Drupal\Component\Plugin\PluginManagerInterface
   *   The entity queue handler plugin manager.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(EntityTypeRepositoryInterface $entity_type_repository, PluginManagerInterface $entity_queue_handler_manager, SelectionPluginManagerInterface $selection_manager, LoggerInterface $logger) {
    $this->entityTypeRepository = $entity_type_repository;
    $this->entityQueueHandlerManager = $entity_queue_handler_manager;
    $this->selectionManager = $selection_manager;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $queue = $this->entity;

    // Default to nodes as the queue target entity type.
    $target_entity_type_id = $queue->getTargetEntityTypeId() ?: 'node';

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 32,
      '#size' => 32,
      '#default_value' => $queue->label(),
      '#description' => $this->t('The human-readable name of this entity queue. This name must be unique.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $queue->id(),
      '#machine_name' => [
        'exists' => '\Drupal\entityqueue\Entity\EntityQueue::load',
      ],
      '#disabled' => !$queue->isNew(),
    ];

    $handlers = $this->entityQueueHandlerManager->getAllEntityQueueHandlers();
    $form['handler'] = [
      '#type' => 'radios',
      '#title' => $this->t('Type'),
      '#options' => $handlers,
      '#default_value' => $queue->getHandler(),
      '#required' => TRUE,
      '#disabled' => !$queue->isNew(),
    ];

    $form['settings'] = [
      '#type' => 'vertical_tabs',
    ];

    $form['queue_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Queue settings'),
      '#open' => TRUE,
      '#tree' => TRUE,
      '#group' => 'settings',
    ];
    $form['queue_settings']['size'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['form--inline', 'clearfix']],
      '#process' => [
        [EntityReferenceItem::class, 'formProcessMergeParent']
      ],
    ];
    $form['queue_settings']['size']['min_size'] = [
      '#type' => 'number',
      '#size' => 2,
      '#default_value' => $queue->getMinimumSize(),
      '#field_prefix' => $this->t('Restrict this queue to a minimum of'),
    ];
    $form['queue_settings']['size']['max_size'] = [
      '#type' => 'number',
      '#default_value' => $queue->getMaximumSize(),
      '#field_prefix' => $this->t('and a maximum of'),
      '#field_suffix' => $this->t('items.'),
    ];
    $form['queue_settings']['act_as_queue'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Act as queue'),
      '#default_value' => $queue->getActAsQueue(),
      '#description' => $this->t('When enabled, adding more than the maximum number of items will remove extra items from the top of the queue.'),
      '#states' => [
        'invisible' => [
          ':input[name="queue_settings[max_size]"]' => ['value' => 0],
        ],
      ],
    ];
    $form['queue_settings']['reverse_in_admin'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Reverse order in admin view'),
      '#default_value' => $queue->getReverseInAdmin(),
      '#description' => $this->t('Ordinarily queues are arranged with the front of the queue (where items will be removed) on top and the back (where items will be added) on the bottom. If checked, this will display the queue such that items will be added to the top and removed from the bottom.'),
    ];

    // We have to duplicate all the code from
    // \Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem::fieldSettingsForm()
    // because field settings forms are not easily embeddable.
    $form['entity_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Entity settings'),
      '#open' => TRUE,
      '#tree' => TRUE,
      '#group' => 'settings',
      '#weight' => -1,
    ];

    // Get all selection plugins for this entity type.
    $selection_plugins = $this->selectionManager->getSelectionGroups($target_entity_type_id);
    $selection_handlers_options = [];
    foreach (array_keys($selection_plugins) as $selection_group_id) {
      // We only display base plugins (e.g. 'default', 'views', ...) and not
      // entity type specific plugins (e.g. 'default:node', 'default:user',
      // ...).
      if (array_key_exists($selection_group_id, $selection_plugins[$selection_group_id])) {
        $selection_handlers_options[$selection_group_id] = Html::escape($selection_plugins[$selection_group_id][$selection_group_id]['label']);
      }
      elseif (array_key_exists($selection_group_id . ':' . $target_entity_type_id, $selection_plugins[$selection_group_id])) {
        $selection_group_plugin = $selection_group_id . ':' . $target_entity_type_id;
        $selection_handlers_options[$selection_group_plugin] = Html::escape($selection_plugins[$selection_group_id][$selection_group_plugin]['base_plugin_label']);
      }
    }
    ksort($selection_handlers_options);

    $form['entity_settings']['settings'] = [
      '#type' => 'container',
      '#process' => [
        [EntityReferenceItem::class, 'fieldSettingsAjaxProcess'],
        [EntityReferenceItem::class, 'formProcessMergeParent']
      ],
      '#element_validate' => [[get_class($this), 'entityReferenceSelectionSettingsValidate']],
    ];

    // @todo It should be up to the queue handler to determine what entity types
    // are queue-able.
    $form['entity_settings']['settings']['target_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type of items to queue'),
      '#options' => $this->entityTypeRepository->getEntityTypeLabels(TRUE),
      '#default_value' => $target_entity_type_id,
      '#required' => TRUE,
      '#disabled' => !$queue->isNew(),
      '#size' => 1,
      '#ajax' => TRUE,
      '#limit_validation_errors' => [],
    ];

    $form['entity_settings']['settings']['handler'] = [
      '#type' => 'select',
      '#title' => $this->t('Reference method'),
      '#options' => $selection_handlers_options,
      '#default_value' => $queue->getEntitySettings()['handler'],
      '#required' => TRUE,
      '#ajax' => TRUE,
      '#limit_validation_errors' => [],
    ];
    $form['entity_settings']['settings']['handler_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Change handler'),
      '#limit_validation_errors' => [],
      '#attributes' => [
        'class' => ['js-hide'],
      ],
      '#submit' => [[EntityReferenceItem::class, 'settingsAjaxSubmit']],
    ];

    $form['entity_settings']['settings']['handler_settings'] = [
      '#type' => 'container',
    ];

    $selection_handler = $this->selectionManager->getInstance($queue->getEntitySettings());
    $form['entity_settings']['settings']['handler_settings'] += $selection_handler->buildConfigurationForm([], $form_state);

    // For entityqueue's purposes, the 'target_bundles' setting of the 'default'
    // selection handler does not have to be required.
    if (isset($form['entity_settings']['settings']['handler_settings']['target_bundles'])) {
      $form['entity_settings']['settings']['handler_settings']['target_bundles']['#required'] = FALSE;
    }

    return $form;
  }

  /**
   * Form element validation handler; Invokes selection plugin's validation.
   *
   * @param array $form
   *   The form where the settings form is being included in.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state of the (entire) configuration form.
   */
  public static function entityReferenceSelectionSettingsValidate(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\entityqueue\EntityQueueInterface $queue */
    $queue = $form_state->getFormObject()->getEntity();

    $selection_handler = \Drupal::service('plugin.manager.entity_reference_selection')->getInstance($queue->getEntitySettings());

    // @todo Take care of passing the right $form and $form_state structures to
    // the selection validation method. For now, we just have to duplicate the
    // validation of the 'default' selection plugin.
    $selection_handler->validateConfigurationForm($form, $form_state);

    // If no checkboxes were checked for 'target_bundles', store NULL ("all
    // bundles are referenceable") rather than empty array ("no bundle is
    // referenceable".
    if ($form_state->getValue(['entity_settings', 'handler_settings', 'target_bundles']) === []) {
      $form_state->setValue(['entity_settings', 'handler_settings', 'target_bundles'], NULL);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildEntity(array $form, FormStateInterface $form_state) {
    $entity = parent::buildEntity($form, $form_state);
    if ($handler = $entity->get('handler')) {
      $entity->setHandler($handler);
    }
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $queue = $this->entity;
    $status = $queue->save();

    $edit_link = $queue->toLink($this->t('Edit'), 'edit-form')->toString();
    if ($status == SAVED_UPDATED) {
      drupal_set_message($this->t('The entity queue %label has been updated.', ['%label' => $queue->label()]));
      $this->logger->notice('The entity queue %label has been updated.', ['%label' => $queue->label(), 'link' => $edit_link]);
    }
    else {
      drupal_set_message($this->t('The entity queue %label has been added.', ['%label' => $queue->label()]));
      $this->logger->notice('The entity queue %label has been added.', ['%label' => $queue->label(), 'link' =>  $edit_link]);
    }

    $form_state->setRedirectUrl($queue->toUrl('collection'));
  }

}
