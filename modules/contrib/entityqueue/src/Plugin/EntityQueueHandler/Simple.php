<?php

namespace Drupal\entityqueue\Plugin\EntityQueueHandler;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\entityqueue\Entity\EntitySubqueue;
use Drupal\entityqueue\EntityQueueHandlerBase;
use Drupal\entityqueue\EntityQueueInterface;

/**
 * Defines an entity queue handler that manages a single subqueue.
 *
 * @EntityQueueHandler(
 *   id = "simple",
 *   title = @Translation("Simple queue")
 * )
 */
class Simple extends EntityQueueHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function supportsMultipleSubqueues() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function hasAutomatedSubqueues() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getQueueListBuilderOperations() {
    // Simple queues have just one subqueue so we can link directly to the edit
    // form.
    $operations['edit_subqueue'] = [
      'title' => $this->t('Edit items'),
      'weight' => -9,
      'url' => EntitySubqueue::load($this->queue->id())->urlInfo('edit-form'),
    ];

    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function onQueuePostSave(EntityQueueInterface $queue, EntityStorageInterface $storage, $update = TRUE) {
    // Make sure that every simple queue has a subqueue.
    if (!$update) {
      $subqueue = EntitySubqueue::create([
        'queue' => $queue->id(),
        'name' => $queue->id(),
        'title' => $queue->label(),
        'langcode' => $queue->language()->getId(),
      ]);
      $subqueue->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onQueuePostDelete(EntityQueueInterface $queue, EntityStorageInterface $storage) {
    // Delete the subqueue when the parent queue is deleted.
    if ($subqueue = EntitySubqueue::load($queue->id())) {
      $subqueue->delete();
    }
  }

}
