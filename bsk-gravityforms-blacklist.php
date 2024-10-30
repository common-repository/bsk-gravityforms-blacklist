<?php

/*
* Plugin Name: BSK Forms Blacklist
* Plugin URI: https://www.bannersky.com/gravity-forms-blacklist-and-custom-validation/
* Description: The plugin help you avoid spam submissions from GravityForms, Formidable Forms, WP Forms. You may set it to use blacklist, whitelist, ip address or email to validate visitor's input and only allow valid entry submitted. It support validate multiple fields.
* Version: 3.9
* Author: BannerSky.com
* Author URI: http://www.bannersky.com/
* License: GPLv2 or later
* License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin Folder Path.
if ( ! defined( 'BSK_GFBLCV_FREE_DIR' ) ) {
    define( 'BSK_GFBLCV_FREE_DIR', plugin_dir_path( __FILE__ ) );
}
// Plugin Folder URL.
if ( ! defined( 'BSK_GFBLCV_FREE_URL' ) ) {
    define( 'BSK_GFBLCV_FREE_URL', plugin_dir_url( __FILE__ ) );
}

class BSK_GFBLCV {
	
    private static $instance;
    
	public static $_plugin_version = '3.9';
	private static $_bsk_gfblcv_db_version = '3.2';
	private static $_bsk_gfblcv_saved_db_version_option = '_bsk_gfbl_db_ver_';
    private static $_plugin_db_upgrading = '_bsk_gfbl_db_upgrading_';
	
	public static $_bsk_gfblcv_list_tbl_name = 'bsk_gfbl_list';
	public static $_bsk_gfblcv_items_tbl_name = 'bsk_gfbl_items';
    public static $_bsk_gfblcv_entries_tbl_name = 'bsk_gfbl_entries';
    public static $_bsk_gfblcv_hits_tbl_name = 'bsk_gfbl_hits';

	public static $_bsk_gfblcv_temp_option_prefix = '_bsk_gfbl_temp_';

    public static $_integrate_cf7_blacklist_done_option = '_bsk_gfbl_integrate_cf7_blacklist_done_';
    public static $_cf7_blacklist_list_id_mapping = '_bsk_gfbl_cf7_blacklist_list_id_mapping_';
    private static $_integrate_cf7_blacklist_doing_option = '_bsk_gfbl_integrate_cf7_blacklist_doing_';
	
	public static $ajax_loader = '';
    public static $delete_country_code_icon_url = '';
    
    public static $_plugin_home_url = 'https://www.bannersky.com/gravity-forms-blacklist-and-custom-validation/';
    
    public static $_supported_plugins = array();
	
	//objects
	public $_CLASS_OBJ_dashboard;
    public $_CLASS_OBJ_submitting;
    public $_CLASS_OBJ_ip_country;
	
	public static function instance() {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof BSK_GFBLCV ) ) {
            global $wpdb;

            self::$instance = new BSK_GFBLCV;

            /*
            * Initialize variables 
            */
            self::$ajax_loader = '<img src="'.BSK_GFBLCV_FREE_URL.'images/ajax-loader.gif" />';
            self::$delete_country_code_icon_url = BSK_GFBLCV_FREE_URL.'images/delete-2.png';
            
            /*
            * plugin hook
            */
            register_activation_hook(__FILE__, array(self::$instance, 'bsk_gfblcv_activate') );
            register_deactivation_hook( __FILE__, array(self::$instance, 'bsk_gfblcv_deactivate') );
            register_uninstall_hook( __FILE__, 'BSK_GFBLCV::bsk_gfblcv_uninstall' );
            
            self::$instance->init_form_plugins();

            /*
              * classes
              */
            require_once BSK_GFBLCV_FREE_DIR . 'classes/dashboard/common.php';
            require_once BSK_GFBLCV_FREE_DIR . 'classes/dashboard/dashboard.php';
            require_once BSK_GFBLCV_FREE_DIR . 'classes/submitting/submitting.php';
            require_once BSK_GFBLCV_FREE_DIR . 'classes/ip-country/ip-country.php';
        
            self::$instance->_CLASS_OBJ_dashboard = new BSK_GFBLCV_Dashboard();
            self::$instance->_CLASS_OBJ_submitting = new BSK_GFBLCV_Submitting();
            self::$instance->_CLASS_OBJ_ip_country = new BSK_GFBLCV_IP_Country();
            /*
            * Actions
            */
            add_action( 'admin_enqueue_scripts', array(self::$instance, 'bsk_gfblcv_enqueue_scripts_n_css') );
            add_action( 'wp_enqueue_scripts', array(self::$instance, 'bsk_gfblcv_enqueue_scripts_n_css') );
            add_action( 'init', array(self::$instance, 'bsk_gfblcv_post_action') );
            
            add_action( 'plugins_loaded', array(self::$instance, 'bsk_gfblcv_update_database_fun'), 10 );
            add_action( 'plugins_loaded', array(self::$instance, 'bsk_gfblcv_integreate_cf7_fun'), 10 );
        }

        return self::$instance;
	}
	
	function bsk_gfblcv_activate( $network_wide ){
		if ( function_exists('is_multisite') && is_multisite() && $network_wide ) {
			global $wpdb;
			
			$current_blog = $wpdb->blogid;
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ($blog_ids as $blog_id) {
				switch_to_blog( $blog_id );
				
				//create or update table
				self::$instance->bsk_gfblcv_create_table();

			}
			
			switch_to_blog( $current_blog );
		}else{
			//create or update table
			self::$instance->bsk_gfblcv_create_table();
		}
	}
	
	function bsk_gfblcv_deactivate(){
	}
	
	function bsk_gfblcv_remove_tables_n_options(){
		global $wpdb;
		
        $table_list = $wpdb->prefix.'bsk_gfbl_list';
		$table_items = $wpdb->prefix.'bsk_gfbl_items';
        $table_entries = $wpdb->prefix.'bsk_gfbl_entries';
        $table_hits = $wpdb->prefix.'bsk_gfbl_hits';
		
		$wpdb->query("DROP TABLE IF EXISTS $table_list");
		$wpdb->query("DROP TABLE IF EXISTS $table_items");
        $wpdb->query("DROP TABLE IF EXISTS $table_entries");
        $wpdb->query("DROP TABLE IF EXISTS $table_hits");
		
		$sql = 'DELETE FROM `'.$wpdb->options.'` WHERE `option_name` LIKE "_bsk_gfbl%"';
        $wpdb->query( $sql );
        
        $sql = 'DELETE FROM `'.$wpdb->options.'` WHERE `option_name` LIKE "_bsk-gravityforms-blcv%"';
		$wpdb->query( $sql );
        
        $sql = 'DELETE FROM `'.$wpdb->options.'` WHERE `option_name` LIKE "_bsk_forms_blacklist_%"';
		$wpdb->query( $sql );

        delete_option( '_bsk_gfbl_db_ver_' );
        delete_option( '_bsk_gfbl_db_upgrading_' );
        delete_option( '_bsk_gfblcv_free_to_pro_done_' );
        delete_option( '_bsk_gfbl_integrate_cf7_blacklist_done_' );
        delete_option( '_bsk_gfbl_cf7_blacklist_list_id_mapping_' );
        delete_option( '_bsk_gfbl_integrate_cf7_blacklist_doing_' );

        $sql = 'DELETE FROM `'.$wpdb->postmeta.'` WHERE `meta_key` LIKE "_bsk_gfblcv_cf7_%"';
		$wpdb->query( $sql );
	}
	
	public static function bsk_gfblcv_uninstall(){
		if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $has_active_pro_verison = false;
        $plugins = get_plugins();
        foreach( $plugins as $plugin_key => $data ){
            if( 'bsk-gravityforms-blcv-pro/bsk-gravityforms-blcv-pro.php' == $plugin_key && 
                is_plugin_active( $plugin_key ) ){
                $has_active_pro_verison = true;
                break;
            }
        }
        if( $has_active_pro_verison == true ){
            return;
        }
        
		//create or update table
        self::$instance->bsk_gfblcv_remove_tables_n_options();
	}
	
	function bsk_gfblcv_enqueue_scripts_n_css(){
		
		wp_enqueue_script('jquery');
		
		if( is_admin() ){
			wp_enqueue_script( 
                                 'bsk-gfblcv-admin', 
                                 BSK_GFBLCV_FREE_URL.'js/bsk-gfblcv-admin.js',
                                 array( 'jquery' ), 
                                 filemtime( BSK_GFBLCV_FREE_DIR.'js/bsk-gfblcv-admin.js' )
                             );
            if( BSK_GFBLCV_Dashboard_Common::bsk_gfblcv_is_form_plugin_supported('WPF') ) {
                wp_enqueue_script( 
                                    'bsk-gfblcv-admin-wpf',
                                    BSK_GFBLCV_FREE_URL.'js/bsk-gfblcv-admin-wpf.js',
                                    array( 'jquery', 'wpforms-builder' ), 
                                    filemtime( BSK_GFBLCV_FREE_DIR.'js/bsk-gfblcv-admin-wpf.js' )
                                );
            }

			wp_enqueue_style( 
                                'bsk-gfblcv-admin', 
                                BSK_GFBLCV_FREE_URL.'css/bsk-gfblcv-admin.css',
                                array(), 
                                filemtime( BSK_GFBLCV_FREE_DIR.'css/bsk-gfblcv-admin.css' )
                            );
		}else{
			//
		}
	}
	
	function bsk_gfblcv_create_table(){
		global $wpdb;
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$charset_collate = $wpdb->get_charset_collate();
		
		$list_table = $wpdb->prefix.self::$_bsk_gfblcv_list_tbl_name;
		$sql = "CREATE TABLE $list_table (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `list_name` varchar(512) NOT NULL,
		  `list_type` varchar(512) NOT NULL,
          `check_way` VARCHAR(8) NOT NULL DEFAULT 'ANY',
          `extra` VARCHAR(512) NULL,
		  `date` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) $charset_collate;";
		dbDelta( $sql );
        
		$items_table = $wpdb->prefix.self::$_bsk_gfblcv_items_tbl_name;
		$sql = "CREATE TABLE $items_table (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `list_id` int(11) NOT NULL,
		  `value` varchar(512) NOT NULL,
          `hits` int(11) NOT NULL DEFAULT '0',
          `extra` varchar(512) NULL,
		  PRIMARY KEY (`id`)
		) $charset_collate;";
		dbDelta($sql);
        
        $entries_table = $wpdb->prefix.self::$_bsk_gfblcv_entries_tbl_name;
		$sql = "CREATE TABLE $entries_table (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
          `forms` varchar(32) NOT NULL DEFAULT 'GF',
          `form_id` int(11) NOT NULL,
          `form_data` TEXT NOT NULL,
          `ip` varchar(256) NOT NULL,
          `submit_date` datetime NOT NULL,
		  PRIMARY KEY (`id`)
		) $charset_collate;";
		dbDelta($sql);
        
        $hits_table = $wpdb->prefix.self::$_bsk_gfblcv_hits_tbl_name;
		$sql = "CREATE TABLE $hits_table (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
          `entry_id` int(11) NOT NULL,
          `field_id` varchar(32) NOT NULL,
          `list_id` int(11) NOT NULL,
          `item_id` int(11) NOT NULL,
          `extra_data` varchar(512) NOT NULL,
          `submit_date` datetime NOT NULL,
		  PRIMARY KEY (`id`)
		) $charset_collate;";
		dbDelta($sql);
        
		update_option( self::$_bsk_gfblcv_saved_db_version_option, self::$_bsk_gfblcv_db_version );
	}

	function bsk_gfblcv_post_action(){
		if( isset( $_POST['bsk_gfblcv_action'] ) && strlen($_POST['bsk_gfblcv_action']) > 0 ) {
			do_action( 'bsk_gfblcv_' . $_POST['bsk_gfblcv_action'], $_POST );
		}
		
		if( isset( $_GET['bsk-gfblcv-action'] ) && strlen($_GET['bsk-gfblcv-action']) > 0 ) {
			do_action( 'bsk_gfblcv_' . $_GET['bsk-gfblcv-action'], $_GET );
		}
	}
	
    public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__,  'Cheatin&#8217;', '1.0' );
	}
    
    public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__,  'Cheatin&#8217;', '1.0' );
	}
    
    function bsk_gfblcv_update_database_fun(){
        $db_version = get_option( self::$_bsk_gfblcv_saved_db_version_option );
		if ( version_compare( $db_version, self::$_bsk_gfblcv_db_version, '>=' ) ) {
			return;
		}
        
        $is_upgrading = get_option( self::$_plugin_db_upgrading, false );
        if( $is_upgrading ){
            //already have instance doing upgrading so exit this one
            return;
        }
        update_option( self::$_plugin_db_upgrading, true );
        
		global $wpdb;
        
        if( version_compare( $db_version, '2.0', '<' ) ){
            //add new field
            $table_name = $wpdb->prefix . self::$_bsk_gfblcv_items_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `hits`  INT(11) DEFAULT \'0\' AFTER `value`';
            $wpdb->query( $sql );
        }
        
        //for version 2.5
        if( version_compare( $db_version, '2.5', '<' ) ){
            $table_name = $wpdb->prefix . self::$_bsk_gfblcv_list_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `check_way` VARCHAR(8) NOT NULL DEFAULT \'ANY\' AFTER `list_type`';
            $wpdb->query( $sql );
        }
        
        //for version 2.6
        if( version_compare( $db_version, '2.6', '<' ) ){
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		    $charset_collate = $wpdb->get_charset_collate();
            
            $entries_table = $wpdb->prefix.self::$_bsk_gfblcv_entries_tbl_name;
            $sql = "CREATE TABLE $entries_table (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `form_id` int(11) NOT NULL,
              `form_data` TEXT NOT NULL,
              `ip` varchar(256) NOT NULL,
              `submit_date` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) $charset_collate;";
            dbDelta($sql);
            
            $hits_table = $wpdb->prefix.self::$_bsk_gfblcv_hits_tbl_name;
            $sql = "CREATE TABLE $hits_table (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `entry_id` int(11) NOT NULL,
              `field_id` varchar(32) NOT NULL,
              `list_id` int(11) NOT NULL,
              `item_id` int(11) NOT NULL,
              `extra_data` varchar(512) NOT NULL,
              `submit_date` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) $charset_collate;";
            dbDelta($sql);
        }
        
        if( version_compare( $db_version, '2.7', '<' ) ){
            $table_name = $wpdb->prefix . self::$_bsk_gfblcv_list_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `extra` VARCHAR(512) NULL AFTER `check_way`';
            $wpdb->query( $sql );
        }
        
        if( version_compare( $db_version, '2.8', '<' ) ){
            $table_name = $wpdb->prefix . self::$_bsk_gfblcv_hits_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` CHANGE `field_id` `field_id` VARCHAR(32) NOT NULL;';
            $wpdb->query( $sql );
        }
        
        /* if( version_compare( $db_version, '2.9', '<' ) ){
            $table_name = $wpdb->prefix . self::$_bsk_gfblcv_hits_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `forms` VARCHAR(32) NOT NULL DEFAULT \'GF\' AFTER `id`;';
            $wpdb->query( $sql );
        } */

        if( version_compare( $db_version, '3.0', '<' ) ){
            $table_name = $wpdb->prefix . self::$_bsk_gfblcv_items_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `extra` VARCHAR(32) NULL AFTER `hits`;';
            $wpdb->query( $sql );
        }

        if( version_compare( $db_version, '3.1', '<' ) ){
            $table_name = $wpdb->prefix . self::$_bsk_gfblcv_entries_tbl_name;

            $sql = 'SHOW COLUMNS FROM `'.$table_name.'` LIKE \'forms\'';
            $return_rows = $wpdb->query( $sql );
            if ( $return_rows < 1 ) {
                $sql = 'ALTER TABLE `'.$table_name.'` ADD `forms` VARCHAR(32) NOT NULL DEFAULT \'GF\' AFTER `id`;';
                $wpdb->query( $sql );
            }
        }

        if( version_compare( $db_version, '3.2', '<' ) ){
            $table_name = $wpdb->prefix . self::$_bsk_gfblcv_items_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` CHANGE `extra` `extra` VARCHAR(256) NULL DEFAULT NULL;';
            $wpdb->query( $sql );
        }
        
        update_option( self::$_bsk_gfblcv_saved_db_version_option, self::$_bsk_gfblcv_db_version );
        delete_option( self::$_plugin_db_upgrading );
    }
    
    function bsk_gfblcv_pro_deactivate_free(){
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = get_plugins();
        foreach( $plugins as $plugin_key => $data ){
            if( 'bsk-gravityforms-blacklist/bsk-gravityforms-blacklist.php' != $plugin_key || 
                is_plugin_active( $plugin_key ) == false ){
                continue;
            }
            deactivate_plugins( $plugin_key );
            wp_redirect( admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_page.'&upgrade=yes' ) ); 
            exit;
        }
    }
    
    function init_form_plugins(){
        if ( ! function_exists( 'is_plugin_active' ) ){
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();
        
        //gravity forms
        if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ){
            $plugin_version = $all_plugins['gravityforms/gravityforms.php']['Version'];
            self::$_supported_plugins['GF'] = array( 'title' => 'Gravity Forms', 'version' => $plugin_version );
        }
        
		//ninja forms
        if ( is_plugin_active( 'ninja-forms/ninja-forms.php' ) ){
            $plugin_version = $all_plugins['ninja-forms/ninja-forms.php']['Version'];
            self::$_supported_plugins['NF'] = array( 'title' => 'Ninja Forms', 'version' => $plugin_version );
        }
		
		//formidable forms
        if ( is_plugin_active( 'formidable/formidable.php' ) ){
            $plugin_version = $all_plugins['formidable/formidable.php']['Version'];
            self::$_supported_plugins['FF'] = array( 'title' => 'Formidable Forms', 'version' => $plugin_version );
        }
		
		//Contact7 forms
        if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ){
			$plugin_version = $all_plugins['contact-form-7/wp-contact-form-7.php']['Version'];
            self::$_supported_plugins['CF7'] = array( 'title' => 'Contact Form 7', 'version' => $plugin_version );
		}
        
        //WPFoms
        if ( is_plugin_active( 'wpforms-lite/wpforms.php' ) || is_plugin_active( 'wpforms/wpforms.php' ) ){
            $plugin_version = '';
            if ( is_plugin_active( 'wpforms-lite/wpforms.php' ) ){
                $plugin_version = $all_plugins['wpforms-lite/wpforms.php']['Version'];
            } elseif ( is_plugin_active( 'wpforms/wpforms.php' ) ) {
                $plugin_version = $all_plugins['wpforms/wpforms.php']['Version'];
            }
			
            self::$_supported_plugins['WPF'] = array( 'title' => 'WPForms', 'version' => $plugin_version );
		}

        //Forminator
        if ( is_plugin_active( 'forminator/forminator.php' ) ) {
            $plugin_version = $all_plugins['forminator/forminator.php']['Version'];
            self::$_supported_plugins['FRMT'] = array( 'title' => $all_plugins['forminator/forminator.php']['Title'], 'version' => $plugin_version );
        }
    }

    function bsk_gfblcv_integreate_cf7_fun() {

        $cf7_integrated = get_option( self::$_integrate_cf7_blacklist_done_option, false );
        if ( $cf7_integrated ) {
            return;
        }

        $is_upgrading = get_option( self::$_integrate_cf7_blacklist_doing_option, false );
        if( $is_upgrading ){
            //already have instance doing upgrading so exit this one
            return;
        }
        update_option( self::$_integrate_cf7_blacklist_doing_option, true );

        if ( ! function_exists( 'is_plugin_active' ) ){
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
        
        //cf7 blacklist plugins
        if ( ! is_plugin_active( 'bsk-contact-form-7-blacklist/bsk-contact-form-7-blacklist.php' ) ){
            
            delete_option( self::$_integrate_cf7_blacklist_doing_option );
            update_option( self::$_integrate_cf7_blacklist_done_option, true );
            return;
        }

        global $wpdb;

        $cf7_blacklist_tbl_name = $wpdb->prefix . 'cf7_blacklist_list';
        $cf7_items_tbl_name = $wpdb->prefix . 'cf7_blacklist_items';

        //move all list and items
        $sql = 'SELECT * FROM `' . $cf7_blacklist_tbl_name . '` WHERE 1';
        $cf7_blacklist_results = $wpdb->get_results( $sql );
        if ( ! $cf7_blacklist_results || ! is_array( $cf7_blacklist_results ) || count( $cf7_blacklist_results ) < 1 ) {
            
            delete_option( self::$_integrate_cf7_blacklist_doing_option );
            update_option( self::$_integrate_cf7_blacklist_done_option, true );
            $this->bsk_gfblcv_pro_deactivate_cf7_blacklist_plugins();
            
            return;
        }

        $_cf7_blacklist_list_id_mapping = array();
        foreach( $cf7_blacklist_results as $cf7_blacklist_obj ) {
            $old_id = $cf7_blacklist_obj->id;
            $data = array(
                'list_name' => $cf7_blacklist_obj->list_name,
                'list_type' => $cf7_blacklist_obj->list_type,
                'date' => $cf7_blacklist_obj->date,
            );

            $wpdb->insert( $wpdb->prefix . self::$_bsk_gfblcv_list_tbl_name, $data, array( '%s', '%s', '%s' ) );
            $new_id = $wpdb->insert_id;
            if ( $new_id < 1 ) {
                continue;
            }

            //update the item's list id
            $wpdb->update( $cf7_items_tbl_name, array( 'list_id' => $new_id ), array( 'list_id' => $old_id ) );

            $sql = 'INSERT INTO `'. $wpdb->prefix . self::$_bsk_gfblcv_items_tbl_name . '`( `list_id`, `value` ) SELECT `list_id`, `value` FROM `'. $cf7_items_tbl_name . '` AS CF7 WHERE CF7.`list_id` = ' . $new_id;
            $wpdb->query( $sql );

            $sql = 'DELETE FROM `'. $cf7_items_tbl_name . '` WHERE `list_id` = ' . $new_id;
            $wpdb->query( $sql );

            $sql = 'DELETE FROM `'. $cf7_blacklist_tbl_name . '` WHERE `id` = ' . $old_id;
            $wpdb->query( $sql );

            $_cf7_blacklist_list_id_mapping[$old_id] = $new_id;
        }
        update_option( self::$_cf7_blacklist_list_id_mapping, $_cf7_blacklist_list_id_mapping );

        delete_option( self::$_integrate_cf7_blacklist_doing_option );
        update_option( self::$_integrate_cf7_blacklist_done_option, true );

        //set plugin settings to support CF7
        $supported_form_plugins = array( 'CF7' );
        $save_blocked_entry = 'NO';
        $notify_blocked = 'NO';
        $notify_details = false;

        $settings_data = array();
        $settings_data['supported_form_plugins'] = $supported_form_plugins;
        $settings_data['save_blocked_entry'] = $save_blocked_entry;
        $settings_data['notify_blocked'] = $notify_blocked;
        $settings_data['notify_details'] = $notify_details;
        update_option( BSK_GFBLCV_Dashboard::$_plugin_settings_option, $settings_data );

        $this->bsk_gfblcv_pro_deactivate_cf7_blacklist_plugins();
        
    }

    function bsk_gfblcv_pro_deactivate_cf7_blacklist_plugins() {
        //deactivate plugin
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = get_plugins();
        foreach( $plugins as $plugin_key => $data ) {
            if ( is_plugin_active( $plugin_key ) == false ) {
                continue;
            }

            if ( 'bsk-contact-form-7-blacklist/bsk-contact-form-7-blacklist.php' != $plugin_key ) {
                
                continue;
            }

            deactivate_plugins( $plugin_key );
        }
    }
}

BSK_GFBLCV::instance();
