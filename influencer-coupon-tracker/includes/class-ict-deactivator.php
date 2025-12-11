<?php
/**
 * Fired during plugin deactivation.
 *
 * @package    Influencer_Coupon_Tracker
 * @subpackage Influencer_Coupon_Tracker/includes
 */

class ICT_Deactivator {

    /**
     * Clean up on deactivation.
     * 
     * Note: We don't delete the database table here to preserve data.
     * The table will only be deleted on uninstall.
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clear any transients
        delete_transient( 'ict_dashboard_stats' );
    }
}
