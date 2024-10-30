<?php

class BSK_GFBLCV_Submitting {
    
    public $_OBJ_common = NULL;
    public $_OBJ_forms_gf = NULL;
    public $_OBJ_forms_ff = NULL;
    public $_OBJ_forms_wpf = NULL;
    public $_OBJ_forms_cf7 = NULL;
    public $_OBJ_forms_frmt = NULL;

	public function __construct() {
        
        require_once( BSK_GFBLCV_FREE_DIR.'classes/submitting/common.php' );
		require_once( BSK_GFBLCV_FREE_DIR.'classes/submitting/gravityforms.php' );
        require_once( BSK_GFBLCV_FREE_DIR.'classes/submitting/formidable-forms.php' );
        require_once( BSK_GFBLCV_FREE_DIR.'classes/submitting/wpforms.php' );
        require_once( BSK_GFBLCV_FREE_DIR.'classes/submitting/cf7.php' );
        require_once( BSK_GFBLCV_FREE_DIR.'classes/submitting/forminator.php' );

		$this->_OBJ_common = new BSK_GFBLCV_Submitting_Common();
        
        $init_args = array();
        $init_args['common_class'] = $this->_OBJ_common;
        
		$this->_OBJ_forms_gf = new BSK_GFBLCV_Submitting_GravityForms( $init_args );
        $this->_OBJ_forms_ff = new BSK_GFBLCV_Submitting_FormidableForms( $init_args );
        $this->_OBJ_forms_wpf = new BSK_GFBLCV_Submitting_WPForms( $init_args );
        $this->_OBJ_forms_cf7 = new BSK_GFBLCV_Submitting_CF7( $init_args );
        $this->_OBJ_forms_frmt = new BSK_GFBLCV_Submitting_Forminator( $init_args );
	}
	
}