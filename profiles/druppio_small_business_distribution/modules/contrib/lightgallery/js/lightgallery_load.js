/**
 * @file
 * JS to init light gallery.
 */

(function ($, Drupal) {
  Drupal.behaviors.lightgallery = {
    galleries: [],
    attach: function (context, settings) {
      var that = this;
      // Init all galleries.
      for (id in settings.lightgallery.instances) {
        // Store galleries so that developers can change options.
        that.galleries[id] = settings.lightgallery.instances[id];
        _lightgallery_init(id, that.galleries[id], context);
      }
    }
  };

  function _lightgallery_init(id, optionset, context) {
    $('#lightgallery-' + id, context).lightGallery(optionset);
  }
})(jQuery, Drupal);