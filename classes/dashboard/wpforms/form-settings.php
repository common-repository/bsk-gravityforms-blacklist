<?php

class BSK_GFBLCV_Dashboard_WPForms_Settings {
	
	function __construct() {
		
      if ( BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_is_form_plugin_supported('WPF') ) {

          add_filter( 'wpforms_builder_settings_sections', array( $this, 'bsk_gfblcv_wpforms_builder_form_settings_section' ), 10, 2 );
          add_action( 'wpforms_form_settings_panel_content', array( $this, 'bsk_gfblcv_wpforms_form_settings') );
      }
	}
    
    
    function bsk_gfblcv_wpforms_builder_form_settings_section( $sections, $form_data ){
        $sections['bskblacklist'] = 'BSK Blacklist';

        return $sections;
    }
    
    function bsk_gfblcv_wpforms_form_settings( $form_obj ) {

        // Check if there is a form created.
        if ( ! $form_obj->form ) {
            echo '<div class="wpforms-alert wpforms-alert-info">';
            echo wp_kses(
                __( 'You need to <a href="#" class="wpforms-panel-switch" data-panel="setup">setup your form</a> before you can manage the settings.', 'wpforms-lite' ),
                array(
                    'a' => array(
                        'href'       => array(),
                        'class'      => array(),
                        'data-panel' => array(),
                    ),
                )
            );
            echo '</div>';

            return;
        }
      
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
        ?>
        <div class="wpforms-panel-content-section wpforms-panel-content-section-bskblacklist">
            <div class="wpforms-panel-content-section-title">
                <?php esc_html_e( 'Blacklist / White list / Email list / IP list', 'bsk_gfblcv' ); ?>
			  </div>
            <div class="bsk-gfblcv-wpform-settings-enable-disable-container">
                <h3><?php esc_html_e( 'General settings', 'bsk_gfblcv' ); ?></h3>
                <?php
                if( !isset($form_obj->form_data['settings']['bsk_gfblcv_form_settings_enable']) ){
                    $form_obj->form_data['settings']['bsk_gfblcv_form_settings_enable'] = 'DISABLE';
                }
                $settings_panel_display = 'none';
                $settings_blocked_data_display = 'none';
                $settings_confirmations_display = 'none';
                $settings_notifications_display = 'none';
                $settings_entry_display = 'none';
                if( $form_obj->form_data['settings']['bsk_gfblcv_form_settings_enable'] == 'ENABLE' ){
                    $settings_panel_display = 'block';
                }

                if( isset($form_obj->form_data['settings']['bsk_gfblcv_form_settings_actions_block']) &&
                    $form_obj->form_data['settings']['bsk_gfblcv_form_settings_actions_block'] ){
                    $settings_blocked_data_display = 'block';
                    $settings_confirmations_display = 'none';
                    $settings_notifications_display = 'none';
                    $settings_entry_display = 'none';
                }
                if( isset($form_obj->form_data['settings']['bsk_gfblcv_form_settings_actions_skip']) &&
                    $form_obj->form_data['settings']['bsk_gfblcv_form_settings_actions_skip'] ){
                    $settings_blocked_data_display = 'none';
                    $settings_notifications_display = 'block';
                    $settings_entry_display = 'block';
                }
                if( isset($form_obj->form_data['settings']['bsk_gfblcv_form_settings_actions_confirmation']) &&
                    $form_obj->form_data['settings']['bsk_gfblcv_form_settings_actions_confirmation'] ){
                    $settings_blocked_data_display = 'none';
                    $settings_confirmations_display = 'block';
                    $settings_entry_display = 'block';
                }
                
                $form_settings_enable_options = array(
                                                        'ENABLE' => array( 'label' => esc_html__( 'Enable for this form', 'bsk_gfblcv' ), 
                                                                            'value' => 'ENABLE'
                                                                          ),
                                                        'DISABLE' => array( 'label' => esc_html__( 'Disable for this form', 'bsk_gfblcv' ), 
                                                                           'value' => 'DISABLE' 
                                                                         ),
                                                     );
                wpforms_panel_field(
                                        'radio',
                                        'settings',
                                        'bsk_gfblcv_form_settings_enable',
                                        $form_obj->form_data,
                                        '',
                                        array( 
                                                'options' => $form_settings_enable_options,
                                                'input_class' => 'bsk-gfblcv-wpforms-eanble-disable-radio',
                                                'class' => 'bsk-gfblcv-wpforms-general-settings'
                                             )
                                   );
                ?>
                <div style="clear: both;"></div>
            </div>
            <p>&nbsp;</p>
            <div class="bsk-gfblcv-wpform-settings-actions-container" style="display: <?php echo $settings_panel_display ?>;">
                <h3><?php esc_html_e( 'Actions', 'bsk_gfblcv' ); ?></h3>
                <?php
                
                wpforms_panel_field(
                                        'checkbox',
                                        'settings',
                                        'bsk_gfblcv_form_settings_actions_block',
                                        $form_obj->form_data,
                                        esc_html__( 'Block form submitting', 'bsk_gfblcv' ),
                                        array( 
                                                'class' => 'bsk-gfblcv-wpforms-actions'
                                             )
                                   );
                
                wpforms_panel_field(
                                        'checkbox',
                                        'settings',
                                        'bsk_gfblcv_form_settings_actions_skip',
                                        $form_obj->form_data,
                                        esc_html__( 'Skip notifications', 'bsk_gfblcv' ),
                                        array( 
                                                'class' => 'bsk-gfblcv-wpforms-actions'
                                             )
                                   );
                wpforms_panel_field(
                                        'checkbox',
                                        'settings',
                                        'bsk_gfblcv_form_settings_actions_confirmation',
                                        $form_obj->form_data,
                                        esc_html__( 'Go specific confirmation', 'bsk_gfblcv' ),
                                        array( 
                                                'class' => 'bsk-gfblcv-wpforms-actions'
                                             )
                                   );
                ?>
                <div style="clear: both;"></div>
            </div>
            <div class="bsk-gfblcv-wpform-settings-notifications-container" style="display: <?php echo $settings_notifications_display ?>;">
                <p>Check Notifications to skip:</p>
                <div class="bsk-gfblcv-tips-box">
                    <p>This feature only supported in Pro verison. </p>
                    <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
                </div>
                <p class="bsk-gfblcv-notification-insert-position">&nbsp;</p>
                <?php
                foreach( $form_obj->form_data[ 'settings' ]['notifications'] as $notification_id => $notifiction_data ){
                    $notification_name = isset( $notifiction_data['notification_name'] ) ? $notifiction_data['notification_name'] : '';
                    if( $notification_id == 1 && $notification_name == '' ){
                        $notification_name = 'Default Notification';
                    }
                    wpforms_panel_field(
                                    'checkbox',
                                    'settings',
                                    'bsk_gfblcv_form_settings_skip_notifications_'.$notification_id,
                                    $form_obj->form_data,
                                    $notification_name,
                                    array( 
                                            'class' => 'bsk-gfblcv-wpforms-notification',
                                         )
                               );
                }
                ?>
                <div style="clear: both;"></div>
            </div>
            <div class="bsk-gfblcv-wpform-settings-confirmations-container" style="display: <?php echo $settings_confirmations_display ?>;">
                <p>Select Confirmation to go:</p>
                <div class="bsk-gfblcv-tips-box">
                    <p>This feature only supported in Pro verison. </p>
                    <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
                </div>
                <p class="bsk-gfblcv-confirmation-insert-position">&nbsp;</p>
                <?php
                foreach( $form_obj->form_data[ 'settings' ]['confirmations'] as $confirmation_id => $confirmation_data ){
                    $confirmation_name = isset( $confirmation_data['name'] ) ? $confirmation_data['name'] : '';
                    if( $confirmation_id == 1 && $confirmation_name == '' ){
                        $confirmation_name = 'Default Confirmation';
                    }
                    wpforms_panel_field(
                                    'checkbox',
                                    'settings',
                                    'bsk_gfblcv_form_settings_skip_confirmations_'.$confirmation_id,
                                    $form_obj->form_data,
                                    $confirmation_name,
                                    array( 
                                            'class' => 'bsk-gfblcv-wpforms-confirmation',
                                         )
                               );
                }
                ?>
                <div style="clear: both;"></div>
            </div>
            <div class="bsk-gfblcv-wpform-settings-entry-container" style="display: <?php echo $settings_entry_display ?>;">
                <p>&nbsp;</p>
                <h3><?php esc_html_e( 'Entry', 'bsk_gfblcv' ); ?></h3>
                <p>Delete Entry</p>
                <?php
                if( !isset($form_obj->form_data['settings']['bsk_gfblcv_wpform_settings_delete_entry']) ){
                    $form_obj->form_data['settings']['bsk_gfblcv_wpform_settings_delete_entry'] = 'NO';
                }
                $form_settings_delete_entry_options = array(
                                                        'YES' => array( 'label' => esc_html__( 'Yes', 'bsk_gfblcv' ), 
                                                                            'value' => 'YES'
                                                                          ),
                                                        'NO' => array( 'label' => esc_html__( 'No', 'bsk_gfblcv' ), 
                                                                           'value' => 'NO' 
                                                                         ),
                                                     );
                wpforms_panel_field(
                                        'radio',
                                        'settings',
                                        'bsk_gfblcv_wpform_settings_delete_entry',
                                        $form_obj->form_data,
                                        '',
                                        array( 
                                                'options' => $form_settings_delete_entry_options,
                                                'input_class' => 'bsk-gfblcv-wpforms-delete-entry-radio',
                                                'class' => 'bsk-gfblcv-wpforms-delete-entry'
                                             )
                                   );
                $pro_display = 'none';
                if( $form_obj->form_data['settings']['bsk_gfblcv_wpform_settings_delete_entry'] == 'YES' ){
                    $pro_display = 'block';
                }
                ?>
                <div class="bsk-gfblcv-tips-box" style="display: <?php echo $pro_display; ?>;">
                    <p>This feature only supported in Pro verison. </p>
                    <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
                </div>
                <div style="clear: both;"></div>
            </div>
            <div class="bsk-gfblcv-wpform-settings-blocked-data-container" style="display: <?php echo $settings_blocked_data_display ?>;">
                <p>&nbsp;</p>
                <h3><?php esc_html_e( 'Blocked form data', 'bsk_gfblcv' ); ?></h3>
                <div class="bsk-gfblcv-wpforms-save-blocked-data-label">
                    <span>Save blocked form data:</span>
                </div>
                <?php
                $blocked_form_data_view_link = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['blocked_data'] );
                $blocked_form_data_global_settings = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['settings'] );
                
                if( $global_save_blocked_entry == 'NO' ){
                    $save_blocked_data = 'NO';
                    if( isset( $form_obj->form_data[ 'settings' ][ 'bsk_gfblcv_form_settings_save_blocked_data' ] ) ){
                        $save_blocked_data = $form_obj->form_data[ 'settings' ][ 'bsk_gfblcv_form_settings_save_blocked_data' ];
                    }
                ?>
                <div class="wpforms-panel-field bsk-gfblcv-wpforms-save-blocked-data-settings">
                    <span class="bsk-gfblcv-wpforms-global-settings-link">
                        <a href="<?php echo $blocked_form_data_global_settings; ?>">Blocked Form Data Global Settings</a>
                    </span>
                    <input type="hidden" name="settings[bsk_gfblcv_form_settings_save_blocked_data]" value="<?php echo $save_blocked_data; ?>" />
                </div>
                <?php
                }else{
                    if( !isset( $form_obj->form_data[ 'settings' ][ 'bsk_gfblcv_form_settings_save_blocked_data' ] ) ){
                        $form_obj->form_data[ 'settings' ][ 'bsk_gfblcv_form_settings_save_blocked_data' ] = 'NO';
                    }
                    $form_settings_save_blocked_data_options = array(
                                                                    'YES' => array( 
                                                                                    'label' => esc_html__( 'Yes', 'bsk_gfblcv' ), 
                                                                                    'value' => 'YES'
                                                                                  ),
                                                                    'NO' => array( 
                                                                                   'label' => esc_html__( 'No', 'bsk_gfblcv' ), 
                                                                                   'value' => 'NO'
                                                                                 ),
                                                                 );
                    wpforms_panel_field(
                                            'radio',
                                            'settings',
                                            'bsk_gfblcv_form_settings_save_blocked_data',
                                            $form_obj->form_data,
                                            '',
                                            array( 
                                                    'options' => $form_settings_save_blocked_data_options,
                                                    'input_class' => 'bsk-gfblcv-wpforms-save-blocked-data-radio',
                                                    'class' => 'bsk-gfblcv-wpforms-save-blocked-data-settings'
                                                 )
                                       );
                    ?>
                     <span class="bsk-gfblcv-form-settings-actions-desc" style="display: inline-block; margin-left: 20px;">Blocked form data listed <a href="<?php echo $blocked_form_data_view_link; ?>">here >></a></span>
                    <?php
                }
                ?>
               
                <div style="clear: both; height: 15px;"></div>
                <div class="bsk-gfblcv-wpforms-notify-administrator-label">
                    <span>Notify administrators: </span>
                </div>
                <?php
                $blocked_form_data_view_link = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['blocked_data'] );
                $blocked_form_data_global_settings = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['settings'] );
                
                if( $global_save_blocked_entry == 'NO' ){
                    $notify_administrators = 'NO';
                    if( isset( $form_obj->form_data[ 'settings' ][ 'bsk_gfblcv_form_settings_notify_administrators' ] ) ){
                        $notify_administrators = $form_obj->form_data[ 'settings' ][ 'bsk_gfblcv_form_settings_notify_administrators' ];
                    }
                ?>
                <div class="wpforms-panel-field bsk-gfblcv-wpforms-notify-administrator-settings">
                    <span class="bsk-gfblcv-wpforms-global-settings-link">
                        <a href="<?php echo $blocked_form_data_global_settings; ?>">Notify Administrators( emails ) Global Settings</a>
                    </span>
                    <input type="hidden" name="settings[bsk_gfblcv_form_settings_notify_administrators]" value="<?php echo $notify_administrators; ?>" />
                </div>
                <?php
                }else{
                    if( !isset( $form_obj->form_data[ 'settings' ][ 'bsk_gfblcv_form_settings_notify_administrators' ] ) ){
                        $form_obj->form_data[ 'settings' ][ 'bsk_gfblcv_form_settings_notify_administrators' ] = 'NO';
                    }
                    $form_settings_notify_administrators_options = array(
                                                                        'YES' => array( 
                                                                                        'label' => esc_html__( 'Yes', 'bsk_gfblcv' ), 
                                                                                        'value' => 'YES'
                                                                                      ),
                                                                        'NO' => array( 
                                                                                       'label' => esc_html__( 'No', 'bsk_gfblcv' ), 
                                                                                       'value' => 'NO'
                                                                                     ),
                                                                     );
                    wpforms_panel_field(
                                            'radio',
                                            'settings',
                                            'bsk_gfblcv_form_settings_notify_administrators',
                                            $form_obj->form_data,
                                            '',
                                            array( 
                                                    'options' => $form_settings_notify_administrators_options,
                                                    'input_class' => 'bsk-gfblcv-wpforms-notify-administrator-radio',
                                                    'class' => 'bsk-gfblcv-wpforms-notify-administrator-settings'
                                                 )
                                       );
                }
                $notify_send_to_display = 'none';
                if( $form_obj->form_data[ 'settings' ][ 'bsk_gfblcv_form_settings_notify_administrators' ] == 'YES' ){
                    $notify_send_to_display = 'block';
                }
                $set_notify_mail_template_link = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['settings'] );
                
                $notify_send_to = '';
                if( isset( $form_obj->form_data[ 'settings' ][ 'bsk_gfblcv_notify_administrator_recipient' ] ) ){
                    $notify_send_to = $form_obj->form_data[ 'settings' ][ 'bsk_gfblcv_notify_administrator_recipient' ];
                }
                if( trim( $notify_send_to == '' ) && $global_notify_send_to ){
                    $notify_send_to = $global_notify_send_to;
                }
                ?>
                <div style="clear: both; height: 15px;"></div>
                <div class="bsk-gfblcv-wpforms-notify-administrator-recipient-label">
                    <span style="display: <?php echo $notify_send_to_display; ?>">Send to: </span>
                </div>
                <div class="wpforms-panel-field bsk-gfblcv-form-settings-notify-administrator-recipient" style="display: <?php echo $notify_send_to_display; ?>">
                    <input type="text" value="<?php echo $notify_send_to; ?>" name="settings[bsk_gfblcv_notify_send_to]" class="bsk-gfblvc-form-settings-input-width" />
                </div>
                <div style="clear: both;"></div>
                <div class="bsk-gfblcv-wpforms-notify-administrator-recipient-label">
                    <span>&nbsp;</span>
                </div>
                <div class="wpforms-panel-field bsk-gfblcv-form-settings-notify-administrator-recipient" style="display: <?php echo $notify_send_to_display; ?>">
                    <span class="bsk-gfblcv-form-settings-actions-desc" style="display: block;">user comma( , ) to separate multiple mails</span>
                </div>
                <div style="clear: both;"></div>
                <div class="bsk-gfblcv-wpforms-notify-administrator-recipient-label">
                    <span>&nbsp;</span>
                </div>
                <div class="wpforms-panel-field bsk-gfblcv-form-settings-notify-administrator-recipient" style="display: <?php echo $notify_send_to_display; ?>">
                    <span class="bsk-gfblcv-wpforms-settings-actions-desc" style="display: block;">Set notify mail template <a href="<?php echo $set_notify_mail_template_link; ?>">here >></a></span>
                </div>
                <div style="clear: both;"></div>
            </div>
            <p>&nbsp;</p>
            <div class="bsk-gfblcv-wpform-settings-error-message-container" style="display: <?php echo $settings_blocked_data_display ?>;">
                <h3><?php esc_html_e( 'Error Messages', 'bsk_gfblcv' ); ?></h3>
                <div class="bsk-gfblcv-wpforms-error-message-label">
                    <span>Blacklist:</span>
                </div>
                <?php
                $default = 'The value for field "[FIELD_LABEL]" is invalid!';
                $ip_default = 'Your IP address [VISITOR_IP] is forbidden!';
        
                if( !isset( $form_obj->form_data['settings']['bsk_gfblcv_form_settings_blacklist_error_message'] ) 
                    || $form_obj->form_data['settings']['bsk_gfblcv_form_settings_blacklist_error_message'] == '' ){
                    $form_obj->form_data['settings']['bsk_gfblcv_form_settings_blacklist_error_message'] = $default;
                }
                if( !isset( $form_obj->form_data['settings']['bsk_gfblcv_form_settings_whitelist_error_message'] ) 
                    || $form_obj->form_data['settings']['bsk_gfblcv_form_settings_whitelist_error_message'] == '' ){
                    $form_obj->form_data['settings']['bsk_gfblcv_form_settings_whitelist_error_message'] = $default;
                }
                if( !isset( $form_obj->form_data['settings']['bsk_gfblcv_form_settings_emaillist_error_message'] ) 
                    || $form_obj->form_data['settings']['bsk_gfblcv_form_settings_emaillist_error_message'] == '' ){
                    $form_obj->form_data['settings']['bsk_gfblcv_form_settings_emaillist_error_message'] = $default;
                }
                if( !isset( $form_obj->form_data['settings']['bsk_gfblcv_form_settings_iplist_error_message'] ) 
                    || $form_obj->form_data['settings']['bsk_gfblcv_form_settings_iplist_error_message'] == '' ){
                    $form_obj->form_data['settings']['bsk_gfblcv_form_settings_iplist_error_message'] = $ip_default;
                }
                wpforms_panel_field(
                                        'text',
                                        'settings',
                                        'bsk_gfblcv_form_settings_blacklist_error_message',
                                        $form_obj->form_data,
                                        '',
                                        array( 
                                                'input_class' => 'bsk-gfblcv-wpforms-error-message-input',
                                                'class' => 'bsk-gfblcv-wpforms-error-message'
                                             )
                                   );
                ?>
                <div style="clear: both; padding: 10px;"></div>
                <div class="bsk-gfblcv-wpforms-error-message-label">
                    <span>White list:</span>
                </div>
                <?php
                wpforms_panel_field(
                                        'text',
                                        'settings',
                                        'bsk_gfblcv_form_settings_whitelist_error_message',
                                        $form_obj->form_data,
                                        '',
                                        array( 
                                                'input_class' => 'bsk-gfblcv-wpforms-error-message-input',
                                                'class' => 'bsk-gfblcv-wpforms-error-message'
                                             )
                                   );
                ?>
                <div style="clear: both; padding: 10px;"></div>
                <div class="bsk-gfblcv-wpforms-error-message-label">
                    <span>Email list:</span>
                </div>
                <?php
                wpforms_panel_field(
                                        'text',
                                        'settings',
                                        'bsk_gfblcv_form_settings_emaillist_error_message',
                                        $form_obj->form_data,
                                        '',
                                        array( 
                                                'input_class' => 'bsk-gfblcv-wpforms-error-message-input',
                                                'class' => 'bsk-gfblcv-wpforms-error-message'
                                             )
                                   );
                ?>
                <div style="clear: both; padding: 10px;"></div>
                <div class="bsk-gfblcv-wpforms-error-message-label">
                    <span>IP list:</span>
                </div>
                <?php
                wpforms_panel_field(
                                        'text',
                                        'settings',
                                        'bsk_gfblcv_form_settings_iplist_error_message',
                                        $form_obj->form_data,
                                        '',
                                        array( 
                                                'input_class' => 'bsk-gfblcv-wpforms-error-message-input',
                                                'class' => 'bsk-gfblcv-wpforms-error-message'
                                             )
                                   );
                ?>
                <div style="clear: both;"></div>
            </div>
        </div>
        <?php
    }
}
