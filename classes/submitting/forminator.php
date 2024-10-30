<?php

class BSK_GFBLCV_Submitting_Forminator {
    
    var $_OBJ_common = false;

	public function __construct( $args ) {

        require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/forminator/forminator.php' );
        
        $this->_OBJ_common = $args['common_class'];
        
        if ( BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_is_form_plugin_supported('FRMT') ) {
            add_filter( 'forminator_custom_form_submit_errors', array( $this, 'bsk_gfblcv_front_form_validation' ), 10, 3 );

            //apply_filters( 'forminator_render_form_markup', $html, $form_fields, $form_type, $form_settings, $form_design, $render_id );
            add_filter( 'forminator_render_form_markup', array( $this, 'bsk_gfblcv_front_form_add_uid_hidden_field_fun' ), 10, 6 );
        }
		
        //add_shortcode( 'footag', array( $this, 'wpdocs_footag_func' ) );
	}

    function bsk_gfblcv_front_form_add_uid_hidden_field_fun( $html, $form_id, $post_id, $nonce ) {

        //get uid
        $match = array();
        if ( preg_match( '/data-uid="(.*?)"/', $html, $match ) != 1 ) {
            return $html;
        }

        $uid = $match[1];
        $hidden_field = '<input type="hidden" name="bsk_gfblcv_forminator_form_ufid" value="' . $uid . '" />';
        $html = str_replace( '</form>', $hidden_field . '</form>', $html );
        
        return $html;
    }

    /* function wpdocs_footag_func() {
        $field_data_array = get_option( '111111-99_33' );
        $submit_errors = get_option( '111111-99_34' );
        $form_id = 99;

        $return = $this->bsk_gfblcv_front_form_validation( $submit_errors, $form_id, $field_data_array );
        print_r( $return );
        exit;
    } */
	
