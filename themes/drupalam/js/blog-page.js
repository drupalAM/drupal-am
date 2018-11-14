(function ($, Drupal, drupalSettings) {

  'use strict';

  var $filter = $('.path-blog .views-exposed-form .form-item');
  var $activeCategory =  $('.active-category');
  var $filterLinksWrapper = $('.bef-links');

  // Display filter items if media query is lower than 993px
  const mediaQuery = window.matchMedia('(max-width: 993px)');
  mediaQuery.addListener((MediaQuery));
  MediaQuery(mediaQuery);

  function MediaQuery(mediaQuery) {
    if (mediaQuery.matches) {
      $filter.addClass('filter-visible hide-filter');
    } else {
      $filter.removeClass('filter-visible hide-filter');
    }
  }

  // Display current active category
  $activeCategory.text($('.bef-link-active a').text());

  // Show/hide filter items
  $('.bef-links-collapsed').on('click', function () {
    $filter.hasClass('hide-filter')
      ? $filter.removeClass('hide-filter')
      : $filter.addClass('hide-filter');
  })

})(jQuery, Drupal, drupalSettings);
