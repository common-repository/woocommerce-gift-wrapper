=== Gift Wrapper for WooCommerce ===
Plugin URI: https://giftwrapper.app/
Contributors: littlepackage
Donate link: https://paypal.me/littlepackage
Tags: woocommerce, wrap, gift, add-on, upsell
Requires at least: 6.0
Requires PHP: 7.4
Tested up to: 6.6
Stable tag: 6.1.10
License: GPLv3 or later

Holidays and birthdays are always coming! Offer to gift wrap your customer's purchase, per order, on the WooCommerce cart and checkout pages.

== Description ==

### The Gift Wrapper is no longer in active development. There are no plans to make it compatible with WC blocks. If you are a developer and would like to contribute, please get in touch. ###

The Gift Wrapper treats your wrapping service as a WooCommerce product, allowing it to be inventoried, priced, discounted and taxed separately. Create and offer as many gift wraps as you like, where you like. Not just for gift wrap - use Gift Wrapper for any (inventoriable, taxable and/or discountable) cart add-ons! Examples: add condiments to a food order, or accessories to an electronics order. If you need more features and functionality such as per-product wrap options, [check out the PLUS version of The Gift Wrapper](https://www.giftwrapper.app "Gift Wrapper Plus plugin") from Little Package (not to be confused with a similarly-named but different plugin sold by Woocommerce since March 2020).

= Some Features =

* Create a simple gift wrap (or other add-on type) option form on the cart and/or checkout page, or go all out with robust gift wrapping offerings
* Set individual prices, descriptions, and images for wrapping types
* Wrap can be inventoried, discounted and/or taxed like other WooCommerce products
* Show or hide gift wrap images in cart/checkout
* Static (slide-down), checkbox, or modal view of gift wrap options on cart and checkout pages
* Accept additional gift wrap note (optional) with gift wrap selection
* Get notice of the customer's intended gift wrap message on email order notification and on the order page - customer also receives confirmation on receipt
* Elementor Pro cart/checkout page widget
* Fully CSS-tagged and templated for your customizing pleasure
* Remember the paid version of this plugin (Gift Wrapper PLUS for WooCommerce) has way more features and likely does what you need!
* If you have suggestions, or find a bug, please get in touch.

= GIFT WRAPPER PLUS (paid version) features =

* Per-product gift wrapping modal/slideout options on product pages, or a simple per-product “add gift wrap for $x” checkbox
* Per-product gift wrapping settings (control offerings per-product)
* Per-product wrap can appear as product attribute or as separate line item (for separate taxes, inventory etc.) in cart.
* Add different gift wrap products to each item in cart, if desired
* Add more than one wrap products to any item in the cart, if desired
* Add/edit/and remove gift wrap to/from products inside cart, per-product
* Control ratio of product:wrap in cart line-item wrap offerings
* Exclude products from wrap, and/or exclude entire product categories from wrap
* If using modal(s), option to use any of 1500+ possible entrance/exit animations, courtesy animate.css
* Elementor Pro cart/checkout and product page compatibility
 Compatible with WooCommerce Mix and Match Products and WooCommerce Composite Products
* Compatibility with CartPops AJAX fly-out cart

= Support future development =

I need your support & encouragement! If you have found this free plugin useful, and *especially if you have benefited commercially from using Gift Wrapper*, please consider donating to support the plugin's future on the WP repository:

[paypal.me/littlepackage](https://paypal.me/littlepackage?country.x=US&locale.x=en_US "Send Caroline a small tip")

I understand you have a budget and might not be able to afford to pay the developer (me) a small tip in thanks. Maybe you can **leave a positive review**?

[Please leave a review of the Gift Wrapper free version](https://wordpress.org/support/plugin/woocommerce-gift-wrapper/reviews "Leave a Review of the Gift Wrapper")

[Purchase Gift Wrapper Plus](https://www.giftwrapper.app "Gift Wrapper Plus plugin")

= Translations =

Take a moment and [help Translate the Gift Wrapper into your language](https://translate.wordpress.org/projects/wp-plugins/gift-wrapper/)

== Installation ==

= To install plugin =

1. Upload the entire "woocommerce-gift-wrapper" folder to the "/wp-content/plugins/" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Visit WooCommerce->Settings, then look for the Gift Wrapper tab to set your plugin preferences.
4. Follow the instructions there and review your settings.

Green Geeks has created a [good overview of Gift Wrapper installation and set up](https://www.greengeeks.com/tutorials/article/woocommerce-gift-wrap-service-wordpress/), which applies to the free version of Gift Wrapper.

= To remove plugin: =

1. Deactivate plugin through the 'Plugins' menu in WordPress
2. Delete plugin through the 'Plugins' menu in WordPress
3. NOTE: Your settings will be deleted from your WP database when the plugin is deleted

== Frequently Asked Questions ==

= Gift Wrap options don't show =
Things to check:

1. Is WooCommerce activated and configured, and are all the theme files current (check WooCommerce->System Status if unsure)
2. Is the Gift Wrapper plugin activated?
3. Are you using WooCommerce version 7.2 or newer? Time to upgrade!
4. Are you using WooCommerce block-style (Gutenberg) cart/checkout? Sorry, Gift Wrapper is not compatible with blocks (yet) The plugin is intended to work with the old-school WC cart/checkout shortcodes.
5. Have you created at least one WooCommerce gift wrap product, given it a price (even if $0), saved it to a WooCommerce product category, and entered that product category in your Gift Wrapper settings?
6. Does the your-theme-or-child-theme/woocommerce/cart/cart.php file include at least one of the following hooks?

`do_action('woocommerce_before_cart');` or
`do_action('woocommerce_before_cart_collaterals');` or
`do_action('woocommerce_after_cart');` or

6**a**. Does the your-theme-or-child-theme/woocommerce/checkout/form-checkout.php file include at least one of the following hooks?

`do_action('woocommerce_before_checkout_form');` or
`do_action('woocommerce_after_checkout_form');`

Due to third-party theme and plugin overrides, these hooks might be moved or removed. If you can't find any of these hooks in your WooCommerce installation, you are missing a crucial hook(s) to the functioning of this plugin. Try using a different location for the "Where to Show Gift Wrapping" in the plugin settings, and if that doesn't work, try the Storefront or TwentyTwentyTwo theme and disable all plugins except WooCommerce & Gift Wrapper to rule out theme/plugin interference.

Gift Wrapper Plus (paid upgrade) allows for easy additional hook placements - just type in the name of the hook you want used.

= Why isn't gift wrapping added when I click the button in the cart? =

Have you added a gift wrapping as a WooCommerce product? This plugin works by creating a product that virtually represents gift wrapping. It is up to you whether that product is visible in the catalog or not, and how fleshed-out you make the product description. But there needs to be a product, and it needs to be in a category whether or not you make more than one wrapping types. That product category is used in the Gift Wrapping settings.

Finally, check your browser console for JavaScript errors. This plugin uses JavaScript and sometimes depends sometimes other plugins' broken JavaScript can interfere with ours.

= Why make more than one type of wrapping? =

Maybe you want to offer "Winter Holiday" wrapping and "Birthday" wrapping separately, or maybe you have other types of gift wrap service: wrapping paper or boxes you use that may incur different prices or shipping rules. It's up to you whether or not you make more than one wrapping product. You don't have to.

= How can I style the appearance? =

I've added CSS tags to every aspect of the cart and checkout gift wrap forms so you can style away. Please if you do not know basic CSS, hire a developer to do this for you. We need jobs, too!

If you would like to change the HTML structure of gift wrap lists and modals, you can use the Gift Wrapper templating system to do that easily.

You will need to create a folder called woocommerce in your theme — or better yet — child theme folder. Inside that folder, create another folder called wcgwp. Move any overwritten plugin template files into this wp-content/theme/woocommerce/wcgwp folder, and your changes will be visible. [Read more information on WooCommerce templating here](https://www.skyverge.com/blog/overriding-templates-in-woocommerce-extensions/).

= Can I change the giftwrap thumbnail size? =

Yes, use the 'wcgiftwrap_change_thumbnail' hook in your (child) theme functions.php file as follows:

`function my_custom_thumbnail_size( $thumbnail ) {
	$thumbnail = 'medium'; // default WP sizes are 'thumb', 'medium', 'medium_large', and 'large'
	return $thumbnail;
}
add_filter( 'wcgiftwrap_change_thumbnail', 'my_custom_thumbnail_size', 10, 1 );`

This is just an example. Change 'medium' to the size desired, using an existing Wordpress image size slug. You can also use an image size slug created by an active plugin or theme. If thumbnails do not show in your size afterward, it's possible you need to clear caches or run thumbnail regeneration.

Gift Wrapper Plus (paid upgrade) allows users to set thumbnail size in the settings.

= I don't want more than one wrapping added to the cart! =

Yeah, that could be a problem. But rather than hard-code against that possibility I leave the settings to you, and for good reason. If you don't want more than one wrapping possible, make sure to set your wrapping product to "sold individually" under Product Data->Inventory in your Product editor. If you do this make sure your customer has a way to remove the gift wrapping from the cart on small screens, as sometimes responsive CSS designs remove the "Remove from Cart" button from the cart table for small screens.

= I don't want gift wrap to incur a shipping cost =

To prevent this happening, I recommend you set up your gift wrap products as WooCommerce "virtual" products (virtual but not downloadable). If setting them up as regular or variable products, make sure to arrange the shipping settings so they don't incur surprise shipping costs.

= I don't want to show gift wrapping in my catalog =

Visit your gift wrap product (WooCommerce product editor screen) and set Catalog Visibility to "hidden" in the upper right corner near the blue update button. If you have more than one gift wrap product, do this for each one.

= I don't want to show a specific element on screen =

This plugin is heavily CSS-tagged. If you don't want to show a part of what the Gift Wrapper displays, add custom CSS to your Wordpress theme settings, Wordpress theme css (usually style.css), or - better yet - Wordpress child theme CSS file (style.css). Wordpress also allows CSS to be added in the Customizer.

An example might be:

Let's hide the gift note textarea/textbox. Add this CSS to your theme:

`.wcgwp-note-container textarea {display: none;}`

or

`.wcgwp-note {display: none;}`

Both lines of CSS should work (version > 6.0). I cannot support all the requests for free custom theme help any longer! Please study up CSS or hire a developer to help you make custom theme and plugin modifications. [WooCommerce has provided some recommendations for where to seek help](https://wordpress.org/support/topic/finding-a-reliable-woocommerce-developer-or-agency/). Thank you for understanding.

To hide the text "We offer the following gift wrap options:," use CSS or the 'wcgwp_hide_details' filter hook to hide it. To use the hook, add the following code to your functions.php file:

`add_filter( 'wcgwp_hide_details', '__return_true' );`

The CSS would be:

`.wcgwp-details {display: none;}`

You can also adjust the HTML output using the template system built into Gift Wrapper.

= How can I hide gift wrapping when there are only virtual products in the cart? =

Easy, add the following line of code to your (child) theme functions.php file:

`add_filter( 'giftwrap_exclude_virtual_products', '__return_true' );`

If you're unfamiliar with how to edit the functions.php file, add this code using the [Code Snippets plugin](https://wordpress.org/plugins/code-snippets/).

= How can I remove the COD payment option if gift wrap in cart (purchase is probably a gift) ? =

Easy, add the following line of code to your (child) theme functions.php file:

`add_filter( 'wcgwp_remove_cod_gateway', '__return_true');`

= How can I make this plugin work with WooCommerce Mix & Match or WooCommerce Composite Products? =

Easy. The [Plus version of this Gift Wrapper](https://www.giftwrapper.app "Gift Wrapper Plus plugin") is compatible with the WooCommerce Mix & Match and WooCommerce Composite Products plugins.

= I would like this plugin in my language, or to say something different on screen =

There are SO many ways to customize this plugin - all the ways! Your developer will have an easy time customizing this exactly how you want it, using filter hooks, templates and/or string translations.

The [PLUS version of this plugin](https://www.giftwrapper.app "Gift Wrapper Plus plugin for WooCommerce") allows for easy string translation by using a settings panel -- just type what you want it to say.

**Filter hooks**
The easiest option is probably to use Wordpress filter hooks included with most strings in this plugin. Here's an example for changing the "Add Gift Wrap?" text:

`function my_change_wrap_prompt( $prompt ) {
	$prompt = "Would you like to wrap this?";
	return $prompt;
}
add_filter( 'wcgwp_add_wrap_prompt', 'my_change_wrap_prompt', 11, 1 );`

Another less specific hook can be used to catch any string. Use the exact original string to match, then replace it:

`function my_custom_wrap_strings( $string ) {
	if ( 'Cancel gift wrap' === $string ) { // Check for default string
		$string = 'Cancelar envolver regalo'; // Replace default string
	} else if ( 'Cancel' === $string ) { // Check for default string
		$string = 'Cancelar'; // Replace default string
	} else if ( 'Note' === $string ) { // Check for default string
		$string = 'Nota'; // Replace default string
	} else if ( 'Note fee' === $string ) { // Check for default string
		$string = 'Tarifa de nota'; // Replace default string
	} // etc
	return $string;
}
add_filter( 'wcgwp_filter_string', 'wcgwp_filter_strings', 11, 1 );`

Now the text will say "Would you like to wrap this?" This PHP code could be added using the [Code Snippets](https://wordpress.org/plugins/code-snippets/) plugin if you do not have a child theme and are not comfortable editing your child theme functions.php file.

**Translation**
This plugin comes ready with a .POT file. If you aren't already familiar with [localisation (translation) of Wordpress plugins and themes, you can learn more here](https://premium.wpmudev.org/blog/how-to-translate-a-wordpress-plugin/). You can add .PO files to the /lang folder of this plugin to change it to your language, or even to just adjust the English currently used.

To change what this plugin says on screen, create PO/MO file(s) in your language. If your site is in English (US), then you would be creating a PO file called *gift-wrapper-en_US.po* and putting it in the /lang/ folder inside the Gift Wrapper plugin folder (/wp-content/plugins/gift-wrapper/lang/). If your site is in French (France), your PO file would be /wp-content/plugins/gift-wrapper/lang/woocommerce-gift-wrapper-fr_FR.po. Note in this case, you would be editing or overwriting the existing po file for French.

I recommend [Poedit](https://poedit.net/) to get string translations done quickly and simply. Note: translation occurs after filters (mentioned above) are run.

**Templating**
If you want to do more about styling gift wrap presentation, this plugin includes a [templating system](https://www.giftwrapper.app/documentation/#templates).

= How can I translate the "Giftwrap Details" (found in Gift Wrapper settings) when using WPML? =

This string is saved in the Wordpress options database table and so it takes a little extra work for WPML to find it. Follow [these instructions in the WPML documentation](https://wpml.org/documentation/getting-started-guide/string-translation/finding-strings-that-dont-appear-on-the-string-translation-page/#strings-arent-selected-for-translation) to find the 'wcgwp_details' database value and translate it.

= The popup (modal) doesn't work with my theme =
Most likely this is due to your Wordpress theme conflicting with this plugin. Oftentimes, themes use aggressive CSS z-indexing to make page sections "float". This can cause third-party modals (from any plugin, not just this one) to fail. If you do not know how to correct z-index issues with some custom CSS, please bring this issue up with your theme author and/or your developer. Usually one short line of CSS code can fix this issue. I'd share it here but it hugely depends on which theme you are using.

= This plugin doesn't look good or work with my theme =
[Learn more about possible theme issues](https://www.giftwrapper.app/documentation/#theme-issues). Gift Wrapper works with many themes, both paid and free, and is offered gratis and as is. Some themes just require very minor tweaking with a line or two of CSS for cooperation. If you have suggestions for how to make it work every time for your theme, we will consider hard-coding in your theme fixes. However, we are not responsible nor for hire to make this plugin work with every theme out there. Thank you for understanding.

= Other problem =
Please [write for support](https://wordpress.org/support/plugin/woocommerce-gift-wrapper/) before leaving negative feedback! Tickets usually get replies within 24-48 hours. This [pinned ticket contains solid advice](https://wordpress.org/support/topic/before-you-post-please-read/) about how to troubleshoot further, and how to request help (to get quick help).

== Screenshots ==

1. Screenshot of the General Options tab (WooCommerce -> Settings -> Gift Wrapping submenu)
2. Screenshot of the Order Wrapping tab options
3. Animated GIF of cart, click of prompt causes gift wrap options to slide down
4. Animated GIF of cart, click of prompt opens modal pop-up window with gift wrap options
5. Animated GIF of checkbox gift wrap options clicked, causing add-to-cart
6. Cart with two prompt placements, before and after cart

== Upgrade Notice ==

= 6.0.0 =
* Version 5 to version 6 is a MAJOR change! Things can break. Make sure to take backups before upgrading, and test thoroughly after upgrading.
* New v6 templates allow for AJAX add gift wrap to cart, more seamless transition to Gift Wrapper Plus. If you are using Gift Wrapper template overrides in your theme, you will want to make backups and update to 6.0 with caution, testing the cart with your theme overrides.

== Changelog ==

= 6.1.10 =
* Fix - JS for vanilla Modal display

= 6.1.9 =
* Tweak - Declare INcompatibilty with WooCommerce `cart_checkout_blocks` feature. This was erroneously declared compatible in 6.1.8, which doesn't necessarily affect function but doesn't reflect the truth. This free plugin is being removed from active development.

= 6.1.8 =
* Tweak - Declare compatibilty with WooCommerce `cart_checkout_blocks` (HPOS) feature
* Testing with WP 6.6, WC 9.1, PHP 8.3
* Increase min PHP to 7.4

= 6.1.7 =
* Tweak - test for product in cart before querying available gift wraps
* Testing with WC 8.8

= 6.1.6 =
* Tweak - provide opportunity to translate the "Gift wrap was added to your car." string using plugin or filter hook
* Testing with WP 6.5 and WC 8.7

= 6.1.5 =
* Fix - Fatal error in PHP < 8 where Exception class is unassigned

= 6.1.4 =
* Fix - Remove breaking use of woocommerce_update_cart_action_cart_updated filter hook

= 6.1.3 =
* Fix - Default hide deactivation Thickbox on plugins.php

= 6.1.2 =
* Fix - For themes like Martfury watching for 'added_to_cart' trigger, set a $thisbutton value
* Tweak - Add optional feedback UI for users deactivating plugin
* Tweak - Hide language settings not used in the free version of this plugin

= 6.1.1 =
* Fix - Save default text strings
* Tweak - Don't refresh cart when wrap can't be added anyway, line 294 class-gift-wrapper-wrapping.php

= 6.1.0 =
* Elementor cart/checkout page support!
* Remove sometimes-confusing PRO settings display and move CTA
* Tweak - prevent editing of gift wrap quantity when no more than one allowed
* Tweak - empty WC notice before running AJAX add_to_cart so that notices don't pile up
* Testing with WC 8.2

= 6.0.8 =
* Testing with WooCommerce v8.2
* Update contributors

= 6.0.7 =
* Testing with WooCommerce v7.8

= 6.0.6 =
* If language string array empty, re-save defaults

= 6.0.5 =
* Make $wrap_in_cart public
* Uncheck checkbox if wrap removed from cart using cart X button
* Default suppress_filters to 'false', even if unclear WC is using it in wc_get_products()
* Normalize theme templates completely with Plus version peri-cart templates, using get_strings() for strings

= 6.0.4 =
* Declare compatibility with WooCommerce HPOS
* Testing with WC 7.7.0

= 6.0.3 =
* Update v6/js/wcgwp-cart.js to better handle situations where only one gift wrap option available

= 6.0.2 =
* String translation text domain corrected to 'woocommerce-gift-wrapper'

= 6.0.1 =
* Don't re-save 'wcgwp_category_id' option when filtered by WPML
* CSS changes for Divi theme - z-indexing can be challenging to work around
* Preserve gift wrap note/message in case of add-to-cart error, customer doesn't have to re-type

= 6.0.0 =
* New v6 templates allow for AJAX add gift wrap to cart, more seamless transition to Gift Wrapper Plus
* Checkbox style cart/checkout gift wrap placements
* Disallow gift product quantity changes when no more should be allowed
* Testing with WooCommerce v7.6