<?php

/**
 * @file
 * Provides views data for Share Everywhere module.
 */

/**
 * Implements hook_views_data().
 */
function share_everywhere_views_data() {
  $module_handler = \Drupal::service('module_handler');
  $data = [];
  $data['node']['share_everywhere_field'] = [
    'title' => t('Share Everywhere'),
    'help' => t('Buttons provided by the Share Everywhere service.'),
    'field' => [
      'id' => 'share_everywhere_field',
      'help' => t('Show Share Everywhere module for a particular node.'),
    ],
  ];
  if ($module_handler->moduleExists('commerce_product')) {
    $data['commerce_product']['share_everywhere_field'] = [
      'title' => t('Share Everywhere'),
      'help' => t('Buttons provided by the Share Everywhere service.'),
      'field' => [
        'id' => 'share_everywhere_field',
        'help' => t('Show Share Everywhere module for a particular product.'),
      ],
    ];
  }
  return $data;
}
