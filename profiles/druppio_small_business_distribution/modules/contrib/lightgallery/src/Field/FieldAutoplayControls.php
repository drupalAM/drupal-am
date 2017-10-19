<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryAutoplay;

class FieldAutoplayControls extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'autoplay_controls';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Autoplay controls';
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
    return 'Show/hide autoplay controls.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryAutoplay();
  }

}