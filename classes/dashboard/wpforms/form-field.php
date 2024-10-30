<?php

class BSK_GFBLCV_Dashboard_WPForms_Field {
	
	private $_bsk_gfblcv_current_form_id = '';
	
	public function __construct() {
		
        if ( BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_is_form_plugin_supported('WPF') ) {
  
            add_action( 'wpforms_field_options_bottom_advanced-options', array($this, 'bsk_gfblcv_render_field_bsk_blacklist_settings'), 9999 );
            add_action( 'wpforms_field_options_bottom_advanced-options', array($this, 'bsk_gfblcv_render_field_bsk_blacklist_adv_field_settings'), 9999 );
        }
	}
    
    function bsk_gfblcv_render_field_bsk_blacklist_settings( $field ){
        
        if( !in_array( $field['type'], array('text', 'email', 'textarea', 'phone', 'url') ) ){
            return;
        }
        
        $field_id = $field['id'];
        
        $blacklist_checked = '';
        $blacklist_display = 'none';
        $blacklist_selected = isset($field['bsk_gfblcv_blacklist_list']) ? $field['bsk_gfblcv_blacklist_list'] : '';
        $blacklist_comparison = isset($field['bsk_gfblcv_blacklist_comparison']) ? $field['bsk_gfblcv_blacklist_comparison'] : '';
        if( isset($field['bsk_gfblcv_blacklist_chk']) && $field['bsk_gfblcv_blacklist_chk'] == 'YES' ){
            $blacklist_checked = 'checked';
            $blacklist_display = 'block';
        }
        
        $whitelist_checked = '';
        $whitelist_display = 'none';
        $whitelist_selected = isset($field['bsk_gfblcv_whitelist_list']) ? $field['bsk_gfblcv_whitelist_list'] : '';
        $whitelist_comparison = isset($field['bsk_gfblcv_whitelist_comparison']) ? $field['bsk_gfblcv_whitelist_comparison'] : '';
        if( isset($field['bsk_gfblcv_whitelist_chk']) && $field['bsk_gfblcv_whitelist_chk'] == 'YES' ){
            $whitelist_checked = 'checked';
            $whitelist_display = 'block';
        }
        
        $emaillist_checked = '';
        $emaillist_display = 'none';
        $emaillist_selected = isset($field['bsk_gfblcv_emaillist_list']) ? $field['bsk_gfblcv_emaillist_list'] : '';
        $emaillist_comparison = isset($field['bsk_gfblcv_emaillist_comparison']) ? $field['bsk_gfblcv_emaillist_comparison'] : '';
        if( isset($field['bsk_gfblcv_emaillist_chk']) && $field['bsk_gfblcv_emaillist_chk'] == 'YES' ){
            $emaillist_checked = 'checked';
            $emaillist_display = 'block';
        }
        
        $iplist_checked = '';
        $iplist_display = 'none';
        $iplist_selected = isset($field['bsk_gfblcv_iplist_list']) ? $field['bsk_gfblcv_iplist_list'] : '';
        $iplist_comparison = isset($field['bsk_gfblcv_iplist_comparison']) ? $field['bsk_gfblcv_iplist_comparison'] : '';
        if( isset($field['bsk_gfblcv_iplist_chk']) && $field['bsk_gfblcv_iplist_chk'] == 'YES' ){
            $iplist_checked = 'checked';
            $iplist_display = 'block';
        }
                    
        $action_when_hit = array( 'BLOCK' );
        ?>
        <div class="wpforms-field-option-advanced-bskblacklist">
            <h3>BSK Blacklist</h3>
            <div class="wpforms-field-option-row wpforms-field-option-row-bskblacklist-apply-black" id="wpforms-field-option-row-<?php echo $field_id; ?>-bskblacklist-apply-black" data-field-id="<?php echo $field_id; ?>">
                <input type="checkbox" class="wpforms-bskblacklist-field-apply-chk" id="bsk_gfblcv_apply_blacklist_chk_ID_<?php echo $field_id; ?>" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_blacklist_chk]" value="YES" <?php echo $blacklist_checked; ?> data-list-type="black" />
                <label for="bsk_gfblcv_apply_blacklist_chk_ID_<?php echo $field_id; ?>" class="wpforms-field-option-advanced-bskblacklist-inline">
                    <?php esc_html_e("Apply Blacklist", "bsk-gfbl"); ?>
                </label>
                <br />
                <select class="wpforms-bskblacklist-field-apply-list" style="margin-top:10px; display:<?php echo $blacklist_display; ?>;" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_blacklist_list]">
                    <option value="">Select a list...</option>
                    <?php 
                        //for gravity forms, the selected will be done by JavaScript
                        echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'BLACK_LIST', $blacklist_selected ); 
                    ?>
                </select>
                <select class="wpforms-bskblacklist-field-apply-comparison" style="margin-top:10px; display:<?php echo $blacklist_display; ?>;" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_blacklist_comparison]">
                    <option value="">Select comparison...</option>
                    <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_comparison( $blacklist_comparison ); ?>
                </select>
            </div>
            <div class="wpforms-field-option-row wpforms-field-option-row-bskblacklist-apply-white" id="wpforms-field-option-row-<?php echo $field_id; ?>-bskblacklist-apply-white" data-field-id="<?php echo $field_id; ?>">
                <input type="checkbox" class="wpforms-bskblacklist-field-apply-chk" id="bsk_gfblcv_apply_whitelist_chk_ID_<?php echo $field_id; ?>" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_whitelist_chk]" value="YES" data-list-type="white"/>
                <label for="bsk_gfblcv_apply_whitelist_chk_ID_<?php echo $field_id; ?>" class="wpforms-field-option-advanced-bskblacklist-inline">
                    <?php _e("Apply White List", "bsk-gfbl"); ?>
                </label>
                <br />
                <select class="wpforms-bskblacklist-field-apply-list" style="margin-top:10px; display:none;" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_whitelist_list]" disabled>
                    <?php 
                        //for gravity forms, the selected will be done by JavaScript
                        echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'WHITE_LIST', $whitelist_selected ); 
                    ?>
                </select>
                <select class="wpforms-bskblacklist-field-apply-comparison" style="margin-top:10px; display:none;" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_whitelist_comparison]">
                    <option value="">Select comparison...</option>
                    <?php 
                        echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_comparison( $whitelist_comparison ); 
                    ?>
                </select>
            </div>
            <div class="wpforms-field-option-row wpforms-field-option-row-bskblacklist-apply-email" id="wpforms-field-option-row-<?php echo $field_id; ?>-bskblacklist-apply-email" data-field-id="<?php echo $field_id; ?>">
                <input type="checkbox" class="wpforms-bskblacklist-field-apply-chk" id="bsk_gfblcv_apply_emaillist_chk_ID_<?php echo $field_id; ?>" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_emaillist_chk]" value="YES" data-list-type="email" />
                <label for="bsk_gfblcv_apply_emaillist_chk_ID_<?php echo $field_id; ?>" class="wpforms-field-option-advanced-bskblacklist-inline">
                    <?php _e("Apply Email List", "bsk-gfbl"); ?>
                </label>
                <br />
                <select class="wpforms-bskblacklist-field-apply-list" style="margin-top:10px; display:none;" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_emaillist_list]" disabled>
                    <?php 
                        //for gravity forms, the selected will be done by JavaScript
                        echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'EMAIL_LIST', $emaillist_selected ); 
                    ?>
                </select>
                <select class="wpforms-bskblacklist-field-apply-comparison" style="margin-top:10px; display:none;" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_emaillist_comparison]">
                    <option value="">Select action...</option>
                    <?php 
                        //for gravity forms, the selected will be done by JavaScript
                        echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( $emaillist_comparison ); 
                    ?>
                </select>
            </div>
            <div class="wpforms-field-option-row wpforms-field-option-row-bskblacklist-apply-ip" id="wpforms-field-option-row-<?php echo $field_id; ?>-bskblacklist-apply-ip" data-field-id="<?php echo $field_id; ?>">
                <input type="checkbox" class="wpforms-bskblacklist-field-apply-chk" id="bsk_gfblcv_apply_iplist_chk_ID_<?php echo $field_id; ?>" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_iplist_chk]" value="YES" data-list-type="ip" />
                <label for="bsk_gfblcv_apply_iplist_chk_ID_<?php echo $field_id; ?>" class="wpforms-field-option-advanced-bskblacklist-inline">
                    <?php _e("Apply IP List", "bsk-gfbl"); ?>
                </label>
                <br />
                <select class="wpforms-bskblacklist-field-apply-list" style="margin-top:10px; display:none;" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_iplist_list]" disabled>
                    <?php 
                        echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'IP_LIST', $iplist_selected ); 
                    ?>
                </select>
                <select class="wpforms-bskblacklist-field-apply-comparison" style="margin-top:10px; display:none;" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_iplist_comparison]">
                    <option value="">Select action...</option>
                    <?php 
                        echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( $iplist_comparison );
                    ?>
                </select>
            </div>
            <div class="wpforms-field-option-row wpforms-field-option-row-bskblacklist-apply-invit" id="wpforms-field-option-row-<?php echo $field_id; ?>-bskblacklist-apply-invit" data-field-id="<?php echo $field_id; ?>">
                <input type="checkbox" class="wpforms-bskblacklist-field-apply-chk" id="bsk_gfblcv_apply_invitlist_chk_ID_<?php echo $field_id; ?>" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_invitlist_chk]" value="YES" data-list-type="invit" />
                <label for="bsk_gfblcv_apply_invitlist_chk_ID_<?php echo $field_id; ?>" class="wpforms-field-option-advanced-bskblacklist-inline">
                    <?php _e("Apply Invitation Code List", "bsk-gfbl"); ?>
                </label>
                <br />
                <select class="wpforms-bskblacklist-field-apply-list" style="margin-top:10px; display:none; ?>;" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_invitlist_list]" disabled>
                    <?php 
                        echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'INVIT_LIST', $invitlist_selected ); 
                    ?>
                </select>
                <select class="wpforms-bskblacklist-field-apply-comparison" style="margin-top:10px; display:none;" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_invitlist_comparison]">
                    <?php 
                        echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( 'ALLOW', true );
                    ?>
                </select>
            </div>
            <?php
            if( in_array( 'BLOCK', $action_when_hit ) ){
            ?>
            <div class="wpforms-field-option-row wpforms-field-option-row-bskblacklist-validation-message" id="wpforms-field-option-row-<?php echo $field_id; ?>-bskblacklist-validation-message" data-field-id="<?php echo $field_id; ?>">
                <label class="wpforms-field-option-advanced-bskblacklist-inline"><?php _e("Validation Message", "bsk-gfbl"); ?></label>
                <input type="text" class="fieldwidth-2" value="Only availabe in Pro verison" disabled name="fields[<?php echo $field_id; ?>][bsk_gfblcv_validation_message]"/>
                <span class="bsk-gfblcv-wpforms-validation-message-desc">[FIELD_LABEL] will be replaced with field label<br>[FIELD_VALUE] will be replaced with field value<br>[VISITOR_IP] will be replaced with visitor's IP</span>
            </div>
            <?php
            }
            ?>
        </div>
        <?php
    }
  
    function bsk_gfblcv_render_field_bsk_blacklist_adv_field_settings( $field ){
        
        if( !in_array( $field['type'], array('name', 'address') ) ){
            return;
        }
        
        $field_id = $field['id'];
        
        $sub_fields = array();
        $sub_fields_display = array();
        if( $field['type'] == 'name' ){
          $sub_fields = array( 'first' => 'First', 'middle' => 'Middle', 'last' => 'Last' );
          $sub_fields_display = array( 'first' => 'none', 'middle' => 'none', 'last' => 'none' );
          if( $field['format'] == 'first-last' ){
              $sub_fields_display['first'] = 'table-row';
              $sub_fields_display['last'] = 'table-row';
          }else if( $field['format'] == 'simple' ){
              $sub_fields_display['first'] = 'table-row';
              $sub_fields['first'] = 'Name';
          }else{
              $sub_fields_display['first'] = 'table-row';
              $sub_fields_display['middle'] = 'table-row';
              $sub_fields_display['last'] = 'table-row';
          }
        }else if( $field['type'] == 'address' ){
          $sub_fields = array( 
                              'address1' => 'Address Line 1', 
                              'address2' => 'Address Line 2', 
                              'city' => 'City', 
                              'state' => 'State', 
                              'postal' => 'Zip Code', 
                              'country' => 'Country'
                           );
          $sub_fields_display = array( 
                                      'address1' => 'table-row', 
                                      'address2' => 'table-row', 
                                      'city' => 'table-row', 
                                      'state' => 'table-row', 
                                      'postal' => 'table-row', 
                                      'country' => 'table-row' 
                                      );
          if( $field['scheme'] == 'us' ){
              $sub_fields_display['country'] = 'none';
          }else{
              $sub_fields['state'] = 'State / Province / Region';
              $sub_fields['postal'] = 'Postal Code';
          }
        }
        
        
        $bsk_gfblcv_list_type = array();
        foreach( $sub_fields as $sub_field_id => $sub_field_name ){
            $bsk_gfblcv_list_type[$sub_field_id] = isset($field['bsk_gfblcv_'.$sub_field_id.'_list_type']) ? $field['bsk_gfblcv_'.$sub_field_id.'_list_type'] : '';
        }
        
        $bsk_gfblcv_list_id = array();
        foreach( $sub_fields as $sub_field_id => $sub_field_name ){
            $bsk_gfblcv_list_id[$sub_field_id] = isset($field['bsk_gfblcv_'.$sub_field_id.'_list_id']) ? $field['bsk_gfblcv_'.$sub_field_id.'_list_id'] : '';
        }
        
        $bsk_gfblcv_comparison = array();
        foreach( $sub_fields as $sub_field_id => $sub_field_name ){
            $bsk_gfblcv_comparison[$sub_field_id] = isset($field['bsk_gfblcv_'.$sub_field_id.'_comparison']) ? $field['bsk_gfblcv_'.$sub_field_id.'_comparison'] : '';
        }
        
        $bsk_gfblcv_action = array();
        foreach( $sub_fields as $sub_field_id => $sub_field_name ){
            $bsk_gfblcv_action[$sub_field_id] = isset($field['bsk_gfblcv_'.$sub_field_id.'_action']) ? $field['bsk_gfblcv_'.$sub_field_id.'_action'] : '';
        }
        
        $action_when_hit = array( 'BLOCK' );
        
        $list_types_array = array( 'BLACK_LIST' => 'Blacklist', 'WHITE_LIST' => 'White list', 'EMAIL_LIST' => 'Email list', 'IP_LIST' => 'IP list' );
        
        
        ?>
        <div class="wpforms-field-option-advanced-bskblacklist">
            <h3>BSK Blacklist</h3>
                <div class="wpforms-field-option-row wpforms-field-option-row-bskblacklist-multiple-selects" id="wpforms-field-option-row-<?php echo $field_id; ?>-multiple-selects" data-field-id="<?php echo $field_id; ?>">
                    <table style="width: 100%;">
                        <thead>
                            <th>Field</th>
                            <th>List type &amp; comparison</th>
                        </thead>
                        <tbody>
                            <?php
                            foreach( $sub_fields as $sub_field_prefix => $sub_field_name ){
                                $selected_list_id = $bsk_gfblcv_list_id[$sub_field_prefix];
                            ?>
                            <tr class="wpforms-bskblacklist-multiple-selects-tr-<?php echo $sub_field_prefix; ?>" style="display: <?php echo $sub_fields_display[$sub_field_prefix]; ?>;">
                                <td class="wpforms-bskblacklist-multiple-sub-field-name-td-<?php echo $sub_field_prefix; ?>"><?php echo $sub_field_name; ?></td>
                                <td class="wpforms-bskblacklist-multiple-selects-td" data-field-id="<?php echo $field_id; ?>" data-list-id="111<?php echo $selected_list_id; ?>">
                                    <p>
                                        <select class="wpforms-bskblacklist-multiple-list-type" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_<?php echo $sub_field_prefix; ?>_list_type]">
                                            <option value="">Type</option>
                                            <?php 
                                            foreach( $list_types_array as $val => $text ){
                                                $selected = 'none';
                                                if( $bsk_gfblcv_list_type[$sub_field_prefix] == $val ){
                                                    $selected = 'selected';
                                                }
                                                echo '<option value="'.$val.'"'.$selected.'>'.$text.'</option>';
                                            }
                                            ?>
                                        </select>
                                        <span class="wpforms-bskblacklist-multiple-list-type-ajax-loader"><?php echo BSK_GFBLCV::$ajax_loader; ?></span>
                                    </p>
                                    <?php
                                    $list_id_display = 'none';
                                    if( $selected_list_id ){
                                        $list_id_display = 'display';
                                    }
                                    ?>
                                    <p>
                                        <select class="wpforms-bskblacklist-multiple-list-id" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_<?php echo $sub_field_prefix; ?>_list_id]" style="display: <?php echo $list_id_display; ?>;">
                                            <option value="">Select...</option>
                                            <?php
                                            if( $bsk_gfblcv_list_type[$sub_field_prefix] == 'BLACK_LIST' ){
                                                echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'BLACK_LIST', $selected_list_id );
                                            }else if( $bsk_gfblcv_list_type[$sub_field_prefix] == 'WHITE_LIST' ){
                                                echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'WHITE_LIST', $selected_list_id );
                                            }else if( $bsk_gfblcv_list_type[$sub_field_prefix] == 'EMAIL_LIST' ){
                                                echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'EMAIL_LIST', $selected_list_id );
                                            }else if( $bsk_gfblcv_list_type[$sub_field_prefix] == 'IP_LIST' ){
                                                echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'IP_LIST', $selected_list_id ); 
                                            }
                                            ?>
                                        </select>
                                    </p>
                                    <?php
                                    $selected_comparison = $bsk_gfblcv_comparison[$sub_field_prefix];
                                    $selected_action = $bsk_gfblcv_action[$sub_field_prefix];
                                    $comparison_display = 'none';
                                    $action_display = 'none';
                                    if( $bsk_gfblcv_list_type[$sub_field_prefix] == 'BLACK_LIST' || 
                                        $bsk_gfblcv_list_type[$sub_field_prefix] == 'WHITE_LIST' ){
                                        $comparison_display = 'block';
                                    }else if( $bsk_gfblcv_list_type[$sub_field_prefix] == 'EMAIL_LIST' || 
                                              $bsk_gfblcv_list_type[$sub_field_prefix] == 'IP_LIST' ){
                                        $action_display = 'block';
                                    }
                                    ?>
                                    <p>
                                        <select class="wpforms-bskblacklist-multiple-list-comparison" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_<?php echo $sub_field_prefix; ?>_comparison]" style="display: <?php echo $comparison_display; ?>">
                                            <option value="">Select...</option>
                                            <?php
                                            echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_comparison( $selected_comparison ); 
                                            ?>
                                        </select>
                                        <select class="wpforms-bskblacklist-multiple-list-action" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_<?php echo $sub_field_prefix; ?>_action]" style="display: <?php echo $action_display; ?>">
                                            <option value="">Select...</option>
                                            <?php
                                            echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( $selected_action );
                                            ?>
                                        </select>
                                    </p>
                                    <p class="wpforms-bskblacklist-multiple-selects-error-message"></p>
                                    <hr />
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                if( in_array( 'BLOCK', $action_when_hit ) ){
                ?>
                <div class="wpforms-field-option-row wpforms-field-option-row-bskblacklist-multiple-validaiton-message" id="wpforms-field-option-row-<?php echo $field_id; ?>-multiple-validaiton-message" data-field-id="<?php echo $field_id; ?>">
                    <table style="width: 100%;">
                        <thead>
                            <th>Field</th>
                            <th>Validaiton Message</th>
                        </thead>
                        <tbody>
                            <?php
                            foreach( $sub_fields as $sub_field_prefix => $sub_field_name ){
                                $selected_list_id = $bsk_gfblcv_list_id[$sub_field_prefix];
                            ?>
                            <tr class="wpforms-bskblacklist-multiple-validation-message-tr-<?php echo $sub_field_prefix; ?>" style="display: <?php echo $sub_fields_display[$sub_field_prefix]; ?>;">
                                <td class="wpforms-bskblacklist-multiple-sub-field-name-td-<?php echo $sub_field_prefix; ?>"><?php echo $sub_field_name; ?></td>
                                <td class="wpforms-bskblacklist-multiple-validation-message-td" data-field-id="<?php echo $field_id; ?>">
                                    <p>
                                        <input type="text" class="wpforms-bskblacklist-multiple-validation-message" name="fields[<?php echo $field_id; ?>][bsk_gfblcv_<?php echo $sub_field_prefix; ?>_validation_message]" value="Only availabe in Pro verison" disabled />
                                    </p>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                            <tr>
                                <td colspan="2">                                        
                                    <span class="bsk-gfblcv-wpforms-validation-message-desc">[FIELD_LABEL] will be replaced with field label<br>[FIELD_VALUE] will be replaced with field value<br>[VISITOR_IP] will be replaced with visitor's IP</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php
                }
                ?>
            <?php
            $nonce = wp_create_nonce( 'bskblacklist_adv_fields_ajax_nonce_'.$field_id );
            ?>
            <input type="hidden" id="wpforms_field_options_row_bskblacklist_adv_fields_ajax_nonce_<?php echo $field_id; ?>" value="<?php echo $nonce; ?>" />
        </div>
        <?php
    }
}
