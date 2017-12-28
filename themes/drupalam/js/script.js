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

})(jQuery, Drupal, drupalSettings);