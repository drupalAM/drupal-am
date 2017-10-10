<?php

namespace Drupal\lightgallery\Field;

use Drupal\lightgallery\Group\GroupLightgalleryThumbs;

class FieldUseThumbs extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'thumbnails';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Use thumbnails';
  }

  /**
   * @Inherit doc.
   */
  protected function setType(){
    return FieldTypesEnum::CHECKBOX;
  }

  /**
   * @Inherit doc.
   */
  protected function setDescription(){
    return 'Indicate if you want to use thumbnails in the LightGallery.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryThumbs();
  }

}