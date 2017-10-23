<?php

namespace Drupal\linkicon;

/**
 * Interface for linkicon plugins.
 */
interface LinkIconManagerInterface {

  /**
   * Returns available settings.
   *
   * @param string $setting_name
   *   The setting name.
   *
   * @return array
   *   An array of available settings.
   */
  public function getSetting($setting_name);

  /**
   * Returns extracted allowed title values.
   *
   * @param string $values
   *   The link title allowed values.
   * @param bool $is_tooltip
   *   If it is for tooltip title or regular link text.
   *
   * @return array
   *   An array of allowed title values.
   */
  public function extractAllowedValues($values, $is_tooltip = FALSE);

}
