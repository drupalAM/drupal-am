<?php

namespace Drupal\lightgallery\Field;


use Drupal\lightgallery\Group\GroupLightgalleryHash;

class FieldGalleryId extends FieldBase{

  /**
   * @Inherit doc.
   */
  protected function setDefaultValue() {
    return 1;
  }

  /**
   * @Inherit doc.
   */
  protected function setName(){
    return 'gallery_id';
  }

  /**
   * @Inherit doc.
   */
  protected function setTitle(){
    return 'Gallery ID';
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
    return 'Unique id for each gallery. It is mandatory when you use hash plugin for multiple galleries on the same page.';
  }

  /**
   * @Inherit doc.
   */
  protected function setGroup(){
    return new GroupLightgalleryHash();
  }

}