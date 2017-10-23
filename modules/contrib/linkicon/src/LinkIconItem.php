<?php

namespace Drupal\linkicon;

use Drupal\Core\Form\FormStateInterface;
use Drupal\link\Plugin\Field\FieldType\LinkItem;

/**
 * Modify plugin implementation of the 'link' field settings form.
 *
 * @see linkicon_field_info_alter().
 */
class LinkIconItem extends LinkItem {

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return array(
      'title_predefined' => '',
    ) + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::fieldSettingsForm($form, $form_state);

    // Appends our option to the title.
    $element['title']['#options'] += array(
      LINKICON_PREDEFINED => $this->t('Predefined'),
    );

    $element['title_predefined'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Allowed link texts'),
      '#default_value' => $this->getSetting('title_predefined'),
      '#description' => '<p>' . $this->t("Enter the list of key|value pairs of predefined link texts separated by new line, where key is the icon name without prefix, e.g.: <br />for <em>icon-facebook</em>, place <em>facebook|Facebook</em>. The prefix is defined at Display formatter so that you are not stuck in database when the icon vendor change prefixes from 'icon-' to just 'fa-', etc. Make sure the icon name is available at your icon set. To have a tooltip different from the title, add a third pipe value. <br />Token relevant to this entity is supported, e.g.: <strong>facebook|Facebook|[node:title]</strong> or  <strong>facebook|Facebook|[user:name]'s Facebook page</strong>.<br /><strong>Warning!</strong> Pre-existing values will be reset.") . '<br><br></p>',
      '#states' => array(
        'visible' => array(
          ':input[name="settings[title]"]' => array('value' => LINKICON_PREDEFINED),
        ),
      ),
    );

    return $element;
  }

  /**
   * Validates predefined link title.
   *
   * Since Link title is not required, we make sure that it is not empty if the
   * URL field is not. And vice versa.
   */
  public static function elementValidateLinkIcon(&$element, FormStateInterface $form_state, $context) {
    if ($element['uri']['#value'] !== '' && $element['title']['#value'] === '') {
      $element['title']['#required'] = TRUE;
      $form_state->setError($element['title'], t('@name field is required. Title must be entered if URL is provided.', array('@name' => $element['title']['#title'])));
    }
    if ($element['uri']['#value'] === '' && $element['title']['#value'] !== '') {
      $element['uri']['#required'] = TRUE;
      $form_state->setError($element['uri'], t('@name field is required. URL must be entered if title is provided.', array('@name' => $element['uri']['#title'])));
    }
  }

}
