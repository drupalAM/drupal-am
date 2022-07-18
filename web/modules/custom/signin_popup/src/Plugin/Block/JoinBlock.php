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
    $roles = \Drupal::currentUser()->getRoles();
    $is_editor = FALSE;
    if(in_array('editor', $roles)) {
      $is_editor = TRUE;
    }
    if(in_array('administrator', $roles)) {
      $is_editor = TRUE;
    }
    return [
      '#theme' => 'join_now_block',
      '#is_anonymous' => \Drupal::currentUser()->isAnonymous(),
      '#is_editor' => $is_editor,
      '#langcode' => \Drupal::languageManager()->getCurrentLanguage()->getId(),
    ];
  }
}
