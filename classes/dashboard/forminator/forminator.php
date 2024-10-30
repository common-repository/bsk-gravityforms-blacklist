<?php
class BSK_GFBLCV_Dashboard_Forminator {

	public static $_bsk_gfblcv_frmt_child_field_separator = '$';
	
    public static $_bsk_gfblcv_frmt_form_settings_option_name_prefix = '_bsk_forms_blacklist_frmt_form_settings_of_';
	public static $_bsk_gfblcv_frmt_field_settings_option_name_prefix = '_bsk_forms_blacklist_frmt_field_settings_of_';

	public $_bsk_gfblcv_OBJ_frmt_settings = false;
    
	public function __construct() {

		if ( BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_is_form_plugin_supported( 'FRMT' ) ) {
			require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/forminator/settings.php' );
			
			$this->_bsk_gfblcv_OBJ_frmt_settings = new BSK_GFBLCV_Dashboard_Forminator_Settings();

			/*
			* Actions & Filters
			*/
			add_action( 'admin_menu', array( $this, 'bsk_gfblcv_dashboard_forminator_settings_mapping_menu' ), 999 );
		}
		
	}
	
	function bsk_gfblcv_dashboard_forminator_settings_mapping_menu() {
		
		$authorized_level = 'manage_options';

        $bsk_gfblcv_menu_hook = add_submenu_page( 
                                                    'forminator',
                                                    'Blacklist', 
                                                    'Blacklist',
                                                    $authorized_level, 
                                                    BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['forminator_blacklist'], 
                                                    array($this, 'bsk_gfblcv_forminator_forms_list_fun') 
                                                );
	}

	function bsk_gfblcv_forminator_forms_list_fun(){
		
		$current_view = 'list';
		if(isset($_GET['view']) && $_GET['view']){
			$current_view = trim($_GET['view']);
		}
		if(isset($_POST['view']) && $_POST['view']){
			$current_view = trim($_POST['view']);
		}
		
		$current_base_page = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['forminator_blacklist'].'&view='.$current_view );
		
		if ( $current_view == 'list' ) {
            $init_args = array();

			require_once( 'forms-list.php' );

			$_bsk_gfblcv_OBJ_forminator_lists = new BSK_GFBLCV_Dashboard_Forminator_Forms_List( $init_args );

			//Fetch, prepare, sort, and filter our data...
			$_bsk_gfblcv_OBJ_forminator_lists->prepare_items();

			$add_new_page_url = add_query_arg( 'view', 'addnew', $current_base_page );
			echo '<div class="wrap">
					<div id="icon-edit" class="icon32"><br/></div>
					<h2>Forminator Forms List<a href="'.$add_new_page_url.'" class="add-new-h2">Add New</a></h2>';
			?>
			<div class="bsk-gfblcv-tips-box">
				<p>Free verison only supports max 10 Forminator forms.</p>
				<p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
			</div>
			<?php
			echo '  <form id="bsk_gfblcv_lists_form_id" method="post" action="'.$current_base_page.'">';
						$_bsk_gfblcv_OBJ_forminator_lists->display();
			echo '  
						<input type="hidden" name="bsk_gfblcv_forminator_form_id_id" id="bsk_gfblcv_forminator_form_id_to_becessed_ID" value="0" />
						<input type="hidden" name="bsk_gfblcv_action" id="bsk_gfblcv_action_ID" value="" />';
						wp_nonce_field( 'bsk_gfblcv_forminator_form_oper_nonce', 'bsk_gfblcv_forminator_form_oper_nonce' );
			echo '
					</form>
					</div>';
		} else if ( $current_view == 'settings' ) {
			$form_id = -1;
			if( isset( $_GET['id'] ) && $_GET['id'] ){
				$form_id = sanitize_text_field( $_GET['id'] );
				$form_id = absint( $form_id );
			}
            $this->_bsk_gfblcv_OBJ_frmt_settings->disaplay( $form_id );

		}
	}

}
