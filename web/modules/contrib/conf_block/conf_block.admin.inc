<?php

/**
 * Homepage blocks config form
 */
function _conf_block_home_page_configure_form($form, &$form_state) {
  global $conf_blocks;
  $form['conf_block_home_blocks'] = array(
    '#type' => 'vertical_tabs',
    '#tree'=>TRUE,
  );
  $f = 0;
  foreach ($conf_blocks as $fieldset_title => $group) {
    $form['fieldset_' . $f] = array(
      '#type' => 'fieldset',
      '#title' => $fieldset_title,
      '#collapsible' => TRUE,
      '#group' => 'conf_block_home_blocks',
    );
    foreach ($group as $block_key => $homepage_block_title) {
      $conf_block_body = variable_get($block_key);
      $conf_block_title = variable_get($block_key . "_title");
      $form['fieldset_' . $f][$block_key . "_title"] = array(
        '#type' => 'textfield',
        '#title' => $homepage_block_title,
        '#default_value' => isset($conf_block_title) ? $conf_block_title : '',
        '#group' => 'conf_block_home_blocks',
        '#description' => t('Block title, leave empty to have block without title.'),
      );
      $form['fieldset_' . $f][$block_key] = array(
        '#type' => 'text_format',
        '#default_value' => isset($conf_block_body['value']) ? $conf_block_body['value'] : '',
        '#format' => isset($conf_block_body['format']) ? $conf_block_body['format'] : filter_default_format(),
        '#group' => 'conf_block_home_blocks',
        '#suffix' => '<br><hr><br><br>',
      );
    }
    $f++;
  }
  return system_settings_form($form);
}

/**
 * Contact us page blocks config form
 */
function _conf_block_contact_form_configure_form($form, &$form_state) {

  $conf_block_contact_block = variable_get('conf_block_contact_block');
  $form['conf_block_contact_block'] = array(
    '#type' => 'text_format',
    '#title' => 'Header Block Body',
    '#default_value' => isset($conf_block_contact_block['value']) ? $conf_block_contact_block['value'] : '',
    '#format' => isset($conf_block_contact_block['format']) ? $conf_block_contact_block['format'] : filter_default_format(),
    '#tree' => TRUE
  );

  return system_settings_form($form);
}

/**
 * Views page blocks config form
 */
function _conf_block_views_form($form, &$form_state) {
  $conf_block_views_setting = variable_get('conf_block_views_setting', '');
  if (empty($conf_block_views_setting)) {
    $form['empty'] = array('#markup'=> t('No views selected. Go to settings and select view.'));
    return $form;
  }
  // Create formatted text form element for eac enabled view display
  foreach ($conf_block_views_setting as $view__display) {
    if ($view__display !== 0) {
      $conf_variable = variable_get('conf_block_' . $view__display);
      list($view_name, $display) = explode("__", $view__display);
      $view = views_get_view($view_name);
      // Set title
      if (isset($view->display[$display]->display_options['title'])) {
        $title = $view->display[$display]->display_options['title'];
      }
      else{
        $title = $view->display['default']->display_options['title'];
      }
      $form['conf_block_' . $view__display] = array(
        '#type' => 'text_format',
        '#title' => $title . " (" . $view->display[$display]->id .") header block",
        '#default_value' => isset($conf_variable['value']) ? $conf_variable['value'] : '',
        '#format' => isset($conf_variable['format']) ? $conf_variable['format'] : filter_default_format(),
      );
    }
  }

  return system_settings_form($form);
}

/**
 * Views blocks settings config form
 */
function _conf_block_views_settings_form($form, &$form_state) {
  $views = views_get_all_views();
  foreach ($views as $view_name => $view) {
    foreach ($view->display as $display_name=>$views_display) {
      if (!$view->disabled and $display_name !== 'default') {
        $suffix = $view_name . "__" . $display_name;
        $form_options[$suffix] = $view->human_name . " (". $views_display->display_title . ")";
      }
    }
  }

  $conf_block_views_setting = variable_get('conf_block_views_setting', '');

  $form['conf_block_views_setting'] = array(
    '#type' => 'checkboxes',
    '#options' => $form_options,
    '#default_value' => $conf_block_views_setting,
    '#title' => t('Select views displays for which custom header block should be applied'),
  );

  return system_settings_form($form);
}