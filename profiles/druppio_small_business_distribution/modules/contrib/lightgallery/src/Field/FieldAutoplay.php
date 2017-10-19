<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryAutoplay;

class FieldAutoplay extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'autoplay';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Autoplay';
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
    return 'Enable/Disable gallery autoplay.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryAutoplay();
  }

}