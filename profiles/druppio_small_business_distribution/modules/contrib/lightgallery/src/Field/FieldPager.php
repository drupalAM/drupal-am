<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryPager;

class FieldPager extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'pager';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Pager';
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
    return 'Enable/Disable pager.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryPager();
  }

}