<?php

class BSK_GFBLCV_Submitting_GravityForms {
    
    var $_OBJ_common = false;

	public function __construct( $args ) {
        
        $this->_OBJ_common = $args['common_class'];
        
        if ( BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_is_form_plugin_supported('GF') ) {
            add_filter( 'gform_validation', array($this, 'bsk_gfblcv_front_form_validation'), 10, 1 );
        }
		
	}
	
	function bsk_gfblcv_front_form_validation( $validation_result ){

        $form = $validation_result['form'];
        
        //check a reCAPTCHA field exist and failed to validate, if so then return.
        foreach( $form['fields'] as $field_obj ){
            if( $field_obj->type != 'captcha' ){
                continue;
            }
            if( isset($field_obj->failed_validation) && $field_obj->failed_validation ){
                return $validation_result;
            }
        }
        
        //form settings
        $bsk_gfblcv_form_settings = rgar( $form, 'bsk_gfblcv_form_settings' );

        $enable = true;
        $action_when_hit = array( 'BLOCK' );
        $validation_message_array = array();
        $validation_message_array['black'] = 'The value for field "[FIELD_LABEL]" is invalid!';
        $validation_message_array['white'] = 'The value for field "[FIELD_LABEL]" is invalid!';
        $validation_message_array['email'] = 'The value for field "[FIELD_LABEL]" is invalid!';
        $validation_message_array['ip'] = 'Your IP address [VISITOR_IP] is forbidden!';
        
        if( $bsk_gfblcv_form_settings && is_array( $bsk_gfblcv_form_settings ) && count( $bsk_gfblcv_form_settings ) > 0 ){
            $enable = $bsk_gfblcv_form_settings['enable'];
            $action_when_hit = $bsk_gfblcv_form_settings['actions'];
            $validation_message_array['black'] = $bsk_gfblcv_form_settings['blacklist_message'];
            $validation_message_array['white'] = $bsk_gfblcv_form_settings['whitelist_message'];
            $validation_message_array['email'] = $bsk_gfblcv_form_settings['emaillist_message'];
            $validation_message_array['ip'] = $bsk_gfblcv_form_settings['iplist_message'];
        }else{
            //compatible with old savd data format
            if( isset( $form['block_or_skip_notification'] ) && $form['block_or_skip_notification'] == 'SKIP' ) {
                $action_when_hit = array( 'SKIP' ); 
            }
            
            if( isset( $form['bsk_gfblcv_validation_message'] ) && $form['bsk_gfblcv_validation_message'] ) {
                $validation_message_array['black'] = $form['bsk_gfblcv_validation_message'];
            }
            
            if( isset( $form['whitelist_validation_message'] ) && $form['whitelist_validation_message'] ) {
                $validation_message_array['white'] = $form['whitelist_validation_message'];
            }
            
            if( isset( $form['emaillist_validation_message'] ) && $form['emaillist_validation_message'] ) {
                $validation_message_array['email'] = $form['emaillist_validation_message'];
            }
            
            if( isset( $form['iplist_validation_message'] ) && $form['iplist_validation_message'] ) {
                $validation_message_array['ip'] = $form['iplist_validation_message'];
            }
        }
        
        if( !$enable ){
            return $validation_result;
        }
        
        if( !in_array( 'BLOCK', $action_when_hit ) ){
            return $validation_result;
        }
        
        $return_validation_result = $this->bsk_gfblcv_front_form_validation_mapping( 
                                                                                    $validation_result,
                                                                                    $validation_message_array
                                                                                   );
        
		return $return_validation_result;
	}
	
	
	function bsk_gfblcv_front_form_validation_mapping( $validation_result, $validation_message_array ){
		global $wpdb;

		$form = $validation_result['form'];
		//validation
        $fields_hit_item_array = array(); //only for blocked
        $form_data_array = array();
		$current_page = rgpost( 'gform_source_page_number_' . $form['id'] ) ? rgpost( 'gform_source_page_number_' . $form['id'] ) : 1;
		foreach( $form['fields'] as $field ){
			if ( $current_page != $field->pageNumber ) {
				continue;
			}
            
			if( $field->is_field_hidden ){
                continue;
            }
            
			$field_obj_array = json_decode( json_encode($field), true );
			if( $field->type == 'name' || 
                $field->type == 'address' || 
                $field->type == 'checkbox' ||
                $field->type == 'time' ){
                //checkbox will come to here
                //let empty value can be checked for supporting checkbox_all rule
				foreach($field['inputs'] as $gravity_form_field_input) {
					if( isset($gravity_form_field_input['isHidden']) && $gravity_form_field_input['isHidden'] ){
						continue;
					}
                    
					$field_id_str = $gravity_form_field_input['id'];
                    
					$field_value = rgpost( 'input_'.str_replace( '.', '_', $field_id_str) );
                    $field_label = $field['label'].'.'.$gravity_form_field_input['label'];
                    
                    $property_appendix = '_'.$field_id_str;
                    
                    $this->bsk_gfblcv_check_field_value_againsit_list_item(
                                                                      $property_appendix,
                                                                      $fields_hit_item_array,
                                                                      $field, 
                                                                      $validation_result,
                                                                      $field_id_str, 
                                                                      $field_value, 
                                                                      $field_label, 
                                                                      $field_obj_array, 
                                                                      $validation_message_array
                                                                    );
				}//end of foreach
			}else{
				$field_id_str = $field['id'];
				$field_value = rgpost( 'input_'.$field_id_str );
                $field_label = $field['label'];
                
                $property_appendix = '';

                $this->bsk_gfblcv_check_field_value_againsit_list_item(
                                                                      $property_appendix,
                                                                      $fields_hit_item_array,
                                                                      $field,
                                                                      $validation_result,
                                                                      $field_id_str, 
                                                                      $field_value, 
                                                                      $field_label, 
                                                                      $field_obj_array, 
                                                                      $validation_message_array
                                                                    );
                
			}//end of multiple inputs filed or single field
		}
        
        $validation_result['form'] = $form;
		return $validation_result;
	}
    
    function bsk_gfblcv_check_field_value_againsit_list_item( $property_appendix,
                                                              &$fields_hit_item_array,
                                                              &$field,
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
        }
        
        if( trim( $validation_message ) == '' ){
            $validation_message = $validation_message_array['black'];
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
                                                                                    GFFormsModel::get_ip() 
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
                    $validation_result['is_valid'] = false;
                    $field['failed_validation'] = true;
                    $field['validation_message'] = $validation_message;
                    
                    $invalid_validation = true;

                    //update hits counter
                    $this->_OBJ_common->bsk_gfblcv_update_item_hits( $item_id_str );
                }
            break;
        }
        
        return $invalid_validation;
    }
	
}