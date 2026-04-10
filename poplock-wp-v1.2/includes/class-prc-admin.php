<?php
/**
 * Admin settings page for Popup Redirect Countdown.
 *
 * @package PopupRedirectCountdown
 * @author  Baris Ozyurt <mirket@mirket.io>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PRC_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_init', array( $this, 'handle_reset' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_media' ) );
    }

    /**
     * Enqueue the WordPress media uploader on the plugin settings page.
     */
    public function enqueue_media( $hook ) {
        if ( 'toplevel_page_prc-settings' !== $hook ) {
            return;
        }
        wp_enqueue_media();
    }

    /**
     * Add a top-level menu item.
     */
    public function add_menu_page() {
        add_menu_page(
            __( 'Popup Redirect Countdown', 'popup-redirect-countdown' ),
            __( 'Popup Redirect', 'popup-redirect-countdown' ),
            'manage_options',
            'prc-settings',
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
            'prc_main_section',
            __( 'Popup Settings', 'popup-redirect-countdown' ),
            null,
            'prc-settings'
        );

        // Image URL
        register_setting( 'prc_settings_group', 'prc_image_url', array(
            'sanitize_callback' => 'esc_url_raw',
        ) );
        add_settings_field( 'prc_image_url', __( 'Image URL', 'popup-redirect-countdown' ), array( $this, 'render_image_url_field' ), 'prc-settings', 'prc_main_section' );

        // Image Alt
        register_setting( 'prc_settings_group', 'prc_image_alt', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        add_settings_field( 'prc_image_alt', __( 'Image Alt Text', 'popup-redirect-countdown' ), array( $this, 'render_text_field' ), 'prc-settings', 'prc_main_section', array( 'option' => 'prc_image_alt' ) );

        // Redirect Type
        register_setting( 'prc_settings_group', 'prc_redirect_type', array(
            'sanitize_callback' => array( $this, 'sanitize_redirect_type' ),
        ) );

        // Redirect Page (internal)
        register_setting( 'prc_settings_group', 'prc_redirect_page', array(
            'sanitize_callback' => 'absint',
        ) );

        // Redirect URL (external)
        register_setting( 'prc_settings_group', 'prc_redirect_url', array(
            'sanitize_callback' => 'esc_url_raw',
        ) );

        add_settings_field( 'prc_redirect_url', __( 'Redirect URL', 'popup-redirect-countdown' ), array( $this, 'render_redirect_field' ), 'prc-settings', 'prc_main_section' );

        // Countdown Seconds
        register_setting( 'prc_settings_group', 'prc_countdown_seconds', array(
            'sanitize_callback' => array( $this, 'sanitize_positive_int' ),
        ) );
        add_settings_field( 'prc_countdown_seconds', __( 'Countdown Seconds', 'popup-redirect-countdown' ), array( $this, 'render_number_field' ), 'prc-settings', 'prc_main_section', array( 'option' => 'prc_countdown_seconds', 'min' => 1 ) );

        // Display On
        register_setting( 'prc_settings_group', 'prc_display_on', array(
            'sanitize_callback' => array( $this, 'sanitize_display_on' ),
        ) );

        // Display Pages (specific page IDs)
        register_setting( 'prc_settings_group', 'prc_display_pages', array(
            'sanitize_callback' => array( $this, 'sanitize_display_pages' ),
        ) );

        add_settings_field( 'prc_display_on', __( 'Display On', 'popup-redirect-countdown' ), array( $this, 'render_display_on_field' ), 'prc-settings', 'prc_main_section' );

        // Cookie Duration
        register_setting( 'prc_settings_group', 'prc_cookie_days', array(
            'sanitize_callback' => 'absint',
        ) );
        add_settings_field( 'prc_cookie_days', __( 'Cookie Duration (days)', 'popup-redirect-countdown' ), array( $this, 'render_number_field' ), 'prc-settings', 'prc_main_section', array( 'option' => 'prc_cookie_days', 'min' => 0, 'description' => __( '0 = always show (no cookie set)', 'popup-redirect-countdown' ) ) );

        // Overlay Opacity
        register_setting( 'prc_settings_group', 'prc_overlay_opacity', array(
            'sanitize_callback' => array( $this, 'sanitize_opacity' ),
        ) );
        add_settings_field( 'prc_overlay_opacity', __( 'Overlay Opacity', 'popup-redirect-countdown' ), array( $this, 'render_opacity_field' ), 'prc-settings', 'prc_main_section' );
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
        $value = esc_url( get_option( 'prc_image_url', '' ) );
        ?>
        <input type="url" id="prc_image_url" name="prc_image_url" value="<?php echo esc_attr( $value ); ?>" class="regular-text" />
        <button type="button" class="button" id="prc-upload-btn"><?php esc_html_e( 'Select Image', 'popup-redirect-countdown' ); ?></button>
        <?php if ( $value ) : ?>
            <div style="margin-top:10px;"><img src="<?php echo esc_url( $value ); ?>" style="max-width:300px;height:auto;" /></div>
        <?php endif; ?>
        <script>
        jQuery(document).ready(function($){
            var frame;
            $('#prc-upload-btn').on('click', function(e){
                e.preventDefault();
                if (frame) { frame.open(); return; }
                frame = wp.media({
                    title: '<?php echo esc_js( __( 'Select Popup Image', 'popup-redirect-countdown' ) ); ?>',
                    button: { text: '<?php echo esc_js( __( 'Use this image', 'popup-redirect-countdown' ) ); ?>' },
                    multiple: false
                });
                frame.on('select', function(){
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#prc_image_url').val(attachment.url);
                });
                frame.open();
            });
        });
        </script>
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
        $type     = get_option( 'prc_redirect_type', 'external' );
        $page_id  = get_option( 'prc_redirect_page', 0 );
        $ext_url  = esc_attr( get_option( 'prc_redirect_url', '' ) );
        $pages    = get_pages( array( 'sort_column' => 'post_title', 'sort_order' => 'ASC' ) );
        ?>
        <label>
            <input type="radio" name="prc_redirect_type" value="page" <?php checked( $type, 'page' ); ?> class="prc-redirect-type" />
            <?php esc_html_e( 'WordPress page', 'popup-redirect-countdown' ); ?>
        </label>
        <br />
        <select name="prc_redirect_page" id="prc-redirect-page" style="margin: 4px 0 8px 24px;" <?php echo 'page' !== $type ? 'disabled' : ''; ?>>
            <option value="0"><?php esc_html_e( '— Select a page —', 'popup-redirect-countdown' ); ?></option>
            <?php foreach ( $pages as $p ) : ?>
                <option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( $page_id, $p->ID ); ?>>
                    <?php echo esc_html( $p->post_title ); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br />
        <label>
            <input type="radio" name="prc_redirect_type" value="external" <?php checked( $type, 'external' ); ?> class="prc-redirect-type" />
            <?php esc_html_e( 'External URL', 'popup-redirect-countdown' ); ?>
        </label>
        <br />
        <input type="url" name="prc_redirect_url" id="prc-redirect-url" value="<?php echo $ext_url; ?>" class="regular-text" style="margin: 4px 0 0 24px;" <?php echo 'external' !== $type ? 'disabled' : ''; ?> />
        <script>
        jQuery(document).ready(function($){
            $('.prc-redirect-type').on('change', function(){
                var isPage = $(this).val() === 'page';
                $('#prc-redirect-page').prop('disabled', !isPage);
                $('#prc-redirect-url').prop('disabled', isPage);
            });
        });
        </script>
        <?php
    }

    /**
     * Render the Display On radio buttons with specific page selection.
     */
    public function render_display_on_field() {
        $value          = get_option( 'prc_display_on', 'homepage' );
        $selected_pages = get_option( 'prc_display_pages', array() );
        if ( ! is_array( $selected_pages ) ) {
            $selected_pages = array();
        }
        $pages = get_pages( array( 'sort_column' => 'post_title', 'sort_order' => 'ASC' ) );
        ?>
        <label>
            <input type="radio" name="prc_display_on" value="homepage" <?php checked( $value, 'homepage' ); ?> class="prc-display-on" />
            <?php esc_html_e( 'Homepage only', 'popup-redirect-countdown' ); ?>
        </label>
        <br />
        <label>
            <input type="radio" name="prc_display_on" value="all" <?php checked( $value, 'all' ); ?> class="prc-display-on" />
            <?php esc_html_e( 'All pages', 'popup-redirect-countdown' ); ?>
        </label>
        <br />
        <label>
            <input type="radio" name="prc_display_on" value="specific" <?php checked( $value, 'specific' ); ?> class="prc-display-on" />
            <?php esc_html_e( 'Specific pages', 'popup-redirect-countdown' ); ?>
        </label>
        <div id="prc-specific-pages" style="margin: 8px 0 0 24px; max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 8px; <?php echo 'specific' !== $value ? 'display:none;' : ''; ?>">
            <?php foreach ( $pages as $p ) : ?>
                <label style="display:block; margin-bottom: 4px;">
                    <input type="checkbox" name="prc_display_pages[]" value="<?php echo esc_attr( $p->ID ); ?>" <?php checked( in_array( $p->ID, $selected_pages, true ) ); ?> />
                    <?php echo esc_html( $p->post_title ); ?>
                </label>
            <?php endforeach; ?>
        </div>
        <script>
        jQuery(document).ready(function($){
            $('.prc-display-on').on('change', function(){
                $('#prc-specific-pages').toggle($(this).val() === 'specific');
            });
        });
        </script>
        <?php
    }

    /**
     * Render the opacity range slider.
     */
    public function render_opacity_field() {
        $value = get_option( 'prc_overlay_opacity', 0.7 );
        ?>
        <input type="range" name="prc_overlay_opacity" min="0" max="1" step="0.05" value="<?php echo esc_attr( $value ); ?>" id="prc_overlay_opacity" />
        <span id="prc-opacity-value"><?php echo esc_html( $value ); ?></span>
        <script>
        jQuery(document).ready(function($){
            $('#prc_overlay_opacity').on('input', function(){
                $('#prc-opacity-value').text($(this).val());
            });
        });
        </script>
        <?php
    }

    /**
     * Handle the "Remove Popup" reset action.
     */
    public function handle_reset() {
        if ( ! isset( $_POST['prc_reset_popup'] ) ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        check_admin_referer( 'prc_reset_action', 'prc_reset_nonce' );

        $options = array(
            'prc_image_url',
            'prc_image_alt',
            'prc_redirect_type',
            'prc_redirect_page',
            'prc_redirect_url',
            'prc_countdown_seconds',
            'prc_display_on',
            'prc_display_pages',
            'prc_cookie_days',
            'prc_overlay_opacity',
        );

        foreach ( $options as $option ) {
            delete_option( $option );
        }

        add_settings_error( 'prc_settings_group', 'prc_reset', __( 'All popup settings have been removed.', 'popup-redirect-countdown' ), 'updated' );
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
            <h1><?php esc_html_e( 'Popup Redirect Countdown Settings', 'popup-redirect-countdown' ); ?></h1>
            <?php settings_errors( 'prc_settings_group' ); ?>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'prc_settings_group' );
                do_settings_sections( 'prc-settings' );
                submit_button();
                ?>
            </form>
            <hr />
            <form method="post" action="" onsubmit="return confirm('<?php echo esc_js( __( 'Are you sure? This will clear all popup settings.', 'popup-redirect-countdown' ) ); ?>');">
                <?php wp_nonce_field( 'prc_reset_action', 'prc_reset_nonce' ); ?>
                <p class="description"><?php esc_html_e( 'Clear all settings and disable the popup.', 'popup-redirect-countdown' ); ?></p>
                <?php submit_button( __( 'Remove Popup', 'popup-redirect-countdown' ), 'delete', 'prc_reset_popup', false ); ?>
            </form>
        </div>
        <?php
    }
}
