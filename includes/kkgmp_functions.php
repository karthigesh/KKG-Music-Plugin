<?php
/*
Plugin Name: KKG Music Plugin
Description:  It used to create a music link!
Author: Karthigesh
*/
if ( !defined( 'ABSPATH' ) )
exit;
// Exit if accessed directly
if ( !function_exists( 'wp_get_current_user' ) ) {
    include( ABSPATH . 'wp-includes/pluggable.php' );
}
require_once plugin_dir_path( __FILE__ ) . 'kkgmp_list_table.php';
require_once plugin_dir_path( __FILE__ ) . 'kkgmp_music_page.php';

add_action( 'plugins_loaded', 'kkgmusic_checkPerm' );

function kkgmusic_checkPerm() {
    if ( is_user_logged_in() ) {

        $user = wp_get_current_user();

        $roles = ( array ) $user->roles;
        if ( !in_array( 'administrator', $roles ) ) {
            die;
        }

    }

}

/*
* Add my new menu to the Admin Control Panel
*/
// Hook the 'admin_menu' action hook, run the function named 'kkgmusic_Add_Menu_Link()'
add_action( 'admin_menu', 'kkgmusic_Add_Menu_Link' );

function kkgmusic_Add_Menu_Link() {
    add_menu_page(
        esc_html__( 'KKG Music', 'kkg-music' ),
        esc_html__( 'KKG Music', 'kkg-music' ),
        'manage_options', // Capability requirement to see the link
        'kkg_musics', // The 'slug' - file to display when clicking the link
        'kkgmusic_page_list',
        'dashicons-media-audio',
        90
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
add_action( 'admin_enqueue_scripts', 'kkgmusic_scripts' );

function kkgmusic_scripts() {
    wp_enqueue_style( 'kkgmp-fa', plugin_dir_url( __FILE__ ) . 'fontawesome/css/all.css' );
    wp_enqueue_script( 'kkgmp-fa', plugin_dir_url( __FILE__ ) . 'fontawesome/js/all.js' );
    wp_enqueue_style( 'kkgmp', plugin_dir_url( __FILE__ ) . 'css/css-file.css' );
    wp_enqueue_style( 'kkgmp-bs', plugin_dir_url( __FILE__ ) . 'bootstrap/css/bootstrap.min.css' );
    wp_enqueue_script( 'kkgmp-bs', plugin_dir_url( __FILE__ ) . 'bootstrap/js/bootstrap.min.js' );
    wp_enqueue_script( 'kkgmp', plugin_dir_url( __FILE__ ) . 'js/kkgmp_scripts.js' );
}

function kkgmusic_encryptor( $action, $string ) {
    $output = false;

    $encrypt_method = 'AES-256-CBC';
    //pls set your unique hashing key
    $secret_key = 'kkgmp';
    $secret_iv = 'music190';

    // hash
    $key = hash( 'sha256', $secret_key );

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

    //do the encyption given text/string/number
    if ( $action == 'encrypt' ) {
        $output1 = openssl_encrypt( $string, $encrypt_method, $key, 0, $iv );
        $output = base64_encode( $output1 );
    } else if ( $action == 'decrypt' ) {
        //decrypt the given text/string/number
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }

    return $output;
}

function kkgmusic_page_list() {
    ob_start();
    kkgmusic_listHtml();
}

function kkgmusic_page_add() {
    include( plugin_dir_path( __FILE__ ) . 'inner/kkgmp_url.php' );
}

function kkgmusic_render_input( $inputType, $name, $id, $value = '', $required = FALSE ) {
    $html = '';
    $requiredAttr = ( $required ) ? 'required' : '';
    switch ( $inputType ) {
        case 'text':
        $html = '<input type="text" id="' . $id . '" name="' . $name . '" class="form-control" value="' . sanitize_text_field( $value ) . '" ' . $requiredAttr . '>';
        break;
        case 'url':
        $html = '<input type="url" id="' . $id . '" name="' . $name . '" class="form-control" value="' . sanitize_url( $value ) . '" ' . $requiredAttr . '>';
        break;
        case 'textarea':
        $html = '<textarea name="' . $name . '" id="' . $id . '" class="form-control" ' . $requiredAttr . '>' . sanitize_textarea_field( $value ) . '</textarea>';
        break;
        case 'hidden':
        $html = '<input type = "hidden" name = "' . $name . '" value = "' . sanitize_textarea_field( $value ) . '">';
        break;
        default:
        $html = '';
        break;
    }

    return $html;
}

function kkgmusic_listHtml() {
    if ( filter_input( INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS ) == 'delete_music' ) {
        kkgmusic_remove();
    }
    include( plugin_dir_path( __FILE__ ) . 'inner/kkgmp_list.php' );
}

function kkgmusic_getsingle( $id = 0 ) {
    if ( $id != 0 ) {
        $sMusic = new kkgmusic_music();
        $sMusic->setMusicId( $id );
        return $sMusic->getSingleMusic();
    } else {
        wp_safe_redirect( KKGMP_FAIL_URL, 307 );
        die;
    }
}

function kkgmusic_page_up() {
    ob_start();
    kkgmusic_up();
}

function kkgmusic_up() {
    include( plugin_dir_path( __FILE__ ) . 'inner/kkgmp_upload.php' );
}

function kkgmusic_view() {
    if ( filter_input( INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS ) == 'view_music' ) {
        if ( filter_input( INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS ) == 'view' ) {
            $musicContent = kkgmusic_getsingle( filter_input( INPUT_GET, 'element', FILTER_SANITIZE_NUMBER_INT ) );
            if ( $musicContent ) {
                wp_enqueue_style( 'fa-css-file', plugin_dir_url( __FILE__ ) . 'fontawesome/css/all.css' );
                wp_enqueue_script( 'fa-js-file', plugin_dir_url( __FILE__ ) . 'fontawesome/js/all.js' );
                include( plugin_dir_path( __FILE__ ) . 'inner/kkgmp_view.php' );
            } else {
                header( 'Location: KKGMP_LIST_URL' );
                die;
            }
        } else if ( filter_input( INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS ) == 'delete_music' ) {
            $sMusic = new kkgmusic_music();
            $sMusic->setMusicId( filter_input( INPUT_GET, 'element', FILTER_SANITIZE_NUMBER_INT ) );

            $deleted = $sMusic->deleteMusic();

            header( 'Location: KKGMP_DEL_URL' );
            die;
        }
    }

}

add_action( 'admin_post_kkg_music_upload', 'kkgmusic_upload' );

function kkgmusic_upload() {
    if ( !function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    check_admin_referer( 'kkg_music_upload' );
    $postNum = sanitize_text_field( filter_input( INPUT_POST, 'postNum', FILTER_SANITIZE_SPECIAL_CHARS ) );
    $title = sanitize_text_field( filter_input( INPUT_POST, 'musicTitle', FILTER_SANITIZE_SPECIAL_CHARS ) );
    $wpnonce = sanitize_text_field( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_SPECIAL_CHARS ) );
    if ( wp_verify_nonce( wp_unslash( $wpnonce ), 'kkg_music_upload' ) ) {
        if ( isset( $_FILES ) && ( $_FILES[ 'chooseFile' ][ 'name' ] != '' ) ) {
            $uploadedfile = $_FILES[ 'chooseFile' ];
            $upload_overrides = array(
                'test_form' => false
            );
            $uploads_dir = wp_upload_dir();
            $source      = $_FILES[ 'chooseFile' ][ 'tmp_name' ];
            $filePath = '/kkgmusic_uploads/' . $_FILES[ 'chooseFile' ][ 'name' ];
            $destination = $uploads_dir[ 'basedir' ].'/kkgmusic_uploads/' . $_FILES[ 'chooseFile' ][ 'name' ];
            $dbpath = str_replace( ABSPATH, '', $uploads_dir[ 'basedir' ] );

            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
            if ( $movefile && isset( $movefile[ 'url' ] ) ) {
                $url = $movefile[ 'url' ];
                $sMusic = new kkgmusic_music();
                $upData = array( 'sub_musicurl' => $url, 'mtype' => '2', 'music_title' => $title );
                if ( filter_has_var( INPUT_POST, 'postNum' ) && $postNum != 0 ) {
                    $encMusicId = kkgmusic_encryptor( 'decrypt', $postNum );
                    $sMusic->setMusicId( $encMusicId );
                    $sMusic->updMusic( $upData );
                } else {
                    $sMusic->insMusic( $upData );
                }
                wp_safe_redirect( KKGMP_SUCCF_URL, 307 );
                die;
            } else {
                /**
                * Error generated by _wp_handle_upload()
                * @see _wp_handle_upload() in wp-admin/includes/file.php
                */
                $GLOBALS[ 'uploadError' ] = $movefile[ 'error' ];
                wp_safe_redirect( KKGMP_FAILF_URL, 307 );
                die;
            }
        }
        wp_safe_redirect( KKGMP_SUCCF_URL, 307 );
        die;
    } else {
        wp_die( esc_html__( 'Security check', 'kkg-music' ) );
    }
}

add_action( 'admin_post_kkg_music_save', 'kkgmusic_save' );

function kkgmusic_save() {
    check_admin_referer( 'kkg_music_save' );
    $wpnonce = sanitize_text_field( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_SPECIAL_CHARS ) );
    if ( wp_verify_nonce( wp_unslash( $wpnonce ), 'kkg_music_save' ) ) {
        $url = sanitize_url( filter_input( INPUT_POST, 'musicUrl', FILTER_SANITIZE_URL ), array( 'http', 'https' ) );
        $title = sanitize_text_field( filter_input( INPUT_POST, 'musicTitle', FILTER_SANITIZE_SPECIAL_CHARS ) );
        $postNum = sanitize_text_field( filter_input( INPUT_POST, 'postNum', FILTER_SANITIZE_SPECIAL_CHARS ) );
        if ( ( !filter_var( $url, FILTER_VALIDATE_URL ) === false ) && ( preg_match( '/(https?):\/\/[-a-zA-Z0-9+&@#\/%?=~_|!:,.;]*[-a-zA-Z0-9+&@#\/%=~_|]\.mp3$/', $url ) ) ) {
            $sMusic = new kkgmusic_music();
            $upData = array( 'sub_musicurl' => $url, 'mtype' => '1', 'music_title' => $title );
            if ( filter_has_var( INPUT_POST, 'postNum' ) && $postNum != 0 ) {
                $encMusicId = kkgmusic_encryptor( 'decrypt', $postNum );
                if ( is_int( $encMusicId ) ) {
                    $sMusic->setMusicId( $encMusicId );
                    $sMusic->updMusic( $upData );
                }
            } else {
                $sMusic->insMusic( $upData );
            }

            wp_safe_redirect( KKGMP_SUCCU_URL, 307 );
            die;
        } else {
            wp_safe_redirect( KKGMP_FAIL_URL, 307 );
            die;
        }
    } else {
        wp_die( esc_html__( 'Security check', 'kkg-music' ) );
    }
}
add_action( 'wp_ajax_kkgmusic_delete', 'kkgmusic_delete' );

function kkgmusic_delete() {
    $response = [ 'status'=>false ];
    check_ajax_referer( 'kkgmusic_delete', 'nonce', false );
    $id = sanitize_text_field( filter_input( INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS ) );
    $sMusic = new kkgmusic_music();
    $sMusic->setMusicId( $id );

    $deleted = $sMusic->deleteMusic();

    $response = [ 'status'=>true ];
    $response[ 'url' ] = KKGMP_DEL_URL;

    echo json_encode( $response );
    exit;
}

function kkgmusic_getfront($element) {
    $sMusic = new kkgmusic_music();
    $sMusic->setMusicId( $element );
    return $sMusic->getSingleMusic();
}

// The shortcode function

function kkgmusic_shortcode($atts) {
    extract(shortcode_atts(array(
        'element' => 1,
     ), $atts));
    wp_enqueue_style( 'fa-css-file', plugin_dir_url( __FILE__ ) . 'fontawesome/css/all.css' );
    wp_enqueue_style( 'fa-css1-file', plugin_dir_url( __FILE__ ) . 'frontend/css/kkgmp_style.css' );
    wp_enqueue_script( 'fa-js-file', plugin_dir_url( __FILE__ ) . 'fontawesome/js/all.js' );
    wp_enqueue_script( 'fa-js1-file', plugin_dir_url( __FILE__ ) . 'js/kkgmp_music.js', array(), false, true);
    $musicSect = kkgmusic_getfront($element);
    $html = '<div style="width: 50px; height: 50px;"></div>
  <div class="audio-player" data-id="'.$musicSect['sub_musicurl'].'">
  <audio controls style="display:none"><source src="'.$musicSect['sub_musicurl'].'"  type="audio/mp3"></audio>
    <div class="timeline">
      <div class="progress"></div>
    </div>
    <div class="controls">
      <div class="play-container">
        <div class="toggle-play play">
      </div>
      </div>
      <div class="time">
        <div class="current">0:00</div>
        <div class="divider">/</div>
        <div class="length"></div>
      </div>
      <div class="name">Music Song</div>
      <div class="volume-container">
        <div class="volume-button">
          <div class="volume icono-volumeMedium"></div>
        </div>
        
        <div class="volume-slider">
          <div class="volume-percentage"></div>
        </div>
      </div>
    </div>
  </div>';
  return $html;
}

// Register shortcode
add_shortcode( 'viewkkgmusic', 'kkgmusic_shortcode' );