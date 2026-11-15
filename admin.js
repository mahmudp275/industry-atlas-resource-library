(function ($) {
    'use strict';
    $(function () {
        var $input = $('#iagrl_media_id');
        var $output = $('#iagrl_selected_file');
        var frame;
        $('#iagrl_select_media').on('click', function (event) {
            event.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({ title: 'Select a Downloadable Resource', button: { text: 'Use This File' }, multiple: false });
            frame.on('select', function () {
                var selected = frame.state().get('selection').first().toJSON();
                $input.val(selected.id);
                $output.text(selected.filename || selected.title || 'File selected');
            });
            frame.open();
        });
        $('#iagrl_clear_media').on('click', function (event) {
            event.preventDefault();
            $input.val('');
            $output.text('No Media Library file selected.');
        });
    });
})(jQuery);
