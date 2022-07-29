/**
 * @file
 * Attaches show/hide functionality to checkboxes in the "Augmenter" form.
 */

(function ($) {

  'use strict';

  Drupal.behaviors.dateAugmenter = {
    attach: function (context, settings) {
      $('.date-augmenter-status-wrapper input.form-checkbox', context).each(function () {
        var $checkbox = $(this);
        var processor_id = $checkbox.data('id');

        var $wrapper = $checkbox.closest('.date-augmenter-status-wrapper').parent();
        var $rows = $wrapper.find('.date-augmenter-weight--' + processor_id);
        var tab = $wrapper.find('.date-augmenter-settings-' + processor_id);

        // Bind a click handler to this checkbox to conditionally show and hide
        // the processor's table row and vertical tab pane.
        $checkbox.on('click.dateAugmenterUpdate', function () {
          if ($checkbox.is(':checked')) {
            $rows.show();
            if (tab) {
              tab.show();
            }
          }
          else {
            $rows.hide();
            if (tab) {
              tab.hide();
            }
          }
        });

        // Trigger our bound click handler to update elements to initial state.
        $checkbox.triggerHandler('click.dateAugmenterUpdate');
      });
    }
  };

})(jQuery);
