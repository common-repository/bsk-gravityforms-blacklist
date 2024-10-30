<?php

class BSK_GFBLCV_Submitting_Common {

	public function __construct() {
        
	}
    
    function bsk_gfblcv_front_check_list_status( $list_id, $list_type ) {
        global $wpdb;
        
        $sql = 'SELECT COUNT(*) FROM `'.$wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_list_tbl_name.'` WHERE `id` = %d AND `list_type` = %s';
        $sql = $wpdb->prepare( $sql, intval($list_id), esc_sql( $list_type ) );
        if ( $wpdb->get_var( $sql ) < 1 ) {
            return false;
        }
        
        return true;
    }
    
    function bsk_gfblcv_render_validation_message( $validation_message, $field_label, $field_value, $ip_address ){
        
        $validation_message = str_replace(
                                          '[FIELD_LABEL]', 
                                          $field_label, 
                                          $validation_message
                                         );
        $validation_message = str_replace(
                                          '[FIELD_VALUE]',
                                          $field_value, 
                                          $validation_message 
                                         );
        
        $validation_message = str_replace(
                                          '[VISITOR_IP]',
                                          $ip_address, 
                                          $validation_message 
                                         );
        
        
        return $validation_message;
    }
    
    function bsk_gfblcv_update_item_hits( $items_id_str ) {
        global $wpdb;
        
        $table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_items_tbl_name;
        $sql = 'UPDATE `'.$table.'` SET `hits` = `hits` + 1 WHERE `id` IN( '.esc_sql($items_id_str).' )';
        $wpdb->query( $sql );
    }
    
    function bsk_gfblcv_front_check_field_value_match_list( $list_type, $list_id, $list_comparison, $field_value ){
		global $wpdb;
		
		if( $field_value == "" ){
			return false;
		}
        
        $list_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_list_tbl_name;
        $items_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_items_tbl_name;
        
        $list_data_sql = 'SELECT `list_type`, `check_way`, `extra` FROM `'.$list_table.'` '.
                         'WHERE `id` = %d AND `list_type` = %s';
        $list_data_sql = $wpdb->prepare( $list_data_sql, $list_id, 'BLACK_LIST' );
        $list_data_results = $wpdb->get_results( $list_data_sql );

        if( !$list_data_results || !is_array( $list_data_results ) || count( $list_data_results ) < 1 ){
            return false;
        }
        
        $list_check_way = $list_data_results[0]->check_way;
        $list_extra_data = false; 
        if( $list_data_results[0]->extra ){
            $list_extra_data = unserialize( $list_data_results[0]->extra );
        }
        
        if( $list_type == 'IP_LIST' && $list_check_way == 'COUNTRY' ){
            //do nothing
        }else{
            $items_array = array();
            //get items data
            $items_sql = 'SELECT I.`id`, I.`value` FROM `'.$items_table.'` AS I '.
                         'WHERE I.`list_id` = %d '.
                         'ORDER BY I.`id` ASC '.
                         'LIMIT 0, 100';
            $items_sql = $wpdb->prepare( $items_sql, $list_id );
            $items_array = $wpdb->get_results( $items_sql );
            if( !$items_array || !is_array($items_array) || count($items_array) < 1 ){
                return false;
            }
        }
        
        $checked_results = false;
		switch ($list_comparison) {
			case 'SAME_CASE_INSENSITIVE':
				$field_value_uppercase = strtoupper( $field_value );
				foreach( $items_array as $item_obj ){
					$item_uppercase = strtoupper( $item_obj->value );
					if( $item_uppercase == $field_value_uppercase ){
						$checked_results = $item_obj->id;
						break;
					}
				}
			break;
			case 'CONTAINS_CASE_INSENSITIVE':
				$field_value_uppercase = strtoupper( $field_value );
				foreach( $items_array as $item_obj ){
					$item_uppercase = strtoupper( $item_obj->value );
					if( strpos($field_value_uppercase, $item_uppercase) !== false ){
						$checked_results = $item_obj->id;
						break;
					}
                    
                    //enhanced checking 1
                    $pattern = '/[a-zA-Z0-9]+([ "*#$!=+@%_\'~,-]+)[a-zA-Z0-9]+/i';
                    $matches_array = array();
                    $match_return = preg_match_all( $pattern, $field_value, $matches_array );
                    if( $match_return && count($matches_array) > 1 ){
                        $special_char_array = array_unique( $matches_array[1] );
                        foreach( $special_char_array as $special_char ){
                            //organise new keyword
                            $new_item_uppercase_array = str_split( $item_uppercase );
                            $item_to_check = implode( $special_char, $new_item_uppercase_array );
                            
                            if( strpos($field_value_uppercase, $item_to_check) !== false ){
                                $checked_results = $item_obj->id;
                                break; //only break foreach for $special_char_array
                            }
                        }
                    }
                    
                    if( $checked_results == true ){
                        break;
                    }
                    
                    //enhanced checking 2
                    $pattern = '/[ "*#$!=+@%_\'~,-]/';
                    $field_value_uppercase = preg_replace( $pattern, '', $field_value_uppercase );
                    if( strpos($field_value_uppercase, $item_uppercase) !== false ){
                        $checked_results = $item_obj->id;
                        break;
                    }
				}
			break;
			case 'SAME_CASE_SENSITIVE':
				foreach( $items_array as $item_obj ){
					if( $field_value == $item_obj->value ){
						$checked_results = $item_obj->id;
						break;
					}
				}
			break;
			case 'CONTAINS_CASE_SENSITIVE':
				foreach( $items_array as $item_obj ){
					if( strpos($field_value, $item_obj->value) !== false ){
						$checked_results = $item_obj->id;
						break;
					}
                    
                    //enchanced checking 1
                    $pattern = '/[a-zA-Z0-9]+([ "*#$!=+@%_\'~,-]+)[a-zA-Z0-9]+/i';
                    $matches_array = array();
                    $match_return = preg_match_all( $pattern, $field_value, $matches_array );
                    if( $match_return && count($matches_array) > 1 ){
                        $special_char_array = array_unique( $matches_array[1] );
                        foreach( $special_char_array as $special_char ){
                            //organise new keyword
                            $new_item_array = str_split( $item_obj->value );
                            $item_to_check = implode( $special_char, $new_item_array );

                            if( strpos($field_value, $item_to_check) !== false ){
                                $checked_results = $item_obj->id;
                                break; //only break foreach for $special_char_array
                            }
                        }
                    }
                    
                    if( $checked_results ){
                        break;
                    }

                    //enhanced checking 2
                    $pattern = '/[ "*#$!=+@%_\'~,-]/';
                    $field_value = preg_replace( $pattern, '', $field_value );
                    if( strpos($field_value, $item_obj->value) !== false ){
                        $checked_results = $item_obj->id;
                        break;
                    }
				}
			break;
			//for email
			case 'ALLOW':
			case 'BLOCK':
                
			break;
		}

        return $checked_results;
	}
    
}
