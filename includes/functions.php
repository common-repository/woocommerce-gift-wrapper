<?php
/**
 *
 * WooCommerce Gift Wrapper helper functions
 *
 */

/**
 * WCGWP() is used in the Plus version to instantiate the main class
 * We use it as a shim here to make seamless template updates between
 * the free and paid version of Gift Wrapper. Weird?
 *
 * @since  6.0.5
 * @return object|The_Gift_Wrapper
 *
 */
function WCGWP() {

	if ( class_exists( 'The_Gift_Wrapper', false ) ) {
		return The_Gift_Wrapper::instance();
	}

}

/**
 * Return array of product objects in correct gift wrap category
 *
 * @since 6.0.0
 * @return array
 */
if ( ! function_exists( 'wcgwp_get_products' ) ) {

	function wcgwp_get_products( $wrap_category_id = null ) {

		if ( ! $wrap_category_id ) {
			$wrap_category_id = wcgwp_get_wrap_category_id();
		}

		if ( ! $wrap_category_id ) {
			error_log( 'Gift Wrapper doesn\'t seem to have a gift wrap WooCommerce product category set.' );
			return [];
		}

		// Make sure category is a product category
		$category = get_term( $wrap_category_id, 'product_cat' );
		if ( ! $category || is_wp_error( $category ) ) {
			$wp_error = new WP_Error( 'wcgwp_setup', 'Gift Wrapper Plus is not set up properly.' );
			$wp_error->add( 'wcgwp_setup', 'Please choose a product category to represent your gift wrap options in the WCGWP settings.' );
			return [];
		}

		// Returns objects by default
		// https://github.com/woocommerce/woocommerce/wiki/wc_get_products-and-WC_Product_Query
		$args = apply_filters( 'wcgwp_post_args', [
			'category'         => array( $category->slug ), // array, accepts WC cat slug
			'status'           => 'publish', // Separate filter available in paid plugin version
			'stock_status'     => 'instock', // Separate filter available in paid plugin version
			'limit'            => '-1',
			'orderby'          => apply_filters( 'wcgwp_orderby', 'date' ),
			'order'            => apply_filters( 'wcgwp_order', 'DESC' ),
			'suppress_filters' => false, // accords for WPML, but also why not (TELL ME). Defaults to 'false' in WP_Query, 'true' in get_posts. We are using neither.
		], $category );
		$products = apply_filters( 'wcgwp_wrap_posts', wc_get_products( $args ) );

	// Provision for if no gift wrap products returned, log an error
	if ( empty( $products ) ) {
		error_log( 'Gift Wrapper didn\'t come up with any wrap products in wcgwp_get_products() get_posts. Review the arguments and maybe use the \'wcgwp_get_posts_args\' filter hook to help get results. The arguments are: ' . print_r( $args, true ) );
		return [];
	}
	return $products;

	}

}

/**
 * Get the WP category ID of Gift Wrapper products
 *
 * @since 6.0.0
 * @return int|bool
 */
function wcgwp_get_wrap_category_id() {

	$wrap_category_id = get_option( 'wcgwp_category_id' );

	// WPML compatibility
	$wrap_category_id = apply_filters( 'wpml_object_id', $wrap_category_id, 'product_cat', true );

	if ( empty( $wrap_category_id ) || 'none' === $wrap_category_id ) {
		return false;
	}
	return (int) $wrap_category_id;

}

/**
 * Is product in the gift wrap category (is it wrap)?
 * Mostly used on the admin-side
 *
 * @param object|int $product_id
 * @since 6.0.0
 * @return boolean
 */
function wcgwp_is_wrap( $product_id ) {

	if ( empty( $product_id ) ) {
		return false;
	} else if ( is_a( $product_id, 'WC_Product' ) ) {
		$product_id = $product_id->get_product_id();
	} else if ( isset( $product_id['data'] ) ) {
		$product_id = (int) $product_id['data']->get_id();
	} else {
		$product_id = (int) $product_id;
	}
	// We tried.
	if ( ! $product_id || ! is_int( $product_id ) ) {
		return false;
	}

	$wrap_category_id = wcgwp_get_wrap_category_id();

	if ( has_term( $wrap_category_id, 'product_cat', $product_id ) ) {
		return true;
	}
	return false;

}


/**
 * Discover if gift wrap product(s) are in cart
 *
 * @since 6.0.0
 * @return boolean
 */
if ( ! function_exists( 'wcgwp_wrap_in_cart' ) ) {

	function wcgwp_wrap_in_cart() {

		$cart_contents = WC()->cart->cart_contents;
		if ( isset( $cart_contents ) ) {
			foreach ( $cart_contents as $cart_item ) {
				if ( isset( $cart_item['wcgwp_source'] ) ) {
					return true;
				}
			}
		}

		return false;

	}

}

/**
 * Whether to show gift wrap product thumbnails...
 *
 * @since 6.0.0
 * @return bool
 */
function wcgwp_show_thumbs() {

	// General cart/checkout (order) wrap thumbs
	if ( 'no' === get_option( 'wcgwp_show_thumb', 'yes' ) ) {
		return false;
	}
	return true;

}

/**
 * Return count of wrap products in cart
 *
 * @since 6.0.1
 * @return int
 */
function wcgwp_count_wrap_in_cart() {

	$count = 0;
	$cart_contents = WC()->cart->get_cart_contents();
	if ( isset( $cart_contents ) ) {
		foreach ( $cart_contents as $cart_item ) {
			if ( isset( $cart_item['wcgwp_source'] ) ) {
				++ $count;
			}
		}
	}
	return $count;

}

/**
 * Check if the cart contains only virtual product (ignores wrap)
 *
 * @since 6.0.0
 * @return bool
 */
function wcgwp_cart_contains_virtual_products_only() {

	$cart_contents = WC()->cart->get_cart_contents();

	if ( ! $cart_contents ) {
		return false;
	}

	$all_virtual_products = false;
	$product_count = 0;
	$virtual_product_count = 0;

	foreach ( $cart_contents as $cart_item ) {

		if ( isset( $cart_item['wcgwp_source'] ) ) { // Skip over wrap products
			continue;
		}
		$product = $cart_item['data'];
		// Increment $has_virtual_product if product is virtual
		if ( $product->get_virtual() ) {
			$virtual_product_count += 1;
		} else {
			$product_count += 1;
		}

	}

	if ( $product_count <= $virtual_product_count ) {
		$all_virtual_products = true;
	}
	return apply_filters( 'wcgwp_virtual_products_only', $all_virtual_products );

}

/**
 * Basically, if cart only has wrap in it (which isn't wrappable), it's excluded from wrapping
 *
 * @since 6.0.0
 * @return bool
 */
function wcgwp_cart_excluded_from_wrap() {

	if ( wcgwp_count_wrap_products() === WC()->cart->get_cart_contents_count() ) {
		return true;
	}
	return false;

}

/**
 * Count wrap products in the cart (order wrapping)
 * If strict, we also count attribute wrap products (per product)
 *
 * @param $strict
 * @return int|mixed
 */
function wcgwp_count_wrap_products() {

	$cart_contents = WC()->cart->get_cart_contents();
	$count = 0;
	if ( $cart_contents ) {
		foreach ( $cart_contents as $cart_item ) {
			if ( isset( $cart_item['wcgwp_source'] ) ) {
				if ( $cart_item['quantity'] > 1 ) {
					$count += $cart_item['quantity'];
					continue;
				}
				++ $count;
			}

		}
	}
	return $count;

}