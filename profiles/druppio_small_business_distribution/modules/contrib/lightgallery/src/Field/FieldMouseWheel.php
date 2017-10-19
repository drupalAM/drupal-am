<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryCore;

class FieldMouseWheel extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'mouse_wheel';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Mouse wheel';
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
    return 'Change slide on mousewheel.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryCore();
  }

}