	function bsk_gfblcv_front_form_validation( $submit_errors, $form_id, $field_data_array ) {
        
        /* update_option( '111111-' . $form_id . '_' . __LINE__, $field_data_array );
        update_option( '111111-' . $form_id . '_' . __LINE__, $submit_errors );
        return $submit_errors; */
        /* 
        $fields_data = $this->frmt_process_form_fields_values( $field_data_array );
        update_option( '111111-' . __LINE__, $fields_data );
        return $submit_errors; 
        */
        /* $error = 'Invalid field';
        $submit_errors = array();
        $submit_errors[][ 'forminator-form-99__field--name-1' ] = $error;
        return $submit_errors; */

        //form settings
        $bsk_gfblcv_form_settings = get_option( BSK_GFBLCV_Dashboard_Forminator::$_bsk_gfblcv_frmt_form_settings_option_name_prefix . $form_id, false );

        $enable = true;
        $action_when_hit = array( 'BLOCK' );
        $save_blocked_data = 'NO';
        $notify_administrators = 'NO';
        $notify_send_to = '';
        $validation_message_array = array();
        $validation_message_array['black'] = 'The value for field "[FIELD_LABEL]" is invalid!';
        $validation_message_array['white'] = 'The value for field "[FIELD_LABEL]" is invalid!';
        $validation_message_array['email'] = 'The value for field "[FIELD_LABEL]" is invalid!';
        $validation_message_array['ip'] = 'Your IP address [VISITOR_IP] is forbidden!';
        $validation_message_array['invit'] = 'The value for field "[FIELD_LABEL]" is invalid!';
        if ( $bsk_gfblcv_form_settings && is_array( $bsk_gfblcv_form_settings ) && count( $bsk_gfblcv_form_settings ) > 0 ) {
            $enable = $bsk_gfblcv_form_settings['enable'];
            $action_when_hit = $bsk_gfblcv_form_settings['actions'];
            $save_blocked_data = $bsk_gfblcv_form_settings['save_blocked_data'];
            $notify_administrators = $bsk_gfblcv_form_settings['notify_administrators'];
            $notify_send_to = $bsk_gfblcv_form_settings['notify_send_to'];
            $validation_message_array['black'] = $bsk_gfblcv_form_settings['blacklist_message'];
            $validation_message_array['white'] = $bsk_gfblcv_form_settings['whitelist_message'];
            $validation_message_array['email'] = $bsk_gfblcv_form_settings['emaillist_message'];
            $validation_message_array['ip'] = $bsk_gfblcv_form_settings['iplist_message'];
            if ( isset($bsk_gfblcv_form_settings['invitlist_message']) && 
                $bsk_gfblcv_form_settings['invitlist_message'] ) {
                $validation_message_array['invit'] = $bsk_gfblcv_form_settings['invitlist_message'];
            }
        }
        
        if ( ! $enable ){
            return $submit_errors;
        }
        
        if ( ! in_array( 'BLOCK', $action_when_hit ) ) {
            return $submit_errors;
        }

        //form fields
        $form_fields = BSK_GFBLCV_Dashboard_Common::forminator_get_form_fields( $form_id );
        if ( ! $form_fields || ! is_array( $form_fields ) || count( $form_fields ) < 1 ) {
            return $submit_errors;
        }
        
        //field mappings
        $saved_field_settings = get_option( BSK_GFBLCV_Dashboard_Forminator::$_bsk_gfblcv_frmt_field_settings_option_name_prefix . $form_id, false );
        if ( ! $saved_field_settings || ! is_array( $saved_field_settings ) || count( $saved_field_settings ) < 1 ) {
            return $submit_errors;
        }

        $fields_value_array = $this->frmt_process_form_fields_values( $field_data_array );
        if ( ! $fields_value_array || ! is_array( $fields_value_array ) || count( $fields_value_array ) < 1 ) {
            return $submit_errors;
        }

        //validation
        $fields_hit_item_array = array(); //only for blocked
        $form_data_array = array();
        foreach ( $form_fields as $field_id => $field ) {
            $field_value = '';
            $field_label = '';
            $submit_error_id = '';
            switch ( $field['type'] ) {
                case 'name.prefix':
                    if ( isset ( $fields_value_array[$field['parent_id']]['value']['prefix'] ) ) {
                        $field_value = $fields_value_array[$field['parent_id']]['value']['prefix'];
                    }
                    if ( isset ( $fields_value_array[$field['parent_id']]['prefix_label'] ) ) {
                        $field_label = $fields_value_array[$field['parent_id']]['prefix_label'];
                    }
                    $submit_error_id = 'forminator-form-' . $form_id . '__field--' . $field['parent_id'];
                break;
                case 'name.fname':
                    if ( isset ( $fields_value_array[$field['parent_id']]['value']['first-name'] ) ) {
                        $field_value = $fields_value_array[$field['parent_id']]['value']['first-name'];
                    }
                    if ( isset ( $fields_value_array[$field['parent_id']]['fname_label'] ) ) {
                        $field_label = $fields_value_array[$field['parent_id']]['fname_label'];
                    }
                    $submit_error_id = 'forminator-field-first-' . $field['parent_id'];
                break;
                case 'name.mname':
                    if ( isset ( $fields_value_array[$field['parent_id']]['value']['middle-name'] ) ) {
                        $field_value = $fields_value_array[$field['parent_id']]['value']['middle-name'];
                    }
                    if ( isset ( $fields_value_array[$field['parent_id']]['mname_label'] ) ) {
                        $field_label = $fields_value_array[$field['parent_id']]['mname_label'];
                    }
                    $submit_error_id = 'forminator-field-middle-' . $field['parent_id'];
                break;
                case 'name.lname':
                    if ( isset ( $fields_value_array[$field['parent_id']]['value']['last-name'] ) ) {
                        $field_value = $fields_value_array[$field['parent_id']]['value']['last-name'];
                    }
                    if ( isset ( $fields_value_array[$field['parent_id']]['lname_label'] ) ) {
                        $field_label = $fields_value_array[$field['parent_id']]['lname_label'];
                    }
                    $submit_error_id = 'forminator-field-last-' . $field['parent_id'];
                break;
                case 'address.street_address':
                    if ( isset ( $fields_value_array[$field['parent_id']]['value']['street_address'] ) ) {
                        $field_value = $fields_value_array[$field['parent_id']]['value']['street_address'];
                    }
                    if ( isset ( $fields_value_array[$field['parent_id']]['street_address_label'] ) ) {
                        $field_label = $fields_value_array[$field['parent_id']]['street_address_label'];
                    }
                    $submit_error_id = 'forminator-field-street_address-' . $field['parent_id'];
                break;
                case 'address.address_line':
                    if ( isset ( $fields_value_array[$field['parent_id']]['value']['address_line'] ) ) {
                        $field_value = $fields_value_array[$field['parent_id']]['value']['address_line'];
                    }
                    if ( isset ( $fields_value_array[$field['parent_id']]['address_line_label'] ) ) {
                        $field_label = $fields_value_array[$field['parent_id']]['address_line_label'];
                    }
                    $submit_error_id = 'forminator-field-address_line-' . $field['parent_id'];
                break;
                case 'address.address_city':
                    if ( isset ( $fields_value_array[$field['parent_id']]['value']['city'] ) ) {
                        $field_value = $fields_value_array[$field['parent_id']]['value']['city'];
                    }
                    if ( isset ( $fields_value_array[$field['parent_id']]['address_city_label'] ) ) {
                        $field_label = $fields_value_array[$field['parent_id']]['address_city_label'];
                    }
                    $submit_error_id = 'forminator-field-city' . $field['parent_id'];
                break;
                case 'address.address_state':
                    if ( isset ( $fields_value_array[$field['parent_id']]['value']['state'] ) ) {
                        $field_value = $fields_value_array[$field['parent_id']]['value']['state'];
                    }
                    if ( isset ( $fields_value_array[$field['parent_id']]['address_state_label'] ) ) {
                        $field_label = $fields_value_array[$field['parent_id']]['address_state_label'];
                    }
                    $submit_error_id = 'forminator-field-state-' . $field['parent_id'];
                break;
                case 'address.address_zip':
                    if ( isset ( $fields_value_array[$field['parent_id']]['value']['zip'] ) ) {
                        $field_value = $fields_value_array[$field['parent_id']]['value']['zip'];
                    }
                    if ( isset ( $fields_value_array[$field['parent_id']]['address_zip_label'] ) ) {
                        $field_label = $fields_value_array[$field['parent_id']]['address_zip_label'];
                    }
                    $submit_error_id = 'forminator-field-zip-' . $field['parent_id'];
                break;
                case 'address.address_country':
                    if ( isset ( $fields_value_array[$field['parent_id']]['value']['country'] ) ) {
                        $field_value = $fields_value_array[$field['parent_id']]['value']['country'];
                    }
                    if ( isset ( $fields_value_array[$field['parent_id']]['address_country_label'] ) ) {
                        $field_label = $fields_value_array[$field['parent_id']]['address_country_label'];
                    }
                    $submit_error_id = 'forminator-form-' . $form_id . '__field--' . $field['parent_id'];
                break;
                default:
                    if ( isset( $fields_value_array[$field_id]['value'] ) ) {
                        $field_value = $fields_value_array[$field_id]['value'];
                    }
                    if ( isset( $fields_value_array[$field_id]['field_label'] ) ) {
                        $field_label = $fields_value_array[$field_id]['field_label'];
                    }
                    $submit_error_id = 'forminator-field-' . $field_id;
                break;
            }

            //validate field value against blacklist
            if( ! isset( $saved_field_settings[$field_id] ) || ! is_array( $saved_field_settings[$field_id] ) || count( $saved_field_settings[$field_id] ) < 1 || 
                $saved_field_settings[$field_id]['list_type'] == '' || $saved_field_settings[$field_id]['save_id_error'] || $saved_field_settings[$field_id]['save_comparison_error'] ){
                continue;
            }
            
            $field_return_message = '';
            $checked_results = $this->bsk_gfblcv_check_field_value_againsit_list_item( $field_id,
                                                                                       $saved_field_settings[$field_id], 
                                                                                       $field_label, 
                                                                                       $field_value, 
                                                                                       $validation_message_array,
                                                                                       $fields_hit_item_array,
                                                                                       $field_return_message );
            if( $checked_results ){
                if ( ! is_array( $submit_errors ) ) {
                    $submit_errors = array();
                }

                if ( $submit_error_id ) {
                    //add form uid to field
                    $submit_error_id .= '_' . $_POST['bsk_gfblcv_forminator_form_ufid']; //since Forminator 1.18.1
                    $submit_errors[][$submit_error_id] = $field_return_message;
                }
            }
        }
        
        return $submit_errors;
	}
    
