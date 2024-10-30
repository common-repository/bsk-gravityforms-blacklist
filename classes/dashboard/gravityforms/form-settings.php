<?php

class BSK_GFBLCV_Dashboard_GForm_Settings {
	
	function __construct() {
		
        if ( BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_is_form_plugin_supported('GF') ) {
            // add a custom menu item to the Form Settings page menu
            add_filter( 'gform_form_settings_menu', array( $this, 'bsk_gfblcv_form_settings_menu_item' ) );
            // handle displaying content for our custom menu when selected
            add_action( 'gform_form_settings_page_bsk_gfblcv_form_settings', array( $this, 'bsk_gfblcv_form_settings_page' ) );
        }
	}
	
	
    function bsk_gfblcv_form_settings_menu_item( $menu_items ) {

        $menu_items[] = array(
            'name' => 'bsk_gfblcv_form_settings',
            'label' => __( 'BSK Blacklist' )
            );

        return $menu_items;
    }


    function bsk_gfblcv_form_settings_page() {

        GFFormSettings::page_header();
        
        $form_id = absint( rgget( 'id' ) );
        
        if ( isset( $_POST['save_bsk_gfblcv_settings'])) {
			$this->process_form_settings( $form_id );
		}
        
        $this->display_form_settings( $form_id );

        GFFormSettings::page_footer();

    }
    
