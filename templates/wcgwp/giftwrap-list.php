<?php
/**
 * The template for displaying gift wrap products in the general cart/checkout areas
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcgwp/giftwrap-list.php
 *
 * If upgrading to the PLUS version of Gift Wrapper, this template is replaced by the
 * /wcgwp/giftwrap-list-cart-checkout.php template
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
 * @since 4.4
 */
defined( 'ABSPATH' ) || exit;
?>

<div class="giftwrap_header_wrapper gift-wrapper-info">
	<button type="button" class="show_giftwrap show_giftwrap<?php esc_attr_e( $label ); ?> wcgwp-slide-toggle button btn fusion-button fusion-button-default edgtf-btn" data-target="<?php esc_attr_e( $label ); ?>"><?php echo wp_kses_post( apply_filters( 'wcgwp_add_wrap_prompt', __( 'Add gift wrap?', 'woocommerce-gift-wrapper' ) ) ); ?></button><span class="gift-wrapper-cancel <?php esc_attr_e( $label ); ?>"><button type="button" class="cancel_giftwrap cancel_giftwrap<?php esc_attr_e( $label ); ?> button btn fusion-button fusion-button-default" data-target="<?php esc_attr_e( $label ); ?>"><?php echo wp_kses_post( __( 'Cancel', 'woocommerce-gift-wrapper' ) ); ?></button></span>
</div>

<form method="post" class="giftwrap_products giftwrapper_products non_modal wcgwp_slideout wcgwp_form slideout<?php esc_attr_e( $label ); ?>">
	<?php if ( ! apply_filters( 'wcgwp_hide_details', false ) ) { ?>
		<p class="giftwrap_details">
		<?php if ( ! empty( $giftwrap_details ) ) {
			echo wp_kses_post( $giftwrap_details );
		} else {
			echo wp_kses_post( apply_filters('wcgwp_wrap_offerings', __( 'We offer the following gift wrap options:', 'woocommerce-gift-wrapper' ) ) );
		} ?>
		</p>
	<?php } 
 
	$product_image = '';
	$count = count( $list ) > 1;
	$image_output_open = '';
	$image_output_close = '';
	$wrap_count = 0;
	$show_link = get_option( 'wcgwp_link', 'yes' );
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
			$price_html	= $product->get_price_html();
			$slug = $product->get_slug();

			if ( $show_thumbs === true ) {
				// Here you could change thumbnail size with the 'wcgwp_change_thumbnail' filter
				$product_image = wp_get_attachment_image( get_post_thumbnail_id( $product->get_id() ), $thumb_size );
				$image_output_open .= '<div class="giftwrap_thumb">';
				if ( 'yes' === $show_link ) {
					$product_URL = $product->get_permalink();
					$image_output_open .= '<a href="' . esc_url( $product_URL ) . '">';
				}
				if ( 'yes' === $show_link ) {
					$image_output_close .= '</a>';
				}
				$image_output_close .= '</div>';
			}
			if ( $count ) {
				echo '<li class="' . esc_attr( $slug . $label ) . ' giftwrap_li';
				if ( $show_thumbs ) {
					echo ' show_thumb';
				} else {
					echo ' no_giftwrap_thumbs';
				}
				echo '"';
				if ( $width ) echo ' style="width:' . esc_attr( $width ) . 'px';
				echo '"><input type="radio" name="wcgwp_product' . esc_attr( $label ) . '" id="' . esc_attr( $slug . $label ) . '" value="' . esc_attr( $product->get_id() ) . '"' . ( $wrap_count == 0 ? 'checked' : '' ) . '>';
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
				echo '<li class="giftwrap_li';
				if ( $show_thumbs ) {
					echo ' show_thumb';
				} else {
					echo ' no_giftwrap_thumbs';
				}
				echo '"><label for="' . esc_attr( $slug . $label ) . '" class="giftwrap_desc singular_label"><span class="giftwrap_title"> ';
				echo wp_kses_post( $product->get_title() ) . '</span> ' . wp_kses_post( $price_html ) . '</label>';
				echo wp_kses_post( $image_output_open ) . $product_image . wp_kses_post( $image_output_close );
				echo '</li><input type="hidden" name="wcgwp_product' . esc_attr( $label ) . '" value="' . esc_attr( $product->get_id() ) . '">';
			}
			++$wrap_count;
		} ?>
	</ul>
	<?php if ( (int) get_option( 'wcgwp_textarea_limit' ) > 0 ) { ?>
	<div class="wc_giftwrap_notes_container">
		<label for="giftwrapper_notes<?php esc_attr_e( $label ); ?>"><?php echo wp_kses_post( apply_filters( 'wcgwp_add_wrap_message', __( 'Add Gift Wrap Message:', 'woocommerce-gift-wrapper' ) ) ); ?></label>
		<textarea name="wcgwp_note<?php esc_attr_e( $label ); ?>" id="giftwrapper_notes<?php esc_attr_e( $label ); ?>" cols="50" rows="4" maxlength="<?php esc_attr_e( get_option( 'wcgwp_textarea_limit', '1000' ) ); ?>" class="wc_giftwrap_notes"></textarea>
	</div>
	<?php } ?>
	<?php wp_nonce_field( 'wcgwp_order_wrap', 'wcgwp_order_wrap_nonce' ); ?>
	<button type="submit" id="cart_giftwrap_submit" class="button btn alt giftwrap_submit wcgwp-submit giftwrap_submit<?php esc_attr_e( $label ); ?> giftwrap_submit_cart replace_wrap fusion-button fusion-button-default edgtf-btn" name="wcgwp_submit<?php esc_attr_e( $label ); ?>">
		<?php echo wp_kses_post( apply_filters( 'wcgwp_add_wrap_button_text', __( 'Add Gift Wrap to Order', 'woocommerce-gift-wrapper' ) ) ); ?>
	</button>

</form>