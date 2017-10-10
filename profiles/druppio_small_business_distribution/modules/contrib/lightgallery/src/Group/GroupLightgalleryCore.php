<?php

namespace Drupal\lightgallery\Group;

class GroupLightgalleryCore extends GroupBase {

  /**
   * @Inherit doc.
   */
  public function getName() {
   return GroupsEnum::LIGHTGALLERY_CORE;
  }

  /**
   * @Inherit doc.
   */
  public function getTitle() {
   return 'Lightgallery core settings';
  }

  /**
   * @Inherit doc.
   */
  public function isOpen() {
    return TRUE;
  }

}