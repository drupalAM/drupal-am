/**
 * @file
 * Drupal Font Awesome plugin.
 *
 * @ignore
 */

(function ($, Drupal, drupalSettings, CKEDITOR) {

  'use strict';

  function parseAttributes(editor, element) {
    var parsedAttributes = {};

    var domElement = element.$;
    var attribute;
    var attributeName;
    for (var attrIndex = 0; attrIndex < domElement.attributes.length; attrIndex++) {
      attribute = domElement.attributes.item(attrIndex);
      attributeName = attribute.nodeName.toLowerCase();
      // Ignore data-cke-* attributes; they're CKEditor internals.
      if (attributeName.indexOf('data-cke-') === 0) {
        continue;
      }
      // Store the value for this attribute, unless there's a data-cke-saved-
      // alternative for it, which will contain the quirk-free, original value.
      parsedAttributes[attributeName] = element.data('cke-saved-' + attributeName) || attribute.nodeValue;
    }

    // Remove any cke_* classes.
    if (parsedAttributes.class) {
      parsedAttributes.class = CKEDITOR.tools.trim(parsedAttributes.class.replace(/cke_\S+/, ''));
    }

    return parsedAttributes;
  }

  function getAttributes(editor, data) {
    var set = {};
    for (var attributeName in data) {
      if (data.hasOwnProperty(attributeName)) {
        set[attributeName] = data[attributeName];
      }
    }

    // CKEditor tracks the *actual* saved href in a data-cke-saved-* attribute
    // to work around browser quirks. We need to update it.
    set['data-cke-saved-href'] = set.href;

    // Remove all attributes which are not currently set.
    var removed = {};
    for (var s in set) {
      if (set.hasOwnProperty(s)) {
        delete removed[s];
      }
    }

    return {
      set: set,
      removed: CKEDITOR.tools.objectKeys(removed)
    };
  }

  CKEDITOR.plugins.add('drupalfontawesome', {
    icons: 'drupalfontawesome',
    hidpi: true,

    init: function init(editor) {
      // Add the command for inserting Font Awesome icons.
      editor.addCommand('drupalfontawesome', {
        allowedContent: {
          i: {
            attributes: {
              '!class': true
            },
            classes: {}
          }
        },
        requiredContent: new CKEDITOR.style({
          element: 'i',
          attributes: {
            class: ''
          }
        }),
        modes: { wysiwyg: 1 },
        canUndo: true,
        exec: function exec(editor) {
          // Set existing values based on selected element.
          var existingValues = {};

          // Prepare a save callback to be used upon saving the dialog.
          var saveCallback = function saveCallback(returnValues) {

            editor.fire('saveSnapshot');

            // Create a new icon element if needed.
            var selection = editor.getSelection();
            var range = selection.getRanges(1)[0];

            // Create the icon text element.
            var icon = new CKEDITOR.dom.text('', editor.document);
            range.insertNode(icon);
            range.selectNodeContents(icon);
            // Apply the new style to the icon text.
            var style = new CKEDITOR.style({ element: 'i', attributes: returnValues.attributes });
            style.type = CKEDITOR.STYLE_INLINE;
            style.applyToRange(range);
            range.select();

            // Save snapshot for undo support.
            editor.fire('saveSnapshot');
          };

          // Drupal.t() will not work inside CKEditor plugins because CKEditor
          // loads the JavaScript file instead of Drupal. Pull translated
          // strings from the plugin settings that are translated server-side.
          var dialogSettings = {
            title: editor.config.drupalFontAwesome_dialogTitleAdd,
            dialogClass: 'fontawesome-icon-dialog'
          };

          // Open the dialog for the edit form.
          Drupal.ckeditor.openDialog(editor, Drupal.url('fontawesome/dialog/icon/' + editor.config.drupal.format), existingValues, saveCallback, dialogSettings);
        }
      });

      // Add button for icons.
      if (editor.ui.addButton) {
        editor.ui.addButton('DrupalFontAwesome', {
          label: Drupal.t('Font Awesome'),
          command: 'drupalfontawesome'
        });
      }
    }
  });

  $.each(drupalSettings.editor.fontawesome.allowedEmptyTags, function (_, v) {
      CKEDITOR.dtd.$removeEmpty[v] = 0;
  });

})(jQuery, Drupal, drupalSettings, CKEDITOR);
