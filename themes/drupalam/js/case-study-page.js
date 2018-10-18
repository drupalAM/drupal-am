(function ($, Drupal, drupalSettings) {

  'use strict';

  var $images = [];

  $('.sis-images .field--items img').each(function (index, el) {
    var $el = $(el);
    $images.push({img : $el.attr('src')});
  });

  $(".sis-wrapper").simpleImageSlider({
    'images': $images
  });

})(jQuery, Drupal, drupalSettings);
