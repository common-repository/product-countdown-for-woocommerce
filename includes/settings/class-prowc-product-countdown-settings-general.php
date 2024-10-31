<?php
/**
 * Product Time Countdown for WooCommerce - General Section Settings
 *
 * @version 1.4.1
 * @since   1.0.0
 * @author  ProWCPlugins
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'ProWC_Product_Countdown_Settings_General' ) ) :

class ProWC_Product_Countdown_Settings_General extends ProWC_Product_Countdown_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public $id   = '';
	public $desc;
	public function __construct() {
		$this->desc = __( 'General', 'product-countdown-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * add_settings.
	 *
	 * @version 1.4.1
	 * @since   1.0.0
	 * @todo    [dev] split into separate settings sections
	 * @todo    [dev] maybe set "Update rate" to higher min (e.g.: 1000)
	 */
	function get_settings() {
		return array(
			array(
				'title'     => __( 'Product Time Countdown Options', 'product-countdown-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'prowc_product_countdown_options',
			),
			array(
				'title'     => __( 'Product Time Countdown for WooCommerce', 'product-countdown-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Enable plugin', 'product-countdown-for-woocommerce' ) . '</strong>',
				'id'        => 'prowc_product_countdown_enabled',
				'default'   => 'yes',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'prowc_product_countdown_options',
			),
			array(
				'title'     => __( 'General Options', 'product-countdown-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'prowc_product_countdown_general_options',
			),
			array(
				'title'     => __( 'Template', 'product-countdown-for-woocommerce' ),
				'desc_tip'  => __( 'You can use HTML and/or shortcodes here.', 'product-countdown-for-woocommerce' ),
				// translators: Placeholder %s represents the actual time counter.
				'desc'      => sprintf( __( '%s is replaced by actual time counter.', 'product-countdown-for-woocommerce' ), '<code>%s</code>' ),
				'id'        => 'prowc_product_countdown_format',
				'default'   => '%s left',
				'type'      => 'textarea',
				'css'       => 'width:100%;',
				'prowc_ptc_raw' => true,
			),
			array(
				'title'     => __( 'Time format', 'product-countdown-for-woocommerce' ),
				'desc_tip'  => __( 'Ignored if "Human readable format" option below is enabled.', 'product-countdown-for-woocommerce' ),
				// translators: Placeholders represent the replaced values: {weeks}, {days}, {hours}, {minutes}, {seconds}.
				'desc'      => sprintf( __( 'Replaced values: %s.', 'product-countdown-for-woocommerce' ),
					'<code>{weeks}</code>, <code>{days}</code>, <code>{hours}</code>, <code>{minutes}</code>, <code>{seconds}</code>' ),
				'id'        => 'prowc_product_countdown_time_format',
				'default'   => '{hours}:{minutes}:{seconds}',
				'type'      => 'text',
				'css'       => 'width:100%;',
			),
			array(
				'desc'      => __( 'Upper limit', 'product-countdown-for-woocommerce' ),
				'id'        => 'prowc_product_countdown_time_format_upper_limit',
				'default'   => 'hours',
				'type'      => 'select',
				'class'     => 'wc-enhanced-select',
				'options'   => array(
					'seconds' => __( 'Seconds', 'product-countdown-for-woocommerce' ),
					'minutes' => __( 'Minutes', 'product-countdown-for-woocommerce' ),
					'hours'   => __( 'Hours', 'product-countdown-for-woocommerce' ),
					'days'    => __( 'Days', 'product-countdown-for-woocommerce' ),
					'weeks'   => __( 'Weeks', 'product-countdown-for-woocommerce' ),
				),
			),
			array(
				'title'     => __( 'Human readable format', 'product-countdown-for-woocommerce' ),
				'desc'      => __( 'Enable', 'product-countdown-for-woocommerce' ),
				// translators: Placeholder %s represents the function name 'human_time_diff()'.
				'desc_tip'  => sprintf( __( 'Will use %s function to display time.', 'product-countdown-for-woocommerce' ),
					'<a target="_blank" href="https://codex.wordpress.org/Function_Reference/human_time_diff"><code>human_time_diff()</code></a>' ),
				'id'        => 'prowc_product_countdown_format_human_time_diff',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'title'     => __( 'Style', 'product-countdown-for-woocommerce' ),
				'id'        => 'prowc_product_countdown_style',
				'default'   => 'font-size: xx-large; font-weight: bold;',
				'type'      => 'textarea',
				'css'       => 'width:100%;',
			),
			array(
				'title'     => __( 'Update rate', 'product-countdown-for-woocommerce' ),
				'desc'      => __( 'milliseconds', 'product-countdown-for-woocommerce' ),
				'id'        => 'prowc_product_countdown_update_rate_ms',
				'default'   => 1000,
				'type'      => 'number',
				'custom_attributes' => array( 'min' => 100 ),
			),
			array(
				'title'     => __( 'Reload page', 'product-countdown-for-woocommerce' ),
				'desc_tip'  => __( 'If enabled will reload page on time finished.', 'product-countdown-for-woocommerce' ),
				'id'        => 'prowc_product_countdown_reload_page',
				'default'   => 'no',
				'type'      => 'select',
				'class'     => 'wc-enhanced-select',
				'options'   => array(
					'yes'        => __( 'Reload', 'product-countdown-for-woocommerce' ),
					'yes_single' => __( 'Reload on single product pages only', 'product-countdown-for-woocommerce' ),
					'no'         => __( 'Do not reload', 'product-countdown-for-woocommerce' ),
				),
			),
			array(
				'title'     => __( 'Message on time finished', 'product-countdown-for-woocommerce' ),
				'desc_tip'  => __( 'Message will be visible on time finished on frontend instead of time counter.', 'product-countdown-for-woocommerce' ). ' ' .
					__( 'Can be empty.', 'product-countdown-for-woocommerce' ),
				'id'        => 'prowc_product_countdown_message_on_time_finished',
				'default'   => '',
				'type'      => 'textarea',
				'css'       => 'width:100%;',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'prowc_product_countdown_general_options',
			),
			array(
				'title'     => __( 'Position Options', 'product-countdown-for-woocommerce' ),
				'desc'      => apply_filters( 'prowc_product_countdown',
					'<em>' . sprintf( 
						// translators: Placeholder %s represents the link to the Pro version plugin.
						__( 'You will need %s plugin to change this section\'s settings.', 'product-countdown-for-woocommerce' ),
						'<a href="https://prowcplugins.com/downloads/product-time-countdown-for-woocommerce/" target="_blank">' .
							__( 'Product Time Countdown for WooCommerce Pro', 'product-countdown-for-woocommerce' ) . '</a>' ) . ' ' .
					sprintf( 
						// translators: Placeholder %s represents the shortcode [product_time_counter].
						__( 'In Pro version you can also use %s shortcode to display the counter.', 'product-countdown-for-woocommerce' ),
						'<code>' . '[product_time_counter]' . '</code>' ) . '</em>', 'settings_position_options' ),
				'type'      => 'title',
				'id'        => 'prowc_product_countdown_position_options',
			),
			array(
				'title'     => __( 'Position on single product page', 'product-countdown-for-woocommerce' ),
				'id'        => 'prowc_product_countdown_position',
				'default'   => 'woocommerce_single_product_summary',
				'type'      => 'select',
				'class'     => 'wc-enhanced-select',
				'options'   => array(
					'disable'                                   => __( 'Do not add', 'product-countdown-for-woocommerce' ),
					'woocommerce_before_single_product'         => __( 'Before single product', 'product-countdown-for-woocommerce' ),
					'woocommerce_before_single_product_summary' => __( 'Before single product summary', 'product-countdown-for-woocommerce' ),
					'woocommerce_single_product_summary'        => __( 'Inside single product summary', 'product-countdown-for-woocommerce' ),
					'woocommerce_after_single_product_summary'  => __( 'After single product summary', 'product-countdown-for-woocommerce' ),
					'woocommerce_after_single_product'          => __( 'After single product', 'product-countdown-for-woocommerce' ),
					'woocommerce_before_add_to_cart_form'       => __( 'Before add to cart form', 'product-countdown-for-woocommerce' ),
					'woocommerce_before_add_to_cart_button'     => __( 'Before add to cart button', 'product-countdown-for-woocommerce' ),
					'woocommerce_after_add_to_cart_button'      => __( 'After add to cart button', 'product-countdown-for-woocommerce' ),
					'woocommerce_after_add_to_cart_form'        => __( 'After add to cart form', 'product-countdown-for-woocommerce' ),
				),
				'custom_attributes' => apply_filters( 'prowc_product_countdown', array( 'disabled' => 'disabled' ), 'settings' ),
			),
			array(
				'desc'      => __( 'Position priority', 'product-countdown-for-woocommerce' ),
				'desc_tip'  => __( 'Change this if you want to move the timer inside the Position.', 'product-countdown-for-woocommerce' ) . ' ' .
						__( 'Existing priorities:', 'product-countdown-for-woocommerce' ) . '<br>' .
						__( '<em>Before single product:</em><br>notices - 10.', 'product-countdown-for-woocommerce' ) . '<br>' .
						__( '<em>Before single product summary:</em><br>sale flash - 10,<br>product images - 20.', 'product-countdown-for-woocommerce' ) . '<br>' .
						__( '<em>Inside single product summary:</em><br>title - 5,<br>rating - 10,<br>price - 10,<br>excerpt - 20,<br>add to cart - 30,<br>meta - 40,<br>sharing - 50.', 'product-countdown-for-woocommerce' ) . '<br>' .
						__( '<em>After single product summary:</em><br>product data tabs - 10,<br>upsell - 15,<br>related products - 20.', 'product-countdown-for-woocommerce' ),
				'id'        => 'prowc_product_countdown_position_priority',
				'default'   => 10,
				'type'      => 'number',
				'custom_attributes' => apply_filters( 'prowc_product_countdown', array( 'disabled' => 'disabled' ), 'settings' ),
			),
			array(
				'title'     => __( 'Position on archive (shop) pages', 'product-countdown-for-woocommerce' ),
				'id'        => 'prowc_product_countdown_loop_position',
				'default'   => 'disable',
				'type'      => 'select',
				'class'     => 'wc-enhanced-select',
				'options'   => array(
					'disable'                                 => __( 'Do not add', 'product-countdown-for-woocommerce' ),
					'woocommerce_before_shop_loop_item'       => __( 'Before product', 'product-countdown-for-woocommerce' ),
					'woocommerce_before_shop_loop_item_title' => __( 'Before product title', 'product-countdown-for-woocommerce' ),
					'woocommerce_shop_loop_item_title'        => __( 'Inside product title', 'product-countdown-for-woocommerce' ),
					'woocommerce_after_shop_loop_item_title'  => __( 'After product title', 'product-countdown-for-woocommerce' ),
					'woocommerce_after_shop_loop_item'        => __( 'After product', 'product-countdown-for-woocommerce' ),
				),
				'custom_attributes' => apply_filters( 'prowc_product_countdown', array( 'disabled' => 'disabled' ), 'settings' ),
			),
			array(
				'desc'      => __( 'Position priority', 'product-countdown-for-woocommerce' ),
				'desc_tip'  => __( 'Change this if you want to move the timer inside the Position.', 'product-countdown-for-woocommerce' ),
				'id'        => 'prowc_product_countdown_loop_position_priority',
				'default'   => 10,
				'type'      => 'number',
				'custom_attributes' => apply_filters( 'prowc_product_countdown', array( 'disabled' => 'disabled' ), 'settings' ),
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'prowc_product_countdown_position_options',
			),
			array(
				'title'     => __( '"Disable product" Action Options', 'product-countdown-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'prowc_product_countdown_disable_product_action_options',
			),
			array(
				'title'     => __( 'Make non-purchasable', 'product-countdown-for-woocommerce' ),
				'desc'      => __( 'Enable', 'product-countdown-for-woocommerce' ),
				'desc_tip'  => __( 'This will make products non-purchasable (i.e. product can\'t be added to the cart). However products will still be visible.', 'product-countdown-for-woocommerce' ),
				'id'        => 'prowc_product_countdown_disable_product_action_purchasable',
				'default'   => 'yes',
				'type'      => 'checkbox',
			),
			array(
				'title'     => __( 'Make invisible', 'product-countdown-for-woocommerce' ),
				'desc'      => __( 'Enable', 'product-countdown-for-woocommerce' ),
				'desc_tip'  => __( 'This will hide products in shop and search results. However products will still be accessible via direct link.', 'product-countdown-for-woocommerce' ) .
					apply_filters( 'prowc_product_countdown', '<br>' . sprintf( 
						// translators: Placeholder %s plugin to enable this option.
						__( 'You will need %s plugin to enable this option.', 'product-countdown-for-woocommerce' ),
						'<a href="https://prowcplugins.com/downloads/product-time-countdown-for-woocommerce/" target="_blank">' .
						// translators: Placeholder %s represents the link to the Pro version plugin.
						__( 'Product Time Countdown for WooCommerce Pro', 'product-countdown-for-woocommerce' ) . '</a>' ), 'settings' ),
				'id'        => 'prowc_product_countdown_disable_product_action_visibility',
				'default'   => 'no',
				'type'      => 'checkbox',
				'custom_attributes' => apply_filters( 'prowc_product_countdown', array( 'disabled' => 'disabled' ), 'settings' ),
			),
			array(
				'title'     => __( 'Make completely invisible', 'product-countdown-for-woocommerce' ),
				'desc'      => __( 'Enable', 'product-countdown-for-woocommerce' ),
				'desc_tip'  => 
					// translators: Placeholder %s represents the link to the Pro version plugin.
					__( 'This will hide products completely (including direct link).', 'product-countdown-for-woocommerce' ) .
					apply_filters( 'prowc_product_countdown', '<br>' . sprintf(
					// translators: Placeholder %s represents plugin to enable this option.
					__( 'You will need %s plugin to enable this option.', 'product-countdown-for-woocommerce' ),
					'<a href="https://prowcplugins.com/downloads/product-time-countdown-for-woocommerce/" target="_blank">' .
							__( 'Product Time Countdown for WooCommerce Pro', 'product-countdown-for-woocommerce' ) . '</a>' ), 'settings' ),
				'id'        => 'prowc_product_countdown_disable_product_action_query',
				'default'   => 'no',
				'type'      => 'checkbox',
				'custom_attributes' => apply_filters( 'prowc_product_countdown', array( 'disabled' => 'disabled' ), 'settings' ),
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'prowc_product_countdown_disable_product_action_options',
			),
			array(
				'title'     => __( 'Admin Products List Options', 'product-countdown-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'prowc_product_countdown_admin_products_list_options',
			),
			array(
				'title'     => __( 'Add column', 'product-countdown-for-woocommerce' ),
				'desc'      => __( 'Add', 'product-countdown-for-woocommerce' ),
				'desc_tip'  => __( 'This will add "Countdown" column to admin products list.', 'product-countdown-for-woocommerce' ),
				'id'        => 'prowc_product_countdown_add_admin_column',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'prowc_product_countdown_admin_products_list_options',
			),
		);
	}

}

endif;

return new ProWC_Product_Countdown_Settings_General();
