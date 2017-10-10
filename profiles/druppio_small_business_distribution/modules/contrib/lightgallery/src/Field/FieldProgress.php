<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryAutoplay;

class FieldProgress extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'progress_bar';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Progress bar';
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
    return 'Enable/Disable autoplay progress bar.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryAutoplay();
  }

}