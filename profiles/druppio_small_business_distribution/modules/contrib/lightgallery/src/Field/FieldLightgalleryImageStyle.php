<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryCore;

class FieldLightgalleryImageStyle extends FieldBase{

  /**
   * @Inherit doc.
   */
  public function appliesToViews() {
    return FALSE;
  }

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
    return ['Drupal\lightgallery\Manager\LightgalleryManager', 'getImageStyles'];
  }

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'lightgallery_image_style';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Lightgallery image style';
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
    return 'The image style used when viewing the lightgallery.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryCore();
  }

}