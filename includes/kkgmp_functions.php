<?php
/*
Plugin Name: KKG Music Plugin
Description: This is my first plugin! It used to create a music link!
Author: Karthigesh
*/
if ( ! defined( 'WPINC' ) ) {
    die;
}

require_once plugin_dir_path( __FILE__ ) . 'kkgmp_list.php';
require_once plugin_dir_path( __FILE__ ) . 'kkgmp_music_page.php';
define( 'KKG_MUSIC_TABLE', 'kkg_music_submissions' );

/*
*Register activation hook
*/
global $kkgmp_db_version;
$kkgmp_db_version = '1.0';
register_activation_hook( __FILE__, 'kkgmp_install' );

function kkgmp_install() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'kkg_music_submissions';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
		sub_id bigint(9) NOT NULL AUTO_INCREMENT,
        music_title text NOT NULL,
        sub_musicurl longtext NOT NULL,
        mtype ENUM('1','2') NOT NULL,
		created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
		PRIMARY KEY  (sub_id)
	) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );

    add_option( 'kkgmp_db_version', $kkgmp_db_version );
}

/*
* Add my new menu to the Admin Control Panel
*/
// Hook the 'admin_menu' action hook, run the function named 'mfp_Add_My_Admin_Link()'
add_action( 'admin_menu', 'kkgmp_Add_Menu_Link' );
// Add a new top level menu link to the ACP

function kkgmp_Add_Menu_Link()
 {
    add_menu_page(
        esc_html__( 'KKG Music', 'kkgmp' ),
        esc_html__( 'KKG Music', 'kkgmp' ),
        'manage_options', // Capability requirement to see the link
        'kkg_musics', // The 'slug' - file to display when clicking the link
        'kkgmp_page_list',
        'dashicons-media-audio',
        6
    );
    add_submenu_page(
        'kkg_musics',
        'Add Music', //page title
        'Add Music', //menu title
        'manage_options', //capability,
        'add_music', //menu slug
        'kkgmp_page_add' //callback function
    );
    add_submenu_page(
        'kkg_musics',
        'Upload Music', //page title
        'Upload Music', //menu title
        'manage_options', //capability,
        'up_music', //menu slug
        'kkgmp_page_up' //callback function
    );
    add_submenu_page(
        '',
        'View Music', //page title
        'View Music', //menu title
        'manage_options', //capability,
        'view_music', //menu slug
        'kkg_music_view' //callback function
    );
}

function kkgmp_scripts() {
    wp_enqueue_style( 'fa-css-file', plugin_dir_url( __FILE__ ) . 'fontawesome/css/all.css' );
    wp_enqueue_script( 'fa-js-file', plugin_dir_url( __FILE__ ) . 'fontawesome/js/all.js' );
    wp_enqueue_style( 'css-file', plugin_dir_url( __FILE__ ) . 'css/css-file.css' );
    wp_enqueue_style( 'css-bootstrap', plugin_dir_url( __FILE__ ) . 'bootstrap/css/bootstrap.min.css' );
    wp_enqueue_script( 'js-bootstrap', plugin_dir_url( __FILE__ ) . 'bootstrap/js/bootstrap.min.js' );
    wp_enqueue_script( 'js-scripts', plugin_dir_url( __FILE__ ) . 'js/music_scripts.js' );
}
add_action( 'admin_enqueue_scripts', 'kkgmp_scripts' );

function kkgmp_page_list()
 {
    ob_start();
    if ( filter_has_var( INPUT_GET, 'action' ) ) {
        $subId = filter_input( INPUT_GET, 'element', FILTER_SANITIZE_SPECIAL_CHARS );
        $action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS );
        kkgmp_musicAction( $subId, $action );
    }
    kkgmp_listHtml();
}

function kkgmp_page_add()
 {
    ob_start();
    kkgmp_addHtml();
}

