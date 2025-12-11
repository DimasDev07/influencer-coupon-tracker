<?php
/**
 * Tracks coupon usage when orders are placed or updated.
 *
 * @package    Influencer_Coupon_Tracker
 * @subpackage Influencer_Coupon_Tracker/includes
 */

class ICT_Tracker {

    /**
     * Initialize the tracker hooks.
     */
    public function init() {
        // Track when order status changes
        add_action( 'woocommerce_order_status_changed', array( $this, 'track_coupon_usage' ), 10, 4 );
        
        // Also track on new orders
        add_action( 'woocommerce_new_order', array( $this, 'track_new_order' ), 10, 2 );
    }

    /**
     * Track coupon usage when a new order is created.
     *
     * @param int      $order_id Order ID.
     * @param WC_Order $order    Order object.
     */
    public function track_new_order( $order_id, $order = null ) {
        if ( ! $order ) {
            $order = wc_get_order( $order_id );
        }
        
        if ( ! $order ) {
            return;
        }

        $this->process_order_coupons( $order );
    }

    /**
     * Track coupon usage when order status changes.
     *
     * @param int    $order_id   Order ID.
     * @param string $old_status Old status.
     * @param string $new_status New status.
     * @param object $order      Order object.
     */
    public function track_coupon_usage( $order_id, $old_status, $new_status, $order ) {
        $this->update_order_status( $order_id, $new_status );
    }

    /**
     * Process coupons used in an order.
     *
     * @param WC_Order $order Order object.
     */
    private function process_order_coupons( $order ) {
        global $wpdb;
        
        $coupons = $order->get_coupon_codes();
        
        if ( empty( $coupons ) ) {
            return;
        }

        $table_name = $wpdb->prefix . 'ict_coupon_tracking';
        $order_id = $order->get_id();
        $order_total = $order->get_total();
        $order_status = $order->get_status();

        foreach ( $coupons as $coupon_code ) {
            // Check if already tracked
            $exists = $wpdb->get_var( $wpdb->prepare(
                "SELECT id FROM $table_name WHERE order_id = %d AND coupon_code = %s",
                $order_id,
                $coupon_code
            ) );

            if ( $exists ) {
                continue;
            }

            // Get coupon info
            $coupon = new WC_Coupon( $coupon_code );
            $coupon_id = $coupon->get_id();
            
            // Get discount amount for this coupon
            $discount_amount = $this->get_coupon_discount_from_order( $order, $coupon_code );
            
            // Calculate commission
            $commission = $this->calculate_commission( $coupon_id, $order_total, $discount_amount );

            // Insert tracking record
            $wpdb->insert(
                $table_name,
                array(
                    'order_id'          => $order_id,
                    'coupon_id'         => $coupon_id,
                    'coupon_code'       => $coupon_code,
                    'order_total'       => $order_total,
                    'discount_amount'   => $discount_amount,
                    'commission_amount' => $commission,
                    'order_status'      => $order_status,
                    'created_at'        => current_time( 'mysql' ),
                ),
                array( '%d', '%d', '%s', '%f', '%f', '%f', '%s', '%s' )
            );
        }
    }

    /**
     * Update order status in tracking table.
     *
     * @param int    $order_id   Order ID.
     * @param string $new_status New status.
     */
    private function update_order_status( $order_id, $new_status ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ict_coupon_tracking';
        
        $wpdb->update(
            $table_name,
            array( 'order_status' => $new_status ),
            array( 'order_id' => $order_id ),
            array( '%s' ),
            array( '%d' )
        );
    }

    /**
     * Get the discount amount for a specific coupon from an order.
     *
     * @param WC_Order $order       Order object.
     * @param string   $coupon_code Coupon code.
     * @return float
     */
    private function get_coupon_discount_from_order( $order, $coupon_code ) {
        $discount = 0;
        
        foreach ( $order->get_items( 'coupon' ) as $item ) {
            if ( strtolower( $item->get_code() ) === strtolower( $coupon_code ) ) {
                $discount = $item->get_discount();
                break;
            }
        }
        
        return floatval( $discount );
    }

    /**
     * Calculate commission based on coupon settings.
     *
     * @param int   $coupon_id       Coupon ID.
     * @param float $order_total     Order total.
     * @param float $discount_amount Discount amount.
     * @return float
     */
    private function calculate_commission( $coupon_id, $order_total, $discount_amount ) {
        $commission_type = get_post_meta( $coupon_id, '_ict_commission_type', true );
        $commission_value = floatval( get_post_meta( $coupon_id, '_ict_commission_value', true ) );

        if ( empty( $commission_type ) || $commission_type === 'none' || $commission_value <= 0 ) {
            return 0;
        }

        switch ( $commission_type ) {
            case 'fixed':
                return $commission_value;
            
            case 'percentage':
                // Commission based on order total (after discount)
                return ( $order_total * $commission_value ) / 100;
            
            default:
                return 0;
        }
    }
}
