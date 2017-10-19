<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryCore;

class FieldClosable extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'closable';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Closable';
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
    return 'Allows clicks on dimmer to close gallery.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryCore();
  }

}