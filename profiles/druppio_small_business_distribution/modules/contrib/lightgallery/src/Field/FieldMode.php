<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryCore;

class FieldMode extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setDefaultValue() {
    return NULL;
  }

  /**
   * @Inherit doc.
   */
  protected function setOptions() {
    return ['Drupal\lightgallery\Manager\LightgalleryManager', 'getLightgalleryModes'];
  }

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'mode';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Transition';
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
    return 'Type of transition between images.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryCore();
  }

}