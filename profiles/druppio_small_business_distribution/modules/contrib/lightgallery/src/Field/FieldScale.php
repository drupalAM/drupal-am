<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryZoom;

class FieldScale extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setDefaultValue() {
    return 1;
  }

  /**
   * @Inherit doc.
   */
  protected function setOptions() {
    return ['Drupal\lightgallery\Manager\LightgalleryManager', 'getScaleOptions'];
  }

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'scale';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Scale';
  }

  /**
   * @Inherit doc.
   */
  protected function setType(){
    return FieldTypesEnum::SELECT;
  }

  /**
   * @Inherit doc.
   */
  protected function setDescription(){
    return 'Value of zoom that should be incremented/decremented.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryZoom();
  }

}