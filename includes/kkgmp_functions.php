<?php
/*
Plugin Name: KKG Music Plugin
Description:  It used to create a music link!
Author: Karthigesh
*/
if ( ! defined( 'ABSPATH' ) ) exit;
// Exit if accessed directly

include_once(ABSPATH . "wp-admin/includes/plugin.php");
require_once plugin_dir_path( __FILE__ ) . 'kkgmp_list.php';
require_once plugin_dir_path( __FILE__ ) . 'kkgmp_music_page.php';

if(!function_exists('wp_get_current_user')) {
    include(ABSPATH . "wp-includes/pluggable.php"); 
}
if(! current_user_can('edit_posts')){
    die;
}

define( 'KKG_MUSIC_TABLE', 'kkg_music_submissions' );

/*
*Register activation hook
*/
global $kkgmusic_db_version;
$kkgmusic_db_version = '1.0';
register_activation_hook( __FILE__, 'kkgmusic_install' );

function kkgmusic_install() {
    global $wpdb;

    $table_name = $wpdb->prefix . KKG_MUSIC_TABLE;

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

    add_option( 'kkgmusic_db_version', $kkgmusic_db_version );
}

/*
* Add my new menu to the Admin Control Panel
*/
// Hook the 'admin_menu' action hook, run the function named 'kkgmusic_Add_Menu_Link()'
add_action( 'admin_menu', 'kkgmusic_Add_Menu_Link' );
// Add a new top level menu link to the ACP

function kkgmusic_Add_Menu_Link()
 {
    add_menu_page(
        esc_html__( 'KKG Music', 'kkg-music' ),
        esc_html__( 'KKG Music', 'kkg-music' ),
        'manage_options', // Capability requirement to see the link
        'kkg_musics', // The 'slug' - file to display when clicking the link
        'kkgmusic_page_list',
        'dashicons-media-audio',
        6
    );
    add_submenu_page(
        'kkg_musics',
        'Add Music', //page title
        'Add Music', //menu title
        'manage_options', //capability,
        'add_music', //menu slug
        'kkgmusic_page_add' //callback function
    );
    add_submenu_page(
        'kkg_musics',
        'Upload Music', //page title
        'Upload Music', //menu title
        'manage_options', //capability,
        'up_music', //menu slug
        'kkgmusic_page_up' //callback function
    );
    add_submenu_page(
        '',
        'View Music', //page title
        'View Music', //menu title
        'manage_options', //capability,
        'view_music', //menu slug
        'kkgmusic_view' //callback function
    );
}

function kkgmusic_scripts() {
    wp_enqueue_style( 'kkgmp-fa', plugin_dir_url( __FILE__ ) . 'fontawesome/css/all.css' );
    wp_enqueue_script( 'kkgmp-fa', plugin_dir_url( __FILE__ ) . 'fontawesome/js/all.js' );
    wp_enqueue_style( 'kkgmp', plugin_dir_url( __FILE__ ) . 'css/css-file.css' );
    wp_enqueue_style( 'kkgmp-bs', plugin_dir_url( __FILE__ ) . 'bootstrap/css/bootstrap.min.css' );
    wp_enqueue_script( 'kkgmp-bs', plugin_dir_url( __FILE__ ) . 'bootstrap/js/bootstrap.min.js' );
    wp_enqueue_script( 'kkgmp', plugin_dir_url( __FILE__ ) . 'js/kkgmp_scripts.js' );
}
add_action( 'admin_enqueue_scripts', 'kkgmusic_scripts' );

function kkgmusic_page_list()
 {
    ob_start();
    if ( filter_has_var( INPUT_GET, 'action' ) ) {
        $subId = filter_input( INPUT_GET, 'element', FILTER_SANITIZE_NUMBER_INT );
        $action = htmlspecialchars( $_GET[ 'action' ], ENT_QUOTES );
        kkgmusic_musicAction( $subId, $action );
    }
    kkgmusic_listHtml();
}

function kkgmusic_page_add()
 {
    ob_start();
    kkgmusic_addHtml();
}

