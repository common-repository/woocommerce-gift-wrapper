<?php

defined( 'ABSPATH' ) || exit;

class The_Gift_Wrapper_Wrapping {

	/**
	 * If add_giftwrap() has already run on template_redirect() hook
	 *
	 * @var boolean
	 */
	private $wrapped = false;

	/**
	 * Is there WCGWP wrap (cart item) product in cart?
	 *
	 * @var boolean
	 */
	public $wrap_in_cart = false;

	/**
	 * Constructor
	 */
	 public function __construct() {

		// No sense in continuing if plugin isn't set up
		if ( ! wcgwp_get_wrap_category_id() ) {
			return;
		}

		// Maybe delete orphan wrap and/or hide add to cart button after ajax remove_from_cart call
		add_action( 'wp_ajax_wcgwp_update_cart',                    [ $this, 'ajax_update_cart' ] );
		add_action( 'wp_ajax_nopriv_wcgwp_update_cart',             [ $this, 'ajax_update_cart' ] );

		// WooCommerce cart and checkout shortcodes are hooked to init, priority 10
		add_action( 'wp',                                           [ $this, 'wp' ], 15 );

		 if ( 'no' === get_option( 'wcgwp_lt6_templates', 'no' ) ) {
			/**
			 * AJAX wrap request from locations around the cart,
			 * as well as from inside the cart (within cart/line items)
			 */
			add_action( 'wp_ajax_wcgwp_ajax_wrap',                  [ $this, 'ajax_wrap' ] );
			add_action( 'wp_ajax_nopriv_wcgwp_ajax_wrap',           [ $this, 'ajax_wrap' ] );

			add_action( 'wp_ajax_wcgwp_ajax_remove_wrap',           [ $this, 'ajax_remove_wrap' ] );
			add_action( 'wp_ajax_nopriv_wcgwp_ajax_remove_wrap',    [ $this, 'ajax_remove_wrap' ] );

		} else {
			// Catch $_POST values for wrap requests, the old way
			add_action( 'template_redirect',                        [ $this, 'template_redirect' ] );

		}

		 // Load session data into Woo array
		add_filter( 'woocommerce_get_cart_item_from_session',       [ $this, 'woocommerce_get_cart_item_from_session' ], 20, 2 );

		// Just a quick check to see if wrap is in cart
		add_action( 'woocommerce_cart_loaded_from_session',         [ $this, 'check_cart_for_wrap' ], 10, 1 );

		 // maybe disable COD if gift wrap is in cart
		add_filter( 'woocommerce_available_payment_gateways',       [ $this, 'woocommerce_available_payment_gateways' ], 10, 1 );

		// If thumbnail links aren't desired, remove them from cart
		add_filter( 'woocommerce_cart_item_permalink',              [ $this, 'woocommerce_item_permalink' ], 10, 3 );

		// Unlink giftwrap item in order if desired
		add_filter( 'woocommerce_order_item_permalink',             [ $this, 'woocommerce_item_permalink' ], 10, 3 );

		// Add more item data to the item data array
		add_filter( 'woocommerce_get_item_data',                    [ $this, 'woocommerce_get_item_data' ], 10, 2 );

		// Don't allow more wrap than should be
		add_filter( 'woocommerce_cart_item_quantity',               [ $this, 'woocommerce_cart_item_quantity' ], 10, 3 );

		// Add class to table row in cart when it's giftwrap
		add_filter( 'woocommerce_cart_item_class',                  [ $this, 'woocommerce_cart_item_class' ], 11, 3 );

		// Filter the item meta display key, such as on order confirmation page, confirmation emails
		add_filter( 'woocommerce_order_item_display_meta_key',      [ $this, 'woocommerce_order_item_display_meta_key' ], 10, 3 );

		// Add line items to order - adjust item before saving to order
		// Fires inside class-wc-checkout.php line 422
		add_action( 'woocommerce_checkout_create_order_line_item',  [ $this, 'woocommerce_checkout_create_order_line_item' ], 10, 3 );

	}

