<?php

class BSK_GFBLCV_Dashboard_List {
    
	public function __construct() {
		
        require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/items.php' );
        		
		add_action( 'bsk_gfblcv_save_list', array($this, 'bsk_gfblcv_save_list_fun') );
		add_action( 'bsk_gfblcv_save_item', array($this, 'bsk_gfblcv_save_item_fun') );
		add_action( 'bsk_gfblcv_delete_item', array($this, 'bsk_gfblcv_delete_item_fun') );
		add_action( 'bsk_gfblcv_delete_list_by_id', array($this, 'bsk_gfblcv_delete_list_by_id_fun') );
	}
	
	function bsk_gfblcv_list_edit( $list_id, $list_view, $current_view ){
		global $wpdb;
		
		$list_type = 'BLACK_LIST';
		$list_title = 'BSK Forms Blacklist';
        $page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['base'];
		if( $list_view == 'whitelist' ){
			$list_type = 'WHITE_LIST';
			$list_title = 'BSK Forms White List';
            $page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['whitelist'];
		}else if( $list_view == 'emaillist' ){
			$list_type = 'EMAIL_LIST';
			$list_title = 'BSK Forms Email List';
            $page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['emailist'];
		}else if( $list_view == 'iplist' ){
			$list_type = 'IP_LIST';
			$list_title = 'BSK Forms IP List';
            $page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['iplist'];
		}else if( $list_view == 'invitlist' ){
			$list_type = 'INVIT_LIST';
			$list_title = 'BSK Forms Invitation Codes List';
            $page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['invitlist'];
		}
		
        $list_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_list_tbl_name;
		$list_name = '';
        $check_way = 'ANY';
        $bsk_gfblcv_edit_item_container = 'block';
        $ip_list_extra = false;
		if( $list_id > 0 ){
			$sql = 'SELECT * FROM '.$list_table.' WHERE id = %d AND `list_type` = %s';
			$sql = $wpdb->prepare( $sql, $list_id, $list_type );
			$list_obj_array = $wpdb->get_results( $sql );
			if( $list_obj_array&& is_array( $list_obj_array ) && count($list_obj_array) > 0 ){
				$list_name = $list_obj_array[0]->list_name;
				$list_date = date( 'Y-m-d', strtotime($list_obj_array[0]->date) );
                $check_way = $list_obj_array[0]->check_way;
                
                if( $list_obj_array[0]->extra && is_string( $list_obj_array[0]->extra ) && strlen( $list_obj_array[0]->extra ) > 1 ){
                    $ip_list_extra = unserialize( $list_obj_array[0]->extra );
                }
			}
		}
        
        $country_to_block_or_allow_array = false;
        $api_server = '';
        $api_key = '';
        if( $ip_list_extra && is_array( $ip_list_extra ) && count( $ip_list_extra ) > 0 ){

            $country_to_block_or_allow_array = explode( ',', $ip_list_extra['country'] );
            $api_server = $ip_list_extra['api_server'];
            $api_key = $ip_list_extra['api_key'];
        }
        
        $check_way_any_checked = $check_way == 'ANY'  ? 'checked' : '';
        $check_way_all_checked = $check_way == 'ALL' ? 'checked' : '';
        
        $check_ip_address_checked = ' checked';
        $check_ip_country_checked = '';
        $iplist_by_country_settings_display = 'none';
        if( $check_way == 'COUNTRY' ){
            $check_ip_address_checked = '';
            $check_ip_country_checked = 'checked';
            $bsk_gfblcv_edit_item_container = 'none';
            $iplist_by_country_settings_display = 'block';
        }
		
        $base_page_url = admin_url( 'admin.php?page='.$page_slug );
		$page_url = add_query_arg( array('view' => 'edit', 'id' => $list_id, 'listview' => $list_view), $base_page_url );
        
        $list_title_for_message = ucfirst(strtolower($list_title));
        if( substr($list_title, 0, 3) == 'BSK' ){
            $list_title_for_message = 'BSK'.ucfirst(strtolower( substr( $list_title_for_message, 3 ) ));
        }

        $item_label = 'item';
        if( $list_view == 'invitlist' ){
            $item_label = 'code';
        }
		?>
        <div class="wrap">
        	<div id="icon-edit" class="icon32"><br/></div>
            <h2><?php echo $list_title; ?></h2>
            <?php
            if ( $list_type == 'IP_LIST' || $list_type == 'WHITE_LIST' || $list_type == 'EMAIL_LIST' || $list_type == 'INVIT_LIST' ) {
            ?>
            <div class="bsk-gfblcv-tips-box">
                <p>This feature only availabel in Pro version</p>
                <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
            </div>
            <?php
            }
            ?>
            <div class="bsk-gfblcv-edit-list-container">
                <form id="bsk_gfblcv_list_edit_form_id" method="post" action="<?php echo $page_url; ?>">
				<?php if( isset($_GET['list_save']) && $_GET['list_save'] == 'succ' ){ ?>
                <div class="notice notice-success is-dismissible inline">
                    <p><?php echo $list_title_for_message; ?> saved successfully</p>
                </div>
                <?php } ?>
                <?php
                if( isset($_GET['list_save']) && $_GET['list_save'] == 'failed' ){
                    $error_message = '';
                    if( isset($_GET['iperr']) ){
                        if( $_GET['iperr'] == 'ipcountry' ){
                            $error_message = 'Please select at least one country to block';
                        }else if( $_GET['iperr'] == 'ipserver' ){
                            $error_message = 'Please choose the API server to decode IP address';
                        }else if( $_GET['iperr'] == 'ipkey' ){
                            $error_message = 'Please enter the API key for the server you selected';
                        }
                        ?>
                        <div class="notice notice-error is-dismissible inline">
                            <p><?php echo $error_message; ?></p>
                        </div>
                        <?php
                    }
                }
                ?>
                <?php if( $list_id < 1 ){ ?>
                <h3>Add New <?php echo $list_title; ?></h3>
                <?php }else{ ?>
                <h3>Edit <?php echo $list_title; ?></h3>
                <?php } ?>
                <p>
                    <label class="bsk-gfblcv-admin-label">List Name: </label>
                    <input type="text" class="bsk-gfblcv-add-list-input" name="bsk_gfblcv_list_name" id="bsk_gfblcv_list_name_ID" value="<?php esc_attr_e( $list_name ); ?>" maxlength="512" />
                    <a class="bsk-gfblcv-action-anchor" id="bsk_gfblcv_blacklist_list_save_ID">Save</a>
                </p>
                <?php if( $list_type == 'BLACK_LIST' || $list_type == 'WHITE_LIST' ){ ?>
                <p>
                    <label class="bsk-gfblcv-admin-label">Check Item Way: </label>
                    <label><input type="radio" class="bsk-gfblcv-list-check-way-raido" name="bsk_gfblcv_list_check_way" value="ANY" <?php echo $check_way_any_checked; ?>/> Any</label>
                    <label><input type="radio" class="bsk-gfblcv-list-check-way-raido" style="margin-left: 20px;" name="bsk_gfblcv_list_check_way" value="ALL" <?php echo $check_way_all_checked; ?> /> All</label>
                </p>
                <p>
                    <label class="bsk-gfblcv-admin-label">&nbsp;</label>
                    <span><b>Any</b> <i>means block / allow event will be triggered if</i> <b>ONE</b> <i>item matched</i></span>
                    <span style="display: block; margin-left: 220px;">&nbsp;<b>All</b> <i>means block / allow event will be triggered if</i> <b>ALL</b> <i>item(s) matched</i></span>
                </p>
                <p>
                    <div class="bsk-gfblcv-tips-box bsk-gfblcv-black-whitle-list-check-all" style="display: none;" id="bsk_gfblcv_black_whitle_list_check_all_ID">
                        <p>This feature only supported in Pro version</p>
                        <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
                    </div>
                </p>
                <?php } ?>
                <?php 
                if( $list_view == 'iplist' ){ 
                ?>
                <p>
                    <label class="bsk-gfblcv-admin-label">Check IP by: </label>
                    <label><input type="radio" name="bsk_gfblcv_iplist_itmes_type" value="IP" <?php echo $check_ip_address_checked; ?> class="bsk-gfblcv-ip-list-check-way-radio" /> Addresses</label>
                    <label style="margin-left: 20px;"><input type="radio" name="bsk_gfblcv_iplist_itmes_type" value="COUNTRY" <?php echo $check_ip_country_checked; ?> class="bsk-gfblcv-ip-list-check-way-radio" /> Country</label>
                </p>
                <div id="bsk_gfblcv_iplist_by_country_settings_container_ID" style="display: <?php echo $iplist_by_country_settings_display; ?>">
                    <div class="bsk-gfblcv-tips-box">
                        <p>This feature requires a <span style="font-weight: bold;">CREATOR</span>( or above ) license for Pro version</p>
                        <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
                    </div>
                    <p>
                        <label class="bsk-gfblcv-admin-label">Country to block or allow</label>
                        <select name="bsk_gfblcv_iplist_by_country_country_to_block_or_allow" id="bsk_gfblcv_iplist_by_country_country_to_block_or_allow_ID" style="width: 350px;">
                            <option value="">Choose country</option>
                            <?php
                            $_bsk_gfblcv_OBJ_ip_country = BSK_GFBLCV::instance()->_CLASS_OBJ_ip_country;
                            $system_built_in_countries = $_bsk_gfblcv_OBJ_ip_country->bsk_gfblcv_get_county_code_list();
                            foreach( $system_built_in_countries as $country_code => $country_name ){
                                $selected = $country_code == $country_to_block_or_allow ? ' selected' : '';
                                echo '<option value="'.$country_code.'"'.$selected.'>'.$country_name.'</option>';
                            }
                            
                            $country_code_hidden = '';
                            if( $country_to_block_or_allow_array && count( $country_to_block_or_allow_array ) > 0 ){
                                $country_code_hidden = implode( ',', $country_to_block_or_allow_array );   
                            }
                            ?>
                        </select>
                        <input type="hidden" name="bsk_gfblcv_iplist_by_country_exist_countries_code" value="<?php echo $country_code_hidden; ?>" id="bsk_gfblcv_iplist_by_country_exist_countries_code_ID" />
                        <input type="hidden" id="bsk_gfblcv_delete_country_code_icon_ID" value="<?php echo BSK_GFBLCV::$delete_country_code_icon_url; ?>" />
                    </p>
                    <div>
                        <label class="bsk-gfblcv-admin-label">&nbsp;</label>
                        <div id="bsk_gfblcv_iplist_by_country_added_countries_container_ID" style="display: inline-block;">
                            <?php
                            if( $country_to_block_or_allow_array && count( $country_to_block_or_allow_array ) > 0 ){
                                foreach( $country_to_block_or_allow_array as $country_code ){
                                    if( strlen($country_code) < 2 ){
                                        continue;
                                    }
                                ?>
                                <span style="display: inline-block;padding-right:10px;"><a href="javascript:void(0);" class="bsk-gfblcv-delete-country-code-anchor" data-country_code="<?php echo $country_code; ?>"><img src="<?php echo BSK_GFBLCV::$delete_country_code_icon_url; ?>" style="width:12px;height:12px;" /></a>&nbsp;<?php echo $system_built_in_countries[$country_code]; ?></span>
                                <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    $api_servers_list = $_bsk_gfblcv_OBJ_ip_country->bsk_gfblcv_get_api_server_list();
                    $disable_api_key = $api_server == 'ip-api.com.free' ? 'disabled' : '';
                    ?>
                    <p>
                        <label class="bsk-gfblcv-admin-label">IP Geolocation API Server</label>
                        <select name="bsk_gfblcv_iplist_by_country_API_server_to_use" id="bsk_gfblcv_iplist_by_country_API_server_to_use_ID" style="width: 350px;">
                            <option value="">Choose API server</option>
                            <?php 
                            foreach( $api_servers_list as $api_server_key => $api_server_data ){
                                $selected = '';
                                if( $api_server == $api_server_key ){
                                   $selected = ' selected'; 
                                }

                                echo '<option value="'.$api_server_key.'"'.$selected.' id="'.$api_server_data['key'].'">'.$api_server_data['label'].'</option>';
                            }
                            ?>
                        </select>
                        <span style="margin-left: 20px;">If the API server you'd like to use not listed here then please <a href="https://www.bannersky.com/contact-us/" target="_blank">contact us</a>.</span>
                    </p>
                    <div>
                        <label class="bsk-gfblcv-admin-label">&nbsp;</label>
                        <?php
                        foreach( $api_servers_list as $api_server_key => $api_server_data ){
                            $api_server_key_id = str_replace( '.', '_', $api_server_key );
                            $display = 'none';
                            if( $api_server_key == $api_server ){
                                $display = 'inline-block';
                            }
                            
                            $appendix_text = $api_server_key == 'ip-api.com.free' ? ' This server does not require API key.' : '';
                        ?>
                        <div id="bsk_gfblcv_iplist_by_country_api_server_ref_<?php echo $api_server_key_id; ?>_ID" class="bsk-gfblcv-iplist-by-country-api-server-ref" style="display: <?php echo $display; ?>;">To get API Key, please visit <a href="<?php echo $api_server_data['ref']; ?>" target="_blank"><?php echo $api_server_data['ref'].$appendix_text; ?></a>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                    <p>
                        <label class="bsk-gfblcv-admin-label">API Key</label>
                        <input type="text" name="bsk_gfblcv_iplist_by_country_API_key" value="<?php echo $api_key; ?>" id="bsk_gfblcv_iplist_by_country_API_key_ID" style="width: 65%;" <?php echo $disable_api_key; ?>/>
                    </p>
                    <p>
                        <label class="bsk-gfblcv-admin-label">Enter an IP to test</label>
                        <input type="text" value="" id="bsk_gfblcv_iplist_by_country_API_test_IP_value_ID" style="width: 350px;" />
                        <a class="bsk-gfblcv-action-anchor bsk-gfblcv-iplist-test-anchor">Test</a>
                        <span class="bsk-gfblcv-iplist-api-test-ajax-loder" style="display: none;"><?php echo BSK_GFBLCV::$ajax_loader; ?></span>
                    </p>
                    <div id="bsk_gfblcv_iplist_api_test_response_container_ID"></div>
                    <?php $ajax_nonce = wp_create_nonce( 'bsk_gfblcv_ip_list_test_api_ajax_oper_nonce' ); ?>
                    <input type="hidden" id="bsk_gfblcv_ip_list_test_api_nonce_ID" value="<?php echo $ajax_nonce; ?>" />
                </div>
                <?php } ?>
                <p>
                    <input type="hidden" name="bsk_gfblcv_list_id" value="<?php echo $list_id; ?>" />
                    <input type="hidden" name="bsk_gfblcv_list_type" value="<?php echo $list_type; ?>" />
                    <input type="hidden" name="bsk_gfblcv_action" value="save_list" />
                    <?php wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_gfblcv_list_save_oper_nonce' ); ?>
                </p>
                </form>
            </div>
            <?php if( $list_id > 0 ){ ?>
            <p style="margin-top: 20px;">&nbsp;</p>
			<a id="bsk_gfblcv_edit_items_conteianer_anchor">&nbsp;</a>
            <div class="bsk-gfblcv-edit-item-container" style="display: <?php echo $bsk_gfblcv_edit_item_container; ?>" id="bsk_gfblcv_edit_item_container_ID">
            	<?php if( isset($_GET['item_action']) && trim($_GET['item_action']) != "" ){ ?>
                <script type="text/javascript">
					jQuery(document).ready( function($) {
						$('html, body').animate({
						  scrollTop: $("#bsk_gfblcv_edit_items_conteianer_anchor").offset().top
						}, 1000);
					});
				</script>
                <?php
					$notice_message = 'Successfully!';
					$notice_class 	 = 'notice-success';
					switch( $_GET['item_action'] ){
						case 'save_succ':
							$notice_message = ucfirst($item_label).' saved successfully';
						break;
						case 'del_succ':
							$notice_message = ucfirst($item_label).' deleted';
						break;
						case 'upload_csv_failed':
							$notice_message = 'Upload CSV file failed';
							$notice_class 	 = 'notice-error';
						break;
						case 'open_csv_failed':
							$notice_message = 'The CSV file cannot be open';
							$notice_class 	 = 'notice-error';
						break;
						case 'empty_csv':
							$notice_message = 'The CSV file is empty';
							$notice_class 	 = 'notice-error';
						break;
						case 'invalid_csv_type':
							$notice_message = 'The CSV file type is not right';
							$notice_class 	 = 'notice-error';
						break;
						case 'inserted_count':
							$inserted_count = absint( sanitize_text_field( $_GET['inserted_count'] ) );
							if ( $inserted_count < 1 ) {
								$notice_message = 'No '.$item_label.' has been imported, please check you CSV file.';
								$notice_class 	 = 'notice-error';
								if( $list_type == 'EMAIL_LIST' ){
									$notice_message .= ' Only valid email address accepted.';
								}
							} else if( $inserted_count == 1 ) {
								$notice_message = $inserted_count.' '.$item_label.' has been imported successfully';
							} else {
                                $notice_message = $inserted_count.' '.$item_label.'s have been imported successfully';
                            }
						break;
                        case 'generated_count':
                            $generated_count = absint( sanitize_text_field( $_GET['generated_count'] ) );
                            $notice_message = $generated_count.' '.$item_label.'(s) has been generated successfully';
                        break;
                        case 'duplicated_codes':
							$notice_message = 'The invitation code alreay exist.';
							$notice_class 	 = 'notice-error';
						break;
                        case 'max_error':
                            $notice_message = 'You have reached the item maximum.';
							$notice_class 	 = 'notice-error';
						break;
					}
				?>
                <div class="notice <?php echo $notice_class; ?> is-dismissible inline">
                    <p><?php echo $notice_message; ?></p>
                </div>
                <?php } ?>
                <h3>Items:</h3>
                <div class="bsk-gfblcv-tips-box">
                    <p>Free verison only supports max 100 items in a list.</p>
                    <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
                </div>
                <form id="bsk_gfblcv_items_form_id" method="post" action="<?php echo $page_url; ?>" enctype="multipart/form-data">
                <?php if( $list_view == 'emaillist' ){ ?>
                <p style="margin-top:20px;">
                	<input type="checkbox" name="bsk_gfblcv_add_email_domain_name_checkbox" id="bsk_gfblcv_add_email_domain_name_checkbox_ID" value="YES" /><label for="bsk_gfblcv_add_email_domain_name_checkbox_ID"> Add email domain name</label>
                </p>
                <p style="margin-top:20px;display:none;" id="bsk_gfblcv_add_email_domain_name_input_container_ID" >
                    <label class="bsk-gfblcv-admin-label">Add email domain name:</label> 
                    <input type="text" class="bsk-gfblcv-add-item-input" name="bsk_gfblcv_email_domain_name" id="bsk_gfblcv_email_domain_name_ID" maxlength="512"/>
                    <a class="bsk-gfblcv-action-anchor" id="bsk_gfblcv_add_email_domain_name_save_anchor_ID" style="margin-left:20px;">Save</a>
                    <br />
                    <label class="bsk-gfblcv-admin-label">&nbsp;</label>
                    <span style="display:inline-block;font-style: italic;">eg: *@gmail.com</span>
                </p>
                <?php } // end of if( $list_view == 'emaillist' ) ?>
                <div id="bsk_gfblcv_add_items_container_ID">
                    <p style="margin-top:20px;" id="bsk_gfblcv_add_email_list_item_input_container_ID">
                        <label class="bsk-gfblcv-admin-label">Add <?php echo $item_label; ?> by input:</label> 
                        <input type="text" class="bsk-gfblcv-add-item-input add-item-input-for-<?php echo $list_view; ?>" name="bsk_gfblcv_add_item_by_input_name" id="bsk_gfblcv_add_item_by_input_name_ID" maxlength="512"/>
                        <a class="bsk-gfblcv-action-anchor" id="bsk_gfblcv_add_item_by_input_save_anchor_ID" style="margin-left:20px;">Save</a>
                    </p>
                    <?php if( $list_view == 'blacklist' || $list_view == 'whitelist' ){ ?>
                        <p><label class="bsk-gfblcv-admin-label">&nbsp;</label>&nbsp;Pro version supports <a href="https://www.bannersky.com/document/gravity-forms-blacklist-documentation/special-tags-for-item-keyword/" target="_blank">special tags for item / keyword</a>.</p>
                    <?php } ?>
                    <?php if( $list_view == 'iplist' ){ ?>
                        <p><label class="bsk-gfblcv-admin-label">&nbsp;</label>&nbsp;support IP ranges such as: 45.91.94.* and 45.91.94.1 - 45.91.94.123</p>
                    <?php } ?>
                    <p>
                        <label class="bsk-gfblcv-admin-label">Add <?php echo $item_label; ?> by CSV:</label> 
                        <input type="file" name="bsk_gfblcv_add_item_by_csv" id="bsk_gfblcv_add_item_by_csv_ID" />
                        <a class="bsk-gfblcv-action-anchor" id="bsk_gfblcv_add_item_by_csv_save_anchor_ID" style="margin-left:20px;">Upload</a>
                    </p>
                    <div class="bsk-gfblcv-tips-box" style="width: 100%; display: none;">
                        <p>This feature only supported in Pro version</p>
                        <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
                    </div>
                    <p>
                        <label class="bsk-gfblcv-admin-label">&nbsp;</label>
                        <label>
                            <input type="checkbox" name="bsk_gfblcv_add_item_by_csv_skip_existing" id="bsk_gfblcv_add_item_by_csv_skip_existing_ID" value="YES" /> Skip duplicate <?php echo $item_label; ?>s
                        </label>
                    </p>
                    <p>
                        <label class="bsk-gfblcv-admin-label">&nbsp;</label>
                        <?php
                        $template_url = BSK_GFBLCV_FREE_URL.'assets/bsk-blacklist-tmpl.csv.zip';
                        ?>
                        <span style="font-style:italic;">In CSV file, the first column of every line will be take as a <?php echo $item_label; ?>, download <a href="<?php echo $template_url; ?>">template </a>here.</span>
                    </p>
                    <?php if( $list_view == 'invitlist' ){ ?>
                    <p>&nbsp;</p>
                    <div class="bsk-gfblcv-tips-box">
                        <p>This feature requires ask a <span style="font-weight: bold;">CREATOR</span>( or above ) license for Pro version</p>
                        <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
                    </div>
                    <h4>Generate <?php echo $item_label; ?>s &amp; send to user / email:</h4>
                    <p id="bsk_gfblcv_generate_item_only_p_ID">
                        <label class="bsk-gfblcv-admin-label">Only generate codes</label>
                        <span class="bsk-gfblcv-item-operation-column-2">
                            <label class="bsk-gfblcv-admin-label">How many codes to generate?</label>
                            <input type="number" name="bsk_gfblcv_generate_item_count" id="bsk_gfblcv_generate_item_count_ID" min="0" max="1000" step="10" size="4" length="4" />
                        </span>
                        <a class="bsk-gfblcv-action-anchor" id="bsk_gfblcv_generate_item_button_ID" style="margin-left:20px;">Generate</a>
                        <span class="bsk-gfblcv-generate-itme-only-loader" style="margin-left: 20px; display: none;"><?php echo BSK_GFBLCV::$ajax_loader; ?></span>
                    </p>
                    <p id="bsk_gfblcv_generate_item_error_p_ID" style="display: none;" class="bsk-gfblcv-error-message"></p>
                    <p id="bsk_gfblcv_generate_item_by_user_role_p_ID" style="margin-top: 20px;">
                        <label class="bsk-gfblcv-admin-label">Generate by user role &amp; send:</label>
                        <span class="bsk-gfblcv-item-operation-column-2">
                            <select name="bsk_gfblcv_send_item_by_user_role" id="bsk_gfblcv_send_item_by_user_role_ID">
                                <option value="">Select user role...</option>
                                <?php wp_dropdown_roles(); ?>
                            </select>
                        </span>
                        <a class="bsk-gfblcv-action-anchor" id="bsk_gfblcv_send_item_by_user_role_anchor_ID" data-type="user_role" style="margin-left:20px;">Generate &amp; Send</a>
                        <span class="bsk-gfblcv-generate-itme-by-user_role-loader" style="margin-left: 20px; display: none;"><?php echo BSK_GFBLCV::$ajax_loader; ?></span>
                    </p>
                    <p><label class="bsk-gfblcv-admin-label"></label><span style="font-style:italic;">Each user in the role will receive one code. </span></p>
                    <p id="bsk_gfblcv_generate_item_by_user_role_msg_p_ID" style="display: none;" class=""></p>
                    <p id="bsk_gfblcv_generate_item_by_email_list_p_ID" style="margin-top: 20px;">
                        <label class="bsk-gfblcv-admin-label">Generate by email list &amp; send:</label>
                        <span class="bsk-gfblcv-item-operation-column-2">
                            <select name="bsk_gfblcv_send_item_by_email_list" id="bsk_gfblcv_send_item_by_email_list_ID">
                                <option value="">Select email list...</option>
                                <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'EMAIL_LIST', '' ); ?>
                            </select>
                        </span>
                        <a class="bsk-gfblcv-action-anchor" id="bsk_gfblcv_send_item_by_email_list_anchor_ID" data-type="email_list" style="margin-left:20px;">Generate by email list &amp; Send</a>
                        <span class="bsk-gfblcv-generate-itme-by-email_list-loader" style="margin-left: 20px; display: none;"><?php echo BSK_GFBLCV::$ajax_loader; ?></span>
                    </p>
                    <p><label class="bsk-gfblcv-admin-label"></label><span style="font-style:italic;">Each email in the list will receive one code. *@domain.com will be skipped.</span></p>
                    <p id="bsk_gfblcv_generate_item_by_email_list_msg_p_ID" style="display: none;" class=""></p>
                    
                    <?php $sending_invitation_code_settings_page = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['settings'].'&target=sending-invitaiton-code' ); ?>
                    <p id="bsk_gfblcv_generate_item_email_settings_p_ID" style="margin-top: 20px;">
                        <label class="bsk-gfblcv-admin-label">Invitation code email template &amp; other settings:</label>
                        <span><a href="<?php echo $sending_invitation_code_settings_page; ?>">Click here to set >></a></span>
                    </p>
                    <?php } ?>
                    <p style="margin-top: 20px;">&nbsp;</p>
                </div>
                <div id="bsk_gfblcv_items_list_container_ID">
                <?php
                $_bsk_gfblcv_OBJ_items = new BSK_GFBLCV_Dashboard_Items( $list_id, $list_type );
                
                //Fetch, prepare, sort, and filter our data...
                $_bsk_gfblcv_OBJ_items->prepare_items();
                $_bsk_gfblcv_OBJ_items->search_box( 'search', 'bsk-gfblcv-items-serch' );
				$_bsk_gfblcv_OBJ_items->views();
				$_bsk_gfblcv_OBJ_items->display();
				
				$this->show_export_as_csv_form( $list_id );
                ?>
                </div>
                <input type="hidden" name="bsk_gfblcv_list_id" value="<?php echo $list_id; ?>" />
                <input type="hidden" name="bsk_gfblcv_action" id="bsk_gfblcv_action_ID" value="" />
                <input type="hidden" name="bsk_gfblcv_item_id" id="bsk_gfblcv_item_id_ID" value="0" />
                <input type="hidden" name="bsk_gfblcv_items_list_type" id="bsk_gfblcv_items_list_type_ID" value="<?php echo $list_type; ?>" />
                <?php wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_gfblcv_item_save_oper_nonce' ); ?>
                </form>
            </div>
            <?php } //end of list_id > 0 ?>
        </div>
        <?php
	}
	
	function show_export_as_csv_form( $list_id ) {
		if( $list_id < 1 ){
			return;
		}
		global $wpdb;
		
        $items_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_items_tbl_name;
		$sql = 'SELECT COUNT(*) FROM `'.$items_table.'` AS i WHERE i.`list_id` = %d';
		$sql = $wpdb->prepare( $sql, $list_id );
		if( $wpdb->get_var( $sql ) < 1 ){
			return;
		}
	?>
    <div class="bsk-gfblcv-admin-export-items-as-csv-div" style="margin-top:40px;">
        <h3>Items Export</h3>
        <p>
        	Click the export button below to download all items as a CSV file.
            <a class="bsk-gfblcv-action-anchor" id="bsk_gfblcv_export_items_as_CSV_anchor_ID">Export</a>
        </p>
        <div class="bsk-gfblcv-tips-box" style="width: 100%; display: none;">
            <p>This feature only supported in Pro version</p>
            <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
        </div>
        <?php wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_gfblcv_items_export_nonce', true ); ?>
    </div>
	<?php
	}
	
	function bsk_gfblcv_save_list_fun( $data ){
		global $wpdb;

		//check nonce field
		if ( !wp_verify_nonce( $data['bsk_gfblcv_list_save_oper_nonce'], plugin_basename( __FILE__ ) ) ){
			die( 'Security check!' );
			return;
		}

		if ( !isset($data['bsk_gfblcv_list_id']) ){
			return;
		}

        $list_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_list_tbl_name;

        $id = intval( sanitize_text_field( $data['bsk_gfblcv_list_id'] ) );
		$name = sanitize_text_field( $data['bsk_gfblcv_list_name'] );
        $name = wp_unslash( $name );
        
		$list_type = sanitize_text_field( $data['bsk_gfblcv_list_type'] );
        $list_check_way = 'ANY';
		$date = date( 'Y-m-d 00:00:00', current_time('timestamp') );
        
        $page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['base'];
		if( $list_type == 'WHITE_LIST' ){
			$page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['whitelist'];
		}else if( $list_type == 'EMAIL_LIST' ){
			$page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['emailist'];
		}else if( $list_type == 'IP_LIST' ){
			$page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['iplist'];
		}else if( $list_type == 'INVIT_LIST' ){
            $page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['invitlist'];
		}
        
        $data_to_update = array( 
                                    'list_name' => $name, 
                                    'check_way' => $list_check_way, 
                                    'date' => $date, 
                                    'list_type' => $list_type 
                               );
        
        $countries_code = '';
        $api_server = '';
        $api_key = '';
        $list_check_way = '';
        
        $redirect_to_params = array( 'view' => 'edit', 'list_save' => 'succ' );
		if ( $id > 0 ){
            unset( $data_to_update['list_type'] );
			$wpdb->update( 
                           $list_table, 
                           $data_to_update,
                           array( 'id' => $id )
                         );
		}else if( $id == -1 ){
            //query
            $sql = 'SELECT COUNT(*) FROM `'.$list_table.'` WHERE `list_type` = %s';
            $sql = $wpdb->prepare( $sql, $list_type );
            if ( $wpdb->get_var( $sql ) < 50 ) {
                //insert
                $wpdb->insert( 
                                $list_table, 
                                $data_to_update
                             );
                $id = $wpdb->insert_id;
            } else {
                $redirect_to_params['view'] = '';
                $redirect_to_params['list_save'] = 'maxlist';
            }
		}
        $redirect_to_params['id'] = $id;
        $redirect_to = add_query_arg( $redirect_to_params, admin_url( 'admin.php?page='.$page_slug ) );
        
        wp_redirect( $redirect_to );
		exit;
	}
	
	function bsk_gfblcv_save_item_fun( $data ){
		global $wpdb;

		//check nonce field
		if ( !wp_verify_nonce( $data['bsk_gfblcv_item_save_oper_nonce'], plugin_basename( __FILE__ ) ) ){
			die( 'Security check!' );
			return;
		}

		if ( !isset($data['bsk_gfblcv_list_id']) ){
			return;
		}

        if ( ! class_exists( 'BSKCommon' ) ) {
            require_once( BSK_GFBLCV_FREE_DIR . 'classes/bskcommon/common.php' );
        }

        $list_id = intval( sanitize_text_field( $data['bsk_gfblcv_list_id'] ) );
		$value = BSKCommon::sanitize_text_field( $data['bsk_gfblcv_add_item_by_input_name'], true );
		$list_type = sanitize_text_field( $data['bsk_gfblcv_items_list_type'] );
		
		$value = wp_unslash($value);
        if( $list_type == 'IP_LIST' || $list_type == 'EMAIL_LIST' ){
            $value = trim( $value );
        }
		
		if( isset($data['bsk_gfblcv_add_email_domain_name_checkbox']) && 
            $data['bsk_gfblcv_add_email_domain_name_checkbox'] == 'YES' ){
            
			$value = sanitize_text_field( $data['bsk_gfblcv_email_domain_name'] );
		}
		
        $items_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_items_tbl_name;
        $page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['base'];
		if( $list_type == 'WHITE_LIST' ){
            $page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['whitelist'];
		}else if( $list_type == 'EMAIL_LIST' ){
            $page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['emailist'];
		}else if( $list_type == 'IP_LIST' ){
            $page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['iplist'];
		}else if( $list_type == 'INVIT_LIST' ){
			$page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['invitlist'];
		}

        
        $redirect_to_params = array( 'view' => 'edit', 'id' => $list_id, 'item_action' => 'save_succ' );
        //query
        $sql = 'SELECT COUNT(*) FROM `'.$items_table.'` WHERE `list_id` = %d';
        $sql = $wpdb->prepare( $sql, $list_id );
        if ( $wpdb->get_var( $sql ) > 100 ) {
            $redirect_to_params['item_action'] = 'max_error';
        } else {
            //insert
            $wpdb->insert( $items_table, array( 'list_id' => $list_id, 'value' => $value ) );
            
        }
        $redirect_to = add_query_arg( $redirect_to_params, admin_url( 'admin.php?page='.$page_slug ) );
        
        wp_redirect( $redirect_to );
        exit;
	}
	
	function bsk_gfblcv_delete_item_fun( $data ){
		global $wpdb;

		//check nonce field
		if ( !wp_verify_nonce( $data['bsk_gfblcv_item_save_oper_nonce'], plugin_basename( __FILE__ ) ) ){
			die( 'Security check!' );
			return;
		}

		if ( !isset($data['bsk_gfblcv_item_id']) ){
			return;
		}
        $items_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_items_tbl_name;
        
		$list_id = $data['bsk_gfblcv_list_id'];
		$id = $data['bsk_gfblcv_item_id'] + 0;
		$list_type = $data['bsk_gfblcv_items_list_type'];
		
		$sql = 'DELETE FROM `'.$items_table.'` WHERE `id` = %d';
		$sql = $wpdb->prepare( $sql, $id );
		$wpdb->query( $sql );
        
        
        //delete hits data
        $hits_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_hits_tbl_name;
        $sql = 'DELETE FROM `'.$hits_table.'` WHERE `list_id` = %d AND `item_id` = %d';
        $sql = $wpdb->prepare( $sql, $list_id, $id );
        $wpdb->query( $sql );
		
		$page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['base'];
		if( $list_type == 'WHITE_LIST' ){
            $page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['whitelist'];
		}else if( $list_type == 'EMAIL_LIST' ){
            $page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['emailist'];
		}else if( $list_type == 'IP_LIST' ){
            $page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['iplist'];
		}else if( $list_type == 'INVIT_LIST' ){
			$page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['invitlist'];
		}
		$redirect_to = add_query_arg( 
                                        array('view' => 'edit', 'id' => $list_id, 'item_action' => 'del_succ'), 
                                        admin_url( 'admin.php?page='.$page_slug ) 
                                    );
		wp_redirect( $redirect_to );
		exit;
	}
	
	function bsk_gfblcv_delete_list_by_id_fun( $data ){
		//check nonce field
		if ( !wp_verify_nonce( $data['_wpnonce'], 'bsk_gfblcv_list_oper_nonce' ) ){
			die( 'Security check!' );
			return;
		}
		
		$list_id = $data['bsk_gfblcv_list_id'];
		if( $list_id < 1 ){
			add_action( 'admin_notices', array($this, 'bsk_gfblcv_delete_list_invlaid_id_fun') );
		}
		
		global $wpdb;
		
        $list_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_list_tbl_name;
        $items_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_items_tbl_name;
        
		//delete items
		$items_count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM `'.$items_table.'` WHERE `list_id` = %d', $list_id) );
		if( $items_count > 0 ){
			$sql = 'DELETE FROM `'.$items_table.'` WHERE `list_id` = %d';
			$sql = $wpdb->prepare( $sql, $list_id );
			$wpdb->query( $sql );
		}
		
		//delete list
		$sql = 'DELETE FROM `'.$list_table.'` WHERE `id` = %d';
		$sql = $wpdb->prepare( $sql, $list_id );
		$wpdb->query( $sql );
        
        //delete hits data
        $hits_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_hits_tbl_name;
        $sql = 'DELETE FROM `'.$hits_table.'` WHERE `list_id` = %d';
        $sql = $wpdb->prepare( $sql, $list_id );
        $wpdb->query( $sql );
		
		add_action( 'admin_notices', array($this, 'bsk_gfblcv_delete_list_successfully_fun') );
	}
	
	function bsk_gfblcv_delete_list_invlaid_id_fun(){
		?>
        <div class="notice notice-error is-dismissible">
            <p>Delete list failed: Invalid list id</p>
        </div>
        <?php
	}
	
	function bsk_gfblcv_delete_list_successfully_fun(){
		?>
        <div class="notice notice-success is-dismissible">
            <p>The list and all items in it have been deleted</p>
        </div>
        <?php
	}
    
    function bsk_gfblcv_max_item_error_notice(){
		?>
        <div class="notice notice-error is-dismissible">
            <p>You have reached the max list amount. </p>
        </div>
        <?php
	}
    
}