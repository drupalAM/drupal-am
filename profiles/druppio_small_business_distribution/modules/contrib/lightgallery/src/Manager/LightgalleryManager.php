<?php

namespace Drupal\lightgallery\Manager;


use Drupal\image\Entity\ImageStyle;
use Drupal\lightgallery\Field\FieldAnimateThumb;
use Drupal\lightgallery\Field\FieldAutoplay;
use Drupal\lightgallery\Field\FieldAutoplayControls;
use Drupal\lightgallery\Field\FieldClosable;
use Drupal\lightgallery\Field\FieldControls;
use Drupal\lightgallery\Field\FieldCounter;
use Drupal\lightgallery\Field\FieldCurrentPagerPosition;
use Drupal\lightgallery\Field\FieldDownload;
use Drupal\lightgallery\Field\FieldDrag;
use Drupal\lightgallery\Field\FieldEscKey;
use Drupal\lightgallery\Field\FieldFullscreen;
use Drupal\lightgallery\Field\FieldGallerId;
use Drupal\lightgallery\Field\FieldGalleryId;
use Drupal\lightgallery\Field\FieldHash;
use Drupal\lightgallery\Field\FieldImage;
use Drupal\lightgallery\Field\FieldKeyPress;
use Drupal\lightgallery\Field\FieldLightgalleryImageStyle;
use Drupal\lightgallery\Field\FieldLoop;
use Drupal\lightgallery\Field\FieldMode;
use Drupal\lightgallery\Field\FieldMouseWheel;
use Drupal\lightgallery\Field\FieldPager;
use Drupal\lightgallery\Field\FieldPause;
use Drupal\lightgallery\Field\FieldPreload;
use Drupal\lightgallery\Field\FieldProgress;
use Drupal\lightgallery\Field\FieldScale;
use Drupal\lightgallery\Field\FieldThumbHeight;
use Drupal\lightgallery\Field\FieldThumbImageStyle;
use Drupal\lightgallery\Field\FieldThumbnail;
use Drupal\lightgallery\Field\FieldThumbWidth;
use Drupal\lightgallery\Field\FieldTitle;
use Drupal\lightgallery\Field\FieldTitleSource;
use Drupal\lightgallery\Field\FieldTouch;
use Drupal\lightgallery\Field\FieldUseThumbs;
use Drupal\lightgallery\Field\FieldZoom;
use Drupal\lightgallery\Optionset\LightgalleryOptionSetInterface;

class LightgalleryManager {

  protected $optionSet;

  /**
   * LightgalleryManager constructor.
   * @param \Drupal\lightgallery\Optionset\LightgalleryOptionSetInterface $option_set
   */
  public function __construct(LightgalleryOptionSetInterface $option_set) {
    $this->optionSet = $option_set;
  }


  /**
   * Loads libraries to init lightgallery.
   * @param $id
   * @return array
   */
  public function loadLibraries($id) {
    $attached = [];

    // JavaScript settings
    $js_settings = array(
      'instances' => array(
        $id => $this->optionSet->get(),
      ),
    );
    // Add settings.
    $attached['drupalSettings']['lightgallery'] = $js_settings;
    // Add loader file.
    // We don't need to add the lightgallery library manually,
    // Because there is a dependency on it.
    $attached['library'][] = 'lightgallery/lightgallery.load';


    return $attached;
  }

  /**
   * Returns all fields that have to be displayed on settings form.
   */
  public static function getSettingFields() {
    return array(
      // LIGHTGALLERY CORE FIELDS.
      new FieldThumbnail(),
      new FieldImage(),
      new FieldTitle(),
      new FieldThumbImageStyle(),
      new FieldLightgalleryImageStyle(),
      new FieldTitleSource(),
      new FieldMode(),
      new FieldPreload(),
      new FieldClosable(),
      new FieldLoop(),
      new FieldEscKey(),
      new FieldKeyPress(),
      new FieldControls(),
      new FieldMouseWheel(),
      new FieldDownload(),
      new FieldCounter(),
      new FieldDrag(),
      new FieldTouch(),
      // LIGHTGALLERY THUMB FIELDS.
      new FieldUseThumbs(),
      new FieldAnimateThumb(),
      new FieldCurrentPagerPosition(),
      new FieldThumbWidth(),
      new FieldThumbHeight(),
      // LIGHTGALLERY AUTPLAY FIELDS.
      new FieldAutoplay(),
      new FieldPause(),
      new FieldProgress(),
      new FieldAutoplayControls(),
      // LIGHTGALLERY FULL SCREEN FIELDS.
      new FieldFullscreen(),
      // LIGHTGALLERY PAGER FIELDS.
      new FieldPager(),
      // LIGHTGALLERY ZOOM FIELDS.
      new FieldZoom(),
      new FieldScale(),
      // LIGHTGALLERY HASH FIELDS.
      new FieldHash(),
      new FieldGalleryId(),
    );
  }

  /**
   * Returns formatted array of all image styles.
   */
  public static function getImageStyles() {
    $options = array('' => t('Original image'));
    $image_styles = ImageStyle::loadMultiple();

    /** @var ImageStyle $image_style */
    foreach ($image_styles as $image_style) {
      $options[$image_style->id()] = $image_style->label();
    }

    return $options;
  }

  /**
   * Returns list of values that can be used as title field.
   */
  public static function getImageSourceFields() {
    return array(
      '' => t('None'),
      'alt' => t('Image - Alt text'),
      'title' => t('Image - Title text'),
    );
  }

  /**
   * Returns all available lightgallery modes.
   * @return array
   */
  public static function getLightgalleryModes() {
    $modes = array(
      'lg-slide',
      'lg-fade',
      'lg-zoom-in',
      'lg-zoom-in-big',
      'lg-zoom-out',
      'lg-zoom-out-big',
      'lg-zoom-out-in',
      'lg-zoom-in-out',
      'lg-soft-zoom',
      'lg-scale-up',
      'lg-slide-circular',
      'lg-slide-circular-vertical',
      'lg-slide-vertical',
      'lg-slide-vertical-growth',
      'lg-slide-skew-only',
      'lg-slide-skew-only-rev',
      'lg-slide-skew-only-y',
      'lg-slide-skew-only-y-rev',
      'lg-slide-skew',
      'lg-slide-skew-rev',
      'lg-slide-skew-cross',
      'lg-slide-skew-cross-rev',
      'lg-slide-skew-ver',
      'lg-slide-skew-ver-rev',
      'lg-slide-skew-ver-cross',
      'lg-slide-skew-ver-cross-rev',
      'lg-lollipop',
      'lg-lollipop-rev',
      'lg-rotate',
      'lg-rotate-rev',
      'lg-tube'
    );

    return array_combine($modes, $modes);
  }

  /**
   * Returns preload options.
   * @return array
   */
  public static function getPreloadOptions() {
    return array_combine(array(1, 2, 3, 4), array(1, 2, 3, 4));
  }

  /**
   * Returns scale options.
   * @return array
   */
  public static function getScaleOptions() {
    return array_combine(array(1, 2, 3, 4), array(1, 2, 3, 4));
  }

  /**
   * Returns current pager options.
   * @return array
   */
  public static function getCurrentPagerPositionOptions() {
    return array(
      'left' => t('Left'),
      'middle' => t('Middle'),
      'right' => t('Right')
    );
  }

  /**
   * Flatten array and preserve keys.
   * @param array $array
   * @return array
   */
  public static function flattenArray(array $array) {
    $flattened_array = array();
    array_walk_recursive($array,
      function ($a, $key) use (&$flattened_array) {
        $flattened_array[$key] = $a;
      });
    return $flattened_array;
  }

}