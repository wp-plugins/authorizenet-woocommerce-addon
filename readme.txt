==== Authorize.Net Payment Gateway WooCommerce Addon ====
Contributors: nazrulhassanmca
Plugin Name: Authorize.Net WooCommerce Addon
Plugin URI: https://wordpress.org/plugins/authorizenet-woocommerce-addon/
Tags: woocommerce, authorize.net, woocommerce addon ,authorize.net for woocommerce,authorize.net for wordpress,credit card payment with Authorize.Net,authorize.net for woocommerce,authorize.net payment gateway for woocommerce,wordpress wocommerce authorize.net,wordpress authorize.net wocommerce
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=nazrulhassan@ymail.com&item_name=Donation+Authorize.Net+Woocommerce+Addon
Requires at least: 4.0 & WooCommerce 2.2+
Author: nazrulhassanmca
Tested up to: 4.2.2 & Woocommerce 2.3.9
Stable tag: 1.0.2
Version: 1.0.2
License: GPLv2

== Description ==

This plugin is an addon for WooCommerce to implement a payment gateway method for accepting **Credit Cards Payments** By merchants via **Authorize.Net** Gateway

This plugin uses AIM (Advance Integration Module) PHP SDK from Authorize.Net® Bundled with plugin
However there is <a href="https://wordpress.org/plugins/authorizenet-woocommerce-lightweight-addon/">another plugin</a> which is a light weight version of this plugin as it does not uses Authorize.Net® Bundled Libraries rather uses CURL to post data to Authorize.Net® Gateway.



= Features =
1. Very Simple Clean Code plugin to add a Authorize.Net payment method to woocommerce
2. No technical skills needed.
3. Prerequisite visualized on screenshots.
4. Adds Transaction ID, Authorization Code, Response Reason to Order Note.
5. Can be customized easily.
6. Bundles with <a href="http://developer.authorize.net/downloads">Official Authorize.Net® AIM Libraries</a>.
7. Can work with Sandbox/Live Authorize.Net accounts for testing purpose.
8. This plugin does not store **Credit Card Details**.
9. This plugin suppports Authorize or Authorize and Capture.
10. Feature to accept the type of card you like.
11. <a href="http://developer.authorize.net/faqs/#md5">MD5 Hash</a> not neccesary as this plugin uses AIM


== Screenshots ==

1. Screenshot-1 - Api Key Location 
2. Screenshot-2 - Admin Settings of Addon
3. Screenshot-3 - Checkout Page Form
4. Screenshot-4 - Showing an order received in admin
5. Screenshot-5 - Showing an Authorize.Net Details 

== Installation ==

1. Upload 'authorize.net-woocommerce-addon' folder to the '/wp-content/plugins/' directory
2. Activate 'Authorize.Net WooCommerce Addon' from wp plugin lists in admin area
3. Plugin will appear in settings of woocommerce
4. You can set the addon settings from wocommmerce->settings->Checkout->Authorize.Net Cards Settings


== Frequently Asked Questions ==

1. You need to have woocoommerce plugin installed to make this plugin work.
2. You need to follow Authorize.Net -> Accounts ->  	API Login ID and Transaction Key  in account to Obtain Api key & transaction key
3. This plugin works on test & live mode of Authorize.Net.
4. This plugin readily works on developmentment server.
5. This plugin does not requires SSL.
6. This plugin does not store Card Details anywhere.
7. You can check for Testing Card No <a href="http://developer.authorize.net/faqs/#testccnumbers">Here</a> 
8. This plugin requires CURL 
9. This plugin does not support Pre Order or Subscriptions 
10. This plugin does Support Refunds in woocommmerce interface
11. Learn more on <a href="http://developer.authorize.net/api/reference/starting_guide.html">Authorization Capture Settlement Void</a>
12. Error connecting to AuthorizeNet

This is a common known error you need to go to your plugins directory and then navigate to following file 
authorizenet-woocommerce-addon/lib/lib/shared/AuthorizeNetRequest.php
On Line no 14- Change public $VERIFY_PEER = true;  to public $VERIFY_PEER = false;

Other Resources That Might help

	1. http://curl.haxx.se/libcurl/c/CURLOPT_SSL_VERIFYPEER.html
	2. http://curl.haxx.se/libcurl/c/CURLOPT_SSL_VERIFYHOST.html

== Changelog ==

2015.05.29 - Version 1.0.2

	1.Added feature for Refunds(settled transaction) Void(capture unsettled transaction) in WooCommerce interface
	2.Added support to store more ordermeta like response_code,response_subcode,cavv_response etc
	3.Updated Official Authorize.Net Libraries and ssl pem.
	4.Fixed warning and Notices when plugin activated in WP debug mode	
	
2015.05.25 - Version 1.0.1

	1. Added Sending Billing & Shipping Address to Authorize.Net
	2. Added support to accept card types
	3. Added support for authorize or authorize & capture
	4. Added performance improvement and bugfixes
	5. Hightlighted the issue **Error connecting to AuthorizeNet** due to verify peer of CURL
	
2015.02.16 - Version 1.0.0

	1. First Release
	
== Upgrade Notice == 
There is no upgrade notice yet
