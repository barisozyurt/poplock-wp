/**
 * Mirket Popup Redirect Countdown — Admin JS
 *
 * @author  Baris Ozyurt <mirket@mirket.io>
 * @license GPL-3.0
 */
(function ($) {
    'use strict';

    var data = window.mirketprcAdmin || {};

    $(function () {
        /* ---- Media uploader ---- */
        var frame;
        $('#mirketprc-upload-btn').on('click', function (e) {
            e.preventDefault();
            if (frame) {
                frame.open();
                return;
            }
            frame = wp.media({
                title: data.mediaTitle,
                button: { text: data.mediaButton },
                multiple: false
            });
            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#mirketprc_image_url').val(attachment.url);
            });
            frame.open();
        });

        /* ---- Redirect type toggle ---- */
        $('.mirketprc-redirect-type').on('change', function () {
            var isPage = $(this).val() === 'page';
            $('#mirketprc-redirect-page').prop('disabled', !isPage);
            $('#mirketprc-redirect-url').prop('disabled', isPage);
        });

        /* ---- Display-on toggle ---- */
        $('.mirketprc-display-on').on('change', function () {
            $('#mirketprc-specific-pages').toggle($(this).val() === 'specific');
        });

        /* ---- Opacity live value ---- */
        $('#mirketprc_overlay_opacity').on('input', function () {
            $('#mirketprc-opacity-value').text($(this).val());
        });

        /* ---- Reset confirmation ---- */
        $('#mirketprc-reset-form').on('submit', function () {
            return window.confirm(data.resetConfirm);
        });
    });
})(jQuery);
