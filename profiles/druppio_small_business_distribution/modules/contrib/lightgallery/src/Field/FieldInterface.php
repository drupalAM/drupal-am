<?php

namespace Drupal\lightgallery\Field;

interface FieldInterface {

  /**
   * Returns field name.
   */
  public function getName();

  /**
   * Returns field title.
   */
  public function getTitle();

  /**
   * Returns field type.
   */
  public function getType();

  /**
   * Returns field description.
   */
  public function getDescription();

  /**
   * Returns if field is required.
   */
  public function isRequired();

  /**
   * Returns where the field has to be rendered in view settings.
   */
  public function appliesToViews();

  /**
   * Returns where the field has to be rendered in field formatter settings.
   */
  public function appliesToFieldFormatter();

  /**
   * Returns field group (parent).
   */
  public function getGroup();

  /**
   * Returns field default value.
   */
  public function getDefaultValue();

  /**
   * Returns field options callback.
   */
  public function getOptions();
}