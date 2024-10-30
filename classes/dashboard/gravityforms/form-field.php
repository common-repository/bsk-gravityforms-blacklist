<?php

class BSK_GFBLCV_Dashboard_GForm_Field {
	
	private $_bsk_gfblcv_current_form_id = '';
	
	function __construct() {
		
        if ( BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_is_form_plugin_supported('GF') ) {
            add_filter( 'gform_admin_pre_render', array($this, 'bsk_gfblcv_admin_pre_render') );
            add_action( 'gform_field_advanced_settings', array($this, 'bsk_gfblcv_render_field_advanced_settings'), 10, 2 );
            add_action( 'gform_editor_js', array($this, 'bsk_gfblcv_render_editor_js') );
            // filter to add a new tooltip
            add_filter( 'gform_tooltips', array($this, 'bsk_gfblcv_add_gf_tooltips') );
        }
	}
	
	function bsk_gfblcv_admin_pre_render( $form ){
		if( !isset( $form['fields'] ) || !is_array( $form['fields'] ) || count( $form['fields'] ) < 1 ){
			return $form;
		}
		
		return $form;
	}
	
	function bsk_gfblcv_render_field_advanced_settings( $position, $form_id ){
		
        if($position != 50){
            return;
        }
        
		$this->_bsk_gfblcv_current_form_id = $form_id;
		$form = GFAPI::get_form( $form_id );

        //form settings
		$bsk_gfblcv_form_settings = rgar( $form, 'bsk_gfblcv_form_settings' );
        
        $enable = true;
        $action_when_hit = array( 'BLOCK' );
        if( $bsk_gfblcv_form_settings && is_array( $bsk_gfblcv_form_settings ) && count( $bsk_gfblcv_form_settings ) > 0 ){
            $enable = $bsk_gfblcv_form_settings['enable'];
            $action_when_hit = $bsk_gfblcv_form_settings['actions'];
        }else{
            //compatible with old savd data format
            if( isset( $form['block_or_skip_notification'] ) && $form['block_or_skip_notification'] == 'SKIP' ) {
                $action_when_hit = array( 'SKIP' ); 
            }
        }
        
        if( $enable ){
        ?>
        <li class="bsk-gfbl-field-setting field_setting" style="display:list-item;">
            <label for="bsk-gfbl" class="section_label">BSK Blacklist<?php gform_tooltip("bsk_gfblcv_form_field_section_label") ?></label>
            <div class="bsk_gfblcv_field_single_input_container">
                <ul>
                    <li class="bsk-gfbl-apply-blacklist-field-setting" style="display:list-item;">
                        <input type="checkbox" class="toggle_setting" id="bsk_gfblcv_apply_blacklist_chk_ID" />
                        <label for="bsk_gfblcv_apply_blacklist_chk_ID" class="inline">
                            <?php _e("Apply Blacklist", "bsk-gfbl"); ?>
                        </label>
                        <br />
                        <select class="bsk-gfbl-list" onchange="SetFieldProperty('bsk_gfbl_apply_blacklist_Property', jQuery(this).val());" style="margin-top:10px; display:none;">
                            <option value="">Select a list...</option>
                            <?php 
                                //for gravity forms, the selected will be done by JavaScript
                                echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'BLACK_LIST', '' ); 
                            ?>
                        </select>
                        <select class="bsk-gfbl-comparison" onchange="SetFieldProperty('bsk_gfbl_apply_blacklist_Comparison', jQuery(this).val());" style="margin-top:10px; display:none;">
                            <option value="">Select comparison...</option>
                            <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_comparison(); ?>
                        </select>
                    </li>
                    <li class="bsk-gfbl-apply-white-list-field-setting" style="display:list-item;">
                        <input type="checkbox" class="toggle_setting" id="bsk_gfblcv_apply_whitelist_chk_ID" />
                        <label for="bsk_gfblcv_apply_whitelist_chk_ID" class="inline">
                            <?php _e("Apply White List", "bsk-gfbl"); ?>
                        </label>
                        <br />
                        <select class="bsk-gfbl-list" style="margin-top:10px; display:none;" disabled>
                                <option value="">Only supported in Pro version...</option>
                            </select>
                            <select class="bsk-gfbl-comparison" style="margin-top:10px; display:none;">
                                <option value="">Select comparison...</option>
                            </select>
                    </li>
                    <li class="bsk-gfbl-apply-email-list-field-setting" style="display:list-item;">
                        <input type="checkbox" class="toggle_setting" id="bsk_gfblcv_apply_emaillist_chk_ID" />
                        <label for="bsk_gfblcv_apply_emaillist_chk_ID" class="inline">
                            <?php _e("Apply Email List", "bsk-gfbl"); ?>
                        </label>
                        <br />
                        <select class="bsk-gfbl-list" style="margin-top:10px; display:none;" disabled>
                            <option value="">Only supported in Pro version...</option>
                        </select>
                        <select class="bsk-gfbl-comparison" style="margin-top:10px; display:none;">
                            <option value="">Select action...</option>
                            <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( '' ); ?>
                        </select>
                    </li>
                    <li class="bsk-gfbl-apply-ip-list-field-setting" style="display:list-item;">
                        <input type="checkbox" class="toggle_setting" id="bsk_gfblcv_apply_iplist_chk_ID" />
                        <label for="bsk_gfblcv_apply_iplist_chk_ID" class="inline">
                            <?php _e("Apply IP List", "bsk-gfbl"); ?>
                        </label>
                         <br />
                        <select class="bsk-gfbl-list" style="margin-top:10px; display:none;" disabled>
                            <option value="">Only supported in Pro version...</option>
                        </select>
                        <select class="bsk-gfbl-comparison" style="margin-top:10px; display:none;">
                            <option value="">Select action...</option>
                            <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( '' ); ?>
                        </select>
                    </li>
					<li class="bsk-gfbl-apply-invit-list-field-setting" style="display:list-item;">
                        <input type="checkbox" class="toggle_setting" id="bsk_gfblcv_apply_invitlist_chk_ID" />
                        <label for="bsk_gfblcv_apply_invitlist_chk_ID" class="inline">
                            <?php _e("Apply Invitation Codes List", "bsk-gfbl"); ?>
                        </label>
                         <br />
                        <select class="bsk-gfbl-list" style="margin-top:10px; display:none;" disabled>
							<option value="">Only supported in Pro version...</option>
                        </select>
                        <select class="bsk-gfbl-comparison" style="margin-top:10px; display:none;">
                            <?php 
                                //for gravity forms, the selected will be done by JavaScript
                                echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( 'ALLOW', true ); 
                            ?>
                        </select>
                    </li>
                    <li class="bsk-gfbl-validation-message-field-setting" style="display:none;">
                        <label class="inline"><?php _e("Validation Message", "bsk-gfbl"); ?> <?php gform_tooltip("bsk_gfblcv_validation_message_tip") ?></label>
                        <input type="text" class="fieldwidth-2" value="" readonly placeholder="Only supported in Pro version" />
                    </li>
                </ul>
            </div>
            <div class="bsk_gfblcv_field_multiple_inputs_container">
                <div class="bsk_gfblcv_field_multiple_inputs_list_select_container"></div>
                <ul class="bsk_gfblcv_validation_message_label_container">
                    <li><label class="inline"><?php _e("Validation Message", "bsk-gfbl"); ?> <?php gform_tooltip("bsk_gfblcv_validation_message_tip") ?></label></li>
                </ul>
                <div class="bsk_gfblcv_field_multiple_inputs_validation_message_container"></div>
            </div>
        </li>
        <?php
        }else{
            
            $form_settings_url = admin_url( sprintf( 'admin.php?page=gf_edit_forms&view=settings&subview=bsk_gfblcv_form_settings&id=%d', $form_id ) );
        ?>
        <li class="bsk-gfbl-field-setting field_setting" style="display:list-item;">
            <label for="bsk-gfbl" class="section_label">BSK Blacklist<?php gform_tooltip("bsk_gfblcv_form_field_section_label") ?></label>
            <p><a href="<?php echo $form_settings_url; ?>">Enable for this form</a></p>
        </li>
        <?php
        }
	}
	
	/*
	 * render some custom JS to get the settings to work
	 */
	function bsk_gfblcv_render_editor_js(){
		?>
		<script type='text/javascript'>

			jQuery(document).bind("gform_load_field_settings", function(event, field, form){

				var bsk_gfblcv_setting_container = jQuery(".bsk-gfbl-field-setting");
				if( !bsk_gfblcv_setting_container ){
					return;
				}
				jQuery( ".bsk_gfblcv_field_single_input_container").hide();
				jQuery( ".bsk_gfblcv_field_multiple_inputs_container").hide();
				
				if( field['displayOnly'] || field['type'] == 'fileupload' ){
					//show the setting container!
					bsk_gfblcv_setting_container.hide();
				}else{
					//show the setting container!
					bsk_gfblcv_setting_container.show();
				}
				
				if( field['type'] == 'name' || field['type'] == 'address' ){
					//create fields map
                    var table_header = '<table class="default_input_values striped"><tbody>';
					var table_body = '<tr><td><strong>Field</strong></td><td colspan="3"><strong>List type & comparison</strong></td></tr>';
					jQuery.each( field['inputs'], function(key, input_obj){
						var id_str = input_obj['id'].replace( '.', '_' );
						table_body += '<tr class="bsk_gfblcv_multiple_fields_row" id="bsk_gfblcv_multiple_fields_row_' + id_str + '" data-input_id="' + input_obj['id'] + '">' + 
									  '<td style="width:20%"><label class="inline">' + input_obj['label'] + '</label></td>' +
									  '<td style="width:20%">' + 
                                      '<select class="bsk_gfblcv_list_type_select" field-id="' + input_obj['id'] + '">' + 
									  '<option value="">Type...</option>' +
									  '<option value="BLACK_LIST">Blacklist</option>' +
									  '<option value="WHITE_LIST">White List</option>' +
									  '<option value="EMAIL_LIST">Email List</option>' +
                                      '<option value="IP_LIST">IP List</option>' +
									  '<option value="INVIT_LIST">Invitation Codes List</option>' +
									  '</select>' + 
                                      '</td>' +
									  '<td style="width:40%">' +
									  '<select class="bsk_gfblcv_list_type_BLACK_LIST_id_select" style="width:100%;" field-id="' + input_obj['id'] + '"><option value="">Select...</option>' + '<?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'BLACK_LIST', '' ); ?>' + '</select>' + 
									  '<select class="bsk_gfblcv_list_type_WHITE_LIST_id_select" style="width:100%;display:none;" field-id="' + input_obj['id'] + '" disabled><option value="">Only supported in Pro verison</option></select>' + 
									  '<select class="bsk_gfblcv_list_type_EMAIL_LIST_id_select" style="width:100%;display:none;" field-id="' + input_obj['id'] + '" disabled><option value="">Only supported in Pro verison</option></select>' + 
                                      '<select class="bsk_gfblcv_list_type_IP_LIST_id_select" style="width:100%;display:none;" field-id="' + input_obj['id'] + '" disabled><option value="">Only supported in Pro verison</option></select>' + 
                                      '<select class="bsk_gfblcv_list_type_INVIT_LIST_id_select" style="width:100%;display:none;" field-id="' + input_obj['id'] + '"><option value="">Select...</option>' + '<?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'INVIT_LIST', '' ); ?>' + '</select>' + 
									  '</td>' + 
									  '<td style="width:20%" class="bsk-gfblcv-comparison-selects-container">' +
									  '<select class="bsk_gfblcv_list_type_BLACK_LIST_comparison_select" style="width:100%;" field-id="' + input_obj['id'] + '"><option value="">Comparison...</option>' + '<?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_comparison(); ?>' + '</select>' + 
									  '<select class="bsk_gfblcv_list_type_WHITE_LIST_comparison_select" style="width:100%;display:none;" field-id="' + input_obj['id'] + '"><option value="">Comparison...</option>' + '<?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_comparison(); ?>' + '</select>' + 
									  '<select class="bsk_gfblcv_list_type_EMAIl_LIST_comparison_select" style="width:100%;display:none;" field-id="' + input_obj['id'] + '"><option value="">Action...</option>' + '<?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( '' ); ?>' + '</select>' + 
                                      '<select class="bsk_gfblcv_list_type_IP_LIST_comparison_select" style="width:100%;display:none;" field-id="' + input_obj['id'] + '"><option value="">Action...</option>' + '<?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( '' ); ?>' + '</select>' + 
                                      '<select class="bsk_gfblcv_list_type_INVIT_LIST_comparison_select" style="width:100%;display:none;" field-id="' + input_obj['id'] + '">' + '<?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( 'ALLOW', true ); ?>' + '</select>' + 
									  '</td>' + 
									  '</tr>';
					});
					var table_footer = '</tbody></table>';
                    jQuery( ".bsk_gfblcv_field_multiple_inputs_list_select_container").html( table_header + table_body + table_footer );
                    
                    //create validation message
                    var table_header = '<table class="default_input_values striped" style="width:100%;"><tbody>';
					var table_body = '<tr><td><strong>Field</strong></td><td><strong>Validation Message</strong></td></tr>';
					jQuery.each( field['inputs'], function(key, input_obj){
						var id_str = input_obj['id'].replace( '.', '_' );
						table_body += '<tr class="bsk_gfblcv_multiple_fields_validation_message_row" id="bsk_gfblcv_multiple_fields_validation_message_row_' + id_str + '" data-input_id="' + input_obj['id'] + '">' + 
									  '<td style="width:20%"><label class="inline">' + input_obj['label'] + '</label></td>' +
									  '<td style="width:80%"><input type="text" class="bsk_gfblcv_field_validation_message" field-id="' + input_obj['id'] + '" style="width:100%;" disabled placeholder="Only supported in Pro version" /></td>' + 
									  '</tr>';
					});
					var table_footer = '</tbody></table>';
                    jQuery( ".bsk_gfblcv_field_multiple_inputs_validation_message_container" ).html( table_header + table_body + table_footer );                   
					
					jQuery( ".bsk_gfblcv_field_multiple_inputs_container").show();
					
					//set val of select
					jQuery.each( field['inputs'], function(key, input_obj){
						
						var id_str = input_obj['id'].replace( '.', '_' );
						var row_container = jQuery("#bsk_gfblcv_multiple_fields_row_" + id_str);

						row_container.find(".bsk_gfblcv_list_type_select").val( "" );
						
						row_container.find(".bsk_gfblcv_list_type_BLACK_LIST_id_select").val( "" );
						row_container.find(".bsk_gfblcv_list_type_WHITE_LIST_id_select").hide().val( "" );
						row_container.find(".bsk_gfblcv_list_type_EMAIL_LIST_id_select").hide().val( "" );
                        row_container.find(".bsk_gfblcv_list_type_IP_LIST_id_select").hide().val( "" );
						row_container.find(".bsk_gfblcv_list_type_BLACK_LIST_comparison_select").val( "" );
						row_container.find(".bsk_gfblcv_list_type_WHITE_LIST_comparison_select").hide().val( "" );
						row_container.find(".bsk_gfblcv_list_type_EMAIl_LIST_comparison_select").hide().val( "" );
                        row_container.find(".bsk_gfblcv_list_type_IP_LIST_comparison_select").hide().val( "" );
						row_container.find(".bsk_gfblcv_list_type_INVIT_LIST_comparison_select").hide().val( "ALLOW" );
						
						//get saved black list
						var property_key = 'bsk_gfbl_apply_blacklist_Property_' + input_obj['id'];
						var comparison_key = 'bsk_gfbl_apply_blacklist_Comparison_' + input_obj['id'];
						var blacklist_Property = (typeof field[property_key] != 'undefined' && field[property_key] != '') ? field[property_key] : false;
						var blacklist_Comparison = (typeof field[comparison_key] != 'undefined' && field[comparison_key] != '') ? field[comparison_key] : false;
                        
                        var blacklist_options_array = [];
                        row_container.find(".bsk_gfblcv_list_type_BLACK_LIST_id_select > option").each(function(){
                           if( jQuery(this).val() == "" ){
                               return;
                           }
                           blacklist_options_array.push( jQuery(this).val() ); 
                        });
						if( blacklist_Property && 
                            blacklist_Comparison && 
                            jQuery.inArray( blacklist_Property, blacklist_options_array ) != -1 ){
							row_container.find(".bsk_gfblcv_list_type_select").val( "BLACK_LIST" );
							row_container.find(".bsk_gfblcv_list_type_BLACK_LIST_id_select").val( blacklist_Property );
							row_container.find(".bsk_gfblcv_list_type_BLACK_LIST_id_select").show();
							row_container.find(".bsk_gfblcv_list_type_WHITE_LIST_id_select").hide();
							row_container.find(".bsk_gfblcv_list_type_EMAIL_LIST_id_select").hide();
                            row_container.find(".bsk_gfblcv_list_type_IP_LIST_id_select").hide();
							row_container.find(".bsk_gfblcv_list_type_INVIT_LIST_id_select").hide();
							
							row_container.find(".bsk_gfblcv_list_type_BLACK_LIST_comparison_select").val( blacklist_Comparison );
							row_container.find(".bsk_gfblcv_list_type_BLACK_LIST_comparison_select").show();
							row_container.find(".bsk_gfblcv_list_type_WHITE_LIST_comparison_select").hide();
							row_container.find(".bsk_gfblcv_list_type_EMAIl_LIST_comparison_select").hide();
                            row_container.find(".bsk_gfblcv_list_type_IP_LIST_comparison_select").hide();
							row_container.find(".bsk_gfblcv_list_type_INVIT_LIST_comparison_select").hide();
						}
						
						//get saved white list
						var property_key = 'bsk_gfbl_apply_white_list_Property_' + input_obj['id'];
						var comparison_key = 'bsk_gfbl_apply_white_list_Comparison_' + input_obj['id'];
						var whitelist_Property = (typeof field[property_key] != 'undefined' && field[property_key] != '') ? field[property_key] : false;
						var whitelist_Comparison = (typeof field[comparison_key] != 'undefined' && field[comparison_key] != '') ? field[comparison_key] : false;
                        
                        var whitelist_options_array = [];
                        row_container.find(".bsk_gfblcv_list_type_WHITE_LIST_id_select > option").each(function(){
                           if( jQuery(this).val() == "" ){
                               return;
                           }
                           whitelist_options_array.push( jQuery(this).val() ); 
                        });
						if( whitelist_Property && 
                            whitelist_Comparison &&
                            jQuery.inArray( whitelist_Property, whitelist_options_array ) != -1 ){
							row_container.find(".bsk_gfblcv_list_type_select").val( "WHITE_LIST" );
							row_container.find(".bsk_gfblcv_list_type_WHITE_LIST_id_select").val( whitelist_Property );
							row_container.find(".bsk_gfblcv_list_type_BLACK_LIST_id_select").hide();
							row_container.find(".bsk_gfblcv_list_type_WHITE_LIST_id_select").show();
							row_container.find(".bsk_gfblcv_list_type_EMAIL_LIST_id_select").hide();
                            row_container.find(".bsk_gfblcv_list_type_IP_LIST_id_select").hide();
							row_container.find(".bsk_gfblcv_list_type_INVIT_LIST_id_select").hide();
							
							row_container.find(".bsk_gfblcv_list_type_WHITE_LIST_comparison_select").val( whitelist_Comparison );
							row_container.find(".bsk_gfblcv_list_type_BLACK_LIST_comparison_select").hide();
							row_container.find(".bsk_gfblcv_list_type_WHITE_LIST_comparison_select").show();
							row_container.find(".bsk_gfblcv_list_type_EMAIl_LIST_comparison_select").hide();
                            row_container.find(".bsk_gfblcv_list_type_IP_LIST_comparison_select").hide();
							row_container.find(".bsk_gfblcv_list_type_INVIT_LIST_comparison_select").hide();
						}
						
						//get saved email list
						var property_key = 'bsk_gfbl_apply_email_list_Property_' + input_obj['id'];
						var comparison_key = 'bsk_gfbl_apply_email_list_Comparison_' + input_obj['id'];
						var emaillist_Property = (typeof field[property_key] != 'undefined' && field[property_key] != '') ? field[property_key] : false;
						var emaillist_Comparison = (typeof field[comparison_key] != 'undefined' && field[comparison_key] != '') ? field[comparison_key] : false;
                        
                        var emaillist_options_array = [];
                        row_container.find(".bsk_gfblcv_list_type_EMAIL_LIST_id_select > option").each(function(){
                           if( jQuery(this).val() == "" ){
                               return;
                           }
                           emaillist_options_array.push( jQuery(this).val() ); 
                        });
						if( emaillist_Property && 
                            emaillist_Comparison &&
                            jQuery.inArray( emaillist_Property, emaillist_options_array ) != -1 ){
							row_container.find(".bsk_gfblcv_list_type_select").val( "EMAIL_LIST" );
							row_container.find(".bsk_gfblcv_list_type_EMAIL_LIST_id_select").val( emaillist_Property );
							row_container.find(".bsk_gfblcv_list_type_BLACK_LIST_id_select").hide();
							row_container.find(".bsk_gfblcv_list_type_WHITE_LIST_id_select").hide();
							row_container.find(".bsk_gfblcv_list_type_EMAIL_LIST_id_select").show();
                            row_container.find(".bsk_gfblcv_list_type_IP_LIST_id_select").hide();
							row_container.find(".bsk_gfblcv_list_type_INVIT_LIST_id_select").hide();

							row_container.find(".bsk_gfblcv_list_type_EMAIl_LIST_comparison_select").val( emaillist_Comparison );
							row_container.find(".bsk_gfblcv_list_type_BLACK_LIST_comparison_select").hide();
							row_container.find(".bsk_gfblcv_list_type_WHITE_LIST_comparison_select").hide();
							row_container.find(".bsk_gfblcv_list_type_EMAIl_LIST_comparison_select").show();
                            row_container.find(".bsk_gfblcv_list_type_IP_LIST_comparison_select").hide();
							row_container.find(".bsk_gfblcv_list_type_INVIT_LIST_comparison_select").hide();
						}
                        
                        //get saved IP list
						var property_key = 'bsk_gfbl_apply_ip_list_Property_' + input_obj['id'];
						var comparison_key = 'bsk_gfbl_apply_ip_list_Comparison_' + input_obj['id'];
						var ip_Property = (typeof field[property_key] != 'undefined' && field[property_key] != '') ? field[property_key] : false;
						var ip_Comparison = (typeof field[comparison_key] != 'undefined' && field[comparison_key] != '') ? field[comparison_key] : false;
                        
                        var iplist_options_array = [];
                        row_container.find(".bsk_gfblcv_list_type_IP_LIST_id_select > option").each(function(){
                           if( jQuery(this).val() == "" ){
                               return;
                           }
                           iplist_options_array.push( jQuery(this).val() ); 
                        });
						if( ip_Property && 
                            ip_Comparison &&
                            jQuery.inArray( ip_Property, iplist_options_array ) != -1 ){
							row_container.find(".bsk_gfblcv_list_type_select").val( "IP_LIST" );
							row_container.find(".bsk_gfblcv_list_type_IP_LIST_id_select").val( ip_Property );
							row_container.find(".bsk_gfblcv_list_type_BLACK_LIST_id_select").hide();
							row_container.find(".bsk_gfblcv_list_type_WHITE_LIST_id_select").hide();
							row_container.find(".bsk_gfblcv_list_type_EMAIL_LIST_id_select").hide();
                            row_container.find(".bsk_gfblcv_list_type_IP_LIST_id_select").show();
							row_container.find(".bsk_gfblcv_list_type_INVIT_LIST_id_select").hide();

							row_container.find(".bsk_gfblcv_list_type_EMAIl_LIST_comparison_select").val( emaillist_Comparison );
							row_container.find(".bsk_gfblcv_list_type_BLACK_LIST_comparison_select").hide();
							row_container.find(".bsk_gfblcv_list_type_WHITE_LIST_comparison_select").hide();
							row_container.find(".bsk_gfblcv_list_type_EMAIl_LIST_comparison_select").hide();
                            row_container.find(".bsk_gfblcv_list_type_IP_LIST_comparison_select").show();
							row_container.find(".bsk_gfblcv_list_type_INVIT_LIST_comparison_select").hide();
						}
                        
                        
                        //validation message
                        var row_container = jQuery("#bsk_gfblcv_multiple_fields_validation_message_row_" + id_str);
                        var validation_message_Property_key = 'bsk_gfblcv_validation_message_' + input_obj['id'];
						var validation_message_Property = (typeof field[validation_message_Property_key] != 'undefined' && field[validation_message_Property_key] != '') ? field[validation_message_Property_key] : false;

						if( validation_message_Property ){
							row_container.find(".bsk_gfblcv_field_validation_message").val( validation_message_Property );
						}
					});
				}else{
					jQuery( ".bsk_gfblcv_field_single_input_container").show();
					
                    var blacklist_checked = false;
                    var whitelist_checked = false;
                    var emaillist_checked = false;
                    var iplist_checked = false;
                    var invitlist_checked = false;
                    
					var apply_blacklist_setting_container = jQuery(".bsk-gfbl-apply-blacklist-field-setting");
					//get the saved blacklist
					var blacklist_Property = (typeof field['bsk_gfbl_apply_blacklist_Property'] != 'undefined' && field['bsk_gfbl_apply_blacklist_Property'] != '') ? field['bsk_gfbl_apply_blacklist_Property'] : false;
					var blacklist_Comparison = (typeof field['bsk_gfbl_apply_blacklist_Comparison'] != 'undefined' && field['bsk_gfbl_apply_blacklist_Comparison'] != '') ? field['bsk_gfbl_apply_blacklist_Comparison'] : false;
                    
                    var blacklist_options_array = [];
                    apply_blacklist_setting_container.find(".bsk-gfbl-list > option").each(function(){
                       if( jQuery(this).val() == "" ){
                           return;
                       }
                       blacklist_options_array.push( jQuery(this).val() ); 
                    });

                    if ( blacklist_Property != false && 
                         blacklist_Comparison != false && 
                        jQuery.inArray( blacklist_Property, blacklist_options_array ) != -1 ) {
						//check the checkbox if previously checked
						apply_blacklist_setting_container.find("input:checkbox").attr("checked", "checked");
						//set the list select and show
						apply_blacklist_setting_container.find(".bsk-gfbl-list").val( blacklist_Property ).show();
						apply_blacklist_setting_container.find(".bsk-gfbl-comparison").val( blacklist_Comparison ).show();
                        
                        blacklist_checked = true;
					} else {
						apply_blacklist_setting_container.find("input:checkbox").removeAttr("checked");
						apply_blacklist_setting_container.find(".bsk-gfbl-list").val('').hide();
						apply_blacklist_setting_container.find(".bsk-gfbl-comparison").val('').hide();
					}
					
					var apply_white_list_setting_container = jQuery(".bsk-gfbl-apply-white-list-field-setting");
					//get the saved white list
					var white_list_Property = (typeof field['bsk_gfbl_apply_white_list_Property'] != 'undefined' && field['bsk_gfbl_apply_white_list_Property'] != '') ? field['bsk_gfbl_apply_white_list_Property'] : false;
					var white_list_Comparison = (typeof field['bsk_gfbl_apply_white_list_Comparison'] != 'undefined' && field['bsk_gfbl_apply_white_list_Comparison'] != '') ? field['bsk_gfbl_apply_white_list_Comparison'] : false;
                    
                    var white_list_options_array = [];
                    apply_white_list_setting_container.find(".bsk-gfbl-list > option").each(function(){
                       if( jQuery(this).val() == "" ){
                           return;
                       }
                       white_list_options_array.push( jQuery(this).val() ); 
                    });
					if ( white_list_Property != false && 
                         white_list_Comparison != false && 
                        jQuery.inArray( white_list_Property, white_list_options_array ) != -1 ) {
						//check the checkbox if previously checked
						apply_white_list_setting_container.find("input:checkbox").attr("checked", "checked");
						//set the list select and show
						apply_white_list_setting_container.find(".bsk-gfbl-list").val( white_list_Property ).show();
						apply_white_list_setting_container.find(".bsk-gfbl-comparison").val( white_list_Comparison ).show();
                        
                        whitelist_checked = true;
					} else {
						apply_white_list_setting_container.find("input:checkbox").removeAttr("checked");
						apply_white_list_setting_container.find(".bsk-gfbl-list").val('').hide();
						apply_white_list_setting_container.find(".bsk-gfbl-comparison").val('').hide();
					}
					
					var apply_email_list_setting_container = jQuery(".bsk-gfbl-apply-email-list-field-setting");
					//get the saved email list
					var email_list_Property = (typeof field['bsk_gfbl_apply_email_list_Property'] != 'undefined' && field['bsk_gfbl_apply_email_list_Property'] != '') ? field['bsk_gfbl_apply_email_list_Property'] : false;
					var email_list_Action = (typeof field['bsk_gfbl_apply_email_list_Comparison'] != 'undefined' && field['bsk_gfbl_apply_email_list_Comparison'] != '') ? field['bsk_gfbl_apply_email_list_Comparison'] : false;
                    
                    var email_list_options_array = [];
                    apply_email_list_setting_container.find(".bsk-gfbl-list > option").each(function(){
                       if( jQuery(this).val() == "" ){
                           return;
                       }
                       email_list_options_array.push( jQuery(this).val() ); 
                    });

                    if ( email_list_Property != false && 
                         email_list_Action != false && 
                        jQuery.inArray( email_list_Property, email_list_options_array ) != -1 ) {
						//check the checkbox if previously checked
						apply_email_list_setting_container.find("input:checkbox").attr("checked", "checked");
						//set the list select and show
                        apply_email_list_setting_container.find(".bsk-gfbl-list").show();

                        apply_email_list_setting_container.find(".bsk-gfbl-list").val( email_list_Property );
                        apply_email_list_setting_container.find(".bsk-gfbl-comparison").show();
						apply_email_list_setting_container.find(".bsk-gfbl-comparison").val( email_list_Action );

                        emaillist_checked = true;
					} else {
						apply_email_list_setting_container.find("input:checkbox").removeAttr("checked");
						apply_email_list_setting_container.find(".bsk-gfbl-list").val('').hide();
						apply_email_list_setting_container.find(".bsk-gfbl-comparison").val('').hide();
					}
                    
                    var apply_ip_list_setting_container = jQuery(".bsk-gfbl-apply-ip-list-field-setting");
					//get the saved email list
					var ip_list_Property = (typeof field['bsk_gfbl_apply_ip_list_Property'] != 'undefined' && field['bsk_gfbl_apply_ip_list_Property'] != '') ? field['bsk_gfbl_apply_ip_list_Property'] : false;
					var ip_list_Action = (typeof field['bsk_gfbl_apply_ip_list_Comparison'] != 'undefined' && field['bsk_gfbl_apply_ip_list_Comparison'] != '') ? field['bsk_gfbl_apply_ip_list_Comparison'] : false;
                    
                    var ip_list_options_array = [];
                    apply_ip_list_setting_container.find(".bsk-gfbl-list > option").each(function(){
                       if( jQuery(this).val() == "" ){
                           return;
                       }
                       ip_list_options_array.push( jQuery(this).val() ); 
                    });
					if ( ip_list_Property != false && 
                         ip_list_Action != false &&
                        jQuery.inArray( ip_list_Property, ip_list_options_array ) != -1 ) {
						//check the checkbox if previously checked
						apply_ip_list_setting_container.find("input:checkbox").attr("checked", "checked");
						//set the list select and show
						apply_ip_list_setting_container.find(".bsk-gfbl-list").val( ip_list_Property ).show();
						apply_ip_list_setting_container.find(".bsk-gfbl-comparison").val( ip_list_Action ).show();
                        
                        iplist_checked = true;
					} else {
						apply_ip_list_setting_container.find("input:checkbox").removeAttr("checked");
						apply_ip_list_setting_container.find(".bsk-gfbl-list").val('').hide();
						apply_ip_list_setting_container.find(".bsk-gfbl-comparison").val('').hide();
					}

					var apply_invit_list_setting_container = jQuery(".bsk-gfbl-apply-invit-list-field-setting");
					//get the saved invit list
					var invit_list_Property = (typeof field['bsk_gfbl_apply_invit_list_Property'] != 'undefined' && field['bsk_gfbl_apply_invit_list_Property'] != '') ? field['bsk_gfbl_apply_invit_list_Property'] : false;
					var invit_list_Action = (typeof field['bsk_gfbl_apply_invit_list_Comparison'] != 'undefined' && field['bsk_gfbl_apply_invit_list_Comparison'] != '') ? field['bsk_gfbl_apply_invit_list_Comparison'] : false;
                    
                    var invit_list_options_array = [];
                    apply_invit_list_setting_container.find(".bsk-gfbl-list > option").each(function(){
                       if( jQuery(this).val() == "" ){
                           return;
                       }
                       invit_list_options_array.push( jQuery(this).val() ); 
                    });
					if ( invit_list_Property != false && 
                         invit_list_Action != false &&
                        jQuery.inArray( invit_list_Property, invit_list_options_array ) != -1 ) {
						//check the checkbox if previously checked
						apply_invit_list_setting_container.find("input:checkbox").attr("checked", "checked");
						//set the list select and show
						apply_invit_list_setting_container.find(".bsk-gfbl-list").val( invit_list_Property ).show();
						apply_invit_list_setting_container.find(".bsk-gfbl-comparison").val( invit_list_Action ).show();
                        
                        invitlist_checked = true;
					} else {
						apply_invit_list_setting_container.find("input:checkbox").removeAttr("checked");
						apply_invit_list_setting_container.find(".bsk-gfbl-list").val('').hide();
						apply_invit_list_setting_container.find(".bsk-gfbl-comparison").val('ALLOW').hide();
					}
                    
                    if( blacklist_checked || whitelist_checked || emaillist_checked || iplist_checked || invitlist_checked ){
                        //enable validation message
                        bsk_gfblcv_disable_or_enable_validation_message( false );
                    }
                    
                    //get saved validation message
                    var validation_message_Property = (typeof field['bsk_gfblcv_validation_message'] != 'undefined' ) ? field['bsk_gfblcv_validation_message'] : '';
                    var validation_message_enable_Property = (typeof field['bsk_gfblcv_validation_message_enable'] != 'undefined' && field['bsk_gfblcv_validation_message_enable'] != '') ? field['bsk_gfblcv_validation_message_enable'] : false;
                    if( validation_message_enable_Property ){
                        bsk_gfblcv_validation_message_val( validation_message_Property );
                    }else{
                        //use default validation message
                        if( blacklist_checked ){
                            bsk_gfblcv_validation_message_val( form['bsk_gfblcv_form_settings']['blacklist_message'] );
                        }else if( whitelist_checked ){
                            bsk_gfblcv_validation_message_val( form['bsk_gfblcv_form_settings']['whitelist_message'] );
                        }else if( emaillist_checked ){
                            bsk_gfblcv_validation_message_val( form['bsk_gfblcv_form_settings']['emaillist_message'] );
                        }else if( iplist_checked ){
                            bsk_gfblcv_validation_message_val( form['bsk_gfblcv_form_settings']['iplist_message'] );
                        }
                    }
				}
			});

			
			jQuery(".bsk-gfbl-apply-blacklist-field-setting input:checkbox").click(function() {
				var checked = jQuery(this).is(":checked");
				var select = jQuery(this).parent(".bsk-gfbl-apply-blacklist-field-setting:first").find("select");
				if( checked ){
					select.slideDown();
					
					//uncheck Whitle list
					var white_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-white-list-field-setting:first");
					var white_list_container_checkbox = white_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Comparison','' );
					if ( white_list_container_checkbox.is(":checked") ) {
						white_list_container_checkbox.removeAttr('checked');
						
						var white_list_select = white_list_container.find("select");
						white_list_select.slideUp();
					}
					
					//uncheck Email list
					var email_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-email-list-field-setting:first");
					var email_list_container_checkbox = email_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Comparison','' );
					if ( email_list_container_checkbox.is(":checked") ) {
						email_list_container_checkbox.removeAttr('checked');
						
						var email_list_select = email_list_container.find("select");
						email_list_select.slideUp();
					}
                    
                    //uncheck ip list
					var ip_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-ip-list-field-setting:first");
					var ip_list_container_checkbox = ip_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_ip_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_ip_list_Comparison','' );
					if ( ip_list_container_checkbox.is(":checked") ) {
						ip_list_container_checkbox.removeAttr('checked');
						
						var ip_list_select = ip_list_container.find("select");
						ip_list_select.slideUp();
					}

					//uncheck invit list
					var invit_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-invit-list-field-setting:first");
					var invit_list_container_checkbox = invit_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_invit_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_invit_list_Comparison','' );
					if ( invit_list_container_checkbox.is(":checked") ) {
						invit_list_container_checkbox.removeAttr('checked');
						
						var invit_list_select = invit_list_container.find("select");
						invit_list_select.slideUp();
					}
                    
                    //enable validation message
                    bsk_gfblcv_disable_or_enable_validation_message( false );
				} else {
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Property', '' );
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Comparison', '' );
                    
                    //disable validation message
                    bsk_gfblcv_disable_or_enable_validation_message( true );
                    
					select.slideUp();
				}
			});
			
			jQuery(".bsk-gfbl-apply-white-list-field-setting input:checkbox").click(function() {
				var checked = jQuery(this).is(":checked");
				var select = jQuery(this).parent(".bsk-gfbl-apply-white-list-field-setting:first").find("select");
				if( checked ){
					select.slideDown();
					
					//uncheck Blacklist
					var blacklist_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-blacklist-field-setting:first");
					var blacklist_container_checkbox = blacklist_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Comparison','' );
					if ( blacklist_container_checkbox.is(":checked") ) {
						blacklist_container_checkbox.removeAttr('checked');
						
						var blacklist_select = blacklist_container.find("select");
						blacklist_select.slideUp();
					}
					
					//uncheck Email list
					var email_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-email-list-field-setting:first");
					var email_list_container_checkbox = email_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Comparison','' );
					if ( email_list_container_checkbox.is(":checked") ) {
						
						email_list_container_checkbox.removeAttr('checked');
						var email_list_select = email_list_container.find("select");
						email_list_select.slideUp();
					}
                    
                    //uncheck ip list
					var ip_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-ip-list-field-setting:first");
					var ip_list_container_checkbox = ip_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_ip_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_ip_list_Comparison','' );
					if ( ip_list_container_checkbox.is(":checked") ) {
						ip_list_container_checkbox.removeAttr('checked');
						
						var ip_list_select = ip_list_container.find("select");
						ip_list_select.slideUp();
					}

					//uncheck invit list
					var invit_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-invit-list-field-setting:first");
					var invit_list_container_checkbox = invit_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_invit_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_invit_list_Comparison','' );
					if ( invit_list_container_checkbox.is(":checked") ) {
						invit_list_container_checkbox.removeAttr('checked');
						
						var invit_list_select = invit_list_container.find("select");
						invit_list_select.slideUp();
					}
                    
                    //enable validation message
                    bsk_gfblcv_disable_or_enable_validation_message( false );
				} else {
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Property', '' );
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Comparison', '' );
                    
                    //disable validation message
                    bsk_gfblcv_disable_or_enable_validation_message( true );
                    
					select.slideUp();
				}
			});
			
			jQuery(".bsk-gfbl-apply-email-list-field-setting input:checkbox").click(function() {
				var checked = jQuery(this).is(":checked");
				var select = jQuery(this).parent(".bsk-gfbl-apply-email-list-field-setting:first").find("select");
                var parent_ul_container = jQuery(this).parents("ul:first");
				if( checked ){
					select.slideDown();
					
					//uncheck Blacklist
					var blacklist_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-blacklist-field-setting:first");
					var blacklist_container_checkbox = blacklist_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Comparison','' );
					if ( blacklist_container_checkbox.is(":checked") ) {
						blacklist_container_checkbox.removeAttr('checked');
						
						var blacklist_select = blacklist_container.find("select");
						blacklist_select.slideUp();
					}
					
					//uncheck Whitle list
					var white_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-white-list-field-setting:first");
					var white_list_container_checkbox = white_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Comparison','' );
					if ( white_list_container_checkbox.is(":checked") ) {
						white_list_container_checkbox.removeAttr('checked');
						
						var white_list_select = white_list_container.find("select");
						white_list_select.slideUp();
					}
                    
                    //uncheck ip list
					var ip_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-ip-list-field-setting:first");
					var ip_list_container_checkbox = ip_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_ip_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_ip_list_Comparison','' );
					if ( ip_list_container_checkbox.is(":checked") ) {
						ip_list_container_checkbox.removeAttr('checked');
						
						var ip_list_select = ip_list_container.find("select");
						ip_list_select.slideUp();
					}

					//uncheck invit list
					var invit_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-invit-list-field-setting:first");
					var invit_list_container_checkbox = invit_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_invit_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_invit_list_Comparison','' );
					if ( invit_list_container_checkbox.is(":checked") ) {
						invit_list_container_checkbox.removeAttr('checked');
						
						var invit_list_select = invit_list_container.find("select");
						invit_list_select.slideUp();
					}
                    
                    //enable validation message
                    bsk_gfblcv_disable_or_enable_validation_message( false );
				} else {
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Property', '' );
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Comparison', '' );
                    
                    //disable validation message
                    bsk_gfblcv_disable_or_enable_validation_message( true );
                    
					select.slideUp();
				}
                
                
			});
            
            jQuery(".bsk-gfbl-apply-ip-list-field-setting input:checkbox").click(function() {
				var checked = jQuery(this).is(":checked");
				var select = jQuery(this).parent(".bsk-gfbl-apply-ip-list-field-setting:first").find("select");
                var parent_ul_container = jQuery(this).parents("ul:first");
				if( checked ){
					select.slideDown();
					
					//uncheck Blacklist
					var blacklist_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-blacklist-field-setting:first");
					var blacklist_container_checkbox = blacklist_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Comparison','' );
					if ( blacklist_container_checkbox.is(":checked") ) {
						blacklist_container_checkbox.removeAttr('checked');
						
						var blacklist_select = blacklist_container.find("select");
						blacklist_select.slideUp();
					}
					
					//uncheck Whitle list
					var white_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-white-list-field-setting:first");
					var white_list_container_checkbox = white_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Comparison','' );
					if ( white_list_container_checkbox.is(":checked") ) {
						white_list_container_checkbox.removeAttr('checked');
						
						var white_list_select = white_list_container.find("select");
						white_list_select.slideUp();
					}
                    
                    //uncheck Email list
					var email_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-email-list-field-setting:first");
					var email_list_container_checkbox = email_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Comparison','' );
					if ( email_list_container_checkbox.is(":checked") ) {
						
						email_list_container_checkbox.removeAttr('checked');
						var email_list_select = email_list_container.find("select");
						email_list_select.slideUp();
					}

					//uncheck invit list
					var invit_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-invit-list-field-setting:first");
					var invit_list_container_checkbox = invit_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_invit_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_invit_list_Comparison','' );
					if ( invit_list_container_checkbox.is(":checked") ) {
						invit_list_container_checkbox.removeAttr('checked');
						
						var invit_list_select = invit_list_container.find("select");
						invit_list_select.slideUp();
					}
                    
                    //enable validation message
                    bsk_gfblcv_disable_or_enable_validation_message( false );
				} else {
					SetFieldProperty( 'bsk_gfbl_apply_ip_list_Property', '' );
					SetFieldProperty( 'bsk_gfbl_apply_ip_list_Comparison', '' );
                    
                    //disable validation message
                    bsk_gfblcv_disable_or_enable_validation_message( true );
                    
					select.slideUp();
				}
                
                
			});
            
            jQuery(".bsk-gfbl-apply-invit-list-field-setting input:checkbox").click(function() {
				var checked = jQuery(this).is(":checked");
				var select = jQuery(this).parent(".bsk-gfbl-apply-invit-list-field-setting:first").find("select");
                var parent_ul_container = jQuery(this).parents("ul:first");
				if( checked ){
					select.slideDown();
					
					//uncheck Blacklist
					var blacklist_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-blacklist-field-setting:first");
					var blacklist_container_checkbox = blacklist_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Comparison','' );
					if ( blacklist_container_checkbox.is(":checked") ) {
						blacklist_container_checkbox.removeAttr('checked');
						
						var blacklist_select = blacklist_container.find("select");
						blacklist_select.slideUp();
					}
					
					//uncheck Whitle list
					var white_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-white-list-field-setting:first");
					var white_list_container_checkbox = white_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Comparison','' );
					if ( white_list_container_checkbox.is(":checked") ) {
						white_list_container_checkbox.removeAttr('checked');
						
						var white_list_select = white_list_container.find("select");
						white_list_select.slideUp();
					}
                    
                    //uncheck Email list
					var email_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-email-list-field-setting:first");
					var email_list_container_checkbox = email_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Comparison','' );
					if ( email_list_container_checkbox.is(":checked") ) {
						
						email_list_container_checkbox.removeAttr('checked');
						var email_list_select = email_list_container.find("select");
						email_list_select.slideUp();
					}
                    
                    //uncheck ip list
					var ip_list_container = jQuery(this).parents("ul:first").find(".bsk-gfbl-apply-ip-list-field-setting:first");
					var ip_list_container_checkbox = ip_list_container.find("input.toggle_setting");
					SetFieldProperty( 'bsk_gfbl_apply_ip_list_Property','' );
					SetFieldProperty( 'bsk_gfbl_apply_ip_list_Comparison','' );
					if ( ip_list_container_checkbox.is(":checked") ) {
						ip_list_container_checkbox.removeAttr('checked');
						
						var ip_list_select = ip_list_container.find("select");
						ip_list_select.slideUp();
					}
                    
                    //enable validation message
                    bsk_gfblcv_disable_or_enable_validation_message( false );
                    
                    //for invit list, action always to ALLOW
                    jQuery(this).parent(".bsk-gfbl-comparison").val( 'ALLOW' );
                    SetFieldProperty( 'bsk_gfbl_apply_invit_list_Comparison', 'ALLOW' );
				} else {
					SetFieldProperty( 'bsk_gfbl_apply_invit_list_Property', '' );
					SetFieldProperty( 'bsk_gfbl_apply_invit_list_Comparison', '' );
                    
                    //disable validation message
                    bsk_gfblcv_disable_or_enable_validation_message( true );
                    
					select.slideUp();
				}
                
			});
            
            function bsk_gfblcv_disable_or_enable_validation_message( disable ){
                //check validation message
                var gfbl_container = jQuery(".bsk_gfblcv_field_single_input_container").find("ul");
                if( gfbl_container.find(".bsk-gfbl-validation-message-field-setting:first").length < 1 ){
                    return;
                }
                var validation_message_container = gfbl_container.find(".bsk-gfbl-validation-message-field-setting:first");
                var validation_message_input = validation_message_container.find("input");
                
                if( disable ){
                    validation_message_container.css("display", "none");
                    SetFieldProperty( 'bsk_gfblcv_validation_message_enable', false );
                }else{
                    validation_message_container.css("display", "block");
                    SetFieldProperty( 'bsk_gfblcv_validation_message_enable', true );
                }
            }
            
            function bsk_gfblcv_validation_message_val( value ){
                var gfbl_container = jQuery(".bsk_gfblcv_field_single_input_container").find("ul");
                if( gfbl_container.find(".bsk-gfbl-validation-message-field-setting:first").length < 1 ){
                    return;
                }
                var validation_message_container = gfbl_container.find(".bsk-gfbl-validation-message-field-setting:first");
                var validation_message_input = validation_message_container.find("input");
                validation_message_input.val( value );
            }
			
			//for multiple fields
			jQuery(".bsk_gfblcv_field_multiple_inputs_container").on("change", "select", function() {
				var select_val = jQuery(this).val();
				var field_id = jQuery(this).attr("field-id");
				var class_val = jQuery(this).attr("class");
                var row_obj = jQuery(this).parents(".bsk_gfblcv_multiple_fields_row");
                
				//process list type
				if( class_val == 'bsk_gfblcv_list_type_select' ){
					if( select_val == 'BLACK_LIST' ){
						row_obj.find(".bsk_gfblcv_list_type_BLACK_LIST_id_select").show();
						row_obj.find(".bsk_gfblcv_list_type_WHITE_LIST_id_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_EMAIL_LIST_id_select").hide();
                        row_obj.find(".bsk_gfblcv_list_type_IP_LIST_id_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_INVIT_LIST_id_select").hide();
						
						row_obj.find(".bsk_gfblcv_list_type_BLACK_LIST_comparison_select").show();
						row_obj.find(".bsk_gfblcv_list_type_WHITE_LIST_comparison_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_EMAIl_LIST_comparison_select").hide();
                        row_obj.find(".bsk_gfblcv_list_type_IP_LIST_comparison_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_INVIT_LIST_comparison_select").hide();
					}else if( select_val == 'WHITE_LIST' ){
						row_obj.find(".bsk_gfblcv_list_type_BLACK_LIST_id_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_WHITE_LIST_id_select").show();
						row_obj.find(".bsk_gfblcv_list_type_EMAIL_LIST_id_select").hide();
                        row_obj.find(".bsk_gfblcv_list_type_IP_LIST_id_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_INVIT_LIST_id_select").hide();
						
						row_obj.find(".bsk_gfblcv_list_type_BLACK_LIST_comparison_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_WHITE_LIST_comparison_select").show();
						row_obj.find(".bsk_gfblcv_list_type_EMAIl_LIST_comparison_select").hide();
                        row_obj.find(".bsk_gfblcv_list_type_IP_LIST_comparison_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_INVIT_LIST_comparison_select").hide();
					}else if( select_val == 'EMAIL_LIST' ){
						row_obj.find(".bsk_gfblcv_list_type_BLACK_LIST_id_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_WHITE_LIST_id_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_EMAIL_LIST_id_select").show();
                        row_obj.find(".bsk_gfblcv_list_type_IP_LIST_id_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_INVIT_LIST_id_select").hide();
						
						row_obj.find(".bsk_gfblcv_list_type_BLACK_LIST_comparison_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_WHITE_LIST_comparison_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_EMAIl_LIST_comparison_select").show();
                        row_obj.find(".bsk_gfblcv_list_type_IP_LIST_comparison_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_INVIT_LIST_comparison_select").hide();
					}else if( select_val == 'IP_LIST' ){
						row_obj.find(".bsk_gfblcv_list_type_BLACK_LIST_id_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_WHITE_LIST_id_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_EMAIL_LIST_id_select").hide();
                        row_obj.find(".bsk_gfblcv_list_type_IP_LIST_id_select").show();
						row_obj.find(".bsk_gfblcv_list_type_INVIT_LIST_id_select").hide();
						
						row_obj.find(".bsk_gfblcv_list_type_BLACK_LIST_comparison_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_WHITE_LIST_comparison_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_EMAIl_LIST_comparison_select").hide();
                        row_obj.find(".bsk_gfblcv_list_type_IP_LIST_comparison_select").show();
						row_obj.find(".bsk_gfblcv_list_type_INVIT_LIST_comparison_select").hide();
					}else if( select_val == 'INVIT_LIST' ){
						row_obj.find(".bsk_gfblcv_list_type_BLACK_LIST_id_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_WHITE_LIST_id_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_EMAIL_LIST_id_select").hide();
                        row_obj.find(".bsk_gfblcv_list_type_IP_LIST_id_select").hide();
                        row_obj.find(".bsk_gfblcv_list_type_INVIT_LIST_id_select").show();
						
						row_obj.find(".bsk_gfblcv_list_type_BLACK_LIST_comparison_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_WHITE_LIST_comparison_select").hide();
						row_obj.find(".bsk_gfblcv_list_type_EMAIl_LIST_comparison_select").hide();
                        row_obj.find(".bsk_gfblcv_list_type_IP_LIST_comparison_select").hide();
                        row_obj.find(".bsk_gfblcv_list_type_INVIT_LIST_comparison_select").show();
					}
					
					return;
				}
				
				//process list id
				if( class_val == 'bsk_gfblcv_list_type_BLACK_LIST_id_select' ){
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Property_' + field_id, select_val );
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Property_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Property_' + field_id, '' );
                    SetFieldProperty( 'bsk_gfbl_apply_ip_list_Property_' + field_id, '' );
					
					return;
				}else if( class_val == 'bsk_gfblcv_list_type_WHITE_LIST_id_select' ){
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Property_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Property_' + field_id, select_val );
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Property_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_ip_list_Property_' + field_id, '' );
                    
					return;
				}else if( class_val == 'bsk_gfblcv_list_type_EMAIL_LIST_id_select' ){
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Property_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Property_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Property_' + field_id, select_val );
					SetFieldProperty( 'bsk_gfbl_apply_ip_list_Property_' + field_id, '' );
                    
					return;
				}else if( class_val == 'bsk_gfblcv_list_type_IP_LIST_id_select' ){
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Property_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Property_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Property_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_ip_list_Property_' + field_id, select_val );
                    
					return;
				}else if( class_val == 'bsk_gfblcv_list_type_INVIT_LIST_id_select' ){
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Property_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Property_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Property_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_ip_list_Property_' + field_id, '' );
                    SetFieldProperty( 'bsk_gfbl_apply_invit_list_Property_' + field_id, select_val );
                    
                    //need to set action for invitlist as the select won't change
                    SetFieldProperty( 'bsk_gfbl_apply_invit_list_Comparison_' + field_id, 'ALLOW' );
                    
					return;
				}
				
				//process comparison
				if( class_val == 'bsk_gfblcv_list_type_BLACK_LIST_comparison_select' ){
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Comparison_' + field_id, select_val );
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Comparison_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Comparison_' + field_id, '' );
                    SetFieldProperty( 'bsk_gfbl_apply_ip_list_Comparison_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_invit_list_Comparison_' + field_id, '' );
					
					return;
				}else if( class_val == 'bsk_gfblcv_list_type_WHITE_LIST_comparison_select' ){
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Comparison_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Comparison_' + field_id, select_val );
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Comparison_' + field_id, '' );
                    SetFieldProperty( 'bsk_gfbl_apply_ip_list_Comparison_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_invit_list_Comparison_' + field_id, '' );
					
					return;
				}else if( class_val == 'bsk_gfblcv_list_type_EMAIl_LIST_comparison_select' ){
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Comparison_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Comparison_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Comparison_' + field_id, select_val );
                    SetFieldProperty( 'bsk_gfbl_apply_ip_list_Comparison_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_invit_list_Comparison_' + field_id, '' );
					
					return;
				}else if( class_val == 'bsk_gfblcv_list_type_IP_LIST_comparison_select' ){
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Comparison_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Comparison_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Comparison_' + field_id, '' );
                    SetFieldProperty( 'bsk_gfbl_apply_ip_list_Comparison_' + field_id, select_val );
					SetFieldProperty( 'bsk_gfbl_apply_invit_list_Comparison_' + field_id, '' );
					
					return;
				}else if( class_val == 'bsk_gfblcv_list_type_INVIT_LIST_comparison_select' ){
					SetFieldProperty( 'bsk_gfbl_apply_blacklist_Comparison_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_white_list_Comparison_' + field_id, '' );
					SetFieldProperty( 'bsk_gfbl_apply_email_list_Comparison_' + field_id, '' );
                    SetFieldProperty( 'bsk_gfbl_apply_ip_list_Comparison_' + field_id, '' );
                    SetFieldProperty( 'bsk_gfbl_apply_invit_list_Comparison_' + field_id, select_val );
					
					return;
				}
			});
            
            //for multiple fields validation message
			jQuery(".bsk_gfblcv_field_multiple_inputs_container").on("change", ".bsk_gfblcv_field_validation_message", function() {
                var validation_message = jQuery(this).val();
				var field_id = jQuery(this).attr("field-id");
                SetFieldProperty( 'bsk_gfblcv_validation_message_' + field_id, validation_message );
            });
		</script>
		<?php
	}
	
	/*
     * Add tooltips for the new field values
	 */
	function bsk_gfblcv_add_gf_tooltips($tooltips){
		
		$tooltips["bsk_gfblcv_form_field_section_label"] = 
                     '<span style="display:block;font-weight:bold;margin-top:10px;">Blacklist</span>
                     Block submission if the value of this field match any item in the chosen list.
                     <span style="display:block;font-weight:bold;margin-top:10px;">White List</span>
                     Block all submissions except the value of this field match any item in the chosen list.
                     <span style="display:block;font-weight:bold;margin-top:10px;">Email List</span>
                     Block or allow sumbmission if the value of this field match any item in the chosen list
                     <span style="display:block;font-weight:bold;margin-top:10px;">IP List</span>
                     Block or allow sumbmission if the visiotr\'s IP address any item in the chosen list. For the entire form you just need apply one field with IP List
                     <span style="display:block;font-weight:bold;margin-top:10px;">Custom Validation</span>
                     Block sumbmission if the imput value doesn\'t match any rule in the chosen list';
        
        $tooltips['bsk_gfblcv_validation_message_tip'] = '[FIELD_LABEL] will be replaced with field label<br />[FIELD_VALUE] will be replaced with field value<br />[VISITOR_IP] will be replaced with visitor\'s IP';
		
		return $tooltips;
	}
	
}
