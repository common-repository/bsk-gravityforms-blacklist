<?php
class BSK_GFBLCV_Dashboard_Settings {

    var $settings_targets = array( 'general-settings', 'blocked-data', 'sending-invitaiton-code' );
	
	public function __construct() {
		
        add_action( 'bsk_gfblcv_save_general_settings', array( $this, 'bsk_gfblcv_save_general_settings_fun' ) );
        add_action( 'bsk_gfblcv_save_blocked_data_settings', array( $this, 'bsk_gfblcv_save_blocked_data_settings_fun' ) );
        add_action( 'bsk_gfblcv_save_sending_invitation_code_settings', array( $this, 'bsk_gfblcv_save_sending_invitation_code_settings_fun' ) );
	}
	
    function display(){
        $settings_data = get_option( BSK_GFBLCV_Dashboard::$_plugin_settings_option, false );
		?>
        <div class="wrap" id="bsk_gfblcv_setings_wrap_ID">
            <div id="icon-edit" class="icon32"><br/></div>
            <h2>Settings</h3>
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab nav-tab-active" href="javascript:void(0);" id="bsk_gfblcv_setings_tab-general-settings"><?php esc_html_e( 'General', 'bskgfbl' ); ?></a>
                <a class="nav-tab" href="javascript:void(0);" id="bsk_gfblcv_setings_tab-blocked-data"><?php esc_html_e( 'Block Form Data & Notify', 'bskgfbl' ); ?></a>
                <a class="nav-tab" href="javascript:void(0);" id="bsk_gfblcv_setings_tab-sending-invitaiton-code"><?php esc_html_e( 'Inviation Code Email Settings', 'bskgfbl' ); ?></a>
            </h2>
            <div id="bsk_gfblcv_setings_tab_content_wrap_ID">
				<section><?php $this->show_general_settings( $settings_data, 'general-settings' ); ?></section>
                <section><?php $this->show_blocked_data_settings( $settings_data, 'blocked-data' ); ?></section>
                <section><?php $this->show_sending_invitation_code_settings( $settings_data, 'sending-invitaiton-code' ); ?></section>
            </div>
        </div>
        <?php
        $target_tab = isset( $_REQUEST['target'] ) ? sanitize_text_field( $_REQUEST['target'] ) : '';
        if ( ! in_array( $target_tab, $this->settings_targets ) ) {
            $target_tab = $this->settings_targets[0];
        }
		echo '<input type="hidden" id="bsk_gfblcv_settings_target_tab_ID" value="'.$target_tab.'" />';
    }

    function show_general_settings( $settings_data, $target_tab ){

        $action_url = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['settings'].'&target='.$target_tab );
        
        $settings_data = get_option( BSK_GFBLCV_Dashboard::$_plugin_settings_option, false );
        $supported_form_plugins = array( 'GF' );
        $save_blocked_entry = 'NO';
        $notify_blocked = 'NO';
        $notify_details = false;
        if( $settings_data && is_array( $settings_data ) && count( $settings_data ) > 0 ){
            if( isset( $settings_data['supported_form_plugins'] ) && count( $settings_data['supported_form_plugins'] ) > 0 ){
                $supported_form_plugins = $settings_data['supported_form_plugins'];
            }
            if( isset( $settings_data['save_blocked_entry'] ) ){
                $save_blocked_entry = $settings_data['save_blocked_entry'];
            }
            if( isset( $settings_data['notify_blocked'] ) ){
                $notify_blocked = $settings_data['notify_blocked'];
            }
            if( isset( $settings_data['notify_details'] ) ){
                $notify_details = $settings_data['notify_details'];
            }
        }
        
