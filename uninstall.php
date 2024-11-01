<?php

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { // If uninstall not called from WordPress exit
	exit();
}

/**
 * Manages Gift Wrapper uninstallation
 * The goal is to remove ALL Gift Wrapper related data in db
 *
 * @since 2.2
 */
class WCGWP_Unwrap {

	/**
	 * Constructor: manages uninstall for multisite
	 *
	 */
	public function __construct() {

		// Check if it is a multisite uninstall - if so, run the uninstall function for each blog id
		if ( is_multisite() ) {
			global $wpdb;
			foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blog_id ) {
				switch_to_blog( $blog_id );
				$this->uninstall();
			}
			restore_current_blog();
		}
		else {
			$this->uninstall();
		}

	}

	/**
	 * Removes ALL plugin data only when the 'wcgwp_delete_all' option is active
	 *
	 * @return void
	 */
	public function uninstall() {

		if ( 'yes' !== get_option( 'wcgwp_delete_all' ) ) {
			return;
		}

		foreach ( [
			'wcgwp_all_products',
			'wcgwp_bootstrap_off',
			'wcgwp_cant_wrap_cue',
			'wcgwp_cart_display',
			'wcgwp_category_id',
			'wcgwp_checkbox_textarea',
			'wcgwp_details',
			'wcgwp_display',
			'wcgwp_exclude_cats',
			'wcgwp_hide_price',
			'wcgwp_line_item',
			'wcgwp_line_item_modal',
			'wcgwp_link',
			'wcgwp_lt5_templates',
			'wcgwp_cart_display',
			'wcgwp_modal_animate',
			'wcgwp_modal_animate_in',
			'wcgwp_modal_animate_out',
			'wcgwp_number',
			'wcgwp_number_max',
			'wcgwp_per_product_type',
			'wcgwp_product_display',
			'wcgwp_product_link',
			'wcgwp_product_num',
			'wcgwp_product_quantity',
			'wcgwp_product_show_thumb',
			'wcgwp_show_relationship',
			'wcgwp_show_thumb',
			'wcgwp_simple_product',
			'wcgwp_strings',
			'wcgwp_note_fee',
			'wcgwp_note_fee_amount',
			'wcgwp_textarea_limit',
			'wcgwp_donate_dismiss',
			'wcgwp_donate_dismiss_4-3',
			'wcgwp_donate_dismiss_11-16',
			'wcgwp_donate_dismiss_12-14',
			'wcgwp_donate_dismiss_10-14',
			'wcgwp_donate_dismiss_03-21',
			'wcgwp_donate_dismiss_02-04',
			'wcgwp_donate_dismiss_05-24',
			'wcgwp_donate_dismiss_08-24',
			'wcgwp_donate_dismiss_09-08',
			'wcgwp_donate_dismiss_11-29',
			'wcwg_version',
			'_wcwgp_details',
			'wcGIFTWRAPPER_VERSION',
			'wcgwp_delete_all',
		] as $option ) {
				delete_option( $option );
		}

	}

}
new WCGWP_Unwrap();