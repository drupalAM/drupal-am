<?php

namespace Drupal\entityqueue;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a EntityQueue entity.
 */
interface EntitySubqueueInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Returns the subqueue's parent queue entity.
   *
   * @return \Drupal\entityqueue\EntityQueueInterface
   *   The parent queue entity.
   */
  public function getQueue();

  /**
   * Sets the subqueue's parent queue entity.
   *
   * @param \Drupal\entityqueue\EntityQueueInterface $queue
   *   The parent queue entity.
   *
   * @return $this
   */
  public function setQueue(EntityQueueInterface $queue);

  /**
   * Gets the subqueue title.
   *
   * @return string
   *   Title of the subqueue.
   */
  public function getTitle();

  /**
   * Sets the subqueue title.
   *
   * @param string $title
   *   The subqueue title.
   *
   * @return \Drupal\entityqueue\EntitySubqueueInterface
   *   The called subqueue entity.
   */
  public function setTitle($title);

  /**
   * Gets the subqueue creation timestamp.
   *
   * @return int
   *   Creation timestamp of the subqueue.
   */
  public function getCreatedTime();

  /**
   * Sets the subqueue creation timestamp.
   *
   * @param int $timestamp
   *   The subqueue creation timestamp.
   *
   * @return \Drupal\entityqueue\EntitySubqueueInterface
   *   The called subqueue entity.
   */
  public function setCreatedTime($timestamp);

}
