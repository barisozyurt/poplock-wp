<?php
/**
 * Frontend overlay rendering for Popup Redirect Countdown.
 *
 * @package PopupRedirectCountdown
 * @author  Baris Ozyurt <mirket@mirket.io>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PRC_Frontend {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_footer', array( $this, 'render_overlay' ) );
    }

    /**
     * Check whether the popup should display on the current page.
     */
    private function should_display() {
        $image_url    = get_option( 'prc_image_url', '' );
        $redirect_url = get_option( 'prc_redirect_url', '' );

        if ( empty( $image_url ) || empty( $redirect_url ) ) {
            return false;
        }

        $display_on = get_option( 'prc_display_on', 'homepage' );

        if ( 'homepage' === $display_on && ! is_front_page() ) {
            return false;
        }

        return true;
    }

    /**
     * Enqueue frontend CSS and JS.
     */
    public function enqueue_assets() {
        if ( ! $this->should_display() ) {
            return;
        }

        wp_enqueue_style(
            'prc-frontend',
            PRC_PLUGIN_URL . 'assets/css/prc-frontend.css',
            array(),
            PRC_VERSION
        );

        wp_enqueue_script(
            'prc-frontend',
            PRC_PLUGIN_URL . 'assets/js/prc-frontend.js',
            array(),
            PRC_VERSION,
            true
        );

        wp_localize_script( 'prc-frontend', 'prcSettings', array(
            'countdownSeconds' => absint( get_option( 'prc_countdown_seconds', 10 ) ),
            'redirectUrl'      => esc_url( get_option( 'prc_redirect_url', '' ) ),
            'cookieDays'       => absint( get_option( 'prc_cookie_days', 7 ) ),
            'overlayOpacity'   => floatval( get_option( 'prc_overlay_opacity', 0.7 ) ),
        ) );
    }

    /**
     * Render the popup overlay HTML in the footer.
     */
    public function render_overlay() {
        if ( ! $this->should_display() ) {
            return;
        }

        $image_url = esc_url( get_option( 'prc_image_url', '' ) );
        $image_alt = esc_attr( get_option( 'prc_image_alt', '' ) );
        ?>
        <div id="prc-overlay" class="prc-overlay" aria-modal="true" role="dialog" aria-label="<?php esc_attr_e( 'Popup', 'popup-redirect-countdown' ); ?>">
            <div class="prc-overlay__backdrop"></div>
            <div class="prc-overlay__content">
                <button class="prc-overlay__close" aria-label="<?php esc_attr_e( 'Close popup', 'popup-redirect-countdown' ); ?>">&times;</button>
                <img class="prc-overlay__image" src="<?php echo $image_url; ?>" alt="<?php echo $image_alt; ?>" />
                <p class="prc-overlay__countdown" id="prc-countdown-text"></p>
            </div>
            <div class="prc-overlay__progress-bar">
                <div class="prc-overlay__progress-fill" id="prc-progress-fill"></div>
            </div>
        </div>
        <?php
    }
}
