<?php
/**
 * @file
 * Contains \Drupal\heading_formatter\Plugin\field\formatter\HeadingFormatter.
 */

namespace Drupal\heading_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'heading_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "heading_formatter",
 *   label = @Translation("Heading Formatter"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class HeadingFormatter extends FormatterBase {
  
  /**
  * {@inheritdoc}
  */
  public static function defaultSettings() {
    return [
      'heading_type' => 'h2',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    
    $summary[] = t('Heading element is @type', array('@type' => $this->getSetting('heading_type')));

    return $summary;
  }
  
  /**
  * {@inheritdoc}
  */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);
    
    $options = [];
    for ($i = 1; $i <= 6; $i++) {
      $options['h' . $i] = 'H' . $i;
    }
    $elements['heading_type'] = [
      '#type' => 'select',
      '#options' => $options,
      '#title' => t('Heading Type'),
      '#default_value' => $this->getSetting('heading_type'),
    ];

    return $elements;
  }

  /**
  * {@inheritdoc}
  */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'html_tag',
        '#tag' => $this->getSetting('heading_type'),
        '#value' => $item->value,
      ];
    }

    return $elements;
  }
  
}
