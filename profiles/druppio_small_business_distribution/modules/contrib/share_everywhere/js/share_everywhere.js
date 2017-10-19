(function ($) {
  'use strict';
  $('.se-trigger img').click(function (e) {
    var links = $(this).parent().parent().find('.se-links');
    $(links).toggleClass('se-active');
    $(links).toggleClass('se-inactive');
    e.stopPropagation();
  });
  $(':not(.se-trigger img, .se-trigger)').click(function () {
    $('.se-links.se-active').addClass('se-inactive');
    $('.se-links.se-active').removeClass('se-active');
  });
})(jQuery);
