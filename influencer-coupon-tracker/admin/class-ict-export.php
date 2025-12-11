<?php
/**
 * Export functionality.
 *
 * @package    Influencer_Coupon_Tracker
 * @subpackage Influencer_Coupon_Tracker/admin
 */

class ICT_Export {

    /**
     * Generate and download CSV file.
     *
     * @param array $filters Filters from request.
     */
    public function generate_csv( $filters ) {
        $data = $this->get_export_data( $filters );
        
        $filename = 'coupon-tracking-' . date( 'Y-m-d-His' ) . '.csv';
        
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=' . $filename );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );
        
        $output = fopen( 'php://output', 'w' );
        
        // Add UTF-8 BOM for Excel compatibility
        fprintf( $output, chr(0xEF) . chr(0xBB) . chr(0xBF) );
        
        // Headers
        fputcsv( $output, array(
            __( 'Coupon Code', 'influencer-coupon-tracker' ),
            __( 'Influencer', 'influencer-coupon-tracker' ),
            __( 'Order ID', 'influencer-coupon-tracker' ),
            __( 'Order Date', 'influencer-coupon-tracker' ),
            __( 'Order Status', 'influencer-coupon-tracker' ),
            __( 'Order Total', 'influencer-coupon-tracker' ),
            __( 'Discount Amount', 'influencer-coupon-tracker' ),
            __( 'Commission', 'influencer-coupon-tracker' ),
        ) );
        
        // Data rows
        foreach ( $data as $row ) {
            $influencer = get_post_meta( $row->coupon_id, '_ict_influencer_name', true );
            
            fputcsv( $output, array(
                strtoupper( $row->coupon_code ),
                $influencer ?: '-',
                $row->order_id,
                date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $row->created_at ) ),
                wc_get_order_status_name( $row->order_status ),
                $row->order_total,
                $row->discount_amount,
                $row->commission_amount,
            ) );
        }
        
        fclose( $output );
        exit;
    }

    /**
     * Get data for export.
     *
     * @param array $filters Filters.
     * @return array
     */
    private function get_export_data( $filters ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'ict_coupon_tracking';
        $where = $this->build_where_clause( $filters );
        
        return $wpdb->get_results(
            "SELECT * FROM $table $where ORDER BY created_at DESC"
        );
    }

    /**
     * Build WHERE clause.
     *
     * @param array $filters Filters.
     * @return string
     */
    private function build_where_clause( $filters ) {
        global $wpdb;
        
        $conditions = array();
        
        $date_from = sanitize_text_field( $filters['date_from'] ?? '' );
        $date_to = sanitize_text_field( $filters['date_to'] ?? '' );
        $order_status = sanitize_text_field( $filters['order_status'] ?? '' );
        
        if ( ! empty( $date_from ) ) {
            $conditions[] = $wpdb->prepare( "created_at >= %s", $date_from . ' 00:00:00' );
        }
        
        if ( ! empty( $date_to ) ) {
            $conditions[] = $wpdb->prepare( "created_at <= %s", $date_to . ' 23:59:59' );
        }
        
        if ( ! empty( $order_status ) ) {
            $conditions[] = $wpdb->prepare( "order_status = %s", $order_status );
        }
        
        if ( empty( $conditions ) ) {
            return '';
        }
        
        return 'WHERE ' . implode( ' AND ', $conditions );
    }
}
