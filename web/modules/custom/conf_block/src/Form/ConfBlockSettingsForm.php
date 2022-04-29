<?php

namespace Drupal\conf_block\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ConfBlockSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'conf_block_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Form constructor.
    $form = parent::buildForm($form, $form_state);
    // Default settings.
    $config = $this->config('conf_block.settings');

    $form['sections'] = [
      '#type' => 'vertical_tabs',
      '#tree' => TRUE,
      /*'#attached' => [
        'library' => ['conf_block/drupal.conf_block_settings'],
      ],*/
    ];
    foreach ($config->get('sections') as $section_key=>$section) {

      $form[$section_key] = [
        '#type' => 'details',
        '#title' => $section['label'],
        '#group' => 'sections',
        '#tree' => TRUE,
      ];

      $form[$section_key]['section_label'] = [
        '#title' => t('Section label'),
        '#type' => 'textfield',
        '#default_value' => $section['label'],
        '#description' => t("Leave blank to remove section"),
      ];

      foreach ($section['blocks'] as $section_block_key => $section_block) {
        $form[$section_key][$section_block_key] = [
          '#title' => t('Block'),
          '#type' => 'textfield',
          '#default_value' => $section_block['name'],
        ];
      }
      $form[$section_key]['new_block_tmp'] = [
        '#title' => t('Add Block'),
        '#type' => 'textfield',
        '#description' => t('Type a name for new block and save configuration'),
      ];
    }

    $form['new_section_tmp'] = [
      '#title' => t('Add Section'),
      '#type' => 'textfield',
      '#description' => t('Type a name for new section and save configuration'),
    ];

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('conf_block.settings');
    $sections = array();
    if ($new_section = $form_state->getValue('new_section_tmp') ) {
      $section_machine_name = str_replace(" ", "_", strtolower(trim($new_section)));
      $sections[$section_machine_name] = array('label'=>$new_section);
    }
    foreach ($form_state->getValues() as $section_key => $formValue) {
      if(is_array($formValue) && $section_key != 'sections') {
        if (!empty($formValue['section_label'])) {
          $sections[$section_key] = ['label'=>$formValue['section_label']];
          foreach ($formValue as $key => $value) {
            if($key != 'section_label' && !empty($value)) {
              $block_machine_name = str_replace(" ", "_", strtolower(trim($value)));
              $sections[$section_key]['blocks'][$block_machine_name] = ['name'=>$value];
            }
          }
        }
      }
    }
    $config->set('sections', $sections);
    $config->save();
    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'conf_block.settings',
    ];
  }

}