<?php
if(!defined( 'ABSPATH' )) exit;
/**
 * WC_Checkout_Field_Editor class.
 */
class WC_Checkout_Field_Editor {
	/**
	 *__construct function.
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function __construct() {
		// Validation rules are controlled by the local fields and can't be changed
		$this->locale_fields = array(
			'billing_address_1', 'billing_address_2', 'billing_state', 'billing_postcode', 'billing_city',
			'shipping_address_1', 'shipping_address_2', 'shipping_state', 'shipping_postcode', 'shipping_city',
			'order_comments'
		);

		add_action('admin_menu', array($this, 'admin_menu'));
		add_filter('woocommerce_screen_ids', array($this, 'add_screen_id'));
		add_action('woocommerce_checkout_update_order_meta', array($this, 'save_data'), 10, 2);
		add_action( 'wp_enqueue_scripts', array($this, 'wc_checkout_fields_scripts'));
		
		add_action('wp_ajax_save_custom_form_fields', array($this, 'save_wcfe_options'));
		add_filter( 'woocommerce_form_field_text', array($this, 'wcfe_checkout_fields_text_field'), 10, 4 );
		add_filter( 'woocommerce_form_field_checkbox', array($this, 'wcfe_checkout_fields_checkbox_field'), 10, 4 );
		add_filter( 'woocommerce_form_field_checkboxgroup', array($this, 'wcfe_checkout_fields_checkboxgroup_field'), 10, 4 );
		add_filter( 'woocommerce_form_field_date', array($this, 'wcfe_checkout_fields_date_picker_field'), 10, 4 );
		add_filter( 'woocommerce_form_field_timepicker', array($this, 'wcfe_checkout_fields_timepicker_field'), 10, 4 );
		add_filter( 'woocommerce_form_field_radio', array($this, 'wcfe_checkout_fields_radio_field'), 10, 4 );
		add_filter( 'woocommerce_form_field_select', array($this, 'wcfe_checkout_fields_select_field'), 10, 4 );
		add_filter( 'woocommerce_form_field_multiselect', array($this, 'wcfe_checkout_fields_multiselect_field'), 10, 4 );
		add_filter( 'woocommerce_form_field_heading', array($this, 'wcfe_checkout_fields_heading_field'), 10, 4 );
		add_filter( 'woocommerce_form_field_paragraph', array($this, 'wcfe_checkout_fields_paragraph_field'), 10, 4 );
		add_filter( 'woocommerce_form_field_url', array($this, 'wcfe_url_field'), 10, 4 );
	}
	
	/**
	 * Admin Menu function 
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function admin_menu() {
		$this->screen_id = add_submenu_page('woocommerce', esc_html__('WooCommerce Checkout & Register Form Editor', 'wcfe'), esc_html__('Checkout & Register Form', 'wcfe'), 
		'manage_woocommerce', 'checkout_form_editor', array($this, 'the_editor'));

		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
	}
	
	/**
	 * Admin Script function 
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function enqueue_admin_scripts() {
		//wp_enqueue_style ('choosen-css', plugins_url('/assets/css/chosen.min.css', dirname(__FILE__)));

		wp_enqueue_style ('wcfe-style', plugins_url('/assets/css/wcfe-style.css', dirname(__FILE__)));

		if ( is_rtl() ) {
			wp_enqueue_style ('wcfe-style-rtl', plugins_url('/assets/css/wcfe-rtl.css', dirname(__FILE__)));
			//wp_enqueue_style ('wcf-select', plugins_url('/assets/css/select.css', dirname(__FILE__)));
			wp_enqueue_style ('wcfe-rtl-ui', plugins_url('/assets/css/rtl-css-ui.css', dirname(__FILE__)));
		}
	
		wp_enqueue_script( 'wcfe-admin-script', plugins_url('/assets/js/wcfe-admin.js', dirname(__FILE__)), array('jquery','jquery-ui-dialog', 'jquery-ui-sortable',
		'woocommerce_admin', 'select2', 'jquery-tiptip'), '1.0', true );
		
	  	wp_localize_script( 'wcfe-admin-script', 'WcfeAdmin', array(
		    'MSG_INVALID_NAME' => 'NAME can not contain spaces',
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		));	
	}
	
	/**
	 * Checkout Fields Front-end Scripts function 
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wc_checkout_fields_scripts() {
		global $wp_scripts;

		if ( is_checkout() ) {
			
			wp_enqueue_style ('wcfe-style-front', plugins_url('/assets/css/wcfe-style-front.css', dirname(__FILE__)));

			if ( is_rtl() ) {
				wp_enqueue_style ('wcfe-style-front-rtl', plugins_url('/assets/css/wcfe-front-end-rtl.css', dirname(__FILE__)));
			}
			
			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
			
			if(is_checkout()) {
				$currentScreen ="checkout";
			} else {
				$currentScreen ="account";
			}

			wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
		
			
			$pattern = array(
				//day
				'd',		//day of the month
				'j',		//3 letter name of the day
				'l',		//full name of the day
				'z',		//day of the year
				'S',

				//month
				'F',		//Month name full
				'M',		//Month name short
				'n',		//numeric month no leading zeros
				'm',		//numeric month leading zeros

				//year
				'Y', 		//full numeric year
				'y'		//numeric year: 2 digit
			);

			$replace = array(
				'dd','d','DD','o','',
				'MM','M','m','mm',
				'yy','y'
			);

			foreach( $pattern as &$p ) {
				$p = '/' . $p . '/';
			}

			wp_localize_script( 'wc-checkout-editor-frontend', 'wc_checkout_fields', array(
				'date_format' => preg_replace( $pattern, $replace, wc_date_format() )
			) );
		}
	}

	/**
	 * Checkout Field Text Fields function 
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_checkout_fields_text_field( $field = '', $key, $args, $value ) {
		if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

		$data_validations = '';
		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$data_validations = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'wcfe'  ) . '">*</abbr>';
		} else {
			$required = '';
		}
		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';

		$data_rules_action = '';
		$data_rules = '';
		
		if(isset($args['rules_action_ajax']) && !empty($args['rules_action_ajax'])){
			$data_rules_action = $args['rules_action_ajax'];
			$data_rules = urldecode($args['rules_ajax']);
		}
		
		$singleq = "'";
		
		$fieldLabel = '';

		$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field" data-rules='.$singleq.$data_rules.$singleq.' data-rules-action="'.$data_rules_action.'" data-validations="'.$data_validations.'" >';

		if ( $args['label'] ) {
			$fieldLabel = $args['label'];
			$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label'] . $required . '</label>';
		}
		
		$field .= '<input type="text" class="input-text '.esc_attr( implode( ' ', $args['input_class'] ) ).'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . $args['placeholder'] . '" '.$args['maxlength'].' value="' . esc_attr( $value ) . '" />';

		$field .= '</p>' . $after;

		return $field;
	}

	/**
	 * Woo Function rest field
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */		