    function bsk_gfblcv_check_field_value_againsit_list_item( $field_id,
                                                              $field_mappings,
                                                              $field_label,
                                                              $field_value, 
                                                              $form_default_validation_message_array,
                                                              &$fields_hit_item_array,
                                                              &$validation_return_message ){
        
        global $wpdb;
                
        $list_type = $field_mappings['list_type'];
        $list_id_to_check = $field_mappings['list_id'];
        $comparison_to_check = $field_mappings['list_comparison'];
        $validation_message = '';

        $invalid_validation = false;
        if( $field_value == "" || $list_type == '' ){
            return $invalid_validation;
        }

        if( ! $list_id_to_check || ! $comparison_to_check ){
            return $invalid_validation;
        }

        //check if the list still active, as some case the list deleted but it still save in form settings
        if ( ! $this->_OBJ_common->bsk_gfblcv_front_check_list_status( $list_id_to_check, $list_type ) ) {
            return $invalid_validation;
        }

        switch ( $list_type ) {
            case 'BLACK_LIST':
                $validation_message = $form_default_validation_message_array['black'];
            break;
            case 'WHITE_LIST':
                $validation_message = $form_default_validation_message_array['white'];
            break;
            case 'EMAIL_LIST':
                $validation_message = $form_default_validation_message_array['email'];
            break;
            case 'IP_LIST':
                $validation_message = $form_default_validation_message_array['ip'];
                $field_value = BSK_GFBLCV_Dashboard_Common::get_ip();
            break;
            case 'INVIT_LIST':
                $validation_message = $form_default_validation_message_array['invit'];
            break;
        }

        if ( isset( $field_mappings['validation_message'] ) && strlen( trim( $field_mappings['validation_message'] ) ) > 0 ) {
            $validation_message = $field_mappings['validation_message'];
        }

        $validation_message = $this->_OBJ_common->bsk_gfblcv_render_validation_message( 
                                                                                    $validation_message, 
                                                                                    $field_label, 
                                                                                    $field_value, 
                                                                                    BSK_GFBLCV_Dashboard_Common::get_ip() 
                                                                                );
        
        $checked_results = $this->_OBJ_common->bsk_gfblcv_front_check_field_value_match_list(
                                                        $list_type,
                                                        $list_id_to_check, 
                                                        $comparison_to_check, 
                                                        $field_value
                                                    );
        $item_id_str = '';
        if( $checked_results ){
            if( is_array($checked_results) && count($checked_results) > 0 ){
                $item_id_str = implode( ',', $checked_results );
            }else{
                $item_id_str = $checked_results;
            }
        }
        
        switch( $list_type ){
            case 'BLACK_LIST':
                if( $checked_results ){

                    $validation_return_message = $validation_message;
                    $invalid_validation = true;

                }
            break;
            case 'WHITE_LIST':
                if( ! $checked_results ){

                    $validation_return_message = $validation_message;
                    $invalid_validation = true;

                }else{
                    //update hits counter
                    $this->_OBJ_common->bsk_gfblcv_update_item_hits( $item_id_str );
                }
            break;
            case 'IP_LIST':
                if( $comparison_to_check == 'ALLOW' ){
                    if( !$checked_results ){

                        $validation_return_message = $validation_message;
                        $invalid_validation = true;


                        break;
                    }else if( $checked_results > 1 || ( is_array($checked_results) && count($checked_results) ) ) {
                        //for the case by country, it only return true or false, so won't come to here
                        //update hits counter
                        $this->_OBJ_common->bsk_gfblcv_update_item_hits( $item_id_str );
                    }
                }else if( $comparison_to_check == 'BLOCK' ){
                    if( $checked_results ){

                        $validation_return_message = $validation_message;
                        $invalid_validation = true;

                        $blocked_item_id_to_save = array( -2 ); //default -2 is for IP list COUNTRY check way
                        if( $checked_results > 1 || ( is_array($checked_results) && count($checked_results) ) ){
                            //for the case by country, it only return true or false, so won't come to here
                            //update hits counter
                            $this->_OBJ_common->bsk_gfblcv_update_item_hits( $item_id_str );

                            $blocked_item_id_to_save = explode( ',', $item_id_str );
                        }

                    }
                }
            break;
            case 'EMAIL_LIST':
            case 'INVIT_LIST':
                if( $comparison_to_check == 'ALLOW' ){
                    if( !$checked_results ){

                        $validation_return_message = $validation_message;
                        $invalid_validation = true;

                        break;
                    }else{
                        //update hits counter, invit codes list will be updated to entry id at the last
                        if( $list_type != 'INVIT_LIST' ){
                            $this->_OBJ_common->bsk_gfblcv_update_item_hits( $item_id_str );
                        }
                    }
                }else if( $comparison_to_check == 'BLOCK' ){
                    if( $checked_results ){

                        $validation_return_message = $validation_message;
                        $invalid_validation = true;
                        
                        //update hits counter
                        $this->_OBJ_common->bsk_gfblcv_update_item_hits( $item_id_str );

                    }
                }
            break;
        }
        
        return $invalid_validation;
    }

