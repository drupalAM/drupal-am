<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryCore;

class FieldTouch extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'touch';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Touch';
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
    return 'Enables touch support.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryCore();
  }

}