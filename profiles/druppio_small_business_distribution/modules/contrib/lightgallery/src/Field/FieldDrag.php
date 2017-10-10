<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryCore;

class FieldDrag extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'drag';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Drag';
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
    return 'Enables desktop mouse drag support.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryCore();
  }

}