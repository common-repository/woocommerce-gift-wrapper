<?php
/**
 * The template for displaying a simple gift wrap checkbox around the cart/checkout pages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcgwp/v6/peri-cart/checkbox.php
 *
 * Set variables: $price, $price_html
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @var string $label       Current action hook
 * @var array  $products    Gift wrap product objects
 *
 * @since 6.0
 * @version 6.0.5
 */
defined( 'ABSPATH' ) || exit;
$button_class = wc_wp_theme_get_element_class_name( 'button' ) ?? '';
$has_textarea = (int) get_option( 'wcgwp_textarea_limit' ) > 0;
?>

<div class="wcgwp-wrapper wcgwp-peri-cart wcgwp-checkbox-wrapper wcgwp-<?php esc_attr_e( $label ); ?>">

	<div id="wcgwp-panel-<?php esc_attr_e( $label ); ?>">
		<?php
		$product_count  = count( $products );
		$i              = 0;
		$show_link      = get_option( 'wcgwp_checkbox_link' );
		$hide_price     = get_option( 'wcgwp_hide_price', 'no' );

		// Product loop
		foreach ( $products as $product ) {
			if ( ! $product->is_in_stock() || ! $product->is_purchasable() ) {
				continue;
			}
			$product_id = $product->get_id();
			$product_title = $product->get_title();
			$slug = $product->get_slug();
			if ( 'no' === $hide_price ) {
				$price_html = apply_filters( 'wcgwp_price_html', ' ' . $product->get_price_html(), $product );
			}
				if ( 'yes' === $show_link ) {
					$product_url = $product->get_permalink();
				}
			if ( 'yes' === $hide_price ) {
				$prompt = WCGWP()->strings->get_string( 'add_x' );
				if ( 'yes' === $show_link ) {
					$product_title = '<a href="' . $product_url . '" rel="noopener">' . $product_title . '</a>';
				}
				$prompt = sprintf( $prompt, $product_title );
			} else {
				if ( $product_count < 2 && apply_filters( 'wcgwp_v5_checkbox_prompt', true ) ) {
					$prompt = WCGWP()->strings->get_string( 'add_wrap_for_x' );
					$prompt = sprintf( $prompt, $price_html );
				} else {
					$prompt = WCGWP()->strings->get_string( 'add_x_for_x' );
					if ( 'yes' === $show_link ) {
						$product_title = '<a href="' . $product_url . '" rel="noopener">' . $product_title . '</a>';
					}
					$prompt = sprintf( $prompt, $product_title, $price_html );
				}
			}
			?>
			<p class="wcgwp-input-wrapper">
				<?php wp_nonce_field( 'wcgwp_ajax_wrap', 'wcgwp_nonce-' . esc_attr( $label ) ); ?>
				<label for="wcgwp-<?php echo esc_attr( $slug . '-' . $label  ); ?>">
					<input name="wcgwp_product_id[]" type="checkbox" value="<?php esc_attr_e( $product_id ); ?>" id="wcgwp-<?php echo esc_attr( $slug . '-' . $label  ); ?>" class="wcgwp-input wcgwp-checkbox <?php if ( ! $has_textarea ) { ?>wcgwp-peri-cart-checkbox<?php } ?>" data-productid="<?php esc_attr_e( $product_id ); ?>" data-label="<?php esc_attr_e( $label ); ?>" data-key="">
					<?php echo sprintf( wp_kses_post( $prompt ), $product_title, $price_html ); ?>
				</label>
			</p>
			<?php
			if ( 'yes' !== get_option( 'wcgwp_multiples' ) ) {
				break;
			}
		} ?>

		<?php if ( $has_textarea ) { ?>
			<p class="wcgwp-note-container">
				<label for="wcgwp-note-<?php esc_attr_e( $label ); ?>">
					<?php echo WCGWP()->strings->get_string( 'add_wrap_message' ); ?>
				</label>
				<textarea name="wcgwp_note" id="wcgwp-note-<?php esc_attr_e( $label ); ?>" maxlength="<?php esc_attr_e( get_option( 'wcgwp_textarea_limit', '1000' ) ); ?>" class="wcgwp-note"></textarea>
			</p>

			<?php wp_nonce_field( 'wcgwp_ajax_wrap', 'wcgwp_nonce-' . esc_attr( $label ) ); ?>
			<?php do_action( 'wcgwp_before_giftwrap_submit_button' ); ?>
			<p class="wcgwp-button-wrapper">
				<button type="button" data-label="<?php esc_attr_e( $label ); ?>" class="wcgwp-submit button btn alt <?php esc_attr_e( $button_class ); ?>" data-label="<?php esc_attr_e( $label ); ?>">
					<?php echo WCGWP()->strings->get_string( 'add_wrap_to_order' ); ?>
				</button>
			</p>
			<?php do_action( 'wcgwp_after_giftwrap_submit_button' ); ?>
		<?php } ?>
	</div>
</div>