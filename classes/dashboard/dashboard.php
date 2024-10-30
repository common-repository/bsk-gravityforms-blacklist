<?php
class BSK_GFBLCV_Dashboard {
	
    public static $_bsk_gfblcv_pages = array(  
                                                'base' => 'bsk-forms-blacklist',
                                                'whitelist' => 'bsk-forms-whitelist',
                                                'emailist' => 'bsk-forms-emailist',
                                                'iplist' => 'bsk-forms-iplist',
                                                'invitlist' => 'bsk-forms-invitation-codes-list',
                                                'blocked_data' => 'bsk-forms-blocked-data',
                                                'settings' => 'bsk-forms-settings',
                                                'license_update' => 'bsk-forms-license-update',
                                                'forminator_blacklist' => 'bsk-forminator-blacklist',
                                            );
    
    public static $_plugin_settings_option = '_bsk_gfbl_settings_';

    public static $_bsk_gfblcv_cf7_form_settings_opt = '_bsk_gfblcv_cf7_form_settings_';
    public static $_bsk_gfblcv_cf7_form_mappings_opt = '_bsk_gfblcv_cf7_form_mappings_';
    
    public $_bsk_gfblcv_OBJ_settings = NULL;
    public $_bsk_gfblcv_OBJ_list = NULL;
    public $_bsk_gfblcv_OBJ_ip_country = NULL;
    
    public $_bsk_gfblcv_forms_OBJ_gf = NULL;
    public $_bsk_gfblcv_forms_OBJ_ff = NULL;
    public $_bsk_gfblcv_forms_OBJ_wpf = NULL;
    public $_bsk_gfblcv_forms_OBJ_cf7 = NULL;
    public $_bsk_gfblcv_forms_OBJ_frmt = NULL;
    
	public function __construct() {
		
        require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/common.php' );
        require_once( BSK_GFBLCV_FREE_DIR.'classes/ip-country/ip-country.php' );

		require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/list.php' );
		require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/lists.php' );
		require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/items.php' );
        require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/list.php' );
        require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/list.php' );
        require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/dashboard-settings.php' );
        require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/entries.php' );
        
        require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/gravityforms/gravityforms.php' );
        require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/formidable-forms/formidable-forms.php' );
        require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/wpforms/wpforms.php' );
        require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/cf7/cf7.php' );
        require_once( BSK_GFBLCV_FREE_DIR.'classes/dashboard/forminator/forminator.php' );
        
		$this->_bsk_gfblcv_OBJ_list = new BSK_GFBLCV_Dashboard_List();
        $this->_bsk_gfblcv_OBJ_settings = new BSK_GFBLCV_Dashboard_Settings();
        
        $this->_bsk_gfblcv_forms_OBJ_gf = new BSK_GFBLCV_Dashboard_GravityForms();
        $this->_bsk_gfblcv_forms_OBJ_ff = new BSK_GFBLCV_Dashboard_Formidable_Forms();
        $this->_bsk_gfblcv_forms_OBJ_wpf = new BSK_GFBLCV_Dashboard_WPForms();
        $this->_bsk_gfblcv_forms_OBJ_cf7 = new BSK_GFBLCV_Dashboard_CF7();
        $this->_bsk_gfblcv_forms_OBJ_frmt = new BSK_GFBLCV_Dashboard_Forminator();

        /*
          * Actions & Filters
          */
		add_action( 'admin_menu', array( $this, 'bsk_gfblcv_dashboard_menu' ), 999 );
        
        add_filter( 'set-screen-option', array( $this, 'bsk_gfblcv_set_option' ), 10, 3);
        
        add_action( 'gform_after_delete_form', array( $this, 'bsk_gfblcv_delete_entries_fun' ) );
        
        add_action( 'admin_notices', array( $this, 'bsk_gfblcv_max_list_error_notice' ) );
	}
	
