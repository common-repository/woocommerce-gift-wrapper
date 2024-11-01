( function( $ ) {
	$( document ).ready( function() {
		// If only one wrap allowed in cart, show alert
		if ( "no" === wcgwpSlide.number ) {
			document.addEventListener( "click", function( e ) {
				if ( e.target.matches( ".wcgwp_slideout .replace_wrap" ) ) {
					const cartItem = document.getElementsByClassName( "wcgwp-wrap-product" );
					if ( cartItem.length ) {
						if ( window.confirm( wcgwpSlide.replaceText ) ) {
							return true;
						}
						e.preventDefault();
					}
				}
			});
		}
		// Toggle slide down
		$( ".giftwrap_header_wrapper" ).on( "click", ".wcgwp-slide-toggle", function(e) {
			e.preventDefault();
			const target = $(this).attr( "data-target" );
			$( ".slideout" + target ).slideToggle( 300 );
			$( ".gift-wrapper-cancel." + target ).show();
			$( ".show_giftwrap" + target ).hide();
		});
		// Hide slide down (Cancel)
		$( ".gift-wrapper-cancel" ).on( "click", "button", function() {
			const target = $( this ).attr( "data-target" );
			if ( ! this.classList.contains( "cancel_giftwrap" + target ) ) {
				return;
			}
			$( ".slideout" + target ).slideToggle(300);
			$( ".gift-wrapper-cancel." + target ).hide();
			$( ".show_giftwrap" + target ).show();
		});
	});
	// Remove peri-cart gift wrap prompts when *wrappables* removed from cart (AJAX)
	$( document ).ajaxComplete( function (event, xhr, settings) {
		if (
			xhr &&
			4 === xhr.readyState &&
			200 === xhr.status &&
			settings.url &&
			settings.url.includes( "wc-ajax=get_refreshed_fragments" )
		) {
			$.ajax( {
				type: "POST",
				url: wcgwpSlide.ajaxurl,
				data: {
					action: "wcgwp_remove_from_cart",
				},
				success: function (response) {
					if ( ! response.data ) {
						return;
					}
					$( ".giftwrap_header_wrapper" ).show();
					if ( true === response.data.hide ) {
						$( ".giftwrap_header_wrapper" ).hide();
					}
				}
			});
		}
	});
}( jQuery ) );