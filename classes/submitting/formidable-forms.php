<?php

class BSK_GFBLCV_Submitting_FormidableForms {
    
    var $_OBJ_common = false;
    
    var $_entry_id_processed = false;

	public function __construct( $args ) {
        
        $this->_OBJ_common = $args['common_class'];
        
        if ( BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_is_form_plugin_supported('FF') ) {
            //https://formidableforms.com/knowledgebase/frm_validate_field_entry/
            add_filter( 'frm_validate_entry', array( $this, 'bsk_gfblcv_ff_validaiton_field' ), 10, 3 );
        }
        
	}
	
    function bsk_gfblcv_ff_validaiton_field( $errors, $values, $exclude ){
        extract( $exclude );
        
        //check spam
        if ( is_array( $errors ) && isset( $errors['spam'] ) ) {
             return $errors;
        }

        $form_id = $values['form_id'];
        $form = $form_id;
        FrmForm::maybe_get_form( $form );
        if ( ! is_object( $form ) ) {
			return $errors;
		}
        //form settings
        $bsk_gfblcv_form_settings = maybe_unserialize( get_option( BSK_GFBLCV_Dashboard_Formidable_Forms::$_bsk_gfblcv_ff_form_settings_option_name_prefix . $form_id) );

        $enable = true;
        $action_when_hit = array( 'BLOCK' );
        $validation_message_array = array();
        $validation_message_array['black'] = 'The value for field "[FIELD_LABEL]" is invalid!';
        $validation_message_array['white'] = 'The value for field "[FIELD_LABEL]" is invalid!';
        $validation_message_array['email'] = 'The value for field "[FIELD_LABEL]" is invalid!';
        $validation_message_array['ip'] = 'Your IP address [VISITOR_IP] is forbidden!';
        
        if ( $bsk_gfblcv_form_settings && is_array( $bsk_gfblcv_form_settings ) && count( $bsk_gfblcv_form_settings ) > 0 ) {
            $enable = $bsk_gfblcv_form_settings['enable'];
            $action_when_hit = $bsk_gfblcv_form_settings['actions'];
            $validation_message_array['black'] = $bsk_gfblcv_form_settings['blacklist_message'];
            $validation_message_array['white'] = $bsk_gfblcv_form_settings['whitelist_message'];
            $validation_message_array['email'] = $bsk_gfblcv_form_settings['emaillist_message'];
            $validation_message_array['ip'] = $bsk_gfblcv_form_settings['iplist_message'];
        }
        
        if ( ! $enable ){

            return $errors;
        }

        if ( ! in_array( 'BLOCK', $action_when_hit ) ){

            return $errors;
        }
        
        $where = apply_filters( 'frm_posted_field_ids', array( 'fi.form_id' => $values['form_id'] ) );
		// Don't get subfields
		$where['fr.parent_form_id'] = array( null, 0 );
		// Don't get excluded fields (like file upload fields in the ajax validation)
		if ( ! empty( $exclude ) ) {
			$where['fi.type not'] = $exclude['exclude'];
		}
        
		$posted_fields = FrmField::getAll( $where, 'field_order' );
        
        //check reCAPTCHA
        $reCAPTCHA_field_id = 0;
        foreach ( $posted_fields as $posted_field ) {
            if ( $posted_field->type == 'captcha' ) {
                $reCAPTCHA_field_id = $posted_field->id;
                break;
            }
        }
        if ( $reCAPTCHA_field_id > 0 && isset ( $errors['field'.$reCAPTCHA_field_id] ) ) {
            return $errors;
        }
        
        $args = array( 'exclude' => $exclude );
        
        //validation
        $fields_hit_item_array = array(); //only for blocked
        $form_data_array = array();

		foreach ( $posted_fields as $field ) {
			if ( in_array( $field->type, array( 'html', 'user_id', 'captcha', 'hidden' ) ) ) {
                continue;
            }
            
            //get field value
            $field_value = self::get_field_value( $field, $values, $args );
            
            $field_id_str = $field->id;
            $field_label = $field->name;

            $property_appendix = '';
            $this->bsk_gfblcv_check_field_value_againsit_list_item(
                                                                  $property_appendix,
                                                                  $fields_hit_item_array,
                                                                  $field,
                                                                  $errors,
                                                                  $field_id_str, 
                                                                  $field_value, 
                                                                  $field_label, 
                                                                  $field->field_options, 
                                                                  $validation_message_array
                                                                );
            
            
		}
        
        return $errors;
    }
    
