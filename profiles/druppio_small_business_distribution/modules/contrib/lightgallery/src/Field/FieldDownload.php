<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryCore;

class FieldDownload extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'download';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Download';
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
    return 'Enable download button. '
    . 'By default download url will be taken from data-src/href attribute but it supports only for modern browsers.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryCore();
  }

}