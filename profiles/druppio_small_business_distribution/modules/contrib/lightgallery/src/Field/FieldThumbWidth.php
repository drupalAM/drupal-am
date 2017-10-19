<?php

namespace Drupal\lightgallery\Field;

use Drupal\lightgallery\Group\GroupLightgalleryThumbs;

class FieldThumbWidth extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setDefaultValue() {
    return 100;
  }

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'thumb_width';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Width';
  }

  /**
   * @Inherit doc.
   */
  protected function setType(){
    return FieldTypesEnum::TEXTFIELD;
  }

  /**
   * @Inherit doc.
   */
  protected function setDescription(){
    return 'Width of each thumbnail.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryThumbs();
  }

}