function kkgmusic_render_input( $inputType, $name, $id, $value = '', $required = FALSE )
 {
    $html = '';
    $requiredAttr = ( $required ) ? 'required' : '';
    switch( $inputType ) {
        case 'text':
        $html = '<input type="text" id="' .$id . '" name="' . $name . '" class="form-control" value="' . sanitize_text_field( $value ) . '" ' . $requiredAttr . '>';
        break;
        case 'url':
        $html = '<input type="url" id="' .$id . '" name="' . $name . '" class="form-control" value="' . sanitize_url( $value ) . '" ' . $requiredAttr . '>';
        break;
        case 'textarea':
        $html = '<textarea name="' . $name . '" id="' .$id . '" class="form-control" ' . $requiredAttr . '>'.sanitize_textarea_field( $value ).'</textarea>';
        break;
        case 'hidden':
        $html = '<input type = "hidden" name = "' . $name . '" value = "'.sanitize_textarea_field( $value ).'">';
        break;
        default:
        $html = '';
        break;
    }

    return $html;
}

function kkgmusic_listHtml() {
    include( plugin_dir_path( __FILE__ ) . 'inner/kkgmp_list.php' );
    $html = wp_kses( '<div class="wrap">', array( 'div' ) );
    $myListTable = new kkgmp_list_Table();
    $myListTable->prepare_items();
    $html .= wp_kses( '<form method="post">
            <input type="hidden" name="page" value="kkg_musics" />', array( 'form', 'input' ) );
    $html .= $myListTable->search_box( 'Search Music', 'search' );
    $html .= wp_kses( '</form>', array( 'form' ) );
    $html .= $myListTable->display();
    $html .= '</div><!--wrap-->';
    echo $html;
}

function kkgmusic_addHtml() {
    if ( isset( $_POST[ 'action' ] ) ) {
        check_admin_referer( 'kkg_music_save');
        kkgmusic_save();
    } else {
        kkgmusic_form();
    }

}

function kkgmusic_form() {
    include( plugin_dir_path( __FILE__ ) . 'inner/kkgmp_url.php' );
}

function kkgmusic_save() {
    global $wpdb;
    check_admin_referer( 'kkg_music_save');
    if ( isset( $_REQUEST[ '_wpnonce' ] ) && wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'kkg_music_save' ) ) {		
        $url = sanitize_url( $_POST[ 'musicUrl' ], array( 'http', 'https' ) );
        $title = sanitize_text_field( $_POST[ 'musicTitle' ] );
        if ( !filter_var( $url, FILTER_VALIDATE_URL ) === false ) {
            if ( filter_has_var( INPUT_GET, 'action' )  && htmlspecialchars( $_GET[ 'action' ], ENT_QUOTES ) == 'edit' ) {
                $musicId = filter_input( INPUT_GET, 'element', FILTER_SANITIZE_NUMBER_INT );
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
            wp_safe_redirect( esc_url_raw( site_url( '/wp-admin/admin.php?page=kkg_musics&status=successu' ) ), 307 );

            die;
        } else {
            wp_safe_redirect( esc_url_raw( site_url( '/wp-admin/admin.php?page=kkg_musics&status=failure' ) ), 307 );
            die;
        }
    } else {
        die( __( 'Security check', 'kkg-music' ) );
    }

}

function kkgmusic_getsingle( $id = 0 ) {
    if ( $id != 0 ) {
        $sMusic = new kkgmusic_music();
        $sMusic->setMusicId( $id );
        return $sMusic->getSingleMusic();
    } else {
        wp_safe_redirect( esc_url_raw( site_url( '/wp-admin/admin.php?page=kkg_musics&status=failure' ) ), 307 );
        die;
    }

}

function kkgmusic_musicAction( $id = 0, $action = '' ) {
    global $wpdb;
    if ( $id != 0 && $action != '' ) {
        if ( $action == 'delete' ) {
            $sMusic = new kkgmusic_music();
            $sMusic->setMusicId( $id );
            $deleted = $sMusic->deleteMusic();
            if ( $deleted ) {
                wp_safe_redirect( esc_url_raw( site_url( '/wp-admin/admin.php?page=kkg_musics&status=delete' ) ), 307 );
                die;
            }
        }
    }
    wp_safe_redirect( esc_url_raw( site_url( '/wp-admin/admin.php?page=kkg_musics&status=failure' ) ), 307 );
    die;
}

function kkgmusic_page_up()
 {
    ob_start();
    kkgmusic_upHtml();
}

function kkgmusic_upHtml() {
    if ( isset( $_POST[ 'action' ] ) ) {
        check_admin_referer( 'kkg_music_upload');
        kkgmusic_upload();
    } else {
        kkgmusic_up();
    }
}

function kkgmusic_up() {
    include( plugin_dir_path( __FILE__ ) . 'inner/kkgmp_upload.php' );
}

function kkgmusic_upload() {
    global $wpdb;
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    check_admin_referer( 'kkg_music_upload');
    if ( isset( $_REQUEST[ '_wpnonce' ] ) && wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'kkg_music_upload' ) ) {
        $action = sanitize_text_field( $_POST[ 'action' ] );
        if ( $action == 'kkg_music_upload' ) {
            if ( isset( $_FILES )  && ( $_FILES[ 'chooseFile' ][ 'name' ] != '' ) ) {
                $uploadedfile = $_FILES[ 'chooseFile' ];
                $upload_overrides = array(
                    'test_form' => false
                );

                $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

                if ( $movefile && !isset( $movefile[ 'error' ] ) ) {
                    $url = $movefile[ 'url' ];
                    if ( is_int( filter_input( INPUT_GET, 'element', FILTER_SANITIZE_NUMBER_INT ) ) ) {
                        $musicId = filter_input( INPUT_GET, 'element', FILTER_SANITIZE_NUMBER_INT );
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
                    wp_safe_redirect( esc_url_raw( site_url( '/wp-admin/admin.php?page=kkg_musics&status=successf' ) ), 307 );
                    die;
                } else {
                    /**
                    * Error generated by _wp_handle_upload()
                    * @see _wp_handle_upload() in wp-admin/includes/file.php
                    */
                    $GLOBALS[ 'uploadError' ] = $movefile[ 'error' ];
                    wp_safe_redirect( esc_url_raw( site_url( '/wp-admin/admin.php?page=kkg_musics&status=failure' ) ), 307 );
                    die;
                }
            }
            wp_safe_redirect( esc_url_raw( site_url( '/wp-admin/admin.php?page=kkg_musics&status=successf' ) ), 307 );
            die;

        }
    } else {
        die( __( 'Security check', 'kkg-music' ) );
    }
}

function kkgmusic_view() {
    if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'view_music' ) {
        if ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'view' ) {
            $musicContent = kkgmusic_getsingle( filter_input( INPUT_GET, 'element', FILTER_SANITIZE_NUMBER_INT ) );
            if ( $musicContent ) {                
                wp_enqueue_style( 'fa-css-file', plugin_dir_url( __FILE__ ) . 'fontawesome/css/all.css' );
                wp_enqueue_script( 'fa-js-file', plugin_dir_url( __FILE__ ) . 'fontawesome/js/all.js' );
                include( plugin_dir_path( __FILE__ ) . 'inner/kkgmp_view.php' );
            } else {
                wp_safe_redirect( esc_url_raw( site_url( '/wp-admin/admin.php?page=kkg_musics' ) ), 307 );
                die;

            }
        }
    }
}

function kkgmusic_getfront() {
    $sMusic = new kkgmusic_music();
    return $sMusic->getAllMusic();
}

// The shortcode function

function kkgmusic_shortcode() {
    wp_enqueue_style( 'fa-css-file', plugin_dir_url( __FILE__ ) . 'fontawesome/css/all.css' );
    wp_enqueue_style( 'fa-css1-file', plugin_dir_url( __FILE__ ) . 'frontend/css/kkgmp_style.css' );
    wp_enqueue_script( 'fa-js-file', plugin_dir_url( __FILE__ ) . 'fontawesome/js/all.js' );
    include( plugin_dir_path( __FILE__ ) . 'frontend/kkgmp_view.php' );
}
// Register shortcode
add_shortcode( 'view_kkg_music', 'kkgmusic_shortcode' );
