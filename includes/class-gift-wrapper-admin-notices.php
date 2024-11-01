<?php defined( 'ABSPATH' ) || exit;

class The_Gift_Wrapper_Admin_Notices {

	private static $outdated_files = [];

	/**
	 * Constructor
	 */
	 public function __construct() {

		// Alert for folks who haven't set up a wrap category
		add_action( 'admin_notices',                [ $this, 'setup_error_notice' ] );

		// Alert for folks with outdated template files
		add_action( 'admin_notices',                [ $this, 'template_file_check_notice' ] );

	}

	/**
	 * Provide user with heads up if gift wrap category is not set
	 *
	 * @return void
	 */
	 public function setup_error_notice() {

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen->id !== 'woocommerce_page_wc-settings' && $screen->id !== 'woocommerce_page_wc-status' ) {
				return;
			}
		}

		$wrap_cat_id = wcgwp_get_wrap_category_id();
		$hooks = (array) get_option( 'wcgwp_cart_hook', [] );

		// Admin doesn't have a gift wrap category set! But maybe they've set hook placements!
		if ( ! $wrap_cat_id || ( 'none' === $wrap_cat_id && ! in_array( 'none', $hooks ) ) ) { ?>
            <div id="message" class="notice notice-error is-dismissible">
                <p><?php echo wp_kses_post( sprintf( __( 'Gift Wrapper is not set up properly yet. Please choose the WooCommerce product category that contains your gift wrap products in the <a href="%s">Gift Wrapper settings</a>.', 'woocommerce-gift-wrapper' ), admin_url( 'admin.php?page=wc-settings&tab=gift-wrapper' ) ) ); ?></p>
            </div>
		<?php }

	}

	/**
	 * If WC admin page, send core templates to check_template_outdated() method
	 * for version-checking
	 *
	 * @return void
	 */
	public static function template_file_check_notice() {

		$screen = get_current_screen();
		if ( $screen->id !== 'woocommerce_page_wc-settings' && $screen->id !== 'woocommerce_page_wc-status' ) {
			return;
		}
		$core_templates = WC_Admin_Status::scan_template_files( GIFTWRAPPER_PLUGIN_DIR . '/templates' );
		$outdated = self::check_template_outdated( $core_templates );
		if ( $outdated ) {
			gift_wrapper_outdated_template_notice( self::$outdated_files );
		}

	}

	/**
	 * Compare plugin template files with any user overrides,
	 * looking for outdated versions in order to warn user
	 *
	 * @param  array $files
	 * @return boolean
	 */
	public static function check_template_outdated( $files ) {

		if ( empty( $files ) ) {
			return false;
		}
		$theme_file = false;
		$outdated   = false;
		foreach ( $files as $file ) {

			if ( file_exists( get_stylesheet_directory() . '/' . WC()->template_path() . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/' . WC()->template_path() . $file;
			} elseif ( file_exists( get_template_directory() . '/' . WC()->template_path() . $file ) ) {
				$theme_file = get_template_directory() . '/' . WC()->template_path() . $file;
			}

			if ( false !== $theme_file ) {
				$core_version  = WC_Admin_Status::get_file_version( GIFTWRAPPER_PLUGIN_DIR . '/templates/' . $file );
				$theme_version = WC_Admin_Status::get_file_version( $theme_file );

				if ( $core_version && $theme_version && version_compare( $theme_version, $core_version, '<' ) ) {
					$outdated = true;
					if ( ! in_array( $theme_file, self::$outdated_files ) ) {
						self::$outdated_files[] = $theme_file;
					}
				}
			}

		}
		return $outdated;

	}

}

/**
 * Provide user with heads up if template has been updated
 *
 * @param array $outdated_files
 * @return void
 */
function gift_wrapper_outdated_template_notice( $outdated_files ) {

	if ( defined( 'DISABLE_NAG_NOTICES' ) && 'DISABLE_NAG_NOTICES' == true ) {
		return;
	}

	$theme = wp_get_theme();
	?>
	<div id="message" class="updated woocommerce-message notice notice-error is-dismissible">
		<p>
			<?php /* translators: %s: theme name */ ?>
			<strong><?php printf( wp_kses( __( 'Your theme (%s) contains outdated copies of some WooCommerce Gift Wrapper template files.' , 'woocommerce-gift-wrapper' ), [ 'a' => [ 'href' => [] ] ], esc_html( $theme['Name'] ) ) ); ?></strong><br />
			<?php esc_html_e( 'The following files should be updated to reflect parent template file changes to ensure they are secure and compatible with the current version of Gift Wrapper.', 'woocommerce-gift-wrapper' ); ?>  
			<ol>
			<?php foreach ( $outdated_files as $file ) { ?>
				<li><?php esc_html_e( $file ); ?></li>
			<?php } ?>
			</ol>
			<?php esc_html_e( 'If you copied over a template file to your theme to change something, you will need to copy the new version of the template and apply your changes again.', 'woocommerce-gift-wrapper' ); ?>
		</p>

	</div>

<?php }