# Influencer Coupon Tracker

![WordPress Plugin](https://img.shields.io/badge/WordPress-5.0+-blue.svg)
![WooCommerce](https://img.shields.io/badge/WooCommerce-4.0+-purple.svg)
![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4.svg)
![License](https://img.shields.io/badge/license-GPLv2-green.svg)

Track WooCommerce coupon usage by influencers. Monitor ROI, calculate commissions, and export reports.

## Description

**Influencer Coupon Tracker** helps you track the performance of discount coupons assigned to influencers and affiliates. Perfect for measuring ROI on influencer marketing campaigns.

## Features

- **Usage Tracking**: Automatically track when coupons are used in orders
- **Influencer Assignment**: Assign influencers/affiliates to specific coupons
- **Commission Calculation**: Configure fixed or percentage-based commissions
- **Dashboard Analytics**: View total uses, revenue, discounts, and commissions
- **Order Details**: See all orders that used a specific coupon
- **Date Filtering**: Filter data by date range
- **Status Filtering**: Filter by order status (completed, processing, etc.)
- **CSV Export**: Export all tracking data to CSV for reporting

## Requirements

- WordPress 5.0 or higher
- WooCommerce 4.0 or higher
- PHP 7.4 or higher

## Installation

1. Upload the `influencer-coupon-tracker` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce > Coupons and edit a coupon to add influencer settings
4. View tracking data in the new "Coupon Tracker" menu

## Usage

### Configuring a Coupon

1. Navigate to **WooCommerce > Coupons**
2. Create or edit an existing coupon
3. Find the **Influencer Settings** section
4. Add the influencer/affiliate name
5. Choose commission type:
   - None
   - Fixed amount per order
   - Percentage of order total (after discount)
6. Set the commission value
7. Save the coupon

### Viewing Analytics

1. Go to **Coupon Tracker** in the WordPress admin menu
2. Select a coupon from the dropdown
3. Use date filters to refine the data
4. View:
   - Total coupon uses
   - Total revenue generated
   - Total discounts applied
   - Total commissions owed

### Exporting Data

1. Configure your filters (coupon, date range, order status)
2. Click the **Export to CSV** button
3. Open the CSV file in Excel or Google Sheets

## FAQ

### Does this work with any coupon type?

Yes, it works with all WooCommerce coupon types (percentage, fixed cart, fixed product).

### How are commissions calculated?

You can choose between:
- **No commission**: No commission tracking
- **Fixed amount**: A fixed amount per order (e.g., $10 per order)
- **Percentage**: A percentage of the order total after the discount is applied

### Can I export the data?

Yes, you can export all tracking data to CSV format with the current filters applied (date range, order status, specific coupon).

### Is the data tracked in real-time?

Yes, coupon usage is tracked automatically when an order is placed in WooCommerce.

## Project Structure

```
influencer-coupon-tracker/
├── admin/                      # Admin interface classes
│   ├── class-ict-admin.php
│   ├── class-ict-coupon-details.php
│   ├── class-ict-coupon-settings.php
│   ├── class-ict-dashboard.php
│   ├── class-ict-export.php
│   ├── css/
│   │   └── ict-admin.css
│   └── js/
│       └── ict-admin.js
├── includes/                   # Core functionality
│   ├── class-ict-activator.php
│   ├── class-ict-deactivator.php
│   └── class-ict-tracker.php
├── languages/                  # Translation files
├── influencer-coupon-tracker.php
└── README.txt
```

## Changelog

### Version 1.0.0
- Initial release
- Coupon tracking functionality
- Influencer assignment
- Commission calculation (fixed and percentage)
- Dashboard with analytics
- CSV export functionality
- Date and status filtering

## License

This project is licensed under the GPLv2 or later - see the [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) for details.

## Author

**Dimas Bueno**

## Contributing

Contributions, issues, and feature requests are welcome!

## Support

If you find this plugin helpful, please consider:
- Leaving a review
- Sharing with others
- Contributing to the code

---

Made with love for the WordPress community
