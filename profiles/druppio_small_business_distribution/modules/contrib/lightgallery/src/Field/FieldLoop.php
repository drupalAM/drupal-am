<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryCore;

class FieldLoop extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'loop';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Loop';
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
    return 'If not selected, the ability to loop back to the beginning of the gallery when on the last element, will be disabled.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryCore();
  }

}