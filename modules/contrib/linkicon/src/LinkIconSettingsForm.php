<?php

namespace Drupal\linkicon;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the Slick admin settings form.
 */
class LinkIconSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
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

    $form['linkicon'] = array(
      '#type' => 'details',
      '#title' => $this->t('Linkicon settings'),
      '#description' => $this->t('You may leave this empty, if your theme has already icon font.'),
      '#open' => TRUE,
      '#collapsible' => FALSE,
    );

    $form['linkicon']['font'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Path to the icon font CSS file'),
      '#description' => $this->t('Valid path to CSS file, e.g.: /libraries/fontello/css/fontello.css. <br />Please be aware of possible conflict if you install different icon fonts <br />due to their namespace collision. Most icon fonts tend to use the same .icon:before. Be sure the icon prefix matches the one defined at field formatter.'),
      '#default_value' => $config->get('font'),
    );

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
    \Drupal::service('library.discovery')->clearCachedDefinitions();

    parent::submitForm($form, $form_state);
  }

}
