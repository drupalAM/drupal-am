<?php

/**
 * @file
 * Contains \Drupal\default_content_deploy\Form\SettingsForm.
 */

namespace Drupal\default_content_deploy\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings form.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * Directory config name.
   */
  const DIRECTORY = 'default_content_deploy.content_directory';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dcd_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::DIRECTORY,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::DIRECTORY);

    $form['content_directory'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Content Directory'),
      '#default_value' => $config->get('content_directory'),
      '#description' => 'Specify the path relative to index.php. For example: ../content',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable(static::DIRECTORY)
      ->set('content_directory', $form_state->getValue('content_directory'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
