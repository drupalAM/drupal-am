<?php

namespace Drupal\lightgallery\Group;


use Drupal\lightgallery\Field\FieldFullscreen;
use Drupal\lightgallery\Field\FieldInterface;

class GroupLightgalleryFullScreen extends GroupBase {

  /**
   * @Inherit doc.
   */
  public function getName() {
   return GroupsEnum::LIGHTGALLERY_FULL_SCREEN;
  }

  /**
   * @Inherit doc.
   */
  public function getTitle() {
   return 'Lightgallery fullscreen settings';
  }

  /**
   * @Inherit doc.
   */
  public function getOpenValue() {
    /** @var FieldInterface $field */
    $field = new FieldFullscreen();
    return $field->getName();
  }

}