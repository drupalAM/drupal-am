<?php

namespace Drupal\lightgallery\Group;

use Drupal\lightgallery\Field\FieldAutoplay;
use Drupal\lightgallery\Field\FieldInterface;

class GroupLightgalleryAutoplay extends GroupBase {

  /**
   * @Inherit doc.
   */
  public function getName() {
   return GroupsEnum::LIGHTGALLERY_AUTOPLAY;
  }

  /**
   * @Inherit doc.
   */
  public function getTitle() {
   return 'Lightgallery autoplay settings';
  }

  /**
   * @Inherit doc.
   */
  public function getOpenValue() {
    /** @var FieldInterface $field */
    $field = new FieldAutoplay();
    return $field->getName();
  }
}