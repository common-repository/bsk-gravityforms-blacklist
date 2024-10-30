<?php
class BSK_GFBLCV_Dashboard_CF7 {
    
	public function __construct() {

		if ( BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_is_form_plugin_supported('CF7') ) {
            add_action( 'wpcf7_editor_panels', array( $this, 'bsk_gfblcv_cf7_form_panel' ) );
            add_action( 'wpcf7_after_save', array( $this, 'bsk_gfblcv_cf7_save_form_setting' ) );
        }
        
	}
	
	function bsk_gfblcv_cf7_form_panel( $panels ){
        
        $panels['cf7-blacklist-panel'] = array(
												'title'     => __( 'BSK Blacklist', 'bsk_gfblcv' ),
												'callback'  => array( $this, 'bsk_gfblcv_cf7_form_panel_render' ),
											);
		return $panels;
    }

	function bsk_gfblcv_cf7_form_panel_render( $cf7_post ) {
        
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

		$form_id = $cf7_post->id();

        //form settings
		$bsk_gfblcv_form_settings = get_post_meta( $form_id, BSK_GFBLCV_Dashboard::$_bsk_gfblcv_cf7_form_settings_opt, true );
		if ( ! $bsk_gfblcv_form_settings || ! is_array( $bsk_gfblcv_form_settings ) || count( $bsk_gfblcv_form_settings ) < 1 ) {
			//no saved, to check if convert from CF7 Blacklist plugin
			BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_cf7_convert_cf7blacklist_data( $cf7_post );
			//get again
			$bsk_gfblcv_form_settings = get_post_meta( $form_id, BSK_GFBLCV_Dashboard::$_bsk_gfblcv_cf7_form_settings_opt, true );
		}
	
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
            $emaillist_message = $bsk_gfblcv_form_settings['emaillist_message'];
            $iplist_message = $bsk_gfblcv_form_settings['iplist_message'];
            if( isset($bsk_gfblcv_form_settings['invitlist_message']) && 
                $bsk_gfblcv_form_settings['invitlist_message'] ){
                $invitlist_message = $bsk_gfblcv_form_settings['invitlist_message'];
            }
        }

        if( trim( $notify_send_to == '' ) && $global_notify_send_to ){
            $notify_send_to = $global_notify_send_to;
        }
        
        //process display
        $form_settings_actions_container_display = 'block';
        $form_settings_blocked_data_container_display = 'none';
        $form_settings_entry_container_display = 'none';
        $form_settings_error_messages_container_display = 'none';
        $form_settings_field_mapping_container_display = 'block';
        
        $form_settings_skip_notifications_container_display = 'none';
        $form_settings_confirmations_to_go_container_display = 'none';
        
        if( in_array( 'BLOCK', $action_when_hit ) ){
            $form_settings_blocked_data_container_display = 'block';
            $form_settings_error_messages_container_display = 'block';
            
            $form_settings_entry_container_display = 'none';
        }

        if( !$enable ){

            $form_settings_actions_container_display = 'none';
            $form_settings_blocked_data_container_display = 'none';
            $form_settings_entry_container_display = 'none';
            $form_settings_error_messages_container_display = 'none';
            $form_settings_field_mapping_container_display = 'none';
        }

