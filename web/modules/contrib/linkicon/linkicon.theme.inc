<?php

/**
 * @file
 * Preprocessors and theme functions of Linkicon module.
 */

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Template\Attribute;

/**
 * Prepares variables for linkicon templates.
 *
 * Default template: linkicon.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #items, #config, #attributes.
 *     - items: An array of items containing links with icon formatted.
 *     - config: An array of settings that decides the link icon output.
 *     - attributes: An associative array of attributes placed in the UL tag.
 */
function template_preprocess_linkicon(array &$variables) {
  $element = $variables['element'];
  $variables['wrapper_attributes'] = new Attribute();
  $variables['attributes'] = isset($element['#attributes']) ? new Attribute($element['#attributes']) : [];
  $variables['settings'] = $element['#config'];
  $variables['items'] = $element['#items'];
  $variables['label'] = isset($element['#title']) ? $element['#title'] : '';
  $variables['_preview'] = !empty($variables['settings']['_preview']);
  foreach (['field_name', 'field_type', 'label_display'] as $key) {
    $variables[$key] = isset($element["#$key"]) ? $element["#$key"] : '';
  }

  if (!empty($element['#linkicon_id'])) {
    $variables['attributes']['id'] = $element['#linkicon_id'];
  }
}

/**
 * Prepares variables for linkicon_item templates.
 *
 * Default template: linkicon-item.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Array keys: #title, #icon_name, #settings, #attributes.
 *     - title: The link text or title, already sanitized at viewElements.
 *     - icon_name: The icon name, e.g.: twitter, facebook, etc.
 *     - settings: The formatter settings.
 *     - attributes: An associative array of attributes to be placed in the span
 *       tag.
 *
 *   Note: String variables in the template are now autoescaped by Twig.
 * @see https://drupal.org/node/2296163
 */
function template_preprocess_linkicon_item(array &$variables) {
  $element = $variables['element'];

  foreach (['attributes', 'icon_name', 'settings', 'title'] as $key) {
    $default = $key == 'attributes' || $key == 'settings' ? [] : '';
    $variables[$key] = isset($element["#$key"]) ? $element["#$key"] : $default;
  }

  $settings = $variables['settings'];
  $attributes = $variables['attributes'];
  $prefix = Html::escape(trim($settings['prefix']));
  $icon_class = Html::cleanCssIdentifier(Unicode::strtolower($prefix . '-' . trim($variables['icon_name'])));

  $attributes['aria-hidden'] = 'true';
  $attributes['class'][] = 'linkicon__icon';

  // The 'icon' class to get consistent across icon and linkicon module, only
  // needed if the icon prefix is not 'icon', e.g., 'fa' for Fontawesome >= 4.
  if ($prefix != 'icon') {
    $attributes['class'][] = 'icon';
  }

  // Order is important for FontAwesome, else broken, fab far fas come first.
  if (!empty($settings['icon_class'])) {
    $attributes['class'][] = trim($settings['icon_class']);
  }

  $attributes['class'][] = $prefix . ' ' . $icon_class;

  // @todo: icon API integration, none by D8 3/2/14.
  if (!empty($settings['bundle'])) {
    // @see https://www.drupal.org/node/2195739
    $icon = [
      '#theme'      => 'icon',
      '#bundle'     => $settings['bundle'],
      '#icon'       => $variables['icon_name'],
      '#attributes' => $attributes,
    ];
  }
  else {
    $icon['#markup'] = '<span ' . new Attribute($attributes) . '></span>';
  }

  $variables['icon'] = $icon;
  $variables['title_attributes'] = new Attribute();
}
