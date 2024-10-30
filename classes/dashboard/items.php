<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BSK_GFBLCV_Dashboard_Items extends WP_List_Table {
    
    private $_bsk_gfblcv_list_id = 0;
    private $_bsk_gfblcv_list_type = '';
    
    function __construct( $list_id, $list_type ) {
        $this->_bsk_gfblcv_list_id = $list_id;
        $this->_bsk_gfblcv_list_type = $list_type;
        
        //Set parent defaults
        parent::__construct( array( 
            'singular' => 'bsk-gfblcv-item',  //singular name of the listed records
            'plural'   => 'bsk-gfblcv-items', //plural name of the listed records
            'ajax'     => false                          //does this table support ajax?
        ) );
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
			case 'id':
				echo $item['id'];
				break;
			case 'value':
				echo $item['value'];
				break;
            case 'hits':
				echo $item['hits'];
				break;
			case 'action':
				echo $item['action'];
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
			'id'				=> 'ID',
            'value'     		=> 'Content',
        );
    
        if( $this->_bsk_gfblcv_list_type != 'WHITE_LIST' ){
            $columns['hits'] = 'Hits';
        }
        if( $this->_bsk_gfblcv_list_type == 'INVIT_LIST' ){
            $columns['hits'] = 'Extra';
        }
        $columns['action'] = 'Action';
        
        return $columns;
    }
   
	function get_sortable_columns() {
		$c = array( 'value' => 'value', 'id' => 'id' );
        if( $this->_bsk_gfblcv_list_type != 'WHITE_LIST' ){
            $c['hits'] = 'hits';
        }
        
		return $c;
	}
	
    function get_views() {
        return array();
    }
   
    function get_bulk_actions() {
    
        $actions = array( 
            'delete'=> 'Delete'
        );
        
        return $actions;
    }

    function do_bulk_action() {
        global $wpdb;
        
        $items_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_items_tbl_name;
        
		if( isset($_POST['bsk-gfblcv-item']) && count($_POST['bsk-gfblcv-item']) > 0 ){
			
			if( $_POST['action'] == 'delete' || $_POST['action2'] == 'delete' ){
				$sql = 'DELETE FROM `'.$items_table.'` WHERE `id` IN('.implode(',', $_POST['bsk-gfblcv-item']).')';
				$wpdb->query( $sql );
			}
		}
    }

    function get_data() {
		global $wpdb;
		
        $items_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_items_tbl_name;
        
		$search = '';
		$orderby = 'value';
		$order = 'DESC';
        // check to see if we are searching
        if( isset( $_POST['s'] ) ) {
            $search = trim( $_POST['s'] );
        }
		if ( isset( $_REQUEST['orderby'] ) ){
			$orderby = $_REQUEST['orderby'];
            if ( $orderby != 'list_name' && $orderby != 'date' && $orderby != 'hits' && $orderby != 'id' ) {
                $orderby = 'value';
            }
		}
		if ( isset( $_REQUEST['order'] ) ){
			$order = strtoupper($_REQUEST['order']);
            if ( $order != 'ASC' && $order != 'DESC' ) {
                $order = 'DESC';
            }
		}
		
		$sql = 'SELECT * FROM `'.
		       $items_table.'` AS i WHERE i.`list_id` = %d ';
		if( $search ){
			$sql .= ' AND i.`value` LIKE %s';
			$sql = $wpdb->prepare( $sql, $this->_bsk_gfblcv_list_id, '%'.$search.'%' );
		}else{
			$sql = $wpdb->prepare( $sql, $this->_bsk_gfblcv_list_id );
		}
		$orderCase = ' ORDER BY i.`value` DESC';
        if( $this->_bsk_gfblcv_list_type == 'INVIT_LIST' ){
            $orderCase = ' ORDER BY i.`id` DESC';
        }
		if ( $orderby ){
			$orderCase = ' ORDER BY i.`'.$orderby.'` '.$order;
		}
		$items = $wpdb->get_results($sql.$orderCase);
		if (!$items || count($items) < 1){
			return NULL;
		}
		
		$items_data = array();
		foreach ( $items as $item ) {
			$delete_anchor = '<a class="bsk-gfblcv-action-anchor bsk-gfblcv-action-anchor-first bsk-gfblcv-item-delete-anchor" rel="'.$item->id.'">Delete</a>';
			
			//organise data
			$lists_data[] = array( 
			    'id' 				=> $item->id,
				'value'     		=> $item->value,
                'hits'     		    => $item->hits,
				'action'			=> $delete_anchor
			);
		}
		
		return $lists_data;
    }

    function prepare_items() {
       
        /**
         * First, lets decide how many records per page to show
         */
        $user = get_current_user_id();
        $screen = get_current_screen();
        $per_page = 50;
        
        $data = array();
		
        add_thickbox();

		$this->do_bulk_action();
       
        $data = $this->get_data();
   
        $current_page = $this->get_pagenum();
        $total_items = 0;
        if( $data && is_array( $data ) ){
            $total_items = count( $data );
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
	

	
	function get_column_info() {
		
		$columns = array( 
							'cb'        		=> '<input type="checkbox"/>',
							'id'				=> 'ID',
							'value'     		=> 'Content',
						);
		if( $this->_bsk_gfblcv_list_type != 'WHITE_LIST' ){
            $columns['hits'] = 'Hits';
        }
        if( $this->_bsk_gfblcv_list_type == 'INVIT_LIST' ){
            $columns['hits'] = 'Extra';
        }
        $columns['action'] = 'Action';
        
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
}