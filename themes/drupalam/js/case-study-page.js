(function ($, Drupal, drupalSettings) {

  'use strict';

  // Simple Image Slider
  var $images = [];

  $('.sis-images .field--items img').each(function (index, el) {
    var $el = $(el);
    $images.push({img : $el.attr('src')});
  });

  $(".sis-wrapper").simpleImageSlider({
    'images': $images
  });

  // Related projects carousel
  var items = $('.related-projects-block .views-element-container .item');
    for (var i = 0; i < items.length; i += 2) {
      items.slice(i, i + 2).wrapAll("<div class='page-wrapper'></div>");
    }
  $('.related-projects-block .views-element-container').pexetoCarousel();

})(jQuery, Drupal, drupalSettings);