    function frmt_process_form_fields_values( $field_data_array ) {

        if ( ! $field_data_array || ! is_array( $field_data_array ) || count( $field_data_array ) < 1 ) {
            return;
        }

        $fields_data = array();
        foreach ( $field_data_array as $field_data ) {
            $id = $field_data['name'];
            $value = $field_data['value'];
            $field_type = $field_data['field_type'];

            $fields_data[$id] = array( 
                                        'type' => $field_type, 
                                        'value' => $value, 
                                        'field_label' => isset( $field_data['field_array']['field_label'] ) ? $field_data['field_array']['field_label'] : '',
                                    );
            switch ( $field_type ) {
                case 'name':
                    if ( isset( $field_data['field_array']['multiple_name'] ) && 
                         $field_data['field_array']['multiple_name'] ) {
                        
                        $name_child_fields = array( 'prefix', 'fname', 'mname', 'lname', );
                        foreach ( $name_child_fields as $child_field_name ) {
                            $fields_data[$id][$child_field_name.'_label'] = $field_data['field_array'][$child_field_name.'_label'];
                        }
                    }
                break;
                case 'address':
                    $address_child_fields = array( 'street_address', 'address_line', 'address_city', 'address_state', 'address_zip', 'address_country', );
                    foreach ( $address_child_fields as $child_field_name ) {
                        $fields_data[$id][$child_field_name.'_label'] = $field_data['field_array'][$child_field_name.'_label'];
                    }
                break;
                case 'hidden':
                    $field_label = $fields_data[$id]['field_label'] == '' ? 'Hidden Field' : $fields_data[$id]['field_label'];
                    $fields_data[$id]['field_label'] = $field_label;
                break;
            }
        }

        return $fields_data;
    }
    
}