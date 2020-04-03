<?php

namespace Drupal\drupalam\Plugin\Alter;

use Drupal\bootstrap\Plugin\Alter\ElementInfo as BootstrapElementInfo;
use Drupal\bootstrap\Utility\Element;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_element_info_alter().
 *
 * @BootstrapAlter("element_info")
 */
class ElementInfo extends BootstrapElementInfo {

  /**
   * {@inheritdoc}
   */
  public function alter(&$types, &$context1 = NULL, &$context2 = NULL) {
    // Let base theme do what it needs to to do.
    parent::alter($types, $context1, $context2);

    // Add our #process callback to the necessary elements.
    foreach (Element::create($types)->children() as $element) {
      if (($theme_wrappers = $element->getProperty('theme_wrappers')) && in_array('form_element', $theme_wrappers)) {
        $element->prependProperty('process', [static::class, 'processFormElement']);
      }
    }
  }

  /**
   * Implements callback_form_element_process().
   *
   * @param array $element
   *   The element render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   * @param array $form
   *   The complete form render array.
   *
   * @return array
   *   The element render array.
   */
  public static function processFormElement(array $element, FormStateInterface $form_state, array &$form) {
    // Get the form identifier.
    $form_id = $form_state->getBuildInfo()['form_id'];

    // Get the index of the "form_element" theme wrapper.
    $key = array_search('form_element', $element['#theme_wrappers']);

    // Replace it with a theme hook suggestion containing the form identifier.
    if ($key !== FALSE) {
      // Check if form is a views exposed form.
      if ($form_id == "views_exposed_form") {
        $element['#theme_wrappers'][$key] = "form_element__" . $form['#form_name'];
      }
      else {
        $element['#theme_wrappers'][$key] = "form_element__$form_id";
      }
    }

    // Must always return the element.
    return $element;
  }

}