	function bsk_gfblcv_dashboard_menu() {
		
		$authorized_level = 'manage_options';
        
        add_menu_page( 
                         'BSK Blacklist', 
                         'BSK Blacklist', 
                         $authorized_level, 
                         self::$_bsk_gfblcv_pages['base'], 
                         '', 
                         'dashicons-admin-network'
                     );
		
        add_submenu_page( 
                            self::$_bsk_gfblcv_pages['base'],
                            'Blacklist', 
                            'Blacklist',
                            $authorized_level, 
                            self::$_bsk_gfblcv_pages['base'],
                            array($this, 'bsk_gfblcv_blacklist') 
                        );

        add_submenu_page( 
                            self::$_bsk_gfblcv_pages['base'],
                            'White list', 
                            'White list',
                            $authorized_level, 
                            self::$_bsk_gfblcv_pages['whitelist'],
                            array($this, 'bsk_gfblcv_whitelist') 
                        );

        add_submenu_page( 
                            self::$_bsk_gfblcv_pages['base'],
                            'Email list', 
                            'Email list',
                            $authorized_level, 
                            self::$_bsk_gfblcv_pages['emailist'],
                            array($this, 'bsk_gfblcv_emailist') 
                        );

        add_submenu_page( 
                            self::$_bsk_gfblcv_pages['base'],
                            'IP list', 
                            'IP list',
                            $authorized_level, 
                            self::$_bsk_gfblcv_pages['iplist'],
                            array($this, 'bsk_gfblcv_iplist') 
                        );

        add_submenu_page( 
                            self::$_bsk_gfblcv_pages['base'],
                            'Invitation Codes List',
                            'Invitation Codes List',
                            $authorized_level, 
                            self::$_bsk_gfblcv_pages['invitlist'],
                            array($this, 'bsk_gfblcv_invitlist_list') 
                        );

        add_submenu_page( 
                            self::$_bsk_gfblcv_pages['base'],
                            'Blocked data', 
                            'Blocked data',
                            $authorized_level, 
                            self::$_bsk_gfblcv_pages['blocked_data'],
                            array($this, 'bsk_gfblcv_blocked_data') 
                        );

        add_submenu_page( 
                            self::$_bsk_gfblcv_pages['base'],
                            'Settings', 
                            'Settings',
                            $authorized_level, 
                            self::$_bsk_gfblcv_pages['settings'],
                            array($this, 'bsk_gfblcv_settings') 
                        );
	}
	
	function bsk_gfblcv_blacklist(){
        
		$current_view = 'list';
        if(isset($_GET['view']) && $_GET['view']){
			$current_view = trim($_GET['view']);
		}
		if(isset($_POST['view']) && $_POST['view']){
			$current_view = trim($_POST['view']);
		}
        
		$current_list_view = 'blacklist';
		$current_base_page = admin_url( 'admin.php?page='.self::$_bsk_gfblcv_pages['base'].'&view='.$current_view );
		
		if( $current_view == 'list' ){
            $_bsk_gfblcv_OBJ_lists = new BSK_GFBLCV_Dashboard_Lists( array( 'list' => 'blacklist' ) );

            //Fetch, prepare, sort, and filter our data...
            $_bsk_gfblcv_OBJ_lists->prepare_items();

            $add_new_page_url = add_query_arg( 'view', 'addnew', $current_base_page );
            echo '<div class="wrap">
                    <div id="icon-edit" class="icon32"><br/></div>
                    <h2>BSK Forms Blacklist<a href="'.$add_new_page_url.'" class="add-new-h2">Add New</a></h2>';
            ?>
            <div class="bsk-gfblcv-tips-box">
                <p>Free verison only supports max 50 lists.</p>
                <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
            </div>
            <?php
            echo '  <form id="bsk_gfblcv_lists_form_id" method="post" action="'.$current_base_page.'">';
                        $_bsk_gfblcv_OBJ_lists->display();
            echo '  
                        <input type="hidden" name="bsk_gfblcv_list_id" id="bsk_gfblcv_list_id_to_be_processed_ID" value="0" />
                        <input type="hidden" name="bsk_gfblcv_action" id="bsk_gfblcv_action_ID" value="" />';
                        wp_nonce_field( 'bsk_gfblcv_list_oper_nonce' );
            echo '
                    </form>
                  </div>';
		}else if ( $current_view == 'addnew' || $current_view == 'edit' ){
			$list_id = -1;
			if(isset($_GET['id']) && $_GET['id']){
				$list_id = trim($_GET['id']);
				$list_id = absint($list_id);
			}
            $this->_bsk_gfblcv_OBJ_list->bsk_gfblcv_list_edit( 
                                                               $list_id, 
                                                               $current_list_view, 
                                                               $current_view 
                                                             );
		}
	}
    
