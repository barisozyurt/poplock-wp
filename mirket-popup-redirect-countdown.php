<?php
/**
 * Plugin Name: Mirket Popup Redirect Countdown
 * Plugin URI:  https://github.com/barisozyurt/poplock-wp
 * Description: Shows an animated image overlay on configurable pages. If the user doesn't close it, a countdown redirects them to a target URL.
 * Version:     1.3
 * Author:      Baris Ozyurt
 * Author URI:  https://mirket.io
 * License:     GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: mirket-popup-redirect-countdown
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'MIRKETPRC_VERSION', '1.3' );
define( 'MIRKETPRC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MIRKETPRC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once MIRKETPRC_PLUGIN_DIR . 'includes/class-mirketprc-admin.php';
require_once MIRKETPRC_PLUGIN_DIR . 'includes/class-mirketprc-frontend.php';

/**
 * Initialize the plugin.
 */
function mirketprc_init() {
    if ( is_admin() ) {
        new MIRKETPRC_Admin();
    } else {
        new MIRKETPRC_Frontend();
    }
}
add_action( 'plugins_loaded', 'mirketprc_init' );

/**
 * Register default options on activation.
 */
function mirketprc_activate() {
    $defaults = array(
        'mirketprc_image_url'          => '',
        'mirketprc_image_alt'          => '',
        'mirketprc_redirect_type'      => 'external',
        'mirketprc_redirect_page'      => 0,
        'mirketprc_redirect_url'       => '',
        'mirketprc_countdown_seconds'  => 10,
        'mirketprc_display_on'         => 'homepage',
        'mirketprc_display_pages'      => array(),
        'mirketprc_cookie_days'        => 7,
        'mirketprc_overlay_opacity'    => 0.7,
    );

    foreach ( $defaults as $key => $value ) {
        if ( false === get_option( $key ) ) {
            add_option( $key, $value );
        }
    }
}
register_activation_hook( __FILE__, 'mirketprc_activate' );
