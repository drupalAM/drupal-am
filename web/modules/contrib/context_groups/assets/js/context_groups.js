(function ($) {
  'use strict';

  Drupal.tableDrag.prototype.onDrop = function () {

    var blockRow = $(this)[0].oldRowElement;
    var regionRow = $(blockRow).prevAll('.region-title:first');
    var droppedInRegion = $(regionRow).data('region');

    var regionSelect = $(blockRow).find('.block-region-select');
    $(regionSelect).val(droppedInRegion);

    // Check if there are some leafs under context group.
    var leafs = $(blockRow).nextUntil('.region-title');
    for (var i = 0; i < leafs.length; i++) {
      $(leafs[i]).find('.block-region-select').val(droppedInRegion);
    }

    // Check if we dragged a block and then if it is under one of the context group.
    var indentation_level = 0;
    var parentContextGroup;
    if ($(blockRow).hasClass('tabledrag-leaf')) {
      indentation_level = getIndentationLevel($(blockRow).find('td').eq(0));

      // Get parent context group.
      if (indentation_level) {
        parentContextGroup = getParentContextGroup(blockRow, indentation_level);
        $(blockRow).find('select.field-parent').eq(0).val(parentContextGroup);
      }
    }
    else {
      // We dragged context group. Check if we dragged it under other context group.
      indentation_level = getIndentationLevel($(blockRow).find('td').eq(0));

      if (indentation_level) {
        // Update parent of context group.
        parentContextGroup = getParentContextGroup($(blockRow), indentation_level);
        $(blockRow).find('select.field-parent').eq(0).val(parentContextGroup);
      }
    }

    /**
     * Get indentation level of element.
     *
     * @param {HTMLElement} element
     *   Block or context group element.
     *
     * @return {number}
     *   Level of indentation.
     */
    function getIndentationLevel(element) {
      return $(element).find('div.indentation').length;
    }

    /**
     * Get parent Context group of element.
     *
     * @param {HTMLElement} element
     *   Block element.
     * @param {number} indentation_level
     *   Indentation level of moved block.
     *
     * @return {string}
     *   Context group id.
     */
    function getParentContextGroup(element, indentation_level) {
      var contextGroup = $(element).prevAll('.context-group:first');
      var indentation = getIndentationLevel(contextGroup);
      if ((indentation_level - indentation) === 1) {
        return $(contextGroup).data('group-id');
      }
      else {
        return getParentContextGroup(contextGroup, indentation_level);
      }
    }
  };


  Drupal.behaviors.addClassToRegions = {
    attach: function (context, settings) {
      var $table = $('#context-groups-draggable', context);
      var regions = $table.find('.region-title');

      var data_for_regions = settings.context_groups_regions;

      // Add data-region attribute to region on context edit page.
      $.each(regions, function (key, value) {
        $(value).attr('data-region', data_for_regions[key]);
      });
    }
  };

})(jQuery);
