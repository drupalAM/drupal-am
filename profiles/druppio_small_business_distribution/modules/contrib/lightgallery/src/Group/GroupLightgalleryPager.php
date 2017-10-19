<?php

namespace Drupal\lightgallery\Group;

use Drupal\lightgallery\Field\FieldInterface;
use Drupal\lightgallery\Field\FieldPager;

class GroupLightgalleryPager extends GroupBase {

  /**
   * @Inherit doc.
   */
  public function getName() {
   return GroupsEnum::LIGHTGALLERY_PAGER;
  }

  /**
   * @Inherit doc.
   */
  public function getTitle() {
   return 'Lightgallery pager settings';
  }

  /**
   * @Inherit doc.
   */
  public function getOpenValue() {
    /** @var FieldInterface $field */
    $field = new FieldPager();
    return $field->getName();
  }

}