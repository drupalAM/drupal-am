<?php

namespace Drupal\default_content_deploy\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\Messenger;
use Drupal\default_content_deploy\DeployManager;
use Drupal\default_content_deploy\Importer;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Config Form for run DCD deploy in Admin UI.
 */
class ImportForm extends FormBase {

  /**
   * Default Content Deploy Importer object.
   *
   * @var \Drupal\default_content_deploy\Importer
   */
  private $importer;

  /**
   * Deploy manager.
   *
   * @var \Drupal\default_content_deploy\DeployManager
   */
  protected $deployManager;

  /**
   * ImportForm constructor.
   *
   * @param \Drupal\default_content_deploy\Importer $importer
   * @param \Drupal\Core\Messenger\Messenger $messenger
   * @param \Drupal\default_content_deploy\DeployManager $deploy_manager
   */
  public function __construct(Importer $importer, Messenger $messenger, DeployManager $deploy_manager) {
    $this->importer = $importer;
    $this->messenger = $messenger;
    $this->deployManager = $deploy_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('default_content_deploy.importer'),
      $container->get('messenger'),
      $container->get('default_content_deploy.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dcd_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['file'] = [
      '#type' => 'file',
      '#title' => $this->t('Archive'),
      '#description' => $this->t('Upload a file if you want importing from archive.</br> <b>Important!!!</b> The structure should be the same as after export.'),
    ];

    $form['folder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Folder'),
      '#description' => $this->t('All existing content will be imported form this folder.'),
      '#default_value' => $this->deployManager->getContentFolder(),
      '#states' => [
        'visible' => [
          ':input[name="files[file]"]' => ['value' => ''],
        ],
      ],
    ];

    $form['force_override'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Force override'),
      '#description' => $this->t('All existing content will be overridden (locally updated default content will be reverted to the state defined in a content directory).'),
      '#default_value' => FALSE,
    ];

    $form['import'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import content'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $all_files = $this->getRequest()->files->get('files', []);

    if (!empty($all_files['file'])) {
      /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file_upload */
      $file_upload = $all_files['file'];
      $extension = $file_upload->getClientOriginalExtension();

      // Checking the extension.
      if ($extension != 'gz') {
        $form_state->setErrorByName('file', $this->t('The selected file %filename cannot be uploaded. Only files with the following extensions are allowed: %extensions.', [
          '%filename' => $file_upload->getClientOriginalName(),
          '%extensions' => 'tar.gz',
        ]));
      }

      if ($file_upload->isValid()) {
        $form_state->setValue('file', $file_upload->getRealPath());
      }
      else {
        $form_state->setErrorByName('file', $this->t('The file could not be uploaded.'));
      }
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
    $force_override = $form_state->getValue('force_override', FALSE);
    $folder = $form_state->getValue('folder');
    $file = $form_state->getValue('file');

    try {
      if ($file) {
        $this->importer->setFolder(file_directory_temp() . '/dcd/content');
        $this->deployManager->uncompressContent($file);
      }
      else {
        $this->importer->setFolder($folder);
      }

      $this->importer->setForceOverride($force_override);
      $this->importer->prepareForImport();
      $this->addResultMessage();
      $this->importer->import();
    }
    catch (\Exception $exception) {
      $this->messenger->addError($exception->getMessage());
    }
  }

  /**
   * Add a message with importing results.
   */
  private function addResultMessage() {
    $result = $this->importer->getResult();
    $array_column = array_column($result, 'status');
    $count = array_count_values($array_column);

    $this->messenger->addMessage($this->t('Created: @count', [
      '@count' => $count['create'] ?: 0,
    ]));

    $this->messenger->addMessage($this->t('Updated: @count', [
      '@count' => $count['update'] ?: 0,
    ]));

    $this->messenger->addMessage($this->t('Skipped: @count', [
      '@count' => $count['skip'] ?: 0,
    ]));

    return $this;
  }

}
