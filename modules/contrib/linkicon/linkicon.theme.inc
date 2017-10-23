<?php
/**
 * @file
 * Preprocessors and theme functions of Linkicon module.
 */

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
  $variables['attributes'] = isset($element['#attributes']) ? new Attribute($element['#attributes']) : array();
  $variables['settings'] = $element['#config'];
  $variables['items'] = $element['#items'];
}

/**
 * Prepares variables for linkicon_item templates.
 *
 * Default template: linkicon-item.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Array keys: #title, #position, #icon_bundle, #icon_name, #attributes.
 *     - title: The link text or title, already sanitized at viewElements.
 *     - position: The icon placement relative to link text.
 *     - icon_bundle: The icon bundle, integration with icon.module.
 *     - icon_name: The icon name, e.g.: twitter, facebook, etc.
 *     - attributes: An associative array of attributes to be placed in the span
 *       tag.
 *
 * Note: String variables in the template are now autoescaped by Twig.
 * @see #2296163.
 */
function template_preprocess_linkicon_item(array &$variables) {
  $element = $variables['element'];
  $settings = isset($element['#settings']) ? $element['#settings'] : array();
  $variables['title'] = $element['#title'];
  $variables['position'] = $element['#position'];
  $variables['icon_bundle'] = $element['#icon_bundle'];
  $variables['icon_name'] = $element['#icon_name'];
  $variables['title_only'] = isset($settings['linkicon_link']) && $settings['linkicon_link'];

  // @todo: icon API integration, none by D8 3/2/14.
  if (!empty($variables['icon_bundle'])) {
    // @see https://www.drupal.org/node/2195739
    $icon = array(
      '#theme'      => 'icon',
      '#bundle'     => $variables['icon_bundle'],
      '#icon'       => $variables['icon_name'],
      '#attributes' => array('class' => array('linkicon__icon', 'icon')),
    );
  }
  else {
    // The 'icon' class to get consistent across icon and linkicon module, only
    // needed if the icon prefix is not 'icon', e.g., 'fa' for Fontawesome >= 4.
    if (isset($element['#icon_prefix']) && $element['#icon_prefix'] != 'icon') {
      $element['#attributes']['class'][] = 'icon';
    }
    $icon['#markup'] = '<span ' . new Attribute($element['#attributes']) . '></span>';
  }

  $variables['icon'] = $icon;
  $variables['title_attributes'] = new Attribute();
}
