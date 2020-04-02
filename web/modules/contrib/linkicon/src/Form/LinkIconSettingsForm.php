<?php

namespace Drupal\linkicon\Form;

use Drupal\Core\Asset\LibraryDiscoveryInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the Slick admin settings form.
 */
class LinkIconSettingsForm extends ConfigFormBase {

  /**
   * Drupal\Core\Asset\LibraryDiscoveryInterface definition.
   *
   * @var Drupal\Core\Asset\LibraryDiscoveryInterface
   */
  protected $libraryDiscovery;

  /**
   * Class constructor.
   */
  public function __construct(ConfigFactoryInterface $config_factory, LibraryDiscoveryInterface $library_discovery) {
    parent::__construct($config_factory);
    $this->libraryDiscovery = $library_discovery;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('library.discovery')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'linkicon_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['linkicon.settings'];
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::buildForm().
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('linkicon.settings');

    $form['linkicon'] = [
      '#type' => 'details',
      '#title' => $this->t('Linkicon settings'),
      '#description' => $this->t('Import a custom icon font CSS located on the file system.'),
      '#open' => TRUE,
      '#collapsible' => FALSE,
    ];

    $form['linkicon']['font'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Icon font CSS file path'),
      '#description' => $this->t('Valid path to CSS file. Use comma separated value for multiple CSS files, e.g.: /libraries/fontello/css/fontello.css, /libraries/fontello/css/other.css. <br />Please be aware of potential namespace conflicts if you import additional icon fonts through a theme, as most icon fonts use the same selectors (.icon:before). <br />Leave it empty if using FontAwesome (5+) module and SVG with JS method, or your theme has already defined one.'),
      '#default_value' => $config->get('font'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::submitForm().
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->config('linkicon.settings')
      ->set('font', $form_state->getValue('font'))
      ->save();

    // Invalidate the library discovery cache to update new icon font.
    $this->libraryDiscovery->clearCachedDefinitions();

    parent::submitForm($form, $form_state);
  }

}
