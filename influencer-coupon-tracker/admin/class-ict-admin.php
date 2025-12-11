<?php
/**
 * Admin functionality for the plugin.
 *
 * @package    Influencer_Coupon_Tracker
 * @subpackage Influencer_Coupon_Tracker/admin
 */

class ICT_Admin {

    /**
     * Initialize admin functionality.
     */
    public function init() {
        // Add admin menu
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        
        // Enqueue admin scripts and styles
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        
        // Add meta box to coupon edit page
        add_action( 'add_meta_boxes', array( $this, 'add_coupon_meta_box' ) );
        
        // Save coupon meta
        add_action( 'woocommerce_coupon_options_save', array( $this, 'save_coupon_meta' ), 10, 2 );
        
        // AJAX handlers
        add_action( 'wp_ajax_ict_filter_dashboard', array( $this, 'ajax_filter_dashboard' ) );
        add_action( 'wp_ajax_ict_export_csv', array( $this, 'ajax_export_csv' ) );
        
        // Load sub-classes
        require_once ICT_PLUGIN_DIR . 'admin/class-ict-dashboard.php';
        require_once ICT_PLUGIN_DIR . 'admin/class-ict-coupon-details.php';
        require_once ICT_PLUGIN_DIR . 'admin/class-ict-coupon-settings.php';
        require_once ICT_PLUGIN_DIR . 'admin/class-ict-export.php';
    }

    /**
     * Add admin menu pages.
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __( 'Coupon Tracker', 'influencer-coupon-tracker' ),
            __( 'Coupon Tracker', 'influencer-coupon-tracker' ),
            'manage_woocommerce',
            'ict-dashboard',
            array( $this, 'render_dashboard_page' ),
            'dashicons-chart-line',
            56
        );

        // Dashboard submenu
        add_submenu_page(
            'ict-dashboard',
            __( 'Dashboard', 'influencer-coupon-tracker' ),
            __( 'Dashboard', 'influencer-coupon-tracker' ),
            'manage_woocommerce',
            'ict-dashboard',
            array( $this, 'render_dashboard_page' )
        );

        // Coupon Details submenu (hidden, accessed via link)
        add_submenu_page(
            null,
            __( 'Coupon Details', 'influencer-coupon-tracker' ),
            __( 'Coupon Details', 'influencer-coupon-tracker' ),
            'manage_woocommerce',
            'ict-coupon-details',
            array( $this, 'render_coupon_details_page' )
        );
    }

    /**
     * Enqueue admin assets.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_assets( $hook ) {
        // Only load on our plugin pages
        if ( strpos( $hook, 'ict-' ) === false && $hook !== 'toplevel_page_ict-dashboard' ) {
            // Also load on coupon edit pages
            global $post_type;
            if ( $post_type !== 'shop_coupon' ) {
                return;
            }
        }

        // Tailwind CSS via CDN
        wp_enqueue_style(
            'ict-tailwind',
            'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css',
            array(),
            '2.2.19'
        );

        // Custom admin styles
        wp_enqueue_style(
            'ict-admin-css',
            ICT_PLUGIN_URL . 'admin/css/ict-admin.css',
            array( 'ict-tailwind' ),
            ICT_VERSION
        );

        // Flatpickr for date picker
        wp_enqueue_style(
            'flatpickr',
            'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
            array(),
            '4.6.13'
        );

        wp_enqueue_script(
            'flatpickr',
            'https://cdn.jsdelivr.net/npm/flatpickr',
            array(),
            '4.6.13',
            true
        );

        // Admin JS
        wp_enqueue_script(
            'ict-admin-js',
            ICT_PLUGIN_URL . 'admin/js/ict-admin.js',
            array( 'jquery', 'flatpickr' ),
            ICT_VERSION,
            true
        );

        // Localize script
        wp_localize_script( 'ict-admin-js', 'ictAdmin', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'ict_admin_nonce' ),
            'i18n'    => array(
                'loading'       => __( 'Loading...', 'influencer-coupon-tracker' ),
                'exportSuccess' => __( 'Export completed!', 'influencer-coupon-tracker' ),
                'error'         => __( 'An error occurred.', 'influencer-coupon-tracker' ),
            ),
        ) );
    }

    /**
     * Render the main dashboard page.
     */
    public function render_dashboard_page() {
        $dashboard = new ICT_Dashboard();
        $dashboard->render();
    }

    /**
     * Render the coupon details page.
     */
    public function render_coupon_details_page() {
        $details = new ICT_Coupon_Details();
        $details->render();
    }

    /**
     * Add meta box to coupon edit page.
     */
    public function add_coupon_meta_box() {
        add_meta_box(
            'ict_influencer_settings',
            __( 'Influencer Settings', 'influencer-coupon-tracker' ),
            array( $this, 'render_coupon_meta_box' ),
            'shop_coupon',
            'side',
            'default'
        );
    }

    /**
     * Render the coupon meta box.
     *
     * @param WP_Post $post Post object.
     */
    public function render_coupon_meta_box( $post ) {
        $settings = new ICT_Coupon_Settings();
        $settings->render_meta_box( $post );
    }

    /**
     * Save coupon meta data.
     *
     * @param int       $post_id Post ID.
     * @param WC_Coupon $coupon  Coupon object.
     */
    public function save_coupon_meta( $post_id, $coupon ) {
        $settings = new ICT_Coupon_Settings();
        $settings->save( $post_id );
    }

    /**
     * AJAX handler for filtering dashboard.
     */
    public function ajax_filter_dashboard() {
        check_ajax_referer( 'ict_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'influencer-coupon-tracker' ) );
        }

        $dashboard = new ICT_Dashboard();
        $html = $dashboard->get_filtered_content( $_POST );
        
        wp_send_json_success( array( 'html' => $html ) );
    }

    /**
     * AJAX handler for CSV export.
     */
    public function ajax_export_csv() {
        check_ajax_referer( 'ict_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'influencer-coupon-tracker' ) );
        }

        $export = new ICT_Export();
        $export->generate_csv( $_POST );
    }
}