function kkgmp_render_input( $inputType, $name, $id, $value = '', $required = FALSE )
 {
    $html = '';
    $requiredAttr = ( $required ) ? 'required' : '';
    switch( $inputType ) {
        case 'text':
        $html = '<input type="text" id="' .$id . '" name="' . $name . '" class="form-control" value="' . $value . '" ' . $requiredAttr . '>';
        break;
        case 'url':
        $html = '<input type="url" id="' .$id . '" name="' . $name . '" class="form-control" value="' . $value . '" ' . $requiredAttr . '>';
        break;
        case 'textarea':
        $html = '<textarea name="' . $name . '" id="' .$id . '" class="form-control" ' . $requiredAttr . '></textarea>';
        break;
        case 'select':
        $html = '';
        break;
        default:
        $html = '';
        break;
    }

    return $html;
}

function kkgmp_listHtml() {
    include( plugin_dir_path( __FILE__ ) . 'inner/kkg_music_list.php' );
    if ( isset( $_GET[ 'status' ] ) ) {
        echo "<div class='row mt-2'><div class='col-md-12'>";
        switch( $_GET[ 'status' ] ) {
            case 'success':
            echo  "<div class='alert alert-success' role='alert'>
                        The Music Url has been saved successfully!
                    </div>";
            break;
            case 'failure':
            echo  "<div class='alert alert-warning' role='alert'>
                        There has been issue on saving the Music Url! try again!".$GLOBALS[ 'uploadError' ]." 
                    </div>";

            break;
            case 'delete':
            echo  "<div class='alert alert-danger' role='alert'>
                        The Music Url has been deleted successfully!
                    </div>";
            break;
            case 'deletefail':
            echo  "<div class='alert alert-warning' role='alert'>
                        The Music Url has not been deleted! try again!
                    </div>";

            break;
        }
        echo '</div></div>';
    }
    $myListTable = new kkg_music_list_Table();
    $myListTable->prepare_items();
    echo '<form method="post">
            <input type="hidden" name="page" value="kkg_musics" />';
    $myListTable->search_box( 'Search Music', 'search' );
    echo '</form>';
    $myListTable->display();
    echo  '</div><!--wrap-->';
}

function kkgmp_addHtml() {
    if ( $_POST ) {
        kkgmp_save();
    } else {
        $html = kkgmp_form();
        echo $html;
    }

}

function kkgmp_form() {
    include( plugin_dir_path( __FILE__ ) . 'inner/kkg_music_url.php' );
}

function kkgmp_save() {
    global $wpdb;
    $POST      = array_map( 'stripslashes_deep', $_POST );
    $url = $POST[ 'musicUrl' ];
    $title = $POST[ 'musicTitle' ];
    if ( !filter_var( $url, FILTER_VALIDATE_URL ) === false ) {
        if ( filter_has_var( INPUT_GET, 'action' ) ) {
            $musicId = filter_input( INPUT_GET, 'element' );
            $wpdb->update(
                $wpdb->base_prefix.KKG_MUSIC_TABLE,
                array( 'sub_musicurl' => $url, 'mtype'=>'1', 'music_title'=>$title ),
                array( 'sub_id' => $musicId )
            );
        } else {
            $wpdb->insert(
                $wpdb->base_prefix.KKG_MUSIC_TABLE,
                array( 'sub_musicurl' => $url, 'mtype'=>'1', 'music_title'=>$title ),
                array( '%s' ),
            );
        }
        wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_musics&status=success' ) );
        die;
    } else {
        wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_musics&status=failure' ) );
        die;
    }

}

function getkkgmusic( $id = 0 ) {
    if ( $id != 0 ) {
        $sMusic = new kkgmp_music();
        $sMusic->setMusicId( $id );
        return $sMusic->getSingleMusic();
    } else {
        wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_musics&staus=failure' ) );
        die;
    }

}

