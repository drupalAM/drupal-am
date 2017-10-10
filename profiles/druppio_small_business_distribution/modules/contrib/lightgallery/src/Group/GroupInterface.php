<?php

namespace Drupal\lightgallery\Group;


interface GroupInterface {

  /**
   * Returns name.
   */
  public function getName();

  /**
   * Returns title.
   */
  public function getTitle();

  /**
   * Boolean indicating if "details" have to be open.
   */
  public function isOpen();

  /**
   * Returns value where "open" property is dependent to.
   */
  public function getOpenValue();
}