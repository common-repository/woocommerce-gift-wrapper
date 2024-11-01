<?php
/**
 * The template for displaying gift wrap modal content in cart/checkout areas
 * 
 * This template can be overridden by copying it to yourtheme/woocommerce/wcgwp/modal.php
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @var string $label
 * @var array $list
 * @var boolean $show_thumbs
 * @deprecated 6.0.0
 * @version 5.2.7
 */
defined( 'ABSPATH' ) || exit;
?>

<div class="giftwrap_header_wrapper">
	<p class="giftwrap_header">
        <button type="button" data-toggle="modal" data-target=".giftwrapper_products_modal<?php esc_attr_e( $label ); ?>" class="wcgwp-modal-toggle wcgwp-modal-toggle<?php esc_attr_e( $label ); ?> button btn fusion-button fusion-button-default edgtf-btn" data-label="<?php esc_attr_e( $label ); ?>">
            <?php echo wp_kses_post( apply_filters( 'wcgwp_add_wrap_prompt', __( 'Add gift wrap?', 'woocommerce-gift-wrapper' ) ) ); ?>
        </button>
    </p>
</div>

<div id="giftwrap_modal<?php esc_attr_e( $label ); ?>" class="giftwrapper_products_modal giftwrapper_products_modal<?php esc_attr_e( $label ); ?> fusion-modal modal" tabindex="-1" role="dialog">
	<div class="modal-dialog <?php echo apply_filters( 'wcgwp_modal_size', 'modal-lg'); ?> modal-dialog-centered" role="document">
		<div class="modal-content fusion-modal-content">
			<div class="modal-header">
				<button class="giftwrap_cancel button btn fusion-button fusion-button-default edgtf-btn" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><?php echo wp_kses_post( __( 'Cancel', 'woocommerce-gift-wrapper' ) ); ?></span></button>
			</div>

			<form class="giftwrapper_products modal_form wcgwp_form" method="post">
				<div class="modal-body">
					<?php if ( ! apply_filters( 'wcgwp_hide_details', false ) ) { ?>
						<p class="giftwrap_details">
							<?php if ( ! empty( $giftwrap_details ) ) {
								echo esc_html( $giftwrap_details );
							} else {
								echo wp_kses_post( apply_filters( 'wcgwp_wrap_offerings', __( 'We offer the following gift wrap options:', 'woocommerce-gift-wrapper' ) ) );
							} ?>
						</p>
					<?php }
					
					$list_count = count( $list ) > 1;
					$product_image = '';
					$image_output_open = '';
					$image_output_close = '';
					$wrap_count = 0;
					$show_link = get_option( 'wcgwp_link' );
					$sizes = wp_get_registered_image_subsizes();
					$thumb_size = apply_filters( 'wcgwp_change_thumbnail', 'thumbnail' );
					$width = isset( $sizes[$thumb_size]['width'] ) ? $sizes[$thumb_size]['width'] : false;
					?>

					<ul class="giftwrap_ul">
						<?php
						// Product loop
						foreach ( $list as $product ) {
							if ( ! $product->is_in_stock() || ! $product->is_purchasable() ) {
								// Bailing on this product, not in stock or not available
								continue;
							}
							$price_html = $product->get_price_html();
							$slug = $product->get_slug();

							if ( $show_thumbs === true ) {
								// Here you could change thumbnail size with the 'wcgwp_change_thumbnail' filter
								$product_image = wp_get_attachment_image( get_post_thumbnail_id( $product->get_id() ), $thumb_size, false, array( "alt" => $product->get_title() ) );
								$image_output_open .= '<div class="giftwrap_thumb">';
								if ( 'yes' === $show_link ) {
									$product_URL = $product->get_permalink();
									$image_output_open .= '<a href="' . esc_url( $product_URL ) . '">';
								}
								if ( $show_link == 'yes' ) {
									$image_output_close .= '</a>';
								}
								$image_output_close .= '</div>';
							}
							echo '<li class="giftwrap_li';
							if ( $show_thumbs ) {
                                echo ' show_thumb';
                            } else {
                                echo ' no_giftwrap_thumbs';
                            }
							if ( $list_count ) {
								echo '"';
								if ( $width ) echo ' style="width:' . esc_attr( $width ) . 'px';
								echo '"><input type="radio" name="wcgwp_product' . esc_attr( $label ) . '" id="' . esc_attr( $slug . $label ) . '" value="' . esc_attr( $product->get_id() ) . '"' . ( $wrap_count == 0 ? 'checked' : '' ) . ' class="wcgwp_product_input">';
								echo '<label for="' . esc_attr( $slug . $label ) . '" class="giftwrap_desc"><span class="giftwrap_title"> ' . wp_kses_post( $product->get_title() ) . '</span> ' . wp_kses_post( $price_html );
								if ( 'yes' === $show_link ) {
									echo '</label>';
									echo wp_kses_post( $image_output_open ) . $product_image . wp_kses_post( $image_output_close );
									echo '</li>';
								} else {
									echo wp_kses_post( $image_output_open ) . $product_image . wp_kses_post( $image_output_close );
									echo '</label></li>';
								}
							} else {
								echo '"><label for="' . esc_attr( $slug . $label ) . '" class="giftwrap_desc singular_label"><span class="giftwrap_title"> ';
								echo wp_kses_post( $product->get_title() ) . '</span> ' . wp_kses_post( $price_html ) . '</label>';
								echo wp_kses_post( $image_output_open ) . $product_image . wp_kses_post( $image_output_close ) . '</li>';
								echo '<input type="hidden" name="wcgwp_product' . esc_attr( $label ) . '" value="' . esc_attr( $product->get_id() ) . '" id="' . esc_attr( $slug . $label ) . '">';
							} 
							++$wrap_count;
						} ?>
					</ul>

					<?php if ( (int) get_option( 'wcgwp_textarea_limit' ) > 0 ) { ?>
					<div class="wc_giftwrap_notes_container">
						<label for="wcgwp_notes<?php esc_attr_e( $label ); ?>">
							<?php echo wp_kses_post( apply_filters( 'wcgwp_add_wrap_message', __( 'Add Gift Wrap Message:', 'woocommerce-gift-wrapper' ) ) ); ?>
						</label>
						<textarea name="wcgwp_note<?php esc_attr_e( $label ); ?>" id="wcgwp_notes<?php esc_attr_e( $label ); ?>" cols="30" rows="4" maxlength="<?php esc_attr_e( get_option( 'wcgwp_textarea_limit', '1000' ) ); ?>" class="wc_giftwrap_notes"></textarea>	
					</div>
					<?php } ?>
				</div>

				<div class="modal-footer">
					<?php do_action( 'wcgwp_before_giftwrap_submit_button' ); ?>
					<?php wp_nonce_field( 'wcgwp_order_wrap', 'wcgwp_order_wrap_nonce' ); ?>
					<button type="submit" class="button btn alt giftwrap_submit replace_wrap fusion-button fusion-button-default edgtf-btn" name="wcgwp_submit<?php esc_attr_e( $label ); ?>">
						<?php echo wp_kses_post( apply_filters( 'wcgwp_add_wrap_button_text', __( 'Add Gift Wrap to Order', 'woocommerce-gift-wrapper' ) ) ); ?>
					</button>
					<?php do_action( 'wcgwp_after_giftwrap_submit_button' ); ?>
				</div>
			</form>
		</div>
	</div>
</div>