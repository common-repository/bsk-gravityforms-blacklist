<?php
class BSK_GFBLCV_Dashboard_Common {
	
	public function __construct( $args ) {
		
	}
    
    public static function bsk_gfblcv_get_list_by_type( $list_type, $selected ){
		global $wpdb;
		
		if( $list_type == "" ){
			return '';
		}
		
        $list_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_list_tbl_name;
		$options_str = '';
		
        if ( $list_type == 'BLACK_LIST' ) {
            $sql = 'SELECT * FROM `'.$list_table.'` WHERE `list_type` = %s ORDER BY `id` ASC LIMIT 0, 50';
            $sql = $wpdb->prepare( $sql, $list_type );
            $results = $wpdb->get_results( $sql );
            if( $results && is_array($results) && count($results) > 0 ){
                foreach( $results as $list_obj ){
                    $checked_str = $selected == $list_obj->id ? ' selected' : '';
                    $options_str .= '<option value="'.$list_obj->id.'"'.$checked_str.'>'.$list_obj->list_name.'</option>';
                }
            }
        } else {
            $options_str .= '<option value="">Only available in Pro version</option>';
        }
		
		return $options_str;
	}
    
    public static function bsk_gfblcv_get_list_comparison( $selected = '' ){
		$options_str = '';
		
		$options_str .= '<optgroup label="Case-insensitive">';
        $selected_str = $selected == 'SAME_CASE_INSENSITIVE' ? ' selected' : '';
		$options_str .= '<option value="SAME_CASE_INSENSITIVE"'.$selected_str.'>Same</option>';
        
        $selected_str = $selected == 'CONTAINS_CASE_INSENSITIVE' ? ' selected' : '';
		$options_str .= '<option value="CONTAINS_CASE_INSENSITIVE"'.$selected_str.'>Contains</option>';
		$options_str .= '</optgroup>';
		
		$options_str .= '<optgroup label="Case-sensitive">';
				
		//for case-sensitive
        $selected_str = $selected == 'SAME_CASE_SENSITIVE' ? ' selected' : '';
		$options_str .= '<option value="SAME_CASE_SENSITIVE"'.$selected_str.'>Same</option>';
        
        $selected_str = $selected == 'CONTAINS_CASE_SENSITIVE' ? ' selected' : '';
		$options_str .= '<option value="CONTAINS_CASE_SENSITIVE"'.$selected_str.'>Contains</option>';
		$options_str .= '</optgroup>';
		
		return $options_str;
	}
	
    public static function bsk_gfblcv_get_list_action( $selected, $only_selected = false ){
        
        $none_str = '<option value="">Action...</option>';
            
        $selected_str = $selected == 'ALLOW' ? ' selected' : '';
		$allow_str = '<option value="ALLOW"'.$selected_str.'>Allow</option>';
        
        $selected_str = $selected == 'BLOCK' ? ' selected' : '';
		$block_str = '<option value="BLOCK"'.$selected_str.'>Block</option>';
        
        $options_str = '';
        if( $only_selected ){
            if( $selected == 'ALLOW' ){
                $options_str = $allow_str;
            }else if( $selected == 'BLOCK' ){
                $options_str = $block_str;
            }
        }else{
            $options_str = $none_str.$allow_str.$block_str;
        }
		
		return $options_str;
	}
    
    public static function bsk_gfblcv_get_form_plugin() {
        
        $data_to_return = array();
        
        
		return $data_to_return;
	}
	