	/**
	 * Hook into Woocommerce_remove_from_cart AJAX and
	 * maybe hide gift wrap prompt when appropriate
	 *
	 * @return void
	 */
	public function ajax_update_cart() {

		try {

			// Remove inappropriate gift wrap from cart
			$this->clean_up_cart();
			$cart_contents_count = WC()->cart->get_cart_contents_count();

			// If cart has wrap but no wrappables (e.g. parent removed), remove the wrap from cart
			wp_send_json_success(
				[
					'hide' =>
						0 === $cart_contents_count
						|| wcgwp_count_wrap_in_cart() === $cart_contents_count
						|| ( apply_filters( 'giftwrap_exclude_virtual_products', false ) && wcgwp_cart_contains_virtual_products_only() === true )
							? true : false,
				]
			);

		} catch ( Exception $e ) {
			wp_send_json_success( [ 'hide' => false ] );
		}

	}

	/**
	 * If the cart shouldn't have gift wrap sitting in it, clean that out
	 *
	 * @since 6.0.0
	 * @return void
	 */
	public function clean_up_cart() {

		if ( wcgwp_cart_excluded_from_wrap()
			|| WC()->cart->get_cart_contents_count() === 0
			|| ( apply_filters( 'giftwrap_exclude_virtual_products', false ) && wcgwp_cart_contains_virtual_products_only() === true )
		) {
			// Clean up cart
			foreach ( WC()->cart->get_cart_contents() as $cart_item_key => $cart_item ) {
				// Is this $cart_item in loop a WCGWP product?
				if ( isset( $cart_item['wcgwp_source'] ) ) {
					WC()->cart->remove_cart_item( $cart_item_key );
				}
			}
		}

	}

	/**
	 * Establish if wrap in cart on page load
	 *
	 * @return void
	 */
	public function check_cart_for_wrap( $cart ) {

		$this->wrap_in_cart = wcgwp_wrap_in_cart();

	}

	/**
	 * Hooked into `wp` action to catch WooCommerce cart/checkout action hooks
	 *
	 * @return void
	 */
	 public function wp() {

		 // Leave if admin, not the right WC endpoint, or Gutenberg
		if ( is_admin()
			|| ( ! is_cart() && ! is_checkout() )
			|| defined( 'REST_REQUEST' ) && REST_REQUEST
		) {
			return;
		}

		$this->clean_up_cart();

		$hooks = (array) get_option( 'wcgwp_cart_hook', [] );

		if ( in_array( "woocommerce_before_cart", $hooks ) ) {
			add_action( 'woocommerce_before_cart', [ $this, 'before_cart' ] );
		}
		if ( in_array( "woocommerce_before_cart_collaterals", $hooks ) ) {
			add_action( 'woocommerce_before_cart_collaterals', [ $this, 'before_cart_collaterals' ] );
		}
		if ( in_array( "woocommerce_after_cart", $hooks ) ) {
			add_action( 'woocommerce_after_cart', [ $this, 'after_cart' ] );
		}
		if ( in_array( "woocommerce_before_checkout_form", $hooks ) ) {
			add_action( 'woocommerce_before_checkout_form', [ $this, 'before_checkout_form' ] );
		}
		if ( in_array( "woocommerce_after_checkout_form", $hooks ) ) {
			add_action( 'woocommerce_after_checkout_form', [ $this, 'after_checkout_form' ] );
		}

	}


	/**
	 * Show a message when wrap has been added to the cart
	 *
	 * @param int $product_id
	 * @since 6.0.0
	 * @return void
	 */
	private function wrap_added_message( $product_id ) {

		WC()->session->set( 'wc_notices', [] );
		$message = WC_Gift_Wrap()->strings->get_string( 'gift_wrap_added' );
		// $message = __( 'Gift wrap was added to your cart.', 'woocommerce-gift-wrapper' );
		$message = apply_filters( 'wcgwp_wrap_added_message', $message, $product_id );
		wc_add_notice( $message, apply_filters( 'woocommerce_add_to_cart_notice_type', 'success' ) );

	}