	function wcfe_url_field($field = '', $key, $args, $value) {
		if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

		$data_validations = '';
		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$data_validations = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'wcfe'  ) . '">*</abbr>';
		} else {
			$required = '';
		}
		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';

		$data_rules_action = '';
		$data_rules = '';
		
		
		$singleq = "'";
		
		$fieldLabel = '';

		$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">';

		if ( $args['label'] ) {
			$fieldLabel = $args['label'];
			$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label'] . $required . '</label>';
		}
		
		$field .= '<input type="url" class="input-text '.esc_attr( implode( ' ', $args['input_class'] ) ).'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . $args['placeholder'] . '" '.$args['maxlength'].' value="' . esc_attr( $value ) . '" />';

		$field .= '</p>' . $after;

		return $field;
	}

	/**
	 * Save Data function Checkout Fields Check box Field Function
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_checkout_fields_checkbox_field( $field = '', $key, $args, $value ) {
		if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'wcfe' ) . '">*</abbr>';
		} else {
			$required = '';
		}

		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';

		$data_rules_action = '';
		$data_rules = '';

		if(isset($args['rules_action_ajax']) && !empty($args['rules_action_ajax'])){
			$data_rules_action = $args['rules_action_ajax'];
			$data_rules = urldecode($args['rules_ajax']);
		}
		
		$singleq = "'";
		$field = '<div class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field" data-rules='.$singleq.$data_rules.$singleq.' data-rules-action="'.$data_rules_action.'">';

		$field .= '<fieldset>' . $required;

		if( isset($args['price']) && !empty($args['price']) || isset($args['price_type']) && $args['price_type'] == 'custom'  ){
			$price_type = "";

			if(isset($args['price_type'])){
				$price_type = $args['price_type'];
			}

			$price_unit = 0;
			if(isset($args['price_unit']) && !empty($args['price_unit'])){
				$price_unit = $args['price_unit'];
			}

			$taxable = "";

			if(isset($args['taxable'])){
				$taxable = $args['taxable'];
			}

			$tax_class = "";

			if(isset($args['tax_class'])){
				$tax_class = $args['tax_class'];
			}
			$field .= '<label><input type="checkbox" class="wcfe-price-field" id="'.$key.'" name="' . esc_attr( $key ) . '" value="' . esc_attr( $key ) . '" data-price-label="'.$args['label'].'" data-taxable="no" data-tax-class="'.$tax_class.'" data-price="'.$args['price'].'" data-price-type="'.$price_type.'" /> ' . esc_html( $args['label'] ) . '</label>';
		} else {
			$field .= '<label><input type="checkbox" ' . checked( $value, $key, false ) . ' name="' . $key . '" class="input-checkbox" value="' . $key . '" /> ' . $args['label'] . '</label>';
		}
		$field .= '</fieldset></div>' . $after;
		return $field;
	}
	
	/**
	 * Check box group function 
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_checkout_fields_checkboxgroup_field( $field = '', $key, $args, $value ) {
		
		if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'wcfe' ) . '">*</abbr>';
		} else {
			$required = '';
		}

		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';
		
		$data_rules_action = '';

		$data_rules = '';
		
		if(isset($args['rules_action_ajax']) && !empty($args['rules_action_ajax'])){
			$data_rules_action = $args['rules_action_ajax'];
			$data_rules = urldecode($args['rules_ajax']);
		}
		
		$singleq = "'";
		
		$field = '<div class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field" data-rules='.$singleq.$data_rules.$singleq.' data-rules-action="'.$data_rules_action.'">';

		$field .= '<fieldset><legend>' . $args['label'] . $required . '</legend>';
	
		if ( ! empty( $args['options_json'] ) ) {
			foreach ( $args['options_json'] as $option ) {
				if( isset($option['price']) && !empty($option['price']) ){
					$hasPricing = true;
					$field .= '<label><input type="checkbox" class="wcfe-price-field" ' . checked( $value, esc_attr( $option['key'] ), false ) . ' id="'.$key.'_'.$option['key'].'" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $option['key'] ) . '" data-price-label="'.$args['label'].'" data-taxable="no" data-tax-class="" data-price="'.$option['price'].'" data-price-type="'.$option['price_type'].'" /> ' . esc_html( $option['text'] ) . '</label>';
				} else {
					$field .= '<label><input type="checkbox" ' . checked( $value, esc_attr( $option['key'] ), false ) . ' name="' . esc_attr( $key ) . '[]" id="'.$key.'_'.$option['key'].'" value="' . esc_attr( $option['key'] ) . '" /> ' . esc_html( $option['text'] ) . '</label>';
				}
			}
		}
		$field .= '</fieldset></div>' . $after;

		return $field;
	}
	
	/**
	 * Radio Field function 
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_checkout_fields_radio_field( $field = '', $key, $args, $value ) {
		if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';
		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'wcfe' ) . '">*</abbr>';
		} else {
			$required = '';
		}

		$data_rules_action = '';
		$data_rules = '';
		
		if(isset($args['rules_action_ajax']) && !empty($args['rules_action_ajax'])){
			$data_rules_action = $args['rules_action_ajax'];
			$data_rules = rawurldecode($args['rules_ajax']);
		}
		
		$singleq = "'";
		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';

		$field = '<div class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field" data-rules='.$singleq.$data_rules.$singleq.' data-rules-action="'.$data_rules_action.'">';

		$field .= '<fieldset><legend>' . $args['label'] . $required . '</legend>';

		if ( ! empty( $args['options_json'] ) ) {
			foreach ( $args['options_json'] as $option ) {
				if( isset($option['price']) && !empty($option['price']) ){
					$hasPricing = true;

					$field .= '<label><input type="radio" id="'.$key.'_'.$option['key'].'" class="wcfe-price-field" ' . checked( $value, esc_attr( $option['key'] ), false ) . ' name="' . esc_attr( $key ) . '" value="' . esc_attr( $option['key'] ) . '" data-price-label="'.$args['label'].'" data-taxable="no" data-tax-class="" data-price="'.$option['price'].'" data-price-type="'.$option['price_type'].'" /> ' . esc_html( $option['text'] ) . '</label>';

				} else {
					$field .= '<label><input type="radio" id="'.$key.'_'.$option['key'].'" ' . checked( $value, esc_attr( $option['key'] ), false ) . ' name="' . esc_attr( $key ) . '" value="' . esc_attr( $option['key'] ) . '" /> ' . esc_html( preg_replace('/\+/', '  ', $option['text']) ) . '</label>';
				}
			}
		}
		$field .= '</fieldset></div>' . $after;
		return $field;
	}
	/**
	 * Checkout Field Time picker Fields function 
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_checkout_fields_timepicker_field( $field = '', $key, $args, $value ){
		set_time_limit(0);
		$customer_user_id = get_current_user_id(); // current user ID here for example
		
		// Getting current customer orders
		$customer_orders = wc_get_orders( array(
			'meta_key' => '_customer_user',
			'meta_value' => $customer_user_id,
			'posts_per_page'=>1,
			'orderby'=>'ID',
            'orderby'=>'DESC'
		) );
			
		$selectedVal = '';

		// Loop through each customer WC_Order objects
		foreach($customer_orders as $order ){
			// Order ID (added WooCommerce 3+ compatibility)
			$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
			$valArr = "";
			
			$valArr = get_post_meta( $order_id, $key, true );
				
			if(!empty($valArr) && is_array($valArr)){
				foreach($valArr as $selectedVal){
					$selectedVal = $selectedVal;
				}
			}		
		}
			
		
		
		if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'wcfe' ) . '">*</abbr>';
		} else {
			$required = '';
		}

		$hasPricing =false;

		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';

		$options = '';
		//$options .= '<option value="'..'">'. esc_html__('Please Select','wcfe') .'</option>';

		$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">';

		if ( $args['label'] ) {
			$fieldLabel = $args['label'];
			$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required . '</label>';
		}

		$min_time = strtotime($args['min_time']);
		$max_time = strtotime($args['max_time']);
		
		$time_step = $args['time_step'];
		$time_format = $args['time_format'];
		
		for($hours=0; $hours<24; $hours++){
			for($mins=0; $mins<60; $mins+=$time_step){
				$rawtime = str_pad(($hours%24),2,'0',STR_PAD_LEFT).':'.str_pad($mins,2,'0',STR_PAD_LEFT);
				$rawtime = strtotime($rawtime);
				$rawtime = date($time_format, $rawtime);
				if( strtotime($rawtime) >= $min_time && strtotime($rawtime) <= $max_time){
					$options .= '<option value="'.$rawtime.'">'.$rawtime.'</option>';
				}
				
			} 
		}
			   
		$class = '';
		
		$field .= '<select name="' . esc_attr( $key ) . '[]" id="' . esc_attr( $key ) . '" class="checkout_chosen_select select wc-enhanced-select ' . $class . '">';
				
		$field .= $options;
		$field .= '</select>
		</p>' . $after;
		
		return $field;
	}

	/**
	 * Checkout Fields Date Picker function 
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_checkout_fields_date_picker_field( $field = '', $key, $args, $value ) {
		if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'wcfe'  ) . '">*</abbr>';
		} else {
			$required = '';
		}
	
		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';
	
	
		if ( ! empty( $args['validate'] ) ) {
			foreach( $args['validate'] as $validate ) {
				$args['class'][] = 'validate-' . $validate;
			}
		}

		$singleq = "'";
		
		$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">';

		if ( $args['label'] ) {
			$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label'] . $required . '</label>';
		}

		$field .= '<input type="text" class="checkout-date-picker input-text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . $args['placeholder'] . '" '.$args['maxlength'].' value="' . esc_attr( $value ) . '" />
			</p>' . $after;

		return $field;
	}	
	
	/**
	 * Multi-select Fields function 
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_checkout_fields_multiselect_field( $field = '', $key, $args, $value ) {
		$customer_user_id = get_current_user_id(); // current user ID here for example
		// Getting current customer orders

		$customer_orders = wc_get_orders( array(
			'meta_key' => '_customer_user',
			'meta_value' => $customer_user_id,
			'posts_per_page'=>1,
			'orderby'=>'ID',
            'orderby'=>'DESC'
		) );
			
		$selectedVal = '';

		// Loop through each customer WC_Order objects
		foreach($customer_orders as $order ) {
			// Order ID (added WooCommerce 3+ compatibility)
			$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
			$valArr = "";

			$valArr = get_post_meta( $order_id, $key, true );
				
			if(!empty($valArr) && is_array($valArr)){
				
				foreach($valArr as $selectedVal){
					$selectedVal = $selectedVal;
				}
			}
		}

		if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'wcfe' ) . '">*</abbr>';
		} else {
			$required = '';
		}

		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';
		
		$data_rules_action = '';
		$data_rules = '';
		
		if(isset($args['rules_action_ajax']) && !empty($args['rules_action_ajax'])){
			$data_rules_action = $args['rules_action_ajax'];
			$data_rules = rawurldecode($args['rules_ajax']);
		}
		
		$singleq = "'";
		$options = '';
		$hasPricing = false;

		if ( ! empty( $args['options_json'] ) ) {
			foreach ( $args['options_json'] as $option ) {
				if(empty( selected( $selectedVal, $option['key'], false )) 	){
					$options .= '<option value = '. $option['key'] . '>' . esc_attr( $option['text'] ) .'</option>';
				} else {
					$options .= '<option value = '. selected( $selectedVal, $option['key'], false ) . '>' . esc_attr( $option['text'] ) .'</option>';
				}
			}

			$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field" data-rules='.$singleq.$data_rules.$singleq.' data-rules-action="'.$data_rules_action.'">';

			if ( $args['label'] ) {
				$fieldLabel = $args['label'];
				$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required . '</label>';
			}

			$class = '';
			$field .= '<select data-placeholder="' . esc_html__( 'Select some options', 'wcfe' ) . '" multiple="multiple" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" class="checkout_chosen_select select wc-enhanced-select ' . $class . '">';	
		    $field .= $options;
			$field .= '</select>
			</p>' . $after;
		}
		return $field;
	}

	/**
	 * Checkout Select Field
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_checkout_fields_select_field( $field = '', $key, $args, $value ) {
		$customer_user_id = get_current_user_id(); // current user ID here for example
	
		// Getting current customer orders
		$customer_orders = wc_get_orders( array(
			'meta_key' => '_customer_user',
			'meta_value' => $customer_user_id,
			'posts_per_page'=>1,
			'orderby'=>'ID',
            'orderby'=>'DESC'
		) );
			
		$selectedVal = '';

		// Loop through each customer WC_Order objects
		foreach($customer_orders as $order ){

			// Order ID (added WooCommerce 3+ compatibility)
			$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
			$valArr = "";

			$valArr = get_post_meta( $order_id, $key, true );
				
			if(!empty($valArr) && is_array($valArr)){
				foreach($valArr as $selectedVal){
					$selectedVal = $selectedVal;
				}
			}		
		}
		
		$data_rules_action = '';
		$data_rules = '';
		
		if(isset($args['rules_action_ajax']) && !empty($args['rules_action_ajax'])){
			$data_rules_action = $args['rules_action_ajax'];
			$data_rules = urldecode($args['rules_ajax']);
		}

		$singleq = "'";
		
		if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'wcfe' ) . '">*</abbr>';
		} else {
			$required = '';
		}
		$hasPricing =false;
		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';

		$options = '';
		//$options .= '<option value="">'. esc_html__('Please Select','wcfe') .'</option>';
		
		if ( ! empty( $args['options_json'] ) ) {
			foreach ( $args['options_json'] as $option ) {
				if(empty( selected( $selectedVal, $option['key'], false )) 	){
					$options .= '<option value = '. $option['key'] . '>' . esc_attr( $option['text'] ) .'</option>';
				} else {
					$options .= '<option value = '. selected( $selectedVal, $option['key'], false ) . '>' . esc_attr( $option['text'] ) .'</option>';
				}
			}

			$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field" data-rules='.$singleq.$data_rules.$singleq.' data-rules-action="'.$data_rules_action.'">';

			if ( $args['label'] ) {
				$fieldLabel = $args['label'];
				$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required . '</label>';
			}

			$class = '';
	
			$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" class="checkout_chosen_select select wc-enhanced-select ' . $class . '">';
			$field .= $options;
			$field .= '</select>
			</p>' . $after;
		}
		return $field;
	}
	
	
	/**
	 * Checkout Field Heading Fields
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_checkout_fields_heading_field( $field = '', $key, $args, $value ) {
		$field = '<h3 class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">' . $args['label'] . '</h3>';
		return $field;
	}	
	
	/**
	 * Checkout Field Paragraph Fields
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_checkout_fields_paragraph_field( $field = '', $key, $args, $value ) {
		$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">' . $args['label'] . '</p>';
		return $field;
	}
	
	/**
	 * Add_screen_id function.
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function add_screen_id($ids){
		$ids[] = 'woocommerce_page_checkout_form_editor';
		$ids[] = strtolower(esc_html__('WooCommerce', 'wcfe')) .'_page_checkout_form_editor';
		return $ids;
	}

	/**
	 * Reset checkout fields.
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function reset_checkout_fields() {
		delete_option('wcfe_account_label');
		delete_option('wcfe_account_sync_fields');
		delete_option('wc_fields_account');
		delete_option('wc_fields_billing');
		delete_option('wc_fields_shipping');
		delete_option('wc_fields_additional');
		echo '<div class="updated"><p>'. esc_html__('SUCCESS: Checkout fields successfully reset', 'wcfe') .'</p></div>';
	}

	/**
	 * Reserved Field Name
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function is_reserved_field_name( $field_name ){
		if($field_name && in_array($field_name, array(
			'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 'billing_country', 'billing_postcode', 'billing_phone', 'billing_email', 'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_state', 'shipping_country', 'shipping_postcode', 'customer_note', 'order_comments', 'account_username','account_password'
		))) {
			return true;
		}
		return false;
	}

	/**
	 * Default Field Name
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function is_default_field_name( $field_name ) {
		if($field_name && in_array($field_name, array (
			'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 'billing_country', 'billing_postcode', 'billing_phone', 'billing_email', 'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_state', 'shipping_country', 'shipping_postcode', 'customer_note', 'order_comments', 'account_username','account_password'
		))) {
			return true;
		}

		return false;
	}
	
	/**
	 * Save Data function 
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function save_data( $order_id, $posted ) {
		if( get_option( 'wcfe_account_sync_fields') && get_option( 'wcfe_account_sync_fields') == "on") {
			$types = array('account','billing', 'shipping', 'additional');
		} else {
			$types = array('billing', 'shipping', 'additional');
		}
		$counter  = 0;

		foreach($types as $type){
			$fields = $this->get_fields($type);
			foreach($fields as $name => $field) {
				if( isset($_SESSION[$name])) {
					update_post_meta($order_id, $name, $_SESSION[$name]);
					unset($_SESSION[$name]);
				}
				
				if(isset($field['custom']) && $field['custom'] && isset($posted[$name])){
					if($field['type'] == 'checkbox' || $field['type'] == 'select') {
						$value = $posted[$name];
						if(is_array($value)){
							$savArr = implode(",",$value);
							update_post_meta($order_id, $name, $savArr);
						} else{
							update_post_meta($order_id, $name, $value);
						}
					} else {
						$value =  $posted[$name];
						if($value) {
							update_post_meta($order_id, $name, $value);
						}
					}
				}							
			}
			$counter++;
		}
	}

	/**
	 * Get Fields 
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	public static function get_fields($key){

		$fields = array_filter(get_option('wc_fields_'. $key, array()));

		if(empty($fields) || sizeof($fields) == 0){
			if($key === 'billing' || $key === 'shipping'){
				$fields = WC()->countries->get_address_fields(WC()->countries->get_base_country(), $key . '_');

			} else if($key === 'additional') {
				$fields = array(
					'order_comments' => array(
						'type'        => 'textarea',
						'class'       => array('notes'),
						'label'       => esc_html__('Order Notes', 'wcfe'),
						'placeholder' => _x('Notes about your order, e.g. special notes for delivery.', 'placeholder', 'wcfe')
					)
				);
			} else if($key === 'account'){
				$fields = array(
					'account_username' => array(
						'type' => 'text',
						'label' => esc_html__('Email address', 'wcfe')
					),
					'account_password' => array(
						'type' => 'password',
						'label' => esc_html__('Password', 'wcfe')
					)

				);
			}
		}
		return $fields;
	}

	/**
	 * Short fields  by order 
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */		
	function sort_fields_by_order($a, $b){
	    if(!isset($a['order']) || $a['order'] == $b['order']){
	        return 0;
	    }
	    return ($a['order'] < $b['order']) ? -1 : 1;
	}
	
	/**
	 * Get Fields Type
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */
	function get_field_types(){
		return array(
			'text' => 'Text',
			'number' => 'Number',
			'hidden' => 'Hidden',
			'password' => 'Password',
			'email' => 'Email',
			'phone' => 'Phone',
			'textarea' => 'Textarea',
			'select' => 'Select',
			'multiselect' => 'Multi-Select',
			'checkbox' => 'Checkbox',
			'checkboxgroup' => 'Checkbox Group',
			'radio'	=> 'Radio',
			'heading' => 'Heading',				
			'date' => 'Date',		
			'timepicker' => 'Time',				
			'url' => 'URL',		
			'paragraph' => 'Paragraph',		
		);
	}

	/**
	 * New field form 
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */
	function wcfe_new_field_form_pp(){
		$field_types = $this->get_field_types();
		$formTitle = 'New Checkout Field';
		$addClass = '';
		?>
		
        <div id="wcfe_new_field_form_pp" title="<?php echo esc_html($formTitle); ?>" class="<?php echo $addClass; ?> wcfe_popup_wrapper">
        	<form method="post" id="wcfe_new_field_form" action="">
				<input type="hidden" name="i_options" value="" />
				<input type="hidden" name="i_rules" value="" />
				<input type="hidden" name="i_rules_ajax" value="" />
							
				<table width="100%">
					<tr>                
						<td colspan="2" class="err_msgs"></td>
					</tr>

					<tr>                    
						<td width="20%"><?php esc_html_e('Field Type','wcfe'); ?></td>
						<td width="80%">
							<select name="ftype" class="field-type-select-new" style="width:100%;" onchange="wcfeFieldTypeChangeListner(this)">
							<?php foreach($field_types as $value=>$label) { ?>
								<option value="<?php echo trim($value); ?>"><?php echo $label; ?></option>
							<?php } ?>
							</select>
						</td>
					</tr>

					<tr class="rowName">                
						<td width="20%" style="vertical-align: top; padding-top: 5px;">
							<?php esc_html_e('Name','wcfe'); ?>
							<font color="red"><?php echo esc_html__('*','wcfe'); ?></font> 
						</td>
						<td width="80%">
							<input type="text" name="fname" placeholder="eg. new_field" style="width:100%;"/>
						   <br>
						   <span><?php esc_html_e(' Must be unique of each field', 'wcfe'); ?></span>
						</td>
					</tr>  

					<tr class="rowLabel">
						<td width="20%"><?php esc_html_e('Label of Field','wcfe'); ?></td>
						<td width="80%"><input type="text" name="flabel" placeholder="eg. New Field" style="width:100%;"/></td>
					</tr>

					<tr class="rowPlaceholder">                    
						<td width="20%"><?php esc_html_e('Placeholder','wcfe'); ?></td>
						<td width="80%"><input type="text" name="fplaceholder" placeholder="eg. New Field" style="width:100%;"/></td>
					</tr>

					<tr class="rowMaxlength">                    
						<td width="20%"><?php esc_html_e('Character limit','wcfe'); ?></td>
						<td width="80%"><input type="number" name="fmaxlength" style="width:100%;"/></td>
					</tr>

					<tr class="rowClass">
						<td width="20%"><?php esc_html_e('Field Width','wcfe'); ?></td>
						<td width="80%">
							<select name="fclass" class="wcf-field-width" style="width:100%;">
								<option value="form-row-wide"><?php esc_html_e('Full Width','wcfe'); ?></option>
								<option value="form-row-first"><?php esc_html_e('Half Width left','wcfe'); ?></option>
								<option value="form-row-last"><?php esc_html_e('Half Width Right','wcfe'); ?></option>
							</select>
						</td>
					</tr>

					<tr class="rowTimepicker">  
						<td width="20%">
							<?php esc_html_e('Min. Time','wcfe'); ?><br>
							<span class="thpladmin-subtitle"><?php esc_html_e('ex: 12:30am','wcfe'); ?></span>
						</td>
						<td width="80%">
							<input type="text" name="i_min_time" value="12:00am" style="width:100%;"> 
						</td>
					</tr>

					<tr class="rowTimepicker"> 
						<td width="20%">
							<?php esc_html_e('Max. Time','wcfe'); ?> <br>
							<span class="thpladmin-subtitle"><?php esc_html_e('ex: 11:30pm','wcfe'); ?></span>
						</td>
						<td width="80%"><input type="text" name="i_max_time" value="11:30pm" style="width:100%;"></td>
					</tr>
					
					<tr class="rowTimepicker">  
						<td width="20%"><?php esc_html_e('Time Format','wcfe'); ?></td>
						<td width="80%">
							<select name="i_time_format" value="h:i A" style="width:100%;">
								<option value="h:i A" selected=""><?php esc_html_e('12-hour format','wcfe'); ?></option>
								<option value="H:i"><?php esc_html_e('24-hour format','wcfe'); ?></option> 
							</select> 
						</td>
					</tr>

					<tr class="rowTimepicker"> 
						<td width="20%">
							<?php esc_html_e('Time Step','wcfe'); ?> <br>
							<span class="thpladmin-subtitle"><?php esc_html_e('In minutes, ex: 30','wcfe'); ?></span>
						</td>
						<td width="80%">
							<input type="text" name="i_time_step" value="30" style="width:100%;"> 
						</td>
					</tr>

					<tr class="rowOptions">                    
						<td>
							<?php esc_html_e('Options','wcfe'); ?>
							<font color="red"><?php echo esc_html__('*','wcfe'); ?></font> 
						</td>

						<td>
							<table border="0" cellpadding="0" cellspacing="0" class="wcfe-option-list thpladmin-dynamic-row-table">
								<tbody class="ui-sortable">
								<tr>
									<td>
										<input type="text" name="i_options_key[]" placeholder="Option Value"> 
									</td>

									<td>
										<input type="text" name="i_options_text[]" placeholder="Option Text"> 
									</td>
							
									<td class="action-cell"><a href="javascript:void(0)" onclick="wcfeAddNewOptionRow(this)" class="btn btn-blue" title="Add new option">+</a></td>

									<td class="action-cell"><a href="javascript:void(0)" onclick="wcfeRemoveOptionRow(this)" class="btn btn-red" title="Remove option">x</a></td>

									<td class="action-cell sort ui-sortable-handle"></td>
								</tr>
								</tbody> 
							</table>
						</td>
					</tr>
											
					<tr class="rowValidate">                    
						<td width="20%"><?php esc_html_e('Validation','wcfe'); ?></td>
						<td width="80%">
							<select multiple="multiple" name="fvalidate" placeholder="Select validations" class="wcfe-enhanced-multi-select" 
							style="width: 100%; height:85px;" >
								<option value="email"><?php esc_html_e('Email','wcfe'); ?></option>
								<option value="phone"><?php esc_html_e('Phone','wcfe'); ?></option>
								<option value="postcode"><?php esc_html_e('Post Code','wcfe'); ?></option>
								<option value="state"><?php esc_html_e('State','wcfe'); ?></option>
								<option value="number"><?php esc_html_e('Number','wcfe'); ?></option>
								<option value="url"><?php esc_html_e('URL','wcfe'); ?></option>
							</select>
						</td>
					</tr>
				
					<tr class="rowRequired">
						<td>&nbsp;</td>                     
						<td style="padding-top: 10px;">                 	
							<input type="checkbox" name="frequired" value="yes" checked/>
							<label><?php esc_html_e('Required','wcfe'); ?></label><br/><br/>
						  
							<input type="checkbox" name="fenabled" value="yes" checked/>
							<label><?php esc_html_e('Enabled','wcfe'); ?></label>
						</td>
					</tr>

					<tr class="rowShowInEmail"> 
						<td>&nbsp;</td>                
						<td>                    	
							<input type="checkbox" name="fshowinemail" value="email" checked/>
							<label><?php esc_html_e('Display in Emails','wcfe'); ?></label>
						</td>
					</tr>
			 
					<tr class="rowShowInOrder"> 
						<td>&nbsp;</td>                   
						<td>                    	
							<input type="checkbox" name="fshowinorder" value="order-review" checked/>
							<label><?php esc_html_e('Display in Order Detail Pages','wcfe'); ?></label>
						</td>
					</tr>               
				</table>
        	</form>
        </div>
    <?php }
	
	/**
	 * New field form popup
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */
	function wcfe_edit_field_form_pp(){
		$field_types = $this->get_field_types();
		$formTitle = 'Edit Checkout Field';
		$addClass = '';
		?>
        <div id="wcfe_edit_field_form_pp" title="<?php echo $formTitle; ?>" class="<?php echo $addClass; ?> wcfe_popup_wrapper">
	        <form id="wcfe_field_editor_form_edit">
				<input type="hidden" name="i_options" value="" />
				<input type="hidden" name="i_rules" value="" />
				<input type="hidden" name="i_rules_ajax" value="" />

				<table style="width: 100%;">
					<tr>                
						<td colspan="2" class="err_msgs"></td>
					</tr>
					<tr class="rowName">                
						<td width="20%" style="vertical-align: top; padding-top: 5px;"><?php esc_html_e('Name','wcfe'); ?><font color="red"><?php echo esc_html__('*','wcfe'); ?></font></td>
						<td width="80%">
							<input type="hidden" name="rowId"/>
							<input type="hidden" name="fname"/>
							<input type="text" name="fnameNew" placeholder="eg. new_field" style="width:100%;"/>
							<br><span><?php esc_html_e(' Must be unique of each field', 'wcfe'); ?></span>
						</td>
					</tr>

					<tr>                   
						<td width="20%"><?php esc_html_e('Field Type','wcfe'); ?></td>
						<td width="80%">
							<select name="ftype" style="width:100%;" class="field-type-select" onchange="wcfeFieldTypeChangeListner(this)">
							<?php foreach($field_types as $value=>$label){ ?>
								<option value="<?php echo trim($value); ?>"><?php echo $label; ?></option>
							<?php } ?>
							</select>
						</td>
					</tr>   

					<tr class="rowLabel">
						<td width="20%"><?php esc_html_e('Label','wcfe'); ?></td>
						<td width="80%"><input type="text" name="flabel" placeholder="eg. New Field" style="width:100%;"/></td>
					</tr>

					<tr class="rowPlaceholder">                    
						<td width="20%"><?php esc_html_e('Placeholder','wcfe'); ?></td>
						<td width="80%"><input type="text" name="fplaceholder" placeholder="eg. New Field" style="width:100%;"/></td>
					</tr>

					<tr class="rowMaxlength">                    
						<td width="20%"><?php esc_html_e('Character limit','wcfe'); ?></td>
						<td width="80%"><input type="number" name="fmaxlength" style="width: 100%;"/></td>
					</tr>

					<tr class="rowClass">
						<td width="20%"><?php esc_html_e('Field Width','wcfe'); ?></td>
						<td width="80%">
							<select name="fclass" class="wcf-field-width" style="width: 100%;">
								<option value="form-row-wide"><?php esc_html_e('Full Width','wcfe'); ?></option>
								<option value="form-row-first"><?php esc_html_e('Half Width left','wcfe'); ?></option>
								<option value="form-row-last"><?php esc_html_e('Half Width Right','wcfe'); ?></option>
							</select>
						</td>
					</tr>

					<tr class="rowTimepicker">  
						<td width="20%">
							<?php esc_html_e('Min. Time','wcfe'); ?><br>
							<span class="thpladmin-subtitle"><?php esc_html_e('ex: 12:30am','wcfe'); ?></span>
						</td>
						<td width="80%">
							<input type="text" name="i_min_time" value="12:00am" style="width:100%;"> 
						</td>
					</tr>

					<tr class="rowTimepicker"> 
						<td width="20%">
							<?php esc_html_e('Max. Time','wcfe'); ?> <br>
							<span class="thpladmin-subtitle"><?php esc_html_e('ex: 11:30pm','wcfe'); ?></span>
						</td>
						<td width="80%"><input type="text" name="i_max_time" value="11:30pm" style="width:100%;"></td>
					</tr>
					
					<tr class="rowTimepicker">  
						<td width="20%"><?php esc_html_e('Time Format','wcfe'); ?></td>
						<td width="80%">
							<select name="i_time_format" value="h:i A" style="width:100%;">
								<option value="h:i A" selected=""><?php esc_html_e('12-hour format','wcfe'); ?></option>
								<option value="H:i"><?php esc_html_e('24-hour format','wcfe'); ?></option> 
							</select> 
						</td>
					</tr>

					<tr class="rowTimepicker"> 
						<td width="20%">
							<?php esc_html_e('Time Step','wcfe'); ?> <br>
							<span class="thpladmin-subtitle"><?php esc_html_e('In minutes, ex: 30','wcfe'); ?></span>
						</td>
						<td width="80%">
							<input type="text" name="i_time_step" value="30" style="width:100%;"> 
						</td>
					</tr>

					<tr class="rowOptions">                    
						<td>
							<?php esc_html_e('Options','wcfe'); ?>
							<font color="red"><?php echo esc_html__('*','wcfe'); ?></font> 
						</td>
						<td>
							<table border="0" cellpadding="0" cellspacing="0" class="wcfe-option-list thpladmin-dynamic-row-table">
								<tbody class="ui-sortable">
								<tr>
									<td>
										<input type="text" name="i_options_key[]" placeholder="Option Value"> 
									</td>
									<td>
										<input type="text" name="i_options_text[]" placeholder="Option Text"> 
									</td>
									<td class="action-cell">
										<a href="javascript:void(0)" onclick="wcfeAddNewOptionRow(this)" class="btn btn-blue" title="Add new option">+</a> 
									</td>
									<td class="action-cell">
										<a href="javascript:void(0)" onclick="wcfeRemoveOptionRow(this)" class="btn btn-red" title="Remove option">x</a> 
									</td>
									<td class="action-cell sort ui-sortable-handle"></td>
								</tr>
								</tbody> 
							</table>
						</td>
					</tr>        
								
					<tr class="rowValidate">                    
						<td width="20%"><?php esc_html_e('Validation','wcfe'); ?></td>
						<td width="80%">
							<select multiple="multiple" name="fvalidate" placeholder="Select validations" class="wcfe-enhanced-multi-select">
								<option value="email"><?php esc_html_e('Email','wcfe'); ?></option>
								<option value="phone"><?php esc_html_e('Phone','wcfe'); ?></option>
								<option value="postcode"><?php esc_html_e('Post Code','wcfe'); ?></option>
								<option value="state"><?php esc_html_e('State','wcfe'); ?></option>
								<option value="number"><?php esc_html_e('Number','wcfe'); ?></option>
								<option value="url"><?php esc_html_e('URL','wcfe'); ?></option>
							</select>
						</td>
					</tr>
													
					<tr class="rowRequired"> 
						<td>&nbsp;</td>                     
						<td style="padding-top: 10px;">             	
							<input type="checkbox" name="frequired" value="yes" checked/>
							<label><?php esc_html_e('Required','wcfe'); ?></label><br/><br/>
							<input type="checkbox" name="fenabled" value="yes" checked/>
							<label><?php esc_html_e('Enabled','wcfe'); ?></label>
						</td>                    
					</tr>

					<tr class="rowShowInEmail"> 
						<td>&nbsp;</td>                   
						<td>                    	
							<input type="checkbox" name="fshowinemail" value="email" checked/>
							<label><?php esc_html_e('Display in Emails','wcfe'); ?></label>
						</td>
					</tr>

					<tr class="rowShowInOrder"> 
						<td>&nbsp;</td>                   
						<td>                    	
							<input type="checkbox" name="fshowinorder" value="order-review" checked/>
							<label><?php esc_html_e('Display in Order Detail Pages','wcfe'); ?></label>
						</td>
					</tr> 
				</table>
	    	</form>
    	</div><!--/#wcfe_edit_field_form_pp -->
    <?php }

	/**
	 * Render Tabs And Sections
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */
	function render_tabs_and_sections(){
		$tabs = array( 
			'fields' => __('Checkout & Account Fields', 'wcfe'),
			'premium_features' => __('Premium Features', 'wcfe'),
			'our_plugins' => __('Our Plugins', 'wcfe'),
		);

		$tab  = isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'fields';

		$sections = ''; $section  = '';
		if($tab === 'fields'){
			$sections = array( 'billing', 'shipping', 'additional', 'account' );
			$section  = isset( $_GET['section'] ) ? esc_attr( $_GET['section'] ) : 'billing';
		}
		
		echo '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">';
		foreach( $tabs as $key => $value ) {
			$active = ( $key == $tab ) ? 'nav-tab-active' : '';
			echo '<a class="nav-tab '.$active.'" href="'.admin_url('admin.php?page=checkout_form_editor&tab='.$key).'">'.$value.'</a>';
		}
		echo '</h2>';
		
		if(!empty($sections)){
			echo '<ul class="wcfe-sections">';
			$size = sizeof($sections); $i = 0;
			foreach( $sections as $key ) {
				$i++;
				$active = ( $key == $section ) ? 'current' : '';
				$url = 'admin.php?page=checkout_form_editor&tab=fields&section='.$key;
				echo '<li>';
				echo '<a href="'. admin_url($url) .'" class="'. $active .'" >'.ucwords($key).' '.esc_html__('Fields', 'wcfe').'</a>';
				echo ($size > $i) ? ' ' : '';
				echo '</li>';				
			}
			echo '</ul>';
		} 
		?>
	<?php }

	/**
	 * Get Current Tab
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */
	function get_current_tab(){
		return isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'fields';
	}

	/**
	 * Get Current Section
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */
	function get_current_section(){
		$tab = $this->get_current_tab();
		$section = '';
		if($tab === 'fields'){
			$section = isset( $_GET['section'] ) ? esc_attr( $_GET['section'] ) : 'billing';
		}
		return $section;
	}

	/**
	 * Heading on Dashboard
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */
	function render_checkout_fields_heading_row() { ?>
		<th class="sort"></th>
		<th class="check-column" style="padding-left:0px !important;">
			<input type="checkbox" style="margin-left:7px;" onclick="wcfeSelectAllCheckoutFields(this)"/> 
		</th>
		<th class="name"><?php esc_html_e('Name','wcfe'); ?></th>
		<th class="id"><?php esc_html_e('Type','wcfe'); ?></th>
		<th><?php esc_html_e('Label','wcfe'); ?></th>
		<th><?php esc_html_e('Placeholder','wcfe'); ?></th>
		<th><?php esc_html_e('Validation Rules','wcfe'); ?></th>
        <th class="status"><?php esc_html_e('Required','wcfe'); ?></th>
		<th class="status"><?php esc_html_e('Enabled','wcfe'); ?></th>	
        <th class="status"><?php esc_html_e('Edit','wcfe'); ?></th>	
    <?php }

	/**
	 * Action Field Button on Dashboard
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function render_actions_row($section) { ?>
        <th colspan="7">
            <button type="button" class="button button-primary" onclick="openNewFieldForm('<?php echo $section; ?>')"><?php _e( '+ Add new field', 'wcfe' ); ?></button>
            <button type="button" class="button" onclick="removeSelectedFields()"><?php _e( 'Remove', 'wcfe' ); ?></button>
            <button type="button" class="button" onclick="enableSelectedFields()"><?php _e( 'Enable', 'wcfe' ); ?></button>
            <button type="button" class="button" onclick="disableSelectedFields()"><?php _e( 'Disable', 'wcfe' ); ?></button>
        </th>
        <th colspan="4">
        	<input type="submit" name="save_fields" class="button-primary" value="<?php _e( 'Save changes', 'wcfe' ) ?>" style="float:right" />
            <input type="submit" name="reset_fields" class="button" value="<?php _e( 'Reset to default fields', 'wcfe' ) ?>" style="float:right; margin-right: 5px;" 
			onclick="return confirm('Are you sure you want to reset to default fields? all your changes will be deleted.');"/>
        </th>  
    <?php }

	/**
	 * The Editor
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function the_editor() {
		$tab = $this->get_current_tab();
		if( $tab === 'fields' ){
			$this->checkout_form_field_editor();
		} elseif($tab === 'premium_features') {
			$this->checkout_form_premium_feature();
		} elseif($tab === 'our_plugins') {
			$this->checkout_form_premium_our_plugins();
		}
	}

	function checkout_form_premium_feature() {
		$section = $this->get_current_section();
		echo '<div class="wcfe-wrap">';
			$this->render_tabs_and_sections();
		?>
			<div class="th-nice-box">
				<h2>Key Features of WooCommerce Checkout Field Editor Pro</h2>
				<p><b>Checkout Field Editor For WooCommerce</b> plugin comes with several advanced features that let you create an organized checkout page. With these premium features, bring your checkout page to its next level.</p>
				<ul class="feature-list star-list">
					<li>17 Custom Checkout Field Types</li>
					<li>Custom section which can be placed at 15 different positions on the checkout page</li>
					<li>Display fields conditionally</li>
					<li>Display sections conditionally</li>
					<li>Price fields with a set of price types</li>
					<li>Custom validations</li>
					<li>Change address display format</li>
					<li>Display fields based on Shipping option or Payment method</li>
					<li>Compatibility with other plugins</li>
					<li>Zapier support</li>
					<li>WPML Compatibility</li>
					<li>Reset all settings on a single click</li>
					<li>Manage field display in emails and order details pages</li>
					<li>Display custom fields optionally at My Account page</li>
					<li>Customise, Disable or delete default WooCommerce fields</li>
					<li>Developer friendly with custom hooks</li>
					<li>Rearrange all fields and sections as per convenience</li>
					<li>Create your own custom classes for styling the field</li>
					<li>Manage your customer registration forms</li>
					<li>Sync billing fields into registration fields</li>
				</ul>
				<p>
					<a class="button big-button" target="_blank" href="https://www.themelocation.com/woocommerce-checkout-register-form-editor/">Upgrade to Premium Version</a>
				</p>
			</div>
			<div class="th-flexbox">
				<div class="th-flexbox-child th-nice-box">
					<h2>Available Field types</h2>
					<p>Following are the custom checkout field types available in the Checkout Field Editor plugin.</p>
					<ul class="feature-list">
						<li>Text</li>
						<li>Hidden</li>
						<li>Password</li>
						<li>Telephone</li>
						<li>Email</li>
						<li>Number</li>
						<li>Textarea</li>
						<li>Select</li>
						<li>Multi Select</li>
						<li>Radio</li>
						<li>Checkbox</li>
						<li>Checkbox Group</li>
						<li>Date picker</li>
						<li>Time picker</li>
						<li>File Upload</li>
						<li>Multiple File Upload</li>
						<li>Heading</li>
						<li>Label</li>
					</ul>
				</div>
				<div class="th-flexbox-child th-nice-box">
					<h2>Display Sections Conditionally</h2>
					<p>Display the custom sections on your checkout page based on the conditions you set. Following are the positions where these checkout sections can be displayed:</p>
					<ul class="feature-list">
						<li>Before customer details</li>
						<li>After customer details</li>
						<li>Before billing form</li>
						<li>After billing form</li>
						<li>Before shipping form</li>
						<li>After shipping form</li>
						<li>Before registration form</li>
						<li>After registration form</li>
						<li>Before order notes</li>
						<li>After order notes</li>
						<li>Before terms and conditions</li>
						<li>After terms and conditions</li>
						<li>Before submit button</li>
						<li>After submit button</li>
						<li>Inside a custom step created using WooCommerce MultiStep Checkout</li>
					</ul>
				</div>
			</div>
			<div class="th-flexbox">
				<div class="th-flexbox-child th-nice-box">
					<h2>Display Fields conditionally</h2>
					<p>Display the custom and default checkout fields based on the conditions you provide. Conditions on which the fields can be displayed are:</p>
					<ul class="feature-list">
						<li>Cart Contents</li>
						<li>Cart Subtotal</li>
						<li>Cart Total</li>
						<li>User Roles</li>
						<li>Product</li>
						<li>Product Variation</li>
						<li>Product Category</li>
						<li>Based on other field values</li>
					</ul>
				</div>
				<div class="th-flexbox-child th-nice-box">
					<h2>Add price fields and choose the price type</h2>
					<p>With the premium version of the Checkout Page Editor plugin, add an extra price value to the total price by creating a field with price into the checkout form.The available price types that can be added to WooCommerce checkout fields are:</p>
					<ul class="feature-list">
						<li>Fixed Price</li>
						<li>Custom Price</li>
						<li>Percentage of Cart Total</li>
						<li>Percentage of Subtotal</li>
						<li>Percent of Subtotal excluding tax</li>
						<li>Dynamic Price</li>
					</ul>
				</div>
			</div>		
		</div>
		<?php
	}	
	
	function checkout_form_premium_our_plugins() {
		$section = $this->get_current_section();
		echo '<div class="wcfe-wrap">';
			$this->render_tabs_and_sections();
			$action = 'install-plugin';			
		?>
			<div class="th-plugins-wrapper featured">
				<div class="th-plugins-child">
					<div class="th-title-box">
						<img src="https://ps.w.org/remove-add-to-cart-woocommerce/assets/icon-128x128.png?rev=1786874" alt="MultiStep Checkout for WooCommerce">
						<h3><a href="https://wordpress.org/plugins/remove-add-to-cart-woocommerce/" target="_blank">Remove Add to Cart WooCommerce</a></h3>
					</div>
					<p>Using the compatibility feature of remove add-to-cart and add custom button in your WooCommerce products</p>
					<span class="plugin-card-woo-multistep-checkout">
					<a href="<?php echo wp_nonce_url( add_query_arg(array('action' => $action,'plugin' => 'remove-add-to-cart-woocommerce'), admin_url( 'update.php' )),$action.'_'.'remove-add-to-cart-woocommerce');?>" class="button-primary install-now" data-originaltext="Install now" data-slug="remove-add-to-cart-woocommerce" aria-label="Install now">Install now</a>
					</span>
				</div>
				<div class="th-plugins-child">
					<div class="th-title-box">
						<img src="https://s.w.org/plugins/geopattern-icon/woo-products-widgets-for-elementor.svg" alt="Email Customizer for WooCommerce">
						<h3><a href="https://wordpress.org/plugins/email-customizer-for-woocommerce" target="_blank">Woo Products Widgets For Elementor</a></h3>
					</div>
					<p>WooCommerce product grid form elementor</p>
					<span class="plugin-card-email-customizer-for-woocommerce">
					<a href="<?php echo wp_nonce_url( add_query_arg(array('action' => $action,'plugin' => 'woo-products-widgets-for-elementor'), admin_url( 'update.php' )),$action.'_'.'woo-products-widgets-for-elementor');?>" class="button-primary install-now install-email-customizer-for-woocommerce th-plugin-action" data-originaltext="Install now" data-name="Email Customizer for WooCommerce" data-slug="woo-products-widgets-for-elementor" aria-label="Install now">Install now</a>
					</span>
				</div>
			</div>		
		</div>
		<?php
	}

	/**
	 * Checkout Form Field Editor
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function checkout_form_field_editor() {
		$section = $this->get_current_section();
						
		echo '<div class="wcfe-wrap">';
			$this->render_tabs_and_sections();
			
			if ( isset( $_POST['save_fields'] ) ) {
				echo $this->save_options( $section );
			}
				
			if ( isset( $_POST['reset_fields'] ) )
				echo $this->reset_checkout_fields();		
	
			global $supress_field_modification;
			$supress_field_modification = false; 

			if( $section != 'account' ) { ?>
			<form method="post" id="wcfe_checkout_fields_form" action="">
            	<table id="wcfe_checkout_fields" class="wc_gateways widefat" cellspacing="0">
					<thead>
                    	<tr><?php $this->render_actions_row( $section ); ?></tr>
                    	<tr><?php $this->render_checkout_fields_heading_row(); ?></tr>						
					</thead>
                    <tfoot>
                    	<tr><?php $this->render_checkout_fields_heading_row(); ?></tr>
						<tr><?php $this->render_actions_row($section); ?></tr>
					</tfoot>
					<tbody class="ui-sortable">
                    <?php 
					$i=0;
					foreach( $this->get_fields( $section ) as $name => $options ) :	
						if ( isset( $options['custom'] ) && $options['custom'] == 1 ) {
							$options['custom'] = '1';
						} else {
							$options['custom'] = '0';
						}
											
						if ( !isset( $options['label'] ) ) {
							$options['label'] = '';
						}
						
						if ( !isset( $options['placeholder'] ) ) {
							$options['placeholder'] = '';
						}
						
						if ( !isset( $options['price'] ) ) {
							$options['price'] = '';
						}
						
						if ( !isset( $options['price_unit'] ) ) {
							$options['price_unit'] = '';
						}
						
						if ( !isset( $options['price_type'] ) ) {
							$options['price_type'] = '';
						}
						
						if ( !isset( $options['taxable'] ) ) {
							$options['taxable'] = '';
						}
						if ( !isset( $options['tax_class'] ) ) {
							$options['tax_class'] = '';
						}		
						if ( !isset( $options['min_time'] ) ) {
							$options['min_time'] = '';
						}
						
						if ( !isset( $options['max_time'] ) ) {
							$options['max_time'] = '';
						}
									
						if ( !isset( $options['time_step'] ) ) {
							$options['time_step'] = '';
						}
						
						if ( !isset( $options['time_format'] ) ) {
							$options['time_format'] = '';
						}						
					
						if( !isset( $options['rules'] ) ) {
							$options['rules'] = '';
						}
					
						if( !isset( $options['rules_action'] ) ) {
							$options['rules_action'] = '';
						}
						
						if( !isset( $options['rules_ajax'] ) ) {
							$options['rules_ajax'] = '';
						}
						
						if( !isset( $options['rules_action_ajax'] ) ) {
							$options['rules_action_ajax'] = '';
						}
					
						if( isset( $options['options_json'] ) && is_array($options['options_json']) ) {
							$options['options_json'] = urlencode(json_encode($options['options_json']));
						} else{
							$options['options_json'] = '';
						}
					
						if( isset( $options['extoptions'] ) && is_array($options['extoptions']) ) {
							$options['extoptions'] = implode(",", $options['extoptions']);
						} else{
							$options['extoptions'] = '';
						}
					
						if( isset( $options['class'] ) && is_array($options['class']) ) {
							$options['class'] = implode(",", $options['class']);
						} else{
							$options['class'] = '';
						}
					
						if( isset( $options['label_class'] ) && is_array($options['label_class']) ) {
							$options['label_class'] = implode(",", $options['label_class']);
						} else{
							$options['label_class'] = '';
						}
					
						if( isset( $options['validate'] ) && is_array($options['validate']) ) {
							$options['validate'] = implode(",", $options['validate']);
						} else{
							$options['validate'] = '';
						}
											
						if ( isset( $options['required'] ) && $options['required'] == 1 ) {
							$options['required'] = '1';
						} else {
							$options['required'] = '0';
						}
		
						if ( isset( $options['access'] ) && $options['access'] == 1 ) {
							$options['access'] = '1';
						} else {
							$options['access'] = '0';
						}
						
						if ( !isset( $options['enabled'] ) || $options['enabled'] == 1 ) {
							$options['enabled'] = '1';
						} else {
							$options['enabled'] = '0';
						}

						if ( !isset( $options['type'] ) ) {
							$options['type'] = 'text';
						}
						
						if ( isset( $options['show_in_email'] ) && $options['show_in_email'] == 1 ) {
							$options['show_in_email'] = '1';
						} else {
							$options['show_in_email'] = '0';
						}
						
						if ( isset( $options['show_in_order'] ) && $options['show_in_order'] == 1 ) {
							$options['show_in_order'] = '1';
						} else {
							$options['show_in_order'] = '0';
						}

						if ( isset( $options['show_in_my_account'] ) && $options['show_in_my_account'] == 1 ) {
							$options['show_in_my_account'] = '1';
						} else {
							$options['show_in_my_account'] = '0';
						}
						?>
						<?php
						$disabled = false;
						if( $name == 'account_username' || $name == 'account_password' ) { 
							$disabled = true;
						?>
						<tr class="row_<?php echo $i; echo ' wcfe-disabled'; ?>">
						<?php } else { ?>
						<tr class="row_<?php echo $i; echo($options['enabled'] == 1 ? '' : ' wcfe-disabled') ?>">
							<?php } ?>
                        	<td width="1%" class="sort ui-sortable-handle">
                            	<input type="hidden" name="f_custom[<?php echo $i; ?>]" class="f_custom" value="<?php echo $options['custom']; ?>" />
                                <input type="hidden" name="f_order[<?php echo $i; ?>]" class="f_order" value="<?php echo $i; ?>" />
                                <input type="hidden" name="f_name[<?php echo $i; ?>]" class="f_name" value="<?php echo esc_attr( $name ); ?>" />
                                <input type="hidden" name="f_name_new[<?php echo $i; ?>]" class="f_name_new" value="" />
                                <input type="hidden" name="f_type[<?php echo $i; ?>]" class="f_type" value="<?php echo $options['type']; ?>" />   
                                <input type="hidden" name="f_label[<?php echo $i; ?>]" class="f_label" value="<?php echo htmlspecialchars( $options['label'] ); ?>" />
                                <input type="hidden" name="f_extoptions[<?php echo $i; ?>]" class="f_extoptions" value="<?php echo($options['extoptions']) ?>" />
								<input type="hidden" name="f_access[<?php echo $i; ?>]" class="f_access" value="<?php echo($options['access']) ?>" />
                                <?php if(isset($options['maxlength'])){ ?>
                                <input type="hidden" name="f_maxlength[<?php echo $i; ?>]" class="f_maxlength" value="<?php echo $options['maxlength']; ?>" />
								<?php } ?>
								<input type="hidden" name="f_placeholder[<?php echo $i; ?>]" class="f_placeholder" value="<?php echo $options['placeholder']; ?>" />
								<input type="hidden" name="i_price[<?php echo $i; ?>]" class="f_price" value="<?php echo $options['price']; ?>" />
								<input type="hidden" name="i_price_unit[<?php echo $i; ?>]" class="f_price_unit" value="<?php echo $options['price_unit']; ?>" />
								<input type="hidden" name="i_price_type[<?php echo $i; ?>]" class="f_price_type" value="<?php echo $options['price_type']; ?>" />
								<input type="hidden" name="i_taxable[<?php echo $i; ?>]" class="f_taxable" value="<?php echo $options['taxable']; ?>" />
								<input type="hidden" name="i_tax_class[<?php echo $i; ?>]" class="f_tax_class" value="<?php echo $options['tax_class']; ?>" />
								<input type="hidden" name="i_min_time[<?php echo $i; ?>]" class="i_min_time" value="<?php echo $options['min_time']; ?>" />
								<input type="hidden" name="i_max_time[<?php echo $i; ?>]" class="i_max_time" value="<?php echo $options['max_time']; ?>" />
								<input type="hidden" name="i_time_step[<?php echo $i; ?>]" class="i_time_step" value="<?php echo $options['time_step']; ?>" />
								<input type="hidden" name="i_time_format[<?php echo $i; ?>]" class="i_time_format" value="<?php echo $options['time_format']; ?>" />
								<input type="hidden" name="f_rules_action[<?php echo $i; ?>]" class="f_rules_action" value="<?php echo $options['rules_action']; ?>" />
								<input type="hidden" name="f_rules[<?php echo $i; ?>]" class="f_rules" value="<?php echo $options['rules']; ?>" />
								<input type="hidden" name="f_rules_action_ajax[<?php echo $i; ?>]" class="f_rules_action_ajax" value="<?php echo $options['rules_action_ajax']; ?>" />
								<input type="hidden" name="f_rules_ajax[<?php echo $i; ?>]" class="f_rules_ajax" value="<?php echo $options['rules_ajax']; ?>" />
                                <input type="hidden" name="f_options[<?php echo $i; ?>]" class="f_options" value="<?php echo($options['options_json']); ?>" />
								<input type="hidden" name="f_class[<?php echo $i; ?>]" class="f_class" value="<?php echo $options['class']; ?>" />
                                <input type="hidden" name="f_label_class[<?php echo $i; ?>]" class="f_label_class" value="<?php echo $options['label_class']; ?>" />                          
								<input type="hidden" name="f_required[<?php echo $i; ?>]" class="f_required" value="<?php echo($options['required']); ?>" />
                                <input type="hidden" name="f_enabled[<?php echo $i; ?>]" class="f_enabled" value="<?php echo($options['enabled']); ?>" />
                                <input type="hidden" name="f_validation[<?php echo $i; ?>]" class="f_validation" value="<?php echo($options['validate']); ?>" />
                                <input type="hidden" name="f_show_in_email[<?php echo $i; ?>]" class="f_show_in_email" value="<?php echo($options['show_in_email']); ?>" />
                                <input type="hidden" name="f_show_in_order[<?php echo $i; ?>]" class="f_show_in_order" value="<?php echo($options['show_in_order']); ?>" /><input type="hidden" name="f_show_in_my_account[<?php echo $i; ?>]" class="f_show_in_my_account" value="<?php echo($options['show_in_my_account']); ?>" />
                                <input type="hidden" name="f_deleted[<?php echo $i; ?>]" class="f_deleted" value="0" />
                            </td>
                            <td class="td_select"><input type="checkbox" name="select_field"/></td>
                            <td class="td_name"><?php echo esc_attr( $name ); ?></td>
                            <td class="td_type"><?php echo $options['type']; ?></td>
                            <td class="td_label"><?php echo $options['label']; ?></td>
                            
                            <td class="td_placeholder"><?php echo $options['placeholder']; ?></td>
                            <td class="td_validate"><?php echo $options['validate']; ?></td>
                            <td class="td_required status"><?php echo($options['required'] == 1 ? '<span class="status-enabled tips">Yes</span>' : '-' ) ?></td>
                            
                            <td class="td_enabled status"><?php echo($options['enabled'] == 1 ? '<span class="status-enabled tips">Yes</span>' : '-' ) ?></td>
                            <td class="td_edit ceter-text">
                            	<button type="button" class="f_edit_btn" <?php if($disabled){ echo  'disabled'; } ?>  <?php echo($options['enabled'] == 1 ? '' : 'disabled') ?> 
                                onclick="openEditFieldForm(this,<?php echo $i; ?>)"><i class="dashicons dashicons-edit"></i></button>
                            </td>
                    	</tr>
                    <?php $i++; endforeach; ?>
                	</tbody>
				</table> 
            </form>

			<?php } else { ?>
				<div class="premium-message"><a href="https://www.themelocation.com/woocommerce-checkout-register-form-editor/"><img src="<?php echo plugins_url('/assets/css/account_sec.jpg', dirname(__FILE__)); ?>" ></a></div>
			<?php } ?>

            <?php
            	$this->wcfe_new_field_form_pp();
				$this->wcfe_edit_field_form_pp();
			?>
    	</div>
    <?php }

	/**
	 * Save Options
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function save_wcfe_options(){
		$section_label = "";
		$sync_with_checkout = "";
		
		foreach($_POST['formdata'] as $formRow){
			if($formRow['name'] == 'section_label'){
				$section_label = $formRow['value'];
			}
			
			if($formRow['name'] == 'sync_with_checkout'){
				$sync_with_checkout = $formRow['value'];
			}
		}
		
		if(!empty($section_label)){
			update_option( 'wcfe_account_label', $section_label);
		}

		if( !empty($sync_with_checkout) ) {
			update_option( 'wcfe_account_sync_fields', $sync_with_checkout);
		} else{
			update_option( 'wcfe_account_sync_fields', 'off');
		}
			
		echo '1';
			
		die();
	}
	
	/**
	 * Save Plug in Options
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */					
	function save_options( $section ) {
		$o_fields      = $this->get_fields( $section );

		$fields        = $o_fields;
	
		$f_order       = !empty( $_POST['f_order'] ) ? $_POST['f_order'] : array();
		
		$f_names       = !empty( $_POST['f_name'] ) ? $_POST['f_name'] : array();
		
		$f_names_new   = !empty( $_POST['f_name_new'] ) ? $_POST['f_name_new'] : array();
	
		$f_types       = !empty( $_POST['f_type'] ) ? $_POST['f_type'] : array();
		$f_labels      = !empty( $_POST['f_label'] ) ? $_POST['f_label'] : array();
		$f_extoptions  = !empty( $_POST['f_extoptions'] ) ? $_POST['f_extoptions'] : array();
		$f_access      = !empty( $_POST['f_access'] ) ? $_POST['f_access'] : array();
		
		$f_placeholder = !empty( $_POST['f_placeholder'] ) ? $_POST['f_placeholder'] : array();
		
		$i_price       = !empty( $_POST['i_price'] ) ? $_POST['i_price'] : array();
		$i_price_unit  = !empty( $_POST['i_price_unit'] ) ? $_POST['i_price_unit'] : array();
		$i_price_type  = !empty( $_POST['i_price_type'] ) ? $_POST['i_price_type'] : array();
		
		$i_min_time    = !empty( $_POST['i_min_time'] ) ? $_POST['i_min_time'] : array();
		$i_max_time    = !empty( $_POST['i_max_time'] ) ? $_POST['i_max_time'] : array();
		
		$i_time_step   = !empty( $_POST['i_time_step'] ) ? $_POST['i_time_step'] : array();
		$i_time_format = !empty( $_POST['i_time_format'] ) ? $_POST['i_time_format'] : array();
		
		$i_taxable     = !empty( $_POST['i_taxable'] ) ? $_POST['i_taxable'] : array();
		$i_tax_class   = !empty( $_POST['i_tax_class'] ) ? $_POST['i_tax_class'] : array();
		
		$f_maxlength   = !empty( $_POST['f_maxlength'] ) ? $_POST['f_maxlength'] : array();
		
		
		if( isset($_POST['f_options']) ){
			$f_options   = !empty( $_POST['f_options'] ) ? $_POST['f_options'] : array();
		}
				
		$f_class       = !empty( $_POST['f_class'] ) ? $_POST['f_class'] : array();
		
		$f_required    = !empty( $_POST['f_required'] ) ? $_POST['f_required'] : array();
		
		$f_enabled     = !empty( $_POST['f_enabled'] ) ? $_POST['f_enabled'] : array();
		
		$f_show_in_email = !empty( $_POST['f_show_in_email'] ) ? $_POST['f_show_in_email'] : array();

		$f_show_in_order = !empty( $_POST['f_show_in_order'] ) ? $_POST['f_show_in_order'] : array();

		$f_show_in_my_account = !empty( $_POST['f_show_in_my_account'] ) ? $_POST['f_show_in_my_account'] : array();
		
		$f_validation  = !empty( $_POST['f_validation'] ) ? $_POST['f_validation'] : array();

		$f_deleted     = !empty( $_POST['f_deleted'] ) ? $_POST['f_deleted'] : array();
						
		$f_position    = !empty( $_POST['f_position'] ) ? $_POST['f_position'] : array();

		$f_display_options = !empty( $_POST['f_display_options'] ) ? $_POST['f_display_options'] : array();
		
		$max  = max( array_map( 'absint', array_keys( $f_names ) ) );
			
		for ( $i = 0; $i <= $max; $i ++ ) {
			$name     = empty( $f_names[$i] ) ? '' : urldecode( sanitize_title( wc_clean( stripslashes( $f_names[$i] ) ) ) );

			$new_name = empty( $f_names_new[$i] ) ? '' : urldecode( sanitize_title( wc_clean( stripslashes( $f_names_new[$i] ) ) ) );


			$allow_override = apply_filters('wcfe_allow_default_field_override_'.$new_name, false);
			
			if(!empty($f_deleted[$i]) && $f_deleted[$i] == 1){
				unset( $fields[$name] );
				continue;
			}

			// Check reserved names
			if($this->is_reserved_field_name( $new_name ) && !$allow_override){
				continue;
			}
		
			//if update field
			if( $name && $new_name && $new_name !== $name ){
				if ( isset( $fields[$name] ) ) {
					$fields[$new_name] = $fields[$name];
				} else {
					$fields[$new_name] = array();
				}
				unset( $fields[$name] );
				$name = $new_name;
			} else {
				$name = $name ? $name : $new_name;
			}

			if(!$name){
				continue;
			}
						
			//if new field
			if ( !isset( $fields[$name] ) ) {
				$fields[$name] = array();
			}

			$o_type  = isset( $o_fields[$name]['type'] ) ? $o_fields[$name]['type'] : 'text';
			
			$allowed_tags = array(
				'a' => array(
					'class' => array(),
					'href'  => array(),
					'rel'   => array(),
					'title' => array(),
				),
				'abbr' => array(
					'title' => array(),
				),
				'b' => array(),
				'blockquote' => array(
					'cite'  => array(),
				),
				'cite' => array(
					'title' => array(),
				),
				'code' => array(),
				'del' => array(
					'datetime' => array(),
					'title' => array(),
				),
				'dd' => array(),
				'div' => array(
					'class' => array(),
					'title' => array(),
					'style' => array(),
				),
				'dl' => array(),
				'dt' => array(),
				'em' => array(),
				'h1' => array(),
				'h2' => array(),
				'h3' => array(),
				'h4' => array(),
				'h5' => array(),
				'h6' => array(),
				'i' => array(),
				'img' => array(
					'alt'    => array(),
					'class'  => array(),
					'height' => array(),
					'src'    => array(),
					'width'  => array(),
				),
				'li' => array(
					'class' => array(),
				),
				'ol' => array(
					'class' => array(),
				),
				'p' => array(
					'class' => array(),
				),
				'q' => array(
					'cite' => array(),
					'title' => array(),
				),
				'span' => array(
					'class' => array(),
					'title' => array(),
					'style' => array(),
				),
				'strike' => array(),
				'strong' => array(),
				'ul' => array(
					'class' => array(),
				),
			);
	
			$fields[$name]['type']    	  = empty( $f_types[$i] ) ? $o_type : wc_clean( $f_types[$i] );

			$fields[$name]['label']   	  = empty( $f_labels[$i] ) ? '' : wp_kses_post( trim( stripslashes( $f_labels[$i] ) ) );			
					
			$fields[$name]['access']    = empty( $f_access[$i] ) ? false : true;
					
			
			$fields[$name]['placeholder'] = empty( $f_placeholder[$i] ) ? '' : wc_clean( stripslashes( $f_placeholder[$i] ) );
			
			$fields[$name]['price'] = empty( $i_price[$i] ) ? '' : wc_clean( stripslashes( $i_price[$i] ) );
			$fields[$name]['price_unit'] = empty( $i_price_unit[$i] ) ? '' : wc_clean( stripslashes( $i_price_unit[$i] ) );

			$fields[$name]['price_type'] = empty( $i_price_type[$i] ) ? '' : wc_clean( stripslashes( $i_price_type[$i] ) );

			$fields[$name]['taxable'] = empty( $i_taxable[$i] ) ? '' : wc_clean( stripslashes( $i_taxable[$i] ) );

			$fields[$name]['tax_class'] = empty( $i_tax_class[$i] ) ? '' : wc_clean( stripslashes( $i_tax_class[$i] ) );
			
			$fields[$name]['min_time'] = empty( $i_min_time[$i] ) ? '' : wc_clean( stripslashes( $i_min_time[$i] ) );

			$fields[$name]['max_time'] = empty( $i_max_time[$i] ) ? '' : wc_clean( stripslashes( $i_max_time[$i] ) );
			
			$fields[$name]['time_step'] = empty( $i_time_step[$i] ) ? '' : wc_clean( stripslashes( $i_time_step[$i] ) );

			$fields[$name]['time_format'] = empty( $i_time_format[$i] ) ? '' : wc_clean( stripslashes( $i_time_format[$i] ) );
			
			$fields[$name]['options_json'] = empty( $f_options[$i] ) ? '' : json_decode(urldecode($f_options[$i]),true);
			
			$fields[$name]['maxlength'] = empty( $f_maxlength[$i] ) ? '' : wc_clean( stripslashes( $f_maxlength[$i] ) );

			$fields[$name]['class'] = empty( $f_class[$i] ) ? array() : array_map( 'wc_clean', explode( ',', $f_class[$i] ) );

			$fields[$name]['label_class'] = empty( $f_label_class[$i] ) ? array() : array_map( 'wc_clean', explode( ',', $f_label_class[$i] ) );
			
			$fields[$name]['rules_action'] = empty( $f_rules_action[$i] ) ? '' : $f_rules_action[$i];

			$fields[$name]['rules'] = empty( $f_rules[$i] ) ? '' : $f_rules[$i];

			$fields[$name]['rules_action_ajax'] = empty( $f_rules_action_ajax[$i] ) ? '' : $f_rules_action_ajax[$i];

			$fields[$name]['rules_ajax'] = empty( $f_rules_ajax[$i] ) ? '' : $f_rules_ajax[$i];
			
			$fields[$name]['required'] = empty( $f_required[$i] ) ? false : true;
			
			$fields[$name]['enabled'] = empty( $f_enabled[$i] ) ? false : true;

			$fields[$name]['order']  = isset($f_order[$i]) && is_numeric($f_order[$i]) ? wc_clean( $f_order[$i] ) : '';

			if (!in_array( $name, $this->locale_fields )){
				$fields[$name]['validate'] = empty( $f_validation[$i] ) ? array() : explode( ',', $f_validation[$i] );
			}

			$fields[$name]['extoptions'] = empty( $f_extoptions[$i] ) ? array() : explode( ',', $f_extoptions[$i] );
				
			if (!$this->is_default_field_name( $name )){
				$fields[$name]['custom'] = true;
				$fields[$name]['show_in_email'] = empty( $f_show_in_email[$i] ) ? false : true;
				$fields[$name]['show_in_order'] = empty( $f_show_in_order[$i] ) ? false : true;
				$fields[$name]['show_in_my_account'] = empty( $f_show_in_my_account[$i] ) ? false : true;
			} else {
				$fields[$name]['custom'] = false;
			}
			
			$fields[$name]['label']   = $fields[$name]['label'];

			$fields[$name]['placeholder'] = esc_html__($fields[$name]['placeholder'], 'woocommerce');

			$fields[$name]['maxlength'] = esc_html__($fields[$name]['maxlength'], 'woocommerce');	
		}
		
		uasort( $fields, array( $this, 'sort_fields_by_order' ) );

		$result = update_option( 'wc_fields_' . $section, $fields );

		if ( $result == true ) {
			echo '<div class="updated"><p>' . esc_html__( 'Your changes were saved.', 'wcfe' ) . '</p></div>';
		} else {
			echo '<div class="error"><p> ' . esc_html__( 'Your changes were not saved due to an error (or you made none!).', 'wcfe' ) . '</p></div>';
		}
	}
}
