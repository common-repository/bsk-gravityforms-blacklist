jQuery(document).ready( function($) {
    
    /* Global settings */
	
	$("#bsk_gfblcv_list_edit_form_id").keypress(function(e) {
		var key = e.charCode || e.keyCode || 0;     
		if (key == 13) {
			e.preventDefault();
		}
    });
	
    /* Blacklist */
    
	$("#bsk_gfblcv_blacklist_list_save_ID").click(function(){
		var list_name = $("#bsk_gfblcv_list_name_ID").val();
        
		list_name = $.trim(list_name);
		if( list_name == "" ){
			alert( "List name cannot be empty" );
			$("#bsk_gfblcv_list_name_ID").focus();
			
			return false;
		}
		
		$("#bsk_gfblcv_list_edit_form_id").submit();
	});
    
    $(".bsk-gfblcv-list-check-way-raido").click( function(){
        var blacklist_check_way = $(this).val();
        
        if( blacklist_check_way == 'ALL' ){
            $( "#bsk_gfblcv_edit_item_container_ID" ).css( "display", "none" );
            $( "#bsk_gfblcv_black_whitle_list_check_all_ID" ).css( "display", "block" );
        }else{
            $( "#bsk_gfblcv_edit_item_container_ID" ).css( "display", "block" );
            $( "#bsk_gfblcv_black_whitle_list_check_all_ID" ).css( "display", "none" );
        }
    });
    
    
	//for IP address, only accpet number, . , * and -
    $(".add-item-input-for-iplist").keyup( function(){
        //only number & letters
        this.value = this.value.replace(/[^0-9.\*\- ]/g, '');
    });
    
	function bsk_gfblcv_valid_email_address( email_address ) {
        var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
        if( pattern.test( email_address ) ){
			return true;
		}
		
		//check if *@domain.com
		var email_str_array = email_address.split('@');
		if( Array.isArray( email_str_array ) == false || 
			email_str_array.length < 2 ){
				
			return false;
		}
		
		if( email_str_array[0] == '*' || pattern.test( 'abc' + email_str_array[1] ) ){
			return true;
		}
		
		return false;
    }
    
    function bsk_gfblcv_valid_ip_address( ip_address ) {
        var pattern = new RegExp(/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/g);
        
        if( pattern.test( ip_address ) ){
			return true;
		}
		
		//check if 45.91.94.*
		var pattern = new RegExp(/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]|\*)$/g);
        if( pattern.test( ip_address ) ){
			return true;
		}
        
        //check if 45.91.94.1 - 45.91.94.123
		var pattern = new RegExp(/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\ \-\ (([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/g);
        
        if( pattern.test( ip_address ) ){
            var ip_start_end = ip_address.split( '-' );
            ip_start_end[0] = $.trim( ip_start_end[0] );
            ip_start_end[1] = $.trim( ip_start_end[1] );
            var ip_start_array = ip_start_end[0].split( '.' );
            var ip_end_array = ip_start_end[1].split( '.' );
            
            if( ip_start_array.length != 4 || ip_end_array.length != 4 ){
                return false;
            }
            
            if( ip_start_array[0] != ip_end_array[0] ){
                return false;
            }
            
            ip_start_array[1] = parseInt( ip_start_array[1] );
            ip_start_array[2] = parseInt( ip_start_array[2] );
            ip_start_array[3] = parseInt( ip_start_array[3] );
            ip_end_array[1] = parseInt( ip_end_array[1] );
            ip_end_array[2] = parseInt( ip_end_array[2] );
            ip_end_array[3] = parseInt( ip_end_array[3] );
            
            if( ip_start_array[1] > ip_end_array[1] ){
                return false;
            }
            
            if( ip_start_array[1] == ip_end_array[1] &&
                ip_start_array[2] > ip_end_array[2] ){
                return false;
            }
            
            if( ip_start_array[1] == ip_end_array[1] &&
                ip_start_array[2] == ip_end_array[2] && 
                ip_start_array[3] > ip_end_array[3] ){
                return false;
            }
            
			return true;
		}

        //check if 45.91.94.1-45.91.94.123
		var pattern = new RegExp(/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\-(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/g);
        if( pattern.test( ip_address ) ){
            var ip_start_end = ip_address.split( '-' );
            var ip_start_array = ip_start_end[0].split( '.' );
            var ip_end_array = ip_start_end[1].split( '.' );
            
            if( ip_start_array[0] != ip_end_array[0] ){
                return false;
            }
            
            ip_start_array[1] = parseInt( ip_start_array[1] );
            ip_start_array[2] = parseInt( ip_start_array[2] );
            ip_start_array[3] = parseInt( ip_start_array[3] );
            ip_end_array[1] = parseInt( ip_end_array[1] );
            ip_end_array[2] = parseInt( ip_end_array[2] );
            ip_end_array[3] = parseInt( ip_end_array[3] );
            
            if( ip_start_array[1] > ip_end_array[1] ){
                return false;
            }
            
            if( ip_start_array[1] == ip_end_array[1] &&
                ip_start_array[2] > ip_end_array[2] ){
                return false;
            }
            
            if( ip_start_array[1] == ip_end_array[1] &&
                ip_start_array[2] == ip_end_array[2] && 
                ip_start_array[3] > ip_end_array[3] ){
                return false;
            }
            
			return true;
		}
        
		return false;
    }
	
	$("#bsk_gfblcv_add_item_by_input_save_anchor_ID").click(function(){
		var item_value = $("#bsk_gfblcv_add_item_by_input_name_ID").val();
		var item_list_type = $("#bsk_gfblcv_items_list_type_ID").val();
		
		item_value = $.trim(item_value);
		if( item_value == "" ){
			alert( "Item value cannot be empty" );
			$("#bsk_gfblcv_add_item_by_input_name_ID").focus();
			
			return false;
		}
		if( item_list_type == 'EMAIL_LIST' ){
			//check if item _value a valid email
			if( !bsk_gfblcv_valid_email_address( item_value ) ){
				alert( "Please enter valid email address or email domain name." );
				$("#bsk_gfblcv_add_item_by_input_name_ID").focus();
				
				return false;
			}
		}else if( item_list_type == 'IP_LIST' ){
			//check if item _value a valid email
			if( !bsk_gfblcv_valid_ip_address( item_value ) ){
				alert( "Please enter valid IP address or IP ranges" );
				$("#bsk_gfblcv_add_item_by_input_name_ID").focus();
				
				return false;
			}
		}
		
		$("#bsk_gfblcv_action_ID").val( "save_item" );
		$("#bsk_gfblcv_items_form_id").submit();
	});
	
	$(".bsk-gfblcv-item-delete-anchor").click(function(){
		var item_id = $(this).attr('rel');
		
		if( parseInt(item_id) < 1 ){
			alert( "Invalid opearation" );
		}
		
		$("#bsk_gfblcv_item_id_ID").val( item_id );
		$("#bsk_gfblcv_action_ID").val( "delete_item" );
		
		$("#bsk_gfblcv_items_form_id").submit();
	});
	
	$("#bsk_gfblcv_add_item_by_csv_ID").change(function (){
       var file_name = $(this).val();
       $("#bsk_gfblcv_add_item_by_csv_selected_file_ID").val( file_name );
    });
	
	$("#bsk_gfblcv_add_item_by_csv_save_anchor_ID").click(function(){
		$(this).parents( '#bsk_gfblcv_add_items_container_ID' ).find( '.bsk-gfblcv-tips-box' ).css( "display", "block" );
	});
	
	$("#bsk_gfblcv_export_items_as_CSV_anchor_ID").click(function(){
		$(this).parents( '.bsk-gfblcv-admin-export-items-as-csv-div' ).find( '.bsk-gfblcv-tips-box' ).css( "display", "block" );
	});
	
	$(".bsk-gfblcv-admin-delete-list").click(function(){
		var list_id = $(this).attr("rel");
		var count = $(this).attr("count");
		
		if( parseInt(list_id) < 1 ){
			alert( "Invalid operation" );
			return false;
		}
		
		if( parseInt(count) > 0 ){
			r = confirm( count + " item(s) inlcuded in this list, are you sure you will remove them?" );
			if( r == false ){
				return false;
			}
		}
		
		$("#bsk_gfblcv_list_id_to_be_processed_ID").val( list_id );
		$("#bsk_gfblcv_action_ID").val( "delete_list_by_id" );
		$("#bsk_gfblcv_lists_form_id").submit();
	});
	
	$("#bsk_gfblcv_add_email_domain_name_checkbox_ID").on("click", function(){
		if( $(this).is(":checked") ){
			$("#bsk_gfblcv_add_email_domain_name_input_container_ID").css( "display", "block" );
			
			$("#bsk_gfblcv_add_email_list_item_input_container_ID").css( "display", "none" );
		}else{
			$("#bsk_gfblcv_add_email_domain_name_input_container_ID").css( "display", "none" );
			
			$("#bsk_gfblcv_add_email_list_item_input_container_ID").css( "display", "block" );
		}
	});
	
	$("#bsk_gfblcv_add_email_domain_name_save_anchor_ID").click(function(){
		var item_value = $("#bsk_gfblcv_email_domain_name_ID").val();
		var item_list_type = $("#bsk_gfblcv_items_list_type_ID").val();
		
		item_value = $.trim(item_value);
		if( item_value == "" ){
			alert( "Item value cannot be empty" );
			$("#bsk_gfblcv_add_item_by_input_name_ID").focus();
			
			return false;
		}
		if( item_list_type == 'EMAIL_LIST' ){
			//check if item _value a valid email
			if( !bsk_gfblcv_valid_email_address( item_value ) ){
				alert( "Please enter valid email address" );
				$("#bsk_gfblcv_add_item_by_input_name_ID").focus();
				
				return false;
			}
		}
		
		$("#bsk_gfblcv_action_ID").val( "save_item" );
		$("#bsk_gfblcv_items_form_id").submit();
	});
    
    /* IP List */
    $( ".bsk-gfblcv-ip-list-check-way-radio" ).click( function(){
        var ip_check_way = $(this).val();
        
        if( ip_check_way == 'COUNTRY' ){
            $( "#bsk_gfblcv_edit_item_container_ID" ).css( "display", "none" );
            $( "#bsk_gfblcv_iplist_by_country_settings_container_ID" ).css( "display", "block" );
        }else{
            $( "#bsk_gfblcv_edit_item_container_ID" ).css( "display", "block" );
            $( "#bsk_gfblcv_iplist_by_country_settings_container_ID" ).css( "display", "none" );
        }
    } );
    
    $("#bsk_gfblcv_iplist_by_country_country_to_block_or_allow_ID").change( function() {
        var country_code = $(this).val();
        if( country_code == '' ){
            return;
        }
        //add new cat id
        var exist_country_codes = $("#bsk_gfblcv_iplist_by_country_exist_countries_code_ID").val();
        var exist_country_codes_array = new Array;
        var already_set = false;
        if( exist_country_codes.length > 0 ){
            exist_country_codes_array = exist_country_codes.split(',');
            if( exist_country_codes_array.length > 0 ){
                for( var i = 0; i < exist_country_codes_array.length; i++ ){
                    if( exist_country_codes_array[i] == country_code ){
                        already_set = true;
                        break;
                    }
                }
            }
        }
        if( already_set == true ){
            return;
        }
        exist_country_codes_array.push( country_code );
        var country_label = $("#bsk_gfblcv_iplist_by_country_country_to_block_or_allow_ID option:selected").text();
        country_label = $.trim(country_label);
        var delete_icon = $("#bsk_gfblcv_delete_country_code_icon_ID").val();
        var html = '<span style="display: inline-block;padding-right:10px;"><a href="javascript:void(0);" class="bsk-gfblcv-delete-country-code-anchor" data-country_code="' + country_code + '"><img src="' + delete_icon + '" style="width:12px;height:12px;" /></a>&nbsp;' + country_label + '</span>';
        $("#bsk_gfblcv_iplist_by_country_added_countries_container_ID").append( html );
        $("#bsk_gfblcv_iplist_by_country_exist_countries_code_ID").val( exist_country_codes_array.join(',') );
    });
    
    $("#bsk_gfblcv_iplist_by_country_settings_container_ID").on("click", ".bsk-gfblcv-delete-country-code-anchor", function(){
        var country_code = $(this).data( 'country_code' );
        
        var exist_country_codes = $("#bsk_gfblcv_iplist_by_country_exist_countries_code_ID").val();
        var exist_country_codes_array = new Array;
        var new_country_codes_array = new Array;
        if( exist_country_codes.length > 0 ){
            exist_country_codes_array = exist_country_codes.split(',');
            if( exist_country_codes_array.length > 0 ){
                for( var i = 0; i < exist_country_codes_array.length; i++ ){
                    if( exist_country_codes_array[i] == country_code ){
                        continue;
                    }
                    new_country_codes_array.push( exist_country_codes_array[i] );
                }
            }
        }
        var new_str = new_country_codes_array.join(',');
        $("#bsk_gfblcv_iplist_by_country_exist_countries_code_ID").val( new_str );
        
        $(this).parent().remove();
    });
    
    
    $( ".bsk-gfblcv-iplist-test-anchor" ).click( function() {
        var api_server = $("#bsk_gfblcv_iplist_by_country_API_server_to_use_ID").val();
        api_server_key_require = $("#bsk_gfblcv_iplist_by_country_API_server_to_use_ID").children(":selected").attr("id");
        var api_key = '';
        var api_test_ip = $("#bsk_gfblcv_iplist_by_country_API_test_IP_value_ID").val();
        
        if( $("#bsk_gfblcv_iplist_by_country_API_key_ID").length ){
            api_key = $("#bsk_gfblcv_iplist_by_country_API_key_ID").val();
        }
        
        $( "#bsk_gfblcv_iplist_api_test_response_container_ID" ).html( '' );
        if( api_server == '' ){
            $( "#bsk_gfblcv_iplist_api_test_response_container_ID" ).html( '<p style="color: #FF0000;">Please choose a API serer.</p>' );
            $("#bsk_gfblcv_iplist_by_country_API_server_to_use_ID").focus();
            
            return;
        }
        
        if( api_server_key_require == 'YES' && $.trim( api_key ) == '' ){
            $( "#bsk_gfblcv_iplist_api_test_response_container_ID" ).html( '<p style="color: #FF0000;">Please enter you API key.</p>' );
            $( "#bsk_gfblcv_iplist_by_country_API_key_ID" ).focus();
            
            return;
        }
        
        api_test_ip = $.trim( api_test_ip );
        if( api_test_ip == '' ){
            $( "#bsk_gfblcv_iplist_api_test_response_container_ID" ).html( '<p style="color: #FF0000;">Please enter an IP address.</p>' );
            $( "#bsk_gfblcv_iplist_by_country_API_test_IP_value_ID" ).focus();
            
            return;
        }else{
            validate_ip_return = bsk_gfblcv_valid_ip_address( api_test_ip );
            if( validate_ip_return == false ){
                $( "#bsk_gfblcv_iplist_api_test_response_container_ID" ).html( '<p style="color: #FF0000;">Invalid IP address.</p>' );
                $( "#bsk_gfblcv_iplist_by_country_API_test_IP_value_ID" ).focus();

                return;
            }
        }
        
        selected_country_val = $("#bsk_gfblcv_iplist_by_country_exist_countries_code_ID").val();
        var ajax_loader = $(this).parent().find( '.bsk-gfblcv-iplist-api-test-ajax-loder' );
        
        //ajax to check api
        var nonce_val = $("#bsk_gfblcv_ip_list_test_api_nonce_ID").val();
        var data = { 
                        action: 'bsk_gfblcv_ip_list_test_API',
                        server: api_server,
                        key: api_key,
                        ip: api_test_ip,
                        selected_country: selected_country_val,
                        nonce: nonce_val
                   };
        
        ajax_loader.css( "display", "inline-block" );
        $.post( ajaxurl, data, function( response ) {
            ajax_loader.css( "display", "none" );
            $( "#bsk_gfblcv_iplist_api_test_response_container_ID" ).html( response );
        });
        
    });
    
    $("#bsk_gfblcv_iplist_by_country_API_server_to_use_ID").change( function(){
        var api_server = $(this).val();
        api_server_key_require = $(this).children(":selected").attr("id");
        console.log( api_server_key_require );
        $("#bsk_gfblcv_iplist_by_country_settings_container_ID").find( ".bsk-gfblcv-iplist-by-country-api-server-ref" ).css( "display", "none" );
        $("#bsk_gfblcv_iplist_by_country_settings_container_ID").find( "#bsk_gfblcv_iplist_by_country_API_key_ID" ).removeAttr( "disabled" );
        $("#bsk_gfblcv_iplist_by_country_settings_container_ID").find( "#bsk_gfblcv_iplist_by_country_API_key_ID" ).val( "" );
        if( api_server == '' ){
            return;
        }
        api_server = api_server.replace( /\./g, '_' );
        $("#bsk_gfblcv_iplist_by_country_api_server_ref_" + api_server + '_ID').css( "display", "inline-block" );
        
        if( api_server_key_require == 'NO' ){
            $("#bsk_gfblcv_iplist_by_country_settings_container_ID").find( "#bsk_gfblcv_iplist_by_country_API_key_ID" ).prop( 'disabled', true );
        }
    });
    
    
    /*
     * blocked entries
     *
     */
    $("#bsk_gfbl_form_select_to_list_entries_ID, #bsk_gfbl_form_selected_plugin_ID").change( function(){
        var slected_form = $(this).val();
        
        $(this).parents( 'form' ).submit();
    });
    
    $( ".bsk-gfblcv-notify-bloked-enable-radio" ).click( function(){
        var notify_blocked_enable = $("input[name='bsk_gfblcv_notify_blocked_enable']:checked").val();
        var details_container = $(this).parents( ".bsk-gfblcv-notify-administrtor-settings" ).find( ".bsk-gfblcv-administrator-mails-details-container" );
        
        if( notify_blocked_enable == 'NO' ){
            details_container.css( "display", "none" );
            return;
        }
        
        details_container.css( "display", "block" );
    });
    
    /*
     * Settings
     *
     */
    /* settings tab switch */
	$("#bsk_gfblcv_setings_wrap_ID .nav-tab-wrapper a").click(function(){
		//alert( $(this).index() );
		$('#bsk_gfblcv_setings_wrap_ID section').hide();
		$('#bsk_gfblcv_setings_wrap_ID section').eq($(this).index()).show();
		
		$(".nav-tab").removeClass( "nav-tab-active" );
		$(this).addClass( "nav-tab-active" );
		
		return false;
	});
    
	//settings target tab
	if( $("#bsk_gfblcv_settings_target_tab_ID").length > 0 ){
		var target = $("#bsk_gfblcv_settings_target_tab_ID").val();
		if( target ){
			$("#bsk_gfblcv_setings_tab-" + target).click();
		}
	}
    
    $("#bsk_gfbl_form_select_to_list_entries_ID, #bsk_gfbl_form_selected_plugin_ID").change( function(){
        var slected_form = $(this).val();
        
        $(this).parents( 'form' ).submit();
    });
    
    $( ".bsk-gfblcv-notify-bloked-enable-radio" ).click( function(){
        var notify_blocked_enable = $("input[name='bsk_gfblcv_notify_blocked_enable']:checked").val();
        var details_container = $(this).parents( ".bsk-gfblcv-notify-administrtor-settings" ).find( ".bsk-gfblcv-administrator-mails-details-container" );
        
        if( notify_blocked_enable == 'NO' ){
            details_container.css( "display", "none" );
            return;
        }
        
        details_container.css( "display", "block" );
    });
    
    
    /*
     * gravity forms form settings
     */
    $( ".bsk-gfblcv-form-settings-enable-raido" ).change(function () {

        var enable = $("input[type='radio'][name='bsk_gfblcv_form_settings_enable']:checked").val();
        var form_settings_container = $(this).parents( '.bsk-gfblcv-form-settings-container' );
        
        if( enable == 'DISABLE' ){
            form_settings_container.find( ".bsk-gfblcv-form-settings-actions-container" ).css( "display", "none" );
            form_settings_container.find( ".bsk-gfblcv-form-settings-blocked-data-container" ).css( "display", "none" );
            form_settings_container.find( ".bsk-gfblcv-form-settings-entry-container" ).css( "display", "none" );
            form_settings_container.find( ".bsk-gfblcv-form-settings-error-messages-container" ).css( "display", "none" );

            form_settings_container.parent().find( "#bsk_gfblcv_cf7_form_mappings_ID" ).css( "display", "none" );
            
            return;
        }
        
        form_settings_container.find( ".bsk-gfblcv-form-settings-actions-container" ).css( "display", "table-row" );
        form_settings_container.parent().find( "#bsk_gfblcv_cf7_form_mappings_ID" ).css( "display", "block" );
        
        bsk_gfblcv_control_settings_display( form_settings_container );
    });
    
    function bsk_gfblcv_control_settings_display( $root_container_object ){
        
        $root_container_object.find( ".bsk-gfblcv-form-settings-blocked-data-container" ).css( "display", "none" );
        $root_container_object.find( ".bsk-gfblcv-form-settings-entry-container" ).css( "display", "none" );
        $root_container_object.find( ".bsk-gfblcv-form-settings-error-messages-container" ).css( "display", "none" );
        
        $root_container_object.find( ".bsk-gfblcv-notificaitons-to-skip" ).css( 'display', 'none' );
        $root_container_object.find( ".bsk-gfblcv-confirmations-to-go" ).css( 'display', 'none' );
        
        var is_action_block = $( ".bsk-gfblcv-form-settings-action-block-chk" ).is( ":checked" );
        var is_action_skip = $( ".bsk-gfblcv-form-settings-action-skip-chk" ).is( ":checked" );
        var is_action_confirmation = $( ".bsk-gfblcv-form-settings-action-confirmation-chk" ).is( ":checked" );
        var is_notify_administrator = $("input[type='radio'][name='bsk_gfblcv_notify_administrators']:checked").val();
        var is_notify_administrator = is_notify_administrator == 'YES' ? true : false;
        
        
        $root_container_object.find( ".bsk-gfblcv-form-settings-entry-container" ).css( "display", "block" );
        
        if( is_action_block ){
            $root_container_object.find( ".bsk-gfblcv-form-settings-blocked-data-container" ).css( "display", "block" );
            $root_container_object.find( ".bsk-gfblcv-form-settings-error-messages-container" ).css( "display", "block" );
            $root_container_object.find( ".bsk-gfblcv-form-settings-entry-container" ).css( "display", "none" );
        }
        
        if( is_action_skip ){
            $root_container_object.find( ".bsk-gfblcv-notificaitons-to-skip" ).css( 'display', 'table-row' );
        }
        
        if( is_action_confirmation ){
            $root_container_object.find( ".bsk-gfblcv-confirmations-to-go" ).css( 'display', 'table-row' );
        }
        
        if( is_notify_administrator ){
            $root_container_object.find( ".bsk-gfblcv-form-settings-notify-send-to" ).css( 'display', 'table-row' );
        }
    }

    $( ".bsk-gfblcv-form-settings-action-block-chk, .bsk-gfblcv-form-settings-action-skip-chk, .bsk-gfblcv-form-settings-action-confirmation-chk" ).change( function( event ){

        var form_settings_container = $(this).parents( '.bsk-gfblcv-form-settings-container' );

        if( $(this).hasClass( 'bsk-gfblcv-form-settings-action-block-chk' ) ){
            if( $(this).is( ':checked' ) ){
                form_settings_container.find( ".bsk-gfblcv-form-settings-action-skip-chk" ).prop( 'checked', false );
                form_settings_container.find( ".bsk-gfblcv-form-settings-action-confirmation-chk" ).prop( 'checked', false );
            }
        }else if( $(this).hasClass( 'bsk-gfblcv-form-settings-action-skip-chk' ) ){
            if( $(this).is( ':checked' ) ){
                form_settings_container.find( ".bsk-gfblcv-form-settings-action-block-chk" ).prop( 'checked', false );
            }
        }else if( $(this).hasClass( 'bsk-gfblcv-form-settings-action-confirmation-chk' ) ){
            if( $(this).is( ':checked' ) ){
                form_settings_container.find( ".bsk-gfblcv-form-settings-action-block-chk" ).prop( 'checked', false );
            }
        }
        
        bsk_gfblcv_control_settings_display( form_settings_container );
    });
    
    $(".bsk-gfblcv-notifiy-administrators-raido").change( function(){
        var notify_administrator = $("input[type='radio'][name='bsk_gfblcv_notify_administrators']:checked").val();
        var form_settings_container = $(this).parents( '.bsk-gfblcv-form-settings-container' );
        
        if( notify_administrator == 'YES' ){
            form_settings_container.find( ".bsk-gfblcv-form-settings-notify-send-to" ).css( 'display', 'table-row' );
        }else{
            form_settings_container.find( ".bsk-gfblcv-form-settings-notify-send-to" ).css( 'display', 'none' );
        }
    })
    
    $(".bsk-gfblcv-form-settings-delete-entry-radio").change( function(){
        var delete_entry = $("input[type='radio'][name='bsk_gfblcv_delete_entry']:checked").val();
        var pro_tips_container = $(this).parents( '.bsk-gfblcv-form-settings-delete-entry-tr' ).find( '.bsk-gfblcv-tips-box' );
        
        if( delete_entry == 'YES' ){
            pro_tips_container.css( 'display', 'block' );
        }else{
            pro_tips_container.css( 'display', 'none' );
        }
    })
    
    /*
     * formidable forms form field
     */
    $( ".bsk-gfbl-ff-form-field-apply-list-chk" ).click( function() {
        var checked = $(this).is(":checked");
        var type = $(this).data( 'list-type' );
        
        //uncheck or checkbox
        $(this).parents( 'ul' ).find( '.bsk-gfbl-ff-form-field-apply-list-chk' ).prop( 'checked', false );
        $(this).parents( 'ul' ).find( 'select' ).val( '');
        $(this).parents( 'ul' ).find( 'select' ).slideUp();
        //hide validaiton message
        $(this).parents( 'ul' ).find( '.bsk-gfbl-validation-message-field-setting' ).css( "display", "none" );
        
        //check the current
        if ( checked ) {
            $(this).prop( 'checked', true );
            //show select
            $(this).parent().find( 'select' ).slideDown();
            $(this).parents( 'ul' ).find( '.bsk-gfbl-validation-message-field-setting' ).css( "display", "block" );
        }
        
    });

    /* CF7 */
    $("#cf7_blacklist_skip_main_mail_chk_ID").click(function(){
        if( $(this).is(":checked") ){
            $("#cf7_blacklist_skip_mail_2_chk_li_ID").css("display", "none");
        }else{
            $("#cf7_blacklist_skip_mail_2_chk_li_ID").css("display", "inline-block");
        }
    });

    $( ".bsk-gfblcv-cf7-mapping-list-type-select" ).change( function() {
        var list_type = $( this ).val();

        $( this ).parents( 'tr' ).find( ".bsk-gfblcv-cf7-mapping-list-id-select, .bsk-gfblcv-cf7-mapping-comparison, .bsk-gfblcv-cf7-mapping-action, .bsk-gfblcv-cf7-mapping-action-for-invit, .bsk-gfblcv-cf7-validation-message" ).css( "display", "none" );
        if ( list_type == '' ) {
            return;
        }
        var list_id_class_identifier = '';
        var list_comparison_class_identifier = '';
        var validation_message_class_identifier = 'bsk-gfblcv-cf7-validation-message';
        switch( list_type ) {
            case 'BLACK_LIST':
                list_id_class_identifier = 'bsk-gfblcv-cf7-blacklist';
                list_comparison_class_identifier = 'bsk-gfblcv-cf7-mapping-comparison';
            break;
            case 'WHITE_LIST':
                list_id_class_identifier = 'bsk-gfblcv-cf7-whitelist';
                list_comparison_class_identifier = 'bsk-gfblcv-cf7-mapping-comparison';
            break;
            case 'EMAIL_LIST':
                list_id_class_identifier = 'bsk-gfblcv-cf7-emaillist';
                list_comparison_class_identifier = 'bsk-gfblcv-cf7-mapping-action';
            break;
            case 'IP_LIST':
                list_id_class_identifier = 'bsk-gfblcv-cf7-iplist';
                list_comparison_class_identifier = 'bsk-gfblcv-cf7-mapping-action';
            break;
            case 'INVIT_LIST':
                list_id_class_identifier = 'bsk-gfblcv-cf7-invitlist';
                list_comparison_class_identifier = 'bsk-gfblcv-cf7-mapping-action-for-invit';
            break;
            default:
                validation_message_class_identifier = ''; 
            break;
        }

        
        $( this ).parents( 'tr' ).find( ".bsk-gfblcv-cf7-mapping-list-id-select." + list_id_class_identifier ).css( "display", "inline-block" );
        $( this ).parents( 'tr' ).find( "." + list_comparison_class_identifier ).css( "display", "inline-block" );
        $( this ).parents( 'tr' ).find( "." + validation_message_class_identifier ).css( "display", "inline-block" );
    });

    $( ".bsk-gfblcv-cf7-mapping-list-id-select, .bsk-gfblcv-cf7-mapping-comparison, .bsk-gfblcv-cf7-mapping-action").change( function() {
        if ( $ ( this ).val() != '' ) {
            $( this ).parent().find( '.bsk-gfblcv-error-message' ).css( "display", "none" );
        }
    });
    
    /*
     * formidable forms form field
     */
    $("#bsk_gfblcv_forminator_setings_wrap_ID .nav-tab-wrapper a").click(function(){
		//alert( $(this).index() );
		$('#bsk_gfblcv_forminator_setings_wrap_ID section').hide();
		$('#bsk_gfblcv_forminator_setings_wrap_ID section').eq($(this).index()).show();
		
		$(".nav-tab").removeClass( "nav-tab-active" );
		$(this).addClass( "nav-tab-active" );
		
		return false;
	});
    
	//settings target tab
	if( $( "#bsk_gfblcv_forminator_settings_target_tab_ID" ).length > 0 ){
		var target = $( "#bsk_gfblcv_forminator_settings_target_tab_ID" ).val();
		if( target ){
			$( "#bsk_gfblcv_forminator_setings_tab-" + target ).click();
		}
	}

    $( ".bsk-gfblcv-frmt-mapping-list-type-select" ).change( function() {
        var list_type = $( this ).val();

        $( this ).parents( 'tr' ).find( ".bsk-gfblcv-frmt-mapping-list-id-select, .bsk-gfblcv-frmt-mapping-comparison, .bsk-gfblcv-frmt-mapping-action, .bsk-gfblcv-frmt-mapping-action-for-invit, .bsk-gfblcv-frmt-validation-message" ).css( "display", "none" );
        if ( list_type == '' ) {
            return;
        }
        var list_id_class_identifier = '';
        var list_comparison_class_identifier = '';
        var validation_message_class_identifier = 'bsk-gfblcv-frmt-validation-message';
        switch( list_type ) {
            case 'BLACK_LIST':
                list_id_class_identifier = 'bsk-gfblcv-frmt-blacklist';
                list_comparison_class_identifier = 'bsk-gfblcv-frmt-mapping-comparison';
            break;
            case 'WHITE_LIST':
                list_id_class_identifier = 'bsk-gfblcv-frmt-whitelist';
                list_comparison_class_identifier = 'bsk-gfblcv-frmt-mapping-comparison';
            break;
            case 'EMAIL_LIST':
                list_id_class_identifier = 'bsk-gfblcv-frmt-emaillist';
                list_comparison_class_identifier = 'bsk-gfblcv-frmt-mapping-action';
            break;
            case 'IP_LIST':
                list_id_class_identifier = 'bsk-gfblcv-frmt-iplist';
                list_comparison_class_identifier = 'bsk-gfblcv-frmt-mapping-action';
            break;
            case 'INVIT_LIST':
                list_id_class_identifier = 'bsk-gfblcv-frmt-invitlist';
                list_comparison_class_identifier = 'bsk-gfblcv-frmt-mapping-action-for-invit';
            break;
            default:
                validation_message_class_identifier = ''; 
            break;
        }

        
        $( this ).parents( 'tr' ).find( ".bsk-gfblcv-frmt-mapping-list-id-select." + list_id_class_identifier ).css( "display", "inline-block" );
        $( this ).parents( 'tr' ).find( "." + list_comparison_class_identifier ).css( "display", "inline-block" );
        $( this ).parents( 'tr' ).find( "." + validation_message_class_identifier ).css( "display", "inline-block" );
    });

    $( ".bsk-gfblcv-frmt-mapping-list-id-select, .bsk-gfblcv-frmt-mapping-comparison, .bsk-gfblcv-frmt-mapping-action").change( function() {
        if ( $ ( this ).val() != '' ) {
            $( this ).parent().find( '.bsk-gfblcv-error-message' ).css( "display", "none" );
        }
    });

});