    function bsk_gfblcv_whitelist(){
        
        $current_view = 'list';
        if(isset($_GET['view']) && $_GET['view']){
			$current_view = trim($_GET['view']);
		}
		if(isset($_POST['view']) && $_POST['view']){
			$current_view = trim($_POST['view']);
		}
        
		$current_list_view = 'whitelist';
		$current_base_page = admin_url( 'admin.php?page='.self::$_bsk_gfblcv_pages['whitelist'].'&view='.$current_view );
		
		if( $current_view == 'list' ){
            $init_args = array();
            $_bsk_gfblcv_OBJ_lists = new BSK_GFBLCV_Dashboard_Lists( array( 'list' => 'whitelist' ) );

            //Fetch, prepare, sort, and filter our data...
            $_bsk_gfblcv_OBJ_lists->prepare_items();

            $add_new_page_url = add_query_arg( 'view', 'addnew', $current_base_page );
            echo '<div class="wrap">
                    <div id="icon-edit" class="icon32"><br/></div>
                    <h2>BSK Forms White List<a href="'.$add_new_page_url.'" class="add-new-h2">Add New</a></h2>';
            ?>
            <div class="bsk-gfblcv-tips-box">
                <p>This feature only available Pro version</p>
                <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
            </div>
            <?php
            echo '  <form id="bsk_gfblcv_lists_form_id" method="post" action="'.$current_base_page.'">';
                        $_bsk_gfblcv_OBJ_lists->display();
            echo '  
                        <input type="hidden" name="bsk_gfblcv_list_id" id="bsk_gfblcv_list_id_to_be_processed_ID" value="0" />
                        <input type="hidden" name="bsk_gfblcv_action" id="bsk_gfblcv_action_ID" value="" />';
                        wp_nonce_field( 'bsk_gfblcv_list_oper_nonce' );
            echo '
                    </form>
                  </div>';
		}else if ( $current_view == 'addnew' || $current_view == 'edit' ){
			$list_id = -1;
			if(isset($_GET['id']) && $_GET['id']){
				$list_id = trim($_GET['id']);
				$list_id = absint($list_id);
			}
            $this->_bsk_gfblcv_OBJ_list->bsk_gfblcv_list_edit( 
                                                               $list_id, 
                                                               $current_list_view, 
                                                               $current_view 
                                                             );
		}
	}
    
