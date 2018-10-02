<?php

namespace Drupal\fontawesome\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Asset\LibraryDiscovery;

/**
 * Defines a form that configures fontawesome settings.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * Drupal LibraryDiscovery service container.
   *
   * @var Drupal\Core\Asset\LibraryDiscovery
   */
  protected $libraryDiscovery;

  /**
   * {@inheritdoc}
   */
  public function __construct(LibraryDiscovery $library_discovery) {
    $this->libraryDiscovery = $library_discovery;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('library.discovery')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'fontawesome_admin_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'fontawesome.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get current settings.
    $fontawesome_config = $this->config('fontawesome.settings');

    // Load the fontawesome libraries so we can use its definitions here.
    $fontawesome_library = $this->libraryDiscovery->getLibraryByName('fontawesome', 'fontawesome');
    $fontawesome_cdn_library = $this->libraryDiscovery->getLibraryByName('fontawesome', 'fontawesome.cdn');

    $form['external'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('External file configuration'),
      '#description' => $this->t('These settings control the method by which the Font Awesome library is loaded. You can choose to use an external library by selecting a URL below, or you can use a local version of the file by leaving the box unchecked and downloading the library <a href=":remoteurl">:remoteurl</a> and installing locally at %installpath.', [
        ':remoteurl' => $fontawesome_library['remote'],
        '%installpath' => '/libraries',
      ]),
      'fontawesome_use_cdn' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Use external file?'),
        '#description' => $this->t('Checking this box will cause the Font Awesome library to be loaded externally rather than from the local library file.'),
        '#default_value' => $fontawesome_config->get('fontawesome_use_cdn'),
      ],
      'fontawesome_external_location' => [
        '#type' => 'textfield',
        '#title' => $this->t('External Library Location'),
        '#default_value' => empty($fontawesome_config->get('fontawesome_external_location')) ? $fontawesome_cdn_library['css'][0]['data'] : $fontawesome_config->get('fontawesome_external_location'),
        '#size' => 80,
        '#description' => $this->t('Enter a source URL for the external Font Awesome library you wish to use. This URL should point to the Font Awesome CSS file. Leave blank to use the default Font Awesome CDN.'),
        '#states' => [
          'disabled' => [
            ':input[name="fontawesome_use_cdn"]' => ['checked' => FALSE],
          ],
        ],
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    // Validate URL.
    if (!empty($values['fontawesome_external_location']) && !UrlHelper::isValid($values['fontawesome_external_location'])) {
      $form_state->setErrorByName('fontawesome_external_location', $this->t('Invalid external library location.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    // Clear the library cache so we use the updated information.
    $this->libraryDiscovery->clearCachedDefinitions();

    // Save the updated settings.
    $this->config('fontawesome.settings')
      ->set('fontawesome_use_cdn', $values['fontawesome_use_cdn'])
      ->set('fontawesome_external_location', (string) $values['fontawesome_external_location'])
      ->save();

    parent::submitForm($form, $form_state);
  }

}
