<?php
/**
 * Fired during plugin activation.
 *
 * @package    Influencer_Coupon_Tracker
 * @subpackage Influencer_Coupon_Tracker/includes
 */

class ICT_Activator {

    /**
     * Create database tables and set default options.
     */
    public static function activate() {
        self::create_tables();
        self::set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create the tracking table.
     */
    private static function create_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ict_coupon_tracking';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id BIGINT(20) UNSIGNED NOT NULL,
            coupon_id BIGINT(20) UNSIGNED NOT NULL,
            coupon_code VARCHAR(255) NOT NULL,
            order_total DECIMAL(10,2) NOT NULL DEFAULT 0,
            discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
            commission_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
            order_status VARCHAR(50) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_coupon_code (coupon_code),
            KEY idx_order_id (order_id),
            KEY idx_order_status (order_status),
            KEY idx_created_at (created_at)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        // Store the database version
        update_option( 'ict_db_version', ICT_VERSION );
    }

    /**
     * Set default plugin options.
     */
    private static function set_default_options() {
        $default_options = array(
            'default_order_status_filter' => 'completed',
            'items_per_page' => 20,
        );

        if ( ! get_option( 'ict_options' ) ) {
            update_option( 'ict_options', $default_options );
        }
    }
}
