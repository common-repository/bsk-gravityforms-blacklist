<?php

class BSK_GFBLCV_Dashboard_Forminator_Forms_List extends WP_List_Table {

	private $_bsk_gfblcv_current_view = 'list';
    private $_bsk_gfblcv_page_slug = '';
    
    function __construct( $args ) {
		
		//Set parent defaults
		parent::__construct( array( 
								'singular' => 'bsk-gfblcv-forminator-forms-lists',  //singular name of the listed records
								'plural'   => 'bsk-gfblcv-forminator-forms-lists', //plural name of the listed records
								'ajax'     => false                          //does this table support ajax?
								) 
						   );
        $this->_bsk_gfblcv_page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['forminator_blacklist'];
		$this->_bsk_gfblcv_current_view = ( !empty($_REQUEST['view']) ? $_REQUEST['view'] : 'list');
	
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
			case 'id':
				echo '<a href="' . $item['edit_link'] .'">' . $item['id'] . '</a>';
			break;
			case 'form_name':
				echo '<a href="' . $item['edit_link'] .'">' . $item['form_name'] . '</a>';
			break;
            case 'form_status':
                if ( $item['form_status'] == 'publish' ) {
                    echo '<span class="sui-tag sui-tag-blue">Published</span>';
                } else if ( $item['form_status'] == 'draft' ) {
                    echo '<span class="sui-tag">Draft</span>';
                } else {
                    echo $item['form_status'];
                }
            break;
            case 'blacklist_status':
                echo $item['blacklist_status'];
            break;
			case 'action':
				echo '<a class="bsk-gfblcv-action-anchor bsk-gfblcv-action-anchor-first bsk-gfblcv-forminator-settings" href="' . $item['settings_link'] . '">Form Settings &amp; Field Settings</a>';
            break;
        }
    }
   
    function column_cb( $item ) {
        return sprintf( 
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            esc_attr( $this->_args['singular'] ),
            esc_attr( $item['id'] )
        );
    }

    function get_columns() {
    
        $columns = array( 
			'cb'        		=> '<input type="checkbox"/>',
			'id'				=> 'Form ID',
            'form_name'     	=> 'Form Name',
            'form_status'     	=> 'Form Status',
            'blacklist_status'  => 'Blacklist Status',
            'action'            => 'Action',
        );
        
        return $columns;
    }
   
	function get_sortable_columns() {
		$c = array(
					'form_name' => 'form_name',
					'id'    	=> 'id'
					);
		
		return $c;
	}

    function get_column_info() {
		
		$columns = array( 
			'cb'        		=> '<input type="checkbox"/>',
			'id'				=> 'Form ID',
            'form_name'     	=> 'Form Name',
            'form_status'     	=> 'Form Status',
            'blacklist_status'  => 'Blacklist Status',
            'action'            => 'Action',
        );

        $hidden = array();

		$_sortable = apply_filters( "manage_{$this->screen->id}_sortable_columns", $this->get_sortable_columns() );
		$sortable = array();
		foreach ( $_sortable as $id => $data ) {
			if ( empty( $data ) )
				continue;

			$data = (array) $data;
			if ( !isset( $data[1] ) )
				$data[1] = false;

			$sortable[$id] = $data;
		}

		$_column_headers = array( $columns, $hidden, $sortable, array() );

		return $_column_headers;
	}
	
    function get_views() {

        return array();
    }
   
    function get_bulk_actions() {
    
        $actions = array( 
            //'delete'=> 'Delete'
        );
        
        return $actions;
    }

    function do_bulk_action() {
    }

    function get_data() {
		global $wpdb;
		
		$search = '';
		$orderby = 'id';
		$order = 'DESC';
        // check to see if we are searching
        if( isset( $_POST['s'] ) ) {
            $search = trim( $_POST['s'] );
        }
		if ( isset( $_REQUEST['orderby'] ) ){
			$orderby = $_REQUEST['orderby'];
		}
		if ( isset( $_REQUEST['order'] ) ){
			$order = sanitize_text_field( $_REQUEST['order'] );
		}
		
        $forms_data = Forminator_API::get_forms( null, 1, 100 );
		$forms_array = array();
        if ( is_array( $forms_data ) && count( $forms_data ) > 0 ) {
            $forminator_base_page = admin_url( 'admin.php?page='.BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['forminator_blacklist'] );
            foreach ( $forms_data as $form_obj ) {
                $edit_link = admin_url( 'admin.php?page=forminator-cform-wizard&id=' . $form_obj->id );
                $settings_link = $forminator_base_page . '&id=' . $form_obj->id . '&view=settings';
                $settings_data = get_option( BSK_GFBLCV_Dashboard_Forminator::$_bsk_gfblcv_frmt_form_settings_option_name_prefix . $form_obj->id, false );
                
                $forms_array[$form_obj->id] = array( 
                                                        'id' => $form_obj->id, 
                                                        'edit_link' => $edit_link, 
                                                        'name' => $form_obj->name, 
                                                        'form_name' => $form_obj->settings['formName'],
                                                        'form_status' => $form_obj->status,
                                                        'blacklist_status'  => ( is_array( $settings_data ) && $settings_data['enable'] ? 'Enabled' : 'Disabled' ),
                                                        'settings_link' => $settings_link,
                                                   );
            }
        }

        if ( $orderby == 'id' ) {
            uasort( $forms_array, array( $this, 'sort_forms_by_id_' . strtolower( $order ) ) );
        } else if ( $orderby == 'form_name' ) {
            uasort( $forms_array, array( $this, 'sort_forms_by_form_name_' . strtolower( $order ) ) );
        }
		
		return $forms_array;
    }

    function sort_forms_by_id_desc( $a, $b ) {
        if ( $a['id'] == $b['id'] ) {
            return 0;
        }

        return ( $a['id'] > $b['id'] ) ? -1 : 1;
    }

    function sort_forms_by_id_asc( $a, $b ) {
        if ( $a['id'] == $b['id'] ) {
            return 0;
        }

        return ( $a['id'] < $b['id'] ) ? -1 : 1;
    }

    function sort_forms_by_form_name_asc( $a, $b ) {
        return strnatcmp( $a['form_name'], $b['form_name'] ); 
    }

    function sort_forms_by_form_name_desc( $a, $b ) {
        $str_ret = strnatcmp( $a['form_name'], $b['form_name'] ); 

        if ( $str_ret == -1 ) {
            return 1;
        } else if ( $str_ret == 1 ) {
            return -1;
        }

        return 0;
    }

    function prepare_items() {
       
        /**
         * First, lets decide how many records per page to show
         */
        $user = get_current_user_id();
        $per_page = 20;
        
        $data = array();
		
        add_thickbox();

		$this->do_bulk_action();
       
        $data = $this->get_data();

        $current_page = $this->get_pagenum();
        $total_items = 0;
        if( $data && is_array( $data ) ){
            count( $data );
        }
        
	    if ($total_items > 0){
        	$data = array_slice( $data,( ( $current_page-1 )*$per_page ),$per_page );
		}
        $this->items = $data;

        $this->set_pagination_args( array( 
            'total_items' => $total_items,                  // We have to calculate the total number of items
            'per_page'    => $per_page,                     // We have to determine how many items to show on a page
            'total_pages' => ceil( $total_items/$per_page ) // We have to calculate the total number of pages
        ) );
    }
	

}
