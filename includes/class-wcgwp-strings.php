<?php

defined( 'ABSPATH' ) || exit;

class WCGWP_Strings {

	/**
	 * Constructor
	 */
	public function __construct() {

	}

	/**
	 * Saves default strings
	 *
	 * @return void
	 */
	public function save_default_strings() {

		// Save default strings to the DB
		update_option( 'wcgwp_strings', $this->get_default_strings(), false );

	}

	/**
	 * Get default string array
	 *
	 * @since 6.0
	 * @return array
	 */
	public function get_default_strings() {

		return apply_filters( 'wcgwp_filter_strings',
			[
				'add_wrap_prompt'   => 'Add gift wrap?',
				'add_wrap_message'  => 'Add Gift Wrap Message:',
				'add_wrap_to_order' => 'Add Gift Wrap to Order',
				'cancel'            => 'Cancel',
				'cancel_wrap'       => 'Cancel gift wrap',
				'add_wrap_for_x'    => 'Add gift wrapping for %s?',
				'add_x'             => 'Add %s?',
				'add_x_for_x'       => 'Add %s for %s?',
				'wrap_details'      => 'We offer the following gift wrap options:',
				'wrap_offerings'    => 'We offer the following gift wrap options:',
				'note'              => 'Note',
				'note_fee'          => 'Note fee',
				'wrap'              => 'Gift wrap',
				'gift_wrap_added'   => 'Gift wrap was added to your cart.',
			]
		);

	}

	public function get_default_string( $key ) {

		$strings = $this->get_default_strings();
		return $strings[ $key ];

	}

	/**
	 * Get language string
	 *
	 * @param string $key
	 * @since 6.0
	 * @return string
	 */
	public function get_string( $key ) {

		$strings = (array) get_option( 'wcgwp_strings', [] );
		if ( empty( $strings ) ) {
			$this->save_default_strings();
		}
		if ( ! isset( $strings[ $key ] ) ) {
			$strings[ $key ] = $this->get_default_string( $key );
		}
		$strings['add_wrap_message'] = apply_filters( 'wcgwp_add_wrap_message', $strings['add_wrap_message'] );
		$strings['add_wrap_prompt'] = apply_filters( 'wcgwp_add_wrap_prompt', $strings['add_wrap_prompt'] );
		$strings['add_wrap_to_order'] = apply_filters( 'wcgwp_add_wrap_button_text', $strings['add_wrap_to_order'] );

		return wp_kses_post( apply_filters( 'wcgwp_filter_string', __( $strings[ $key ], 'woocommerce-gift-wrapper' ) ) );

	}

}