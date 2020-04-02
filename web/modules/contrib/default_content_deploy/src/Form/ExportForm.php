<?php

namespace Drupal\default_content_deploy\Form;

use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\Messenger;
use Drupal\default_content_deploy\DeployManager;
use Drupal\default_content_deploy\Exporter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Config Form for run DCD deploy in Admin UI.
 */
class ExportForm extends FormBase {

  /**
   * Default Content Deploy Export object.
   *
   * @var \Drupal\default_content_deploy\Exporter
   */
  protected $exporter;

  /**
   * Deploy manager.
   *
   * @var \Drupal\default_content_deploy\DeployManager
   */
  protected $deployManager;

  /**
   * Static cache of bundle information.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $bundleInfo;

  /**
   * ExportForm constructor.
   *
   * @param \Drupal\Core\Messenger\Messenger $messenger
   * @param \Drupal\default_content_deploy\DeployManager $deploy_manager
   * @param \Drupal\default_content_deploy\Exporter $exporter
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $bundle_info
   */
  public function __construct(Messenger $messenger, DeployManager $deploy_manager, Exporter $exporter, EntityTypeBundleInfoInterface $bundle_info) {
    $this->exporter = $exporter;
    $this->messenger = $messenger;
    $this->deployManager = $deploy_manager;
    $this->bundleInfo = $bundle_info;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('default_content_deploy.manager'),
      $container->get('default_content_deploy.exporter'),
      $container->get('entity_type.bundle.info')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dcd_export_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Mode'),
      '#description' => $this->t('Mode of the export (export all content, with reference etc). Read more in the documentation.'),
      '#required' => TRUE,
      '#options' => [
        'default' => $this->t('Content of the entity type'),
        'reference' => $this->t('Content of the entity type with reference'),
        'all' => $this->t('All content'),
      ],
      '#default_value' => 'default',
      '#ajax' => [
        'callback' => [$this, 'processingMode'],
        'wrapper' => 'edit-settings-wrapper',
      ],
    ];

    $form['settings'] = [
      '#type' => 'container',
      '#attributes' => [
        'style' => 'display:block',
      ],
      '#prefix' => '<div id="edit-settings-wrapper">',
      '#suffix' => '</div>',
    ];

    $form['settings']['entity_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Entity type'),
      '#description' => $this->t('Select entity type id for exporting.'),
      '#empty_value' => '',
      '#options' => $this->deployManager->getContentEntityTypes(),
      '#ajax' => [
        'callback' => [$this, 'processingEntityType'],
        'wrapper' => 'edit-entity-bundle-wrapper',
      ],
    ];

    $form['settings']['bundle'] = [
      '#type' => 'select',
      '#title' => $this->t('Bundle'),
      '#description' => $this->t('Select entity type id for exporting. By default all bundles will be exported.'),
      '#empty_value' => '',
      '#options' => [],
      '#prefix' => '<div id="edit-entity-bundle-wrapper">',
      '#suffix' => '</div>',
      '#validated' => TRUE,
      '#default_value' => '',
    ];

    $form['settings']['entity_ids'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Entity IDs'),
      '#placeholder' => $this->t('2,10,11'),
      '#description' => $this->t('Exports an entity with the specified IDs.'),
    ];

    $form['settings']['skip_entities'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Skip entity IDs'),
      '#placeholder' => $this->t('3,5,20'),
      '#description' => $this->t('IDs of entity to skip.'),
    ];

    $form['folder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Folder'),
      '#description' => $this->t('All existing content will be exported to this folder.'),
      '#default_value' => $this->deployManager->getContentFolder(),
      '#states' => [
        'visible' => [
          ':input[name="download"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['force_update'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Force update'),
      '#description' => $this->t('All existing content will be overridden (locally updated default content will be reverted to the state defined on the site).'),
      '#default_value' => FALSE,
      '#states' => [
        'visible' => [
          ':input[name="download"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['download'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Export to a tar archive'),
      '#description' => $this->t('Download an archive with the content of current settings.'),
      '#default_value' => FALSE,
    ];

    $form['export'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export content'),
    ];

    return $form;
  }

  /**
   * Ajax callback for Mode form element.
   *
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function processingMode($form, FormStateInterface $form_state) {
    $mode = $form_state->getValue('mode');

    if ($mode != 'all') {
      $form['settings']['entity_type']['#required'] = TRUE;
    }
    else {
      $form['settings']['entity_type']['#required'] = FALSE;
      $form['settings']['#attributes']['style'] = 'display:none';
    }

    return $form['settings'];
  }

  /**
   * Ajax callback for Entity type form element.
   *
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function processingEntityType($form, FormStateInterface $form_state) {
    $entity_type_id = $form_state->getValue('entity_type');
    $form['settings']['bundle']['#options'] = $this->getBundlesByEntityTypeId($entity_type_id);
    $form['settings']['bundle']['#value'] = '';

    return $form['settings']['bundle'];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $mode = $form_state->getValue('mode');
    $entity_type = $form_state->getValue('entity_type');

    if ($mode != 'all' && $entity_type == NULL) {
      $form_state->setErrorByName('entity_type', $this->t('@name field is required.', [
        '@name' => $form['settings']['entity_type']['#title'],
      ]));
    }
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @throws \Exception
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $mode = $form_state->getValue('mode');
    $folder = $form_state->getValue('folder');
    $force_update = $form_state->getValue('force_update');

    // Set mode of export.
    $this->exporter->setMode($mode);

    // Set Entity type id.
    if ($entity_type = $form_state->getValue('entity_type')) {
      $this->exporter->setEntityTypeId($entity_type);
    }

    // Set Bundle of entity type.
    if ($bundle = $form_state->getValue('bundle')) {
      $this->exporter->setEntityBundle($bundle);
    }

    // Set Entity IDs for export.
    if ($entity_ids = $form_state->getValue('entity_ids')) {
      $entity_ids_array = explode(',', $entity_ids);
      $this->exporter->setEntityIds($entity_ids_array);
    }

    // Set Entity IDs to skip.
    if ($skip_ids = $form_state->getValue('skip_entities')) {
      $skip_ids_array = explode(',', $skip_ids);
      $this->exporter->setSkipEntityIds($skip_ids_array);
    }

    if ($form_state->getValue('download') == FALSE) {
      $this->exporter->setForceUpdate($force_update);
      $this->exporter->setFolder($folder);
      $this->exporter->export();
      $this->addResultMessage();
    }
    else {
      $this->exporter->setForceUpdate(TRUE);
      $this->exporter->setFolder(file_directory_temp() . '/dcd/content');
      $this->exporter->export();

      // Redirect for download archive file.
      $form_state->setRedirect('default_content_deploy.export.download');
    }
  }

  /**
   * Add a message with exporting results.
   */
  private function addResultMessage() {
    $result = $this->exporter->getResult();

    foreach ($result as $entity_type => $value) {
      $this->messenger->addMessage($this->t('Exported @count entities of the "@entity_type" entity type.', [
        '@count' => count($value),
        '@entity_type' => $entity_type, MB_CASE_TITLE,
      ]));
    }

    return $this;
  }

  /**
   * Helper for getting all bundles list of Entity type id.
   *
   * @param $entity_type_id
   *
   * @return array|false
   */
  private function getBundlesByEntityTypeId($entity_type_id) {
    $bundles[''] = $this->t('- None -');

    $list = $this->bundleInfo->getBundleInfo($entity_type_id);
    $bundles += array_combine(array_keys($list), array_column($list, 'label'));

    return $bundles;
  }

}
