<?php

use Drupal\lightgallery\Optionset\LightgalleryOptionset;
use Drupal\lightgallery\Manager\LightgalleryManager;

/**
 * Prepares variables for lightgallery view templates.
 *
 * Default template: lightgallery-views-style.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - view: A ViewExecutable object.
 *   - rows: The raw row data.
 */
function template_preprocess_lightgallery_views_style(&$variables) {
  // Only run the preprocessor if it is a view.
  if (empty($variables['view'])) {
    return;
  }

  /**
   * @var \Drupal\lightgallery\Plugin\views\style\LightGallery $handler
   * @var \Drupal\views\Plugin\views\field\Field $field
   */
  $view = $variables['view'];
  $rows = $variables['rows'];
  $fields = $view->field;
  $handler = $view->style_plugin;

  // The lightgallery handler is not properly configured yet.
  if (empty($handler->options['lightgallery'])) {
    return;
  }

  $options = $handler->options['lightgallery'];
  // Render fields with view style plugin.
  $rendered_rows = $handler->renderFields($rows);
  unset($variables['rows']);
  $thumb_field = $options['thumb_field'];
  $image_field = $options['image_field'];
  $title_field = $options['title'];

  $items = array();
  foreach ($rendered_rows as $id => $row) {
    /** @var \Drupal\views\ResultRow $row */
    $rendered_slide = FALSE;
    $rendered_thumb = FALSE;
    $rendered_title = FALSE;

    foreach ($row as $field_name => $field) {
      // Check if field is thumb field.
      if ($field_name == $thumb_field) {
        $rendered_thumb = $field;
      }

      // Check if field is image field.
      if ($field_name == $image_field) {
        $rendered_slide = $field;
      }

      // Check if field is title field.
      if ($field_name == $title_field) {
        $rendered_title = $field;
      }

      // Store field labels.
      $item['field_label'] = $fields[$field_name]->label();
    }

    $item['slide'] = $rendered_slide;
    $item['thumb'] = $rendered_thumb;
    $item['title'] = ($rendered_title) ? $rendered_title->__toString() : FALSE;

    // Set row style to display: hidden or block.
    $item['row_style'] = 'display: block;';

    $items[] = $item;
  }

  // Set unique id, so that multiple instances on one page can be created.
  $unique_id = uniqid();
  // Load libraries.
  $lightgallery_optionset = new LightgalleryOptionset($options);
  $lightgallery_manager = new LightgalleryManager($lightgallery_optionset);
  // Build render array.
  $content = array(
    '#theme' => 'lightgallery',
    '#items' => $items,
    '#id' => $unique_id,
    '#attached' => $lightgallery_manager->loadLibraries($unique_id),
  );

  // Add the slide items to the variables.
  $variables['content'] = $content;

}
