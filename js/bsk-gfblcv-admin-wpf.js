jQuery(document).ready( function($) {
   /*
     * wpforms form field
     */
    $( "#wpforms-field-options" ).on( 'click', ".wpforms-bskblacklist-field-apply-chk", function() {
        var checked = $(this).is(":checked");
        if( checked ){
            $(this).parent().find( ".wpforms-bskblacklist-field-apply-list" ).css( "display", "block" );
            $(this).parent().find( ".wpforms-bskblacklist-field-apply-comparison" ).css( "display", "block" );
            
            //unselect and display others
            var list_type = $(this).data( "list-type" );
            var inner_container = $(this).parents( '.wpforms-field-option-advanced-bskblacklist' );
            inner_container.find( ".wpforms-field-option-row" ).each( function( index ){
                if( $(this).hasClass( 'wpforms-field-option-row-bskblacklist-apply-' + list_type ) ){
                    return;
                }
                $(this).find( ".wpforms-bskblacklist-field-apply-chk" ).prop( "checked", false );
                $(this).find( ".wpforms-bskblacklist-field-apply-list" ).val( "" );
                $(this).find( ".wpforms-bskblacklist-field-apply-comparison" ).val( "" );
                $(this).find( ".wpforms-bskblacklist-field-apply-list" ).css( "display", "none" );
                $(this).find( ".wpforms-bskblacklist-field-apply-comparison" ).css( "display", "none" );
            });
        }else{
            $(this).parent().find( ".wpforms-bskblacklist-field-apply-list" ).val( "" );
            $(this).parent().find( ".wpforms-bskblacklist-field-apply-comparison" ).val( "" );
            $(this).parent().find( ".wpforms-bskblacklist-field-apply-list" ).css( "display", "none" );
            $(this).parent().find( ".wpforms-bskblacklist-field-apply-comparison" ).css( "display", "none" );
        }
    });
    
    // wpforms field settings for advanced fields
    $( "#wpforms-field-options" ).on('change', ".wpforms-bskblacklist-multiple-list-type", function(){
        var list_type = $(this).val();
        var td_parent = $(this).parents( ".wpforms-bskblacklist-multiple-selects-td" );
        var list_id_select_obj = td_parent.find( ".wpforms-bskblacklist-multiple-list-id" );
        var ajax_loader_obj = td_parent.find( ".wpforms-bskblacklist-multiple-list-type-ajax-loader" );
        var error_message_obj = td_parent.find( ".wpforms-bskblacklist-multiple-selects-error-message" );
        
        error_message_obj.html( "" );
        
        td_parent.find( ".wpforms-bskblacklist-multiple-list-id" ).css( "display", "none" );
        td_parent.find( ".wpforms-bskblacklist-multiple-list-comparison" ).css( "display", "none" );
        td_parent.find( ".wpforms-bskblacklist-multiple-list-action" ).css( "display", "none" );
        td_parent.find( ".wpforms-bskblacklist-multiple-list-comparison" ).val( "" );
        td_parent.find( ".wpforms-bskblacklist-multiple-list-action" ).val( "" );
        
        if( list_type == '' ){
            ajax_loader_obj.css( "display", "none" );
            return;
        }
        
        var field_id = td_parent.data( "field-id" );
        var list_id = td_parent.data( "list-id" );
        var nonce_val = $( "#wpforms_field_options_row_bskblacklist_adv_fields_ajax_nonce_" + field_id ).val();
        //load lists
        var data = { 
                        action: 'bsk_gfblcv_wpforms_get_list_by_type',
                        fieldid: field_id,
                        type: list_type,
                        listid: list_id,
                        nonce: nonce_val
                   };
        ajax_loader_obj.css( "display", "inline-block" );
        $.post( ajaxurl, data, function( response ) {
            ajax_loader_obj.css( "display", "none" );
            //console.log( response );
            var return_obj = $.parseJSON( response );
            if( return_obj.success == false ){
                error_message_obj.html( return_obj.msg );
                error_message_obj.css( "display", "block" );
                
                return;
            }
            list_id_select_obj.html( return_obj.lists_options );
            list_id_select_obj.css( "display", "block" );
            //show comparison or action
            if( list_type == 'BLACK_LIST' || list_type == 'WHITE_LIST' ){
                td_parent.find( ".wpforms-bskblacklist-multiple-list-comparison" ).css( "display", "block" );
            }else if( list_type == 'EMAIL_LIST' || list_type == 'IP_LIST' ){
                td_parent.find( ".wpforms-bskblacklist-multiple-list-action" ).css( "display", "block" );
            }
        });
        
    });
    
    /*
     * for name format change
     */
    $( ".wpforms-field-option-row-format select").change( function(){
        var selectd_format = $(this).val();
        var field_id = $(this).parents( ".wpforms-field-option-row-format" ).data( "field-id" );
        var field_options_container = $( '#wpforms-field-option-' + field_id );

        field_options_container.find( ".wpforms-bskblacklist-multiple-selects-tr-first" ).css( "display", "none" );
        field_options_container.find( ".wpforms-bskblacklist-multiple-selects-tr-middle" ).css( "display", "none" );
        field_options_container.find( ".wpforms-bskblacklist-multiple-selects-tr-last" ).css( "display", "none" );
        field_options_container.find( ".wpforms-bskblacklist-multiple-validation-message-tr-first" ).css( "display", "none" );
        field_options_container.find( ".wpforms-bskblacklist-multiple-validation-message-tr-middle" ).css( "display", "none" );
        field_options_container.find( ".wpforms-bskblacklist-multiple-validation-message-tr-last" ).css( "display", "none" );
        field_options_container.find( ".wpforms-bskblacklist-multiple-sub-field-name-td-first" ).html( 'First' );
        if( selectd_format == 'first-last' ){
            field_options_container.find( ".wpforms-bskblacklist-multiple-selects-tr-first" ).css( "display", "table-row" );
            field_options_container.find( ".wpforms-bskblacklist-multiple-selects-tr-last" ).css( "display", "table-row" );
            field_options_container.find( ".wpforms-bskblacklist-multiple-validation-message-tr-first" ).css( "display", "table-row" );
            field_options_container.find( ".wpforms-bskblacklist-multiple-validation-message-tr-last" ).css( "display", "table-row" );
        }else if( selectd_format == 'simple' ){
            field_options_container.find( ".wpforms-bskblacklist-multiple-selects-tr-first" ).css( "display", "table-row" );
            field_options_container.find( ".wpforms-bskblacklist-multiple-validation-message-tr-first" ).css( "display", "table-row" );
            field_options_container.find( ".wpforms-bskblacklist-multiple-sub-field-name-td-first" ).html( 'Name' );
        }else{
            field_options_container.find( ".wpforms-bskblacklist-multiple-selects-tr-first" ).css( "display", "table-row" );
            field_options_container.find( ".wpforms-bskblacklist-multiple-selects-tr-middle" ).css( "display", "table-row" );
            field_options_container.find( ".wpforms-bskblacklist-multiple-selects-tr-last" ).css( "display", "table-row" );
            field_options_container.find( ".wpforms-bskblacklist-multiple-validation-message-tr-first" ).css( "display", "table-row" );
            field_options_container.find( ".wpforms-bskblacklist-multiple-validation-message-tr-middle" ).css( "display", "table-row" );
            field_options_container.find( ".wpforms-bskblacklist-multiple-validation-message-tr-last" ).css( "display", "table-row" );
        }
    });
    
    /*
     * for address scheme change
     */
    $( ".wpforms-field-option-row-scheme select").change( function(){
        var selectd_format = $(this).val();
        var field_id = $(this).parents( ".wpforms-field-option-row-scheme" ).data( "field-id" );
        var field_options_container = $( '#wpforms-field-option-' + field_id );

        field_options_container.find( ".wpforms-bskblacklist-multiple-selects-tr-country" ).css( "display", "none" );
        field_options_container.find( ".wpforms-bskblacklist-multiple-validation-message-tr-country" ).css( "display", "none" );
        field_options_container.find( ".wpforms-bskblacklist-multiple-sub-field-name-td-state" ).html( 'State' );
        field_options_container.find( ".wpforms-bskblacklist-multiple-sub-field-name-td-postal" ).html( 'Zip Code' );
        if( selectd_format == 'international' ){
            console.log( field_options_container );
            field_options_container.find( ".wpforms-bskblacklist-multiple-selects-tr-country" ).css( "display", "table-row" );
            field_options_container.find( ".wpforms-bskblacklist-multiple-validation-message-tr-country" ).css( "display", "table-row" );
            field_options_container.find( ".wpforms-bskblacklist-multiple-sub-field-name-td-state" ).html( 'State / Province / Region' );
            field_options_container.find( ".wpforms-bskblacklist-multiple-sub-field-name-td-postal" ).html( 'Postal Code' );
        }
    });
  
  /*
   * form settings
   */
  $( ".bsk-gfblcv-wpforms-eanble-disable-radio" ).change(function () {

      var enable = $("input[type='radio'][name='settings[bsk_gfblcv_form_settings_enable]']:checked").val();
      var form_settings_container = $(this).parents( '.wpforms-panel-content-section-bskblacklist' );

      if( enable == 'DISABLE' ){
          form_settings_container.find( ".bsk-gfblcv-wpform-settings-actions-container" ).css( "display", "none" );
          form_settings_container.find( ".bsk-gfblcv-wpform-settings-blocked-data-container" ).css( "display", "none" );
          form_settings_container.find( ".bsk-gfblcv-wpform-settings-notifications-container" ).css( "display", "none" );
          form_settings_container.find( ".bsk-gfblcv-wpform-settings-confirmations-container" ).css( "display", "none" );
          form_settings_container.find( ".bsk-gfblcv-wpform-settings-entry-container" ).css( "display", "none" );
          form_settings_container.find( ".bsk-gfblcv-wpform-settings-error-message-container" ).css( "display", "none" );

          return;
      }

      form_settings_container.find( ".bsk-gfblcv-wpform-settings-actions-container" ).css( "display", "block" );

      bsk_gfblcv_wpf_control_settings_display( form_settings_container );
  });
  
  $( '.bsk-gfblcv-wpforms-actions' ).change( function(){
    var form_settings_container = $(this).parents( '.wpforms-panel-content-section-bskblacklist' );

    if( $(this).attr( 'id' ) == 'wpforms-panel-field-settings-bsk_gfblcv_form_settings_actions_block-wrap' ){
        if( $(this).find( '#wpforms-panel-field-settings-bsk_gfblcv_form_settings_actions_block' ).is( ':checked' ) ){
            form_settings_container.find( "#wpforms-panel-field-settings-bsk_gfblcv_form_settings_actions_skip" ).prop( 'checked', false );
            form_settings_container.find( "#wpforms-panel-field-settings-bsk_gfblcv_form_settings_actions_confirmation" ).prop( 'checked', false );
        }
    }else if( $(this).attr( 'id' ) == 'wpforms-panel-field-settings-bsk_gfblcv_form_settings_actions_skip-wrap' ){
        if( $(this).find( '#wpforms-panel-field-settings-bsk_gfblcv_form_settings_actions_skip' ).is( ':checked' ) ){
            form_settings_container.find( "#wpforms-panel-field-settings-bsk_gfblcv_form_settings_actions_block" ).prop( 'checked', false );
        }
    }else if( $(this).attr( 'id' ) == 'wpforms-panel-field-settings-bsk_gfblcv_form_settings_actions_confirmation-wrap' ){
        if( $(this).find( '#wpforms-panel-field-settings-bsk_gfblcv_form_settings_actions_confirmation' ).is( ':checked' ) ){
            form_settings_container.find( "#wpforms-panel-field-settings-bsk_gfblcv_form_settings_actions_block" ).prop( 'checked', false );
        }
    }
    
    bsk_gfblcv_wpf_control_settings_display( form_settings_container );
  });
  
  function bsk_gfblcv_wpf_control_settings_display( $root_container_object ){
        
    $root_container_object.find( ".bsk-gfblcv-wpform-settings-blocked-data-container" ).css( "display", "none" );
    $root_container_object.find( ".bsk-gfblcv-wpform-settings-notifications-container" ).css( "display", "none" );
    $root_container_object.find( ".bsk-gfblcv-wpform-settings-confirmations-container" ).css( "display", "none" );
    $root_container_object.find( ".bsk-gfblcv-wpform-settings-entry-container" ).css( "display", "none" );
    $root_container_object.find( ".bsk-gfblcv-wpform-settings-error-message-container" ).css( "display", "none" );
    
    $root_container_object.find( ".bsk-gfblcv-wpforms-notify-administrator-recipient-label" ).css( "display", "none" );
    $root_container_object.find( ".bsk-gfblcv-wpforms-notify-administrator-recipient-label span" ).css( "display", "none" );
    $root_container_object.find( ".bsk-gfblcv-form-settings-notify-administrator-recipient" ).css( "display", "none" );

    var is_action_block = $( "#wpforms-panel-field-settings-bsk_gfblcv_form_settings_actions_block" ).is( ":checked" );
    var is_action_skip = $( "#wpforms-panel-field-settings-bsk_gfblcv_form_settings_actions_skip" ).is( ":checked" );
    var is_action_confirmation = $( "#wpforms-panel-field-settings-bsk_gfblcv_form_settings_actions_confirmation" ).is( ":checked" );
    
    var is_notify_administrator = $( 'input[name="settings[bsk_gfblcv_form_settings_notify_administrators]"]:checked' ).val();
    var is_notify_administrator = is_notify_administrator == 'YES' ? true : false;


    $root_container_object.find( ".bsk-gfblcv-wpform-settings-entry-container" ).css( "display", "block" );

    if( is_action_block ){
        $root_container_object.find( ".bsk-gfblcv-wpform-settings-blocked-data-container" ).css( "display", "block" );
        $root_container_object.find( ".bsk-gfblcv-wpform-settings-error-message-container" ).css( "display", "block" );
        $root_container_object.find( ".bsk-gfblcv-wpform-settings-entry-container" ).css( "display", "none" );
    }

    if( is_action_skip ){
        $root_container_object.find( ".bsk-gfblcv-wpform-settings-notifications-container" ).css( 'display', 'block' );
    }

    if( is_action_confirmation ){
        $root_container_object.find( ".bsk-gfblcv-wpform-settings-confirmations-container" ).css( 'display', 'block' );
    }

    if( is_notify_administrator ){        
        $root_container_object.find( ".bsk-gfblcv-wpforms-notify-administrator-recipient-label" ).css( "display", "block" );
        $root_container_object.find( ".bsk-gfblcv-wpforms-notify-administrator-recipient-label span" ).css( "display", "block" );
        $root_container_object.find( ".bsk-gfblcv-form-settings-notify-administrator-recipient" ).css( "display", "block" );
    }
  }
  
  $( ".bsk-gfblcv-wpforms-notify-administrator-radio" ).click( function(){
      var notify_administrator = $( 'input[name="settings[bsk_gfblcv_form_settings_notify_administrators]"]:checked' ).val();
      var panel_obj = $(this).parents( ".wpforms-panel-content-section-bskblacklist" );

      if( notify_administrator == 'YES' ){
          panel_obj.find( ".bsk-gfblcv-wpforms-notify-administrator-recipient-label" ).css( "display", "block" );
          panel_obj.find( ".bsk-gfblcv-wpforms-notify-administrator-recipient-label span" ).css( "display", "block" );
          panel_obj.find( ".bsk-gfblcv-form-settings-notify-administrator-recipient" ).css( "display", "block" );
      }else{
          panel_obj.find( ".bsk-gfblcv-wpforms-notify-administrator-recipient-label" ).css( "display", "none" );
          panel_obj.find( ".bsk-gfblcv-wpforms-notify-administrator-recipient-label span" ).css( "display", "none" );
          panel_obj.find( ".bsk-gfblcv-form-settings-notify-administrator-recipient" ).css( "display", "none" );
      }
  });
  
  $(".bsk-gfblcv-wpform-settings-confirmations-container").on( 'change', '.bsk-gfblcv-wpforms-confirmation', function(){
      var confirmation_id_full = $(this).attr( 'id' );
      var confirmation_id_num = confirmation_id_full.replace( 'wpforms-panel-field-settings-bsk_gfblcv_form_settings_skip_confirmations_', '' );
      confirmation_id_num = confirmation_id_num.replace( '-wrap', '' );
      var is_checked = $('#wpforms-panel-field-settings-bsk_gfblcv_form_settings_skip_confirmations_'+confirmation_id_num).is( ':checked' );
      $(this).parent().find( '.wpforms-panel-field.bsk-gfblcv-wpforms-confirmation.wpforms-panel-field-checkbox' ).each( function(){
          if( $(this).attr( 'id' ) == confirmation_id_full ){
            return;
          }
          var id_to_unchecked_num = $(this).attr( 'id' ).replace( 'wpforms-panel-field-settings-bsk_gfblcv_form_settings_skip_confirmations_', '' );
          id_to_unchecked_num = id_to_unchecked_num.replace( '-wrap', '' );
          $('#wpforms-panel-field-settings-bsk_gfblcv_form_settings_skip_confirmations_'+id_to_unchecked_num).prop( 'checked', false );
      });
  });
  
  /*
   * WPForms save hook
   */
  if( WPFormsBuilder && WPFormsBuilder != undefined ){
    //console.log( WPFormsBuilder.settings );
    
    $( '#wpforms-builder' ).on( 'wpformsSettingsBlockAdded', function( e, block ) {
        //console.log( block.data( 'block-id' ) );
        //insert
        var block_id = block.data( 'block-id' );
        var block_type = block.data( 'block-type' );
        var block_name = block.find( '.wpforms-builder-settings-block-name' ).html();
        
        if( block_type == 'confirmation' || block_type == 'notification' ){
          var copy_confirmation_html = $( '#wpforms-panel-field-settings-bsk_gfblcv_form_settings_skip_'+block_type+'s_1-wrap' ).html();
          var first_postion = $( '.bsk-gfblcv-wpform-settings-'+block_type+'s-container' ).find( '.bsk-gfblcv-'+block_type+'-insert-position' );

          var to_insert = copy_confirmation_html.replaceAll( block_type+'s_1', block_type+'s_'+block_id );
          to_insert = to_insert.replace( 'checked="checked"', '' );
          to_insert = '<div id="wpforms-panel-field-settings-bsk_gfblcv_form_settings_skip_'+block_type+'s_'+block_id+'-wrap" class="wpforms-panel-field bsk-gfblcv-wpforms-'+block_type+' wpforms-panel-field-checkbox">'+to_insert+'</div>';
          $( to_insert ).insertAfter( first_postion );

          //change name
          $( '#wpforms-panel-field-settings-bsk_gfblcv_form_settings_skip_'+block_type+'s_'+block_id+'-wrap' ).find( 'label' ).html( block_name );
        }
    });
    
    $( '#wpforms-builder' ).on( 'wpformsSettingsBlockDeleted', function( e, blockType, blockId ) {
        //console.log( blockType, blockId );
        
        //delete confirmation
        if( blockType == 'notification' || blockType == 'confirmation' ){
          $( '#wpforms-panel-field-settings-bsk_gfblcv_form_settings_skip_'+blockType+'s_'+blockId+'-wrap' ).remove();
        }
    });
    
  }
    
  $( ".bsk-gfblcv-wpforms-delete-entry-radio" ).change(function () {

      var yes_or_no = $("input[type='radio'][name='settings[bsk_gfblcv_wpform_settings_delete_entry]']:checked").val();
      
      if( yes_or_no == 'YES' ){
        $(this).parents( '.bsk-gfblcv-wpform-settings-entry-container' ).find( '.bsk-gfblcv-tips-box' ).css( 'display', 'block' );
      }else{
        $(this).parents( '.bsk-gfblcv-wpform-settings-entry-container' ).find( '.bsk-gfblcv-tips-box' ).css( 'display', 'none' );
      }
      
  });
  
});
