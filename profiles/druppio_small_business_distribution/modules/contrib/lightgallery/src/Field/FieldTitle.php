<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryCore;

class FieldTitle extends FieldBase{

  /**
   * @Inherit doc.
   */
  public function appliesToFieldFormatter() {
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
    return ['Drupal\lightgallery\Plugin\views\style\LightGallery', 'getNonImageFields'];
  }

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'title';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Title field';
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
    return 'Select the field you want to use as title in the Lightgallery. Leave empty to omit titles.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryCore();
  }

}