<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Base as Widget_Base;

defined( 'ABSPATH' ) || exit;

class Gift_Wrapper_Elementor_Widget extends Widget_Base {

	/**
	 * Retrieve Gift Wrapper widget name
	 *
	 * @access public
	 * @return string Widget name
	 */
	public function get_name() {

		return 'wcgwp-elementor';

	}

	/**
	 * Retrieve Gift Wrapper widget title
	 *
	 * @access public
	 * @return string Widget title
	 */
	public function get_title() {

		return 'Gift Wrapper';

	}

	/**
	 * Retrieve Gift Wrapper widget icon
	 *
	 * @access public
	 * @return string Widget icon
	 */
	public function get_icon() {

		// https://elementor.github.io/elementor-icons/
		return 'eicon-product-stock';

	}

	/**
	 * Retrieve the list of categories the Gift Wrapper widget belongs to
	 *
	 * Used to determine where to display the widget in the editor
	 *
	 * @access public
	 * @return array Widget categories
	 */
	public function get_categories() {

		return apply_filters( 'wcgwp_elementor_get_categories', [ 'woocommerce-elements' ] );

	}

	/**
	 * Retrieve the list of scripts the Gift Wrapper widget requires
	 *
	 * @access public
	 * @return array scripts
	 */
	public function get_script_depends() {

		return [ 'wc-cart', 'wc-add-to-cart', 'wcgwp-modal', 'wcgwp-cart' ];

	}

	/**
	 * Retrieve the list of CSS files the Gift Wrapper widget requires
	 *
	 * @access public
	 * @return array styles
	 */
	public function get_style_depends() {

		return [ 'wcgwp-css', 'wcgwp-modal-css' ];

	}

	public function get_keywords() {

		return [ 'gift', 'wrap', 'wrapper', 'wrapping', 'cadeau', 'regalo' ];

	}

	/**
	 * Register gift wrapper widget controls
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings
	 *
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Gift Wrapper Settings', 'woocommerce-gift-wrapper' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'wcgwp_cat_id',
			[
				'label' => __( 'Gift Wrap Category', 'woocommerce-gift-wrapper' ),
				'type' => Controls_Manager::SELECT,
				'options' => WC_Gift_Wrap()->settings->product_cats(),
			]
		);

		$this->add_control(
			'wcgwp_cart_display',
			[
				'label'     => __( 'How should options be displayed?', 'woocommerce-gift-wrapper' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'modal'     => __( 'Modal/Popup', 'woocommerce-gift-wrapper' ),
					'slide'     => __( 'SlideToggle - uses jQuery', 'woocommerce-gift-wrapper' ),
					'checkbox'  => __( 'Checkbox', 'woocommerce-gift-wrapper' ),
				],
				'default'   => 'modal',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render Gift Wrapper widget output on the frontend
	 *
	 * Written in PHP and used to generate the final HTML
	 *
	 * @access protected
	 * @return void
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( ! apply_filters( 'wcgwp_continue_gift_wrap_action', true, $settings ) ) {
			return;
		}

		if ( isset( $settings['wcgwp_cat_id'] ) ) {
			$products = wcgwp_get_products( esc_attr( $settings['wcgwp_cat_id'] ) );
		} else {
			$products = wcgwp_get_products();
		}

		$display = $settings['wcgwp_cart_display'] ?? get_option( 'wcgwp_cart_display', 'modal' );

		// Get the unique alphanumeric Elementor widget ID for each placement
		$id = $this->get_id();

		?>
		<div id="wcgwp-elementor-<?php esc_attr_e( $id ); ?>" class="wc-giftwrap wcgwp-elementor">

			<?php

			// Checkbox style
			if ( 'checkbox' === $display ) {

				wc_get_template(
				'wcgwp/v6/peri-cart/checkbox.php',
					[
						'label'         => $id,
						'products'      => $products,
					], '', GIFTWRAPPER_PLUGIN_DIR . 'templates/'
				);

			} else if ( 'slide' === $display ) {

				wc_get_template(
				'wcgwp/v6/peri-cart/slide.php',
					[
						'label'         => $id,
						'products'      => $products,
						'show_thumbs'   => wcgwp_show_thumbs(),
					], '', GIFTWRAPPER_PLUGIN_DIR . 'templates/'
				);

			// Modal style
			} else {

				wc_get_template(
					'wcgwp/v6/peri-cart/modal.php',
					[
						'label'       => $id,
						'products'    => $products,
						'show_thumbs' => wcgwp_show_thumbs(),
					], '', GIFTWRAPPER_PLUGIN_DIR . 'templates/'
				);

			} ?>
			</div>
		<?php

	}

}