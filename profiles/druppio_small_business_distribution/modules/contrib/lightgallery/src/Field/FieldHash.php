<?php

namespace Drupal\lightgallery\Field;

use Drupal\lightgallery\Group\GroupLightgalleryHash;

class FieldHash extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'hash';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Hash';
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
    return 'Enable/Disable hash option.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryHash();
  }

}