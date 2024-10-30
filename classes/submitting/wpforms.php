<?php

class BSK_GFBLCV_Submitting_WPForms {
    
    var $_OBJ_common = false;

	public function __construct( $args ) {
        
        $this->_OBJ_common = $args['common_class'];
        
        if ( BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_is_form_plugin_supported('WPF') ) {
            add_action( 'wpforms_process_validate_text', array( $this, 'bsk_gfblcv_front_form_validation' ), 9999, 3 );
            add_action( 'wpforms_process_validate_name', array( $this, 'bsk_gfblcv_front_form_validation' ), 9999, 3 );
            add_action( 'wpforms_process_validate_email', array( $this, 'bsk_gfblcv_front_form_validation' ), 9999, 3 );
            add_action( 'wpforms_process_validate_textarea', array( $this, 'bsk_gfblcv_front_form_validation' ), 9999, 3 );
            add_action( 'wpforms_process_validate_address', array( $this, 'bsk_gfblcv_front_form_validation' ), 9999, 3 );
            add_action( 'wpforms_process_validate_phone', array( $this, 'bsk_gfblcv_front_form_validation' ), 9999, 3 );
            add_action( 'wpforms_process_validate_url', array( $this, 'bsk_gfblcv_front_form_validation' ), 9999, 3 );
        }
		
	}
    
	function bsk_gfblcv_front_form_validation( $field_id, $field_submit, $form_data ){
        
        if( !isset( $form_data['settings']['bsk_gfblcv_form_settings_enable'] ) ||
            $form_data['settings']['bsk_gfblcv_form_settings_enable'] != 'ENABLE' ) {
            
            return;
        }

        //form settings
        $bsk_gfblcv_form_settings = $form_data['settings'];
      
        $enable = true;
        $action_when_hit = array();
        $validation_message_array = array();
        $validation_message_array['black'] = 'The value for field "[FIELD_LABEL]" is invalid!';
        $validation_message_array['white'] = 'The value for field "[FIELD_LABEL]" is invalid!';
        $validation_message_array['email'] = 'The value for field "[FIELD_LABEL]" is invalid!';
        $validation_message_array['ip'] = 'Your IP address [VISITOR_IP] is forbidden!';
        
        if( $bsk_gfblcv_form_settings && is_array( $bsk_gfblcv_form_settings ) && count( $bsk_gfblcv_form_settings ) > 0 ){
            $enable = $bsk_gfblcv_form_settings['bsk_gfblcv_form_settings_enable'];
            if( isset( $bsk_gfblcv_form_settings['bsk_gfblcv_form_settings_actions_block'] ) &&
                $bsk_gfblcv_form_settings['bsk_gfblcv_form_settings_actions_block'] ){
                if( !in_array( 'BLOCK', $action_when_hit ) ){
                    $action_when_hit[] = 'BLOCK';
                }
            }
            
            $validation_message_array['black'] = $bsk_gfblcv_form_settings['bsk_gfblcv_form_settings_blacklist_error_message'];
            $validation_message_array['white'] = $bsk_gfblcv_form_settings['bsk_gfblcv_form_settings_whitelist_error_message'];
            $validation_message_array['email'] = $bsk_gfblcv_form_settings['bsk_gfblcv_form_settings_emaillist_error_message'];
            $validation_message_array['ip'] = $bsk_gfblcv_form_settings['bsk_gfblcv_form_settings_iplist_error_message'];
        }
        
        if( !in_array( 'BLOCK', $action_when_hit ) ){
            return;
        }
        
        $field = $form_data['fields'][$field_id];
        $field_value = $field_submit;
        $return_validation_result = $this->bsk_gfblcv_front_form_validation_mapping( 
                                                                                    $form_data,
                                                                                    $field,
                                                                                    $field_value,
                                                                                    $validation_message_array,
                                                                                    true
                                                                                   );
        
		  return $return_validation_result;
	}
	