    function bsk_gfblcv_emailist(){
        
        $current_view = 'list';
        if(isset($_GET['view']) && $_GET['view']){
			$current_view = trim($_GET['view']);
		}
		if(isset($_POST['view']) && $_POST['view']){
			$current_view = trim($_POST['view']);
		}
        
		$current_list_view = 'emaillist';
		$current_base_page = admin_url( 'admin.php?page='.self::$_bsk_gfblcv_pages['emailist'].'&view='.$current_view );
		
		if( $current_view == 'list' ){
            $_bsk_gfblcv_OBJ_lists = new BSK_GFBLCV_Dashboard_Lists( array( 'list' => 'emaillist' ) );

            //Fetch, prepare, sort, and filter our data...
            $_bsk_gfblcv_OBJ_lists->prepare_items();

            $add_new_page_url = add_query_arg( 'view', 'addnew', $current_base_page );
            echo '<div class="wrap">
                    <div id="icon-edit" class="icon32"><br/></div>
                    <h2>BSK Forms Email List<a href="'.$add_new_page_url.'" class="add-new-h2">Add New</a></h2>';
            ?>
            <div class="bsk-gfblcv-tips-box">
                <p>This feature only available Pro version</p>
                <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
            </div>
            <?php
            echo '  <form id="bsk_gfblcv_lists_form_id" method="post" action="'.$current_base_page.'">';
                        $_bsk_gfblcv_OBJ_lists->display();
            echo '  
                        <input type="hidden" name="bsk_gfblcv_list_id" id="bsk_gfblcv_list_id_to_be_processed_ID" value="0" />
                        <input type="hidden" name="bsk_gfblcv_action" id="bsk_gfblcv_action_ID" value="" />';
                        wp_nonce_field( 'bsk_gfblcv_list_oper_nonce' );
            echo '
                    </form>
                  </div>';
		}else if ( $current_view == 'addnew' || $current_view == 'edit' ){
			$list_id = -1;
			if(isset($_GET['id']) && $_GET['id']){
				$list_id = trim($_GET['id']);
				$list_id = absint($list_id);
			}
            $this->_bsk_gfblcv_OBJ_list->bsk_gfblcv_list_edit( 
                                                               $list_id, 
                                                               $current_list_view, 
                                                               $current_view 
                                                             );
		}
	}
    
    function bsk_gfblcv_iplist(){
        
        $current_view = 'list';
        if(isset($_GET['view']) && $_GET['view']){
			$current_view = trim($_GET['view']);
		}
		if(isset($_POST['view']) && $_POST['view']){
			$current_view = trim($_POST['view']);
		}
        
		$current_list_view = 'iplist';
		$current_base_page = admin_url( 'admin.php?page='.self::$_bsk_gfblcv_pages['iplist'].'&view='.$current_view );
		
		if( $current_view == 'list' ){
            $_bsk_gfblcv_OBJ_lists = new BSK_GFBLCV_Dashboard_Lists( array( 'list' => 'iplist' ) );

            //Fetch, prepare, sort, and filter our data...
            $_bsk_gfblcv_OBJ_lists->prepare_items();

            $add_new_page_url = add_query_arg( 'view', 'addnew', $current_base_page );
            echo '<div class="wrap">
                    <div id="icon-edit" class="icon32"><br/></div>
                    <h2>BSK Forms IP List<a href="'.$add_new_page_url.'" class="add-new-h2">Add New</a></h2>';
            ?>
                    <div class="bsk-gfblcv-tips-box">
                        <p>This feature only available Pro version</p>
                        <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
                    </div>
            <?php
            echo '  <form id="bsk_gfblcv_lists_form_id" method="post" action="'.$current_base_page.'">';
                        $_bsk_gfblcv_OBJ_lists->display();
            echo '  
                        <input type="hidden" name="bsk_gfblcv_list_id" id="bsk_gfblcv_list_id_to_be_processed_ID" value="0" />
                        <input type="hidden" name="bsk_gfblcv_action" id="bsk_gfblcv_action_ID" value="" />';
                        wp_nonce_field( 'bsk_gfblcv_list_oper_nonce' );
            echo '
                    </form>
                  </div>';
		}else if ( $current_view == 'addnew' || $current_view == 'edit' ){
			$list_id = -1;
			if(isset($_GET['id']) && $_GET['id']){
				$list_id = trim($_GET['id']);
				$list_id = absint($list_id);
			}
            $this->_bsk_gfblcv_OBJ_list->bsk_gfblcv_list_edit( 
                                                               $list_id, 
                                                               $current_list_view, 
                                                               $current_view 
                                                             );
		}
	}

