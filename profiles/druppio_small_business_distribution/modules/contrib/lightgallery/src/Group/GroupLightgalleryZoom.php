<?php

namespace Drupal\lightgallery\Group;

use Drupal\lightgallery\Field\FieldInterface;
use Drupal\lightgallery\Field\FieldZoom;

class GroupLightgalleryZoom extends GroupBase {

  /**
   * @Inherit doc.
   */
  public function getName() {
   return GroupsEnum::LIGHTGALLERY_ZOOM;
  }

  /**
   * @Inherit doc.
   */
  public function getTitle() {
   return 'Lightgallery zoom settings';
  }

  /**
   * @Inherit doc.
   */
  public function getOpenValue() {
    /** @var FieldInterface $field */
    $field = new FieldZoom();
    return $field->getName();
  }

}