<?php

namespace Drupal\empty_front\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Empty Front module.
 */
class EmptyFrontController extends ControllerBase {

  /**
   * Returns an empty page.
   *
   * @return array
   *   An empty array.
   */
  public function emptyPage() {
    return [];
  }

}
