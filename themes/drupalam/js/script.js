(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.headerScroll = {
    attach: function (context, settings) {
      // Adds/removes a class to the sticky header on scroll
      $(window).scroll(function () {
        if ($(window).scrollTop() >= 5) {
          $('#navbar').addClass('navbar-scrolled-top');
        } else {
          $('#navbar').removeClass('navbar-scrolled-top');
        }
      });

    }
  };
  // Frontpage hero slider
  $(".view-hero-slide .view-content").pexetoContentSlider({
    "autoplay": true, 
    "pauseOnHover": true, 
    "animationInterval": 3000, 
    "thumbnailPreview": true, 
    "buttons": true, 
    "arrows": true
  });
  // Frontpage case study slider
 var items =  $('.block-views-blockcase-study-block-1 .views-element-container .item');
  if (items.length > 2) {
    for (var i = 0; i < items.length; i += 2) {
      items.slice(i, i + 2).wrapAll("<div class='page-wrapper'></div>");
    }
  }
  $('.carousel-block .views-element-container').pexetoCarousel()
  // Frontpage case study colorbox
  $('.block-views-blockcase-study-block-1 .node').on('click', ':not(.contextual)', function(){
    $(this).find('.colorbox').colorbox({open: true});
  });

})(jQuery, Drupal, drupalSettings);