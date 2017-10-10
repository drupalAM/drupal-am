<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryFullScreen;

class FieldFullscreen extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'full_screen';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Full screen';
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
    return 'Enable/Disable fullscreen mode.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryFullScreen();
  }

}