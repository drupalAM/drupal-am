<?php

namespace Drupal\lightgallery\Field;

use Drupal\lightgallery\Group\GroupLightgalleryThumbs;

class FieldCurrentPagerPosition extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setDefaultValue() {
    return 'middle';
  }

  /**
   * @Inherit doc.
   */
  protected function setOptions() {
    return ['Drupal\lightgallery\Manager\LightgalleryManager', 'getCurrentPagerPositionOptions'];
  }

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'current_pager_position';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Position';
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
    return 'Position of selected thumbnail.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryThumbs();
  }

}