<?php

namespace Drupal\conf_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'ConfBlock' block.
 *
 * @Block(
 *  id = "conf_block_block",
 *  admin_label = @Translation("Conf Block"),
 *  deriver = "Drupal\conf_block\Plugin\Derivative\ConfBlock"
 * )
 */
class ConfBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $confBlockRow;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param $someVarArg
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $confBlockValues = \Drupal::config('conf_block.content.html')->get('values');
    $this->confBlockRow = $confBlockValues[$this->getDerivativeId()];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    if (!empty($this->configuration['label'])) {
      return $this->configuration['label'];
    }

    $definition = $this->getPluginDefinition();
    // Cast the admin label to a string since it is an object.
    // @see \Drupal\Core\StringTranslation\TranslatableMarkup
    return (string) $definition['default_title'];
  }


  /**
   * {@inheritdoc}
   */
  public function build() {

    $build['#cache'] = ['max-age' => -1];

    $build['#markup'] = check_markup($this->confBlockRow['body']['value'], $this->confBlockRow['body']['format']);

    return $build;
  }
}