    function display_form_settings( $form_id ) {

		$form = GFAPI::get_form( $form_id );
        
        //plugin gloabla settings
        $settings_data = get_option( BSK_GFBLCV_Dashboard::$_plugin_settings_option, false );
        $settings_data['save_blocked_entry'] = 'NO';
        $settings_data['notify_blocked'] = 'NO';

        $global_save_blocked_entry = 'NO';
        $global_notify_blocked = 'NO';
        $global_notify_details = false;
        $global_notify_send_to = '';
        if( $settings_data && is_array( $settings_data ) && count( $settings_data ) > 0 ){
            if( isset( $settings_data['save_blocked_entry'] ) ){
                $global_save_blocked_entry = $settings_data['save_blocked_entry'];
            }
            if( isset( $settings_data['notify_blocked'] ) ){
                $global_notify_blocked = $settings_data['notify_blocked'];
            }
            if( isset( $settings_data['notify_details'] ) ){
                $global_notify_details = $settings_data['notify_details'];
                if( isset( $global_notify_details['send_to'] ) && $global_notify_details['send_to'] ){
                    $global_notify_send_to = $global_notify_details['send_to'];
                }
            }
        }

        //form settings
		$bsk_gfblcv_form_settings = rgar( $form, 'bsk_gfblcv_form_settings' );
        
        $enable = true;
        $action_when_hit = array( 'BLOCK' );
        $notification_to_skip = array();
        $confirmation_to_go = '';
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

        if( $bsk_gfblcv_form_settings && is_array( $bsk_gfblcv_form_settings ) && count( $bsk_gfblcv_form_settings ) > 0 ){
            $enable = $bsk_gfblcv_form_settings['enable'];
            $action_when_hit = $bsk_gfblcv_form_settings['actions'];
            $notification_to_skip = $bsk_gfblcv_form_settings['notification_to_skip'];
            $confirmation_to_go = $bsk_gfblcv_form_settings['confirmation_to_go'];
            $save_blocked_data = $bsk_gfblcv_form_settings['save_blocked_data'];
            $notify_administrators = $bsk_gfblcv_form_settings['notify_administrators'];
            $notify_send_to = $bsk_gfblcv_form_settings['notify_send_to'];
            $delete_entry = $bsk_gfblcv_form_settings['delete_entry'];
            $blacklist_message = $bsk_gfblcv_form_settings['blacklist_message'];
            $whitelist_message = $bsk_gfblcv_form_settings['whitelist_message'];
            $emaillist_message = $bsk_gfblcv_form_settings['emaillist_message'];
            $iplist_message = $bsk_gfblcv_form_settings['iplist_message'];
        }else{
            //compatible with old savd data format
            if( isset( $form['block_or_skip_notification'] ) && $form['block_or_skip_notification'] == 'SKIP' ) {
                $action_when_hit = array( 'SKIP' ); 
            }
            if( isset( $form['notifications_to_skip'] ) ){
                $notification_to_skip = explode( ',', $form['notifications_to_skip'] );
            }
            if( isset( $form['blacklist_validation_message'] ) ) {
                $blacklist_message = $form['blacklist_validation_message'];
            }
            if( isset( $form['whitelist_validation_message'] ) ) {
                $whitelist_message = $form['whitelist_validation_message']; 
            }
            if( isset( $form['emaillist_validation_message'] ) ) {
                $emaillist_message = $form['emaillist_validation_message']; 
            }
            if( isset( $form['iplist_validation_message'] ) ) {
                $iplist_message = $form['iplist_validation_message']; 
            }
        }
        
        if( trim( $notify_send_to == '' ) && $global_notify_send_to ){
            $notify_send_to = $global_notify_send_to;
        }
        
        $blacklist_message = wp_specialchars_decode( $blacklist_message );
        $whitelist_message = wp_specialchars_decode( $whitelist_message );
        $emaillist_message = wp_specialchars_decode( $emaillist_message );
        $iplist_message = wp_specialchars_decode( $iplist_message );
        
        
        //process display
        $form_settings_actions_container_display = 'block';
        $form_settings_blocked_data_container_display = 'none';
        $form_settings_entry_container_display = 'none';
        $form_settings_error_messages_container_display = 'none';
        
        $form_settings_skip_notifications_container_display = 'none';
        $form_settings_confirmations_to_go_container_display = 'none';
        
        if( in_array( 'BLOCK', $action_when_hit ) ){
            $form_settings_blocked_data_container_display = 'block';
            $form_settings_error_messages_container_display = 'block';
            
            $form_settings_entry_container_display = 'none';
        }
        
        if( in_array( 'SKIP', $action_when_hit ) ){
            $form_settings_blocked_data_container_display = 'none';
            $form_settings_error_messages_container_display = 'none';
            
            $form_settings_entry_container_display = 'block';
            $form_settings_skip_notifications_container_display = 'table-row';
        }
        
        if( in_array( 'CONFIRMATION', $action_when_hit ) ){
            $form_settings_blocked_data_container_display = 'none';
            $form_settings_error_messages_container_display = 'none';
            
            $form_settings_entry_container_display = 'block';
            $form_settings_confirmations_to_go_container_display = 'table-row';
        }
        
        if( !in_array( 'BLOCK', $action_when_hit ) && !in_array( 'SKIP', $action_when_hit ) && !in_array( 'CONFIRMATION', $action_when_hit ) ){
            //even no actions, still support delete entry
            $form_settings_entry_container_display = 'block';
        }

        if( !$enable ){

            $form_settings_actions_container_display = 'none';
            $form_settings_blocked_data_container_display = 'none';
            $form_settings_entry_container_display = 'none';
            $form_settings_error_messages_container_display = 'none';
        }
        
		$action_url = admin_url( sprintf( 'admin.php?page=gf_edit_forms&view=settings&subview=bsk_gfblcv_form_settings&id=%d', $form_id ) );
		?>
		<h3><span><i class="fa fa-lock"></i> <?php esc_html_e( 'Blacklist / White list / Email list / IP list', 'bsk_gfblcv' ); ?></h3>
		<div class="gform_panel gform_panel_form_settings bsk-gfblcv-form-settings-container" id="bsk_gfblcv_settings">
			<form action="<?php esc_url( $action_url ); ?>" method="POST">
				<?php wp_nonce_field( 'gravityforms_bsk_gfblcv_settings' ); ?>
                <div class="bsk-gfblcv-form-settings-enable-disable-container">
                    <h4><?php esc_html_e( 'General settings', 'bsk_gfblcv' ); ?></h4>
                    <table class="gforms_form_settings" cellspacing="0" cellpadding="0">
                        <?php
                        $enable_checked = $enable ? ' checked' : '';
                        $disable_checked = $enable ? '' : ' checked';
                        ?>
                        <tr>
                            <th>&nbsp;</th>
                            <td>
                                <label>
                                    <input type="radio" value="ENABLE" name="bsk_gfblcv_form_settings_enable" class="bsk-gfblcv-form-settings-enable-raido"<?php echo $enable_checked; ?>/> Enable for this form
                                </label>
                                <label style="margin-left:20px;">
                                    <input type="radio" value="DISABLE" name="bsk_gfblcv_form_settings_enable" class="bsk-gfblcv-form-settings-enable-raido"<?php echo $disable_checked; ?>/> Disable for this form
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="bsk-gfblcv-form-settings-actions-container" style="display: <?php echo $form_settings_actions_container_display; ?>">
                    <h4><?php esc_html_e( 'Actions', 'bsk_gfblcv' ); ?></h4>
                    <table class="gforms_form_settings" cellspacing="0" cellpadding="0">
                        <?php
                        $block_checked = in_array( 'BLOCK', $action_when_hit ) ? ' checked' : '';
                        $skip_checked = in_array( 'SKIP', $action_when_hit ) ? ' checked' : '';
                        $confirmation_checked = in_array( 'CONFIRMATION', $action_when_hit ) ? ' checked' : '';
                        ?>
                        <tr class="bsk-gfblcv-form-settings-option-tr">
                            <th>&nbsp;</th>
                            <td>
                                <label>
                                    <input type="checkbox" value="BLOCK" name="bsk_gfblcv_form_settings_actions[]"<?php echo $block_checked ?> class="bsk-gfblcv-form-settings-action-block-chk" /> Block form submitting
                                </label>
                                <label style="margin-left:20px;">
                                    <input type="checkbox" value="SKIP" name="bsk_gfblcv_form_settings_actions[]"<?php echo $skip_checked ?> class="bsk-gfblcv-form-settings-action-skip-chk" /> Skip notifications
                                </label>
                                <label style="margin-left:20px;">
                                    <input type="checkbox" value="CONFIRMATION" name="bsk_gfblcv_form_settings_actions[]"<?php echo $confirmation_checked ?> class="bsk-gfblcv-form-settings-action-confirmation-chk" /> Go specific confirmation
                                </label>
                            </td>
                        </tr>
                        <?php
                        if( isset($form['notifications']) && is_array($form['notifications']) && count($form['notifications']) > 0 ){
                            $html = '
                                <tr class="bsk-gfblcv-form-settings-option-tr bsk-gfblcv-notificaitons-to-skip" style="display: '.$form_settings_skip_notifications_container_display.'">
                                    <th>&nbsp;</th>
                                    <td>
                                        <p>Check Notifications to skip:</p>
                                        <div class="bsk-gfblcv-tips-box">
                                            <p>This feature only supported in Pro version. </p>
                                            <p>To buy a license, please <a href="'.BSK_GFBLCV::$_plugin_home_url.'" target="_blank">click here >></a></p>
                                        </div>
                                        <ul>';
                            foreach( $form['notifications'] as $notification_id => $notification_setting ){
                                if( isset($notification_setting['isActive']) && !$notification_setting['isActive'] ){
                                    continue;
                                }
                                
                                $checked_str = '';
                                if( is_array( $notification_to_skip ) &&
                                    in_array( $notification_id, $notification_to_skip ) ){

                                    $checked_str = ' checked';
                                }
                                $html .= '<li>
                                            <label>
                                            <input type="checkbox" name="bsk_gfblcv_notification_to_skip[]" value="'.$notification_id.'"'.$checked_str.' /> '.$notification_setting['name'].'
                                            </label>
                                          </li>';

                            }
                            $html .= '</ul>
                                    </td>
                                </tr>';
                            echo $html;
                        }

                        if( isset($form['confirmations']) && is_array($form['confirmations']) && count($form['confirmations']) > 0 ){
                            $html = '
                                <tr class="bsk-gfblcv-form-settings-option-tr bsk-gfblcv-confirmations-to-go" style="display: '.$form_settings_confirmations_to_go_container_display.'">
                                    <th>&nbsp;</th>
                                    <td>
                                        <p>Select Confirmation to go</p>';
                            
                            $html .= '<div class="bsk-gfblcv-tips-box">
                                            <p>This feature requires a <span style="font-weight: bold;">CREATOR</span>( or above ) license in Pro version. </p>
                                            <p>To buy a license, please <a href="'.BSK_GFBLCV::$_plugin_home_url.'" target="_blank">click here >></a></p>
                                      </div>';
                            
                            $html .=   '<ul>';

                            foreach( $form['confirmations'] as $confirmation_id => $confirmation_setting ){
                                
                                if( isset( $confirmation_setting['isActive'] ) && !$confirmation_setting['isActive'] ){
                                    continue;
                                }
                                $checked_str = '';
                                if( $confirmation_id == $confirmation_to_go ){
                                    $checked_str = ' checked';
                                }
                                $html .= '<li>
                                            <label>
                                                <input type="radio" name="bsk_gfblcv_confirmation_to_go" value="'.$confirmation_id.'"'.$checked_str.' /> '.$confirmation_setting['name'].'
                                            </label>
                                          </li>';

                            }
                            $html .= '</ul>
                                    </td>
                                </tr>';
                            echo $html;
                        }
                        ?>
                        <?php
                        $save_yes_checked = $save_blocked_data == 'YES' ? ' checked' : '';
                        $save_no_checked = $save_blocked_data == 'NO' ? ' checked' : '';
                        ?>
                    </table>
                </div>
                <?php
                $blocked_form_data_view_link = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['blocked_data'] );
                $blocked_form_data_global_settings = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['settings'] );
                ?>
                <div class="bsk-gfblcv-form-settings-blocked-data-container" style="display: <?php echo $form_settings_blocked_data_container_display; ?>">
                    <h4><?php esc_html_e( 'Blocked form data', 'bsk_gfblcv' ); ?></h4>
                    <table class="gforms_form_settings" cellspacing="0" cellpadding="0">
                        <tr class="bsk-gfblcv-form-settings-option-tr bsk-gfblcv-form-settings-save-blocked-data">
                            <th>&nbsp;</th>
                            <td>
                                <span class="bsk-gfblcv-form-settings-label">Save blocked form data:</span>
                                <?php
                                if( $global_save_blocked_entry == 'NO' ){
                                ?>
                                <span><a href="<?php echo $blocked_form_data_global_settings; ?>">Blocked Form Data Global Settings</a></span>
                                <input type="hidden" value="<?php echo $save_blocked_data; ?>" name="bsk_gfblcv_save_blocked_data" />
                                <?php
                                }else{
                                ?>
                                <label>
                                    <input type="radio" value="YES" name="bsk_gfblcv_save_blocked_data"<?php echo $save_yes_checked ?> /> Yes
                                </label>
                                <label style="margin-left:20px;">
                                    <input type="radio" value="NO" name="bsk_gfblcv_save_blocked_data"<?php echo $save_no_checked ?> /> No
                                </label>
                                <span class="bsk-gfblcv-form-settings-actions-desc" style="display: inline-block; margin-left: 50px;">Blocked form data listed <a href="<?php echo $blocked_form_data_view_link; ?>">here >></span>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                        $notify_yes_checked = $notify_administrators == 'YES' ? ' checked' : '';
                        $notify_no_checked = $notify_administrators == 'NO' ? ' checked' : '';
                        ?>
                        <tr class="bsk-gfblcv-form-settings-option-tr"><th>&nbsp;</th></tr>
                        <tr class="bsk-gfblcv-form-settings-option-tr bsk-gfblcv-form-settings-notify-administrators">
                            <th>&nbsp;</th>
                            <td>
                                <span class="bsk-gfblcv-form-settings-label">Notify administrators: </span>
                                <?php
                                if( $global_notify_blocked == 'NO' ){
                                ?>
                                <span><a href="<?php echo $blocked_form_data_global_settings; ?>">Notify Administrators( emails ) Global Settings</a></span>
                                <input type="hidden" value="<?php echo $notify_administrators; ?>" name="bsk_gfblcv_notify_administrators" />
                                <?php
                                }else{
                                ?>
                                <label>
                                    <input type="radio" value="YES" name="bsk_gfblcv_notify_administrators"<?php echo $notify_yes_checked ?> class="bsk-gfblcv-notifiy-administrators-raido" /> Yes
                                </label>
                                <label style="margin-left:20px;">
                                    <input type="radio" value="NO" name="bsk_gfblcv_notify_administrators"<?php echo $notify_no_checked ?> class="bsk-gfblcv-notifiy-administrators-raido" /> No
                                </label>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                        $notify_send_to_display = $notify_administrators == 'YES' ? 'table-row' : 'none';
                        if( !$enable || $global_notify_blocked == 'NO' ){
                            $notify_send_to_display = 'none';
                        }
                        $set_notify_mail_template_link = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['settings'] );
                        ?>
                        <tr class="bsk-gfblcv-form-settings-option-tr bsk-gfblcv-form-settings-notify-send-to" style="display: <?php echo $notify_send_to_display; ?>">
                            <th>&nbsp;</th>
                            <td>
                                <span class="bsk-gfblcv-form-settings-label">Send to: </span>
                                <input type="text" value="<?php echo $notify_send_to; ?>" name="bsk_gfblcv_notify_send_to" class="bsk-gfblvc-form-settings-input-width" />
                                <br />
                                <span class="bsk-gfblcv-form-settings-label">&nbsp;</span>
                                <span class="bsk-gfblcv-form-settings-actions-desc" style="display: inline-block;">user comma( , ) to separate multiple mails</span>
                                <br />
                                <span class="bsk-gfblcv-form-settings-label">&nbsp;</span>
                                <span class="bsk-gfblcv-form-settings-actions-desc" style="display: inline-block;">Set notify mail template <a href="<?php echo $set_notify_mail_template_link; ?>">here >></span>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
                $delete_entry_yes_checked = $delete_entry == 'YES' ? ' checked' : '';
                $delete_entry_no_checked = $delete_entry == 'NO' ? ' checked' : '';
                ?>
                <div class="bsk-gfblcv-form-settings-entry-container" style="display: <?php echo $form_settings_entry_container_display; ?>">
                    <h4><?php esc_html_e( 'Entry', 'bsk_gfblcv' ); ?></h4>
                    <table class="gforms_form_settings" cellspacing="0" cellpadding="0">
                        <tr class="bsk-gfblcv-form-settings-option-tr bsk-gfblcv-form-settings-delete-entry-tr">
                            <th>&nbsp;</th>
                            <td>
                                <span class="bsk-gfblcv-form-settings-label">Delete entry: </span>
                                <label>
                                    <input type="radio" value="YES" name="bsk_gfblcv_delete_entry"<?php echo $delete_entry_yes_checked ?> class="bsk-gfblcv-form-settings-delete-entry-radio" /> Yes
                                </label>
                                <label style="margin-left:20px;">
                                    <input type="radio" value="NO" name="bsk_gfblcv_delete_entry"<?php echo $delete_entry_no_checked ?> class="bsk-gfblcv-form-settings-delete-entry-radio" /> No
                                </label>
                                <p>&nbsp;</p>
                                <div class="bsk-gfblcv-tips-box" style="display: none;">
                                    <p>This feature only supported in Pro version. </p>
                                    <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="bsk-gfblcv-form-settings-error-messages-container" style="display: <?php echo $form_settings_error_messages_container_display; ?>">
                    <h4><?php esc_html_e( 'Error messages', 'bsk_gfblcv' ); ?></h4>
                    <table class="gforms_form_settings" cellspacing="0" cellpadding="0">
                        <tr class="bsk-gfblcv-form-settings-option-tr">
                            <th>&nbsp;</th>
                            <td>
                                <span class="bsk-gfblcv-form-settings-error-message-label">Blacklist: </span>
                                <input type="text" name="bsk_gfblcv_blacklist_message" class="bsk-gfblvc-form-settings-input-width" value="<?php echo esc_attr( $blacklist_message ); ?>" />
                            </td>
                        </tr>
                        <tr class="bsk-gfblcv-form-settings-option-tr">
                            <th></th>
                            <td>
                                <span class="bsk-gfblcv-form-settings-error-message-label">White list: </span>
                                <input type="text" name="bsk_gfblcv_whitelist_message" class="bsk-gfblvc-form-settings-input-width" value="<?php echo esc_attr( $whitelist_message ); ?>" />
                            </td>
                        </tr>
                        <tr class="bsk-gfblcv-form-settings-option-tr">
                            <th>&nbsp;</th>
                            <td>
                                <span class="bsk-gfblcv-form-settings-error-message-label">Email list: </span>
                                <input type="text" name="bsk_gfblcv_emaillist_message" class="bsk-gfblvc-form-settings-input-width" value="<?php echo esc_attr( $emaillist_message ); ?>" />
                            </td>
                        </tr>
                        <tr class="bsk-gfblcv-form-settings-option-tr">
                            <th>&nbsp;</th>
                            <td>
                                <span class="bsk-gfblcv-form-settings-error-message-label">IP list: </span>
                                <input type="text" name="bsk_gfblcv_iplist_message" class="bsk-gfblvc-form-settings-input-width" value="<?php echo esc_attr( $iplist_message ); ?>" />
                            </td>
                        </tr>
                        <tr class="bsk-gfblcv-form-settings-option-tr">
                            <th>&nbsp;</th>
                            <td>
                                <p><span class="bsk-gfblcv-form-settings-error-message-label">&nbsp;</span>[FIELD_LABEL] will be replaced by field label</p>
                                <p><span class="bsk-gfblcv-form-settings-error-message-label">&nbsp;</span>[FIELD_VALUE] will be replaced with field value</p>
                                <p><span class="bsk-gfblcv-form-settings-error-message-label">&nbsp;</span>[VISITOR_IP] will be replaced with visitor's IP</p>
                            </td>
                        </tr>
                    </table>
                </div>
                <input
                    class="button-primary"
                    type="submit"
                    name="save_bsk_gfblcv_settings"
                    value="<?php esc_attr_e( 'Save', 'gravityforms' ); ?>"
                />
			</form>
		</div>
		<?php
	}
    
    function process_form_settings( $form_id ) {
		check_admin_referer( 'gravityforms_bsk_gfblcv_settings' );

		$form = GFAPI::get_form( $form_id );

		if ( ! isset( $form['bsk_gfblcv_form_settings'] ) ) {
			$form['bsk_gfblcv_form_settings'] = array();
		}

        $form_bsk_gfblcv_settings = $form['bsk_gfblcv_form_settings'];
		$form_bsk_gfblcv_settings['enable'] = rgpost( 'bsk_gfblcv_form_settings_enable' ) == 'ENABLE' ? true : false;
        $form_bsk_gfblcv_settings['actions'] = array( 'BLOCK' );;
        $form_bsk_gfblcv_settings['blacklist_message'] = rgpost( 'bsk_gfblcv_blacklist_message' );
        $form_bsk_gfblcv_settings['whitelist_message'] = rgpost( 'bsk_gfblcv_whitelist_message' );
        $form_bsk_gfblcv_settings['emaillist_message'] = rgpost( 'bsk_gfblcv_emaillist_message' );
        $form_bsk_gfblcv_settings['iplist_message'] = rgpost( 'bsk_gfblcv_iplist_message' );
        
        
        $form_bsk_gfblcv_settings['notification_to_skip'] = array();
        $form_bsk_gfblcv_settings['confirmation_to_go'] = '';
        $form_bsk_gfblcv_settings['notify_administrators'] = rgpost( 'bsk_gfblcv_notify_administrators' );
        $form_bsk_gfblcv_settings['save_blocked_data'] = rgpost( 'bsk_gfblcv_save_blocked_data' );
        $form_bsk_gfblcv_settings['delete_entry'] = 'NO';
        
        $invalid_send_to_email = false;
        if( $form_bsk_gfblcv_settings['notify_administrators'] == 'YES' ){
            $notify_send_to_str = rgpost( 'bsk_gfblcv_notify_send_to' );
            $notify_send_to_array = explode( ',', $notify_send_to_str );
            foreach( $notify_send_to_array as $key => $val ){
                $val = trim( $val );
                if( !is_email( $val ) ){
                    $invalid_send_to_email = true;
                    unset( $notify_send_to_array[$key] );
                }
                $notify_send_to_array[$key] = $val;
            }
            $form_bsk_gfblcv_settings['notify_send_to'] = implode( ',', $notify_send_to_array );
        }else{
            $form_bsk_gfblcv_settings['notify_send_to'] = rgpost( 'bsk_gfblcv_notify_send_to' );
        }
        
		$form['bsk_gfblcv_form_settings'] = $form_bsk_gfblcv_settings;
        
        //remove old saved data
        unset( $form['block_or_skip_notification'] );
        unset( $form['notifications_to_skip'] );
        unset( $form['blacklist_validation_message'] );
        unset( $form['whitelist_validation_message'] );
        unset( $form['emaillist_validation_message'] );
        unset( $form['iplist_validation_message'] );

		GFAPI::update_form( $form );
		?>
		<div class="updated below-h2" id="after_update_dialog">
			<p>
				<strong><?php _e( 'Blacklist settings updated successfully.', 'gravityforms' ); ?></strong>
			</p>
		</div>
		<?php
        if( $form_bsk_gfblcv_settings['enable'] && $invalid_send_to_email ){
        ?>
        <div class="error below-h2" id="after_update_dialog2" style="padding: 1px 12px;">
			<p>
				<strong><?php _e( 'Invalid email address found for "Send to" field', 'gravityforms' ); ?></strong>
			</p>
		</div>
        <?php
        }
	}
    
}
