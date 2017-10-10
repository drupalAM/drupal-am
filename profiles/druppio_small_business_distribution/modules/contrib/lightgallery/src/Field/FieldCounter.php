<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryCore;

class FieldCounter extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'counter';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Counter';
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
    return 'Whether to show total number of images and index number of currently displayed image.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryCore();
  }

}