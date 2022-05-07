(function (Drupal) {
  // Override core/dialog.ajax prepareDialogButtons behaviors
  Drupal.behaviors.dialog.prepareDialogButtons = function prepareDialogButtons($dialog) {
    // Do nothing = do not put buttons into dialog footer
    return [];
  }
})(Drupal);
