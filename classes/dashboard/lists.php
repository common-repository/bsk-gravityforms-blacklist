<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BSK_GFBLCV_Dashboard_Lists extends WP_List_Table {
    
    private $_bsk_gfblcv_current_view = 'list';
    private $_bsk_gfblcv_list_view = 'blacklist';
    private $_bsk_gfblcv_list_type = 'BLACK_LIST';
    private $_bsk_gfblcv_page_slug = '';
    
    function __construct( $args ) {
		
		//Set parent defaults
		parent::__construct( array( 
								'singular' => 'bsk-gfblcv-lists',  //singular name of the listed records
								'plural'   => 'bsk-gfblcv-lists', //plural name of the listed records
								'ajax'     => false                          //does this table support ajax?
								) 
						   );
        $this->_bsk_gfblcv_page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['base'];
		$this->_bsk_gfblcv_current_view = ( !empty($_REQUEST['view']) ? $_REQUEST['view'] : 'list');
		$this->_bsk_gfblcv_list_view = $args['list'];
		$this->_bsk_gfblcv_list_type = 'BLACK_LIST';
		if( $this->_bsk_gfblcv_list_view == 'whitelist' ){
			$this->_bsk_gfblcv_list_type = 'WHITE_LIST';
            $this->_bsk_gfblcv_page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['whitelist'];
		}else if( $this->_bsk_gfblcv_list_view == 'emaillist' ){
			$this->_bsk_gfblcv_list_type = 'EMAIL_LIST';
            $this->_bsk_gfblcv_page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['emailist'];
		}else if( $this->_bsk_gfblcv_list_view == 'iplist' ){
			$this->_bsk_gfblcv_list_type = 'IP_LIST';
            $this->_bsk_gfblcv_page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['iplist'];
		}else if( $this->_bsk_gfblcv_list_view == 'invitlist' ){
			$this->_bsk_gfblcv_list_type = 'INVIT_LIST';
            $this->_bsk_gfblcv_page_slug = BSK_GFBLCV_Dashboard::$_bsk_gfblcv_pages['invitlist'];
		}
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
			case 'id':
				echo $item['id_link'];
				break;
			case 'list_name':
				echo $item['list_name'];
				break;
            case 'check_way':
				echo $item['check_way'];
				break;
			case 'items_count':
				if( $this->_bsk_gfblcv_list_type == 'INVIT_LIST' ){
                    echo $item['valid_count'].' / '.$item['items_count'];
                }else{
                    echo $item['items_count'];
                }
				break;
            case 'date':
                echo $item['date'];
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
            'list_name'     	=> 'List Name',
        );
        
        if( $this->_bsk_gfblcv_list_type == 'BLACK_LIST' || $this->_bsk_gfblcv_list_type == 'WHITE_LIST' ){
            $columns['check_way'] = 'Check Way';
        }
        $columns['items_count'] = 'Items Count';
        if( $this->_bsk_gfblcv_list_type == 'INVIT_LIST' ){
            $columns['items_count'] = 'Valid / All codes';
        }
        $columns['date'] = 'Date';
        $columns['action'] = 'Action';
        
        return $columns;
    }
   
	function get_sortable_columns() {
		$c = array(
					'list_name' => 'list_name',
					'date'    	=> 'date'
					);
		
		return $c;
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
		$orderby = 'date';
		$order = 'DESC';
        // check to see if we are searching
        if( isset( $_POST['s'] ) ) {
            $search = trim( $_POST['s'] );
        }
		if ( isset( $_REQUEST['orderby'] ) ){
			$orderby = $_REQUEST['orderby'];
            if ( $orderby != 'list_name' && $orderby != 'date' ) {
                $orderby = 'date';
            }
		}
		if ( isset( $_REQUEST['order'] ) ){
			$order = strtoupper($_REQUEST['order']);
            if ( $order != 'ASC' && $order != 'DESC' ) {
                $order = 'DESC';
            }
		}
		
		$sql = 'SELECT * FROM '.
		       $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_list_tbl_name.' AS l WHERE l.`list_type` = %s ';
		if( $search ){
			$sql .= ' AND l.list_name LIKE %s';
			$sql = $wpdb->prepare( $sql, $this->_bsk_gfblcv_list_type, '%'.$search.'%' );
		}else{
			$sql = $wpdb->prepare( $sql, $this->_bsk_gfblcv_list_type );
		}
		$orderCase = ' ORDER BY l.date DESC';
		if ( $orderby ){
			$orderCase = ' ORDER BY l.`'.$orderby.'` ' . $order;
		}
		$lists = $wpdb->get_results($sql.$orderCase);
		if (!$lists || count($lists) < 1){
			return NULL;
		}
		
        //according to extra data to check IP address
        $base_page_url = admin_url( 'admin.php?page='.$this->_bsk_gfblcv_page_slug );
        $_bsk_gfblcv_OBJ_ip_country = BSK_GFBLCV::instance()->_CLASS_OBJ_ip_country;
        $items_table = $wpdb->prefix.BSK_GFBLCV::$_bsk_gfblcv_items_tbl_name;
		$lists_data = array();
		foreach ( $lists as $list ) {
			$items_count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM `'.$items_table.'` WHERE `list_id` = %d', $list->id) );
            
            if( $list->list_type == 'IP_LIST' && $list->check_way == 'COUNTRY' ){
                $county = '';
                if( $list->extra ){
                    $extra_array = unserialize( $list->extra );
                    if( is_array( $extra_array ) && isset( $extra_array['country'] ) ){
                        $county = $extra_array['country'];
                    }
                }
                $countrys_code = false;
                if( $county ){
                    $countrys_code = explode( ',', $county );
                }
                
                $items_count = 'By country: '.$_bsk_gfblcv_OBJ_ip_country->get_countrys_name_by_code( $countrys_code );
            }
            
            $valid_count = 0;
            if( $this->_bsk_gfblcv_list_type == 'INVIT_LIST' ){
                $valid_count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM `'.$items_table.'` WHERE `list_id` = %d AND `hits` = 0 ', $list->id) );
            }

            //list edit
			$list_edit_url = add_query_arg( 
                                            array('view' 	 => 'edit', 'id' 		 => $list->id),
											$base_page_url 
                                          );
            $list_eidt_anchor = '<a class="bsk-gfblcv-action-anchor bsk-gfblcv-action-anchor-first bsk-gfblcv-admin-edit-list" href="'.$list_edit_url.'">Edit</a>';
            
			//list delete
			$list_delete_url = add_query_arg( 
                                              array('view' 	=> 'delete', 'id' 	=> $list->id),
											  $base_page_url
                                            );
            
			$delete_anchor = '<a class="bsk-gfblcv-action-anchor bsk-gfblcv-admin-delete-list" '.
							 'rel="'.$list->id.'" count="'.$items_count.'">Delete</a>';
			
			//organise data
			$lists_data[] = array( 
			    'id' 				=> $list->id,
				'id_link' 			=> '<a href="' . esc_url( $list_edit_url ) . '">' . esc_html( $list->id ) . '</a>',
				'list_name'     	=> '<a href="' . esc_url( $list_edit_url ) . '">' . esc_html( $list->list_name ) . '</a>',
                'check_way'     	=> ucfirst(strtolower($list->check_way)),
				'date'				=> date('Y-m-d', strtotime($list->date)),
				'action'			=> $list_eidt_anchor.$delete_anchor,
				'items_count'		=> $items_count,
                'valid_count'		=> $valid_count
			);
		}
		
		return $lists_data;
    }

    function prepare_items() {
       
        /**
         * First, lets decide how many records per page to show
         */
        $user = get_current_user_id();
        $per_page = 50;
        
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
	

	
	function get_column_info() {
		
		$columns = array( 
							'cb'        		=> '<input type="checkbox"/>',
							'id'				=> 'ID',
							'list_name'     	=> 'List Name'
                        );
        if( $this->_bsk_gfblcv_list_type == 'BLACK_LIST' || $this->_bsk_gfblcv_list_type == 'WHITE_LIST' ){
            $columns['check_way'] = 'Check Way';
        }
        $columns['items_count'] = 'Items Count';
        $columns['date'] = 'Date';
        $columns['action'] = 'Action';
		if( $this->_bsk_gfblcv_list_type == 'INVIT_LIST' ){
            $columns['items_count'] = 'Valid / All codes';
        }
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