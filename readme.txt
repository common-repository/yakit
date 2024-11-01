=== Yakit for WooCommerce ===
Contributors: Yakit
Tags: shipping, ddp, guaranteed duties and taxes, woocommerce
Requires at least: 3.0
Tested up to: 4.8
Stable tag: trunk
License: GPLv2 or later

Yakit - Hassle-free international shipping

== Description ==
Now, your international customers can buy without fear of having to pay additional fees for duties/taxes at the time of delivery.

The Yakit for WooCommerce plugin provides the following capabilities for your store :

* Yakit international shipping rates and duties/taxes (fully landed cost) in your WooCommerce shopping cart.
* Two service types : Yakit Standard (6-12 days worldwide) and Yakit Express (2-5 days worldwide)
* Ability to pull WooCommerce orders (whether they are quoted by Yakit in your shopping cart or not) into Yakit.
* Access to the Yakit shipping tool to process your shipments.
* Yakit updates the WooCommerce shipped order notification email with the 'Yakit tracking URL upon shipment dispatch in Yakit'.

For more information, check out https://www.yakit.com/woocommerce/yakit-for-woocommerce/

= Requirements =
* WordPress version 3.0 or later
* WooCommerce version 3.0 or later

= Installation =
1. Unpack the download package
2. Upload Yakit folder to the `/wp-content/plugins/` directory
3. Ensure WooCommerce is installed and active
4. While activating the plugin through the 'Plugins' menu in WordPress (will be prompted with woocommerce authorization screen 
just approve it and proceed with the yakit account setup)
5. Once registered/logged into yakit.com, you will be re-directed to store admin dashboard with auto populated Yakit account credentials under 
woocommerce API tab wp-admin/admin.php?page=wc-settings&tab=api&section=yakit_settings
6. Configure the Yakit shipping method for all zones
	a) Click the 'Manage shipping methods' on mouse over the text 'Locations not covered by your other zones'
	b) Click the 'Add shipping method' button, pop-up opens with shipping method dropdown. Select the Yakit shipping and click blue color button.
	c) After adding, mouse over text 'Yakit Shipping'  can find the Edit/Delete links. Click edit link.
	d) Can enable/disable the Yakit shipping method and save.
7. Configure the Yakit shipping method for specific shipping zones
	a) Go to admin dashboard Dashboard-> Woocommerce-> Shipping-> Shipping Zones /wp-admin/admin.php?page=wc-settings&tab=shipping
	b) Click the button 'Add shipping zone'
	c) Enter Zone name, Zone regions, Shipping methods
	d) While selecting the 'Shipping methods' Click the 'Add shipping method' button, pop-up opens with shipping method dropdown. Select the Yakit shipping and click blue color button.
	e) After adding, mouse over text 'Yakit Shipping'  can find the Edit/Delete links. Click edit link.
	f) Can enable/disable the Yakit shipping method and Save.
8. In order to pull the store orders into Yakit dashboard, click the 'Yakit Shipping Tool' menu under 'Woocommerce'

For further support reach us through Y-Chat from yakit.com.

= License =
This plugin is released under the GPL. You can use it free of charge on your personal or commercial blog.

== Screenshots ==

1. The slick Yakit plugin row.
2. After clicking activate link reaching woocommerce authorization screen.
3. Register/Login to Yakit.
4. Woocommerce Yakit API settings
5. Open your store link in new tab and go to woocommerce->settings->Shipping->shipping Zones Yakit tab.
6. Click the 'Manage shipping methods' on mouse over the text 'Locations not covered by your other zones'
7. Click the 'Add shipping method' button, pop-up opens with shipping method dropdown. Select the Yakit shipping and click blue color button.
8. Link to 'Yakit Shipping Tool'


== Changelog ==

= v1.2.3 (2018-02-14) =
* Included Region in the Rate API requests to get more precise duties/taxes for certain countries.

= v1.2.2 (2017-09-11) =
* Updated the rate api timeout - 10000 to wp_remote_get parameter
* Declared the null array if no parameter set in calculate_shipping

= v1.2.1 (2017-09-04) =
* Set the rate api timeout - 500 to wp_remote_get parameter

= v1.2.0 (2017-08-28) =
* Rate api updated and calculate shipping modified based on the service types

= v1.1.0 (2017-07-25) =
* Automated the Yakit account settings
* Simplified the overall process
* Yakit Shipping Tool link added under woocommerce menu

= v1.0.1 (2017-07-17) =
* Added the supporting zones

= v1.0.0 (2017-06-26) =
* Refactored plugin for listing in WordPress.org and Woo directories.
* Orders pulled to yakit using woocommerce api.
* Store front displayed with real time rates including Duties and Taxes
* Improved integration with WooCommerce.
* Improved support for variable products.