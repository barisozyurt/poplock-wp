/**
 * Popup Redirect Countdown — Frontend JS
 *
 * @author  Baris Ozyurt <mirket@mirket.io>
 * @license GPL-3.0
 */
(function () {
    'use strict';

    /* ---- Cookie helpers ---- */

    function setCookie(name, value, days) {
        if (days <= 0) return;
        var d = new Date();
        d.setTime(d.getTime() + days * 86400000);
        document.cookie = name + '=' + encodeURIComponent(value) +
            ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
    }

    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : null;
    }

    /* ---- Settings ---- */

    var settings = window.prcSettings || {};
    var countdownTotal = parseInt(settings.countdownSeconds, 10) || 10;
    var redirectUrl    = settings.redirectUrl || '';
    var cookieDays     = parseInt(settings.cookieDays, 10);
    var overlayOpacity = parseFloat(settings.overlayOpacity) || 0.7;

    /* ---- Guard: already dismissed ---- */

    if (cookieDays > 0 && getCookie('prc_dismissed') === '1') {
        return;
    }

    /* ---- DOM ready ---- */

    function onReady(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    onReady(function () {
        var overlay       = document.getElementById('prc-overlay');
        var backdrop      = overlay ? overlay.querySelector('.prc-overlay__backdrop') : null;
        var closeBtn      = overlay ? overlay.querySelector('.prc-overlay__close') : null;
        var countdownText = document.getElementById('prc-countdown-text');
        var progressFill  = document.getElementById('prc-progress-fill');

        if (!overlay || !redirectUrl) return;

        /* Apply opacity from settings */
        if (backdrop) {
            backdrop.style.opacity = overlayOpacity;
        }

        var remaining = countdownTotal;
        var timer     = null;

        /* ---- Show overlay ---- */

        function show() {
            overlay.classList.add('prc-overlay--visible');
            updateCountdownText();
            startProgressBar();
            timer = setInterval(tick, 1000);
        }

        /* ---- Countdown ---- */

        function updateCountdownText() {
            if (countdownText) {
                countdownText.textContent = 'Redirecting in ' + remaining + ' second' + (remaining !== 1 ? 's' : '') + '…';
            }
        }

        function startProgressBar() {
            if (!progressFill) return;
            /* Force reflow so the transition starts from full width */
            progressFill.style.transition = 'none';
            progressFill.style.width = '100%';
            progressFill.offsetWidth; // trigger reflow
            progressFill.style.transition = 'width ' + countdownTotal + 's linear';
            progressFill.style.width = '0%';
        }

        function tick() {
            remaining--;
            if (remaining <= 0) {
                clearInterval(timer);
                window.location.href = redirectUrl;
                return;
            }
            updateCountdownText();
        }

        /* ---- Close ---- */

        function dismiss() {
            clearInterval(timer);
            overlay.classList.remove('prc-overlay--visible');

            /* Stop progress bar */
            if (progressFill) {
                var current = progressFill.getBoundingClientRect().width;
                var parent  = progressFill.parentElement.getBoundingClientRect().width;
                var pct     = parent > 0 ? (current / parent) * 100 : 0;
                progressFill.style.transition = 'none';
                progressFill.style.width = pct + '%';
            }

            /* Set cookie */
            if (cookieDays > 0) {
                setCookie('prc_dismissed', '1', cookieDays);
            }
        }

        /* Close on X button */
        if (closeBtn) {
            closeBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                dismiss();
            });
        }

        /* Close on backdrop click (outside image) */
        overlay.addEventListener('click', function (e) {
            /* Only dismiss if click is on the overlay or backdrop, not on content children */
            if (e.target === overlay || e.target === backdrop) {
                dismiss();
            }
        });

        /* Prevent clicks on the content area from bubbling to overlay */
        var content = overlay.querySelector('.prc-overlay__content');
        if (content) {
            content.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

        /* Close on Escape key */
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay.classList.contains('prc-overlay--visible')) {
                dismiss();
            }
        });

        /* ---- Delay then show ---- */

        setTimeout(show, 300);
    });
})();
