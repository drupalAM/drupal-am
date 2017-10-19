<?php

namespace Drupal\lightgallery\Field;


abstract class FieldBase implements FieldInterface {

  protected $name;
  protected $title;
  protected $type;
  protected $description;
  protected $isRequired;
  protected $group;
  protected $defaultValue;
  protected $options;

  public function __construct() {
    $this->name = $this->setName();
    $this->title = $this->setTitle();
    $this->type = $this->setType();
    $this->description = $this->setDescription();
    $this->isRequired = $this->setIsRequired();
    $this->group = $this->setGroup();
    $this->defaultValue = $this->setDefaultValue();
    $this->options = $this->setOptions();
  }

  /**
   * @Inherit doc.
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @Inherit doc.
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * @Inherit doc.
   */
  public function getType() {
    return $this->type;
  }

  /**
   * @Inherit doc.
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * @Inherit doc.
   */
  public function isRequired() {
    return $this->isRequired;
  }

  /**
   * @Inherit doc.
   */
  public function getGroup() {
    return $this->group;
  }

  /**
   * @Inherit doc.
   */
  public function getDefaultValue() {
    return $this->defaultValue;
  }

  /**
   * @Inherit doc.
   */
  public function appliesToViews() {
    return TRUE;
  }

  /**
   * @Inherit doc.
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * @Inherit doc.
   */
  public function appliesToFieldFormatter() {
    return TRUE;
  }

  /**
   * Sets required flag.
   */
  protected function setIsRequired() {
    return FALSE;
  }

  /**
   * Sets default value.
   */
  protected function setDefaultValue() {
    return TRUE;
  }

  /**
   * Sets options.
   */
  protected function setOptions() {
    return FALSE;
  }

  /**
   * Sets name.
   */
  protected abstract function setName();

  /**
   * Sets title.
   */
  protected abstract function setTitle();

  /**
   * Sets type.
   */
  protected abstract function setType();

  /**
   * Sets description.
   */
  protected abstract function setDescription();

  /**
   * Sets group.
   */
  protected abstract function setGroup();

}