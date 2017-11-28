<?php

namespace Drupal\better_normalizers\Normalizer;

/**
 * Defines a value object to track an entity type and ID pair.
 */
class EntityStub {

  /**
   * Entity Type ID.
   *
   * @var string
   */
  protected $entityTypeId;

  /**
   * Entity ID.
   *
   * @var mixed
   */
  protected $entityId;

  /**
   * Factory method to create new stub from entity URI.
   *
   * @param string $uri
   *   Entity URI to parse.
   *
   * @return static
   *   New instance.
   *
   * @throws \InvalidArgumentException
   *   When not an entity URI.
   */
  public static function fromEntityUri($uri) {
    $scheme = parse_url($uri, PHP_URL_SCHEME);
    if ($scheme !== 'entity') {
      throw new \InvalidArgumentException();
    }
    $path = parse_url($uri, PHP_URL_PATH);
    $static = new static();
    list($static->entityTypeId, $static->entityId) = explode('/', $path);
    return $static;
  }

  /**
   * Gets value of entity_id.
   *
   * @return mixed
   *   Value of entity_id
   */
  public function getEntityId() {
    return $this->entityId;
  }

  /**
   * Gets value of entity_type_id.
   *
   * @return mixed
   *   Value of entity_type_id
   */
  public function getEntityTypeId() {
    return $this->entityTypeId;
  }

}
