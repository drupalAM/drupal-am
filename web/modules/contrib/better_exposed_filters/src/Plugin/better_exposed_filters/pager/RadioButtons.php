<?php

namespace Drupal\better_exposed_filters\Plugin\better_exposed_filters\pager;

use Drupal\Core\Form\FormStateInterface;

/**
 * Radio Buttons pager widget implementation.
 *
 * @BetterExposedFiltersPagerWidget(
 *   id = "bef",
 *   label = @Translation("Radio Buttons"),
 * )
 */
class RadioButtons extends PagerWidgetBase {

  /**
   * {@inheritdoc}
   */
  public function exposedFormAlter(array &$form, FormStateInterface $form_state) {
    parent::exposedFormAlter($form, $form_state);

    if (!empty($form['items_per_page'])) {
      $form['items_per_page']['#type'] = 'radios';
      if (empty($form['items_per_page']['#process'])) {
        $form['items_per_page']['#process'] = [];
      }
      array_unshift($form['items_per_page']['#process'], ['\Drupal\Core\Render\Element\Radios', 'processRadios']);
      $form['items_per_page']['#prefix'] = '<div class="bef-sortby bef-select-as-radios">';
      $form['items_per_page']['#suffix'] = '</div>';
    }
  }

}
