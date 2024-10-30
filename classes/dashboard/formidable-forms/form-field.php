<?php

class BSK_GFBLCV_Dashboard_FF_Field {
	
	function __construct() {
        if ( BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_is_form_plugin_supported('FF') ) {
            add_action( 'frm_after_field_options', array( $this, 'bsk_gfblcv_ff_field_settings_html' ), 10, 1 );
            add_filter( 'frm_default_field_opts', array( $this, 'bsk_gfblcv_ff_field_settings_save' ), 20, 3 );
        }
	}
	
    function bsk_gfblcv_ff_field_settings_html( $field_display_values ) {
        extract( $field_display_values );
        if ( in_array( $field['type'], array( 'html', 'user_id', 'captcha', 'hidden' ) ) ) {
            return;
        }
        ?>
        <h3 class="ff2z-populating-from-zoho-field-title">BSK Blacklist<i class="frm_icon_font frm_arrowdown6_icon"></i></h3>
        <?php
        //form settings
        $form_id = $values['id'];
		$bsk_gfblcv_form_settings = maybe_unserialize( get_option( BSK_GFBLCV_Dashboard_Formidable_Forms::$_bsk_gfblcv_ff_form_settings_option_name_prefix . $form_id ) );
        
        $enable = true;
        $action_when_hit = array( 'BLOCK' );
        if( $bsk_gfblcv_form_settings && is_array( $bsk_gfblcv_form_settings ) && count( $bsk_gfblcv_form_settings ) > 0 ){
            $enable = $bsk_gfblcv_form_settings['enable'];
            $action_when_hit = $bsk_gfblcv_form_settings['actions'];
        }
        
        if ( ! $enable ) {
            $form_settings_url = admin_url( sprintf( 'admin.php?page=formidable&frm_action=settings&id=%d#bsk_blacklist_ff_form_settings_tab_settings', $form_id ) );
            ?>
            <div class="bsk_gfblcv_field_single_input_container frm_grid_container frm-collapse-me">
                <p><a href="<?php echo $form_settings_url; ?>">Enable for this form</a></p>
            </div>
            <?php
            
            return;
        }
        
        $validation_message = isset ( $field['bsk_gfblcv_validation_message'] ) ? $field['bsk_gfblcv_validation_message'] : '';
        $validation_message_display = 'none';
        
        $blacklist_list = isset ( $field['bsk_gfbl_apply_blacklist_Property'] ) ? $field['bsk_gfbl_apply_blacklist_Property'] : '';
        $blacklist_comparison = isset ( $field['bsk_gfbl_apply_blacklist_Comparison'] ) ? $field['bsk_gfbl_apply_blacklist_Comparison'] : '';

        ?>
        <div class="bsk_gfblcv_field_single_input_container frm_grid_container frm-collapse-me">
            <ul>
                <?php
                $display = 'none';
                $checked = '';
                if ( $blacklist_list && $blacklist_comparison ) {
                    $display = 'block';
                    $checked = ' checked="true"';
                    $validation_message_display = 'block';
                }
                ?>
                <li class="bsk-gfbl-apply-blacklist-field-setting" style="display:list-item;">
                    <input type="checkbox" name="bsk_gfblcv_ff_form_field_apply_blacklist_chk_<?php echo $field['id']; ?>" class="toggle_setting bsk-gfbl-ff-form-field-apply-list-chk" data-list-type="BLACK_LIST"<?php echo $checked; ?> />
                    <label class="inline">
                        <?php _e("Apply Blacklist", "bsk-gfbl"); ?>
                    </label>
                    <br />
                    <select name="bsk_gfblcv_ff_form_field_apply_blacklist_<?php echo $field['id']; ?>" class="bsk-gfbl-list" style="margin-top:10px; display:<?php echo $display ?>;">
                        <option value="">Select a list...</option>
                        <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'BLACK_LIST', $blacklist_list ); ?>
                    </select>
                    <select name="bsk_gfblcv_ff_form_field_blacklist_comparision_<?php echo $field['id']; ?>" class="bsk-gfbl-comparison" style="margin-top:10px; display:<?php echo $display ?>;">
                        <option value="">Select comparison...</option>
                        <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_comparison( $blacklist_comparison ); ?>
                    </select>
                </li>
                <?php
                $display = 'none';
                $checked = '';
                ?>
                <li class="bsk-gfbl-apply-white-list-field-setting" style="display:list-item;">
                    <input type="checkbox" name="bsk_gfblcv_ff_form_field_apply_whitelist_chk_<?php echo $field['id']; ?>" class="toggle_setting bsk-gfbl-ff-form-field-apply-list-chk" data-list-type="WHITE_LIST"<?php echo $checked; ?> />
                    <label class="inline">
                        <?php _e("Apply White List", "bsk-gfbl"); ?>
                    </label>
                    <br />
                    <select name="bsk_gfblcv_ff_form_field_apply_whitelist_<?php echo $field['id']; ?>"  class="bsk-gfbl-list" style="margin-top:10px; display:<?php echo $display ?>;" disabled>
                        <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'WHITE_LIST', '' ); ?>
                    </select>
                    <select name="bsk_gfblcv_ff_form_field_whitelist_comparision_<?php echo $field['id']; ?>" class="bsk-gfbl-comparison" style="margin-top:10px; display:<?php echo $display ?>;">
                        <option value="">Select comparison...</option>
                        <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_comparison( '' ); ?>
                    </select>
                </li>
                <?php
                $display = 'none';
                $checked = '';
                ?>
                <li class="bsk-gfbl-apply-email-list-field-setting" style="display:list-item;">
                    <input type="checkbox" name="bsk_gfblcv_ff_form_field_apply_emaillist_chk_<?php echo $field['id']; ?>" class="toggle_setting bsk-gfbl-ff-form-field-apply-list-chk" data-list-type="EMAIL_LIST"<?php echo $checked; ?> />
                    <label class="inline">
                        <?php _e("Apply Email List", "bsk-gfbl"); ?>
                    </label>
                     <br />
                    <select name="bsk_gfblcv_ff_form_field_apply_emaillist_<?php echo $field['id']; ?>" class="bsk-gfbl-list" style="margin-top:10px; display:<?php echo $display ?>;" disabled>
                        <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'EMAIL_LIST', '' ); ?>
                    </select>
                    <select name="bsk_gfblcv_ff_form_field_emaillist_action_<?php echo $field['id']; ?>" class="bsk-gfbl-comparison" style="margin-top:10px; display:<?php echo $display ?>;">
                        <option value="">Select action...</option>
                        <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( '' ); ?>
                    </select>
                </li>
                <?php
                $display = 'none';
                $checked = '';
                ?>
                <li class="bsk-gfbl-apply-ip-list-field-setting" style="display:list-item;">
                    <input type="checkbox" name="bsk_gfblcv_ff_form_field_apply_iplist_chk_<?php echo $field['id']; ?>" class="toggle_setting bsk-gfbl-ff-form-field-apply-list-chk" data-list-type="IP_LIST"<?php echo $checked; ?>  />
                    <label class="inline">
                        <?php _e("Apply IP List", "bsk-gfbl"); ?>
                    </label>
                     <br />
                    <select class="bsk-gfbl-list" name="bsk_gfblcv_ff_form_field_apply_iplist_<?php echo $field['id']; ?>" style="margin-top:10px; display:<?php echo $display ?>;" disabled>
                        <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'IP_LIST', '' ); ?>
                    </select>
                    <select name="bsk_gfblcv_ff_form_field_iplist_action_<?php echo $field['id']; ?>" class="bsk-gfbl-comparison" style="margin-top:10px; display:<?php echo $display ?>;">
                        <option value="">Select action...</option>
                        <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( '' ); ?>
                    </select>
                </li>
                <?php
                $display = 'none';
                $checked = '';
                ?>
                <li class="bsk-gfbl-apply-invit-list-field-setting" style="display:list-item;">
                    <input type="checkbox" name="bsk_gfblcv_ff_form_field_apply_invitlist_chk_<?php echo $field['id']; ?>" id="bsk_gfblcv_ff_form_field_apply_invitlist_chk_<?php echo $field['id']; ?>_ID" class="toggle_setting bsk-gfbl-ff-form-field-apply-list-chk" data-list-type="INVIT_LIST"<?php echo $checked; ?>  />
                    <label class="inline" for="bsk_gfblcv_ff_form_field_apply_invitlist_chk_<?php echo $field['id']; ?>_ID">
                        <?php _e("Apply Invitation Code List", "bsk-gfbl"); ?>
                    </label>
                     <br />
                    <select class="bsk-gfbl-list" name="bsk_gfblcv_ff_form_field_apply_invitlist_<?php echo $field['id']; ?>" style="margin-top:10px; display:<?php echo $display ?>;" disabled>
                        <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'INVIT_LIST', '' ); ?>
                    </select>
                    <select name="bsk_gfblcv_ff_form_field_invitlist_action_<?php echo $field['id']; ?>" class="bsk-gfbl-comparison" style="margin-top:10px; display:<?php echo $display ?>;">
                        <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( 'ALLOW', true ); ?>
                    </select>
                </li>
                <?php
                if( in_array( 'BLOCK', $action_when_hit ) ){
                ?>
                <li class="bsk-gfbl-validation-message-field-setting" style="display: block;margin-top: 20px;">
                    <label class="inline"><?php _e("Validation Message", "bsk-gfbl"); ?>
                    <input type="text" name="bsk_gfblcv_ff_form_field_validaiton_message_<?php echo $field['id']; ?>" class="fieldwidth-2" value="Only availabe in Pro verison" disabled />
                    <br />
                    <span class="frm-sub-label">[FIELD_LABEL] will be replaced with field label<br />[FIELD_VALUE] will be replaced with field value<br />[VISITOR_IP] will be replaced with visitor's IP</span>
                </li>
                <?php
                }
                ?>
            </ul>
            <p>
                <input type="hidden" name="bsk_gfblcv_ff_form_field_save" value="SAVE" />
            <p>
            <div style="clear: both;">&nbsp;</div>
        </div>
        <?php
    }
    
    function bsk_gfblcv_ff_field_settings_save( $opts, $values, $field ){
        
        if ( isset( $_POST['bsk_gfblcv_ff_form_field_apply_blacklist_'.$field->id] ) ) {
            $opts['bsk_gfbl_apply_blacklist_Property'] = sanitize_text_field( $_POST['bsk_gfblcv_ff_form_field_apply_blacklist_'.$field->id] );
        } else {
            $opts['bsk_gfbl_apply_blacklist_Property'] = '';
        }
        if ( isset( $_POST['bsk_gfblcv_ff_form_field_blacklist_comparision_'.$field->id] ) ) {
            $opts['bsk_gfbl_apply_blacklist_Comparison'] = sanitize_text_field( $_POST['bsk_gfblcv_ff_form_field_blacklist_comparision_'.$field->id] );
        } else {
            $opts['bsk_gfbl_apply_blacklist_Comparison'] = '';
        }
        
        if ( isset( $_POST['bsk_gfblcv_ff_form_field_apply_whitelist_'.$field->id] ) ) {
            $opts['bsk_gfbl_apply_white_list_Property'] = sanitize_text_field( $_POST['bsk_gfblcv_ff_form_field_apply_whitelist_'.$field->id] );
        } else {
            $opts['bsk_gfbl_apply_white_list_Property'] = '';
        }
        if ( isset( $_POST['bsk_gfblcv_ff_form_field_whitelist_comparision_'.$field->id] ) ) {
            $opts['bsk_gfbl_apply_white_list_Comparison'] = sanitize_text_field( $_POST['bsk_gfblcv_ff_form_field_whitelist_comparision_'.$field->id] );
        } else {
            $opts['bsk_gfbl_apply_white_list_Comparison'] = '';
        }
        
        if ( isset( $_POST['bsk_gfblcv_ff_form_field_apply_emaillist_'.$field->id] ) ) {
            $opts['bsk_gfbl_apply_email_list_Property'] = sanitize_text_field( $_POST['bsk_gfblcv_ff_form_field_apply_emaillist_'.$field->id] );
        } else {
            $opts['bsk_gfbl_apply_email_list_Property'] = '';
        }
        if ( isset( $_POST['bsk_gfblcv_ff_form_field_emaillist_action_'.$field->id] ) ) {
            $opts['bsk_gfbl_apply_email_list_Comparison'] = sanitize_text_field( $_POST['bsk_gfblcv_ff_form_field_emaillist_action_'.$field->id] );
        } else {
            $opts['bsk_gfbl_apply_email_list_Comparison'] = '';
        }
        
        if ( isset( $_POST['bsk_gfblcv_ff_form_field_apply_iplist_'.$field->id] ) ) {
            $opts['bsk_gfbl_apply_ip_list_Property'] = sanitize_text_field( $_POST['bsk_gfblcv_ff_form_field_apply_iplist_'.$field->id] );
        } else {
            $opts['bsk_gfbl_apply_ip_list_Property'] = '';
        }
        if ( isset( $_POST['bsk_gfblcv_ff_form_field_iplist_action_'.$field->id] ) ) {
            $opts['bsk_gfbl_apply_ip_list_Comparison'] = sanitize_text_field( $_POST['bsk_gfblcv_ff_form_field_iplist_action_'.$field->id] );
        } else {
            $opts['bsk_gfbl_apply_ip_list_Comparison'] = '';
        }
        
        if ( isset( $_POST['bsk_gfblcv_ff_form_field_validaiton_message_'.$field->id] ) ) {
            $opts['bsk_gfblcv_validation_message'] = sanitize_text_field( $_POST['bsk_gfblcv_ff_form_field_validaiton_message_'.$field->id] );
        } else {
            $opts['bsk_gfblcv_validation_message'] = '';
        }

        return $opts;
    }
    
}
