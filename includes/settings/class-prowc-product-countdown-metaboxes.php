<?php
/**
 * Product Time Countdown for WooCommerce - Metaboxes
 *
 * @version 1.4.0
 * @since   1.0.0
 * @author  ProWCPlugins
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'ProWC_Product_Countdown_Metaboxes' ) ) :

class ProWC_Product_Countdown_Metaboxes {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		add_action( 'add_meta_boxes',    array( $this, 'add_counter_metabox' ) );
		add_action( 'save_post_product', array( $this, 'save_counter_meta_box' ), PHP_INT_MAX, 2 );
	}

	/**
	 * add_counter_metabox.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_counter_metabox() {
		add_meta_box(
			'alg-product-countdown',
			__( 'Product Time Countdown', 'product-countdown-for-woocommerce' ),
			array( $this, 'display_counter_metabox' ),
			'product',
			'side',
			'high'
		);
	}

	/**
	 * display_counter_metabox.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 * @todo    [dev] maybe use JS datepicker / timepicker
	 * @todo    [dev] `apply_filters` for `prowc_product_countdown_action`
	 */
	function display_counter_metabox() {
		$the_post_id      = get_the_ID();
		$is_enabled       = get_post_meta( $the_post_id, '_' . 'prowc_product_countdown_enabled', true );
		$countdown_date   = get_post_meta( $the_post_id, '_' . 'prowc_product_countdown_date',    true );
		$countdown_time   = get_post_meta( $the_post_id, '_' . 'prowc_product_countdown_time',    true );
		$countdown_action = get_post_meta( $the_post_id, '_' . 'prowc_product_countdown_action',  true );
		$table_data       = array();

		$field_html = '';
		$field_html .= '<select name="prowc_product_countdown_enabled">';
		$field_html .= '<option value="no" '  . selected( 'no',  $is_enabled, false ) . '>' .
			__( 'No', 'product-countdown-for-woocommerce' )  . '</option>';
		$field_html .= '<option value="yes" ' . selected( 'yes', $is_enabled, false ) . '>' .
			__( 'Yes', 'product-countdown-for-woocommerce' ) . '</option>';
		$field_html .= '</select>';
		$table_data[] = array( __( 'Enabled', 'product-countdown-for-woocommerce' ), $field_html );

		$field_html = '';
		$field_html .= '<input type="date" name="prowc_product_countdown_date" value="' . $countdown_date . '">';
		$table_data[] = array( __( 'Date', 'product-countdown-for-woocommerce' ), $field_html );

		$field_html = '';
		$field_html .= '<input type="time" name="prowc_product_countdown_time" value="' . $countdown_time . '">';
		$table_data[] = array( __( 'Time', 'product-countdown-for-woocommerce' ), $field_html );

		$action_options = apply_filters( 'prowc_product_countdown', array(
			'do_nothing'        => __( 'Do nothing', 'product-countdown-for-woocommerce' ),
			'disable_product'   => __( 'Disable product', 'product-countdown-for-woocommerce' ),
		), 'actions' );
		$field_html = '';
		$field_html .= '<select name="prowc_product_countdown_action">';
		foreach ( $action_options as $option_id => $option_title ) {
			$field_html .= '<option value="' . $option_id . '" ' . selected( $option_id, $countdown_action, false ) . '>' . $option_title . '</option>';
		}
		$field_html .= '</select>';
		$help_tip = ( '' != ( $help_tip = apply_filters( 'prowc_product_countdown',
			__( 'Pro version also includes "Cancel sale" and "Make sold out" actions.', 'product-countdown-for-woocommerce' ), 'settings' ) ) ? wc_help_tip( $help_tip, true ) : '' );
		$table_data[] = array( __( 'Action', 'product-countdown-for-woocommerce' ) . $help_tip, $field_html );

		$html = '';
		$html .= $this->get_table_html( $table_data, array( 'table_heading_type' => 'vertical', 'table_class' => 'widefat striped' ) );
		$html .= '<p><em>' . __( 'Current date and time', 'product-countdown-for-woocommerce' ) . ': ' . current_time( 'mysql' ) . '</em></p>';
		$html .= '<input type="hidden" name="prowc_product_countdown_save_post" value="prowc_product_countdown_save_post">';
		echo $html;
	}

	/**
	 * save_counter_meta_box.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function save_counter_meta_box( $post_id, $post ) {
		// Check that we are saving with current metabox displayed.
		if ( ! isset( $_POST[ 'prowc_product_countdown_save_post' ] ) ) {
			return;
		}
		update_post_meta( $post_id, '_' . 'prowc_product_countdown_enabled', $_POST[ 'prowc_product_countdown_enabled'] );
		update_post_meta( $post_id, '_' . 'prowc_product_countdown_date',    $_POST[ 'prowc_product_countdown_date'] );
		update_post_meta( $post_id, '_' . 'prowc_product_countdown_time',    $_POST[ 'prowc_product_countdown_time'] );
		update_post_meta( $post_id, '_' . 'prowc_product_countdown_action',  $_POST[ 'prowc_product_countdown_action'] );
	}

	/**
	 * get_table_html.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 */
	function get_table_html( $data, $args = array() ) {
		$defaults = array(
			'table_class'        => '',
			'table_style'        => '',
			'row_styles'         => '',
			'table_heading_type' => 'horizontal',
			'columns_classes'    => array(),
			'columns_styles'     => array(),
		);
		$args = array_merge( $defaults, $args );
		$table_class = ( '' == $args['table_class'] ? '' : ' class="' . $args['table_class'] . '"' );
		$table_style = ( '' == $args['table_style'] ? '' : ' style="' . $args['table_style'] . '"' );
		$row_styles  = ( '' == $args['row_styles']  ? '' : ' style="' . $args['row_styles']  . '"' );
		$html = '';
		$html .= '<table' . $table_class . $table_style . '>';
		$html .= '<tbody>';
		foreach( $data as $row_number => $row ) {
			$html .= '<tr' . $row_styles . '>';
			foreach( $row as $column_number => $value ) {
				$th_or_td = ( ( 0 === $row_number && 'horizontal' ===  $args['table_heading_type'] ) || ( 0 === $column_number && 'vertical' ===  $args['table_heading_type'] ) ?
					'th' : 'td' );
				$column_class = ( ! empty( $args['columns_classes'][ $column_number ] ) ? ' class="' . $args['columns_classes'][ $column_number ] . '"' : '' );
				$column_style = ( ! empty( $args['columns_styles'][ $column_number ] )  ? ' style="' . $args['columns_styles'][ $column_number ]  . '"' : '' );
				$html .= '<' . $th_or_td . $column_class . $column_style . '>';
				$html .= $value;
				$html .= '</' . $th_or_td . '>';
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}

}

endif;

return new ProWC_Product_Countdown_Metaboxes();
