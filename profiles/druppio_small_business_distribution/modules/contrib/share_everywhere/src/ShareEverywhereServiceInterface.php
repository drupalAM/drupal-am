<?php

namespace Drupal\share_everywhere;

/**
 * Interface for ShareEverywhereService.
 */
interface ShareEverywhereServiceInterface {

  /**
   * Builds a renderable array of Social buttons.
   *
   * @param string $url
   *   Node url.
   * @param string $id
   *   Node entity type plus node id.
   *
   * @return array
   *   Renderable build array.
   */
  public function build($url, $id);

  /**
   * Determines if module is restricted to show or not on certain pages.
   *
   * @param string $view_mode
   *   Node view mode.
   * @param string $node_path
   *   Node path.
   *
   * @return bool
   *   Returns TRUE or FALSE.
   */
  public function isRestricted($view_mode, $node_path);

  /**
   * Custom function instead of php's built in array_diff function.
   *
   * See http://php.net/manual/en/function.array-diff.php#120821.
   *
   * @param array $a
   *   First array to compare.
   * @param array $b
   *   Second array to compare.
   *
   * @return array
   *   Returns an array containing the different elements
   *   between the given input arrays.
   */
  public function arrayDiff(array $a, array $b);

}
