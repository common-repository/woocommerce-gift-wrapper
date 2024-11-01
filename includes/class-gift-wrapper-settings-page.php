<?php

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'Gift_Wrapper_Settings_Page', false ) ) {
	return new Gift_Wrapper_Settings_Page();
}

class Gift_Wrapper_Settings_Page extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id = 'gift-wrapper';
		$this->label = __( 'Gift Wrapper', 'woocommerce-gift-wrapper' );

		parent::__construct();

	}

	/**
	 * Set Gift Wrapper tab sections.
	 *
	 * @return array
	 */
	protected function get_own_sections() {

		return [
			''                  => __( 'General Options', 'woocommerce-gift-wrapper' ),
			'order_wrapping'    => __( 'Order Wrapping', 'woocommerce-gift-wrapper' ),
			'language'          => __( 'Language', 'woocommerce-gift-wrapper' ),
			'more_info'         => __( 'More Info', 'woocommerce-gift-wrapper' ),
		];

	}

	/**
	 * Get default (general options) settings array
	 *
	 * @return array
	 */
	public function get_settings_for_default_section() {

		$settings[] = [
			'name'          => __( 'Setup Instructions', 'woocommerce-gift-wrapper' ),
			'type'          => 'title',
			'desc'          => '<strong>1.</strong> '
								. sprintf( __( 'Create a new, unique <a href="%s" target="_blank" rel="noopener">WooCommerce product category</a> to hold your gift wrap product(s). Name it anything you\'d like.', 'woocommerce-gift-wrapper' ), admin_url( '/edit-tags.php?taxonomy=product_cat&post_type=product' ) )
								. '<br /><strong>2.</strong> ' . wp_kses_post( sprintf( __( 'Create at least one <a href="%s" target="_blank">WooCommerce product</a> to represent your gift wrap or add-on.', 'woocommerce-gift-wrapper' ), admin_url( 'post-new.php?post_type=product' ) ) )
                                . '<br /> &nbsp; &nbsp; &nbsp; &nbsp; ' . __( 'Give the product a title and a price. It must have a price set, even if at 0. It can be hidden from the catalog if you like, but shouldn\'t be private.', 'woocommerce-gift-wrapper' )
								. '<br /><strong>3.</strong> ' . __( 'Add the product(s) from step 2 to your new, unique WooCommerce product category from step 1.', 'woocommerce-gift-wrapper' )
								. '<br /><strong>4.</strong> ' . sprintf( __( 'Change the "<a href="#" id="wcgwp-wrap-category-link">Gift wrap category</a>" setting below to the product category created in step 1, and save.', 'woocommerce-gift-wrapper' )
								. '<br /><strong>5.</strong> ' . __( 'Review all the settings below, then finish on the <a href="%s" rel="noopener">Order Wrapping</a> options page.', 'woocommerce-gift-wrapper' ), admin_url( 'admin.php?page=wc-settings&tab=gift-wrapper&section=order_wrapping' ) ),
		];
		$settings[]	= [
			'id'            => 'wcgwp_category_id',
			'title'         => __( 'Gift wrap category', 'woocommerce-gift-wrapper' ),
			'desc_tip'      => __( 'Define the category which holds your gift wrap product(s), e.g. your boxes, bags, and gift paper product(s).', 'woocommerce-gift-wrapper' ),
			'type'          => 'select',
			'default'       => 'none',
			'options'       => WC_Gift_Wrap()->settings->product_cats(),
			'custom_attributes' => [
				'data-placeholder' => __( 'Define a Category', 'woocommerce-gift-wrapper' )
			],
			'class'         => 'chosen_select',
			'autoload'      => false,
		];
		$settings[] = [
			'id'            => 'wcgwp_textarea_limit',
			'name'          => __( 'Textarea character limit', 'woocommerce-gift-wrapper' ),
			'desc'          => __( 'Set to 0 (zero) to hide the textarea', 'woocommerce-gift-wrapper' ),
			'desc_tip'      => __( 'How many characters your customer can type when creating their own note for giftwrapping. Defaults to 1000 characters; lower this number if you want shorter notes from your customers.', 'woocommerce-gift-wrapper' ),
			'type'          => 'number',
			'default'       => 1000,
			'autoload'      => false,
		];
		$settings[] = [
			'type'  => 'sectionend'
		];

		$settings[] = [
			'name'          => __( 'Advanced Options', 'woocommerce-gift-wrapper' ),
			'type'          => 'title',
			'desc'          => '',
		];
		$settings[] = [
			'id'                => 'wcgwp_lt6_templates',
			'name'              => __( 'Accommodate templates from version 5?', 'woocommerce-gift-wrapper' ),
			'type'              => 'checkbox',
			'default'           => 'no',
			'desc'              => __( 'Version 6.0 of Gift Wrapper includes many template adjustments, and uses AJAX in the cart/checkout instead of a form submit.<br />If you have overwritten the plugin templates with customizations before 6.0 and things broke for you with the 6.0 update, check this box until you can update your templates.', 'woocommerce-gift-wrapper-plus' ),
			'autoload'          => false
		];

		$settings[] = [
			'id'            => 'wcgwp_delete_all',
			'name'          => __( 'Leave No Trace', 'woocommerce-gift-wrapper' ),
			'type'          => 'checkbox',
			'default'       => 'yes',
			'desc'          => __( 'Delete all settings upon plugin uninstall', 'woocommerce-gift-wrapper' ),
			'desc_tip'      => __( 'If you plan on deleting this plugin and not coming back, and want to keep your Wordpress database tables tidy, check this box, save settings, then delete the plugin.', 'woocommerce-gift-wrapper' ),
			'autoload'      => false
		];

		$settings[] = [
			'type' => 'sectionend'
		];

		return $settings;

	}

	/**
	 * Get order wrapping settings array
	 *
	 * @return array
	 */
	public function get_settings_for_order_wrapping_section() {

		$settings[] = [
			'name' => __( 'Order Gift Wrapping Settings', 'woocommerce-gift-wrapper' ),
			'type' => 'title',
			'desc' => __( 'These settings apply to per-order wrap options in the cart and checkout areas, not cart item (per-item) or per-product wrapping.', 'woocommerce-gift-wrapper' )
						. '<br />'
						. wp_kses_post( sprintf( __( 'Per-product and cart item (inside the cart) gift wrapping options are available in the <a href="%s" target="_blank" rel="noopener">PLUS version of this plugin.</a>', 'woocommerce-gift-wrapper' ), 'https://giftwrapper.app' ) ),
		];
		$settings[] = [
			'id'       => 'wcgwp_cart_hook',
			'name'     => __( 'Gift wrap prompt location', 'woocommerce-gift-wrapper' ),
			'desc_tip' => __( 'Choose where to show gift wrap options to the customer on the cart page. You may choose more than one. Set to "none" to both temporarily hide your wrap and keep your settings for later.', 'woocommerce-gift-wrapper' ),
			'type'     => 'multiselect',
			'default'  => 'none',
			'options'  => WC_Gift_Wrap()->settings->get_placements(),
			'css'      => 'min-height:110px',
			'autoload' => false
		];
		$settings[] = [
			'id'       => 'wcgwp_cart_display',
			'name'     => __( 'How should options be displayed?', 'woocommerce-gift-wrapper' ),
			'desc_tip' => __( 'If modal or slideToggle, there will be a prompt link ("header") in the cart, which when clicked will open a panel for customers to choose gift wrapping options. Checkbox and alternative modal display options available in the PLUS (paid) version.', 'woocommerce-gift-wrapper' ),
			'type'     => 'select',
			'default'  => 'modal',
			'options'  => [
				'modal'     => __( 'Modal/Popup', 'woocommerce-gift-wrapper' ),
				'slide'     => __( 'SlideToggle - uses jQuery', 'woocommerce-gift-wrapper' ),
				'checkbox'  => __( 'Checkbox', 'woocommerce-gift-wrapper' ),
			],
			'autoload' => false
		];
		$settings[] = [
			'id'        => 'wcgwp_checkbox_link',
			'name'      => __( 'Link checkbox label?', 'woocommerce-gift-wrapper-plus' ),
			'desc_tip'  => __( 'Should the checkbox label product title link to its WC product page?', 'woocommerce-gift-wrapper-plus' ),
			'type'      => 'select',
			'default'   => 'no',
			'options'   => [
				'yes'       => __( 'Yes', 'woocommerce-gift-wrapper' ),
				'no'        => __( 'No', 'woocommerce-gift-wrapper' ),
			],
			'autoload'  => false,
		];
		$settings[] = [
			'id'       => 'wcgwp_show_thumb',
			'name'     => __( 'Show thumbnails?', 'woocommerce-gift-wrapper' ),
			'desc_tip' => __( 'Should gift wrap product thumbnail images be visible in the cart?', 'woocommerce-gift-wrapper' ),
			'type'     => 'select',
			'default'  => 'yes',
			'options'  => [
				'yes' => __( 'Yes', 'woocommerce-gift-wrapper' ),
				'no'  => __( 'No', 'woocommerce-gift-wrapper' ),
			],
			'autoload' => false,
		];
		$settings[] = [
			'id'       => 'wcgwp_link',
			'name'     => __( 'Link thumbnails?', 'woocommerce-gift-wrapper' ),
			'desc_tip' => __( 'Should thumbnail images link to gift wrap product details?', 'woocommerce-gift-wrapper' ),
			'type'     => 'select',
			'default'  => 'no',
			'options'  => [
				'yes' => __( 'Yes', 'woocommerce-gift-wrapper' ),
				'no'  => __( 'No', 'woocommerce-gift-wrapper' ),
			],
			'autoload' => false
		];
		$settings[] = [
			'id'       => 'wcgwp_number',
			'name'     => __( 'Allow more than one gift wrap product in cart?', 'woocommerce-gift-wrapper' ),
			'desc_tip' => __( 'If yes, customers can buy more than one gift wrapping product in one order.', 'woocommerce-gift-wrapper' ),
			'type'     => 'select',
			'default'  => 'no',
			'options'  => [
				'yes'               => __( 'Yes', 'woocommerce-gift-wrapper' ),
				'no'                => __( 'No', 'woocommerce-gift-wrapper' ),
			],
			'autoload' => false
		];
		$settings[] = [
			'type' => 'sectionend'
		];

		return $settings;

	}

	/**
	 * Get "more info" section settings array
	 *
	 * @return void
	 */
	public function output_more_info_screen() { ?>

		<div class="wcgwp-donation" style="margin:3em">
			<p style="font-size: 2em;">
				Hi, I'm Caroline, a WordPress developer in Nevada USA. I've kept <strong>WooCommerce Gift Wrapper</strong> in active development since 2014 <em>as an unpaid volunteer</em>. Why? I love the idea of more gifts being sent all around the world! 🎁 Who doesn't love a present? 😍
			</p>
			<p style="font-size: 1.75em;">
                But also -- and truthfully -- I depend on donations and upgrades to make my living. If you find this little plugin useful, and particularly if you benefit from it, consider upgrading to the much more powerful <a href="https://www.giftwrapper.app" target="_blank" rel="noopener">Gift Wrapper Plus</a>.</p>
			<p style="font-size: 1.5em;">
			If that's not in your budget I understand. Please take a moment to write <a href="https://wordpress.org/support/plugin/woocommerce-gift-wrapper/reviews/?filter=5" target="_blank" rel="noopener">an encouraging review</a>, or <a href="https://www.paypal.com/paypalme/littlepackage" target="_blank" rel="noopener noreferrer">donate a couple dollars (like $2) using PayPal</a>. 🎉 Your kindness and enthusiasm makes donating my time to this open-source project worthwhile!
			</p>
			<h2 style="font-size:3em">Need help?</h2>
			<p style="font-size: 2em;">
				Please refer to the <a href="https://wordpress.org/plugins/woocommerce-gift-wrapper/#faq-header" target=_blank" rel="noopener">FAQ</a> and <a href="https://wordpress.org/support/plugin/woocommerce-gift-wrapper/" target="_blank" rel="noopener nofollow">support forum</a> where your question might already be answered. <a href="https://wordpress.org/support/topic/before-you-post-please-read/" rel="https://wordpress.org/support/topic/before-you-post-please-read/">Read this before posting</a>. I only provide email support for paying customers (thank you ✌️).
            </p>
		</div>

	<?php }


	/**
	 * Get language settings array
	 *
	 * @return array
	 */
	public function get_settings_for_language_section() {

		$settings[] = [
			'name'              => '',
			'type'              => 'title',
			'id'                => 'wcgwp-plus-only-lang',
			'desc'              => '<p style="color:#135e96;font-size:20px"><strong>' . sprintf( wp_kses( __( 'Easy translation to your language is available in the PLUS version of Gift Wrapper. &nbsp; <a href="%s" class="button button-primary" rel="noopener" target="_blank">Upgrade now!</a>', 'woocommerce-gift-wrapper' ), [ 'a' => [ 'href' => [], 'target' => [], 'rel' => [], 'class' => [ 'btn','button' ] ] ] ), 'https://www.giftwrapper.app' ) . '</strong></p>',
		];
		$settings[] = [
			'type' => 'sectionend'
		];

		$settings[] = [
			'name'              => __( 'Language Matters', 'woocommerce-gift-wrapper' ),
			'type'              => 'title',
			'desc'              => __( 'Enter your adjustments or translations in the fields at right. Defaults are shown at left.' )
			                       . __( ' To make text disappear on the front end, you may need to use CSS (e.g. {style="display:none"} ), to hide enclosing elements. To use HTML, you will need to override included plugin template files.' ),
		];
		$settings[] = [
			'id'                => 'wcgwp_strings[wrap]',
			'name'              => __( 'Gift wrap', 'woocommerce-gift-wrapper' ),
			'type'              => 'text',
			'default'           => 'Gift wrap',
			'class'             => 'wcgwp-plus',
			'autoload'          => false
		];
		$settings[] = [
			'id'                => 'wcgwp_strings[add_wrap_prompt]',
			'name'              => __( 'Add gift wrap?', 'woocommerce-gift-wrapper' ),
			'type'              => 'text',
			'default'           => 'Add gift wrap?',
			'class'             => 'wcgwp-plus',
		];
		$settings[] = [
			'id'                => 'wcgwp_strings[add_wrap_message]',
			'name'              => __( 'Add Gift Wrap Message:', 'woocommerce-gift-wrapper' ),
			'type'              => 'text',
			'default'           => 'Add Gift Wrap Message:',
			'class'             => 'wcgwp-plus',
			'autoload'          => false
		];
		$settings[] = [
			'id'                => 'wcgwp_strings[add_wrap_to_order]',
			'name'              => __( 'Add Gift Wrap to Order:', 'woocommerce-gift-wrapper' ),
			'type'              => 'text',
			'default'           => 'Add Gift Wrap to Order:',
			'class'             => 'wcgwp-plus',
			'autoload'          => false
		];
		$settings[] = [
			'id'                => 'wcgwp_strings[cancel]',
			'name'              => __( 'Cancel', 'woocommerce-gift-wrapper' ),
			'type'              => 'text',
			'default'           => 'Cancel',
			'class'             => 'wcgwp-plus',
			'autoload'          => false
		];
		$settings[] = [
			'id'                => 'wcgwp_strings[cancel_wrap]',
			'name'              => __( 'Cancel gift wrap', 'woocommerce-gift-wrapper' ),
			'type'              => 'text',
			'default'           => 'Cancel gift wrap',
			'class'             => 'wcgwp-plus',
			'autoload'          => false
		];
		$settings[] = [
			'id'                => 'wcgwp_strings[add_wrap_for_x]',
			'name'              => __( 'Add gift wrapping for %s?', 'woocommerce-gift-wrapper' ),
			'type'              => 'text',
			'default'           => 'Add gift wrapping for %s?',
			'class'             => 'wcgwp-plus',
			'autoload'          => false
		];
		$settings[] = [
			'id'                => 'wcgwp_strings[add_x_for_x]',
			'name'              => __( 'Add %s for %s?', 'woocommerce-gift-wrapper-plus' ),
			'type'              => 'text',
			'default'           => 'Add %s for %s?',
			'class'             => 'wcgwp-plus',
			'autoload'          => false
		];
		$settings[] = [
			'id'                => 'wcgwp_strings[wrap_details]',
			'name'              => __( 'We offer the following gift wrap options:', 'woocommerce-gift-wrapper' ),
            'desc'              => __( 'Optional text to give any details or conditions of your gift wrap', 'woocommerce-gift-wrapper' ),
			'type'              => 'textarea',
			'css'               => 'height: 75px;',
			'default'           => 'We offer the following gift wrap options:',
		];
		$settings[] = [
			'id'                => 'wcgwp_strings[note]',
			'name'              => __( 'Note', 'woocommerce-gift-wrapper' ),
			'type'              => 'text',
			'default'           => 'Note',
			'class'             => 'wcgwp-plus',
			'autoload'          => false
		];
		$settings[] = [
			'type'  => 'sectionend'
		];

		return $settings;

	}

	/**
	 * Output the settings.
	 */
	public function output() {

		global $current_section;

		if ( 'more_info' === $current_section ) {
			$this->output_more_info_screen();
		}
		$settings = $this->get_settings_for_section( $current_section );

		WC_Admin_Settings::output_fields( $settings );

	}

}