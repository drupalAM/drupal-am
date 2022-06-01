(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.boostrapTheme = {
    attach: function (context, settings) {
      $('.node--type-case-study .field--name-field-screens-media').slick();
      $('.node--type-case-study .field--name-field-screens-media .slick-prev').text('');
      $('.node--type-case-study .field--name-field-screens-media .slick-next').text('');
    }
  };

  $(document).ready(function() {
    // Related projects carousel
    var items = $('.related-projects-block .views-element-container .item');
    for (var i = 0; i < items.length; i += 2) {
      items.slice(i, i + 2).wrapAll("<div class='page-wrapper'></div>");
    }
    $('.related-projects-block .views-element-container').pexetoCarousel();

    // Set the same heigth to carousel items by 'MatchHeight'
    $('.related-projects-block .views-element-container .item .node').matchHeight();
  });

})(jQuery, Drupal, drupalSettings);
