<?php

namespace Drupal\entityqueue;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a EntityQueue entity.
 */
interface EntityQueueInterface extends ConfigEntityInterface {

  /**
   * Gets the EntityQueueHandler plugin id.
   *
   * @return string
   */
  public function getHandler();

  /**
   * Sets the EntityQueueHandler.
   *
   * @param string $handler
   *   The handler name.
   *
   * @return $this
   */
  public function setHandler($handler);

  /**
   * Gets the EntityQueueHandler plugin object.
   *
   * @return EntityQueueHandlerInterface
   */
  public function getHandlerPlugin();

  /**
   * Gets the ID of the target entity type.
   *
   * @return string
   *   The target entity type ID.
   */
  public function getTargetEntityTypeId();

  /**
   * Gets the minimum number of items that this queue can hold.
   *
   * @return int
   */
  public function getMinimumSize();

  /**
   * Gets the maximum number of items that this queue can hold.
   *
   * @return int
   */
  public function getMaximumSize();

  /**
   * Returns the behavior of exceeding the maximum number of queue items.
   *
   * If TRUE, when a maximum size is set and it is exceeded, the queue will be
   * truncated to the maximum size by removing items from the front of the
   * queue.
   *
   * @return bool
   */
  public function getActAsQueue();

  /**
   * Returns the behavior of editing the queue's items.
   *
   * Ordinarily, queues are arranged with the front of the queue (where items
   * will be removed) on top, and the back (where items will be added) on the
   * bottom.
   *
   * If TRUE, this will display the queue such that items will be added to the
   * top and removed from the bottom.
   *
   * @return bool
   */
  public function getReverseInAdmin();

  /**
   * Gets the selection settings used by a subqueue's 'items' reference field.
   *
   * @return array
   *   An array with the following keys:
   *   - target_type: The type of the entities that will be queued.
   *   - handler: The entity reference selection handler that will be used by
   *     the subqueue's 'items' field.
   *   - handler_settings: The entity reference selection handler settings that
   *     will be used by the subqueue's 'items' field.
   */
  public function getEntitySettings();

  /**
   * Gets the queue settings.
   *
   * @return array
   *   An array with the following keys:
   *   - min_size: The minimum number of items that this queue can hold.
   *   - max_size: The maximum number of items that this queue can hold.
   *   - act_as_queue: The behavior of exceeding the maximum number of queue
   *     items.
   *   - reverse_in_admin: Show the items in reverse order when editing a
   *     subqueue.
   */
  public function getQueueSettings();

  /**
   * Loads one or more queues based on their target entity type.
   *
   * @param string $target_entity_type_id
   *   The target entity type ID.
   *
   * @return static[]
   *   An array of entity queue objects, indexed by their IDs.
   */
  public static function loadMultipleByTargetType($target_entity_type_id);

}
