<?php

namespace Drupal\share_everywhere\Plugin\views\field;

use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\share_everywhere\ShareEverywhereServiceInterface;

/**
 * Field handler to display Share Everywhere buttons.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("share_everywhere_field")
 */
class ShareEverywhereField extends FieldPluginBase {

  /**
   * The ShareEverywhere service.
   *
   * @var Drupal\share_everywhere\ShareEverywhereService
   */
  protected $shareService;

  /**
   * Constructs a ShareEverywhereField object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\share_everywhere\ShareEverywhereServiceInterface $share_service
   *   The module manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ShareEverywhereServiceInterface $share_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->shareService = $share_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('share_everywhere.service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();
    $this->addAdditionalFields();
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $node = $values->_entity;
    $url = $node->toUrl()->setAbsolute()->toString();
    $id = $node->getEntityTypeId() . $node->id();

    return $this->shareService->build($url, $id);
  }

}
