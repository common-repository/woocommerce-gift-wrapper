<?php
/**
 * Plugin Name: Gift Wrapper
 * Plugin URI: https://www.giftwrapper.app
 * Description: Offer gift wrap options on WooCommerce cart and/or checkout pages. Let customers wrap their orders!
 * Version: 6.1.10
 * WC requires at least: 5.6
 * WC tested up to: 9.3
 * Author: Little Package
 * Author URI: https://www.giftwrapper.app
 * Donate link: https://paypal.me/littlepackage
 * Text Domain: woocommerce-gift-wrapper
 * Domain Path: /lang
 *
 * Gift Wrapper - for WooCommerce - since 2014
 * Copyright: (c) 2014-2024 Little Package
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * This plugin is free. If you have problems with it, please be
 * nice and contact me for help before leaving negative feedback.
 * Together we can make Wordpress better. Thank you.
 *
 * Wordpress developers need your support & encouragement!
 * If you have found this plugin useful,
 * and especially if you benefit commercially from it,
 * please donate a few dollars to support my work & this plugin's future:
 *
 * https://paypal.me/littlepackage
 *
 * I understand you have a budget and might not be able to afford
 * to send the developer (me) a small tip in thanks.
 * Maybe you can leave a positive review?
 *
 * https://wordpress.org/support/plugin/woocommerce-gift-wrapper/reviews
 *
 * Thank you!
 *
 */
defined( 'ABSPATH' ) || exit;

if ( ! defined( 'GIFTWRAPPER_PLUGIN_FILE' ) ) {
	define( 'GIFTWRAPPER_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'GIFTWRAPPER_VERSION' ) ) {
	define( 'GIFTWRAPPER_VERSION', '6.1.10' );
}

if ( function_exists('is_plugin_active') && is_plugin_active( 'woocommerce-gift-wrapper-plus/woocommerce-gift-wrapper-plus.php' ) ) {
	wp_die( 'Before activating Gift Wrapper, please deactivate the Plus (paid) version. You can use one or the other, but not both.', 'Gift Overload', [ 'back_link' => true ] );
}

/**
 * Functions used by plugins
 */
if ( ! class_exists( 'WC_Dependencies' ) ) {
	require_once 'woo-includes/class-wc-dependencies.php';
}

/**
 * WooCommerce Detection
 *
 * @return boolean
 */
if ( ! function_exists( 'is_woocommerce_active' ) ) {
	function is_woocommerce_active() {
		return WC_Dependencies::woocommerce_active_check();
	}
}

/**
 * Ensure that WooCommerce plugin is active
 *
 * @return void
 */
if ( ! is_woocommerce_active() ) {
	add_action( 'admin_notices', 'wcgw_woocommerce_inactive_notice' );
	return;
}

/**
 * Declare compatibility with HPOS
 * @return void
 */
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, false );
	}
} );

/**
 * Include the main Gift Wrapper class
 *
 * @return void
 */
if ( ! class_exists( 'The_Gift_Wrapper', false ) ) {
	try {
		include_once 'includes/class-gift-wrapper.php';
	} catch ( Exception $e ) {
		deactivate_plugins( 'woocommerce-gift-wrapper/woocommerce-gift-wrapper.php' );
	}
}

/**
 * Return the main instance of Gift Wrapper for WooCommerce
 *
 * @since  5.2.3
 * @return object|The_Gift_Wrapper
 *
 */
function WC_Gift_Wrap() {
	if ( class_exists( 'The_Gift_Wrapper', false ) ) {
		return The_Gift_Wrapper::instance();
	}
}

WC_Gift_Wrap();

/**
 * Stating the obvious. Can't have Gift Wrapper without WooCommerce
 *
 * @return void
 */
function wcgw_woocommerce_inactive_notice() { ?>

	<div id="message" class="notice notice-error is-dismissible">
		<p><?php esc_html_e( 'The WooCommerce plugin must be active in order to activate and use WooCommerce Gift Wrapper.', 'woocommerce-gift-wrapper' ); ?></p>
	</div>

	<?php
	deactivate_plugins( 'woocommerce-gift-wrapper/woocommerce-gift-wrapper.php' );

}