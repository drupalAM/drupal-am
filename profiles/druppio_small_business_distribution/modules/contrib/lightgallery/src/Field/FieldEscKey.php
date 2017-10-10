<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryCore;

class FieldEscKey extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'esc_key';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Escape key';
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
    return 'Whether the LightGallery could be closed by pressing the "Esc" key.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryCore();
  }

}