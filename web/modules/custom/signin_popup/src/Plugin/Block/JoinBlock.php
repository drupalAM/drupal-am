<?php

namespace Drupal\signin_popup\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Copyright block.
 *
 * @Block(
 *   id = "join_now_block",
 *   admin_label = @Translation("Join now"),
 * )
 */
class JoinBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'join_now_block',
    ];
  }

}
