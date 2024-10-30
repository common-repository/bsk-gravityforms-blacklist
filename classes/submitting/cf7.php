<?php

class BSK_GFBLCV_Submitting_CF7 {
    
    var $_OBJ_common = false;

	public function __construct( $args ) {
        
        $this->_OBJ_common = $args['common_class'];
        
        if ( BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_is_form_plugin_supported('CF7') ) {
            add_filter( 'wpcf7_validate', array( $this, 'bsk_gfblcv_cf7_validate_item' ), 10, 2 );
        }
		
	}
	
	function bsk_gfblcv_cf7_validate_item( $validation_result, $form_fields ){
        
        if( ! $form_fields || ! is_array( $form_fields ) || count( $form_fields ) < 1 ){
            return $validation_result;
        }

        $wpcf7_instance = WPCF7_Submission::get_instance();
        $contact_form = $wpcf7_instance->get_contact_form();
        $form_id = $contact_form->id();
        $form_title = $contact_form->title();
        $bsk_gfblcv_form_settings = $this->cf7_blacklist_get_form_settings( $form_id, $contact_form );

        $enable = false;
        $action_when_hit = array( 'BLOCK' );
        $mails_to_skip = array();
        $save_blocked_data = 'NO';
        $notify_administrators = 'NO';
        $notify_send_to = '';
        $delete_entry = 'NO';
        $default = 'The value for field "[FIELD_LABEL]" is invalid!';
        $ip_default = 'Your IP address [VISITOR_IP] is forbidden!';
        $blacklist_message = $default;
        $whitelist_message = $default;
        $emaillist_message = $default;
        $iplist_message = $ip_default;
        $invitlist_message = $default;
        if( $bsk_gfblcv_form_settings && is_array( $bsk_gfblcv_form_settings ) && count( $bsk_gfblcv_form_settings ) > 0 ){
            $enable = $bsk_gfblcv_form_settings['enable'];
            $action_when_hit = $bsk_gfblcv_form_settings['actions'];
            if ( isset( $bsk_gfblcv_form_settings['mails_to_skip'] ) ) {
                $mails_to_skip = $bsk_gfblcv_form_settings['mails_to_skip'];
            }
            $blacklist_message = $bsk_gfblcv_form_settings['blacklist_message'];
            $whitelist_message = $bsk_gfblcv_form_settings['whitelist_message'];
            if ( isset($bsk_gfblcv_form_settings['emaillist_message']) && 
                $bsk_gfblcv_form_settings['emaillist_message'] ) {
                $emaillist_message = $bsk_gfblcv_form_settings['emaillist_message'];
            }
            if ( isset($bsk_gfblcv_form_settings['iplist_message']) && 
                $bsk_gfblcv_form_settings['iplist_message'] ) {
                $iplist_message = $bsk_gfblcv_form_settings['iplist_message'];
            }
            if ( isset($bsk_gfblcv_form_settings['invitlist_message']) && 
                $bsk_gfblcv_form_settings['invitlist_message'] ) {
                $invitlist_message = $bsk_gfblcv_form_settings['invitlist_message'];
            }
        }
        $form_default_validation_messages = array( 
                                                    'black' => $blacklist_message,
                                                    'white' => $whitelist_message,
                                                    'email' => $emaillist_message,
                                                    'ip' => $iplist_message,
                                                    'invit' => $invitlist_message,
                                                 );
        if ( !$enable ) {
            return $validation_result;
        }
        
        if ( !in_array( 'BLOCK', $action_when_hit ) ) {
            return $validation_result;
        }
        
        //validation
        //get field mapping
        $form_mappings = $this->cf7_blacklist_get_form_mappings( $form_id );
        foreach ( $form_fields as $field ) {
            if( $field->name == "" ){
                continue;
            }

            $field_value = $wpcf7_instance->get_posted_data( $field->name );
            if( isset( $validation_result->invalid_fields[$field->name] ) ){
                continue;
            }
            
            //validate field value against blacklist
            if( ! isset( $form_mappings[$field->name] ) || ! is_array( $form_mappings[$field->name] ) || count( $form_mappings[$field->name] ) < 1 || 
                $form_mappings[$field->name]['list_type'] == '' || $form_mappings[$field->name]['save_id_error'] || $form_mappings[$field->name]['save_comparison_error'] ){
                continue;
            }
            
            $field_return_message = '';
            $checked_results = $this->bsk_gfblcv_check_field_value_againsit_list_item( $form_mappings[$field->name], 
                                                                                       $field->name, 
                                                                                       $field_value, 
                                                                                       $form_default_validation_messages,
                                                                                       $fields_hit_item_array,
                                                                                       $field_return_message );
            if( $checked_results ){
                $validation_result->invalidate( $field, $field_return_message );
            }
        }

        return $validation_result;
    }
    
    function cf7_blacklist_get_form_settings( $form_id, $contact_form ){

        $bsk_gfblcv_form_settings = get_post_meta( $form_id, BSK_GFBLCV_Dashboard::$_bsk_gfblcv_cf7_form_settings_opt, true );
		if ( ! $bsk_gfblcv_form_settings || ! is_array( $bsk_gfblcv_form_settings ) || count( $bsk_gfblcv_form_settings ) < 1 ) {
			//no saved, to check if convert from CF7 Blacklist plugin
			BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_cf7_convert_cf7blacklist_data( $contact_form );
			//get again
			$bsk_gfblcv_form_settings = get_post_meta( $form_id, BSK_GFBLCV_Dashboard::$_bsk_gfblcv_cf7_form_settings_opt, true );
		}
        
        return $bsk_gfblcv_form_settings;
    }

    function cf7_blacklist_get_form_mappings( $form_id ){
        $form_mappings = get_post_meta( $form_id, BSK_GFBLCV_Dashboard::$_bsk_gfblcv_cf7_form_mappings_opt, true );
        
        return $form_mappings;
    }
    
    function bsk_gfblcv_check_field_value_againsit_list_item( $field_mappings,
                                                              $field_name,
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
        }

        $validation_message = $this->_OBJ_common->bsk_gfblcv_render_validation_message( 
                                                                                    $validation_message, 
                                                                                    $field_name, 
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
        }
        
        return $invalid_validation;
    }
}