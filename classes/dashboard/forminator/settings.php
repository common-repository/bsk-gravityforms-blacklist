<?php
class BSK_GFBLCV_Dashboard_Forminator_Settings {

    public $_bsk_gfblcv_OBJ_frmt_form_settings = false;
    public $_bsk_gfblcv_OBJ_frmt_field_settings = false;

    var $settings_targets = array( 'form-settings', 'field-settings' );

	public function __construct() {

        require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/forminator/form-settings.php' );
        require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/forminator/form-field.php' );

        $this->_bsk_gfblcv_OBJ_frmt_form_settings = new BSK_GFBLCV_Dashboard_Forminator_Settings_Form_Settings();
        $this->_bsk_gfblcv_OBJ_frmt_field_settings = new BSK_GFBLCV_Dashboard_Forminator_Settings_Field_Settings();

        add_action( 'bsk_gfblcv_save_forminator_form_settings', array( $this, 'bsk_gfblcv_save_forminator_form_settings_fun' ) );
        add_action( 'bsk_gfblcv_save_forminator_field_settings', array( $this, 'bsk_gfblcv_save_forminator_field_settings_fun' ) );

	}

    function disaplay( $form_id ){
        
        $form_obj = Forminator_API::get_form( $form_id );
        if ( is_wp_error( $form_obj ) ) {
            wp_die( 'Invalid form ID: ' . $form_id );
        }
        $form_edit_link = admin_url( 'admin.php?page=forminator-cform-wizard&id=' . $form_obj->id );
		?>
        <div class="wrap" id="bsk_gfblcv_forminator_setings_wrap_ID">
            <div id="icon-edit" class="icon32"><br/></div>
            <h2>BSK Balcklist Forminator Form Settings &amp; Field Settings</h2>
            <h3>Form Name: <a href="<?php echo $form_edit_link; ?>"><?php echo $form_obj->settings['formName']; ?></a>, ID: <a href="<?php echo $form_edit_link; ?>"><?php echo $form_obj->id; ?></a></h3>
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab nav-tab-active" href="javascript:void(0);" id="bsk_gfblcv_forminator_setings_tab-form-settings"><?php esc_html_e( 'Form Settings', 'bskgfbl' ); ?></a>
                <a class="nav-tab" href="javascript:void(0);" id="bsk_gfblcv_forminator_setings_tab-field-settings"><?php esc_html_e( 'Field Settings', 'bskgfbl' ); ?></a>
            </h2>
            <div id="bsk_gfblcv_setings_tab_content_wrap_ID">
				<section><?php $this->show_form_settings( $form_id, 'form-settings' ); ?></section>
                <section><?php $this->show_field_settings( $form_id, 'field-settings' ); ?></section>
            </div>
        </div>
        <?php
        $target_tab = isset($_REQUEST['target']) ? sanitize_text_field($_REQUEST['target']) : '';
        if ( ! in_array( $target_tab, $this->settings_targets ) ) {
            $target_tab = $this->settings_targets[0];
        }
		echo '<input type="hidden" id="bsk_gfblcv_forminator_settings_target_tab_ID" value="'.$target_tab.'" />';
    }