    public static function bsk_gfblcv_get_gf_forms( $form_plugin ) {
		global $wpdb;
        
        $entries_table_name = $wpdb->prefix . BSK_GFBLCV::$_bsk_gfblcv_entries_tbl_name;
        
        $data_to_return = array();
        //Gravity Forms
        if ( $form_plugin == 'GF' && isset( BSK_GFBLCV::$_supported_plugins['GF'] ) ){
            if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
            $gf_table_name = 'rg_form';
            if ( version_compare( BSK_GFBLCV::$_supported_plugins['GF']['version'], '2.2', '>=' ) ){
                 $gf_table_name = 'gf_form';
            }
            
            $gf_table_name = $wpdb->prefix . $gf_table_name;
            
            $sql = 'SELECT DISTINCT( E.`form_id` ), G.`title` from `'.$entries_table_name.'` AS E '.
                   'LEFT JOIN `'.$gf_table_name.'` AS G ON E.`form_id` = G.`id` '.
                   'WHERE E.`forms` LIKE "GF" ORDER BY G.`title` ASC';
            $results = $wpdb->get_results( $sql );
            if( !$results || !is_array( $results ) || count( $results ) < 1 ){
                return false;
            }
            
            foreach( $results as $gf_form_data ){
                $data_to_return[$gf_form_data->form_id] = $gf_form_data->title;
            }
        }
        
        if ( $form_plugin == 'FF' && isset( BSK_GFBLCV::$_supported_plugins['FF'] ) ){
            $forms = FrmForm::getAll(
                                        array(
                                            'is_template' => 0,
                                            'status'      => 'published',
                                            array(
                                                'or'               => 1,
                                                'parent_form_id'   => null,
                                                'parent_form_id <' => 1,
                                            ),
                                        ),
                                        'name'
                                    );
            if ( $forms && is_array( $forms ) && count( $forms ) > 0 ) {
                $sql = 'SELECT DISTINCT( E.`form_id` ) from `'.$entries_table_name.'` AS E '.
                       'WHERE E.`forms` LIKE "FF"';
                $results = $wpdb->get_results( $sql );
                if( !$results || !is_array( $results ) || count( $results ) < 1 ){
                    return false;
                }
                $entries_existing_form_ids = array();
                foreach( $results as $ff_form_existing ){
                    $entries_existing_form_ids[] = $ff_form_existing->form_id;
                }
                
                $forms_data_array = array();
                foreach( $forms as $ff_form_data ){
                    if( in_array($ff_form_data->id, $entries_existing_form_ids ) ){
                        $data_to_return[$ff_form_data->id] = $ff_form_data->name;
                    }
                }
            }
        }
        
		return $data_to_return;
	}
    