	/**
	 * Show a message when customer not allowed to add another specific product to cart
	 *
	 * @param object $product
	 * @since 6.0.0
	 * @return void
	 */
	private function duplicate_message( $product ) {

		if ( is_numeric( $product ) ) {
			$product = wc_get_product( $product );
		}
		$message = sprintf( __( 'You cannot add another "%s" to your cart.', 'woocommerce' ), $product->get_name() );
		$message = apply_filters( 'woocommerce_cart_product_cannot_add_another_message', $message, $product );
		wc_add_notice( $message, apply_filters( 'wcgwp_duplicate_product_notice_type', 'error' ) );

	}

	/**
	 * AJAX maybe add wrap
	 * when a peri-cart/checkout wrap prompts is clicked
	 *
	 * Uses WC add_to_cart method to add giftwrap to order from peri-cart/checkout and cart item prompts
	 * Peri-cart wrap is not attribute wrap; it is its own WC product
	 *
	 * @return void
	 * @since 6.0.0
	 * @throws Exception Could throw an exception to prevent adding to cart
	 */
	public function ajax_wrap() {

		// Quick security check
		if ( ! check_ajax_referer( 'wcgwp_ajax_wrap', 'nonce' ) ) {
			wp_send_json_error( [ 'message' => 'Invalid nonce' ] );
		}

		$product_id = $_POST['product_id'];

		if ( empty( $product_id ) ) {
			wp_send_json_error( [ 'message' => __( 'No valid WC product ID found', 'woocommerce-gift-wrapper' ) ] );
		}

		// For counting wrap already inside cart
		$count = 0;

		// Number of wrap product(s) added to cart; default 1
		$quantity = apply_filters( 'wcgwp_add_to_cart_quantity', 1, $_POST );

		// Check if allowed more than one gift wrap to cart
		$allow_multiple = get_option( 'wcgwp_number', 'no' );

		// Get the cart contents array
		$cart_contents = WC()->cart->get_cart();

		/**
		 * There's maybe more in the cart than there are wrap products
		 */
		foreach ( $cart_contents as $cart_item_key => $cart_item ) {

			$product = $cart_item['data'];
			// For products sold individually
			if ( $cart_item['product_id'] == $product_id && $product->is_sold_individually() ) {
				// Can't add another to cart message, and return AJAX
				$this->duplicate_message( $product );
				wp_send_json_error( [ 'message' => 'wcgwp-quantity' ] );

			}

			// Is this $cart_item in loop a WCGWP product?
			if ( isset( $cart_item[ 'wcgwp_source' ] ) ) {

				++$count;
				if ( $cart_item[ 'quantity' ] > 1 ) {
					$count += $cart_item[ 'quantity' ];
				}

				// When only one allowed or at max, give shopper option to replace what is already in cart
				if ( 'no' === $allow_multiple && $count > 0 ) {
					if ( 'yes' === get_option( 'wcgwp_lt6_templates', 'no' ) ) {
						// When only one allowed, give shopper option (JS) to replace what is already in cart
						WC()->cart->remove_cart_item( $cart_item_key );
					} else {
						$this->limit_message( 1 );
						wp_send_json_error( [ 'message' => 'wcgwp-quantity' ]);
					}
				}

			}

		} // end foreach $cart_contents

		// Add WCGWP note cart item data if relevant
		$note = null;
		if ( ! empty( $_POST['note'] ) ) {
			$note = sanitize_text_field( stripslashes( $_POST['note'] ) );
		}
		// Initialize some WCGWP cart item data
		$cart_item_data = [
			'wcgwp_source'  => 'peri-cart',
			'wcgwp_note'    => $note
		];

		try {
			do_action( 'wcgwp_before_add_to_cart', $cart_contents, $product_id, $note );
			if ( 0 < $quantity ) {
				$cart_item_key = WC()->cart->add_to_cart( (int) $product_id, $quantity, null, [], $cart_item_data );
				do_action( 'woocommerce_ajax_added_to_cart', $product_id );
				$this->wrap_added_message( $product_id );
			}
			do_action( 'wcgwp_after_add_to_cart', $cart_contents, $product_id, $note );
		} catch ( Exception $e ) {
			error_log( 'Gift Wrapper failed adding wrap to order. More info: ' . $e->getMessage() );
			wp_send_json_error( [ 'message' => 'Adding wrap failed. More info: ' . $e->getMessage() ] );
		}

		wp_send_json_success( [ 'cart_item_key' => $cart_item_key, 'cart_hash' => WC()->cart->get_cart_hash() ] );

	}

