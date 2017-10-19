<?php

namespace Drupal\lightgallery\Group;


abstract class GroupBase implements GroupInterface{

  /**
   * @Inherit doc
   */
  public function isOpen() {
   return FALSE;
  }

  /**
   * @Inherit doc
   */
  public function getOpenValue() {
   return FALSE;
  }

}