    public static function bsk_gfblcv_render_entry_html( $form_submit_data, $hits_data, $entry_id, $ip ) {
		global $wpdb;
                
        $hits_tbl = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_hits_tbl_name;
        $list_tbl = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_list_tbl_name;
        $items_tbl = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_items_tbl_name;
        
        if( $hits_data == false && $entry_id ){
            //get hits data
            $sql = 'SELECT H.*, L.`list_name`, L.`list_type`, L.`check_way`, L.`extra` AS list_extra, I.`value` '.
               'FROM `'.$hits_tbl.'` AS H LEFT JOIN `'.$list_tbl.'` AS L ON H.list_id = L.id '.
               'LEFT JOIN `'.$items_tbl.'` AS I ON H.`item_id` = I.id '.
               'WHERE H.`entry_id` = %d';
            $sql = $wpdb->prepare( $sql, $entry_id );
            $hits_data_results = $wpdb->get_results( $sql );
            if( $hits_data_results && is_array( $hits_data_results ) && count( $hits_data_results ) > 0 ){
                $hits_data = array();
                foreach( $hits_data_results as $hit_data_obj ){
                    if( !isset( $hits_data[$hit_data_obj->field_id] ) ){
                        $hits_data[$hit_data_obj->field_id] = array();
                    }
                    $hits_data[$hit_data_obj->field_id]['list_id'] = $hit_data_obj->list_id;
                    $hits_data[$hit_data_obj->field_id]['list_name'] = $hit_data_obj->list_name;
                    $hits_data[$hit_data_obj->field_id]['list_type'] = $hit_data_obj->list_type;
                    $hits_data[$hit_data_obj->field_id]['list_check_way'] = $hit_data_obj->check_way;
                    $hits_data[$hit_data_obj->field_id]['list_extra'] = $hit_data_obj->list_extra;
                    $hits_data[$hit_data_obj->field_id]['extra_data'] = $hit_data_obj->extra_data;
                    if( !isset( $hits_data[$hit_data_obj->field_id]['items_value'] ) ){
                        $hits_data[$hit_data_obj->field_id]['items_value'] = array();
                    }
                    $hits_data[$hit_data_obj->field_id]['items_value'][] = ( $hit_data_obj->item_id == -1 || $hit_data_obj->item_id == -2 ) ? 'NO_ITEM_MATCH' : $hit_data_obj->value;
                }
            }
        }
        
        if( !$hits_data || !is_array( $hits_data ) || count( $hits_data ) < 1 ){
            return '<p>No valid keywords hit data</p>';
        }
        
        ob_start();
        
        $form_data = maybe_unserialize( $form_submit_data );
        if( $form_data && is_array( $form_data ) && count( $form_data ) > 0 ){
        ?>
        <div class="bsk-gfblcv-entry-form-data-container">
            <table class="widefat striped">
                <thead>
                    <th>Field ID</th>
                    <th>Field label</th>
                    <th>Field value</th>
                    <th>&nbsp;</th>
                </thead>
                <tbody>

        <?php
        
        $_bsk_gfblcv_OBJ_ip_country = BSK_GFBLCV::instance()->_CLASS_OBJ_ip_country;
        foreach( $form_data as $field_ID => $field_data ){
            if( $field_ID == 'form_id' ){
                continue;
            }
            $blocked_info = '';
            if( $hits_data && isset( $hits_data[$field_ID] ) ){
                $blocked_data = $hits_data[$field_ID];
                $blocked_items_data = $hits_data[$field_ID]['items_value'];
                $blocked_item_extra_data = maybe_unserialize( $hits_data[$field_ID]['extra_data'] );

                $item_value = '';
                if( $blocked_items_data && is_array($blocked_items_data) && count($blocked_items_data) > 0 ){
                    foreach( $blocked_items_data as $blocked_item_value ){
                        if( $blocked_item_value == '' ){
                            continue;
                        }
                        $item_value .= '<span class="bsk-gfblcv-entry-blocked-keyword">'.$blocked_item_value.'</span>, ';
                    }
                }

                $item_value = trim( $item_value, ',' );
                if( $hits_data[$field_ID]['list_type'] == 'IP_LIST' && $hits_data[$field_ID]['list_check_way'] == 'COUNTRY' ) {
                    //
                }else{
                    $blocked_info .= 'blocked by item: '.$item_value;
                }

                if( $hits_data[$field_ID]['list_type'] == 'IP_LIST' && $hits_data[$field_ID]['list_check_way'] == 'COUNTRY' ){
                    $blocked_info .= 'blocked by list: ';
                }else{
                    $blocked_info .= ' on list: ';
                }


                $blocked_list_id = intval( $hits_data[$field_ID]['list_id'] );

                $_bsk_gfblcv_page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['base'];
                if( $hits_data[$field_ID]['list_type'] == 'WHITE_LIST' ){
                    $_bsk_gfblcv_page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['whitelist'];
                }else if( $hits_data[$field_ID]['list_type'] == 'EMIL_LIST' ){
                    $_bsk_gfblcv_page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['emailist'];
                }else if( $hits_data[$field_ID]['list_type'] == 'IP_LIST' ){
                    $_bsk_gfblcv_page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['iplist'];
                }
                $base_page_url = admin_url( 'admin.php?page='.$_bsk_gfblcv_page_slug );
                $list_edit_url = add_query_arg( 
                                                array( 'view' => 'edit', 'id' => $blocked_list_id ),
                                                $base_page_url 
                                              );
                $blocked_info .= '<a href="'.$list_edit_url.'">'.$hits_data[$field_ID]['list_name'].'</a>, ';
                $blocked_info .= $hits_data[$field_ID]['list_type'].', ';
                if( $blocked_item_extra_data['mode'] != 'NOT_APPLIIED' ){
                    $blocked_info .= $blocked_item_extra_data['mode'].' mode, ';
                }
                $blocked_info .= $hits_data[$field_ID]['list_check_way'].' check way.';

                if( $hits_data[$field_ID]['list_type'] == 'IP_LIST' && $hits_data[$field_ID]['list_check_way'] == 'COUNTRY' ){
                    $countrys_name = '';
                    if( $hits_data[$field_ID]['list_extra'] ){
                        $list_extra_array = unserialize( $hits_data[$field_ID]['list_extra'] );
                        $counry_code = false;
                        if( is_array( $list_extra_array ) && isset( $list_extra_array['country'] ) ){
                            $county = $list_extra_array['country'];
                            if( $county ){
                                $countrys_code_array = explode( ',', $county );
                                $countrys_name = $_bsk_gfblcv_OBJ_ip_country->get_countrys_name_by_code( $countrys_code_array );
                            }
                        }
                    }

                    if( $countrys_name ){
                        if( $blocked_item_extra_data['mode'] == 'ALLOW' ){
                            $blocked_info .= ' IP out of <span style="font-weight: bold;">'.$countrys_name.'</span> is blocked';
                        }else if( $blocked_item_extra_data['mode'] == 'BLOCK' ){
                            $blocked_info .= ' IP in <span style="font-weight: bold;">'.$countrys_name.'</span> is blocked';
                        }
                    }
                }

                if( $hits_data[$field_ID]['list_type'] == 'IP_LIST' ){
                    $blocked_info .= '<p>Client IP: <span style="font-weight: bold;">'.$ip.'</p>';
                }
            }
                $field_data_value = is_array($field_data['value']) ? implode( ';', $field_data['value']) : $field_data['value'];
        ?>
                <tr>
                    <td class="bsk-gfblcv-column-ID"><?php echo $field_ID; ?></td>
                    <td class="bsk-gfblcv-column-label"><label><?php echo $field_data['label']; ?></label></td>
                    <td class="bsk-gfblcv-column-value"><?php echo $field_data_value; ?></td>
                    <td class="bsk-gfblcv-column-blocked-info"><?php echo $blocked_info; ?></td>
                </tr>
        <?php
        }
        ?>
                </tbody>
            </table>
        </div>
        <?php
        }
        
        $entry_html = ob_get_contents();
        ob_end_clean();
        
        return $entry_html;
	}
    
