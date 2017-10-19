<?php

namespace Drupal\lightgallery\Field;

use Drupal\lightgallery\Group\GroupLightgalleryThumbs;

class FieldAnimateThumb extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'animate_thumb';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Animate thumbnails';
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
    return 'Enable thumbnail animation.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryThumbs();
  }

}