<?php
class BSK_GFBLCV_Dashboard_WPForms {
	
	public $_bsk_gfblcv_OBJ_wpforms_field = NULL;
	public $_bsk_gfblcv_OBJ_wpforms_settings = NULL;
    
	public function __construct() {
		
		require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/wpforms/form-field.php' );
		require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/wpforms/form-settings.php' );
        
		$this->_bsk_gfblcv_OBJ_wpforms_field = new BSK_GFBLCV_Dashboard_WPForms_Field();
		$this->_bsk_gfblcv_OBJ_wpforms_settings = new BSK_GFBLCV_Dashboard_WPForms_Settings();
        
        add_action( 'wp_ajax_bsk_gfblcv_wpforms_get_list_by_type', array( $this, 'bsk_gfblcv_wpforms_get_list_by_type_fun' ) );

	}
	
    function bsk_gfblcv_wpforms_get_list_by_type_fun(){
        $data_to_return = array();

        $field_id = sanitize_text_field($_POST['fieldid']);
        if( !check_ajax_referer( 'bskblacklist_adv_fields_ajax_nonce_'.$field_id, 'nonce' ) ){
            $data_to_return['success'] = false;
            $data_to_return['msg'] = 'Invalid nonce, please refresh page to try again.';
            
            wp_die( json_encode($data_to_return) );
        }
                

        $selected_list_id = intval($_POST['listid']);
        $list_type = sanitize_text_field($_POST['type']);
        if( $list_type != 'BLACK_LIST' && $list_type != 'WHITE_LIST' && $list_type != 'EMAIL_LIST' && $list_type != 'IP_LIST' ){
            
            $data_to_return['success'] = false;
            $data_to_return['msg'] = 'Invalid list type, please refresh page to try again.';
            
            wp_die( json_encode($data_to_return) );
        }
        
		$options = BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_list_by_type( $list_type, $selected_list_id );
        
        $data_to_return['success'] = true;
        $data_to_return['lists_options'] = '<option val="">Select...</option>'.$options;
        
        wp_die( json_encode($data_to_return) );
    }
}
