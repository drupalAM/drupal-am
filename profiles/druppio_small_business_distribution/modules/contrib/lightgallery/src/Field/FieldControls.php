<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryCore;

class FieldControls extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'controls';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Controls';
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
    return 'If not checked, prev/next buttons will not be displayed.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryCore();
  }

}