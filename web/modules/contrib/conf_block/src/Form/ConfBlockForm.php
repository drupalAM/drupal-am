<?php

namespace Drupal\conf_block\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ConfBlockForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'conf_block_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Form constructor.
    $form = parent::buildForm($form, $form_state);
    // Default settings.
    $default_html_blocks = $this->config('conf_block.content.default');
    $default_title = $default_html_blocks->get('title');
    $default_body = $default_html_blocks->get('body');

    $sections = $this->config('conf_block.settings')->get('sections');

    $values = $this->config('conf_block.content.html')->get('values');

    $form['sections'] = [
      '#type' => 'vertical_tabs',
      /*'#attached' => [
        'library' => ['conf_block/drupal.conf_block_settings'],
      ],*/
    ];
    foreach ($sections as $section_key => $section) {

      $form[$section_key] = [
        '#type' => 'details',
        '#title' => $section['label'],
        '#group' => 'sections',
        '#tree' => TRUE
      ];

      foreach ($section['blocks'] as $section_block_key => $section_block) {

        $vk = $section_key . "__" . $section_block_key; // Values key

        $form[$section_key][$section_block_key]['name'] = [
          '#markup' => $section_block['name'],
        ];
        $form[$section_key][$section_block_key]['title'] = [
          '#title' => t('Block Title'),
          '#type' => 'textfield',
          '#default_value' => isset($values[$vk]['title']) ? $values[$vk]['title'] : $default_title,
        ];
        $form[$section_key][$section_block_key]['body'] = [
          '#title' => t('Block Body'),
          '#type' => 'text_format',
          '#default_value' => isset($values[$vk]['body']['value']) ? $values[$vk]['body']['value'] : $default_body,
          '#format' => isset($values[$vk]['body']['format']) ? $values[$vk]['body']['format'] : filter_default_format(),
          '#suffix' => '<br><hr><br><br>',
        ];

      }
    }
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
    $config = $this->config('conf_block.content.html');
    $sections = $this->config('conf_block.settings')->get('sections');
    $values = array();
    foreach ($form_state->getValues() as $section_key => $formValue) {

      if(is_array($formValue)) {
        foreach ($formValue as $key => $block) {
          if(!empty($block)) {
            $values[$section_key . "__" . $key] = [
              'title' => $block['title'],
              'body' => [
                'format' => $block['body']['format'],
                'value' => $block['body']['value'],
              ],
              'admin_label' => $sections[$section_key]['blocks'][$key]['name']
            ];
          }
        }
      }
    }
    $config->set('values', $values);
    $config->save();
    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'conf_block.content.html',
    ];
  }

}