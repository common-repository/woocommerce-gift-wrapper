<?php
/**
 * The template for displaying gift wrap modal content in cart/checkout areas
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcgwp/v6/peri-cart/modal.php
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @var string  $label      Current action hook
 * @var array   $products   Gift wrap product objects
 * @var boolean $show_thumbs
 * @version 6.0.5
 * @since 6.0
 */
defined( 'ABSPATH' ) || exit;
$button_class = wc_wp_theme_get_element_class_name( 'button' ) ?? '';
?>

<div class="wcgwp-wrapper wcgwp-peri-cart wcgwp-wrapper-<?php esc_attr_e( $label ); ?>">
	<p class="wcgwp-prompt-wrapper">
		<button type="button" data-label="<?php esc_attr_e( $label ); ?>" class="wcgwp-modal-toggle button btn alt <?php esc_attr_e( $button_class ); ?>">
			<?php echo WCGWP()->strings->get_string( 'add_wrap_prompt' ); ?>
		</button>
	</p>

	<div id="wcgwp-panel-<?php esc_attr_e( $label ); ?>" class="wcgwp-modal modal fusion-modal" tabindex="-1" role="dialog">
		<div class="modal-dialog <?php echo apply_filters( 'wcgwp_modal_size', 'modal-lg'); ?> modal-dialog-centered" role="document">
			<div class="modal-content fusion-modal-content">
				<div class="modal-header">
					<button type="button" id="wcgwp-cancel-<?php esc_attr_e( $label ); ?>" class="wcgwp-cancel button btn <?php esc_attr_e( $button_class ); ?>" data-dismiss="modal" aria-label="Close">
						<?php echo WCGWP()->strings->get_string( 'cancel' ); ?>
					</button>
				</div>
				<div class="modal-body">
					<?php if ( ! apply_filters( 'wcgwp_hide_details', false ) ) { ?>
						<p class="wcgwp-details">
							<?php echo WCGWP()->strings->get_string( 'wrap_details' ); ?>
						</p>
					<?php }

					$product_count      = count( $products );
					$i                  = 0;
					$image_output_open  = '';
					$image_output_close = '';
					$product_image      = '';
					$show_link          = get_option( 'wcgwp_link', 'no' );
					$hide_price         = get_option( 'wcgwp_hide_price', 'no' );
					$sizes              = wp_get_registered_image_subsizes();
					$thumb_size         = apply_filters( 'wcgwp_change_thumbnail', 'thumbnail' );
					$width              = $sizes[$thumb_size]['width'] ?? false;
					?>

					<ul class="wcgwp-ul giftwrap_ul <?php if ( $product_count < 2 ) { echo ' singular'; } if ( 'yes' === get_option( ' wcgwp_multiples' ) ) { echo 'wcgwp-multiples'; } ?>">

						<?php
						// Product loop
						foreach ( $products as $product ) {
							if ( ! $product->is_in_stock() || ! $product->is_purchasable() ) {
								continue;
							}
							$product_id = $product->get_id();
							$product_title = $product->get_title();
							if ( 'no' === $hide_price ) {
								$price_html	= apply_filters( 'wcgwp_price_html', ' ' . $product->get_price_html(), $product );
							}
							$slug = $product->get_slug();

							if ( $show_thumbs === true ) {
								$product_image = wp_get_attachment_image( get_post_thumbnail_id( $product_id ), $thumb_size, false, array( "alt" => $product_title ) );
								$image_output_open .= '<div class="wcgwp-thumb">';
								if ( 'yes' === $show_link ) {
									$product_url = $product->get_permalink();
									$image_output_open .= '<a href="' . esc_url( $product_url ) . '">';
								}
								if ( 'yes' === $show_link ) {
									$image_output_close .= '</a>';
								}
								$image_output_close .= '</div>';
							}

							echo '<li class="wcgwp-li';
							if ( $show_thumbs ) {
								echo ' show_thumb"';
							} else {
								echo ' no_giftwrap_thumbs"';
							}
							if ( $width && $product_count > 1 ) {
								echo ' style="max-width:' . esc_attr( $width ) . 'px"';
							}
							echo '>';
							if ( $product_count > 1 ) {
								$type = 'radio';
								if ( 'yes' === get_option( 'wcgwp_multiples' ) ) {
									$type = 'checkbox';
								}
								echo '<input type="' . esc_attr( $type ) . '" name="wcgwp_product_id[]" data-productid="' . esc_attr( $product_id ) . '" id="' . esc_attr( $slug . "-" . $label ) . '" class="wcgwp-input" ' . ( 'radio' === $type && $i == 0 ? 'checked' : '' ) . '>';
							} else {
								echo '<input type="hidden" name="wcgwp_product_id[]" data-productid="' . esc_attr( $product_id ) . '" id="' . esc_attr( $slug . "-" . $label ) . '" class="wcgwp-input">';
							}
							echo '<label for="' . esc_attr( $slug . "-" . $label ) . '" class="wcgwp-desc' . ( $product_count < 2 ? ' singular_label' : '' ). '">';
							echo '<span class="wcgwp-title"> ' . wp_kses_post( $product_title ) . '</span>';
							if ( 'no' === $hide_price ) {
								echo wp_kses_post( $price_html );
							}
							if ( 'yes' === $show_link ) {
								echo '</label>' . wp_kses_post( $image_output_open ) . $product_image . wp_kses_post( $image_output_close );
							} else {
								echo wp_kses_post( $image_output_open ) . $product_image . wp_kses_post( $image_output_close ) . '</label>';
							}
							echo '</li>';
							++$i;
						} ?>
					</ul>

					<?php if ( (int) get_option( 'wcgwp_textarea_limit' ) > 0 ) { ?>
						<div class="wcgwp-note-container">
							<label for="wcgwp-note-<?php esc_attr_e( $label ); ?>">
								<?php echo WCGWP()->strings->get_string( 'add_wrap_message' ); ?>
							</label>
							<textarea name="wcgwp_note" id="wcgwp-note-<?php esc_attr_e( $label ); ?>" maxlength="<?php esc_attr_e( get_option( 'wcgwp_textarea_limit', '1000' ) ); ?>" class="wcgwp-note"></textarea>
						</div>
					<?php } ?>

				</div>

				<div class="modal-footer">
					<?php do_action( 'wcgwp_before_giftwrap_submit_button' ); ?>
					<?php wp_nonce_field( 'wcgwp_ajax_wrap', 'wcgwp_nonce-' . esc_attr( $label ) ); ?>
					<p class="wcgwp-button-wrapper">
						<button type="button" class="wcgwp-submit button btn alt <?php esc_attr_e( $button_class ); ?>" data-label="<?php esc_attr_e( $label ); ?>">
							<?php echo WCGWP()->strings->get_string( 'add_wrap_to_order' ); ?>
						</button>
					</p>
					<?php do_action( 'wcgwp_after_giftwrap_submit_button' ); ?>
				</div>
			</div>
		</div>
	</div>
</div>