        $gf_checked = '';
        $ff_checked = '';
        $wpf_checked = '';
        $cf7_checked = '';
        $forminator_checked = '';
        if( in_array( 'GF', $supported_form_plugins ) ){
            $gf_checked = 'checked';
        }
        if( in_array( 'FF', $supported_form_plugins ) ){
            $ff_checked = 'checked';
        }
        if( in_array( 'WPF', $supported_form_plugins ) ){
            $wpf_checked = 'checked';
        }
        if( in_array( 'CF7', $supported_form_plugins ) ){
            $cf7_checked = 'checked';
        }
        if( in_array( 'FRMT', $supported_form_plugins ) ){
            $forminator_checked = 'checked';
        }
		?>
        <form action="<?php echo $action_url; ?>" method="POST" id="bsk_gfblcv_general_settings_form_ID">
        <div>
            <h3 style="margin-top: 40px;">Supported form plugins</h3>
            <p>
                <label style="display: inline-block; width: 15%;">
                    <input type="radio" name="bsk_gfblcv_supported_form_plugins[]" value="GF" <?php echo $gf_checked ?> /> Gravity Forms
                </label>
                <label style="display: inline-block; width: 15%;">
                    <input type="radio" name="bsk_gfblcv_supported_form_plugins[]" value="FF" <?php echo $ff_checked ?> /> Formidable Forms
                </label>
                <label style="display: inline-block; width: 15%;">
                    <input type="radio" name="bsk_gfblcv_supported_form_plugins[]" value="WPF" <?php echo $wpf_checked ?> /> WPForms
                </label>
                <label style="display: inline-block; width: 15%;">
                    <input type="radio" name="bsk_gfblcv_supported_form_plugins[]" value="CF7" <?php echo $cf7_checked ?> /> Contact Form 7
                </label>
                <label style="display: inline-block; width: 15%;">
                    <input type="radio" name="bsk_gfblcv_supported_form_plugins[]" value="FRMT" <?php echo $forminator_checked; ?> /> Forminator
                </label>
            </p>
            <div class="bsk-gfblcv-tips-box" style="width: 75%;">
                <p>Free version can only choose one form plugin to support. </p>
                <p><span style="font-weight: bold;">CREATOR</span> and above license for Pro version can support all above form plugins. </p>
                <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
            </div>
        </div>
        <p style="margin-top: 40px;">
            <input type="submit" class="button-primary" name="bsk_gfblcv_save_settings" value="Save" />
            <input type="hidden" name="bsk_gfblcv_action" value="save_general_settings" />
            <?php wp_nonce_field( 'bsk_gfbcv_general_settings_save_oper_nonce', '_general_settings_nonce' ); ?>
        </p>
        </form>
    <?php
    }

    function bsk_gfblcv_save_general_settings_fun(){
        //check nonce field
		if ( !wp_verify_nonce( $_POST['_general_settings_nonce'], 'bsk_gfbcv_general_settings_save_oper_nonce' ) ){
			wp_die( 'Security check!' );
			return;
		}

		$settings_data = get_option( BSK_GFBLCV_Dashboard::$_plugin_settings_option, false );
        if( !$settings_data || !is_array( $settings_data ) ){
            $settings_data = array();    
        }
        
        $settings_data['supported_form_plugins'] = array();
        if ( isset( $_POST['bsk_gfblcv_supported_form_plugins'] ) && 
             is_array( $_POST['bsk_gfblcv_supported_form_plugins'] ) && 
             count( $_POST['bsk_gfblcv_supported_form_plugins'] ) > 0 ) {

            foreach( $_POST['bsk_gfblcv_supported_form_plugins'] as $plugin ) {
                $settings_data['supported_form_plugins'][] = sanitize_text_field( $plugin );
            }
        }
        
        update_option( BSK_GFBLCV_Dashboard::$_plugin_settings_option, $settings_data );
    }

    function show_blocked_data_settings( $settings_data, $target_tab ){
        $action_url = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['settings'].'&target='.$target_tab );
        $save_blocked_entry = 'NO';
        $notify_blocked = 'NO';
        $notify_details = false;
        if( $settings_data && is_array( $settings_data ) && count( $settings_data ) > 0 ){
            if( isset( $settings_data['save_blocked_entry'] ) ){
                $save_blocked_entry = $settings_data['save_blocked_entry'];
            }
            if( isset( $settings_data['notify_blocked'] ) ){
                $notify_blocked = $settings_data['notify_blocked'];
            }
            if( isset( $settings_data['notify_details'] ) ){
                $notify_details = $settings_data['notify_details'];
            }
        }
        $save_blocked_entry_yes_checked = $save_blocked_entry == 'YES' ? ' checked' : '';
        $save_blocked_entry_no_checked = $save_blocked_entry == 'NO' ? ' checked' : '';
    ?>
    <form action="<?php echo $action_url; ?>" method="POST" id="bsk_gfblcv_blocked_data_settings_form_ID">
    <div>
        <h3 style="margin-top: 40px;">Enable save blocked form data</h3>
        <p>
            <label><input type="radio" name="bsk_gfblcv_save_blocked_entry_enable" value="YES" <?php echo $save_blocked_entry_yes_checked; ?>/> Yes</label>
            <label style="margin-left: 40px;"><input type="radio" name="bsk_gfblcv_save_blocked_entry_enable" value="NO" <?php echo $save_blocked_entry_no_checked; ?>/> No</label>
        </p>
        <p>With this enabled, the form data will be saved if a submitting blocked.</p>
        <div class="bsk-gfblcv-tips-box" style="width: 75%;">
            <p>This feature requires a <span style="font-weight: bold;">BUSINESS</span>( or above ) license for Pro version</p>
            <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
        </div>
    </div>
    <div class="bsk-gfblcv-notify-administrtor-settings">
        <?php
        $notify_blocked_yes_checked = $notify_blocked == 'YES' ? ' checked' : '';
        $notify_blocked_no_checked = $notify_blocked == 'NO' ? ' checked' : '';
        ?>
        <h3 style="margin-top: 40px;">Enable notify administrators</h3>
        <p>Notify administrators( emails ) when form submitting blocked</p>
        <p>
            <label><input type="radio" name="bsk_gfblcv_notify_blocked_enable" value="YES" <?php echo $notify_blocked_yes_checked; ?> class="bsk-gfblcv-notify-bloked-enable-radio" /> Yes</label>
            <label style="margin-left: 40px;"><input type="radio" name="bsk_gfblcv_notify_blocked_enable" value="NO" <?php echo $notify_blocked_no_checked; ?> class="bsk-gfblcv-notify-bloked-enable-radio" /> No</label>
        </p>
        <div class="bsk-gfblcv-tips-box">
                <p>This feature requires a <span style="font-weight: bold;">BUSINESS</span>( or above ) license for Pro version</p>
                <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
            </div>
        <?php
        
        $details_container_display = $notify_blocked == 'YES' ? 'block' : 'none';
        $send_to = get_option( 'admin_email' );
        $from_name = '';
        $from_email = '';
        $subject = 'New submission from {form_title} on {form_submission_date}';
        $message = '<p>Submitted from IP: {form_submission_IP}</p><p>Submission data:</p><p>{form_submission_data}</p>';

        if( $notify_details && is_array( $notify_details ) && count( $notify_details ) > 0 ){
            if( isset( $notify_details['send_to'] ) && $notify_details['send_to'] ){
                $send_to = $notify_details['send_to'];
            }
            if( isset( $notify_details['from_name'] ) && $notify_details['from_name'] ){
                $from_name = $notify_details['from_name'];
            }
            if( isset( $notify_details['from_email'] ) && $notify_details['from_email'] ){
                $from_email = $notify_details['from_email'];
            }
            if( isset( $notify_details['subject'] ) && $notify_details['subject'] ){
                $subject = $notify_details['subject'];
            }
            if( isset( $notify_details['message'] ) && $notify_details['message'] ){
                $message = $notify_details['message'];
            }
        }
        ?>
        <div class="bsk-gfblcv-administrator-mails-details-container" style="display: <?php echo $details_container_display; ?>;">
            <p>
                <label>Send To</label>
                <span style="display: inline-block;">
                    <input type="text" class="bsk-gfblcv-administrator-mails-input" name="bsk_gfblcv_administrator_mails_send_to" value="<?php echo $send_to; ?>" />
                    <span style="font-style: italic; margin-left: 20px;">user comma( , ) to separate multiple mails</span>
                </span>
            </p>
            <p>
                <label>From Name</label>
                <span>
                    <input type="text" class="bsk-gfblcv-administrator-mails-input" name="bsk_gfblcv_administrator_mails_from_name" value="<?php echo $from_name; ?>" />
                </span>
            </p>
            <p>
                <label>From Email</label>
                <span>
                    <input type="text" class="bsk-gfblcv-administrator-mails-input" name="bsk_gfblcv_administrator_mails_from_email" value="<?php echo $from_email; ?>" />
                </span>
            </p>
            <p>
                <label>Subject</label>
                <span>
                    <input type="text" class="bsk-gfblcv-administrator-mails-input" name="bsk_gfblcv_administrator_mails_subject" value="<?php echo $subject; ?>" />
                </span>
            </p>
            <p>
                <label>Message</label>
                <span>
                <?php
                    $settings = array( 
                                    'media_buttons' => false,
                                    'editor_height' => 150,
                                    'wpautop' => false,
                                    'default_editor' => 'tinymce',
                                    );
                    wp_editor( $message, 'bsk_gfblcv_administrator_mails_message', $settings );
                ?>
                </span>
            </p>
            <p>* {form_title} will be replaced by form title</p>
            <p>* {form_submission_data} will be replaced by form submission data</p>
            <p>* {form_submission_IP} will be replaced by client ip address</p>
            <p>* {form_submission_date} will be replaced by submission date</p>
        </div>
    </div>
    <p style="margin-top: 40px;">
        <input type="submit" class="button-primary" name="bsk_gfblcv_save_settings" value="Save" />
        <input type="hidden" name="bsk_gfblcv_action" value="save_blocked_data_settings" />
        <?php wp_nonce_field( 'bsk_gfbcv_blocked_data_settings_save_oper_nonce', '_blocked_data_settings_nonce' ); ?>
    </p>
    </form>
    <?php
    }

    function bsk_gfblcv_save_blocked_data_settings_fun(){
        //check nonce field
		if ( !wp_verify_nonce( $_POST['_blocked_data_settings_nonce'], 'bsk_gfbcv_blocked_data_settings_save_oper_nonce' ) ){
			wp_die( 'Security check!' );
			return;
		}

		$settings_data = get_option( BSK_GFBLCV_Dashboard::$_plugin_settings_option, false );
        if( !$settings_data || !is_array( $settings_data ) ){
            $settings_data = array();    
        }

        $settings_data['save_blocked_entry'] = $_POST['bsk_gfblcv_save_blocked_entry_enable'];
        $settings_data['notify_blocked'] = $_POST['bsk_gfblcv_notify_blocked_enable'];

        $notify_details = array();
        if( trim( $_POST['bsk_gfblcv_administrator_mails_send_to'] ) ){
            $send_to = trim( $_POST['bsk_gfblcv_administrator_mails_send_to'] );
            $send_to_array = explode( ',', $send_to );
            if( count($send_to_array) ){
                foreach( $send_to_array as $key => $email ){
                    if( !is_email( $email ) ){
                        unset( $send_to_array[$key] );
                    }
                }
                $notify_details['send_to'] = count($send_to_array) ? implode( ',', $send_to_array ) : '';
            }else{
                $notify_details['send_to'] = '';
            }
        }
        if( isset( $_POST['bsk_gfblcv_administrator_mails_from_name'] ) && $_POST['bsk_gfblcv_administrator_mails_from_name'] ){
            $notify_details['from_name'] = sanitize_text_field( $_POST['bsk_gfblcv_administrator_mails_from_name'] );
        }
        if( isset( $_POST['bsk_gfblcv_administrator_mails_from_email'] ) && $_POST['bsk_gfblcv_administrator_mails_from_email'] ){
            $from_email = sanitize_text_field( $_POST['bsk_gfblcv_administrator_mails_from_email'] );
            $notify_details['from_email'] = is_email( $from_email ) ? $from_email : '';
        }
        if( isset( $_POST['bsk_gfblcv_administrator_mails_subject'] ) && $_POST['bsk_gfblcv_administrator_mails_subject'] ){
            $notify_details['subject'] = sanitize_text_field( $_POST['bsk_gfblcv_administrator_mails_subject'] );
        }
        if( isset( $_POST['bsk_gfblcv_administrator_mails_message'] ) && $_POST['bsk_gfblcv_administrator_mails_message'] ){
            $notify_details['message'] = sanitize_text_field( $_POST['bsk_gfblcv_administrator_mails_message'] );
        }
        $settings_data['notify_details'] = $notify_details;
        
        
        
        update_option( BSK_GFBLCV_Dashboard::$_plugin_settings_option, $settings_data );
    }
    
    function show_sending_invitation_code_settings( $settings_data, $target_tab ){
        $action_url = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['settings'].'&target='.$target_tab );
        $sending_invitation_code_details = false;
        if( $settings_data && is_array( $settings_data ) && count( $settings_data ) > 0 ){
            if( isset( $settings_data['sending_invitation_code'] ) ){
                $sending_invitation_code_details = $settings_data['sending_invitation_code'];
            }
        }
    ?>
    <form action="<?php echo $action_url; ?>" method="POST" id="bsk_gfblcv_sending_invitation_code_settings_form_ID">
    <div class="bsk-gfblcv-sending-invitation-code-settings">
        <?php
        $from_name =  get_bloginfo( 'name' );
        $from_email = get_option( 'admin_email' );
        $subject = 'Your invitation code';
        $message = '<p style="font-size: 14px; line-height: 140%;"><span style="font-size: 18px; line-height: 25.2px; color: #666666;">Hello,</span></p>
