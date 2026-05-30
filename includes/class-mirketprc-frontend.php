<?php
/**
 * Frontend overlay rendering for Mirket Popup Redirect Countdown.
 *
 * @package MirketPopupRedirectCountdown
 * @author  Baris Ozyurt <mirket@mirket.io>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MIRKETPRC_Frontend {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_footer', array( $this, 'render_overlay' ) );
    }

    /**
     * Get the resolved redirect URL based on type setting.
     */
    private function get_redirect_url() {
        $type = get_option( 'mirketprc_redirect_type', 'external' );

        if ( 'page' === $type ) {
            $page_id = absint( get_option( 'mirketprc_redirect_page', 0 ) );
            if ( $page_id > 0 ) {
                return get_permalink( $page_id );
            }
            return '';
        }

        return get_option( 'mirketprc_redirect_url', '' );
    }

    /**
     * Check whether the popup should display on the current page.
     */
    private function should_display() {
        $image_url    = get_option( 'mirketprc_image_url', '' );
        $redirect_url = $this->get_redirect_url();

        if ( empty( $image_url ) || empty( $redirect_url ) ) {
            return false;
        }

        $display_on = get_option( 'mirketprc_display_on', 'homepage' );

        if ( 'homepage' === $display_on && ! is_front_page() ) {
            return false;
        }

        if ( 'specific' === $display_on ) {
            $display_pages = get_option( 'mirketprc_display_pages', array() );
            if ( ! is_array( $display_pages ) || empty( $display_pages ) ) {
                return false;
            }
            if ( ! is_page( $display_pages ) ) {
                return false;
            }
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
            'mirketprc-frontend',
            MIRKETPRC_PLUGIN_URL . 'assets/css/mirketprc-frontend.css',
            array(),
            MIRKETPRC_VERSION
        );

        wp_enqueue_script(
            'mirketprc-frontend',
            MIRKETPRC_PLUGIN_URL . 'assets/js/mirketprc-frontend.js',
            array(),
            MIRKETPRC_VERSION,
            true
        );

        wp_localize_script( 'mirketprc-frontend', 'mirketprcSettings', array(
            'countdownSeconds' => absint( get_option( 'mirketprc_countdown_seconds', 10 ) ),
            'redirectUrl'      => esc_url( $this->get_redirect_url() ),
            'cookieDays'       => absint( get_option( 'mirketprc_cookie_days', 7 ) ),
            'overlayOpacity'   => floatval( get_option( 'mirketprc_overlay_opacity', 0.7 ) ),
            'redirectingText'  => __( 'Redirecting in', 'mirket-popup-redirect-countdown' ),
        ) );
    }

    /**
     * Render the popup overlay HTML in the footer.
     */
    public function render_overlay() {
        if ( ! $this->should_display() ) {
            return;
        }

        $image_url = get_option( 'mirketprc_image_url', '' );
        $image_alt = get_option( 'mirketprc_image_alt', '' );
        ?>
        <div id="mirketprc-overlay" class="mirketprc-overlay" aria-modal="true" role="dialog" aria-label="<?php esc_attr_e( 'Popup', 'mirket-popup-redirect-countdown' ); ?>">
            <div class="mirketprc-overlay__backdrop"></div>
            <div class="mirketprc-overlay__content">
                <button class="mirketprc-overlay__close" aria-label="<?php esc_attr_e( 'Close popup', 'mirket-popup-redirect-countdown' ); ?>">&times;</button>
                <img class="mirketprc-overlay__image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />
                <p class="mirketprc-overlay__countdown" id="mirketprc-countdown-text"></p>
            </div>
            <div class="mirketprc-overlay__progress-bar">
                <div class="mirketprc-overlay__progress-fill" id="mirketprc-progress-fill"></div>
            </div>
        </div>
        <?php
    }
}
