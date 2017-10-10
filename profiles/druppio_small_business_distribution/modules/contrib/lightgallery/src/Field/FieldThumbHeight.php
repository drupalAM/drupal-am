<?php

namespace Drupal\lightgallery\Field;

use Drupal\lightgallery\Group\GroupLightgalleryThumbs;

class FieldThumbHeight extends FieldBase{

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
    return 'thumb_cont_height';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Height';
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
    return 'Height of the thumbnail container including padding and border.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryThumbs();
  }

}