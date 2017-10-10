<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryCore;

class FieldKeyPress extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'key_press';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Keyboard';
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
    return 'Enable keyboard navigation.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryCore();
  }

}