		?>
		<div class="gform_panel gform_panel_form_settings bsk-gfblcv-form-settings-container" id="bsk_gfblcv_cf7_form_settings">
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
                    ?>
                    <tr class="bsk-gfblcv-form-settings-option-tr">
                        <th>&nbsp;</th>
                        <td>
                            <label>
                                <input type="checkbox" value="BLOCK" name="bsk_gfblcv_form_settings_actions[]"<?php echo $block_checked ?> class="bsk-gfblcv-form-settings-action-block-chk" /> Block form submitting
                            </label>
                            <label style="margin-left:20px;">
                                <input type="checkbox" value="SKIP" name="bsk_gfblcv_form_settings_actions[]"<?php echo $skip_checked ?> class="bsk-gfblcv-form-settings-action-skip-chk" /> Skip Mail(s)
                            </label>
                        </td>
                    </tr>
                    <?php
                    $skip_mails_checked_Mail = '';
                    $skip_mails_checked_Mail_2 = '';
                    $skip_mails_checked_Mail_2_display = 'block';
                    if ( is_array( $mails_to_skip ) && count( $mails_to_skip ) > 0 ) {
                        if ( in_array( 'Mail', $mails_to_skip ) ) {
                            $skip_mails_checked_Mail = ' checked';

                            //According to Contat Form 7, if Mail skipeed, then all others will be skipped too.
                            $skip_mails_checked_Mail_2 = ' checked';
                            $skip_mails_checked_Mail_2_display = 'none';
                        } else if ( in_array( 'Mail_2', $mails_to_skip ) ) {
                            $skip_mails_checked_Mail_2 = ' checked';
                        }
                    }
                    ?>
                    <tr class="bsk-gfblcv-form-settings-option-tr bsk-gfblcv-notificaitons-to-skip" style="display: <?php echo $form_settings_skip_notifications_container_display; ?>">
                        <th>&nbsp;</th>
                        <td>
                            <p>Check Mail(s) to skip:</p>
                            <div class="bsk-gfblcv-tips-box">
                                <p>This feature only supported in Pro version. </p>
                                <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
                            </div>
                            <ul>
                                <li>
                                    <label><input type="checkbox" id="cf7_blacklist_skip_main_mail_chk_ID" name="cf7_blacklist_skip_mail" value="YES"<?php echo $skip_mails_checked_Mail; ?> /> Mail</label>
                                </li>
                                <li style="display: <?php echo $skip_mails_checked_Mail_2_display; ?>;" id="cf7_blacklist_skip_mail_2_chk_li_ID">
                                    <label><input type="checkbox" id="cf7_blacklist_skip_mail_2_chk_ID" name="cf7_blacklist_skip_mail_2" value="YES"<?php echo $skip_mails_checked_Mail_2; ?> /> Mail (2)</label>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <?php
                    $save_yes_checked = $save_blocked_data == 'YES' ? ' checked' : '';
                    $save_no_checked = $save_blocked_data == 'NO' ? ' checked' : '';
                    ?>
                </table>
            </div>
            <?php
            $blocked_form_data_view_link = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['blocked_data'] );
            $blocked_form_data_global_settings = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['settings'].'&target=blocked-data' );
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
                            <span class="bsk-gfblcv-form-settings-actions-desc" style="display: inline-block; margin-left: 20px;">Blocked form data listed <a href="<?php echo $blocked_form_data_view_link; ?>">here >></a></span>
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
                    $set_notify_mail_template_link = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['settings'].'&target=blocked-data' );
                    ?>
                    <tr class="bsk-gfblcv-form-settings-option-tr bsk-gfblcv-form-settings-notify-send-to" style="display: <?php echo $notify_send_to_display; ?>">
                        <th>&nbsp;</th>
                        <td>
                            <br />
                            <span class="bsk-gfblcv-form-settings-label">Send to: </span>
                            <input type="text" value="<?php echo $notify_send_to; ?>" name="bsk_gfblcv_notify_send_to" class="bsk-gfblvc-form-settings-input-width" />
                            <br />
                            <span class="bsk-gfblcv-form-settings-label">&nbsp;</span>
                            <span class="bsk-gfblcv-form-settings-actions-desc" style="display: inline-block;">user comma( , ) to separate multiple mails</span>
                            <br />
                            <span class="bsk-gfblcv-form-settings-label">&nbsp;</span>
                            <span class="bsk-gfblcv-form-settings-actions-desc" style="display: inline-block;">Click to set <a href="<?php echo $set_notify_mail_template_link; ?>">notify mail template and other info >></a></span>
                        </td>
                    </tr>
                </table>
            </div>
            <?php
            $delete_entry_yes_checked = $delete_entry == 'YES' ? ' checked' : '';
            $delete_entry_no_checked = $delete_entry == 'NO' ? ' checked' : '';

            if ( 0 ) {
            ?>
            <div class="bsk-gfblcv-form-settings-entry-container" style="display: <?php echo $form_settings_entry_container_display; ?>;">
                <h4><?php esc_html_e( 'Entry', 'bsk_gfblcv' ); ?></h4>
                <table class="gforms_form_settings" cellspacing="0" cellpadding="0">
                    <tr class="bsk-gfblcv-form-settings-option-tr bsk-gfblcv-form-settings-delete-entry-tr">
                        <th>&nbsp;</th>
                        <td>
                            <span class="bsk-gfblcv-form-settings-label">Delete entry: </span>
                            <label>
                                <input type="radio" value="YES" name="bsk_gfblcv_delete_entry"<?php echo $delete_entry_yes_checked ?> /> Yes
                            </label>
                            <label style="margin-left:20px;">
                                <input type="radio" value="NO" name="bsk_gfblcv_delete_entry"<?php echo $delete_entry_no_checked ?> /> No
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
            <?php 
            }
            ?>
            <div class="bsk-gfblcv-form-settings-error-messages-container" style="display: <?php echo $form_settings_error_messages_container_display; ?>">
                <h4><?php esc_html_e( 'Default validation messages', 'bsk_gfblcv' ); ?></h4>
                <table class="gforms_form_settings" cellspacing="0" cellpadding="0">
                    <tr class="bsk-gfblcv-form-settings-option-tr">
                        <th>&nbsp;</th>
                        <td>
                            <span class="bsk-gfblcv-form-settings-label">Blacklist: </span>
                            <input type="text" name="bsk_gfblcv_blacklist_message" class="bsk-gfblcv-cf7-validation-messages-input" value="<?php  echo esc_attr( $blacklist_message ); ?>" />
                        </td>
                    </tr>
                    <tr class="bsk-gfblcv-form-settings-option-tr">
                        <th></th>
                        <td>
                            <span class="bsk-gfblcv-form-settings-label">White list: </span>
                            <input type="text" name="bsk_gfblcv_whitelist_message" class="bsk-gfblcv-cf7-validation-messages-input" value="<?php echo esc_attr( $whitelist_message ); ?>" />
                        </td>
                    </tr>
                    <tr class="bsk-gfblcv-form-settings-option-tr">
                        <th>&nbsp;</th>
                        <td>
                            <span class="bsk-gfblcv-form-settings-label">Email list: </span>
                            <input type="text" name="bsk_gfblcv_emaillist_message" class="bsk-gfblcv-cf7-validation-messages-input" value="<?php echo esc_attr( $emaillist_message ); ?>" />
                        </td>
                    </tr>
                    <tr class="bsk-gfblcv-form-settings-option-tr">
                        <th>&nbsp;</th>
                        <td>
                            <span class="bsk-gfblcv-form-settings-label">IP list: </span>
                            <input type="text" name="bsk_gfblcv_iplist_message" class="bsk-gfblcv-cf7-validation-messages-input" value="<?php echo esc_attr( $iplist_message ); ?>" />
                        </td>
                    </tr>
                    <tr class="bsk-gfblcv-form-settings-option-tr">
                        <th>&nbsp;</th>
                        <td>
                            <span class="bsk-gfblcv-form-settings-label">Invitation codes list: </span>
                            <input type="text" name="bsk_gfblcv_invitlist_message" class="bsk-gfblcv-cf7-validation-messages-input" value="<?php echo esc_attr( $invitlist_message ); ?>" />
                        </td>
                    </tr>
                    <tr class="bsk-gfblcv-form-settings-option-tr">
                        <th>&nbsp;</th>
                        <td>
                            <p><span class="bsk-gfblcv-form-settings-label">&nbsp;</span>[FIELD_LABEL] will be replaced by field name</p>
                            <p><span class="bsk-gfblcv-form-settings-label">&nbsp;</span>[FIELD_VALUE] will be replaced with field value</p>
                            <p><span class="bsk-gfblcv-form-settings-label">&nbsp;</span>[VISITOR_IP] will be replaced with visitor's IP</p>
                        </td>
                    </tr>
                </table>
            </div>
            <?php wp_nonce_field( 'bsk_gfblcv_cf7_form_settings_nonce', 'bsk_gfblcv_cf7_form_settings_nonce' ); ?>
		</div>
        <?php $this->bsk_gfblcv_cf7_field_mapping( $cf7_post, $form_settings_field_mapping_container_display ); ?>
		<?php
	}

    function bsk_gfblcv_cf7_field_mapping( $cf7_form, $container_display ) {

        $form_id = $cf7_form->id();
        $form_fields = BSK_GFBLCV_Dashboard_Common::cf7_blacklist_get_form_fields( $form_id );
        $saved_form_mapping = get_post_meta( $form_id, BSK_GFBLCV_Dashboard::$_bsk_gfblcv_cf7_form_mappings_opt, true );

        ?>
        <div class="gform_panel gform_panel_form_settings bsk-gfblcv-form-settings-container" id="bsk_gfblcv_cf7_form_mappings_ID" style="display: <?php echo $container_display; ?>">
            <hr />
            <h4><?php esc_html_e( 'Field mappings', 'bsk_gfblcv' ); ?></h4>
            <table class="widefat striped">
                <thead>
                <th style="width: 18%;">Field Name</th>
                    <th style="width: 17%;">List Type</th>
                    <th style="width: 25%;">List</th>
                    <th style="width: 13%;">Comparison / Action</th>
                    <th style="width: 27%;">Validaiton Message</th>
                </thead>
                <tbody>
                    <?php
                    if( $form_fields && is_array( $form_fields ) && count( $form_fields ) > 0 ){
                        foreach( $form_fields as $field ){
                            if( $field->name == "" ){
                                continue;
                            }
                            $field_settings = $saved_form_mapping && isset( $saved_form_mapping[$field->name] ) ? $saved_form_mapping[$field->name] : false;
                            $list_type = '';
                            $list_id = 0;
                            $list_comparison = '';
                            $save_id_error = false;
                            $save_comparison_error = false;
                            $validation_message = '';
                            if ( $field_settings && is_array( $field_settings ) && count( $field_settings ) > 0 ) {
                                $list_type = $field_settings['list_type'];
                                $list_id = $field_settings['list_id'];
                                $list_comparison = $field_settings['list_comparison'];
                                if ( isset( $field_settings['save_id_error'] ) ) {
                                    $save_id_error = $field_settings['save_id_error'];
                                }
                                if ( isset( $field_settings['save_comparison_error'] ) ) {
                                    $save_comparison_error = $field_settings['save_comparison_error'];
                                }
                                if ( isset( $field_settings['validation_message'] ) ) {
                                    $validation_message = $field_settings['validation_message'];
                                }
                            }
                    ?>
                    <tr>
                        <td><?php echo $field->name; ?></td>
                        <td>
                            <select class="bsk-gfblcv-cf7-mapping-list-type-select" name="bsk_gfblcv_cf7_list_type_of_<?php echo $field->name; ?>">
                                <option value="">Type...</option>
                                <option value="BLACK_LIST"<?php echo ( $list_type == 'BLACK_LIST' ? ' selected' : '' ); ?>>Blacklist</option>
                                <option value="WHITE_LIST"<?php echo ( $list_type == 'WHITE_LIST' ? ' selected' : '' ); ?>>White List</option>
                                <option value="EMAIL_LIST"<?php echo ( $list_type == 'EMAIL_LIST' ? ' selected' : '' ); ?>>Email List</option>
                                <option value="IP_LIST"<?php echo ( $list_type == 'IP_LIST' ? ' selected' : '' ); ?>>IP List</option>
                                <option value="INVIT_LIST"<?php echo ( $list_type == 'INVIT_LIST' ? ' selected' : '' ); ?>>Invitation Codes List</option>
                            </select>
                        </td>
                        <?php
                            $list_id_blacklist_display = 'none';
                            $list_id_whitelist_display = 'none';
                            $list_id_emaillist_display = 'none';
                            $list_id_iplist_display = 'none';
                            $list_id_invitlist_display = 'none';
                            $list_comparison_display = 'none';
                            $list_action_display = 'none';
                            $list_invit_action_display = 'none';
                            $validation_message_display = 'inline-block;';
                            switch ( $list_type ) {
                                case 'BLACK_LIST':
                                    $list_id_blacklist_display = 'inline-block';
                                    $list_comparison_display = 'inline-block';
                                break;
                                case 'WHITE_LIST':
                                    $list_id_whitelist_display = 'inline-block';
                                    $list_comparison_display = 'inline-block';
                                break;
                                case 'EMAIL_LIST':
                                    $list_id_emaillist_display = 'inline-block';
                                    $list_action_display = 'inline-block';
                                break;
                                case 'IP_LIST':
                                    $list_id_iplist_display = 'inline-block';
                                    $list_action_display = 'inline-block';
                                break;
                                case 'INVIT_LIST':
                                    $list_id_invitlist_display = 'inline-block';
                                    $list_invit_action_display = 'inline-block';
                                break;
                                default:
                                    $validation_message_display = 'none';
                                break;
                            }
                        ?>
                        <td>
                            <select class="bsk-gfblcv-cf7-mapping-list-id-select bsk-gfblcv-cf7-blacklist" name="bsk_gfblcv_cf7_blacklist_id_of_<?php echo $field->name; ?>" style="display: <?php echo $list_id_blacklist_display; ?>;">
                                <option value="">Select a list...</option>
                                <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'BLACK_LIST', $list_id ); ?>
                            </select>
                            <select disabled class="bsk-gfblcv-cf7-mapping-list-id-select bsk-gfblcv-cf7-whitelist" name="bsk_gfblcv_cf7_whitelist_id_of_<?php echo $field->name; ?>" style="display: <?php echo $list_id_whitelist_display; ?>;">
                                <option value="">Only supported in Pro version...</option>
                            </select>
                            <select disabled class="bsk-gfblcv-cf7-mapping-list-id-select bsk-gfblcv-cf7-emaillist" name="bsk_gfblcv_cf7_emaillist_id_of_<?php echo $field->name; ?>" style="display: <?php echo $list_id_emaillist_display; ?>;">
                                <option value="">Only supported in Pro version...</option>
                            </select>
                            <select disabled class="bsk-gfblcv-cf7-mapping-list-id-select bsk-gfblcv-cf7-iplist" name="bsk_gfblcv_cf7_iplist_id_of_<?php echo $field->name; ?>" style="display: <?php echo $list_id_iplist_display; ?>;">
                                <option value="">Only supported in Pro version...</option>
                            </select>
                            <select disabled class="bsk-gfblcv-cf7-mapping-list-id-select bsk-gfblcv-cf7-invitlist" name="bsk_gfblcv_cf7_invitlist_id_of_<?php echo $field->name; ?>" style="display: <?php echo $list_id_invitlist_display; ?>;">
                                <option value="">Only supported in Pro version...</option>
                            </select>
                            <?php if ( $save_id_error ) echo '<span class="bsk-gfblcv-error-message" style="display:inline-block;">*</span>'; ?>
                        </td>
                        <td>
                            <select class="bsk-gfblcv-cf7-mapping-comparison" name="bsk_gfblcv_cf7_comparison_of_<?php echo $field->name; ?>" style="display: <?php echo $list_comparison_display; ?>;">
                                <option value="">Select comparison...</option>
                                <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_comparison( $list_comparison ); ?>
                            </select>
                            <select class="bsk-gfblcv-cf7-mapping-action" name="bsk_gfblcv_cf7_action_of_<?php echo $field->name; ?>" style="display: <?php echo $list_action_display; ?>;">
                                <?php
                                    echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( $list_comparison ); 
                                ?>
                            </select>
                            <select class="bsk-gfblcv-cf7-mapping-action-for-invit" name="bsk_gfblcv_cf7_action_of_for_invit<?php echo $field->name; ?>" style="display: <?php echo $list_invit_action_display; ?>;">
                                <?php
                                    echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( 'ALLOW', true ); 
                                ?>
                            </select>
                            <?php if ( $save_comparison_error ) echo '<span class="bsk-gfblcv-error-message" style="display:inline-block;">*</span>'; ?>
                        </td>
                        <td>
                            <input type="text" class="bsk-gfblcv-cf7-validation-message" name="bsk_gfblcv_cf7_validation_message_of_<?php echo $field->name; ?>" value="Only supported in Pro version" readonly style="width: 95%; display: <?php echo $validation_message_display; ?>;" />
                        </td>
                    </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }
	
    function bsk_gfblcv_cf7_save_form_setting( $contact_form ){
        if ( ! isset( $_POST ) || empty( $_POST ) ) {
			return;
        }
        
        if ( ! wp_verify_nonce( $_POST['bsk_gfblcv_cf7_form_settings_nonce'], 'bsk_gfblcv_cf7_form_settings_nonce' ) ) {
            return;
        }
        
        $form_id = $contact_form->id();
        $form_fields = BSK_GFBLCV_Dashboard_Common::cf7_blacklist_get_form_fields( $form_id );
        if( !$form_fields || !is_array( $form_fields ) || count( $form_fields ) < 1 ){
            return;
        }

        //organise form setting
        $bsk_gfblcv_form_settings = array();
		$bsk_gfblcv_form_settings['enable'] = false;
        if ( isset( $_POST['bsk_gfblcv_form_settings_enable'] ) && sanitize_text_field( $_POST['bsk_gfblcv_form_settings_enable'] ) == 'ENABLE' ) {
            $bsk_gfblcv_form_settings['enable'] = true;
        }
		$bsk_gfblcv_form_settings['actions'] = array( 'BLOCK');
		$bsk_gfblcv_form_settings['mails_to_skip'] = array();

        $bsk_gfblcv_form_settings['blacklist_message'] = sanitize_text_field( $_POST['bsk_gfblcv_blacklist_message'] );
		$bsk_gfblcv_form_settings['whitelist_message'] = sanitize_text_field( $_POST['bsk_gfblcv_whitelist_message'] );
		$bsk_gfblcv_form_settings['emaillist_message'] = sanitize_text_field( $_POST['bsk_gfblcv_emaillist_message'] );
		$bsk_gfblcv_form_settings['iplist_message'] = sanitize_text_field( $_POST['bsk_gfblcv_iplist_message'] );
		$bsk_gfblcv_form_settings['invitlist_message'] = sanitize_text_field( $_POST['bsk_gfblcv_invitlist_message'] );

		//save form settings
		update_post_meta( $form_id, BSK_GFBLCV_Dashboard::$_bsk_gfblcv_cf7_form_settings_opt, $bsk_gfblcv_form_settings );


        //save form mapping
        if ( $bsk_gfblcv_form_settings['enable'] == true ) {
            $this->bsk_gfblcv_cf7_save_form_mapping( $form_id, $form_fields );
        }
    }

    function bsk_gfblcv_cf7_save_form_mapping( $form_id, $form_fields ) {
        
        //organise form setting
        $form_mappings = array();
        foreach( $form_fields as $field ){
            if( $field->name == "" ){
                continue;
            }
            $field_settings = array();
            $field_settings['list_type'] = '';
            $field_settings['list_id'] = 0;
            $field_settings['list_comparison '] = '';
            $field_settings['save_id_error'] = false;
            $field_settings['save_comparison_error'] = false;

            if ( isset( $_POST['bsk_gfblcv_cf7_list_type_of_'.$field->name] ) ) {
                $field_settings['list_type'] = sanitize_text_field( $_POST['bsk_gfblcv_cf7_list_type_of_'.$field->name] );
            }
            if ( $field_settings['list_type'] == '' ) {
                continue;
            }
            
            switch ( $field_settings['list_type'] ) {
                case 'BLACK_LIST':
                    $list_id = intval( sanitize_text_field( $_POST['bsk_gfblcv_cf7_blacklist_id_of_'.$field->name] ) );
                    $list_comparison  = sanitize_text_field( $_POST['bsk_gfblcv_cf7_comparison_of_'.$field->name] );
                break;
                case 'WHITE_LIST':
                    $list_id = intval( sanitize_text_field( $_POST['bsk_gfblcv_cf7_whitelist_id_of_'.$field->name] ) );
                    $list_comparison  = sanitize_text_field( $_POST['bsk_gfblcv_cf7_comparison_of_'.$field->name] );
                break;
                case 'EMAIL_LIST':
                    $list_id = intval( sanitize_text_field( $_POST['bsk_gfblcv_cf7_emaillist_id_of_'.$field->name] ) );
                    $list_comparison  = sanitize_text_field( $_POST['bsk_gfblcv_cf7_action_of_'.$field->name] );
                break;
                case 'IP_LIST':
                    $list_id = intval( sanitize_text_field( $_POST['bsk_gfblcv_cf7_iplist_id_of_'.$field->name] ) );
                    $list_comparison  = sanitize_text_field( $_POST['bsk_gfblcv_cf7_action_of_'.$field->name] );
                break;
                case 'INVIT_LIST':
                    $list_id = intval( sanitize_text_field( $_POST['bsk_gfblcv_cf7_invitlist_id_of_'.$field->name] ) );
                    $list_comparison  = sanitize_text_field( $_POST['bsk_gfblcv_cf7_action_of_for_invit'.$field->name] );
                break;
            }
            $field_settings['list_id'] = $list_id;
            $field_settings['list_comparison'] = $list_comparison;
            $field_settings['validation_message'] = '';

            if ( $list_id < 1 ) {
                $field_settings['save_id_error'] = true;
            }
            if ( $list_comparison == '' ) {
                $field_settings['save_comparison_error'] = true;
            }
            
            $form_mappings[$field->name] = $field_settings;
        }

        //save form mappings
        update_post_meta( $form_id, BSK_GFBLCV_Dashboard::$_bsk_gfblcv_cf7_form_mappings_opt, $form_mappings );
    }

}