<p style="font-size: 14px; line-height: 140%;"> </p>
<p style="font-size: 14px; line-height: 140%;"><span style="font-size: 18px; line-height: 25.2px; color: #666666;">We have sent you this email in response to your request to invitation code.</span></p>
<p style="font-size: 14px; line-height: 140%;"> </p>
<p>{INVITATION_CODE}</p>';

        if( $sending_invitation_code_details && is_array( $sending_invitation_code_details ) && count( $sending_invitation_code_details ) > 0 ){
            if( isset( $sending_invitation_code_details['from_name'] ) && $sending_invitation_code_details['from_name'] ){
                $from_name = $sending_invitation_code_details['from_name'];
            }
            if( isset( $sending_invitation_code_details['from_email'] ) && $sending_invitation_code_details['from_email'] ){
                $from_email = $sending_invitation_code_details['from_email'];
            }
            if( isset( $sending_invitation_code_details['subject'] ) && $sending_invitation_code_details['subject'] ){
                $subject = $sending_invitation_code_details['subject'];
            }
            if( isset( $sending_invitation_code_details['message'] ) && $sending_invitation_code_details['message'] ){
                $message = $sending_invitation_code_details['message'];
            }
        }
        ?>
        <div class="bsk-gfblcv-tips-box">
            <p>This feature only available Pro version</p>
            <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
        </div>
        <div class="bsk-gfblcv-administrator-mails-details-container">
            <p>
                <label>From Name</label>
                <span>
                    <input type="text" class="bsk-gfblcv-administrator-mails-input" name="bsk_gfblcv_sending_invitation_code_mails_from_name" value="<?php echo $from_name; ?>" />
                </span>
            </p>
            <p>
                <label>From Email</label>
                <span>
                    <input type="text" class="bsk-gfblcv-administrator-mails-input" name="bsk_gfblcv_sending_invitation_code_mails_from_email" value="<?php echo $from_email; ?>" />
                </span>
            </p>
            <p>
                <label>Subject</label>
                <span>
                    <input type="text" class="bsk-gfblcv-administrator-mails-input" name="bsk_gfblcv_sending_invitation_code_mails_subject" value="<?php echo $subject; ?>" />
                </span>
            </p>
            <p>
                <label>Message</label>
                <span>
                <?php
                    $settings = array(
                                    'media_buttons' => false,
                                    'editor_height' => 250,
                                    'wpautop' => false,
                                    'default_editor' => 'tinymce',
                                 );
                    wp_editor( $message, 'bsk_gfblcv_sending_invitation_code_mails_message', $settings );
                ?>
                </span>
            </p>
            <p>* {INVITATION_CODE} will be replaced by the invitation code</p>
        </div>
    </div>
    <p style="margin-top: 40px;">
        <input type="submit" class="button-primary" name="bsk_gfblcv_save_settings" value="Save" />
        <input type="hidden" name="bsk_gfblcv_action" value="save_sending_invitation_code_settings" />
        <?php wp_nonce_field( 'bsk_gfblcv_sending_invitation_code_settings_save_oper_nonce', '_sending_invitation_code_settings_nonce' ); ?>
    </p>
    </form>
    <?php
    }

    function bsk_gfblcv_save_sending_invitation_code_settings_fun(){
        //check nonce field
		if ( !wp_verify_nonce( $_POST['_sending_invitation_code_settings_nonce'], 'bsk_gfblcv_sending_invitation_code_settings_save_oper_nonce' ) ){
			wp_die( 'Security check!' );
			return;
		}

		$settings_data = get_option( BSK_GFBLCV_Dashboard::$_plugin_settings_option, false );
        if( !$settings_data || !is_array( $settings_data ) ){
            $settings_data = array();
        }

        $sending_invitation_code_details = array();
        if( isset( $_POST['bsk_gfblcv_sending_invitation_code_mails_from_name'] ) && $_POST['bsk_gfblcv_sending_invitation_code_mails_from_name'] ){
            $sending_invitation_code_details['from_name'] = sanitize_text_field( $_POST['bsk_gfblcv_sending_invitation_code_mails_from_name'] );
        }
        if( isset( $_POST['bsk_gfblcv_sending_invitation_code_mails_from_email'] ) && $_POST['bsk_gfblcv_sending_invitation_code_mails_from_email'] ){
            $from_email = trim( $_POST['bsk_gfblcv_sending_invitation_code_mails_from_email'] );
            $sending_invitation_code_details['from_email'] = is_email( $from_email ) ? $from_email : '';
        }
        if( isset( $_POST['bsk_gfblcv_sending_invitation_code_mails_subject'] ) && $_POST['bsk_gfblcv_sending_invitation_code_mails_subject'] ){
            $sending_invitation_code_details['subject'] = sanitize_text_field( $_POST['bsk_gfblcv_sending_invitation_code_mails_subject'] );
        }
        if( isset( $_POST['bsk_gfblcv_sending_invitation_code_mails_message'] ) && $_POST['bsk_gfblcv_sending_invitation_code_mails_message'] ){
            $sending_invitation_code_details['message'] = wp_unslash( $_POST['bsk_gfblcv_sending_invitation_code_mails_message'] );
        }
        $settings_data['sending_invitation_code'] = $sending_invitation_code_details;

        update_option( BSK_GFBLCV_Dashboard::$_plugin_settings_option, $settings_data );
    }

}
