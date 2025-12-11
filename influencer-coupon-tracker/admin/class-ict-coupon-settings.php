<?php
/**
 * Coupon settings meta box.
 *
 * @package    Influencer_Coupon_Tracker
 * @subpackage Influencer_Coupon_Tracker/admin
 */

class ICT_Coupon_Settings {

    /**
     * Render the meta box content.
     *
     * @param WP_Post $post Post object.
     */
    public function render_meta_box( $post ) {
        // Get current values
        $influencer_name = get_post_meta( $post->ID, '_ict_influencer_name', true );
        $commission_type = get_post_meta( $post->ID, '_ict_commission_type', true );
        $commission_value = get_post_meta( $post->ID, '_ict_commission_value', true );

        wp_nonce_field( 'ict_coupon_settings', 'ict_coupon_settings_nonce' );
        ?>
        <div class="ict-meta-box">
            <p>
                <label for="ict_influencer_name">
                    <strong><?php esc_html_e( 'Influencer Name', 'influencer-coupon-tracker' ); ?></strong>
                </label>
                <input type="text" 
                       id="ict_influencer_name" 
                       name="ict_influencer_name" 
                       value="<?php echo esc_attr( $influencer_name ); ?>" 
                       class="widefat"
                       placeholder="<?php esc_attr_e( 'e.g. @influencer_username', 'influencer-coupon-tracker' ); ?>">
            </p>

            <p>
                <label for="ict_commission_type">
                    <strong><?php esc_html_e( 'Commission Type', 'influencer-coupon-tracker' ); ?></strong>
                </label>
                <select id="ict_commission_type" name="ict_commission_type" class="widefat">
                    <option value="none" <?php selected( $commission_type, 'none' ); ?>>
                        <?php esc_html_e( 'No Commission', 'influencer-coupon-tracker' ); ?>
                    </option>
                    <option value="fixed" <?php selected( $commission_type, 'fixed' ); ?>>
                        <?php esc_html_e( 'Fixed Amount', 'influencer-coupon-tracker' ); ?>
                    </option>
                    <option value="percentage" <?php selected( $commission_type, 'percentage' ); ?>>
                        <?php esc_html_e( 'Percentage of Order', 'influencer-coupon-tracker' ); ?>
                    </option>
                </select>
            </p>

            <p id="ict_commission_value_field" style="<?php echo $commission_type === 'none' || empty( $commission_type ) ? 'display:none;' : ''; ?>">
                <label for="ict_commission_value">
                    <strong><?php esc_html_e( 'Commission Value', 'influencer-coupon-tracker' ); ?></strong>
                </label>
                <input type="number" 
                       id="ict_commission_value" 
                       name="ict_commission_value" 
                       value="<?php echo esc_attr( $commission_value ); ?>" 
                       class="widefat"
                       step="0.01"
                       min="0"
                       placeholder="<?php esc_attr_e( 'Enter value', 'influencer-coupon-tracker' ); ?>">
                <span class="description" id="ict_commission_help">
                    <?php 
                    if ( $commission_type === 'percentage' ) {
                        esc_html_e( 'Enter percentage (e.g., 10 for 10%)', 'influencer-coupon-tracker' );
                    } else {
                        esc_html_e( 'Enter fixed amount', 'influencer-coupon-tracker' );
                    }
                    ?>
                </span>
            </p>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#ict_commission_type').on('change', function() {
                var type = $(this).val();
                if (type === 'none') {
                    $('#ict_commission_value_field').hide();
                } else {
                    $('#ict_commission_value_field').show();
                    if (type === 'percentage') {
                        $('#ict_commission_help').text('<?php echo esc_js( __( 'Enter percentage (e.g., 10 for 10%)', 'influencer-coupon-tracker' ) ); ?>');
                    } else {
                        $('#ict_commission_help').text('<?php echo esc_js( __( 'Enter fixed amount', 'influencer-coupon-tracker' ) ); ?>');
                    }
                }
            });
        });
        </script>
        <?php
    }

    /**
     * Save meta box data.
     *
     * @param int $post_id Post ID.
     */
    public function save( $post_id ) {
        // Verify nonce
        if ( ! isset( $_POST['ict_coupon_settings_nonce'] ) || 
             ! wp_verify_nonce( $_POST['ict_coupon_settings_nonce'], 'ict_coupon_settings' ) ) {
            return;
        }

        // Check autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save influencer name
        if ( isset( $_POST['ict_influencer_name'] ) ) {
            update_post_meta( $post_id, '_ict_influencer_name', sanitize_text_field( $_POST['ict_influencer_name'] ) );
        }

        // Save commission type
        if ( isset( $_POST['ict_commission_type'] ) ) {
            $allowed_types = array( 'none', 'fixed', 'percentage' );
            $type = sanitize_text_field( $_POST['ict_commission_type'] );
            if ( in_array( $type, $allowed_types, true ) ) {
                update_post_meta( $post_id, '_ict_commission_type', $type );
            }
        }

        // Save commission value
        if ( isset( $_POST['ict_commission_value'] ) ) {
            update_post_meta( $post_id, '_ict_commission_value', floatval( $_POST['ict_commission_value'] ) );
        }
    }
}
