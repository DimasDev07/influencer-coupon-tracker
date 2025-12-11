/**
 * Influencer Coupon Tracker - Admin JavaScript
 */
(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        ICT.init();
    });

    var ICT = {
        /**
         * Initialize all functionality
         */
        init: function() {
            this.initDatePickers();
            this.initFilters();
            this.initExport();
        },

        /**
         * Initialize Flatpickr date pickers
         */
        initDatePickers: function() {
            if (typeof flatpickr === 'undefined') {
                return;
            }

            $('.ict-datepicker').flatpickr({
                dateFormat: 'Y-m-d',
                allowInput: true,
                locale: {
                    firstDayOfWeek: 1
                }
            });
        },

        /**
         * Initialize filter form handling
         */
        initFilters: function() {
            var self = this;

            // Filter form submission
            $('#ict-filters-form').on('submit', function(e) {
                e.preventDefault();
                self.filterDashboard();
            });

            // Reset filters
            $('#ict-reset-filters').on('click', function() {
                $('#ict-date-from').val('');
                $('#ict-date-to').val('');
                $('#ict-order-status').val('completed');
                self.filterDashboard();
            });
        },

        /**
         * Filter dashboard via AJAX
         */
        filterDashboard: function() {
            var $table = $('#ict-coupons-table');
            
            $table.addClass('ict-loading');

            $.ajax({
                url: ictAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ict_filter_dashboard',
                    nonce: ictAdmin.nonce,
                    date_from: $('#ict-date-from').val(),
                    date_to: $('#ict-date-to').val(),
                    order_status: $('#ict-order-status').val()
                },
                success: function(response) {
                    if (response.success) {
                        $table.html(response.data.html);
                    } else {
                        alert(ictAdmin.i18n.error);
                    }
                },
                error: function() {
                    alert(ictAdmin.i18n.error);
                },
                complete: function() {
                    $table.removeClass('ict-loading');
                }
            });
        },

        /**
         * Initialize export functionality
         */
        initExport: function() {
            var self = this;

            $('#ict-export-csv').on('click', function(e) {
                e.preventDefault();
                self.exportCSV();
            });
        },

        /**
         * Export data to CSV
         */
        exportCSV: function() {
            var params = new URLSearchParams({
                action: 'ict_export_csv',
                nonce: ictAdmin.nonce,
                date_from: $('#ict-date-from').val() || '',
                date_to: $('#ict-date-to').val() || '',
                order_status: $('#ict-order-status').val() || ''
            });

            // Create a temporary form and submit it
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = ictAdmin.ajaxUrl;
            form.style.display = 'none';

            // Add form fields
            var fields = {
                action: 'ict_export_csv',
                nonce: ictAdmin.nonce,
                date_from: $('#ict-date-from').val() || '',
                date_to: $('#ict-date-to').val() || '',
                order_status: $('#ict-order-status').val() || ''
            };

            for (var key in fields) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = fields[key];
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    };

    // Expose ICT to global scope
    window.ICT = ICT;

})(jQuery);
