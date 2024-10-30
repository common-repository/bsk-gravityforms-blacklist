<?php
class BSK_GFBLCV_Dashboard_GravityForms {
	
	public $_bsk_gfblcv_OBJ_gform_field = NULL;
	public $_bsk_gfblcv_OBJ_gform_settings = NULL;
    
	public function __construct() {
		
		require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/gravityforms/form-field.php' );
		require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/gravityforms/form-settings.php' );
        
		$this->_bsk_gfblcv_OBJ_gform_field = new BSK_GFBLCV_Dashboard_GForm_Field();
		$this->_bsk_gfblcv_OBJ_gform_settings = new BSK_GFBLCV_Dashboard_GForm_Settings();
        
	}
	
}