    function bsk_gfblcv_invitlist_list(){
        
        $current_view = 'list';
        if(isset($_GET['view']) && $_GET['view']){
			$current_view = trim($_GET['view']);
		}
		if(isset($_POST['view']) && $_POST['view']){
			$current_view = trim($_POST['view']);
		}
        
		$current_list_view = 'invitlist';
		$current_base_page = admin_url( 'admin.php?page='.self::$_bsk_gfblcv_pages['invitlist'].'&view='.$current_view );
		
		if( $current_view == 'list' ){
            $_bsk_gfblcv_OBJ_lists = new BSK_GFBLCV_Dashboard_Lists( array( 'list' => 'invitlist' ) );

            //Fetch, prepare, sort, and filter our data...
            $_bsk_gfblcv_OBJ_lists->prepare_items();

            $add_new_page_url = add_query_arg( 'view', 'addnew', $current_base_page );
            echo '<div class="wrap">
                    <div id="icon-edit" class="icon32"><br/></div>
                    <h2>BSK Forms Invitation Codes List<a href="'.$add_new_page_url.'" class="add-new-h2">Add New</a></h2>';
            ?>
                    <div class="bsk-gfblcv-tips-box">
                        <p>This feature only available Pro version</p>
                        <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
                    </div>
            <?php
            echo '  <form id="bsk_gfblcv_lists_form_id" method="post" action="'.$current_base_page.'">';
                        $_bsk_gfblcv_OBJ_lists->display();
            echo '  
                        <input type="hidden" name="bsk_gfblcv_list_id" id="bsk_gfblcv_list_id_to_be_processed_ID" value="0" />
                        <input type="hidden" name="bsk_gfblcv_action" id="bsk_gfblcv_action_ID" value="" />';
                        wp_nonce_field( 'bsk_gfblcv_list_oper_nonce' );
            echo '
                    </form>
                  </div>';
		}else if ( $current_view == 'addnew' || $current_view == 'edit' ){
			$list_id = -1;
			if(isset($_GET['id']) && $_GET['id']){
				$list_id = trim($_GET['id']);
				$list_id = absint($list_id);
			}
            $this->_bsk_gfblcv_OBJ_list->bsk_gfblcv_list_edit( 
                                                               $list_id, 
                                                               $current_list_view, 
                                                               $current_view 
                                                             );
		}
	}
    
