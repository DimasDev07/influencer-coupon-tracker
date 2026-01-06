<?php
/**
 * Plugin Name: Influencer Coupon Tracker
 * Plugin URI: https://github.com/DimasDev07/influencer-coupon-tracker
 * Description: Track WooCommerce coupon usage by influencers. Monitor ROI, calculate commissions, and export reports.
 * Version: 1.0.0
 * Author: Dimas Bueno
 * Author URI: https://github.com/DimasDev07/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: influencer-coupon-tracker
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * WC requires at least: 4.0
 * WC tested up to: 8.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Current plugin version.
 */
define( 'ICT_VERSION', '1.0.0' );
define( 'ICT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ICT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ICT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Check if WooCommerce is active
 */
function ict_check_woocommerce_active() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'ict_woocommerce_missing_notice' );
        return false;
    }
    return true;
}

/**
 * Admin notice for missing WooCommerce
 */
function ict_woocommerce_missing_notice() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p>
            <strong><?php esc_html_e( 'Influencer Coupon Tracker', 'influencer-coupon-tracker' ); ?></strong>
            <?php esc_html_e( 'requires WooCommerce to be installed and active.', 'influencer-coupon-tracker' ); ?>
            <a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) ); ?>">
                <?php esc_html_e( 'Install WooCommerce', 'influencer-coupon-tracker' ); ?>
            </a>
        </p>
    </div>
    <?php
}

/**
 * Load plugin textdomain for translations
 */
function ict_load_textdomain() {
    load_plugin_textdomain(
        'influencer-coupon-tracker',
        false,
        dirname( ICT_PLUGIN_BASENAME ) . '/languages'
    );
}
add_action( 'plugins_loaded', 'ict_load_textdomain' );

/**
 * The code that runs during plugin activation.
 */
function ict_activate() {
    require_once ICT_PLUGIN_DIR . 'includes/class-ict-activator.php';
    ICT_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function ict_deactivate() {
    require_once ICT_PLUGIN_DIR . 'includes/class-ict-deactivator.php';
    ICT_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'ict_activate' );
register_deactivation_hook( __FILE__, 'ict_deactivate' );

/**
 * Begin execution of the plugin.
 */
function ict_run() {
    // Check WooCommerce dependency
    if ( ! ict_check_woocommerce_active() ) {
        return;
    }

    // Load required files
    require_once ICT_PLUGIN_DIR . 'includes/class-ict-tracker.php';
    
    if ( is_admin() ) {
        require_once ICT_PLUGIN_DIR . 'admin/class-ict-admin.php';
        $admin = new ICT_Admin();
        $admin->init();
    }

    // Initialize tracker
    $tracker = new ICT_Tracker();
    $tracker->init();
}
add_action( 'plugins_loaded', 'ict_run', 20 );
