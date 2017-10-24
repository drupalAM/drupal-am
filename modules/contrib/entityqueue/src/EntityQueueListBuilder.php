<?php

namespace Drupal\entityqueue;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\entityqueue\Entity\EntitySubqueue;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a class that builds a listing of entity queues.
 */
class EntityQueueListBuilder extends ConfigEntityListBuilder {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($entity_type, $entity_type_manager->getStorage($entity_type->id()));

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    $entities = array(
      'enabled' => array(),
      'disabled' => array(),
    );
    foreach (parent::load() as $entity) {
      if ($entity->status()) {
        $entities['enabled'][] = $entity;
      }
      else {
        $entities['disabled'][] = $entity;
      }
    }
    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Queue name');
    $header['target_type'] = $this->t('Target type');
    $header['handler'] = $this->t('Queue type');
    $header['items'] = $this->t('Items');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row = [
      'data' => [
        'label' => $entity->label(),
        'target_type' => $this->entityTypeManager->getDefinition($entity->getTargetEntityTypeId())->getLabel(),
        'handler' => $entity->getHandlerPlugin()->getPluginDefinition()['title'],
        'items' => $this->getQueueItemsStatus($entity),
      ] + parent::buildRow($entity),
      'title' => $this->t('Machine name: @name', array('@name' => $entity->id())),
    ];

    return $row;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $entities = $this->load();

    $build['#type'] = 'container';
    $build['#attributes']['id'] = 'entity-queue-list';
    $build['#attached']['library'][] = 'core/drupal.ajax';

    $build['enabled']['heading']['#markup'] = '<h2>' . $this->t('Enabled', array(), array('context' => 'Plural')) . '</h2>';
    $build['disabled']['heading']['#markup'] = '<h2>' . $this->t('Disabled', array(), array('context' => 'Plural')) . '</h2>';

    foreach (array('enabled', 'disabled') as $status) {
      $build[$status]['#type'] = 'container';
      $build[$status]['#attributes'] = array('class' => array('entity-queue-list-section', $status));
      $build[$status]['table'] = array(
        '#type' => 'table',
        '#attributes' => array(
          'class' => array('entity-queue-listing-table'),
        ),
        '#header' => $this->buildHeader(),
        '#rows' => array(),
        '#cache' => [
          'contexts' => $this->entityType->getListCacheContexts(),
          'tags' => $this->entityType->getListCacheTags(),
        ],
      );
      foreach ($entities[$status] as $entity) {
        $build[$status]['table']['#rows'][$entity->id()] = $this->buildRow($entity);
      }
    }
    // @todo Use a placeholder for the entity label if this is abstracted to
    // other entity types.
    $build['enabled']['table']['#empty'] = $this->t('There are no enabled queues.');
    $build['disabled']['table']['#empty'] = $this->t('There are no disabled queues.');

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    if (isset($operations['edit'])) {
      $operations['edit']['title'] = $this->t('Configure');
    }

    // Add AJAX functionality to enable/disable operations.
    foreach (array('enable', 'disable') as $op) {
      if (isset($operations[$op])) {
        $operations[$op]['url'] = $entity->toUrl($op);
        // Enable and disable operations should use AJAX.
        $operations[$op]['attributes']['class'][] = 'use-ajax';
      }
    }

    // Allow queue handlers to add their own operations.
    $operations += $entity->getHandlerPlugin()->getQueueListBuilderOperations();

    return $operations;
  }

  /**
   * Returns the number of items in a subqueue or the number of subqueues.
   *
   * @param \Drupal\entityqueue\EntityQueueInterface $queue
   *   An entity queue object.
   *
   * @return string
   *   The number of items in a subqueue or the number of subqueues.
   */
  protected function getQueueItemsStatus(EntityQueueInterface $queue) {
    $handler = $queue->getHandlerPlugin();

    $items = NULL;
    if ($handler->supportsMultipleSubqueues()) {
      $subqueues_count = $this->entityTypeManager->getStorage('entity_subqueue')->getQuery()
        ->condition('queue', $queue->id(), '=')
        ->count()
        ->execute();

      $items = $this->t('@count subqueues', ['@count' => $subqueues_count]);
    }
    else {
      $subqueue = EntitySubqueue::load($queue->id());

      $items = $this->t('@count items', ['@count' => count($subqueue->items)]);
    }

    return $items;
  }

}
