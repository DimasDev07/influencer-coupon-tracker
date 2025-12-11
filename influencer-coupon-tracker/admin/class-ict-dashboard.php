<?php
/**
 * Dashboard page for the plugin.
 *
 * @package    Influencer_Coupon_Tracker
 * @subpackage Influencer_Coupon_Tracker/admin
 */

class ICT_Dashboard {

    /**
     * Render the dashboard page.
     */
    public function render() {
        $filters = $this->get_filters();
        $stats = $this->get_stats( $filters );
        $coupons = $this->get_coupons_data( $filters );
        ?>
        <div class="ict-wrap">
            <div class="ict-container">
                <!-- Header -->
                <div class="ict-header">
                    <h1 class="ict-title">
                        <span class="dashicons dashicons-chart-line"></span>
                        <?php esc_html_e( 'Influencer Coupon Tracker', 'influencer-coupon-tracker' ); ?>
                    </h1>
                    <p class="ict-subtitle">
                        <?php esc_html_e( 'Track coupon performance and influencer ROI', 'influencer-coupon-tracker' ); ?>
                    </p>
                </div>

                <!-- Stats Cards -->
                <div class="ict-stats-grid">
                    <div class="ict-stat-card ict-stat-primary">
                        <div class="ict-stat-icon">
                            <span class="dashicons dashicons-tag"></span>
                        </div>
                        <div class="ict-stat-content">
                            <span class="ict-stat-value"><?php echo esc_html( $stats['total_uses'] ); ?></span>
                            <span class="ict-stat-label"><?php esc_html_e( 'Total Uses', 'influencer-coupon-tracker' ); ?></span>
                        </div>
                    </div>

                    <div class="ict-stat-card ict-stat-success">
                        <div class="ict-stat-icon">
                            <span class="dashicons dashicons-cart"></span>
                        </div>
                        <div class="ict-stat-content">
                            <span class="ict-stat-value"><?php echo wc_price( $stats['total_revenue'] ); ?></span>
                            <span class="ict-stat-label"><?php esc_html_e( 'Total Revenue', 'influencer-coupon-tracker' ); ?></span>
                        </div>
                    </div>

                    <div class="ict-stat-card ict-stat-warning">
                        <div class="ict-stat-icon">
                            <span class="dashicons dashicons-tickets-alt"></span>
                        </div>
                        <div class="ict-stat-content">
                            <span class="ict-stat-value"><?php echo wc_price( $stats['total_discounts'] ); ?></span>
                            <span class="ict-stat-label"><?php esc_html_e( 'Total Discounts', 'influencer-coupon-tracker' ); ?></span>
                        </div>
                    </div>

                    <div class="ict-stat-card ict-stat-info">
                        <div class="ict-stat-icon">
                            <span class="dashicons dashicons-money-alt"></span>
                        </div>
                        <div class="ict-stat-content">
                            <span class="ict-stat-value"><?php echo wc_price( $stats['total_commissions'] ); ?></span>
                            <span class="ict-stat-label"><?php esc_html_e( 'Total Commissions', 'influencer-coupon-tracker' ); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="ict-filters-card">
                    <form id="ict-filters-form" class="ict-filters-form">
                        <div class="ict-filter-group">
                            <label for="ict-date-from"><?php esc_html_e( 'From', 'influencer-coupon-tracker' ); ?></label>
                            <input type="text" id="ict-date-from" name="date_from" class="ict-datepicker" 
                                   value="<?php echo esc_attr( $filters['date_from'] ); ?>" 
                                   placeholder="<?php esc_attr_e( 'Start date', 'influencer-coupon-tracker' ); ?>">
                        </div>

                        <div class="ict-filter-group">
                            <label for="ict-date-to"><?php esc_html_e( 'To', 'influencer-coupon-tracker' ); ?></label>
                            <input type="text" id="ict-date-to" name="date_to" class="ict-datepicker" 
                                   value="<?php echo esc_attr( $filters['date_to'] ); ?>" 
                                   placeholder="<?php esc_attr_e( 'End date', 'influencer-coupon-tracker' ); ?>">
                        </div>

                        <div class="ict-filter-group">
                            <label for="ict-order-status"><?php esc_html_e( 'Order Status', 'influencer-coupon-tracker' ); ?></label>
                            <select id="ict-order-status" name="order_status" class="ict-select">
                                <option value=""><?php esc_html_e( 'All Statuses', 'influencer-coupon-tracker' ); ?></option>
                                <?php foreach ( wc_get_order_statuses() as $status_key => $status_label ) : 
                                    $status_key = str_replace( 'wc-', '', $status_key );
                                ?>
                                    <option value="<?php echo esc_attr( $status_key ); ?>" 
                                            <?php selected( $filters['order_status'], $status_key ); ?>>
                                        <?php echo esc_html( $status_label ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="ict-filter-actions">
                            <button type="submit" class="ict-btn ict-btn-primary">
                                <span class="dashicons dashicons-filter"></span>
                                <?php esc_html_e( 'Filter', 'influencer-coupon-tracker' ); ?>
                            </button>
                            <button type="button" id="ict-reset-filters" class="ict-btn ict-btn-secondary">
                                <span class="dashicons dashicons-dismiss"></span>
                                <?php esc_html_e( 'Reset', 'influencer-coupon-tracker' ); ?>
                            </button>
                            <button type="button" id="ict-export-csv" class="ict-btn ict-btn-success">
                                <span class="dashicons dashicons-download"></span>
                                <?php esc_html_e( 'Export CSV', 'influencer-coupon-tracker' ); ?>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Coupons Table -->
                <div class="ict-table-card" id="ict-coupons-table">
                    <?php $this->render_coupons_table( $coupons ); ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render the coupons table.
     *
     * @param array $coupons Coupons data.
     */
    public function render_coupons_table( $coupons ) {
        ?>
        <div class="ict-table-header">
            <h2><?php esc_html_e( 'Coupon Performance', 'influencer-coupon-tracker' ); ?></h2>
        </div>
        
        <?php if ( empty( $coupons ) ) : ?>
            <div class="ict-empty-state">
                <span class="dashicons dashicons-info-outline"></span>
                <p><?php esc_html_e( 'No coupon usage data found for the selected filters.', 'influencer-coupon-tracker' ); ?></p>
            </div>
        <?php else : ?>
            <div class="ict-table-responsive">
                <table class="ict-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Coupon Code', 'influencer-coupon-tracker' ); ?></th>
                            <th><?php esc_html_e( 'Influencer', 'influencer-coupon-tracker' ); ?></th>
                            <th class="text-center"><?php esc_html_e( 'Uses', 'influencer-coupon-tracker' ); ?></th>
                            <th class="text-right"><?php esc_html_e( 'Revenue', 'influencer-coupon-tracker' ); ?></th>
                            <th class="text-right"><?php esc_html_e( 'Discounts', 'influencer-coupon-tracker' ); ?></th>
                            <th class="text-right"><?php esc_html_e( 'Commission', 'influencer-coupon-tracker' ); ?></th>
                            <th class="text-center"><?php esc_html_e( 'Actions', 'influencer-coupon-tracker' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $coupons as $coupon ) : ?>
                            <tr>
                                <td>
                                    <span class="ict-coupon-code"><?php echo esc_html( strtoupper( $coupon->coupon_code ) ); ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $influencer = get_post_meta( $coupon->coupon_id, '_ict_influencer_name', true );
                                    echo $influencer ? esc_html( $influencer ) : '<span class="ict-text-muted">—</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <span class="ict-badge"><?php echo esc_html( $coupon->uses ); ?></span>
                                </td>
                                <td class="text-right ict-text-success">
                                    <?php echo wc_price( $coupon->total_revenue ); ?>
                                </td>
                                <td class="text-right ict-text-warning">
                                    <?php echo wc_price( $coupon->total_discounts ); ?>
                                </td>
                                <td class="text-right ict-text-info">
                                    <?php echo wc_price( $coupon->total_commissions ); ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=ict-coupon-details&coupon_id=' . $coupon->coupon_id ) ); ?>" 
                                       class="ict-btn ict-btn-sm ict-btn-outline">
                                        <?php esc_html_e( 'View Details', 'influencer-coupon-tracker' ); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif;
    }

    /**
     * Get filtered content for AJAX.
     *
     * @param array $post_data POST data.
     * @return string
     */
    public function get_filtered_content( $post_data ) {
        $filters = array(
            'date_from'    => sanitize_text_field( $post_data['date_from'] ?? '' ),
            'date_to'      => sanitize_text_field( $post_data['date_to'] ?? '' ),
            'order_status' => sanitize_text_field( $post_data['order_status'] ?? 'completed' ),
        );

        $coupons = $this->get_coupons_data( $filters );
        
        ob_start();
        $this->render_coupons_table( $coupons );
        return ob_get_clean();
    }

    /**
     * Get current filters from request.
     *
     * @return array
     */
    private function get_filters() {
        return array(
            'date_from'    => sanitize_text_field( $_GET['date_from'] ?? '' ),
            'date_to'      => sanitize_text_field( $_GET['date_to'] ?? '' ),
            'order_status' => sanitize_text_field( $_GET['order_status'] ?? 'completed' ),
        );
    }

    /**
     * Get dashboard stats.
     *
     * @param array $filters Filters.
     * @return array
     */
    private function get_stats( $filters ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'ict_coupon_tracking';
        $where = $this->build_where_clause( $filters );
        
        $results = $wpdb->get_row(
            "SELECT 
                COUNT(*) as total_uses,
                COALESCE(SUM(order_total), 0) as total_revenue,
                COALESCE(SUM(discount_amount), 0) as total_discounts,
                COALESCE(SUM(commission_amount), 0) as total_commissions
            FROM $table
            $where"
        );

        return array(
            'total_uses'        => intval( $results->total_uses ?? 0 ),
            'total_revenue'     => floatval( $results->total_revenue ?? 0 ),
            'total_discounts'   => floatval( $results->total_discounts ?? 0 ),
            'total_commissions' => floatval( $results->total_commissions ?? 0 ),
        );
    }

    /**
     * Get coupons data grouped by coupon.
     *
     * @param array $filters Filters.
     * @return array
     */
    private function get_coupons_data( $filters ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'ict_coupon_tracking';
        $where = $this->build_where_clause( $filters );
        
        $results = $wpdb->get_results(
            "SELECT 
                coupon_id,
                coupon_code,
                COUNT(*) as uses,
                SUM(order_total) as total_revenue,
                SUM(discount_amount) as total_discounts,
                SUM(commission_amount) as total_commissions
            FROM $table
            $where
            GROUP BY coupon_id, coupon_code
            ORDER BY total_revenue DESC"
        );

        return $results;
    }

    /**
     * Build WHERE clause for queries.
     *
     * @param array $filters Filters.
     * @return string
     */
    private function build_where_clause( $filters ) {
        global $wpdb;
        
        $conditions = array();
        
        if ( ! empty( $filters['date_from'] ) ) {
            $conditions[] = $wpdb->prepare( "created_at >= %s", $filters['date_from'] . ' 00:00:00' );
        }
        
        if ( ! empty( $filters['date_to'] ) ) {
            $conditions[] = $wpdb->prepare( "created_at <= %s", $filters['date_to'] . ' 23:59:59' );
        }
        
        if ( ! empty( $filters['order_status'] ) ) {
            $conditions[] = $wpdb->prepare( "order_status = %s", $filters['order_status'] );
        }
        
        if ( empty( $conditions ) ) {
            return '';
        }
        
        return 'WHERE ' . implode( ' AND ', $conditions );
    }
}
