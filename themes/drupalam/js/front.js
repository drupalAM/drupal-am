(function ($, Drupal, drupalSettings) {

  'use strict';

  // Hero slider
  $(".view-hero-slide .view-content").pexetoContentSlider({
    "autoplay": true,
    "pauseOnHover": true,
    "animationInterval": 3000,
    "thumbnailPreview": true,
    "buttons": true,
    "arrows": true
  });
  // Case study slider
  var items = $('.block-views-blockcase-study-block-1 .views-element-container .item');
  if (items.length > 2) {
    for (var i = 0; i < items.length; i += 2) {
      items.slice(i, i + 2).wrapAll("<div class='page-wrapper'></div>");
    }
  }
  $('.carousel-block .views-element-container').pexetoCarousel()
  //Case study colorbox
  $('.block-views-blockcase-study-block-1 .node').on('click', ':not(.contextual)', function () {
    $(this).find('.colorbox').colorbox({open: true});
  });
  // Community members effect
  $('.view-community-members').each(function () {
    new PEXETO.parallax(
      $(this),
      'list',
      {
        children: $(this).find('.parallax-element'),
        animation: 'scale'
      }
    ).init();
  });

})(jQuery, Drupal, drupalSettings);