    function show_form_settings( $form_id, $target_tab ) {

        $action_url = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['forminator_blacklist'] );
        $action_url .= '&id=' . $form_id . '&view=settings&target='.$target_tab;
        ?>
        <div>
        <form action="<?php echo $action_url; ?>" method="POST" id="bsk_gfblcv_forminator_form_settings_form_ID">
        <?php
            $this->_bsk_gfblcv_OBJ_frmt_form_settings->settings( $form_id );
        ?>
        <p style="margin-top: 40px;">
            <input type="submit" class="button-primary" name="bsk_gfblcv_save_settings" value="Save" />
            <input type="hidden" name="bsk_gfblcv_action" value="save_forminator_form_settings" />
            <input type="hidden" name="bsk_gfblcv_forminator_form_id" value="<?php echo $form_id; ?>" />
            <?php wp_nonce_field( 'bsk_gfblcv_forminator_form_settings_save_oper_nonce', '_forminator_form_settings_nonce' ); ?>
        </p>
        </form>
        </div>
        <?php
    }

    function bsk_gfblcv_save_forminator_form_settings_fun(){
        //check nonce field
		if ( !wp_verify_nonce( $_POST['_forminator_form_settings_nonce'], 'bsk_gfblcv_forminator_form_settings_save_oper_nonce' ) ){
			wp_die( 'Security check!' );
			return;
		}

        $form_id = absint( sanitize_text_field( $_POST['bsk_gfblcv_forminator_form_id'] ) );
        if ( $form_id < 1 ) {
            wp_die( 'Invalid form ID!' );
			return;
        }

		$bsk_gfblcv_form_settings = array();

        $bsk_gfblcv_form_settings['enable'] = sanitize_text_field($_POST['bsk_gfblcv_form_settings_enable']) == 'ENABLE' ? true : false; ;
        $action_when_hit = array();
        if ( isset( $_POST['bsk_gfblcv_form_settings_actions'] ) &&
                is_array( $_POST['bsk_gfblcv_form_settings_actions'] ) &&
                count( $_POST['bsk_gfblcv_form_settings_actions'] ) > 0 ) {

            foreach ( $_POST['bsk_gfblcv_form_settings_actions'] as $aciton ) {
                $action_when_hit[] = $aciton;
            }
        }
        if ( count( $action_when_hit ) < 1 ) {
            $action_when_hit[] = 'BLOCK';
        }
        $bsk_gfblcv_form_settings['actions'] = $action_when_hit;
        $bsk_gfblcv_form_settings['notification_to_skip'] = array();
        if ( isset( $_POST['bsk_gfblcv_notification_to_skip'] ) ) {
            if( $_POST['bsk_gfblcv_notification_to_skip'] &&
                is_array( $_POST['bsk_gfblcv_notification_to_skip'] ) &&
                count( $_POST['bsk_gfblcv_notification_to_skip'] ) ) {

                foreach ( $_POST['bsk_gfblcv_notification_to_skip'] as $notification_id ) {
                    $bsk_gfblcv_form_settings['notification_to_skip'][] = absint( sanitize_text_field( $notification_id ) );
                }
            }
        }
        
        $bsk_gfblcv_form_settings['save_blocked_data'] = sanitize_text_field($_POST['bsk_gfblcv_save_blocked_data']);
        $bsk_gfblcv_form_settings['notify_administrators'] = sanitize_text_field($_POST['bsk_gfblcv_notify_administrators']);
        $bsk_gfblcv_form_settings['notify_send_to'] = sanitize_text_field($_POST['bsk_gfblcv_notify_send_to']);
        $bsk_gfblcv_form_settings['blacklist_message'] = wp_unslash(sanitize_text_field($_POST['bsk_gfblcv_blacklist_message']));
        $bsk_gfblcv_form_settings['whitelist_message'] = wp_unslash(sanitize_text_field($_POST['bsk_gfblcv_whitelist_message']));
        $bsk_gfblcv_form_settings['emaillist_message'] = wp_unslash(sanitize_text_field($_POST['bsk_gfblcv_emaillist_message']));
        $bsk_gfblcv_form_settings['iplist_message'] = wp_unslash(sanitize_text_field($_POST['bsk_gfblcv_iplist_message']));
        $bsk_gfblcv_form_settings['invitlist_message'] = wp_unslash(sanitize_text_field($_POST['bsk_gfblcv_invitlist_message']));

        update_option(
                        BSK_GFBLCV_Dashboard_Forminator::$_bsk_gfblcv_frmt_form_settings_option_name_prefix . $form_id,
                        $bsk_gfblcv_form_settings
                     );

        //update_option( BSK_GFBLCV_Dashboard::$_plugin_settings_option, $settings_data );
    }

    function show_field_settings( $form_id, $target_tab ) {

        $action_url = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['forminator_blacklist'] );
        $action_url .= '&id=' . $form_id . '&view=settings&target='.$target_tab;
        ?>
        <div>
        <form action="<?php echo $action_url; ?>" method="POST" id="bsk_gfblcv_general_settings_form_ID">
        <?php
            $this->_bsk_gfblcv_OBJ_frmt_field_settings->settings( $form_id );
        ?>
        </div>
        <p style="margin-top: 40px;">
            <input type="submit" class="button-primary" name="bsk_gfblcv_save_settings" value="Save" />
            <input type="hidden" name="bsk_gfblcv_action" value="save_forminator_field_settings" />
            <input type="hidden" name="bsk_gfblcv_forminator_form_id" value="<?php echo $form_id; ?>" />
            <?php wp_nonce_field( 'bsk_gfblcv_forminator_field_settings_save_oper_nonce', '_forminator_field_settings_nonce' ); ?>
        </p>
        </form>
        <?php
    }

    function bsk_gfblcv_save_forminator_field_settings_fun(){
        //check nonce field
		if ( !wp_verify_nonce( $_POST['_forminator_field_settings_nonce'], 'bsk_gfblcv_forminator_field_settings_save_oper_nonce' ) ){
			wp_die( 'Security check!' );
			return;
		}

        $form_id = absint( sanitize_text_field( $_POST['bsk_gfblcv_forminator_form_id'] ) );
        if ( $form_id < 1 ) {
            wp_die( 'Invalid form ID!' );
			return;
        }

        $form_fields_array = BSK_GFBLCV_Dashboard_Common::forminator_get_form_fields( $form_id );

        //organise form setting
        $form_mappings = array();
        foreach( $form_fields_array as $field_id => $field ){
            if( $field_id == "" ){
                continue;
            }
            $field_settings = array();
            $field_settings['list_type'] = '';
            $field_settings['list_id'] = 0;
            $field_settings['list_comparison '] = '';
            $field_settings['save_id_error'] = false;
            $field_settings['save_comparison_error'] = false;

            if ( isset( $_POST['bsk_gfblcv_frmt_list_type_of_'.$field_id] ) ) {
                $field_settings['list_type'] = sanitize_text_field( $_POST['bsk_gfblcv_frmt_list_type_of_'.$field_id] );
            }
            if ( $field_settings['list_type'] == '' ) {
                continue;
            }
            
            switch ( $field_settings['list_type'] ) {
                case 'BLACK_LIST':
                    $list_id = intval( sanitize_text_field( $_POST['bsk_gfblcv_frmt_blacklist_id_of_'.$field_id] ) );
                    $list_comparison  = sanitize_text_field( $_POST['bsk_gfblcv_frmt_comparison_of_'.$field_id] );
                break;
                case 'WHITE_LIST':
                    $list_id = intval( sanitize_text_field( $_POST['bsk_gfblcv_frmt_whitelist_id_of_'.$field_id] ) );
                    $list_comparison  = sanitize_text_field( $_POST['bsk_gfblcv_frmt_comparison_of_'.$field_id] );
                break;
                case 'EMAIL_LIST':
                    $list_id = intval( sanitize_text_field( $_POST['bsk_gfblcv_frmt_emaillist_id_of_'.$field_id] ) );
                    $list_comparison  = sanitize_text_field( $_POST['bsk_gfblcv_frmt_action_of_'.$field_id] );
                break;
                case 'IP_LIST':
                    $list_id = intval( sanitize_text_field( $_POST['bsk_gfblcv_frmt_iplist_id_of_'.$field_id] ) );
                    $list_comparison  = sanitize_text_field( $_POST['bsk_gfblcv_frmt_action_of_'.$field_id] );
                break;
                case 'INVIT_LIST':
                    $list_id = intval( sanitize_text_field( $_POST['bsk_gfblcv_frmt_invitlist_id_of_'.$field_id] ) );
                    $list_comparison  = sanitize_text_field( $_POST['bsk_gfblcv_frmt_action_of_for_invit'.$field_id] );
                break;
            }
            $field_settings['list_id'] = $list_id;
            $field_settings['list_comparison'] = $list_comparison;
            $field_settings['validation_message'] = ''; //wp_unslash( sanitize_text_field( $_POST['bsk_gfblcv_frmt_validation_message_of_'.$field_id] ) );

            if ( $list_id < 1 ) {
                $field_settings['save_id_error'] = true;
            }
            if ( $list_comparison == '' ) {
                $field_settings['save_comparison_error'] = true;
            }
            
            $form_mappings[$field_id] = $field_settings;
        }

        //save form mappings
        update_option(
            BSK_GFBLCV_Dashboard_Forminator::$_bsk_gfblcv_frmt_field_settings_option_name_prefix . $form_id,
            $form_mappings
         );

    }

}
