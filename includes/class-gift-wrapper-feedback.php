<?php

defined( 'ABSPATH' ) || exit;

class The_Gift_Wrapper_Feedback {

	/**
	 * @since 6.2.0
	 * @access public
	 */
	public function __construct() {

		// Output the deactivation feedback HTML
		add_action( 'admin_footer-plugins.php', [ $this, 'print_feedback_dialog' ] );

		// AJAX
		add_action( 'wp_ajax_gift_wrapper_deactivate_feedback', [ $this, 'ajax_gift_wrapper_deactivate_feedback' ] );
	}

	/**
	 * Display a dialog box to ask the user why they are deactivating
	 *
	 * Fired by `admin_footer` filter
	 *
	 * @since 6.2.0
	 * @access public
	 */
	public function print_feedback_dialog() {

		$deactivate_reasons = [
			'was_not_what_I_expected' => [
				'title' => esc_html__( 'It was not what I expected', 'woocommerce-gift-wrapper' ),
				'input_placeholder' => '',
			],
			'own_gift_wrapper_plus' => [
				'title' => esc_html__( 'I have Gift Wrapper Plus', 'woocommerce-gift-wrapper' ),
				'alert' => esc_html__( 'Yay! 🎉 Thank you so much!', 'woocommerce-gift-wrapper' ),
			],
			'no_longer_needed' => [
				'title' => esc_html__( 'I no longer need Gift Wrapper', 'woocommerce-gift-wrapper' ),
				'input_placeholder' => '',
			],
			'found_a_better_plugin' => [
				'title' => esc_html__( 'I found a better plugin', 'woocommerce-gift-wrapper' ),
				'input_placeholder' => esc_html__( 'Please share which plugin', 'woocommerce-gift-wrapper' ),
			],
			'could_not_get_the_plugin_to_work' => [
				'title' => esc_html__( 'I couldn\'t get Gift Wrapper to work', 'woocommerce-gift-wrapper' ),
				'input_placeholder' => esc_html__( 'Any details?', 'woocommerce-gift-wrapper' ),
			],
			'temporary_deactivation' => [
				'title' => esc_html__( 'It\'s a temporary deactivation', 'woocommerce-gift-wrapper' ),
				'input_placeholder' => '',
			],
			'other' => [
				'title' => esc_html__( 'Other', 'woocommerce-gift-wrapper' ),
				'input_placeholder' => esc_html__( 'Please share', 'woocommerce-gift-wrapper' ),
			],
		];

		?>
		<a aria-label="Give the Gift of Feedback 🎁" href="#TB_inline?width=600&height=600&inlineId=wcgwp-feedback-thickbox" id="wcgwp-feedback-thickbox-link" class="hidden thickbox">Love is the only language that works</a>

		<div id="wcgwp-feedback-thickbox" class="hidden">
			<form id="wcgwp-feedback-dialog-form" method="post">
				<?php wp_nonce_field( 'gift_wrapper_deactivate_feedback_nonce' ); ?>
				<input type="hidden" name="action" value="gift_wrapper_deactivate_feedback">
				<p style="font-size:1.125rem">We'd love to know why you are deactivating <strong>Gift Wrapper</strong>:</p>
				<div>
					<?php foreach ( $deactivate_reasons as $reason_key => $reason ) : ?>
						<span class="wcgwp-feedback-dialog-input-wrapper">
							<input id="wcgwp-feedback-<?php esc_attr_e( $reason_key ); ?>" type="radio" name="reason_key" value="<?php esc_attr_e( $reason_key ); ?>">
							<label for="wcgwp-feedback-<?php esc_attr_e( $reason_key ); ?>"><?php esc_html_e( $reason['title'] ); ?></label>
							<?php if ( ! empty( $reason['input_placeholder'] ) ) : ?>
								<input class="wcgwp-feedback-text wcgwp-feedback-<?php esc_attr_e( $reason_key ); ?> hidden" type="text" name="reason_<?php esc_attr_e( $reason_key ); ?>" placeholder="<?php esc_attr_e( $reason['input_placeholder'] ); ?>">
							<?php endif; ?>
							<?php if ( ! empty( $reason['alert'] ) ) { ?>
								<p class="wcgwp-feedback-text wcgwp-feedback-<?php esc_attr_e( $reason_key ); ?> hidden">
									<strong><?php esc_html_e( $reason['alert'] ); ?></strong>
								</p>
							<?php } ?>
						</span>
					<?php endforeach; ?>
				</div>
				<br>
				<p>
					<button type="button" id="wcgwp-feedback-skip" class="button wcgwp-button">Skip & Deactivate</button> &nbsp; <button type="button" id="wcgwp-feedback-submit" class="button wcgwp-button button-primary">Submit Feedback & Deactivate</button> <span class="spinner"></span>
				</p>
				<p style="font-size:0.75rem">
					Your anonymous feedback helps us improve this free, open source software for others, and possibly you -- if you come back! Bye for now 👋🏼
				</p>

			</form>
		</div>
		<?php
	}

	/**
	 * AJAX send the user feedback when Gift Wrapper is deactivated
	 *
	 * Fired by `wp_ajax_gift_wrapper_deactivate_feedback` action.
	 *
	 * @since 6.2.0
	 * @access public
	 */
	public function ajax_gift_wrapper_deactivate_feedback() {

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'gift_wrapper_deactivate_feedback_nonce' ) ) {
			wp_send_json_error();
		}

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( 'Permission denied' );
		}

		$reason_key = sanitize_text_field( $_POST['reason_key'] ) ?? '';
		$reason_text = sanitize_text_field( $_POST["reason_{$reason_key}"] ) ?? '';

		if ( empty( $reason_key ) && empty( $reason_text ) ) {
			wp_send_json_success();
		}

		$this->send_feedback( $reason_key, $reason_text );
		wp_send_json_success();

	}

	/**
	 * Fires a request to Little Package server with the feedback data
	 *
	 * @since 6.2.0
	 * @access private
	 *
	 * @param string $feedback_key  Feedback key
	 * @param string $feedback_text Feedback text
	 *
	 * @return void
	 */
	private function send_feedback( $feedback_key, $feedback_text ) {

		try {
			wp_remote_post( 'https://giftwrapper.app/api/v1/feedback/', [
				'timeout' => 15,
				'body' => [
					'plugin_version'=> GIFTWRAPPER_VERSION,
					'site_lang'     => get_bloginfo( 'language' ),
					'feedback_key'  => $feedback_key,
					'feedback'      => $feedback_text,
				],
			] );
		} catch ( Exception $e ) {

		}

	}

	/**
	 * @since 2.3.0
	 * @access private
     * @return boolean
	 */
	private function is_plugins_screen() {
		return in_array( get_current_screen()->id, [ 'plugins', 'plugins-network' ] );
	}

}
new The_Gift_Wrapper_Feedback();