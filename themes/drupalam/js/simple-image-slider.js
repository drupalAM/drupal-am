(function($) {
  "use strict";

  /**
   * Simple Image Slider. Displays an image slider.
   * Dependencies:
   * - jQuery
   * - Images Loaded : http://github.com/desandro/imagesloaded
   * - Touchwipe by Andreas Waltl, netCU Internetagentur (http://www.netcu.de)
   *
   * @author Karen Pirumyan
   * http://kpirumyan.drupal.am/
   */
  $.fn.simpleImageSlider = function(options) {

    var defaults = {
      images                  : [],
      wrapperClass            : 'sis-wrapper',
      imageContainerClass     : '.sis-images',
      descClass               : 'sis-desc',
      numClass                : 'sis-imgnum',
      prevBtnClass            : 'sis-prev-arrow',
      nextBtnClass            : 'sis-next-arrow',
      minHeight               : 150,
      animationDuration       : 700,
      easing                  : 'easeOutExpo'
    },
    //define some helper variables that will be used globally by the plugin
    o                         = $.extend(defaults, options),
    $root                     = $(this),
    $imageContainer           = $root.find(o.imageContainerClass),
    $desc                     = $('<div />', {class: o.descClass}).appendTo($imageContainer),
    images                    = o.images,
    $larrow                   = null,
    $rarrow                   = null,
    imgNum                    = images.length,
    currentImgIndex           = 0,
    $img                      = null,
    containerWidth            = 0,
    $pendingImg               = null,
    $imgnum                   = null,
    inAnimation               = false;

    /**
     * Inits the main functionality - calls the initialization functions.
     */
    function init() {
      loadSlider();
      bindEventHandlers();
      showSlider();
    }

    /**
     * Loads the slider once all the images are loaded.
     */
    function loadSlider() {
      addNavigation();

      if (imgNum > 1) {
        showImageNumeration();
        showImage(true);
      }
      else {
        showImage(false);
      }
    }

    /**
     * Displays slider when loaded.
     */
    function showSlider() {
      $imageContainer.find('img').pexetoOnImgLoaded({callback: onSliderLoaded});
    }

    /**
     * Binds event handlers.
     */
    function bindEventHandlers() {
      if (imgNum > 1) {
        //navigation event handlers
        $larrow.on('click', doOnPreviousClicked);
        $rarrow.on('click', doOnNextClicked);
      }

      $(window).on('resize', doOnWindowResize);

      $imageContainer.touchwipe({
        wipeLeft: doOnNextClicked,
        wipeRight: doOnPreviousClicked,
        preventDefaultEvents: false
      });
    }

    /**
     * Displays an image in the slider.
     * @param  {boolean} next sets whether it is the next image (when it
     * is set to true) or the previous one (when it is set to false)
     */
    function showImage(next) {
      $img = $('<img />', {src: images[currentImgIndex].img}).appendTo($imageContainer);
    }

    /**
     * Displays the description of the current image.
     * @param  {string} desc the description text
     */
    function showDescription(desc) {
      if(desc) {
        $desc.html(desc).fadeIn();
      } else {
        $desc.fadeOut();
      }
    }

    /**
     * Displays current page of images.
     */
    function showImageNumeration() {
      $imgnum = $('<div />', {class: o.numClass}).html((currentImgIndex + 1) + ' / ' + images.length).appendTo($imageContainer);
    }

    /**
     * Calculates and sets the container height according to the current
     * image height.
     */
    function setContainerHeight() {
      var height = Math.max($img.get(0).clientHeight, o.minHeight);
      $root.height(height);
      $imageContainer.height(height);
    }

    /**
     * Adds the navigation elements.
     */
    function addNavigation() {
      //previous/next arrows
      $larrow = $('<div />', {class: o.prevBtnClass}).appendTo($imageContainer);
      $rarrow = $('<div />', {class: o.nextBtnClass}).appendTo($imageContainer);
    }

    /**
     * On next arrow click event handler. Shows the next image if there is
     * one.
     */
    function doOnNextClicked() {
      var $this = $(this);
      containerWidth = $root.width();

      if (!inAnimation && currentImgIndex < imgNum - 1) {
        inAnimation = true;
        $pendingImg = $('<img />', {src: images[currentImgIndex + 1].img}).appendTo($imageContainer);
        $img.css('right', '').animate({left: -containerWidth, opacity: 0.5}, {duration: o.animationDuration, easing: o.easing});
        $pendingImg.css({
          left: containerWidth,
          opacity: 1}).animate({
            left: 0
          }, {
          duration: o.animationDuration,
          easing: o.easing,
          complete: navBtnsCompleteCallback.bind($this, this)
        });
        $imgnum.html((currentImgIndex + 2) + ' / ' + imgNum);
        currentImgIndex++;
      }
      else {
        animateLastImage(true);
      }
    }

    /**
     * On previous arrow click event handler. Shows the previous one if
     * there is one.
     */
    function doOnPreviousClicked() {
      var $this = $(this);
      containerWidth = $root.width();

      if (!inAnimation && currentImgIndex > 0) {
        inAnimation = true;
        $pendingImg = $('<img />', {src: images[currentImgIndex - 1].img}).appendTo($imageContainer);
        $img.css('left', '').animate({right: -containerWidth, opacity: 0.5}, {
          duration: o.animationDuration,
          easing: o.easing
        });
        $pendingImg.css({right: containerWidth, opacity: 1}).animate({right: 0}, {
          duration: o.animationDuration,
          easing: o.easing,
          complete: navBtnsCompleteCallback.bind($this, this)
        });
        $imgnum.html((currentImgIndex) + ' / ' + imgNum);
        currentImgIndex--;
      }
      else {
        animateLastImage(false);
      }
    }

    /**
     * On slider loaded event handler.
     */
    function onSliderLoaded() {
      $root.fadeIn();
      $img.animate({opacity: 1}, 1000);
      setContainerHeight();
    }

    /**
     * Animates the slider in a way to show that there isn't anymore images
     * to display.
     * @param  {boolean} last sets whether it is the last image (when set
     * to true) or whether it is the first image (when set to false)
     */
    function animateLastImage(last) {
      var i = last ? -1 : 1;
      $img.stop().animate({
        left: i * 10
      }, 100, function() {
        $(this).stop().animate({
          left: 0
        }, 300);
      });
    }

    /**
     * On window resize event handler. Resets the container size.
     */
    function doOnWindowResize() {console.log('last');
      setContainerHeight();
    }

    /**
     * Calls the function after slider animation.
     */
    function navBtnsCompleteCallback(btn) {
      $img.remove();
      $img = $pendingImg;
      $pendingImg = null;
      inAnimation = false;
    }

    init();
  }

}(jQuery));