<?php

namespace Drupal\conf_block\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Component\Plugin\Derivative\DeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides block plugin definitions for all Views block displays.
 *
 * @see \Drupal\views\Plugin\Block\ViewsBlock
 */
class ConfBlock extends DeriverBase implements DeriverInterface {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $confBlockValues = \Drupal::config('conf_block.content.html')->get('values');
    foreach ($confBlockValues as $blockKey => $blockValue) {
      $this->derivatives[$blockKey] = $base_plugin_definition;
      $this->derivatives[$blockKey]['admin_label'] = t('Conf block: ') . $blockValue['admin_label'];
      $this->derivatives[$blockKey]['default_title'] = $blockValue['title'];
    }
    return $this->derivatives;
  }

}