	/**
	 * AJAX remove peri-cart wrap when peri-cart checkbox un-checked
	 *
	 * @return void
	 * @throws Exception Could throw an exception to prevent adding to cart
	 */
	public function ajax_remove_wrap() {

		// Quick security check
		if ( ! check_ajax_referer( 'wcgwp_ajax_wrap', 'nonce' ) ) {
			wp_send_json_error( [ 'message' => 'Invalid nonce' ] );
		}

		if ( empty( $_POST['cart_item_key'] ) ) {
			wp_send_json_error( [ 'message' => 'Missing cart item key' ] );
		}
		$cart_item_key = $_POST['cart_item_key'];

		$cart = WC()->cart->get_cart();
		if ( ! isset( $cart[ $cart_item_key ] ) ) {
			// Item isn't in the cart to remove
			wp_send_json_error( [ 'message' => 'Missing cart item key' ] );
		}

		WC()->cart->remove_cart_item( $cart_item_key );
		WC()->cart->set_session();
		wp_send_json_success();

	}

	/**
	 * Show a message when customer has hit max WCGWP product in cart
	 *
	 * @param integer $limit
	 * @since 6.0.0
	 * @return void
	 */
	public function limit_message( $limit ) {

		$string = strtolower( WC_Gift_Wrap()->strings->get_string( 'wrap' ) );
		if ( 1 < $limit ) {
			$string .= 's';
		}

		$message = sprintf( __( 'You can only add %s %s to your cart.', 'woocommerce' ), $limit, $string );
		$message = apply_filters( 'woocommerce_cart_limit_message', $message, $limit, $string );
		wc_add_notice( $message, apply_filters( 'woocommerce_cart_item_removed_notice_type', 'error' ) );

	}

	/**
	 * Hooked into `template_redirect` action to add gift wrap to cart
	 *
	 * @return void
	 * @deprecated 6.0.0
	 */
	public function template_redirect() {

		if ( ! is_checkout() && ! is_cart() ) {
			return;
		}

		if ( ! apply_filters( 'wcgwp_add_giftwrap_to_order', true ) ) {
			return;
		}

		// Check $this->wrapped to avoid loop
		if ( ! $this->wrapped ) {
			$this->add_giftwrap_to_order();
			$this->wrapped = true;
		}

	}


	/**
	 * Check Gift Wrapper form nonce before continuing
	 *
	 * @deprecated 6.0.0
	 * @return void
	 */
	private function check_nonce() {

		// Check nonce
		if ( isset( $_POST['wcgwp_order_wrap_nonce'] ) && wp_verify_nonce( $_POST['wcgwp_order_wrap_nonce'], 'wcgwp_order_wrap' ) ) {
			return;
		}
		error_log( 'Your Gift Wrapper plugin cart/checkout template override is missing a nonce. Please update your template(s).' );
		wp_die( 'Bad nonce' );

	}

	/**
	 * Redirect to cart or checkout page as appropriate
	 *
	 * @deprecated 6.0.0
	 * @return void
	 */
	private function redirect() {

		// POST/REDIRECT/GET to prevent wrap from showing back up after delete + refresh
		if ( isset( $_POST['wcgwp_submit_before_cart'] ) || isset( $_POST['wcgwp_submit_coupon'] ) || isset( $_POST['wcgwp_submit_after_cart'] ) ) {
			$this->wrapped = true;
			wp_safe_redirect( wc_get_cart_url(), 303 );
			exit; // not die() because inside hook
		}

		if ( isset( $_POST['wcgwp_submit_checkout'] ) || isset( $_POST['wcgwp_submit_after_checkout'] ) ) {
			$this->wrapped = true;
			wp_safe_redirect( wc_get_checkout_url(), 303 );
			exit;
		}

	}

