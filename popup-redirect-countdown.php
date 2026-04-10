<?php
/**
 * Plugin Name: Popup Redirect Countdown
 * Plugin URI:  https://mirket.io
 * Description: Shows an animated image overlay on configurable pages. If the user doesn't close it, a countdown redirects them to a target URL.
 * Version:     1.0
 * Author:      Baris Ozyurt
 * Author URI:  https://mirket.io
 * License:     GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: popup-redirect-countdown
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'PRC_VERSION', '1.0' );
define( 'PRC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PRC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once PRC_PLUGIN_DIR . 'includes/class-prc-admin.php';
require_once PRC_PLUGIN_DIR . 'includes/class-prc-frontend.php';

/**
 * Initialize the plugin.
 */
function prc_init() {
    if ( is_admin() ) {
        new PRC_Admin();
    } else {
        new PRC_Frontend();
    }
}
add_action( 'plugins_loaded', 'prc_init' );

/**
 * Register default options on activation.
 */
function prc_activate() {
    $defaults = array(
        'prc_image_url'          => '',
        'prc_image_alt'          => '',
        'prc_redirect_url'       => '',
        'prc_countdown_seconds'  => 10,
        'prc_display_on'         => 'homepage',
        'prc_cookie_days'        => 7,
        'prc_overlay_opacity'    => 0.7,
    );

    foreach ( $defaults as $key => $value ) {
        if ( false === get_option( $key ) ) {
            add_option( $key, $value );
        }
    }
}
register_activation_hook( __FILE__, 'prc_activate' );
