<?php
/*
Plugin Name: KKG Music Plugin
Description: It used to create a music link!
Author: Karthigesh
*/
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class kkgmp_list_Table extends WP_List_Table {

    // Here we will add our code
    // define $table_data property
    private $table_data;

    // Define table columns

    function get_columns()
 {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'music_title'          => esc_html__( 'Title', 'kkg-music' ),
            'sub_musicurl'          => esc_html__( 'Music URL', 'kkg-music' )
        );
        return $columns;
    }


    // Adding action links to column
    function column_music_title($item)
    {
        $mode = $item[ 'mtype' ];
        $link = ($mode == 1)? 'add_music':'up_music';
        $itemId = $item['sub_id'];
        $actions = array(
                'edit'      => sprintf('<a href="?page=%s&action=%s&element=%s">%s</a>', $link, 'edit', $itemId,esc_html(__('Edit', 'kkg-music')))
        );
        $nonce = wp_create_nonce( 'kkgmusic_delete' );
        $actions['delete'] = sprintf(
            '<a id="%s" data-id="%s" data-nonce="%s" data-url="%s" data-action="%s">%s</a>',
            'delete_music',
            $itemId,
            $nonce,
            esc_attr('admin-ajax.php'),
            'kkgmusic_delete',
        esc_html(__('Delete', 'kkg-music'))
        );
        $mTitle = sprintf('<a href="?page=%s&action=%s&element=%s">%s</a>', $link, 'edit', $itemId,$item['music_title']);
        $actions['view'] = sprintf('<a href="?page=%s&action=%s&element=%s">%s</a>', 'view_music', 'view', $itemId,esc_html(__('View', 'kkg-music')));
        return sprintf('%1$s %2$s', $mTitle, $this->row_actions($actions));
    }

    // Bind table with columns, data and all

    function prepare_items()
 {
        
        if (filter_has_var(INPUT_POST, 's') && filter_has_var(INPUT_GET, 'page') && filter_input(INPUT_GET, 'page',FILTER_SANITIZE_SPECIAL_CHARS) == 'kkg_musics' ) {
            $searchString = sanitize_text_field(filter_input(INPUT_POST, 's',FILTER_SANITIZE_SPECIAL_CHARS));
            $this->table_data = $this->get_table_data($searchString);
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

        $table = $wpdb->prefix . KKG_MUSIC_TABLE;
        $query = "SELECT * from {$table}";
        if($s != ""){
            $query .= " where music_title like '%{$s}%'";
            return $wpdb->get_results(
                $wpdb->prepare(
                        $query,
                        array($s)
                ),
                ARRAY_A);
        }else{
            $query .= " where mtype IN (%d,%d)";
            return $wpdb->get_results(
                $wpdb->prepare(
                        $query,
                        array(1,2)
                ),
                ARRAY_A);
        }
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
        return array(
            'sub_musicurl'  => array( 'sub_musicurl', false )
        );
    }

    public function search_box( $text, $input_id ) { ?>
        <form method="post">
        <p class="search-box">
          <label class="screen-reader-text" for="<?php echo esc_html($input_id); ?>"><?php echo esc_html($text); ?>:</label>
          <input type="search" id="<?php echo esc_html($input_id); ?>" name="s" value="<?php _admin_search_query(); ?>" />
          <?php submit_button( esc_html($text), 'button', false, false, array('id' => 'search-submit') ); ?>
      </p>
        </form>
    <?php }
    }