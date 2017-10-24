<?php

namespace Drupal\entityqueue;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Defines a class that builds a listing of entity subqueues.
 */
class EntitySubqueueListBuilder extends EntityListBuilder {

  /**
   * The ID of the entity queue for which to list all subqueues.
   *
   * @var \Drupal\entityqueue\Entity\EntityQueue
   */
  protected $queueId;

  /**
   * Sets the entity queue ID.
   *
   * @param string $queue_id
   *   The entity queue ID.
   *
   * @return $this
   */
  public function setQueueId($queue_id) {
    $this->queueId = $queue_id;

    return $this;
  }

  /**
   * Loads entity IDs using a pager sorted by the entity id and optionally
   * filtered by bundle.
   *
   * @return array
   *   An array of entity IDs.
   */
  protected function getEntityIds() {
    $query = $this->getStorage()->getQuery()
      ->sort($this->entityType->getKey('id'));

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $query->pager($this->limit);
    }

    if ($this->queueId) {
      $query->condition($this->entityType->getKey('bundle'), $this->queueId);
    }

    return $query->execute();
  }


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Subqueue');
    $header['items'] = $this->t('Items');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['items'] = $this->t('@count items', ['@count' => count($entity->items)]);

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    $operations['edit']['title'] = $this->t('Edit items');

    return $operations;
  }

}
