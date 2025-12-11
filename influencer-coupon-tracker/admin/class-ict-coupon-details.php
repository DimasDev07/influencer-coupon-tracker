<?php
/**
 * Coupon details page.
 *
 * @package    Influencer_Coupon_Tracker
 * @subpackage Influencer_Coupon_Tracker/admin
 */

class ICT_Coupon_Details {

    /**
     * Current coupon ID.
     *
     * @var int
     */
    private $coupon_id;

    /**
     * Render the coupon details page.
     */
    public function render() {
        $this->coupon_id = isset( $_GET['coupon_id'] ) ? intval( $_GET['coupon_id'] ) : 0;
        
        if ( ! $this->coupon_id ) {
            $this->render_error( __( 'Invalid coupon ID.', 'influencer-coupon-tracker' ) );
            return;
        }

        $coupon = new WC_Coupon( $this->coupon_id );
        if ( ! $coupon->get_id() ) {
            $this->render_error( __( 'Coupon not found.', 'influencer-coupon-tracker' ) );
            return;
        }

        $filters = $this->get_filters();
        $stats = $this->get_coupon_stats( $filters );
        $orders = $this->get_coupon_orders( $filters );
        $influencer = get_post_meta( $this->coupon_id, '_ict_influencer_name', true );
        $commission_type = get_post_meta( $this->coupon_id, '_ict_commission_type', true );
        $commission_value = get_post_meta( $this->coupon_id, '_ict_commission_value', true );
        ?>
        <div class="ict-wrap">
            <div class="ict-container">
                <!-- Header -->
                <div class="ict-header">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=ict-dashboard' ) ); ?>" class="ict-back-link">
                        <span class="dashicons dashicons-arrow-left-alt"></span>
                        <?php esc_html_e( 'Back to Dashboard', 'influencer-coupon-tracker' ); ?>
                    </a>
                    <h1 class="ict-title">
                        <span class="dashicons dashicons-tag"></span>
                        <?php echo esc_html( strtoupper( $coupon->get_code() ) ); ?>
                    </h1>
                </div>

                <!-- Coupon Info Card -->
                <div class="ict-info-card">
                    <div class="ict-info-grid">
                        <div class="ict-info-item">
                            <span class="ict-info-label"><?php esc_html_e( 'Influencer', 'influencer-coupon-tracker' ); ?></span>
                            <span class="ict-info-value">
                                <?php echo $influencer ? esc_html( $influencer ) : '—'; ?>
                            </span>
                        </div>
                        <div class="ict-info-item">
                            <span class="ict-info-label"><?php esc_html_e( 'Discount Type', 'influencer-coupon-tracker' ); ?></span>
                            <span class="ict-info-value">
                                <?php echo esc_html( wc_get_coupon_type( $coupon->get_discount_type() ) ); ?>
                            </span>
                        </div>
                        <div class="ict-info-item">
                            <span class="ict-info-label"><?php esc_html_e( 'Discount Value', 'influencer-coupon-tracker' ); ?></span>
                            <span class="ict-info-value">
                                <?php 
                                if ( $coupon->get_discount_type() === 'percent' ) {
                                    echo esc_html( $coupon->get_amount() . '%' );
                                } else {
                                    echo wc_price( $coupon->get_amount() );
                                }
                                ?>
                            </span>
                        </div>
                        <div class="ict-info-item">
                            <span class="ict-info-label"><?php esc_html_e( 'Commission', 'influencer-coupon-tracker' ); ?></span>
                            <span class="ict-info-value">
                                <?php 
                                if ( $commission_type === 'fixed' ) {
                                    echo wc_price( $commission_value ) . ' ' . esc_html__( 'per order', 'influencer-coupon-tracker' );
                                } elseif ( $commission_type === 'percentage' ) {
                                    echo esc_html( $commission_value . '%' ) . ' ' . esc_html__( 'of order', 'influencer-coupon-tracker' );
                                } else {
                                    esc_html_e( 'No commission', 'influencer-coupon-tracker' );
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="ict-stats-grid ict-stats-grid-sm">
                    <div class="ict-stat-card ict-stat-primary">
                        <div class="ict-stat-content">
                            <span class="ict-stat-value"><?php echo esc_html( $stats['total_uses'] ); ?></span>
                            <span class="ict-stat-label"><?php esc_html_e( 'Times Used', 'influencer-coupon-tracker' ); ?></span>
                        </div>
                    </div>
                    <div class="ict-stat-card ict-stat-success">
                        <div class="ict-stat-content">
                            <span class="ict-stat-value"><?php echo wc_price( $stats['total_revenue'] ); ?></span>
                            <span class="ict-stat-label"><?php esc_html_e( 'Total Revenue', 'influencer-coupon-tracker' ); ?></span>
                        </div>
                    </div>
                    <div class="ict-stat-card ict-stat-info">
                        <div class="ict-stat-content">
                            <span class="ict-stat-value"><?php echo wc_price( $stats['total_commissions'] ); ?></span>
                            <span class="ict-stat-label"><?php esc_html_e( 'Total Commission', 'influencer-coupon-tracker' ); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="ict-filters-card">
                    <form method="get" class="ict-filters-form">
                        <input type="hidden" name="page" value="ict-coupon-details">
                        <input type="hidden" name="coupon_id" value="<?php echo esc_attr( $this->coupon_id ); ?>">
                        
                        <div class="ict-filter-group">
                            <label for="ict-date-from"><?php esc_html_e( 'From', 'influencer-coupon-tracker' ); ?></label>
                            <input type="text" id="ict-date-from" name="date_from" class="ict-datepicker" 
                                   value="<?php echo esc_attr( $filters['date_from'] ); ?>">
                        </div>

                        <div class="ict-filter-group">
                            <label for="ict-date-to"><?php esc_html_e( 'To', 'influencer-coupon-tracker' ); ?></label>
                            <input type="text" id="ict-date-to" name="date_to" class="ict-datepicker" 
                                   value="<?php echo esc_attr( $filters['date_to'] ); ?>">
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
                        </div>
                    </form>
                </div>

                <!-- Orders Table -->
                <div class="ict-table-card">
                    <div class="ict-table-header">
                        <h2><?php esc_html_e( 'Orders Using This Coupon', 'influencer-coupon-tracker' ); ?></h2>
                    </div>
                    
                    <?php if ( empty( $orders ) ) : ?>
                        <div class="ict-empty-state">
                            <span class="dashicons dashicons-info-outline"></span>
                            <p><?php esc_html_e( 'No orders found for this coupon.', 'influencer-coupon-tracker' ); ?></p>
                        </div>
                    <?php else : ?>
                        <div class="ict-table-responsive">
                            <table class="ict-table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e( 'Order', 'influencer-coupon-tracker' ); ?></th>
                                        <th><?php esc_html_e( 'Date', 'influencer-coupon-tracker' ); ?></th>
                                        <th><?php esc_html_e( 'Status', 'influencer-coupon-tracker' ); ?></th>
                                        <th class="text-right"><?php esc_html_e( 'Order Total', 'influencer-coupon-tracker' ); ?></th>
                                        <th class="text-right"><?php esc_html_e( 'Discount', 'influencer-coupon-tracker' ); ?></th>
                                        <th class="text-right"><?php esc_html_e( 'Commission', 'influencer-coupon-tracker' ); ?></th>
                                        <th class="text-center"><?php esc_html_e( 'Actions', 'influencer-coupon-tracker' ); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ( $orders as $order_data ) : 
                                        $order = wc_get_order( $order_data->order_id );
                                        if ( ! $order ) continue;
                                    ?>
                                        <tr>
                                            <td>
                                                <strong>#<?php echo esc_html( $order_data->order_id ); ?></strong>
                                            </td>
                                            <td>
                                                <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $order_data->created_at ) ) ); ?>
                                            </td>
                                            <td>
                                                <span class="ict-status ict-status-<?php echo esc_attr( $order_data->order_status ); ?>">
                                                    <?php echo esc_html( wc_get_order_status_name( $order_data->order_status ) ); ?>
                                                </span>
                                            </td>
                                            <td class="text-right">
                                                <?php echo wc_price( $order_data->order_total ); ?>
                                            </td>
                                            <td class="text-right ict-text-warning">
                                                -<?php echo wc_price( $order_data->discount_amount ); ?>
                                            </td>
                                            <td class="text-right ict-text-info">
                                                <?php echo wc_price( $order_data->commission_amount ); ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?php echo esc_url( $order->get_edit_order_url() ); ?>" 
                                                   class="ict-btn ict-btn-sm ict-btn-outline" target="_blank">
                                                    <?php esc_html_e( 'View Order', 'influencer-coupon-tracker' ); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render error message.
     *
     * @param string $message Error message.
     */
    private function render_error( $message ) {
        ?>
        <div class="ict-wrap">
            <div class="ict-container">
                <div class="ict-error-card">
                    <span class="dashicons dashicons-warning"></span>
                    <p><?php echo esc_html( $message ); ?></p>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=ict-dashboard' ) ); ?>" class="ict-btn ict-btn-primary">
                        <?php esc_html_e( 'Back to Dashboard', 'influencer-coupon-tracker' ); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Get current filters.
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
     * Get stats for this coupon.
     *
     * @param array $filters Filters.
     * @return array
     */
    private function get_coupon_stats( $filters ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'ict_coupon_tracking';
        $where = $this->build_where_clause( $filters );
        
        $results = $wpdb->get_row( $wpdb->prepare(
            "SELECT 
                COUNT(*) as total_uses,
                COALESCE(SUM(order_total), 0) as total_revenue,
                COALESCE(SUM(discount_amount), 0) as total_discounts,
                COALESCE(SUM(commission_amount), 0) as total_commissions
            FROM $table
            WHERE coupon_id = %d $where",
            $this->coupon_id
        ) );

        return array(
            'total_uses'        => intval( $results->total_uses ?? 0 ),
            'total_revenue'     => floatval( $results->total_revenue ?? 0 ),
            'total_discounts'   => floatval( $results->total_discounts ?? 0 ),
            'total_commissions' => floatval( $results->total_commissions ?? 0 ),
        );
    }

    /**
     * Get orders for this coupon.
     *
     * @param array $filters Filters.
     * @return array
     */
    private function get_coupon_orders( $filters ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'ict_coupon_tracking';
        $where = $this->build_where_clause( $filters );
        
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM $table 
            WHERE coupon_id = %d $where
            ORDER BY created_at DESC",
            $this->coupon_id
        ) );
    }

    /**
     * Build WHERE clause additions.
     *
     * @param array $filters Filters.
     * @return string
     */
    private function build_where_clause( $filters ) {
        global $wpdb;
        
        $conditions = array();
        
        if ( ! empty( $filters['date_from'] ) ) {
            $conditions[] = $wpdb->prepare( "AND created_at >= %s", $filters['date_from'] . ' 00:00:00' );
        }
        
        if ( ! empty( $filters['date_to'] ) ) {
            $conditions[] = $wpdb->prepare( "AND created_at <= %s", $filters['date_to'] . ' 23:59:59' );
        }
        
        if ( ! empty( $filters['order_status'] ) ) {
            $conditions[] = $wpdb->prepare( "AND order_status = %s", $filters['order_status'] );
        }
        
        return implode( ' ', $conditions );
    }
}
