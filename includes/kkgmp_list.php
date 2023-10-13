<?php
/*
Plugin Name: KKG Music Plugin
Description: This is my first plugin! It used to create a music link!
Author: Karthigesh
*/
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class kkg_music_list_Table extends WP_List_Table {

    // Here we will add our code
    // define $table_data property
    private $table_data;

    // Define table columns

    function get_columns()
 {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'music_title'          => __( 'Title', 'kkg_music' ),
            'sub_musicurl'          => __( 'Music URL', 'kkg_music' )
        );
        return $columns;
    }


    // Adding action links to column
    function column_music_title($item)
    {
        $mode = $item[ 'mtype' ];
        $link = ($mode == 1)? 'add_music':'up_music';
        $actions = array(
                'edit'      => sprintf('<a href="?page=%s&action=%s&element=%s">%s</a>', $link, 'edit', $item[ 'sub_id' ],esc_html(__('Edit', 'kkg_music'))),
                'delete'    => sprintf('<a href="?page=%s&action=%s&element=%s">%s</a>', $_REQUEST['page'], 'delete', $item[ 'sub_id' ],esc_html(__('Delete', 'kkg_music'))),
                'view'    => sprintf('<a href="?page=%s&action=%s&element=%s">%s</a>', 'view_music', 'view', $item[ 'sub_id' ],esc_html(__('View', 'kkg_music'))),
        );
        return sprintf('%1$s %2$s', $item['music_title'], $this->row_actions($actions));
    }

    // Bind table with columns, data and all

    function prepare_items()
 {
        $searchcol = array(
            'music_title'
        );
        if ( isset($_POST['s']) && isset($_POST['page']) && $_POST['page'] == 'kkg_musics') {
            $this->table_data = $this->get_table_data($_POST['s']);
        } else {
            $this->table_data = $this->get_table_data();
        }

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $primary = 'sub_musicurl';
        $this->_column_headers = array( $columns, $hidden, $sortable,$primary );
        /* pagination */
        $per_page = 5;
        $current_page = $this->get_pagenum();
        $total_items = count($this->table_data);

        $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
                'total_items' => $total_items, // total number of items
                'per_page'    => $per_page, // items to show on a page
                'total_pages' => ceil( $total_items / $per_page ) // use ceil to round up
        ));
        $this->items = $this->table_data;
    }

    // Get table data

    private function get_table_data($s="") {
        global $wpdb;

        $table = $wpdb->prefix . 'kkg_music_submissions';
        $query = "SELECT * from {$table}";
        if($s != ""){
            $query .= " where music_title like '%{$s}%'";
        }
        return $wpdb->get_results(
            $query,
            ARRAY_A
        );
    }

    function column_default( $item, $column_name )
 {
        switch ( $column_name ) {
            case 'id':
            case 'sub_musicurl':
            default:
            return $item[ $column_name ];
        }
    }

    function column_cb( $item )
 {
        return sprintf(
            '<input type="checkbox" name="element[]" value="%s" />',
            $item[ 'sub_id' ]
        );
    }

    protected function get_sortable_columns()
 {
        $sortable_columns = array(
            'sub_musicurl'  => array( 'sub_musicurl', false )
        );
        return $sortable_columns;
    }

}