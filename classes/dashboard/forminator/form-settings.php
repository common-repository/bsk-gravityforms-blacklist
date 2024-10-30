<?php

class BSK_GFBLCV_Dashboard_Forminator_Settings_Form_Settings {

	function __construct() {

	}

    function settings( $form_id ) {

        $bsk_gfblcv_form_settings = get_option( BSK_GFBLCV_Dashboard_Forminator::$_bsk_gfblcv_frmt_form_settings_option_name_prefix . $form_id, false );

        //plugin gloabla settings
        $plugin_settings_data = get_option( BSK_GFBLCV_Dashboard::$_plugin_settings_option, false );
        $plugin_settings_data['save_blocked_entry'] = 'NO';
        $plugin_settings_data['notify_blocked'] = 'NO';
        $global_save_blocked_entry = 'NO';
        $global_notify_blocked = 'NO';
        $global_notify_details = false;
        $global_notify_send_to = '';
        if( $plugin_settings_data && is_array( $plugin_settings_data ) && count( $plugin_settings_data ) > 0 ){
            if( isset( $plugin_settings_data['save_blocked_entry'] ) ){
                $global_save_blocked_entry = $plugin_settings_data['save_blocked_entry'];
            }
            if( isset( $plugin_settings_data['notify_blocked'] ) ){
                $global_notify_blocked = $plugin_settings_data['notify_blocked'];
            }
            if( isset( $plugin_settings_data['notify_details'] ) ){
                $global_notify_details = $plugin_settings_data['notify_details'];
                if( isset( $global_notify_details['send_to'] ) && $global_notify_details['send_to'] ){
                    $global_notify_send_to = $global_notify_details['send_to'];
                }
            }
        }

        //form settings
        $enable = false;
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
        $invitlist_message = $default;
        if( $bsk_gfblcv_form_settings && is_array( $bsk_gfblcv_form_settings ) && count( $bsk_gfblcv_form_settings ) > 0 ){
            $enable = $bsk_gfblcv_form_settings['enable'];
            $action_when_hit = $bsk_gfblcv_form_settings['actions'];
            /* $notification_to_skip = $bsk_gfblcv_form_settings['notification_to_skip'];
            $confirmation_to_go = $bsk_gfblcv_form_settings['confirmation_to_go']; */
            $save_blocked_data = $bsk_gfblcv_form_settings['save_blocked_data'];
            $notify_administrators = $bsk_gfblcv_form_settings['notify_administrators'];
            $notify_send_to = $bsk_gfblcv_form_settings['notify_send_to'];
            //$delete_entry = $bsk_gfblcv_form_settings['delete_entry'];
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

        if ( count( $action_when_hit ) < 1 ) {
            $action_when_hit[] = 'BLOCK';
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

        ?>
		<div class="gform_panel gform_panel_form_settings bsk-gfblcv-form-settings-container" id="bsk_gfblcv_settings">
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
                                <input type="checkbox" value="BLOCK" name="bsk_gfblcv_form_settings_actions[]"<?php echo $block_checked ?> class="bsk-gfblcv-form-settings-action-block-chk" disabled /> Block form submitting
                            </label>
                            <?php if ( 0 ) : ?>
                            <label style="margin-left:20px;">
                                <input type="checkbox" value="SKIP" name="bsk_gfblcv_form_settings_actions[]"<?php echo $skip_checked ?> class="bsk-gfblcv-form-settings-action-skip-chk" /> Skip Actions &amp; Notifications
                            </label>
                            <label style="margin-left:20px;">
                                <input type="checkbox" value="CONFIRMATION" name="bsk_gfblcv_form_settings_actions[]"<?php echo $confirmation_checked ?> class="bsk-gfblcv-form-settings-action-confirmation-chk" /> Change <strong>ON SUBMIT</strong><span class="frm_help frm_icon_font frm_tooltip_icon" title="" data-original-title="Formidable forms' ON SUBMIT settings on General tab"></span> action
                            </label>
                            <?php endif; ?>
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
                    $set_notify_mail_template_link = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['settings'].'&target=blocked-data' );
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
                            <span class="bsk-gfblcv-form-settings-actions-desc" style="display: inline-block;">Click to set <a href="<?php echo $set_notify_mail_template_link; ?>">notify mail template and other info >></span>
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
                            <span class="bsk-gfblcv-form-settings-error-message-label">Invitation code list: </span>
                            <input type="text" name="bsk_gfblcv_invitlist_message" class="bsk-gfblvc-form-settings-input-width" value="<?php echo esc_attr( $invitlist_message ); ?>" />
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
		</div>
        <?php
    }

}
