<?php
/**
 * Admin settings page for Mirket Popup Redirect Countdown.
 *
 * @package MirketPopupRedirectCountdown
 * @author  Baris Ozyurt <mirket@mirket.io>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MIRKETPRC_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_init', array( $this, 'handle_reset' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * Enqueue the WordPress media uploader, admin script and styles on the settings page.
     */
    public function enqueue_assets( $hook ) {
        if ( 'toplevel_page_mirketprc-settings' !== $hook ) {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_style(
            'mirketprc-admin',
            MIRKETPRC_PLUGIN_URL . 'assets/css/mirketprc-admin.css',
            array(),
            MIRKETPRC_VERSION
        );

        wp_enqueue_script(
            'mirketprc-admin',
            MIRKETPRC_PLUGIN_URL . 'assets/js/mirketprc-admin.js',
            array( 'jquery' ),
            MIRKETPRC_VERSION,
            true
        );

        wp_localize_script( 'mirketprc-admin', 'mirketprcAdmin', array(
            'mediaTitle'   => __( 'Select Popup Image', 'mirket-popup-redirect-countdown' ),
            'mediaButton'  => __( 'Use this image', 'mirket-popup-redirect-countdown' ),
            'resetConfirm' => __( 'Are you sure? This will clear all popup settings.', 'mirket-popup-redirect-countdown' ),
        ) );
    }

    /**
     * Add a top-level menu item.
     */
    public function add_menu_page() {
        add_menu_page(
            __( 'Mirket Popup Redirect Countdown', 'mirket-popup-redirect-countdown' ),
            __( 'Popup Redirect', 'mirket-popup-redirect-countdown' ),
            'manage_options',
            'mirketprc-settings',
            array( $this, 'render_settings_page' ),
            'dashicons-megaphone',
            80
        );
    }

    /**
     * Register all plugin settings.
     */
    public function register_settings() {
        // Section
        add_settings_section(
            'mirketprc_main_section',
            __( 'Popup Settings', 'mirket-popup-redirect-countdown' ),
            null,
            'mirketprc-settings'
        );

        // Image URL
        register_setting( 'mirketprc_settings_group', 'mirketprc_image_url', array(
            'sanitize_callback' => 'esc_url_raw',
        ) );
        add_settings_field( 'mirketprc_image_url', __( 'Image URL', 'mirket-popup-redirect-countdown' ), array( $this, 'render_image_url_field' ), 'mirketprc-settings', 'mirketprc_main_section' );

        // Image Alt
        register_setting( 'mirketprc_settings_group', 'mirketprc_image_alt', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        add_settings_field( 'mirketprc_image_alt', __( 'Image Alt Text', 'mirket-popup-redirect-countdown' ), array( $this, 'render_text_field' ), 'mirketprc-settings', 'mirketprc_main_section', array( 'option' => 'mirketprc_image_alt' ) );

        // Redirect Type
        register_setting( 'mirketprc_settings_group', 'mirketprc_redirect_type', array(
            'sanitize_callback' => array( $this, 'sanitize_redirect_type' ),
        ) );

        // Redirect Page (internal)
        register_setting( 'mirketprc_settings_group', 'mirketprc_redirect_page', array(
            'sanitize_callback' => 'absint',
        ) );

        // Redirect URL (external)
        register_setting( 'mirketprc_settings_group', 'mirketprc_redirect_url', array(
            'sanitize_callback' => 'esc_url_raw',
        ) );

        add_settings_field( 'mirketprc_redirect_url', __( 'Redirect URL', 'mirket-popup-redirect-countdown' ), array( $this, 'render_redirect_field' ), 'mirketprc-settings', 'mirketprc_main_section' );

        // Countdown Seconds
        register_setting( 'mirketprc_settings_group', 'mirketprc_countdown_seconds', array(
            'sanitize_callback' => array( $this, 'sanitize_positive_int' ),
        ) );
        add_settings_field( 'mirketprc_countdown_seconds', __( 'Countdown Seconds', 'mirket-popup-redirect-countdown' ), array( $this, 'render_number_field' ), 'mirketprc-settings', 'mirketprc_main_section', array( 'option' => 'mirketprc_countdown_seconds', 'min' => 1 ) );

        // Display On
        register_setting( 'mirketprc_settings_group', 'mirketprc_display_on', array(
            'sanitize_callback' => array( $this, 'sanitize_display_on' ),
        ) );

        // Display Pages (specific page IDs)
        register_setting( 'mirketprc_settings_group', 'mirketprc_display_pages', array(
            'sanitize_callback' => array( $this, 'sanitize_display_pages' ),
        ) );

        add_settings_field( 'mirketprc_display_on', __( 'Display On', 'mirket-popup-redirect-countdown' ), array( $this, 'render_display_on_field' ), 'mirketprc-settings', 'mirketprc_main_section' );

        // Cookie Duration
        register_setting( 'mirketprc_settings_group', 'mirketprc_cookie_days', array(
            'sanitize_callback' => 'absint',
        ) );
        add_settings_field( 'mirketprc_cookie_days', __( 'Cookie Duration (days)', 'mirket-popup-redirect-countdown' ), array( $this, 'render_number_field' ), 'mirketprc-settings', 'mirketprc_main_section', array( 'option' => 'mirketprc_cookie_days', 'min' => 0, 'description' => __( '0 = always show (no cookie set)', 'mirket-popup-redirect-countdown' ) ) );

        // Overlay Opacity
        register_setting( 'mirketprc_settings_group', 'mirketprc_overlay_opacity', array(
            'sanitize_callback' => array( $this, 'sanitize_opacity' ),
        ) );
        add_settings_field( 'mirketprc_overlay_opacity', __( 'Overlay Opacity', 'mirket-popup-redirect-countdown' ), array( $this, 'render_opacity_field' ), 'mirketprc-settings', 'mirketprc_main_section' );
    }

    /**
     * Sanitize positive integer (min 1).
     */
    public function sanitize_positive_int( $value ) {
        $value = absint( $value );
        return max( 1, $value );
    }

    /**
     * Sanitize redirect type.
     */
    public function sanitize_redirect_type( $value ) {
        return in_array( $value, array( 'page', 'external' ), true ) ? $value : 'external';
    }

    /**
     * Sanitize display_on option.
     */
    public function sanitize_display_on( $value ) {
        return in_array( $value, array( 'homepage', 'all', 'specific' ), true ) ? $value : 'homepage';
    }

    /**
     * Sanitize display pages (array of page IDs).
     */
    public function sanitize_display_pages( $value ) {
        if ( ! is_array( $value ) ) {
            return array();
        }
        return array_map( 'absint', $value );
    }

    /**
     * Sanitize opacity (0.0 to 1.0).
     */
    public function sanitize_opacity( $value ) {
        $value = floatval( $value );
        return max( 0.0, min( 1.0, $value ) );
    }

    /**
     * Render the image URL field with media picker button.
     */
    public function render_image_url_field() {
        $value = esc_url( get_option( 'mirketprc_image_url', '' ) );
        ?>
        <input type="url" id="mirketprc_image_url" name="mirketprc_image_url" value="<?php echo esc_attr( $value ); ?>" class="regular-text" />
        <button type="button" class="button" id="mirketprc-upload-btn"><?php esc_html_e( 'Select Image', 'mirket-popup-redirect-countdown' ); ?></button>
        <?php if ( $value ) : ?>
            <div class="mirketprc-image-preview"><img src="<?php echo esc_url( $value ); ?>" alt="" /></div>
        <?php endif; ?>
        <?php
    }

    /**
     * Render a text / URL input field.
     */
    public function render_text_field( $args ) {
        $option = $args['option'];
        $type   = isset( $args['type'] ) ? $args['type'] : 'text';
        $value  = esc_attr( get_option( $option, '' ) );
        printf(
            '<input type="%s" name="%s" value="%s" class="regular-text" />',
            esc_attr( $type ),
            esc_attr( $option ),
            $value
        );
    }

    /**
     * Render a number input field.
     */
    public function render_number_field( $args ) {
        $option = $args['option'];
        $min    = isset( $args['min'] ) ? $args['min'] : 0;
        $value  = get_option( $option, 0 );
        printf(
            '<input type="number" name="%s" value="%s" min="%d" class="small-text" />',
            esc_attr( $option ),
            esc_attr( $value ),
            intval( $min )
        );
        if ( ! empty( $args['description'] ) ) {
            printf( '<p class="description">%s</p>', esc_html( $args['description'] ) );
        }
    }

    /**
     * Render the Redirect URL field with page selector and external URL option.
     */
    public function render_redirect_field() {
        $type     = get_option( 'mirketprc_redirect_type', 'external' );
        $page_id  = get_option( 'mirketprc_redirect_page', 0 );
        $ext_url  = get_option( 'mirketprc_redirect_url', '' );
        $pages    = get_pages( array( 'sort_column' => 'post_title', 'sort_order' => 'ASC' ) );
        ?>
        <label>
            <input type="radio" name="mirketprc_redirect_type" value="page" <?php checked( $type, 'page' ); ?> class="mirketprc-redirect-type" />
            <?php esc_html_e( 'WordPress page', 'mirket-popup-redirect-countdown' ); ?>
        </label>
        <br />
        <select name="mirketprc_redirect_page" id="mirketprc-redirect-page" class="mirketprc-indented" <?php echo 'page' !== $type ? 'disabled' : ''; ?>>
            <option value="0"><?php esc_html_e( '— Select a page —', 'mirket-popup-redirect-countdown' ); ?></option>
            <?php foreach ( $pages as $p ) : ?>
                <option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( $page_id, $p->ID ); ?>>
                    <?php echo esc_html( $p->post_title ); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br />
        <label>
            <input type="radio" name="mirketprc_redirect_type" value="external" <?php checked( $type, 'external' ); ?> class="mirketprc-redirect-type" />
            <?php esc_html_e( 'External URL', 'mirket-popup-redirect-countdown' ); ?>
        </label>
        <br />
        <input type="url" name="mirketprc_redirect_url" id="mirketprc-redirect-url" value="<?php echo esc_attr( $ext_url ); ?>" class="regular-text mirketprc-indented" <?php echo 'external' !== $type ? 'disabled' : ''; ?> />
        <?php
    }

    /**
     * Render the Display On radio buttons with specific page selection.
     */
    public function render_display_on_field() {
        $value          = get_option( 'mirketprc_display_on', 'homepage' );
        $selected_pages = get_option( 'mirketprc_display_pages', array() );
        if ( ! is_array( $selected_pages ) ) {
            $selected_pages = array();
        }
        $pages = get_pages( array( 'sort_column' => 'post_title', 'sort_order' => 'ASC' ) );
        ?>
        <label>
            <input type="radio" name="mirketprc_display_on" value="homepage" <?php checked( $value, 'homepage' ); ?> class="mirketprc-display-on" />
            <?php esc_html_e( 'Homepage only', 'mirket-popup-redirect-countdown' ); ?>
        </label>
        <br />
        <label>
            <input type="radio" name="mirketprc_display_on" value="all" <?php checked( $value, 'all' ); ?> class="mirketprc-display-on" />
            <?php esc_html_e( 'All pages', 'mirket-popup-redirect-countdown' ); ?>
        </label>
        <br />
        <label>
            <input type="radio" name="mirketprc_display_on" value="specific" <?php checked( $value, 'specific' ); ?> class="mirketprc-display-on" />
            <?php esc_html_e( 'Specific pages', 'mirket-popup-redirect-countdown' ); ?>
        </label>
        <div id="mirketprc-specific-pages" class="mirketprc-page-list" <?php echo 'specific' !== $value ? 'style="display:none;"' : ''; ?>>
            <?php foreach ( $pages as $p ) : ?>
                <label class="mirketprc-page-list__item">
                    <input type="checkbox" name="mirketprc_display_pages[]" value="<?php echo esc_attr( $p->ID ); ?>" <?php checked( in_array( $p->ID, $selected_pages, true ) ); ?> />
                    <?php echo esc_html( $p->post_title ); ?>
                </label>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Render the opacity range slider.
     */
    public function render_opacity_field() {
        $value = get_option( 'mirketprc_overlay_opacity', 0.7 );
        ?>
        <input type="range" name="mirketprc_overlay_opacity" min="0" max="1" step="0.05" value="<?php echo esc_attr( $value ); ?>" id="mirketprc_overlay_opacity" />
        <span id="mirketprc-opacity-value"><?php echo esc_html( $value ); ?></span>
        <?php
    }

    /**
     * Handle the "Remove Popup" reset action.
     */
    public function handle_reset() {
        if ( ! isset( $_POST['mirketprc_reset_popup'] ) ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        check_admin_referer( 'mirketprc_reset_action', 'mirketprc_reset_nonce' );

        $options = array(
            'mirketprc_image_url',
            'mirketprc_image_alt',
            'mirketprc_redirect_type',
            'mirketprc_redirect_page',
            'mirketprc_redirect_url',
            'mirketprc_countdown_seconds',
            'mirketprc_display_on',
            'mirketprc_display_pages',
            'mirketprc_cookie_days',
            'mirketprc_overlay_opacity',
        );

        foreach ( $options as $option ) {
            delete_option( $option );
        }

        add_settings_error( 'mirketprc_settings_group', 'mirketprc_reset', __( 'All popup settings have been removed.', 'mirket-popup-redirect-countdown' ), 'updated' );
    }

    /**
     * Render the settings page.
     */
    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Mirket Popup Redirect Countdown Settings', 'mirket-popup-redirect-countdown' ); ?></h1>
            <?php settings_errors( 'mirketprc_settings_group' ); ?>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'mirketprc_settings_group' );
                do_settings_sections( 'mirketprc-settings' );
                submit_button();
                ?>
            </form>
            <hr />
            <form method="post" action="" id="mirketprc-reset-form">
                <?php wp_nonce_field( 'mirketprc_reset_action', 'mirketprc_reset_nonce' ); ?>
                <p class="description"><?php esc_html_e( 'Clear all settings and disable the popup.', 'mirket-popup-redirect-countdown' ); ?></p>
                <?php submit_button( __( 'Remove Popup', 'mirket-popup-redirect-countdown' ), 'delete', 'mirketprc_reset_popup', false ); ?>
            </form>
        </div>
        <?php
    }
}
