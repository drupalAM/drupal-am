/**
 * Portfolio item slider (carousel) - displays a set of images, separated by pages.
 * The pages can be changed by clicking on arrows with an animation.
 * @author Pexeto
 * http://pexetothemes.com
 */
(function ($) {
  "use strict";

  var carouselId = 0;

  $.fn.pexetoCarousel = function (options) {
    carouselId++;

    var defaults = {
      //set the default options (can be overwritten from the calling function)
      minItemWidth: 290,
      namespace: 'carousel' + carouselId,
      itemMargin: 12,
      shadowWidth: 0,
      selfDisplay: true, //if set to true, the carousel will get displayed 
      //as soon as it is loaded. Otherwise, the calling code would be
      //responsible to display the carousel (set its opacity to 1)

      //selectors and classes
      holderSelector: '.view-content',
      pageWrapperSelector: '.page-wrapper',
      wrapperSel: '.carousel-holder',
      wrapperContainer: '.views-element-container',
      itemSelector: '.item',
      titleSelector: '.portfolio-project-title',
      hoverClass: 'portfolio-hover',
      headerSelector: '.pc-header'
    },
    o = $.extend(defaults, options),
    //define some variables that will be used globally within the script
    $container = this,
    $root = $container.find(o.holderSelector).eq(0),
    $items = $root.find(o.itemSelector),
    $wrapper = $container.find(o.wrapperSel),
    $header = $container.find(o.headerSelector),
    pageNumber = 0,
    itemsNumber = $items.length,
    currentPage = 0,
    inAnimation = false,
    pageWidth = $root.parents().find(o.wrapperContainer).eq(0).width(),
    itemsPerPage = 0,
    columns = 0,
    $prevArrow = null,
    $nextArrow = null;
    /**
     * Inits the main functionality.
     */

    function init() {

      var defWidth = parseInt($items.eq(0).data('defwidth'), 10);
      if ($container.hasClass('pc-no-spacing')) {
        o.itemMargin = 0;
      }

      if (defWidth && defWidth > 100) {
        o.minItemWidth = defWidth - 70;
      }
      
      pageNumber = $root.find(o.pageWrapperSelector).length;

      if (pageNumber > 1) {
        //show the arrows and add the animation functionality if there are 
        //more than one pages
        buildNavigation();
      }

      setImageSize();

      bindEventHandlers();

      if (o.selfDisplay) {
        $container.animate({
          opacity: 1
        });
      }

      itemsPerPage = $root.find(o.pageWrapperSelector + ':first' + ' ' + o.itemSelector).length;
    }

    /**
     * Sets the image size according to the current wrapper width.
     */
    function setImageSize() {
      var itemWidth, rootWidth;

      columns = Math.floor(($container.width() - o.itemMargin) / (o.minItemWidth + o.itemMargin));

      if (columns <= 1) {
        columns = 2;
      }
      itemWidth = Math.floor(($container.width() + o.itemMargin - 2 * o.shadowWidth) / columns) - o.itemMargin;
      $items.width(itemWidth + 1);

      pageWidth = $root.find(o.pageWrapperSelector).eq(0).width();

      rootWidth = pageNumber * pageWidth + 1000;
      $root.css({
        width: rootWidth
      });

      setNavigationVisibility();

    }

    /**
     * Binds a change slide event handler to the root, so that it can be animated
     * when any of the navigation buttons has been clicked.
     */

    function bindEventHandlers() {
      if (pageNumber > 1) {

        //mobile device finger slide events
        $root.touchwipe({
          wipeLeft: doOnNextSlide,
          wipeRight: doOnPreviousSlide,
          preventDefaultEvents: false
        });

        $(window).on('resize.' + o.namespace, doOnWindowResize);
      }

      $root.on('destroy' + o.namespace, doOnDestroy);
    }

    /**
     * Changes the current slide of items, to another one.
     * @param  {int} index the index of the new slide to show
     */
    function changeSlide(index) {
      if (!inAnimation) {
        inAnimation = true;
        var margin = getPageMarginPosition(index);
        $root.animate({
          marginLeft: [margin, 'easeOutExpo']
        }, 800, function () {
          inAnimation = false;
          currentPage = index;
        });
      }
    }

    /**
     * Calculates the position offset (margin) of the current slide 
     * according to the current wrapper width.
     * @param  {int} index the inex of the current slide
     * @return {int}       the calculated margin
     */
    function getPageMarginPosition(index) {
      setSizes();
      return -index * pageWidth - o.itemMargin / 2 + o.shadowWidth;
    }

    function setSizes() {
      setImageSize();
      pageWidth = $root.find(o.pageWrapperSelector).eq(0).width();
    }

    /**
     * On window resize event handler - resizes the wrapper and then the
     * inner images according to the current window size.
     */
    function doOnWindowResize() {
      setSizes();
      $root.css({
        marginLeft: getPageMarginPosition(currentPage)
      });
    }

    /**
     * On next slide event handler - shows the next slide if there is one.
     */
    function doOnNextSlide() {
      if (!inAnimation) {
        if (!isLastPageVisible()) {
          var index = currentPage < pageNumber - 1 ? currentPage + 1 : 0;
          changeSlide(index);
        } else {
          animateLastPage(true);
        }
      }

    }

    /**
     * On previous slide event handler - shows the previous slide if there
     * is one.
     */
    function doOnPreviousSlide() {
      if (!inAnimation) {
        if (currentPage > 0) {
          changeSlide(currentPage - 1);
        } else {
          animateLastPage(false);
        }
      }

    }

    /**
     * Animates the carousel when there are no more slides left and the
     * user still tries to open the previous/next slide - animates it in a
     * way to show that there are no more slides.
     * @param  {boolean} last setting whether this is the last slide (when
     * set to true) or the first slide (when set to false)
     */
    function animateLastPage(last) {
      var i = last ? -1 : 1;
      $root.stop().animate({
        left: i * 10
      }, 100, function () {
        $(this).stop().animate({
          left: 0
        }, 300);
      });
    }

    /**
     * Checks if the last slide/page is visible on the carousel.
     * @return {boolean} true if it is visible and false if it is not
     */
    function isLastPageVisible() {
      if ((itemsNumber - currentPage * itemsPerPage) <= columns) {
        return true;
      }

      return false;
    }

    /**
     * Checks if all of the slides/pages are visible on the carousel.
     * @return {boolean} true if they are visible and false if they are not
     */
    function areAllPagesVisible() {
      return(itemsNumber <= columns && currentPage === 0);
    }

    /**
     * Builds the navigation (arrows) to change the slides.
     */
    function buildNavigation() {

      //next items arrow
      $prevArrow = $('<div />', {
        'class': 'pc-next hover'
      }).on('click.' + o.namespace, doOnNextSlide).appendTo($wrapper);

      //previous items arrow
      $nextArrow = $('<div />', {
        'class': 'pc-prev hover'
      }).on('click.' + o.namespace, doOnPreviousSlide).appendTo($wrapper);
    }

    /**
     * Shows the navigation arrows when there are some slides that are not
     * visible and hides them when all of the slides are visible.
     */
    function setNavigationVisibility() {
      if (areAllPagesVisible()) {
        if ($prevArrow) {
          $prevArrow.hide();
        }
        if ($nextArrow) {
          $nextArrow.hide();
        }
      } else {
        if ($prevArrow) {
          $prevArrow.show();
        }
        if ($nextArrow) {
          $nextArrow.show();
        }
      }

    }

    /**
     * On destroy event handler- removes all the registered event listeners.
     */
    function doOnDestroy() {
      $(window).off('.' + o.namespace);
      $root.off('.' + o.namespace);
      $prevArrow.off('.' + o.namespace);
      $nextArrow.off('.' + o.namespace);
    }


    if ($root.length) {
      init();
    }

  };
}(jQuery));