	//return ture means have kewords hit, error occur
	function bsk_gfblcv_front_form_validation_mapping( $form_data, $field, $field_value, $validation_message_array, $for_block ){
		  global $wpdb;
        
      $field_type = $field['type'];
      if( $field_type == 'hidden' ){
          return false;
      }

      $field_id_str = $field['id'];
      $field_label = $field['label'];
      $fields_hit_item_array = array(); //only for blocked

      $has_failed_validation = false;
      $property_appendix = '';
      $validation_result = array();
      
      if( $field['type'] == 'name' || $field['type'] == 'address' ){
          if( is_array( $field_value ) && 
              ( ( $field['type'] == 'name' && $field['format'] != 'simple' ) || $field['type'] == 'address' ) ){
              foreach( $field_value as $sub_field_id => $sub_field_value ){
                  $field_id_str = $field['id'].'.'.$sub_field_id;

                  if( !isset($field['bsk_gfblcv_'.$sub_field_id.'_list_type']) || $field['bsk_gfblcv_'.$sub_field_id.'_list_type'] == '' ||
                      !isset($field['bsk_gfblcv_'.$sub_field_id.'_list_id']) || $field['bsk_gfblcv_'.$sub_field_id.'_list_id'] < 1 ||
                      ( !isset($field['bsk_gfblcv_'.$sub_field_id.'_comparison']) && !isset($field['bsk_gfblcv_'.$sub_field_id.'_action']) ) ||
                      ($field['bsk_gfblcv_'.$sub_field_id.'_comparison'] == '' && $field['bsk_gfblcv_'.$sub_field_id.'_action'] == '' ) ){
                      continue;
                  }

                  $property_appendix = '_'.$sub_field_id;
                  if( $field['bsk_gfblcv_'.$sub_field_id.'_list_type'] == 'BLACK_LIST' ){
                      $field['bsk_gfblcv_blacklist_chk'.$property_appendix] = 'YES';
                      $field['bsk_gfblcv_blacklist_comparison'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_comparison'];
                      $field['bsk_gfblcv_blacklist_list'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_list_id'];
                  }else if( $field['bsk_gfblcv_'.$sub_field_id.'_list_type'] == 'WHITE_LIST' ){
                      $field['bsk_gfblcv_whitelist_chk'.$property_appendix] = 'YES';
                      $field['bsk_gfblcv_whitelist_comparison'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_comparison'];
                      $field['bsk_gfblcv_whitelist_list'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_list_id'];
                  }else if( $field['bsk_gfblcv_'.$sub_field_id.'_list_type'] == 'EMAIL_LIST' ){
                      $field['bsk_gfblcv_emaillist_chk'.$property_appendix] = 'YES';
                      $field['bsk_gfblcv_emaillist_comparison'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_action'];
                      $field['bsk_gfblcv_emaillist_list'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_list_id'];
                  }else if( $field['bsk_gfblcv_'.$sub_field_id.'_list_type'] == 'IP_LIST' ){
                      $field['bsk_gfblcv_iplist_chk'.$property_appendix] = 'YES';
                      $field['bsk_gfblcv_iplist_comparison'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_action'];
                      $field['bsk_gfblcv_iplist_list'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_list_id'];
                  }
                  $field['bsk_gfblcv_validation_message'.$property_appendix] = '';
                  if( isset($field['bsk_gfblcv_'.$sub_field_id.'_validation_message']) ){
                      $field['bsk_gfblcv_validation_message'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_validation_message'];
                  }

                  $validation_return = $this->bsk_gfblcv_check_field_value_againsit_list_item(
                                                                $property_appendix,
                                                                $fields_hit_item_array,
                                                                $field,
                                                                $validation_result,
                                                                $field_id_str, 
                                                                $sub_field_value, 
                                                                $field_label, 
                                                                $field, 
                                                                $validation_message_array
                                                              );
                  if( $validation_return ){
                      $has_failed_validation = true;
                      if( $for_block ){
                          wpforms()->process->errors[ $form_data['id'] ][ $field['id'] ][$sub_field_id] = $validation_result['validation_message'];
                      }
                  }
              }
          }else{
              $sub_field_id = 'first';
              if( !isset($field['bsk_gfblcv_'.$sub_field_id.'_list_type']) || $field['bsk_gfblcv_'.$sub_field_id.'_list_type'] == '' ||
                    !isset($field['bsk_gfblcv_'.$sub_field_id.'_list_id']) || $field['bsk_gfblcv_'.$sub_field_id.'_list_id'] < 1 ||
                    ( !isset($field['bsk_gfblcv_'.$sub_field_id.'_comparison']) && !isset($field['bsk_gfblcv_'.$sub_field_id.'_action']) ) ||
                    ($field['bsk_gfblcv_'.$sub_field_id.'_comparison'] == '' && $field['bsk_gfblcv_'.$sub_field_id.'_action'] == '' ) ){
                    return false;
                }

                $property_appendix = '_'.$sub_field_id;
                if( $field['bsk_gfblcv_'.$sub_field_id.'_list_type'] == 'BLACK_LIST' ){
                    $field['bsk_gfblcv_blacklist_chk'.$property_appendix] = 'YES';
                    $field['bsk_gfblcv_blacklist_comparison'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_comparison'];
                    $field['bsk_gfblcv_blacklist_list'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_list_id'];
                }else if( $field['bsk_gfblcv_'.$sub_field_id.'_list_type'] == 'WHITE_LIST' ){
                    $field['bsk_gfblcv_whitelist_chk'.$property_appendix] = 'YES';
                    $field['bsk_gfblcv_whitelist_comparison'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_comparison'];
                    $field['bsk_gfblcv_whitelist_list'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_list_id'];
                }else if( $field['bsk_gfblcv_'.$sub_field_id.'_list_type'] == 'EMAIL_LIST' ){
                    $field['bsk_gfblcv_emaillist_chk'.$property_appendix] = 'YES';
                    $field['bsk_gfblcv_emaillist_comparison'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_action'];
                    $field['bsk_gfblcv_emaillist_list'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_list_id'];
                }else if( $field['bsk_gfblcv_'.$sub_field_id.'_list_type'] == 'IP_LIST' ){
                    $field['bsk_gfblcv_iplist_chk'.$property_appendix] = 'YES';
                    $field['bsk_gfblcv_iplist_comparison'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_action'];
                    $field['bsk_gfblcv_iplist_list'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_list_id'];
                }
                $field['bsk_gfblcv_validation_message'.$property_appendix] = '';
                if( isset($field['bsk_gfblcv_'.$sub_field_id.'_validation_message']) ){
                    $field['bsk_gfblcv_validation_message'.$property_appendix] = $field['bsk_gfblcv_'.$sub_field_id.'_validation_message'];
                }
                $sub_field_value = $field_value;
                $validation_return = $this->bsk_gfblcv_check_field_value_againsit_list_item(
                                                              $property_appendix,
                                                              $fields_hit_item_array,
                                                              $field,
                                                              $validation_result,
                                                              $field_id_str, 
                                                              $sub_field_value, 
                                                              $field_label, 
                                                              $field, 
                                                              $validation_message_array
                                                            );
                if( $validation_return ){
                    $has_failed_validation = true;
                    if( $for_block ){
                        wpforms()->process->errors[ $form_data['id'] ][ $field['id'] ] = $validation_result['validation_message'];
                    }
                }
          }
      }else{
          $validation_return = $this->bsk_gfblcv_check_field_value_againsit_list_item(
                                                            $property_appendix,
                                                            $fields_hit_item_array,
                                                            $field,
                                                            $validation_result,
                                                            $field_id_str, 
                                                            $field_value, 
                                                            $field_label, 
                                                            $field, 
                                                            $validation_message_array
                                                          );

          if( $validation_return ){
              $has_failed_validation = true;
              if( $for_block ){
                  wpforms()->process->errors[ $form_data['id'] ][ $field['id'] ] = $validation_result['validation_message'];
              }
          }
      }
      
      return $has_failed_validation;
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
        
        if( isset($field_obj_array['bsk_gfblcv_blacklist_chk'.$property_appendix]) &&
            $field_obj_array['bsk_gfblcv_blacklist_chk'.$property_appendix] &&
            $field_obj_array['bsk_gfblcv_blacklist_chk'.$property_appendix] == 'YES' &&
            isset($field_obj_array['bsk_gfblcv_blacklist_comparison'.$property_appendix]) && 
            $field_obj_array['bsk_gfblcv_blacklist_comparison'.$property_appendix] ){

            $list_id_to_check = $field_obj_array['bsk_gfblcv_blacklist_list'.$property_appendix];
            $comparison_to_check = $field_obj_array['bsk_gfblcv_blacklist_comparison'.$property_appendix];
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
                                                                                    wpforms_get_ip() 
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
                    
                    $validation_result['validation_message'] = $validation_message;
                    $invalid_validation = true;

                    //update hits counter
                    $this->_OBJ_common->bsk_gfblcv_update_item_hits( $item_id_str );

                }
            break;
            case 'WHITE_LIST':
                if( ! $checked_results ){
                    
                    $validation_result['validation_message'] = $validation_message;
                    $invalid_validation = true;

                }else{
                    //update hits counter
                    $this->_OBJ_common->bsk_gfblcv_update_item_hits( $item_id_str );
                }
            break;
            case 'IP_LIST':
                if( $comparison_to_check == 'ALLOW' ){
                    if( !$checked_results ){
                        
                        $validation_result['validation_message'] = $validation_message;
                        $invalid_validation = true;

                        break;
                    }else if( $checked_results > 1 || ( is_array($checked_results) && count($checked_results) ) ) {
                        //for the case by country, it only return true or false, so won't come to here
                        //update hits counter
                        $this->_OBJ_common->bsk_gfblcv_update_item_hits( $item_id_str );
                    }
                }else if( $comparison_to_check == 'BLOCK' ){
                    if( $checked_results ){
                        
                        $validation_result['validation_message'] = $validation_message;
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
                if( $comparison_to_check == 'ALLOW' ){
                    if( !$checked_results ){
                        
                        $validation_result['validation_message'] = $validation_message;
                        $invalid_validation = true;

                        break;
                    }else{
                        //update hits counter
                        $this->_OBJ_common->bsk_gfblcv_update_item_hits( $item_id_str );
                    }
                }else if( $comparison_to_check == 'BLOCK' ){
                    if( $checked_results ){
                        
                        $validation_result['validation_message'] = $validation_message;
                        $invalid_validation = true;
                        
                        //update hits counter
                        $this->_OBJ_common->bsk_gfblcv_update_item_hits( $item_id_str );
                    }
                }
            break;
        }
        
        return $invalid_validation;
    }
    
}
