<?php

namespace Drupal\lightgallery\Group;


abstract class GroupsEnum {
  const LIGHTGALLERY_CORE = 'lightgallery_core';
  const LIGHTGALLERY_THUMBS = 'lightgallery_thumbs';
  const LIGHTGALLERY_AUTOPLAY = 'lightgallery_autoplay';
  const LIGHTGALLERY_FULL_SCREEN = 'lightgallery_full_screen';
  const LIGHTGALLERY_PAGER = 'lightgallery_pager';
  const LIGHTGALLERY_ZOOM = 'lightgallery_zoom';
  const LIGHTGALLERY_HASH = 'lightgallery_hash';

  /**
   * Returns array of all groups.
   */
  public static function toArray() {
    return array(
      self::LIGHTGALLERY_CORE,
      self::LIGHTGALLERY_THUMBS,
      self::LIGHTGALLERY_AUTOPLAY,
      self::LIGHTGALLERY_FULL_SCREEN,
      self::LIGHTGALLERY_PAGER,
      self::LIGHTGALLERY_ZOOM,
      self::LIGHTGALLERY_HASH,
    );
  }
}