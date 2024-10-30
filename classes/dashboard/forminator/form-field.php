<?php

class BSK_GFBLCV_Dashboard_Forminator_Settings_Field_Settings {
	
	function __construct() {
        
	}
	
    function settings( $form_id ) {
        
        $saved_field_settings = get_option( BSK_GFBLCV_Dashboard_Forminator::$_bsk_gfblcv_frmt_field_settings_option_name_prefix . $form_id, false );
        $form_fields_array = BSK_GFBLCV_Dashboard_Common::forminator_get_form_fields( $form_id );

        $settings_data = get_option( BSK_GFBLCV_Dashboard_Forminator::$_bsk_gfblcv_frmt_form_settings_option_name_prefix . $form_id, false );
        ?>
        <div class="gform_panel gform_panel_form_settings bsk-gfblcv-form-settings-container" id="bsk_gfblcv_frmt_form_mappings_ID" style="display: <?php echo $container_display; ?>">
            <hr />
            <h4><?php esc_html_e( 'Field mappings', 'bsk_gfblcv' ); ?></h4>
            <?php  if ( is_array( $settings_data ) && $settings_data['enable'] ) { ?>
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
                    if( $form_fields_array && is_array( $form_fields_array ) && count( $form_fields_array ) > 0 ){
                        foreach( $form_fields_array as $field_id => $field ){
                            $field_settings = $saved_field_settings && isset( $saved_field_settings[$field_id] ) ? $saved_field_settings[$field_id] : false;
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
                        <td><?php echo $field['label']; ?></td>
                        <td>
                            <select class="bsk-gfblcv-frmt-mapping-list-type-select" name="bsk_gfblcv_frmt_list_type_of_<?php echo $field_id; ?>">
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
                            <select class="bsk-gfblcv-frmt-mapping-list-id-select bsk-gfblcv-frmt-blacklist" name="bsk_gfblcv_frmt_blacklist_id_of_<?php echo $field_id; ?>" style="display: <?php echo $list_id_blacklist_display; ?>;">
                                <option value="">Select a list...</option>
                                <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'BLACK_LIST', $list_id ); ?>
                            </select>
                            <select class="bsk-gfblcv-frmt-mapping-list-id-select bsk-gfblcv-frmt-whitelist" name="bsk_gfblcv_frmt_whitelist_id_of_<?php echo $field_id; ?>" style="display: <?php echo $list_id_whitelist_display; ?>;">
                                <option value="">Select a list...</option>
                                <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'WHITE_LIST', $list_id ); ?>
                            </select>
                            <select class="bsk-gfblcv-frmt-mapping-list-id-select bsk-gfblcv-frmt-emaillist" name="bsk_gfblcv_frmt_emaillist_id_of_<?php echo $field_id; ?>" style="display: <?php echo $list_id_emaillist_display; ?>;">
                                <option value="">Select a list...</option>
                                <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'EMAIL_LIST', $list_id ); ?>
                            </select>
                            <select class="bsk-gfblcv-frmt-mapping-list-id-select bsk-gfblcv-frmt-iplist" name="bsk_gfblcv_frmt_iplist_id_of_<?php echo $field_id; ?>" style="display: <?php echo $list_id_iplist_display; ?>;">
                                <option value="">Select a list...</option>
                                <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'IP_LIST', $list_id ); ?>
                            </select>
                            <select class="bsk-gfblcv-frmt-mapping-list-id-select bsk-gfblcv-frmt-invitlist" name="bsk_gfblcv_frmt_invitlist_id_of_<?php echo $field_id; ?>" style="display: <?php echo $list_id_invitlist_display; ?>;">
                                <option value="">Select a list...</option>
                                <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( 'INVIT_LIST', $list_id ); ?>
                            </select>
                            <?php if ( $save_id_error ) echo '<span class="bsk-gfblcv-error-message" style="display:inline-block;">*</span>'; ?>
                        </td>
                        <td>
                            <select class="bsk-gfblcv-frmt-mapping-comparison" name="bsk_gfblcv_frmt_comparison_of_<?php echo $field_id; ?>" style="display: <?php echo $list_comparison_display; ?>;">
                                <option value="">Select comparison...</option>
                                <?php echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_comparison( $list_comparison ); ?>
                            </select>
                            <select class="bsk-gfblcv-frmt-mapping-action" name="bsk_gfblcv_frmt_action_of_<?php echo $field_id; ?>" style="display: <?php echo $list_action_display; ?>;">
                                <?php
                                    echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( $list_comparison ); 
                                ?>
                            </select>
                            <select class="bsk-gfblcv-frmt-mapping-action-for-invit" name="bsk_gfblcv_frmt_action_of_for_invit<?php echo $field_id; ?>" style="display: <?php echo $list_invit_action_display; ?>;">
                                <?php
                                    echo BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_action( 'ALLOW', true ); 
                                ?>
                            </select>
                            <?php if ( $save_comparison_error ) echo '<span class="bsk-gfblcv-error-message" style="display:inline-block;">*</span>'; ?>
                        </td>
                        <td>
                            <input type="text" class="bsk-gfblcv-frmt-validation-message" name="bsk_gfblcv_frmt_validation_message_of_<?php echo $field_id; ?>" value="Only availalbe in Pro version" disabled style="width: 95%; display: <?php echo $validation_message_display; ?>;" />
                        </td>
                    </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
            <?php 
            } else { 
                $settings_tab_url = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['forminator_blacklist'] );
                $settings_tab_url .= '&id=' . $form_id . '&view=settings&target=form-settings';
            ?>
            <p>Please enable the form on <a href="<?php echo $settings_tab_url; ?>">form settings tab</a> first.
            <?php } ?>
        </div>
        <?php
    }
    
}