    public static function bsk_gfblcv_get_mail_tmpl(){
        require_once( 'common-tmpl.php' );
        
        return $email_html_tmpl;
    }
    
    public static function bsk_gfblcv_is_form_plugin_supported( $form_plugin ){
        
        $settings_data = get_option( BSK_GFBLCV_Dashboard::$_plugin_settings_option, false );
        $supported_form_plugins = array( 'GF' );
        if( $settings_data && is_array( $settings_data ) && count( $settings_data ) > 0 ){
            if( isset( $settings_data['supported_form_plugins'] ) && count( $settings_data['supported_form_plugins'] ) > 0 ){
                $supported_form_plugins = $settings_data['supported_form_plugins'];
            }
        }

        return ( in_array( $form_plugin, $supported_form_plugins ) && isset( BSK_GFBLCV::$_supported_plugins[$form_plugin] ) );
    }
    
    public static function bsk_gfblcv_cf7_convert_cf7blacklist_data( $contact_form ) {
        
        $form_id = $contact_form->id();

		//get old data
		$saved_form_data = get_post_meta( $form_id, '_cf7_blacklist_form_list_data_', true );
		if ( ! $saved_form_data || ! is_array( $saved_form_data ) || count( $saved_form_data ) < 1 ) {
			return;
		}

		$bsk_gfblcv_form_settings = array();
		$bsk_gfblcv_form_settings['enable'] = isset( $saved_form_data['enable'] ) && $saved_form_data['enable'] == 'YES' ? true : false;
		$bsk_gfblcv_form_settings['actions'] = array( 'BLOCK' );
		if ( isset( $saved_form_data['block_or_skip'] ) && $saved_form_data['block_or_skip'] == 'SKIP' ) {
			$bsk_gfblcv_form_settings['actions'] = 'SKIP';
		}
		$bsk_gfblcv_form_settings['mails_to_skip'] = array();
		if ( isset( $saved_form_data['skip_mails_Mail'] ) && $saved_form_data['skip_mails_Mail'] == 'YES' ) {
			$bsk_gfblcv_form_settings['mails_to_skip'][] = 'Mail';
		}
		if ( isset( $saved_form_data['skip_mails_Mail_2'] ) && $saved_form_data['skip_mails_Mail_2'] == 'YES' ) {
			$bsk_gfblcv_form_settings['mails_to_skip'][] = 'Mail_2';
		}

		$bsk_gfblcv_form_settings['save_blocked_data'] = 'NO';
		$bsk_gfblcv_form_settings['notify_administrators'] = 'NO';
		$bsk_gfblcv_form_settings['notify_send_to'] = '';;
		$bsk_gfblcv_form_settings['delete_entry'] = 'NO';

		$default = 'The value for field "[FIELD_LABEL]" is invalid!';
        $ip_default = 'Your IP address [VISITOR_IP] is forbidden!';

		$bsk_gfblcv_form_settings['blacklist_message'] = $default;
		if ( isset( $saved_form_data['blacklist_validation_message'] ) && $saved_form_data['blacklist_validation_message'] ) {
			$bsk_gfblcv_form_settings['blacklist_message'] = $saved_form_data['blacklist_validation_message'];
		}
		$bsk_gfblcv_form_settings['whitelist_message'] = $default;
		if ( isset( $saved_form_data['white_list_validation_message'] ) && $saved_form_data['white_list_validation_message'] ) {
			$bsk_gfblcv_form_settings['whitelist_message'] = $saved_form_data['white_list_validation_message'];
		}
		$bsk_gfblcv_form_settings['emaillist_message'] = $default;
		if ( isset( $saved_form_data['email_list_validation_message'] ) && $saved_form_data['email_list_validation_message'] ) {
			$bsk_gfblcv_form_settings['emaillist_message'] = $saved_form_data['email_list_validation_message'];
		}
		$bsk_gfblcv_form_settings['iplist_message'] = $ip_default;
		$bsk_gfblcv_form_settings['invitlist_message'] = $default;

		//save form settings
		update_post_meta( $form_id, BSK_GFBLCV_Dashboard::$_bsk_gfblcv_cf7_form_settings_opt, $bsk_gfblcv_form_settings );


		//convert form fields mapping
        $form_fields = self::cf7_blacklist_get_form_fields( $form_id );
        if( !$form_fields || !is_array( $form_fields ) || count( $form_fields ) < 1 ){
            //delete old form data
            $return = delete_post_meta( $form_id, '_cf7_blacklist_form_list_data_' );

            return;
        }
        //get list id_mapping
        $list_id_mapping = get_option( BSK_GFBLCV::$_cf7_blacklist_list_id_mapping, false );
        if( ! $list_id_mapping || ! is_array( $list_id_mapping ) || count( $list_id_mapping ) < 1 ) {
            //delete old form data
            $return = delete_post_meta( $form_id, '_cf7_blacklist_form_list_data_' );

            return;
        }

        $bsk_gfblcv_form_mappings = array();
        foreach( $form_fields as $field ) {
            if( $field->name == "" || ! isset( $saved_form_data[$field->name] ) ){
                continue;
            }

            $converted = $saved_form_data[$field->name];
            $converted['list_comparison'] = $converted['list_comparision'];
            unset( $converted['list_comparision'] );

            $converted['save_comparison_error'] = false;
            $converted['save_id_error'] = false;
            if ( $converted['list_type'] == '' ) {
                $bsk_gfblcv_form_mappings[$field->name] = $converted;
                continue;
            }

            if ( $converted['list_id'] < 1 ) {
                $converted['save_comparison_error'] = true;
            } else {
                $converted['list_id'] = $list_id_mapping[$converted['list_id']];
            }
            
            if ( $converted['list_comparison'] == '' ) {
                $converted['save_comparison_error'] = true;
            }

            $bsk_gfblcv_form_mappings[$field->name] = $converted;
        }

        //save form mappings
        update_post_meta( $form_id, BSK_GFBLCV_Dashboard::$_bsk_gfblcv_cf7_form_mappings_opt, $bsk_gfblcv_form_mappings );

        //delete old form data
        $return = delete_post_meta( $form_id, '_cf7_blacklist_form_list_data_' );
    
	}

