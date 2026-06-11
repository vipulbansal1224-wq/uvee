<?php
/**
 * Plugin Name: WooCommerce Checkout & Account Field Editor
 * Description: Customize WooCommerce checkout and my account page (Add, Edit, Delete and re-arrange fields).
 * Author:      ThemeLocation
 * Version:     1.2.8
 * Author URI:  https://www.themelocation.com
 * Plugin URI:  
 * Text Domain: wcfe
 * Domain Path: /languages
 * WC requires at least: 3.0.0
 * WooCommerce tested up to: 3.4.2
 */

// Create a helper function for easy SDK access.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WooCommerce Checkout & Account Field Main Fields
 *
 * @package WooCommerce Checkout & Account Field Editor
 * @since 1.0
 */	
function tl_fields() {
    global $tl_fields;

    if ( ! isset( $tl_fields ) ) {
        // Include Freemius SDK.
        require_once dirname(__FILE__) . '/freemius/start.php';

        $tl_fields = fs_dynamic_init( array(
            'id'                  => '1707',
            'slug'                => 'add-fields-to-checkout-page-woocommerce',
            'type'                => 'plugin',
            'public_key'          => 'pk_3abcc29391266d676ac7996328bce',
            'is_premium'          => false,
            // If your plugin is a serviceware, set this option to false.
            'has_premium_version' => true,
            'has_addons'          => false,
            'has_paid_plans'      => true,
            'menu'                => array(
                'slug'           => 'checkout_form_editor',
                'support'        => false,
                'parent'         => array(
                    'slug' => 'woocommerce',
                ),
            ),
        ) );
    }

    return $tl_fields;
}

tl_fields();

// Signal that SDK was initiated.
do_action( 'tl_fields_loaded' );

/**
 * Is WooCommerce active
 *
 * @package WooCommerce Checkout & Account Field Editor
 * @since 1.0
 */	
if ( !function_exists( 'is_woocommerce_active' ) ) {
    function is_woocommerce_active() {
        $active_plugins = (array) get_option( 'active_plugins', array() );
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }
        return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
    }
}

/**
 * WooCommerce Checkout & Account Filed plugin activation
 *
 * @package WooCommerce Checkout & Account Field Editor
 * @since 1.0
 */	
function wcfe_activate() {
    register_uninstall_hook( __FILE__, 'wcfe_uninstall' );
}
register_activation_hook( __FILE__, 'wcfe_activate' );

/**
 * WooCommerce Checkout & Account Field Editor Uninstall function 
 *
 * @package WooCommerce Checkout & Account Field Editor
 * @since 1.0
 */	
function wcfe_uninstall() {
    delete_option( 'wcfe_account_label' );
    delete_option( 'wc_fields_account' );
    delete_option( 'wc_fields_billing' );
    delete_option( 'wc_fields_shipping' );
    delete_option( 'wc_fields_additional' );
}

