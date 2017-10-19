<?php

namespace Drupal\lightgallery\Optionset;


class LightgalleryOptionset implements LightgalleryOptionSetInterface {

  protected $options;
  protected $useThumbs = FALSE;
  protected $autoplay = FALSE;
  protected $zoom = FALSE;
  protected $hash = FALSE;

  /**
   * LightgalleryOptionset constructor.
   * @param $options
   */
  public function __construct($options) {
    $this->options = $options;

    if ($this->options['thumbnails']) {
      $this->useThumbs = TRUE;
    }
    if ($this->options['autoplay']) {
      $this->autoplay = TRUE;
    }
    if ($this->options['zoom']) {
      $this->zoom = TRUE;
    }
    if ($this->options['hash']) {
      $this->hash = TRUE;
    }
  }

  /**
   * Returns the formatted optionset.
   * @return array
   */
  public function get() {
    $option_set = array(
      'mode' => !empty($this->options['mode']) ? $this->options['mode'] : 'lg-slide',
      'preload' => !empty($this->options['preload']) ? $this->options['preload'] : 1,
      'loop' => !empty($this->options['loop']) ? TRUE : FALSE,
      'closable' => !empty($this->options['closable']) ? TRUE : FALSE,
      'escKey' => !empty($this->options['esc_key']) ? TRUE : FALSE,
      'keyPress' => !empty($this->options['key_press']) ? TRUE : FALSE,
      'controls' => !empty($this->options['controls']) ? TRUE : FALSE,
      'mousewheel' => !empty($this->options['mouse_wheel']) ? TRUE : FALSE,
      'download' => !empty($this->options['download']) ? TRUE : FALSE,
      'counter' => !empty($this->options['counter']) ? TRUE : FALSE,
      'enableDrag' => !empty($this->options['drag']) ? TRUE : FALSE,
      'enableTouch' => !empty($this->options['touch']) ? TRUE : FALSE,
      'thumbnail' => !empty($this->options['thumbnails']) ? TRUE : FALSE,
      'autoplay' => !empty($this->options['autoplay']) ? TRUE : FALSE,
      'fullScreen' => !empty($this->options['full_screen']) ? TRUE : FALSE,
      'pager' => !empty($this->options['pager']) ? TRUE : FALSE,
      'zoom' => !empty($this->options['zoom']) ? TRUE : FALSE,
      'hash' => !empty($this->options['hash']) ? TRUE : FALSE,
    );

    if ($this->useThumbs) {
      // Add extra thumb options.
      $option_set['animateThumb'] = !empty($this->options['animate_thumb']) ? TRUE : FALSE;
      $option_set['currentPagerPosition'] = !empty($this->options['current_pager_position']) ? $this->options['current_pager_position'] : 'middle';
      $option_set['thumbWidth'] = (int) !empty($this->options['thumb_width']) ? $this->options['thumb_width'] : 100;
      $option_set['thumbContHeight'] = (int) !empty($this->options['thumb_cont_height']) ? $this->options['thumb_cont_height'] : 100;
    }

    if ($this->autoplay) {
      // Add extra autoplay options.
      $option_set['pause'] = (int) !empty($this->options['pause']) ? $this->options['pause'] : 5000;
      $option_set['progressBar'] = !empty($this->options['progress_bar']) ? TRUE : FALSE;
      $option_set['autoplayControls'] = !empty($this->options['autoplay_controls']) ? TRUE : FALSE;
    }

    if ($this->zoom) {
      // Add extra zoom options.
      $option_set['scale'] = (int) !empty($this->options['scale']) ? $this->options['scale'] : 1;
    }

    if ($this->hash) {
      // Add extra hash (a lot more hash pleazzzzz) options.
      $option_set['galleryId'] = (int) !empty($this->options['gallery_id']) ? $this->options['gallery_id'] : 1;
    }

    return $option_set;
  }

}