	/**
	 * Use WC add_to_cart method to add cart/checkout wrap to order
	 *
	 * @param  int       $product_id
	 * @param  array     $cart_item_data
	 * @deprecated 6.0.0
	 * @return void
	 * @throws Exception
	 */
	public function add_giftwrap( $product_id, $cart_item_data = [] ) {

		// Allow more than one gift wrap to cart
		$allow_multiple = get_option( 'wcgwp_number', 'no' );

		if ( $this->wrap_in_cart ) {

			$product = wc_get_product( $product_id );

			if ( ! $product ) {
				return;
			}

			// For products sold individually
			if ( $product->is_sold_individually() ) {
				$cart = WC()->cart->get_cart_contents();
				foreach ( $cart as $cart_item ) {
					// Selected gift item is already in cart:
					if ( isset( $cart_item['product_id'] ) && $cart_item['product_id'] === $product_id ) {
						$this->redirect();
					}
				}
			}

			if ( 'no' === $allow_multiple ) {

				// Crude but if wrap in cart and shouldn't overlap, remove existing.
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					if ( isset( $cart_item['wcgwp_source'] ) ) {
						WC()->cart->remove_cart_item( $cart_item_key );
					}
				}
			}
		}

		WC()->cart->add_to_cart( $product_id, 1, 0, [], $cart_item_data );

	}

	/**
	 * Add gift wrapping to cart
	 *
	 * @return void
	 * @deprecated 6.0.0
	 * @throws Exception
	 */
	 public function add_giftwrap_to_order() {

		 $cart_item_data = [ 'wcgwp_source' => 'peri-cart' ];

		 if ( isset( $_POST['wcgwp_submit_before_cart'] ) ) {
			$this->check_nonce();
			$product_id = isset( $_POST['wcgwp_product_before_cart'] ) ? (int) $_POST['wcgwp_product_before_cart'] : false;
			if ( ! $product_id ) {
				return;
			}
			if ( isset( $_POST['wcgwp_note_before_cart'] ) && '' !== $_POST['wcgwp_note_before_cart'] ) {
				$cart_item_data['wcgwp_note'] = wc_sanitize_textarea( stripslashes( $_POST['wcgwp_note_before_cart'] ) );
			}
			$this->add_giftwrap( $product_id, $cart_item_data );
		}

		if ( isset( $_POST['wcgwp_submit_coupon'] ) ) {
			$this->check_nonce();
			$product_id = isset( $_POST['wcgwp_product_coupon'] ) ? (int) $_POST['wcgwp_product_coupon'] : false;
			if ( ! $product_id ) {
				return;
			}
			if ( isset( $_POST['wcgwp_note_coupon'] ) && '' !== $_POST['wcgwp_note_coupon'] ) {
				$cart_item_data['wcgwp_note'] = wc_sanitize_textarea( stripslashes( $_POST['wcgwp_note_coupon'] ) );
			}
			$this->add_giftwrap( $product_id, $cart_item_data );
		}

		if ( isset( $_POST['wcgwp_submit_after_cart'] ) ) {
			$this->check_nonce();
			$product_id = isset( $_POST['wcgwp_product_after_cart'] ) ? (int) $_POST['wcgwp_product_after_cart'] : false;
			if ( ! $product_id ) {
				return;
			}
			if ( isset( $_POST['wcgwp_note_after_cart'] ) && '' !== $_POST['wcgwp_note_after_cart'] ) {
				$cart_item_data['wcgwp_note'] = wc_sanitize_textarea( stripslashes( $_POST['wcgwp_note_after_cart'] ) );
			}
			$this->add_giftwrap( $product_id, $cart_item_data );
		}

		if ( isset( $_POST['wcgwp_submit_checkout'] ) ) {
			$this->check_nonce();
			$product_id = isset( $_POST['wcgwp_product_checkout'] ) ? (int) wc_clean( $_POST['wcgwp_product_checkout'] ) : false;
			if ( ! $product_id ) {
				return;
			}
			if ( isset( $_POST['wcgwp_note_checkout'] ) && '' !== $_POST['wcgwp_note_checkout'] ) {
				$cart_item_data['wcgwp_note'] = wc_sanitize_textarea( stripslashes( $_POST['wcgwp_note_checkout'] ) );
			}
			$this->add_giftwrap( $product_id, $cart_item_data );
		}

		if ( isset( $_POST['wcgwp_submit_after_checkout'] ) ) {
			$this->check_nonce();
			$product_id = isset( $_POST['wcgwp_product_after_checkout'] ) ? (int) $_POST['wcgwp_product_after_checkout'] : false;
			if ( ! $product_id ) {
				return;
			}
			if ( isset( $_POST['wcgwp_note_after_checkout'] ) && '' !== $_POST['wcgwp_note_after_checkout'] ) {
				$cart_item_data['wcgwp_note'] = wc_sanitize_textarea( stripslashes( $_POST['wcgwp_note_after_checkout'] ) );
			}
			$this->add_giftwrap( $product_id, $cart_item_data );
		}

		$this->redirect();

	}

	/**
	 * Assign cart item values from session
	 *
	 * @param  array $cart_item Cart item data.
	 * @param  array $values    Cart item values.
	 * @return array
	 */
	 public function woocommerce_get_cart_item_from_session( $cart_item, $values ) {

		// Cart/checkout hooked general gift wrapping
		if ( isset( $values['wcgwp_note'] ) ) {
			$cart_item['wcgwp_note'] = $values['wcgwp_note'];
		}
		if ( isset( $values['wcgwp_source'] ) ) {
			$cart_item['wcgwp_source'] = $values['wcgwp_source'];
		}
		return $cart_item;

	}

	/**
	 * Wrapper function for gift_wrap_action()
	 *
	 * @return void
	 */
	public function before_cart() {

		$this->gift_wrap_action( '_before_cart' );

	}

	/**
	 * Wrapper function for gift_wrap_action()
	 *
	 * @return void
	 */
	public function before_cart_collaterals() {

		$this->gift_wrap_action( '_coupon' );

	}

	/**
	 * Wrapper function for gift_wrap_action()
	 *
	 * @return void
	 */
	public function after_cart() {

		$this->gift_wrap_action( '_after_cart' );

	}

	/**
	 * Wrapper function for gift_wrap_action()
	 *
	 * @return void
	 */
	public function before_checkout_form() {

		$this->gift_wrap_action( '_checkout' );

	}

	/**
	 * Wrapper function for gift_wrap_action()
	 *
	 * @return void
	 */
	public function after_checkout_form() {

		$this->gift_wrap_action( '_after_checkout' );

	}

	/**
	 * Wrapper function for gift_wrap_action()
	 * Allows for a custom wrap location
	 *
	 * @param string $label
	 * @return void
	 */
	public function custom_wrap_location( string $label = '' ) {

		$this->gift_wrap_action( $label );

	}

	/**
	 * Add gift wrap options to cart/checkout action hooks
	 *
	 * @param string $label
	 * @return void
	 */
	public function gift_wrap_action( $label ) {

		if ( WC()->cart->get_cart_contents_count() === 0
			|| ( apply_filters( 'giftwrap_exclude_virtual_products', false ) && wcgwp_cart_contains_virtual_products_only() === true )
		) {
			return;
		}

		$products = wcgwp_get_products();

		if ( count( $products ) < 1 ) {
			return;
		}

		if ( ! apply_filters( 'wcgwp_continue_gift_wrap_action', true, $products, $label ) ) {
			return;
		}

		$giftwrap_details = wc_sanitize_textarea( get_option( 'wcgwp_details' ) );
		?>

		<div class="wc-giftwrap giftwrap<?php esc_attr_e( $label ); ?> <?php esc_attr_e( $this->extra_class() ); ?>">

			<?php
			$v6 = 'no' === get_option( 'wcgwp_lt6_templates', 'no' ) ? 'v6/' : '';

			$display = get_option( 'wcgwp_cart_display', 'modal' );
			// If modal version
			if ( 'modal' === $display ) {
				if ( empty( $v6 ) ) {
					wc_get_template( 'wcgwp/modal.php', array(
							'label'            => $label,
							'list'             => $products,
							'giftwrap_details' => $giftwrap_details,
							'show_thumbs'      => wcgwp_show_thumbs()
					), '', GIFTWRAPPER_PLUGIN_DIR . 'templates/' );
				} else {
					wc_get_template( 'wcgwp/v6/peri-cart/modal.php', array(
						'label'         => $label,
						'products'      => $products,
						'show_thumbs'   => wcgwp_show_thumbs()
					), '', GIFTWRAPPER_PLUGIN_DIR . 'templates/' );
				}

			// Non-modal (slideToggle) version
			} else if ( 'slide' === $display ) {

				if ( empty( $v6 ) ) {
					// New template since version 4.4, old was wcgwp/giftwrap-list-cart.php (deprecated)
					wc_get_template( 'wcgwp/giftwrap-list.php', array(
						'label'             => $label,
						'list'              => $products,
						'giftwrap_details'  => $giftwrap_details,
						'show_thumbs'       => wcgwp_show_thumbs()
					), '', GIFTWRAPPER_PLUGIN_DIR . 'templates/' );
				} else {
					wc_get_template( 'wcgwp/v6/peri-cart/slide.php', array(
						'label'         => $label,
						'products'      => $products,
						'show_thumbs'   => wcgwp_show_thumbs()
					), '', GIFTWRAPPER_PLUGIN_DIR . 'templates/' );
				}

			} else {

				wc_get_template( 'wcgwp/v6/peri-cart/checkbox.php', array(
					'label'         => $label,
					'products'      => $products
				), '', GIFTWRAPPER_PLUGIN_DIR . 'templates/' );

			} ?>
		</div>

	<?php

	}

	/**
	 * Check if the cart contains only virtual product
	 *
	 * @deprecated 6.0.0
	 * @return bool
	 */
	 public function cart_virtual_products_only() {

		return wcgwp_cart_contains_virtual_products_only();

	}

	/**
	 * Maybe disable payment gateways if gift wrap in cart
	 *
	 * @param  array      $gateways
	 * @return null|array
	 */
	 public function woocommerce_available_payment_gateways( $gateways ) {

		if ( ! $this->wrap_in_cart ) {
			return $gateways;
		}
		if ( apply_filters( 'wcgwp_remove_cod_gateway', false ) ) {
			if ( isset( $gateways['cod'] ) ) {
				unset( $gateways['cod'] );
			}
		}
		return apply_filters( 'wcgwp_change_gateways', $gateways );

	}

	/**
	 * Unlink giftwrap item (remove link to product) in order if desired
	 *
	 * @param string $link  Order item link, whether URL or blank
	 * @param object $cart_item  Order item
	 * @param object $order Order
	 * @return string
	 */
	 public function woocommerce_item_permalink( $link, $cart_item, $order ) {

		// Exit right away if we're not dealing with wrap
		if ( ! isset( $cart_item['wcgwp_source'] ) ) {
			return $link;
		}
		if ( 'no' === get_option( 'wcgwp_link', 'yes' ) ) {
			$link = '';
		}
		return apply_filters( 'wcgwp_filter_link_in_cart', $link, $cart_item );

	}

	/**
	 * Display user's note in cart itemization
	 *
	 * @param array $item_data
	 * @param array $cart_item
	 * @return array
	 */
	 public function woocommerce_get_item_data( $item_data, $cart_item ) {

		// Cart/checkout hooked general gift wrapping
		if ( ! isset( $cart_item['wcgwp_note'] ) ) {
			return $item_data;
		}
		$note = $cart_item['wcgwp_note'];

		$item_data[] = array(
			'key'   => __( 'Note', 'woocommerce-gift-wrapper' ),
			'value' => $note,
		);
		return $item_data;

	}

	/**
	 * Alter cart input 'max' attributes to prevent user from updating cart despite gift wrap limits
	 *
	 * @param string $product_quantity
	 * @param int $cart_item_key
	 * @param object $cart_item
	 *
	 * @return string
	 */
	public function woocommerce_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {

		if ( ! isset( $cart_item['wcgwp_source'] ) ) {
			return $product_quantity;
		}
		$wcgwp_number = get_option( 'wcgwp_number', 'no' );
		if ( 'no' === $wcgwp_number ) {
			$product_quantity = preg_replace( '/max="[\d]*"/', 'max="1"', $product_quantity );
		}

		return $product_quantity;

	}

	/**
	 * Alter CSS class names of wrapping cart items <tr> in cart table
	 *
	 * @param  string  $class
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function woocommerce_cart_item_class( $class, $cart_item, $cart_item_key ) {

		if ( isset( $cart_item['wcgwp_source'] ) ) {
			$class .= ' wcgwp-wrap-product ';
		}
		return $class;

	}

	/**
	 * Filter the item meta display key, such as on order confirmation page
	 *
	 * @param  string $display_key    Display key
	 * @param  object $meta           WC_Meta_Data
	 * @param  object $order_item     WC_Order_Item_Product
	 * @return string
	 */
	 public function woocommerce_order_item_display_meta_key( $display_key, $meta, $order_item ) {

		if ( 'wcgwp_note' === $display_key ) {
			$display_key = str_replace( 'wcgwp_note', __( 'Note', 'woocommerce-gift-wrapper' ), $display_key );
		}
		return $display_key;

	}

	/**
	 * Include add-ons line item meta
	 *
	 * @param  object $item           WC_Order_Item_Product
	 * @param  string $cart_item_key  Cart item key
	 * @param  array  $values         Order item values
	 * @return object
	 */
	 public function woocommerce_checkout_create_order_line_item( $item, $cart_item_key, $values ) {

		if ( isset( $values['wcgwp_note'] ) ) {
			$item->add_meta_data( 'wcgwp_note', $values['wcgwp_note'] );
		}
		return $item;

	}

	/**
	 * Return array of products in gift wrap category
	 *
	 * @return null|array
	 * @deprecated 6.0.0
	 */
	public function get_products() {

		return wcgwp_get_products();

	}

	/**
	 * Check if the cart is excluded from wrap offering
	 *
	 * @return bool
	 * @deprecated 6.0.0
	 */
	public function cart_excluded_from_wrap() {

		return wcgwp_cart_excluded_from_wrap();

	}

	/**
	 * Is product gift wrap?
	 *
	 * @param  int|object $product (might be cart item)
	 * @return bool
	 * @deprecated 6.0.0
	 */
	public function is_wrap( $product_id ) {

		return wcgwp_is_wrap( $product_id );

	}

	/**
	 * Add conditional classes to giftwrap wrapper div
	 *
	 * @since 6.0.0
	 * @return string
	 */
	protected function extra_class() {

		$extra_class = '';
		if ( ! WC_Gift_Wrap()->wrapping->wrap_in_cart || ( WC_Gift_Wrap()->wrapping->wrap_in_cart && 'yes' === get_option( 'wcgwp_number', 'no' ) ) ) {
			$extra_class = ' wcgwp_could_giftwrap';
		}
		return apply_filters( 'wcgwp_extra_wrapper_class', $extra_class );

	}

	/**
	 * Whether to show gift wrap product thumbnails...
	 *
	 * @return bool
	 * @deprecated 6.0.0
	 */
	public function show_thumbs() {

		return wcgwp_show_thumbs();

	}

}