    function bsk_gfblcv_blocked_data(){
        $settings_data = get_option( self::$_plugin_settings_option, false );
        $save_blocked_entry = true;
        if( $settings_data && is_array( $settings_data ) && count( $settings_data ) > 0 ){
            if( isset( $settings_data['save_blocked_entry'] ) ){
                $save_blocked_entry = $settings_data['save_blocked_entry'];
            }
        }
        $action_url = admin_url( 'admin.php?page='.self::$_bsk_gfblcv_pages['blocked_data'] );
		?>
        <div class="wrap">
            <div id="icon-edit" class="icon32"><br/></div>
            <h2>BSK Forms Blacklist / White List / Email List / IP List Blocked Data</h3>
            <div style="clear: both;"></div>
            <form action="<?php echo $action_url; ?>" method="POST" id="bsk_gfblcv_entries_form_ID">
            <div>
                <div class="bsk-gfblcv-tips-box">
                    <p>This feature requires ask a <span style="font-weight: bold;">BUSINESS</span>( or above ) license for Pro version</p>
                    <p>To buy a license, please <a href="<?php echo BSK_GFBLCV::$_plugin_home_url; ?>" target="_blank">click here >></a></p>
                </div>
                <h4 style="margin-top: 40px;">Please select form plugin and form name to filter blocked data</h4>
                <p>
                    <?php
                    $selected_form_plugin = '';
                    $current_selected_form_plugin = '';
                    $form_plugin_list = BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_form_plugin();
                    if( isset( $_POST['bsk_gfbl_form_selected_plugin'] ) ){
                        $selected_form_plugin = sanitize_text_field( $_POST['bsk_gfbl_form_selected_plugin'] );
                        $current_selected_form_plugin = sanitize_text_field( $_POST['bsk_gfbl_form_current_selected_plugin'] );
                    }
                    
                    ?>
                    <select name="bsk_gfbl_form_selected_plugin" id="bsk_gfbl_form_selected_plugin_ID" class="bsk-gfbl-entries-filter-select">
                        <option value="">Select form plugin...</option>
                        <?php 
                        if( $form_plugin_list ){
                            foreach( $form_plugin_list as $form_plugin_id => $form_plugin_title ){
                                if( $selected_form_plugin == $form_plugin_id ){
                                    echo '<option value="'.$form_plugin_id.'" selected>'.$form_plugin_title.'</option>';
                                }else{
                                    echo '<option value="'.$form_plugin_id.'">'.$form_plugin_title.'</option>';
                                }
                            }    
                        }
                        ?>
                    </select>
                    <?php
                    $selected_form_id = 0;
                    if( isset( $_POST['bsk_gfbl_form_select_to_list_entries'] ) && $_POST['bsk_gfbl_form_select_to_list_entries'] > 0 ){
                        if ( $current_selected_form_plugin && $current_selected_form_plugin == $selected_form_plugin ) {
                            $selected_form_id = $_POST['bsk_gfbl_form_select_to_list_entries'];
                        }
                    }
                    ?>
                    <select name="bsk_gfbl_form_select_to_list_entries" id="bsk_gfbl_form_select_to_list_entries_ID" class="bsk-gfbl-entries-filter-select" style="display: inline-block; margin-left: 20px;">
                        <option value="">Select form...</option>
                        <?php 
                        if( $selected_form_plugin ){
                            $forms_list = BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_get_gf_forms( $selected_form_plugin );
                            foreach( $forms_list as $form_id => $form_title ){
                                if( $selected_form_id == $form_id ){
                                    echo '<option value="'.$form_id.'" selected>'.$form_title.'</option>';
                                }else{
                                    echo '<option value="'.$form_id.'">'.$form_title.'</option>';
                                }
                            }    
                        }
                        ?>
                    </select>
                </p>
                <div>
                <?php
                $init_args = array();
                $init_args['form_plugin'] = $selected_form_plugin;
                $init_args['form_id'] = $selected_form_id;
                $_bsk_gfblcv_OBJ_entries_lists = new BSK_GFBLCV_Dashboard_Entries_List( $init_args );

                //Fetch, prepare, sort, and filter our data...
                $_bsk_gfblcv_OBJ_entries_lists->prepare_items();
                $_bsk_gfblcv_OBJ_entries_lists->display();
                    
                ?>
                </div>
            </div>
            <p style="margin-top: 40px;">
                <?php wp_nonce_field( 'bsk_gfbcv_settings_save_oper_nonce' ); ?>
                <input type="hidden" name="bsk_gfbl_form_current_selected_plugin" value="<?php echo $selected_form_plugin; ?>" />
            </p>
            </form>
        </div>
        <?php
	}
    
    function bsk_gfblcv_settings(){
        $this->_bsk_gfblcv_OBJ_settings->display();
	}
    
    function bsk_gfblcv_set_option($status, $option, $value) {

        if ( 'bsk_gfblcv_lists_per_page' == $option || 
             'bsk_gfblcv_items_per_page' == $option ){
            
            return $value;
        } 
        
        return $status;
    }
    
    function bsk_gfblcv_delete_entries_fun( $form_id ){
        global $wpdb;
        
        $entries_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_entries_tbl_name;
        $sql = 'DELETE FROM `'.$entries_table.'` WHERE `form_id` = %d';
        $sql = $wpdb->prepare( $sql, $form_id );
        
        $wpdb->query( $sql );
    }
    
    function bsk_gfblcv_max_list_error_notice(){
        if ( !isset( $_GET['list_save'] ) ) {
            return;
        }
        $list_save = sanitize_text_field( $_GET['list_save'] );
        if ( $list_save != 'maxlist' ) {
            return;
        }
		?>
        <div class="notice notice-error is-dismissible">
            <p>You have reached the list maximum. </p>
        </div>
        <?php
	}
    
}
