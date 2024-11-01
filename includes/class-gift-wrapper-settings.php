<?php defined( 'ABSPATH' ) || exit;

class The_Gift_Wrapper_Settings {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Settings link on the plugins listing page
		add_filter( 'plugin_action_links_' . GIFTWRAPPER_PLUGIN_BASE_FILE, [ $this, 'plugin_action_links' ], 10, 1 );

		// Add a tab to the WooCommerce settings page
		add_filter( 'woocommerce_get_settings_pages', [ $this, 'get_settings_pages' ], 10, 1 );

	}

	/**
	 * Add settings link to WP plugin listing
	 *
	 * @param  array $links
	 * @return array
	 */
	public function plugin_action_links( $links ) {

		$wcgwp_links = array(
			sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=wc-settings&tab=gift-wrapper' ), esc_html__( 'Settings', 'woocommerce-gift-wrapper' ) ),
			sprintf( '<a href="%s" target="_blank" rel="noopener">%s</a>', 'https://paypal.me/littlepackage?country.x=US&locale.x=en_US', esc_html__( 'Donate', 'woocommerce-gift-wrapper' ) ),
		);
		return array_merge( $links, $wcgwp_links );

	}

	/**
	 * Get the settings tab (and sections) going
	 *
	 * @param  array $settings
	 * @return array
	 */
	public function get_settings_pages( $settings ) {

		$settings[] = include 'class-gift-wrapper-settings-page.php';
		return $settings;

	}

	/**
	 * Get array of WooCommerce product categories
	 *
	 * @return array
	 */
	public function product_cats() {

		$wrap_cat_id = wcgwp_get_wrap_category_id();

		$selection = [];
		$args = array(
			'taxonomy'      => 'product_cat',
			'orderby'       => 'id',
			'hide_empty'    => '0',
		);

		$categories = get_categories( $args );
		$categories = isset( $categories ) ? $categories : [];

		if ( ! empty( $categories ) ) {
			$selection['none'] = __( 'None selected', 'woocommerce-gift-wrapper' );
			foreach ( $categories as $category ) {
				$selection[ $category->term_id ] = $category->name;
			}
		} else {
			$selection['none'] = __( 'None set up yet', 'woocommerce-gift-wrapper' );
		}

		return $selection;

	}

	/**
	 * Get possible theme placements. Some themes do CSS/JS acrobatics with WC templates
	 * and should not get all the placements
	 *
	 * @since 5.0
	 * @return array
	 */
	public function get_placements() {

		$placements = [
			'none'                                  => __( 'None', 'woocommerce-gift-wrapper' ),
			'woocommerce_before_cart'               => __( 'Before cart', 'woocommerce-gift-wrapper' ),
			'woocommerce_before_cart_collaterals'   => __( 'Before cart collaterals', 'woocommerce-gift-wrapper' ),
			'woocommerce_after_cart'                => __( 'After cart', 'woocommerce-gift-wrapper' ),
			'woocommerce_before_checkout_form'      => __( 'Before checkout', 'woocommerce-gift-wrapper' ),
			'woocommerce_after_checkout_form'       => __( 'After checkout', 'woocommerce-gift-wrapper' ),
		];
		// Some themes do not accommodate this placement at all. Let's not lead people on...
		if ( in_array( get_option( 'current_theme' ), array( 'Basel', 'Bridge', 'Flatsome', 'Martfury', 'Woodmart' ) ) ) {
			unset( $placements['woocommerce_before_cart_collaterals'] );
		}
		return $placements;

	}

	/**
	 * Returns an array of all possible CSS animations for modal entry/exit
	 *
	 * @return array
	 */
	public function get_animations() {

		return array(
			'attention_seekers' => array(
				'none'                  => 'none',
				'bounce'                => 'bounce',
				'flash'                 => 'flash',
				'pulse'                 => 'pulse',
				'rubberBand'            => 'rubberBand',
				'shake'                 => 'shake',
				'swing'                 => 'swing',
				'tada'                  => 'tada',
				'wobble'                => 'wobble',
				'jello'                 => 'jello',
			),
			'in' => array(
				'backInDown'            => 'backInDown',
				'backInLeft'            => 'backInLeft',
				'backInRight'           => 'backInRight',
				'backInUp'              => 'backInUp',
				'bounceIn'              => 'bounceIn',
				'bounceInDown'          => 'bounceInDown',
				'bounceInLeft'          => 'bounceInLeft',
				'bounceInRight'         => 'bounceInRight',
				'bounceInUp'            => 'bounceInUp',
				'fadeIn'                => 'fadeIn',
				'fadeInDown'            => 'fadeInDown',
				'fadeInDownBig'         => 'fadeInDownBig',
				'fadeInLeft'            => 'fadeInLeft',
				'fadeInLeftBig'         => 'fadeInLeftBig',
				'fadeInRight'           => 'fadeInRight',
				'fadeInRightBig'        => 'fadeInRightBig',
				'fadeInUp'              => 'fadeInUp',
				'fadeInUpBig'           => 'fadeInUpBig',
				'flipInX'               => 'flipInX',
				'flipInY'               => 'flipInY',
				// 'lightSpeedIn'          => 'lightSpeedIn',
				'rotateIn'              => 'rotateIn',
				'rotateInDownLeft'      => 'rotateInDownLeft',
				'rotateInDownRight'     => 'rotateInDownRight',
				'rotateInUpLeft'        => 'rotateInUpLeft',
				'rotateInUpRight'       => 'rotateInUpRight',
				'slideInUp'             => 'slideInUp',
				'slideInDown'           => 'slideInDown',
				'slideInLeft'           => 'slideInLeft',
				'slideInRight'          => 'slideInRight',
				'zoomIn'                => 'zoomIn',
				'zoomInDown'            => 'zoomInDown',
				'zoomInLeft'            => 'zoomInLeft',
				'zoomInRight'           => 'zoomInRight',
				'zoomInUp'              => 'zoomInUp',
				'rollIn'                => 'rollIn',
			),
			'out' => array(
				'backOutDown'           => 'backOutDown',
				'backOutLeft'           => 'backOutLeft',
				'backOutRight'          => 'backOutRight',
				'backOutUp'             => 'backOutUp',
				'bounceOut'             => 'bounceOut',
				'bounceOutDown'         => 'bounceOutDown',
				'bounceOutLeft'         => 'bounceOutLeft',
				'bounceOutRight'        => 'bounceOutRight',
				'bounceOutUp'           => 'bounceOutUp',
				'fadeOut'               => 'fadeOut',
				'fadeOutDown'           => 'fadeOutDown',
				'fadeOutDownBig'        => 'fadeOutDownBig',
				'fadeOutLeft'           => 'fadeOutLeft',
				'fadeOutLeftBig'        => 'fadeOutLeftBig',
				'fadeOutRight'          => 'fadeOutRight',
				'fadeOutRightBig'       => 'fadeOutRightBig',
				'fadeOutUp'             => 'fadeOutUp',
				'fadeOutUpBig'          => 'fadeOutUpBig',
				'fadeOutTopLeft'        => 'fadeOutTopLeft',
				'fadeOutTopRight'       => 'fadeOutTopRight',
				'fadeOutBottomRight'    => 'fadeOutBottomRight',
				'fadeOutBottomLeft'     => 'fadeOutBottomLeft',
				'flipOutX'              => 'flipOutX',
				'flipOutY'              => 'flipOutY',
				'rotateOut'             => 'rotateOut',
				'rotateOutDownLeft'     => 'rotateOutDownLeft',
				'rotateOutDownRight'    => 'rotateOutDownRight',
				'rotateOutUpLeft'       => 'rotateOutUpLeft',
				'rotateOutUpRight'      => 'rotateOutUpRight',
				'slideOutDown'          =>  'slideOutDown',
				'slideOutLeft'          => 'slideOutLeft',
				'slideOutRight'         => 'slideOutRight',
				'slideOutUp'            => 'slideOutUp',
				'zoomOut'               => 'zoomOut',
				'zoomOutDown'           => 'zoomOutDown',
				'zoomOutLeft'           => 'zoomOutLeft',
				'zoomOutRight'          => 'zoomOutRight',
				'zoomOutUp'             => 'zoomOutUp',
			)

		);

	}

}