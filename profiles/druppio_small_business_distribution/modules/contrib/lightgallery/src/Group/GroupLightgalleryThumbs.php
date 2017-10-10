<?php

namespace Drupal\lightgallery\Group;


use Drupal\lightgallery\Field\FieldInterface;
use Drupal\lightgallery\Field\FieldUseThumbs;

class GroupLightgalleryThumbs extends GroupBase {

  /**
   * @Inherit doc.
   */
  public function getName() {
   return GroupsEnum::LIGHTGALLERY_THUMBS;
  }

  /**
   * @Inherit doc.
   */
  public function getTitle() {
   return 'Lightgallery thumbnail settings';
  }

  /**
   * @Inherit doc.
   */
  public function getOpenValue() {
    /** @var FieldInterface $field */
    $field = new FieldUseThumbs();
    return $field->getName();
  }

}