if ( is_woocommerce_active() ) {
	/**
	 * Load Language function 
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
    load_plugin_textdomain( 'wcfe', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    /**
     * WooCommerce Checkout & Account Field Editor Lite function 
     *
     * @package 
     * @since 1.0
     */	
    function wcfe_init_checkout_field_editor_lite() {
        global  $supress_field_modification;

        $supress_field_modification = false;

        define( 'WCFE_VERSION', '1.2.6' );

        !defined( 'WCFE_URL' ) && define( 'WCFE_URL', plugins_url( '/', __FILE__ ) );

        if ( !class_exists( 'WC_Checkout_Field_Editor' ) ) {
            require_once 'classes/class-wc-checkout-field-editor.php';
        }
        if ( !class_exists( 'WC_Checkout_Field_Editor_Export_Handler' ) ) {
            require_once 'classes/class-wc-checkout-field-editor-export-handler.php';
        }

        new WC_Checkout_Field_Editor_Export_Handler();

        $GLOBALS['WC_Checkout_Field_Editor'] = new WC_Checkout_Field_Editor();
    }
    
    /**
     * WooCommerce Checkout & Account Field Editor Lite Local Fields
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    add_action( 'init', 'wcfe_init_checkout_field_editor_lite' );
    function wcfe_is_locale_field( $field_name ) {
        if ( !empty($field_name) && in_array( $field_name, array(
            'billing_address_1',
            'billing_address_2',
            'billing_state',
            'billing_postcode',
            'billing_city',
            'shipping_address_1',
            'shipping_address_2',
            'shipping_state',
            'shipping_postcode',
            'shipping_city'
        ) ) ) {
            return true;
        }
        return false;
    }
    
    /**
     * WooCOmmerce Version Check 
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    function wcfe_woocommerce_version_check( $version = '3.0' ) {
        if ( function_exists( 'is_woocommerce_active' ) && is_woocommerce_active() ) {
            global  $woocommerce ;
            if ( version_compare( $woocommerce->version, $version, ">=" ) ) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * WooCommerce Checkout & Account Field Editor Script
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    function wcfe_enqueue_scripts() {
        global $wp_scripts;
        
        if ( is_checkout() ) {
            $in_footer = apply_filters( 'wcfe_enqueue_script_in_footer', true );

            wp_register_script('wcfe-checkout-editor-frontend', plugins_url('/add-fields-to-checkout-page-woocommerce/assets/js/wcfe-checkout-field-editor-frontend.js', dirname(__FILE__)), array( 'jquery', 'select2', 'jquery-ui-datepicker'  ), WCFE_VERSION, true );
			
            wp_enqueue_script( 'wcfe-checkout-editor-frontend' );
        }
    }
    add_action( 'wp_enqueue_scripts', 'wcfe_enqueue_scripts' );
   
    /**
     * Hide Additional Fields title if no fields available.
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    function wcfe_enable_order_notes_field() {
        global  $supress_field_modification ;
        if ( $supress_field_modification ) {
            return $fields;
        }
        $additional_fields = get_option( 'wc_fields_additional' );
        if ( is_array( $additional_fields ) ) {
            $enabled = 0;
            foreach ( $additional_fields as $field ) {
                if ( $field['enabled'] ) {
                    $enabled++;
                }
            }
            return ( $enabled > 0 ? true : false );
        }
        return true;
    }
	

	/**
	 * WooCommerce Checkout & Account Address Field
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
    add_filter( 'woocommerce_enable_order_notes_field', 'wcfe_enable_order_notes_field', 1000 );
    function wcfe_woo_default_address_fields( $fields ) {

        $sname = apply_filters( 'wcfe_address_field_override_with', 'billing' );

        if ( $sname === 'billing' || $sname === 'shipping' ) {
            $address_fields = get_option( 'wc_fields_' . $sname );
            
            if ( is_array( $address_fields ) && !empty($address_fields) && !empty($fields) ) {
                $override_required = apply_filters( 'wcfe_address_field_override_required', true );
                foreach ( $fields as $name => $field ) {
                    $fname = $sname . '_' . $name;
                    if ( wcfe_is_locale_field( $fname ) && $override_required ) {
                        $custom_field = ( isset( $address_fields[$fname] ) ? $address_fields[$fname] : false );
                        if ( $custom_field && !(isset( $custom_field['enabled'] ) && $custom_field['enabled'] == false) ) {
                            $fields[$name]['required'] = ( isset( $custom_field['required'] ) && $custom_field['required'] ? true : false );
                        }
                    }
                }
            }
        }
        return $fields;
    }
   
    /**
     * WooCommerce Checkout & Account Field Country Prepare Local
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    add_filter( 'woocommerce_default_address_fields', 'wcfe_woo_default_address_fields' );
    function wcfe_prepare_country_locale( $fields ) {
        if ( is_array( $fields ) ) {
            foreach ( $fields as $key => $props ) {
                $override_ph = apply_filters( 'wcfe_address_field_override_placeholder', true );
                $override_label = apply_filters( 'wcfe_address_field_override_label', true );
                $override_required = apply_filters( 'wcfe_address_field_override_required', false );
                $override_priority = apply_filters( 'wcfe_address_field_override_priority', true );
                if ( $override_ph && isset( $props['placeholder'] ) ) {
                    unset( $fields[$key]['placeholder'] );
                }
                if ( $override_label && isset( $props['label'] ) ) {
                    unset( $fields[$key]['label'] );
                }

                if($override_required && isset($props['required'])){
                    $fkey = $sname.'_'.$key;
                    if(is_array($address_fields) && isset($address_fields[$fkey])){
                        $cf_props = $address_fields[$fkey];
                        if(is_array($cf_props) && isset($cf_props['required'])){
                            $fields[$key]['required'] = $cf_props['required'] ? true : false;
                        }
                    }
                    //unset($fields[$key]['required']);
                }

                if ( $override_priority && isset( $props['priority'] ) ) {
                    unset( $fields[$key]['priority'] );
                }
            }
        }
        return $fields;
    }

    /**
	 * WooCommerce Checkout & Account Field Country get Local 
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
    add_filter( 'woocommerce_get_country_locale_default', 'wcfe_prepare_country_locale' );
    add_filter( 'woocommerce_get_country_locale_base', 'wcfe_prepare_country_locale' );

    function wcfe_woo_get_country_locale( $locale ) {
        if ( is_array( $locale ) ) {
            foreach ( $locale as $country => $fields ) {
                $locale[$country] = wcfe_prepare_country_locale( $fields );
            }
        }
        return $locale;
    }
    add_filter( 'woocommerce_get_country_locale', 'wcfe_woo_get_country_locale' );

    /**
	 * WooCommerce Checkout & Account Editor Billing Field Lite
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
    function wcfe_billing_fields_lite( $fields, $country ) {
        global  $supress_field_modification ;
        if ( $supress_field_modification ) {
            return $fields;
        }
        if ( is_wc_endpoint_url( 'edit-address' ) ) {
            return $fields;
        } else {
            return wcfe_prepare_address_fields( get_option('wc_fields_billing'), $fields, 'billing', $country );
        }
    }
    add_filter( 'woocommerce_billing_fields', 'wcfe_billing_fields_lite', apply_filters('wcfe_billing_fields_priority', 1000), 2);

    /**
     * WooCommerce Checkout & Account Field Shipping Fields Lite  
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    function wcfe_shipping_fields_lite( $fields, $country ) {
        global $supress_field_modification ;
        if ( $supress_field_modification ) {
            return $fields;
        }
        if ( is_wc_endpoint_url( 'edit-address' ) ) {
            return $fields;
        } else {
            return wcfe_prepare_address_fields( get_option( 'wc_fields_shipping' ), $fields,  'shipping', $country );
        }
    }
    add_filter( 'woocommerce_shipping_fields', 'wcfe_shipping_fields_lite', apply_filters('wcfe_shipping_fields_priority', 1000), 2);

    /**
     * WooCommerce Checkout & Account Field Checkout Fields Lite  
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    function wcfe_checkout_fields_lite( $fields ) {
        global $supress_field_modification ;
        if ( $supress_field_modification ) {
            return $fields;
        }
       
        if ( $additional_fields = get_option( 'wc_fields_additional' ) ) {
            if ( isset( $fields['order'] ) && is_array( $fields['order'] ) ) {
                $fields['order'] = $additional_fields + $fields['order'];
            }
            // check if order_comments is enabled/disabled
            if ( isset( $additional_fields ) && !$additional_fields['order_comments']['enabled'] ) {
                unset( $fields['order']['order_comments'] );
            }
        }
        
        if ( isset( $fields['order'] ) && is_array( $fields['order'] ) ) {
            $fields['order'] = wcfe_prepare_checkout_fields_lite( $fields['order'], true );
        }

        if ( isset( $fields['billing'] ) && is_array( $fields['billing'] ) ) {
            $fields['billing'] = wcfe_prepare_checkout_fields_lite( $fields['billing'], true );
        }
        
        return $fields;
    }
    add_filter( 'woocommerce_checkout_fields', 'wcfe_checkout_fields_lite', apply_filters('wcfe_checkout_fields_priority', 9999) );

    /**
     * WooCommerce Checkout & Account Field Address Fields
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    function wcfe_prepare_address_fields( $fieldset, $original_fieldset = false, $sname = 'billing', $country ) {
        if ( is_array( $fieldset ) && !empty($fieldset) ) {
            $locale = WC()->countries->get_country_locale();
            if ( isset( $locale[$country] ) && is_array( $locale[$country] ) ) {
                foreach ( $locale[$country] as $key => $value ) {
                    if ( is_array( $value ) && isset( $fieldset[$sname . '_' . $key] ) ) {
                        if ( isset( $value['required'] ) ) {
                            $fieldset[$sname . '_' . $key]['required'] = $value['required'];
                        }
                    }
                }
            }
            if ( get_option( 'wc_fields_billing' ) ) {
                $fieldset = wcfe_prepare_checkout_fields_lite( $fieldset, $original_fieldset, $sname );
            } else {
                $fieldset = array_merge( $original_fieldset, $fieldset );
            }
            return $fieldset;
        } else {
            return $original_fieldset;
        }
    }

    /**
     * WooCommerce Checkout & Account Field Extra Register Field  
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    function wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {
	   	if ( get_option( 'wcfe_account_sync_fields' ) && get_option( 'wcfe_account_sync_fields' ) == "on" ) {
	   		foreach ($_POST as $key => $field) {
		        // Validation: Required fields
		        if( strpos( $key, 'billing_' ) !== false ) {
		            if( isset($key) && ( $key == 'billing_country' && empty($field) ) ) {
		            	if( !empty($_POST[$key]) ) {
		            		continue;
		            	} else {
		            		$validation_errors->add( $key.'_error',  __( 'Please select a country.', 'woocommerce' ));
		            	}
		            }
		            if(isset($key) && ( $key == 'billing_first_name' && empty($field) ) ) {
	            		if( !empty($_POST[$key]) ) {
							continue;
						} else {
							$validation_errors->add( $key.'_error', __( 'Please enter first name.', 'woocommerce' ) );
						}
		            }
		            if(isset($key) && ( $key == 'billing_last_name' && empty($field) ) ) {
	            		if( !empty($_POST[$key]) ) {
							continue;
						} else {
							$validation_errors->add( $key.'_error', __( 'Please enter last name.', 'woocommerce' ) );
						}
		            }
		            if(isset($key) && ( $key == 'billing_address_1' && empty($field) ) ) {
	            		if( !empty($_POST[$key]) ) {
							continue;
						} else {
							$validation_errors->add( $key.'_error', __( 'Please enter address.', 'woocommerce' ) );
						}
		            }
		            if(isset($key) && ( $key == 'billing_city' && empty($field) ) ) {
	            		if( !empty($_POST[$key]) ) {
							continue;
						} else {
							$validation_errors->add( $key.'_error', __( 'Please enter city.', 'woocommerce' ) );
						}
		            }
		            if(isset($key) && ( $key == 'billing_state' && empty($field) ) ) {
		                if(count( WC()->countries->get_states($_POST['billing_country']) ) > 0) {
			                if( !empty($_POST[$key]) ) {
								continue;
							} else {
								$validation_errors->add( $key.'_error', __( 'Please enter state.', 'woocommerce' ) );
							}
						}
		            }
		            if(isset($key) && ( $key == 'billing_postcode' && empty($field) ) ) {
		            	if( !empty($_POST[$key]) ) {
							continue;
						} else {
							$validation_errors->add( $key.'_error', __( 'Please enter a postcode.', 'woocommerce' ) );
						}
		            }
		            		            
		            if(isset($key) && ( $key == 'billing_phone' && empty($field) ) ) {
		            	if( !empty($_POST[$key]) ) {
							continue;
						} else {
							$validation_errors->add( $key.'_error', __( 'Please enter phone number.', 'woocommerce' ) );
						}
		            }

		            if(isset($key) && ( $key == 'billing_email' && empty($field) ) ) {
		            	if( !empty($_POST[$key]) ) {
							continue;
						} else {
							$validation_errors->add( $key.'_error', __( 'Please enter Email Address.', 'woocommerce' ) );
						}
		            }

		        }
			}
	   	}
    }
    add_action( 'woocommerce_register_post', 'wooc_validate_extra_register_fields', 10, 3);

    /**
     * WooCommerce Checkout & Account Field Field Update 
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.5
     */	
    function wcfc_save_additional_account_details( $user_ID ){
    	if ( !current_user_can( 'edit_user', $user_id ) ) {
    		return false;
    	}
    	if ( is_array( get_option( 'wc_fields_account' ) ) ) {
    	    foreach ( get_option( 'wc_fields_account' ) as $key => $value ) {
    	        if ( $key == 'account_username' || $key == 'account_password' ) {
    	            continue;
    	        }
    	        if ( get_user_meta( $user_id, $key, true ) ) {
    	            update_user_meta( $user_id, $key, $_POST[$key] );
    	        }
    	    }
    	}
    }
    add_action( 'save_account_details', 'wcfc_save_additional_account_details' );
    
    /**
     * WooCommerce Checkout & Account Field Prepare CHeckout Field  
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    function wcfe_prepare_checkout_fields_lite( $fields, $original_fields, $sname = "" ) {
        if ( is_array( $fields ) && !empty($fields) ) {
            foreach ( $fields as $name => $field ) {
                if ( isset( $field['enabled'] ) && $field['enabled'] == false ) {
                    unset( $fields[$name] );
                } else {
                    $new_field = false;
                    if ( $original_fields && isset( $original_fields[$name] ) ) {
                        $new_field = $original_fields[$name];
                        $new_field['label'] = ( isset( $field['label'] ) ? $field['label'] : '' );
                        $new_field['placeholder'] = ( isset( $field['placeholder'] ) ? $field['placeholder'] : '' );
                        $new_field['class'] = ( isset( $field['class'] ) && is_array( $field['class'] ) ? $field['class'] : array() );
						
                       
                        $new_field['validate'] = ( isset( $field['validate'] ) && is_array( $field['validate'] ) ? $field['validate'] : array() );
                        $new_field['required'] = ( isset( $field['required'] ) ? $field['required'] : 0 );
                        $new_field['clear'] = ( isset( $field['clear'] ) ? $field['clear'] : 0 );
					} else {
                        $new_field = $field;
                    }

                    if(isset($new_field['rules_action_ajax']) && !empty($new_field['rules_action_ajax']) && isset($new_field['rules_ajax']) && !empty($new_field['rules_ajax'])){

						$new_field['class'][] = 'wcfe-conditional-field';
						//$new_field['required'] = false;
					}
					
					if(isset($new_field['rules_action']) && !empty($new_field['rules_action']) && isset($new_field['rules']) && !empty($new_field['rules'])){
						$new_field['class'][] = 'wcfe-conditional-field';
						//$new_field['required'] = false;
					}

                    if ( isset( $new_field['type'] ) && $new_field['type'] === 'file' && isset( $_SESSION[$name] ) ) {
                        //$new_field['required'] = false;
                    }
                    if ( isset( $new_field['type'] ) && $new_field['type'] === 'hidden' ) {
                        //$new_field['required'] = false;
                    }
                    if ( isset( $new_field['type'] ) && $new_field['type'] === 'heading' ) {
                        //$new_field['required'] = false;
                    }
                    if ( isset( $new_field['type'] ) && $new_field['type'] === 'select' ) {
                        if ( apply_filters( 'wcfe_enable_select2_for_select_fields', true ) ) {
                            $new_field['input_class'][] = 'wcfe-enhanced-select';
                            //$new_field['required'] = false;
                        }
                    }
                   	$new_field['order'] = isset($field['order']) && is_numeric($field['order']) ? $field['order'] : 0;

					if(isset($new_field['order']) && is_numeric($new_field['order'])){
						$priority = ($new_field['order']+1)*10;
						$new_field['priority'] = $priority;
					}
                    if ( isset( $new_field['label'] ) ) {
                        $new_field['label'] = __( $new_field['label'], 'wcfe' );
                    }
                    if ( isset( $new_field['placeholder'] ) ) {
                        $new_field['placeholder'] = __( $new_field['placeholder'], 'wcfe' );
                    }
					
					$new_field['input_class'][] = 'wcfe-input-field';
					
                    $fields[$name] = $new_field;
				
                }
            }
            return $fields;
        } else {
            return $original_fields;
        }
    }
	
	/**
	 * WooCommerce Checkout & Account Field Checkout FIeld Validation  
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_check_field_validations( $A ) {
		$fields = get_option( 'wc_fields_billing' );
		if ( is_array( $fields ) && !empty($fields) ) {
            foreach ( $fields as $name => $field ) {
                if ( isset( $field['enabled'] ) && $field['enabled'] == false ) {
                    unset( $fields[$name] );
                } else {
                    $new_field = $field;
                    $label = '';
					
					if(isset($new_field['label'])){
						$label = $new_field['label'];
					} else{
						$label = $name;
					}
                }
            }
        }
	}
    add_action( 'woocommerce_after_checkout_validation', 'wcfe_check_field_validations' );
	add_action( 'woocommerce_after_checkout_form', 'wcfe_check_field_validations' );
		
	/**
	 * WooCommerce Checkout & Account Field Check Condition Front End  
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_check_conditions_frontend( $condition,$value ) {
		switch ($condition['operator']) {
			case 'empty':
				if( empty($value) ){
					return true;
				} else { 
					return false; 
				}
			case 'not_empty':
				if( !empty($value) ){
					return true;
				} else { 
					return false; 
				}
			case 'value_eq':
				if( $value == $condition['value'] ){
					return true;
				} else { 
					return false; 
				}
			case 'value_ne':
				if( $value != $condition['value'] ){
					return true;
				} else { 
					return false; 
				}
			case 'value_gt':
				if( $value > $condition['value'] ){
					return true;
				} else { 
					return false; 
				}
			case 'value_le':
				if( $value < $condition['value'] ){
					return true;
				} else { 
					return false; 
				}
			
			case 'date_eq':
				if( strtotime($value) == strtotime($condition['value']) ){
					return true;
				} else { 
					return false; 
				}
			case 'date_ne':
				if( strtotime($value) != strtotime($condition['value']) ){
					return true;
				} else { 
					return false; 
				}
			case 'date_gt':
				if( strtotime($value) > strtotime($condition['value']) ) {
					return true;
				} else { 
					return false; 
				}
			case 'date_lt':
				if( strtotime($value) < strtotime($condition['value']) ){
					return true;
				} else { 
					return false; 
				}
			case 'day_eq':
				if( strtolower($value) == strtolower($condition['value']) ){
					return true;
				} else { 
					return false; 
				}
			case 'day_ne':
				if( strtolower($value) != strtolower($condition['value']) ){
					return true;
				} else { 
					return false; 
				}
			case 'checked':
				if( $value ){
					return true;
				} else { 
					return false; 
				}
				
			case 'not_checked':
				if( empty($value) ){
					return true;
				} else { 
					return false; 
				}
		}
	}
	
	/**
	 * WooCommerce Checkout & Account Field Check Cart Total
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_check_cart_total($operator,$conditionalval) {
		if(!WC()->cart->is_empty()):
			$total = WC()->cart->total;
			$subtotal = WC()->cart->subtotal;
			
			if($operator == 'cart_subtotal_gt' && $subtotal > $conditionalval)
				return true;

			if($operator == 'cart_subtotal_eq' && $subtotal == $conditionalval)
				return true;
			
			if($operator == 'cart_subtotal_lt' && $subtotal < $conditionalval)
				return true;
			
			if($operator == 'cart_total_gt' && $total > $conditionalval)
				return true;
			
			if($operator == 'cart_total_eq' && $total == $conditionalval)
				return true;
			
			if($operator == 'cart_total_lt' && $total < $conditionalval)
				return true;
		endif;
	}
	
	/**
	 * WooCommerce Checkout & Account Field Check Product In Cart
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_check_product_in_cart($product_id) {
		if(!WC()->cart->is_empty()):
	        foreach(WC()->cart->get_cart() as $cart_item ):
	            $items_id = $cart_item['product_id'];
				$product_id;
	            // for a unique product ID (integer or string value)
	            if($product_id == $items_id)
	              return true;
	        endforeach;
	    endif;
	}
	
	/**
	 * WooCommerce Checkout & Account Field Check User Role Is Matched
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_check_user_role_is_match($condition,$user_role){
		if( is_user_logged_in() ) {
			$user = wp_get_current_user();
			$role = ( array ) $user->roles;
			
			if($condition == 'user_role_eq') {
				if($user_role[0] == $role[0]){
					return true;
				}
				else {
					return false;
				}
			} elseif ( $condition == 'user_role_ne' ) {
				if($user_role[0] != $role[0]){
					return true;
				} else {
					return false;
				}
			}
		}
	}

	/**
	 * WooCommerce Checkout & Account Field Check User Role Is Matched
	 *
	 * @package WooCommerce Checkout & Account Field Editor
	 * @since 1.0
	 */	
	function wcfe_check_category_in_cart( $term_id ) {
		if(!WC()->cart->is_empty()):
	        foreach(WC()->cart->get_cart() as $cart_item ):
			   	$terms = get_the_terms( $cart_item['product_id'], 'product_cat' );
				foreach ($terms as $term) {
				   // for a unique product ID (integer or string value)
					if($term->term_id == $term_id)
						return true;
				}
	        endforeach;
   		endif;
	}

    /**
     * WooCommerce Checkout & Account Field Add Custom field under my account billing  
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.5.2
     */
    add_filter( 'woocommerce_billing_fields', 'wcfe_woocommerce_billing_fields' );
    function wcfe_woocommerce_billing_fields( $fields ) {
        if( ! is_account_page() ) return $fields;

        $fields_set = array();
        if ( is_array( get_option( 'wc_fields_billing' ) ) ) {
            foreach ( get_option( 'wc_fields_billing' ) as $name => $field ) {
                if ( $name == 'account_username' || $name == 'account_password' ) {
                    continue;
                }
                if ( isset( $field['type']['type'] ) && $field['type'] === 'hidden' ) {
                    $field['required'] = 0;
                }
                if ( isset( $field['type'] ) && $field['type'] === 'heading' ) {
                    $field['required'] = 0;
                }
                if ( isset( $field['type'] ) && $field['type'] === 'file' && isset( $_SESSION[$name] ) ) {
                    $field['required'] = 0;
                }
                $fields_set[$name] = $field;
            }
            foreach ( $fields_set as $name => $options ) {
                if( isset( $options['show_in_my_account'] ) &&  $options['show_in_my_account'] == false ) {
                    unset( $fields_set[$name] );
                }
                if($options['type'] == 'file') {
                    unset( $fields_set[$name] );
                }
            }
            return $fields_set;
        } else {
            return $fields;
        }
    }

    /**
     * WooCommerce Checkout & Account Field Add Custom field under my-account Shipping  
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.5.2
     */ 
    add_filter( 'woocommerce_shipping_fields', 'wcfe_woocommerce_shipping_fields' );
    function wcfe_woocommerce_shipping_fields( $fields ) {
        if( ! is_account_page() ) return $fields;

        $fields_set = array();

        if ( is_array( get_option( 'wc_fields_shipping' ) ) ) {
            foreach ( get_option( 'wc_fields_shipping' ) as $name => $field ) {
                if ( $name == 'account_username' || $name == 'account_password' ) {
                    continue;
                }
                if ( isset( $field['type']['type'] ) && $field['type'] === 'hidden' ) {
                    $field['required'] = 0;
                }
                if ( isset( $field['type'] ) && $field['type'] === 'heading' ) {
                    $field['required'] = 0;
                }
                if ( isset( $field['type'] ) && $field['type'] === 'file' && isset( $_SESSION[$name] ) ) {
                    $field['required'] = 0;
                }
                $fields_set[$name] = $field;
            }
            foreach ( $fields_set as $name => $options ) {
                if( isset( $options['show_in_my_account'] ) &&  $options['show_in_my_account'] == false ) {
                    unset( $fields_set[$name] );
                }
                if($options['type'] == 'file') {
                    unset( $fields_set[$name] );
                }
            }
            return $fields_set;
        } else {
           return $fields; 
        }
    }
	
    /**
     * WooCommerce Checkout & Account Field Check Custom Field Email
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    function wcfe_display_custom_fields_in_emails_lite( $order, $sent_to_admin, $plain_text ) {
        if ( wcfe_woocommerce_version_check() ) {
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }
        $fields_html = '';

        $fields = array_merge( WC_Checkout_Field_Editor::get_fields( 'billing' ), WC_Checkout_Field_Editor::get_fields( 'shipping' ), WC_Checkout_Field_Editor::get_fields( 'additional' ) );
        
        
        if ( $plain_text === false ) {
			if( get_option('wcfe_account_label') !== null && get_option('wcfe_account_label') != ""){
				$custom_heading = get_option('wcfe_account_label');
				$fields_html .= '<h2>' . esc_html( $custom_heading ) . '</h2>';
			} 
            $fields_html .= '<ul>';
        }
        
        // Loop through all custom fields to see if it should be added
        foreach ( $fields as $key => $options ) {
            if ( isset( $options['show_in_email'] ) && $options['show_in_email'] ) {
                $value = '';
                if ( wcfe_woo_version_check() ) {
                    if ( $options['type'] == 'select' || $options['type'] == 'checkboxgroup' || $options['type'] == 'timepicker' || $options['type'] == 'multiselect' ) {
                        $value = get_post_meta( $order_id, $key, true );
                        if ( is_array( $value ) ) {
                            $value = implode( ",", $value );
                        } else {
                            $value = get_post_meta( $order_id, $key, true );
                        }
                    } else {
                        $value = get_post_meta( $order_id, $key, true );
                    }
                } else {
                    if ( $options['type'] == 'select' || $options['type'] == 'checkboxgroup' || $options['type'] == 'timepicker' || $options['type'] == 'multiselect' ) {
                        $value = get_post_meta( $order_id, $key, true );
                        if ( is_array( $value ) ) {
                            $value = implode( ",", $value );
                        } else {
                            $value = get_post_meta( $order_id, $key, true );
                        }
                    } else {
                        $value = get_post_meta( $order_id, $key, true );
                    }
                }
                
                if ( !empty($value) ) {
                    $label = ( isset( $options['label'] ) && $options['label'] ? $options['label'] : $key );
                    $label = esc_attr( $label );
                    $fields_html .= '<li>' .$label . ':' . $value . '</li>';
                }
            }
        }
        if ( $plain_text === false ) {
            $fields_html .= '</ul>';
        }
        echo $fields_html;
    }
    
    add_action( 'woocommerce_email_order_meta', 'wcfe_display_custom_fields_in_emails_lite',10, 3 );
    add_filter( 'wc_admin_custom_order_field_options', 'wcfe_display_custom_fields_in_emails_lite', 10, 2 );

    /**
     * WooCommerce Checkout & Account Field Display Customer Details
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    function wcfe_order_details_after_customer_details_lite( $order ) {
        if ( wcfe_woocommerce_version_check() ) {
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }
        $fields = array();
        if ( !wc_ship_to_billing_address_only() && $order->needs_shipping_address() ) {
            $fields = array_merge( WC_Checkout_Field_Editor::get_fields( 'billing' ), WC_Checkout_Field_Editor::get_fields( 'shipping' ), WC_Checkout_Field_Editor::get_fields( 'additional' ) );
        } else {
            $fields = array_merge( WC_Checkout_Field_Editor::get_fields( 'billing' ), WC_Checkout_Field_Editor::get_fields( 'additional' ) );
        }
        
        if ( is_array( $fields ) && !empty($fields) ) {
            $fields_html = '';
            // Loop through all custom fields to see if it should be added
            foreach ( $fields as $name => $options ) {

                $enabled = ( isset( $options['enabled'] ) && $options['enabled'] == false ? false : true );
                $is_custom_field = ( isset( $options['custom'] ) && $options['custom'] == true ? true : false );
                
                if ( isset( $options['show_in_order'] ) && $options['show_in_order'] && $enabled && $is_custom_field ) {

                    if ( $options['type'] == 'select' || $options['type'] == 'checkboxgroup' || $options['type'] == 'timepicker' || $options['type'] == 'multiselect' ) {

                        $value = get_post_meta( $order_id, $name, true );

                        if ( is_array( $value ) ) {
                            $value = implode( ",", $value );
                        } else {
                            $value = get_post_meta( $order_id, $name, true );
                        }
                    } else {
                        $value = get_post_meta( $order_id, $name, true );
                    }

                    if ( !empty($value) ) {
                        $label = ( isset( $options['label'] ) && !empty($options['label']) ? __( $options['label'], 'wcfe' ) : $name );
                        
                        if ( apply_filters( 'wcfe_thankyou_customer_details_table_view', true ) ) {
                            if ( isset( $options['type'] ) && $options['type'] == 'file' ) {
                                $fields_html .= '<tr><th>' . esc_attr( $label ) . ':</th><td><a href="' . esc_url( $value ) . '" download>Download File</a></td></tr>';
                            } else {
                                $fields_html .= '<tr><th>' . esc_attr( $label ) . ':</th><td>' . wptexturize( $value ) . '</td></tr>';
                            }
                        } else {
                            if ( isset( $options['type'] ) && $options['type'] == 'file' ) {
                                $fields_html .= '<br/><dt>' . esc_attr( $label ) . ':</dt><dd><a href="' . esc_url( $value ) . '" download>Download File</a></dd>';
                            } else {
                                $fields_html .= '<br/><dt>' . esc_attr( $label ) . ':</dt><dd>' . wptexturize( $value ) . '</dd>';
                            }
                        }
                    }
                }
            }
            
            if ( $fields_html ) {
                do_action( 'wcfe_order_details_before_custom_fields_table', $order ); ?>
				<h2 class="woocommerce-column__title">
					<?php 
					if( get_option('wcfe_account_label') !== null && get_option('wcfe_account_label') != ""){
						$custom_heading = get_option('wcfe_account_label');
					} else{
						$custom_heading = esc_html__('Custom Checkout Fields','wcfe');
					}
	                echo $custom_heading; ?> 
            	</h2>
				<table class="woocommerce-table woocommerce-table--custom-fields shop_table custom-fields">
					<?php echo $fields_html ;?>
				</table>
				<?php 
                do_action( 'wcfe_order_details_after_custom_fields_table', $order );
            }
        }
    }
    
    add_action( 'woocommerce_order_details_after_order_table','wcfe_order_details_after_customer_details_lite', 20, 1 );

    /**
     * WooCommerce Checkout & Account Field Register Meta Box
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    function wcfe_register_order_meta_boxes() {
		if( get_option('wcfe_account_label') !== null && get_option('wcfe_account_label') != "") {
			$custom_heading = get_option('wcfe_account_label');
		} else{
			$custom_heading = esc_html__('Custom Checkout Fields','wcfe');
		}
        add_meta_box( 'wcfe-custom-order-box', $custom_heading, 'wcfe_orderbox_display_callback', 'shop_order', 'normal', 'high' );
    }
    add_action( 'add_meta_boxes', 'wcfe_register_order_meta_boxes' );

    /**
     * WooCommerce Checkout & Account Field Meta Box Display Callback
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    function wcfe_orderbox_display_callback( $post ) {
        // Display code/markup goes here. Don't forget to include nonces!
        $order = new WC_Order( $post->ID );
        if ( wcfe_woocommerce_version_check() ) {
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }
       
        $fields = array();
        if ( !wc_ship_to_billing_address_only() && $order->needs_shipping_address() ) {
            if ( get_option( 'wcfe_account_sync_fields' ) && get_option( 'wcfe_account_sync_fields' ) == "on" ) {
                $fields = array_merge(
                    WC_Checkout_Field_Editor::get_fields( 'account' ),
                    WC_Checkout_Field_Editor::get_fields( 'billing' ),
                    WC_Checkout_Field_Editor::get_fields( 'shipping' ),
                    WC_Checkout_Field_Editor::get_fields( 'additional' )
                );
            } else {
                $fields = array_merge( WC_Checkout_Field_Editor::get_fields( 'billing' ), WC_Checkout_Field_Editor::get_fields( 'shipping' ), WC_Checkout_Field_Editor::get_fields( 'additional' ) );
            }
        } else {
            if ( get_option( 'wcfe_account_sync_fields' ) && get_option( 'wcfe_account_sync_fields' ) == "on" ) {
                $fields = array_merge( WC_Checkout_Field_Editor::get_fields( 'account' ), WC_Checkout_Field_Editor::get_fields( 'billing' ), WC_Checkout_Field_Editor::get_fields( 'additional' ) );
            } else {
                $fields = array_merge( WC_Checkout_Field_Editor::get_fields( 'billing' ), WC_Checkout_Field_Editor::get_fields( 'additional' ) );
            }
        }

        if ( is_array( $fields ) && !empty($fields) ) {
            $fields_html = '';
            // Loop through all custom fields to see if it should be added
            foreach ( $fields as $name => $options ) {

                $enabled = ( isset( $options['enabled'] ) && $options['enabled'] == false ? false : true );
                $is_custom_field = ( isset( $options['custom'] ) && $options['custom'] == true ? true : false );
                if ( isset( $options['show_in_order'] ) && $options['show_in_order'] && $enabled && $is_custom_field ) {
                    if ( $options['type'] == 'select' || $options['type'] == 'checkboxgroup' || $options['type'] == 'timepicker' || $options['type'] == 'multiselect' ) {
                        $value = get_post_meta( $order_id, $name, true );
                        if ( is_array( $value ) ) {
                            $value = implode( ",", $value );
                        } else {
                            $value = get_post_meta( $order_id, $name, true );
                        }
                    } else {
                        $value = get_post_meta( $order_id, $name, true );
                    }
                    
                    if ( !empty($value) ) {
                        $label = ( isset( $options['label'] ) && !empty($options['label']) ? __( $options['label'], 'wcfe' ) : $name );
                        
                        if ( is_account_page() ) {
                            if ( isset( $options['type'] ) && $options['type'] == 'file' ) {
                                $fields_html .= '<tr><th style="text-align:left; width:50%">' . esc_attr( $label ) . ':</th><td style="text-align:left; width:50%"><a href="' . $value . '" download>Download File</a></td></tr>';
                            } else {
                                $fields_html .= '<tr><th style="text-align:left; width:50%">' . esc_attr( $label ) . ':</th><td style="text-align:left; width:50%">' . wptexturize( $value ) . '</td></tr>';
                            }
                        } else {
                            
                            if ( isset( $options['type'] ) && $options['type'] == 'file' ) {
                                $fields_html .= '<tr><th style="text-align:left; width:50%">' . esc_attr( $label ) . ':</th><td style="text-align:left; width:50%"><a href="' . $value . '" download>Download File</a></td></tr>';
                            } else {
                                $fields_html .= '<tr><th style="text-align:left; width:50%">' . esc_attr( $label ) . ':</th><td style="text-align:left; width:50%">' . wptexturize( $value ) . '</td></tr>';
                            }
                        }
                    }
                }
            }
            
            if ( $fields_html ) { ?>
				<table width="100%" class="woocommerce-table woocommerce-table--custom-fields shop_table custom-fields">
					<?php echo $fields_html; ?>
				</table>
			<?php  }
        }
    }

    /**
     * WooCommerce Checkout & Account Field Save Meta Box Content
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    function wcfe_save_order_meta_box( $post_id ) {
        // Save logic goes here. Don't forget to include nonce checks!
    }
    
    add_action( 'save_post', 'wcfe_save_order_meta_box' );

    /**
     * WooCommerce Checkout & Account Field Woo Commerce Version Check
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */	
    function wcfe_woo_version_check( $version = '3.4.3' ) {
        if ( function_exists( 'is_woocommerce_active' ) && is_woocommerce_active() ) {
            global  $woocommerce ;
            if ( version_compare( $woocommerce->version, $version, ">=" ) ) {
                return true;
            }
        }
        return false;
    }
    
}