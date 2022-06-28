(function ($, Drupal, drupalSettings) {

  'use strict';

  CollapsibleFilter();

  function CollapsibleFilter() {
    var $filter = $('.path-blog .views-exposed-form .form-item');
    var $activeCategory =  $('.active-category');

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
  }

  Drupal.behaviors.blogPostsListing = {
    attach: function (context, settings) {
      once('blogPostsListingBehavior', '.view-blog.view-display-id-blog_all .view-content', context).forEach(function (element) {
        var msnry = new Masonry( element, {
          itemSelector: '.views-row',
          columnWidth: '.col-sm-6',
          percentPosition: true
        });
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
