<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryCore;

class FieldTitleSource extends FieldBase{

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
    return ['Drupal\lightgallery\Manager\LightgalleryManager', 'getImageSourceFields'];
  }

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'title_source';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Title source';
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
    return 'The image value that should be used for the title.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryCore();
  }

}