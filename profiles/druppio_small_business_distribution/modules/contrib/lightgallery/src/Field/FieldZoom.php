<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryZoom;

class FieldZoom extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'zoom';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Zoom';
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
    return 'Enable/Disable zoom option.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryZoom();
  }

}