<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryAutoplay;

class FieldPause extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setDefaultValue() {
    return 5000;
  }

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'pause';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Pause';
  }

  /**
   * @Inherit doc.
   */
  protected function setType(){
    return FieldTypesEnum::TEXTFIELD;
  }

  /**
   * @Inherit doc.
   */
  protected function setDescription(){
    return 'The time (in ms) between each auto transition.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryAutoplay();
  }

}