    function bsk_gfblcv_check_field_value_againsit_list_item( $property_appendix,
                                                              &$fields_hit_item_array,
                                                              $field,
                                                              &$validation_result,
                                                              $field_id_str, 
                                                              $field_value, 
                                                              $field_label, 
                                                              $field_obj_array, 
                                                              $validation_message_array ){
        
        global $wpdb;
        
        $list_id_to_check = '';
        $comparison_to_check = '';
        $validation_message = '';
        $list_type = '';
        
        if( isset($field_obj_array['bsk_gfbl_apply_blacklist_Property'.$property_appendix]) &&
            $field_obj_array['bsk_gfbl_apply_blacklist_Property'.$property_appendix] &&
            isset($field_obj_array['bsk_gfbl_apply_blacklist_Comparison'.$property_appendix]) && 
            $field_obj_array['bsk_gfbl_apply_blacklist_Comparison'.$property_appendix] ){

            $list_id_to_check = $field_obj_array['bsk_gfbl_apply_blacklist_Property'.$property_appendix];
            $comparison_to_check = $field_obj_array['bsk_gfbl_apply_blacklist_Comparison'.$property_appendix];
            $list_type = 'BLACK_LIST';

            if( isset( $field_obj_array['bsk_gfblcv_validation_message'.$property_appendix] ) ){
                $validation_message = $field_obj_array['bsk_gfblcv_validation_message'.$property_appendix];
            }
            if( trim( $validation_message ) == '' ){
                $validation_message = $validation_message_array['black'];
            }
        }

        $invalid_validation = false;
        if( $field_value == "" || $list_type == '' ){
            return $invalid_validation;
        }
        
        if( !$list_id_to_check || !$comparison_to_check ){
            return $invalid_validation;
        }

        //check if the list still active, as some case the list deleted but it still save in form settings
        if ( ! $this->_OBJ_common->bsk_gfblcv_front_check_list_status( $list_id_to_check, $list_type ) ) {
            return $invalid_validation;
        }

        $validation_message = $this->_OBJ_common->bsk_gfblcv_render_validation_message( 
                                                                                    $validation_message, 
                                                                                    $field_label, 
                                                                                    $field_value, 
                                                                                    FrmAppHelper::get_ip_address() 
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
                    $validation_result['field'. $field_id_str] = $validation_message;
                    
                    $invalid_validation = true;

                    //update hits counter
                    $this->_OBJ_common->bsk_gfblcv_update_item_hits( $item_id_str );
                }
            break;
        }
        
        return $invalid_validation;
    }
    
    public static function get_field_value( $posted_field, $values, $args = array() ) {
		$defaults = array(
			'id'              => $posted_field->id,
			'parent_field_id' => '', // the id of the repeat or embed form
			'key_pointer'     => '', // the pointer in the posted array
			'exclude'         => array(), // exclude these field types from validation
		);
		$args     = wp_parse_args( $args, $defaults );

		if ( empty( $args['parent_field_id'] ) ) {
			$value = isset( $values['item_meta'][ $args['id'] ] ) ? $values['item_meta'][ $args['id'] ] : '';
		} else {
			// value is from a nested form
			$value = $values;
		}

		// Check for values in "Other" fields
		FrmEntriesHelper::maybe_set_other_validation( $posted_field, $value, $args );

		self::maybe_clear_value_for_default_blank_setting( $posted_field, $value );

		$should_trim = is_array( $value ) && count( $value ) == 1 && isset( $value[0] ) && $posted_field->type !== 'checkbox';
		if ( $should_trim ) {
			$value = reset( $value );
		}

		if ( ! is_array( $value ) ) {
			$value = trim( $value );
		}

		return $value;
	}
    
    private static function maybe_clear_value_for_default_blank_setting( $field, &$value ) {
		$position = FrmField::get_option( $field, 'label' );
		if ( ! $position ) {
			$position = FrmStylesController::get_style_val( 'position', $field->form_id );
		}

		if ( $position === 'inside' && FrmFieldsHelper::is_placeholder_field_type( $field->type ) && $value === $field->name ) {
			$value = '';
		}
	}

}
