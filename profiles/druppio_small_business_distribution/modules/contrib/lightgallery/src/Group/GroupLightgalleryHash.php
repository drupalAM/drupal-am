<?php

namespace Drupal\lightgallery\Group;

use Drupal\lightgallery\Field\FieldInterface;
use Drupal\lightgallery\Field\FieldHash;

class GroupLightgalleryHash extends GroupBase {

  /**
   * @Inherit doc.
   */
  public function getName() {
   return GroupsEnum::LIGHTGALLERY_HASH;
  }

  /**
   * @Inherit doc.
   */
  public function getTitle() {
   return 'Lightgallery hash settings';
  }

  /**
   * @Inherit doc.
   */
  public function getOpenValue() {
    /** @var FieldInterface $field */
    $field = new FieldHash();
    return $field->getName();
  }

}