function kkgmp_musicAction( $id = 0, $action = '' ) {
    global $wpdb;
    if ( $id != 0 && $action != '' ) {
        if ( $action == 'delete' ) {
            $sMusic = new kkgmp_music();
            $sMusic->setMusicId( $id );
            $deleted = $sMusic->deleteMusic();
            if ( $deleted ) {
                wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_musics&status=delete' ) );
                die;
            } else {
                wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_musics&status=failure' ) );
                die;
            }

        } else {
            wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_musics&status=failure' ) );
            die;
        }
    } else {
        wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_musics&status=failure' ) );
        die;
    }
}

function kkgmp_page_up()
 {
    ob_start();
    kkgmp_upHtml();
}

function kkgmp_upHtml() {
    if ( $_POST ) {
        kkgmp_upload();
    } else {
        kkgmp_up();
    }
}

function kkgmp_up() {
    include( plugin_dir_path( __FILE__ ) . 'inner/kkg_music_upload.php' );
}

function kkgmp_upload() {
    global $wpdb;
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    $POST = array_map( 'stripslashes_deep', $_POST );

    $action = $POST[ 'action' ];
    if ( $action == 'kkg_music_upload' ) {
        if ( isset( $_FILES )  && ( $_FILES[ 'chooseFile' ][ 'name' ] != '' ) ) {
            $uploadedfile = $_FILES[ 'chooseFile' ];
            $upload_overrides = array(
                'test_form' => false
            );

            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

            if ( $movefile && !isset( $movefile[ 'error' ] ) ) {
                $url = $movefile[ 'url' ];
                if ( filter_input( INPUT_GET, 'element' ) ) {
                    $musicId = filter_input( INPUT_GET, 'element' );
                    $wpdb->update(
                        $wpdb->base_prefix.KKG_MUSIC_TABLE,
                        array( 'sub_musicurl' => $url, 'mtype'=>'2' ),
                        array( 'sub_id' => $musicId )
                    );
                } else {
                    $wpdb->insert(
                        $wpdb->base_prefix.KKG_MUSIC_TABLE,
                        array( 'sub_musicurl' => $url, 'mtype'=>'2' ),
                        array( '%s' ),
                    );
                }
                wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_musics&status=success' ) );
                die;
            } else {
                /**
                * Error generated by _wp_handle_upload()
                * @see _wp_handle_upload() in wp-admin/includes/file.php
                */
                $GLOBALS[ 'uploadError' ] = $movefile[ 'error' ];
                wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_musics&status=failure' ) );
                die;
            }
        }
        wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_musics&status=success' ) );
        die;

    }
    wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_musics&status=save' ) );
    die;
}



function kkgmp_view() {
    $action = $_GET;
    if ( isset( $action[ 'page' ] ) && $action[ 'page' ] == 'view_music' ) {
        if ( isset( $action[ 'action' ] ) && $action[ 'action' ] == 'view' ) {
            $musicContent = getkkgmusic( $action[ 'element' ] );
            if ( $musicContent ) {
                $GLOBALS[ 'viewMusicTitle' ] = $musicContent[ 'music_title' ];
                $GLOBALS[ 'viewMusicPlayer' ] = plugins_url( 'kkg_music/includes/inner/imgs/dvd.png', 'kkg_music' );
                $GLOBALS[ 'viewMusicContent' ] = $musicContent[ 'sub_musicurl' ];
                $html = include( plugin_dir_path( __FILE__ ) . 'inner/kkg_music_view.php' );
            } else {
                wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_musics' ) );
                die;

            }
        }
    }
}

function kkgmp_getfrontkkgmusics() {
    $sMusic = new kkgmp_music();
    return $sMusic->getAllMusic();
}

// The shortcode function

function kkgmp_music_shortcode() {
    $musicContent = kkgmp_getfrontkkgmusics();
    $GLOBALS[ 'frontMusic' ] = $musicContent;
    $GLOBALS[ 'viewMusicPlayer' ] = plugins_url( 'kkg_music/includes/inner/imgs/dvd.png', 'kkg_music' );
    include( plugin_dir_path( __FILE__ ) . 'frontend/kkg_music_view.php' );
}
// Register shortcode
add_shortcode( 'view_kkg_music', 'kkgmp_music_shortcode' );
