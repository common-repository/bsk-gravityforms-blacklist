<?php
class BSK_GFBLCV_Dashboard_Formidable_Forms {
	
	public $_bsk_gfblcv_OBJ_ff_field = NULL;
	public $_bsk_gfblcv_OBJ_ff_settings = NULL;
    
    public static $_bsk_gfblcv_ff_form_settings_option_name_prefix = '_bsk_forms_blacklist_ff_settings_of_';
    
	public function __construct() {
		
		require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/formidable-forms/form-field.php' );
		require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/formidable-forms/form-settings.php' );
        
		$this->_bsk_gfblcv_OBJ_ff_field = new BSK_GFBLCV_Dashboard_FF_Field();
		$this->_bsk_gfblcv_OBJ_ff_settings = new BSK_GFBLCV_Dashboard_FF_Settings();
	}
	
}