    public static function cf7_blacklist_get_form_fields( $post_id ) {
        if ( $post_id < 1 ) {
            return false;
        }

		$contact_form = WPCF7_ContactForm::get_instance( $post_id );
		$manager = WPCF7_FormTagsManager::get_instance();

        if ( ! $contact_form ) {
            return false;
        }

		$form_fields = $manager->scan( $contact_form->prop( 'form' ) );

		return $form_fields;
	}

    public static function get_ip() {

		$ip = $_SERVER['REMOTE_ADDR'];

		// HTTP_X_FORWARDED_FOR can return a comma separated list of IPs; use the first one
		$ips = explode( ',', $ip );

		return $ips[0];
	}

    public static function forminator_get_form_fields( $form_id ) {
        $form_fields = Forminator_API::get_form_fields( $form_id );
        
        $child_field_separator = BSK_GFBLCV_Dashboard_Forminator::$_bsk_gfblcv_frmt_child_field_separator;
        $form_fields_array = false;
        if ( ! is_wp_error( $form_fields ) && is_array( $form_fields ) && count( $form_fields ) > 0 ) {
            
            $form_fields_array = array();

            foreach ( $form_fields as $form_field_obj ) {
                $field_obj_to_array = $form_field_obj->to_array();
                $field_label = isset( $field_obj_to_array['field_label'] ) ? $field_obj_to_array['field_label'] : '';

                switch ( $field_obj_to_array['type'] ) {
                    case 'name':
                        $field_label = $field_label == '' ? 'Name' : $field_label;
                        if ( isset( $field_obj_to_array['multiple_name'] ) && $field_obj_to_array['multiple_name'] ) {
                            $name_child_fields = array( 'prefix', 'fname', 'mname', 'lname', );
                            foreach ( $name_child_fields as $child_field_name ) {
                                if ( $field_obj_to_array[$child_field_name] ) {
                                    $form_fields_array[$field_obj_to_array['id'] . $child_field_separator . $child_field_name] = array( 
                                                                                            'type' => $field_obj_to_array['type'] . '.' . $child_field_name,
                                                                                            'label' => $field_label . ' . ' . $field_obj_to_array[$child_field_name . '_label'],
                                                                                            'parent_id' => $field_obj_to_array['id'],
                                                                                        );
                                }
                            }
                        } else {
                            $form_fields_array[$field_obj_to_array['id']] = array( 
                                                                                    'type' => $field_obj_to_array['type'],
                                                                                    'label' => $field_label,
                                                                                );
                        }
                    break;
                    case 'address':
                        $address_child_fields = array( 'street_address', 'address_line', 'address_city', 'address_state', 'address_zip', 'address_country', );
                        foreach ( $address_child_fields as $child_field_name ) {
                            if ( $field_obj_to_array[$child_field_name] ) {
                                $form_fields_array[$field_obj_to_array['id'] . $child_field_separator . $child_field_name] = array( 
                                                                                        'type' => $field_obj_to_array['type'] . '.' . $child_field_name,
                                                                                        'label' => 'Address . ' . $field_obj_to_array[$child_field_name . '_label'],
                                                                                        'parent_id' => $field_obj_to_array['id'],
                                                                                    );
                            }
                        }
                    break;
                    case 'hidden':
                        $field_label = $field_label == '' ? 'Hidden Field' : $field_label;
                        $form_fields_array[$field_obj_to_array['id']] = array( 
                                                                                'type' => $field_obj_to_array['type'],
                                                                                'label' => $field_label,
                                                                            );
                    break;
                    default:
                        $form_fields_array[$field_obj_to_array['id']] = array( 
                                                                                'type' => $field_obj_to_array['type'],
                                                                                'label' => $field_label,
                                                                             );
                    break;
                }
            }
        }

        return $form_fields_array;
    }

}
