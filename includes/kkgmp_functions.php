<?php

/*
  Plugin Name: KKG Music Plugin
  Description:  It used to create a music link!
  Author: Karthigesh
 */
if (!defined('ABSPATH'))
    exit;
// Exit if accessed directly
if (!function_exists('wp_get_current_user')) {
    include(ABSPATH . "wp-includes/pluggable.php");
}
require_once plugin_dir_path(__FILE__) . 'kkgmp_list_table.php';
require_once plugin_dir_path(__FILE__) . 'kkgmp_music_page.php';
add_action('init', 'kkgmusic_checkPerm');

function kkgmusic_checkPerm() {
    if (!current_user_can('edit_posts')) {
        die;
    }
}

/*
 * Add my new menu to the Admin Control Panel
 */
// Hook the 'admin_menu' action hook, run the function named 'kkgmusic_Add_Menu_Link()'
add_action('admin_menu', 'kkgmusic_Add_Menu_Link');

function kkgmusic_Add_Menu_Link() {
    add_menu_page(
            esc_html__('KKG Music', 'kkg-music'),
            esc_html__('KKG Music', 'kkg-music'),
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
    wp_enqueue_style('kkgmp-fa', plugin_dir_url(__FILE__) . 'fontawesome/css/all.css');
    wp_enqueue_script('kkgmp-fa', plugin_dir_url(__FILE__) . 'fontawesome/js/all.js');
    wp_enqueue_style('kkgmp', plugin_dir_url(__FILE__) . 'css/css-file.css');
    wp_enqueue_style('kkgmp-bs', plugin_dir_url(__FILE__) . 'bootstrap/css/bootstrap.min.css');
    wp_enqueue_script('kkgmp-bs', plugin_dir_url(__FILE__) . 'bootstrap/js/bootstrap.min.js');
    wp_enqueue_script('kkgmp', plugin_dir_url(__FILE__) . 'js/kkgmp_scripts.js');
}

add_action('admin_enqueue_scripts', 'kkgmusic_scripts');

function kkgmusic_encryptor($action, $string) {
    $output = false;

    $encrypt_method = "AES-256-CBC";
    //pls set your unique hashing key
    $secret_key = 'kkgmp';
    $secret_iv = 'music190';

    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    //do the encyption given text/string/number
    if( $action == 'encrypt' ) {
        $output1 = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output1);
    }
    else if( $action == 'decrypt' ){
    	//decrypt the given text/string/number
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}

function kkgmusic_page_list() {
    ob_start();
    kkgmusic_listHtml();
}

function kkgmusic_page_add() {
    include( plugin_dir_path(__FILE__) . 'inner/kkgmp_url.php' );
}

function kkgmusic_render_input($inputType, $name, $id, $value = '', $required = FALSE) {
    $html = '';
    $requiredAttr = ( $required ) ? 'required' : '';
    switch ($inputType) {
        case 'text':
            $html = '<input type="text" id="' . $id . '" name="' . $name . '" class="form-control" value="' . sanitize_text_field($value) . '" ' . $requiredAttr . '>';
            break;
        case 'url':
            $html = '<input type="url" id="' . $id . '" name="' . $name . '" class="form-control" value="' . sanitize_url($value) . '" ' . $requiredAttr . '>';
            break;
        case 'textarea':
            $html = '<textarea name="' . $name . '" id="' . $id . '" class="form-control" ' . $requiredAttr . '>' . sanitize_textarea_field($value) . '</textarea>';
            break;
        case 'hidden':
            $html = '<input type = "hidden" name = "' . $name . '" value = "' . sanitize_textarea_field($value) . '">';
            break;
        default:
            $html = '';
            break;
    }

    return $html;
}

function kkgmusic_listHtml() {
    include( plugin_dir_path(__FILE__) . 'inner/kkgmp_list.php' );
    $allowed = array(
            'div' => array(
                    'class' => true,
                    )	
            );
    echo  wp_kses('<div class="wrap">', $allowed);
    $myListTable = new kkgmp_list_Table();
    $myListTable->prepare_items();
    $myListTable->search_box('Search', 'search');
    echo  $myListTable->display();
    echo  wp_kses('</div>', array('div'));

}

function kkgmusic_getsingle($id = 0) {
    if ($id != 0) {
        $sMusic = new kkgmusic_music();
        $sMusic->setMusicId($id);
        return $sMusic->getSingleMusic();
    } else {
        wp_safe_redirect(esc_url_raw(site_url('/wp-admin/admin.php?page=kkg_musics&status=failure')), 307);
        die;
    }
}

function kkgmusic_musicAction($id = 0, $action = '') {
    if ($id != 0 && $action != '') {
        if ($action == 'delete') {
            $sMusic = new kkgmusic_music();
            $sMusic->setMusicId($id);
            $deleted = $sMusic->deleteMusic();
            if ($deleted) {
                wp_safe_redirect(esc_url_raw(site_url('/wp-admin/admin.php?page=kkg_musics&status=delete')), 307);
                die;
            }
        }
    }
    wp_safe_redirect(esc_url_raw(site_url('/wp-admin/admin.php?page=kkg_musics&status=failure')), 307);
    die;
}

function kkgmusic_page_up() {
    ob_start();
    kkgmusic_up();
}
function kkgmusic_up() {
    include( plugin_dir_path(__FILE__) . 'inner/kkgmp_upload.php' );
}

function kkgmusic_view() {
    if (filter_input(INPUT_GET, 'page') == 'view_music' && filter_input(INPUT_GET, 'action') == 'view') {
        $musicContent = kkgmusic_getsingle(filter_input(INPUT_GET, 'element', FILTER_SANITIZE_NUMBER_INT));
        if ($musicContent) {
            wp_enqueue_style('fa-css-file', plugin_dir_url(__FILE__) . 'fontawesome/css/all.css');
            wp_enqueue_script('fa-js-file', plugin_dir_url(__FILE__) . 'fontawesome/js/all.js');
            include( plugin_dir_path(__FILE__) . 'inner/kkgmp_view.php' );
        } else {
            wp_safe_redirect(esc_url_raw(site_url('/wp-admin/admin.php?page=kkg_musics')), 307);
            die;
        }
    }
}

add_action('admin_post_kkg_music_upload', 'kkgmusic_upload');

function kkgmusic_upload() {
    global $wpdb;
    if (!function_exists('wp_handle_upload')) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    check_admin_referer('kkg_music_upload');
    $postNum = sanitize_text_field(filter_input(INPUT_POST, 'postNum'));
    $title = sanitize_text_field(filter_input(INPUT_POST, 'musicTitle'));
    if (wp_verify_nonce(wp_unslash(filter_input(INPUT_POST, '_wpnonce')), 'kkg_music_upload')) {
        if (isset($_FILES) && ( $_FILES['chooseFile']['name'] != '' )) {
            $uploadedfile = $_FILES['chooseFile'];
            $upload_overrides = array(
                'test_form' => false
            );
            $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
            if ($movefile && !isset($movefile['error'])) {
                $url = $movefile['url'];
                if (filter_has_var(INPUT_POST, 'postNum') && $postNum != 0) {
                    $encMusicId = kkgmusic_encryptor('decrypt',$postNum);
                    $wpdb->update(
                            $wpdb->base_prefix . KKG_MUSIC_TABLE,
                            array('sub_musicurl' => $url, 'mtype' => '2', 'music_title' => $title),
                            array('sub_id' => $encMusicId)
                    );
                } else {
                    $wpdb->insert(
                            $wpdb->base_prefix . KKG_MUSIC_TABLE,
                            array('sub_musicurl' => $url, 'mtype' => '2', 'music_title' => $title),
                            array('%s'),
                    );
                }
                wp_safe_redirect(esc_url_raw(site_url('/wp-admin/admin.php?page=kkg_musics&status=successf')), 307);
                die;
            } else {
                /**
                 * Error generated by _wp_handle_upload()
                 * @see _wp_handle_upload() in wp-admin/includes/file.php
                 */
                $GLOBALS['uploadError'] = $movefile['error'];
                wp_safe_redirect(esc_url_raw(site_url('/wp-admin/admin.php?page=kkg_musics&status=failuref')), 307);
                die;
            }
        }
        wp_safe_redirect(esc_url_raw(site_url('/wp-admin/admin.php?page=kkg_musics&status=successf')), 307);
        die;
    } else {
        die(esc_html__('Security check', 'kkg-music'));
    }
}

add_action('admin_post_kkg_music_save', 'kkgmusic_save');

function kkgmusic_save() {
    global $wpdb;
    check_admin_referer('kkg_music_save');
    if (wp_verify_nonce(wp_unslash(filter_input(INPUT_POST, '_wpnonce')), 'kkg_music_save')) {
        $url = sanitize_url(filter_input(INPUT_POST, 'musicUrl', FILTER_SANITIZE_URL), array('http', 'https'));
        $title = sanitize_text_field(filter_input(INPUT_POST, 'musicTitle'));
        $postNum = sanitize_text_field(filter_input(INPUT_POST, 'postNum'));
        if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            if (filter_has_var(INPUT_POST, 'postNum') && $postNum != 0) {
                $encMusicId = kkgmusic_encryptor('decrypt',$postNum);
                $wpdb->update(
                        $wpdb->base_prefix . KKG_MUSIC_TABLE,
                        array('sub_musicurl' => $url, 'mtype' => '1', 'music_title' => $title),
                        array('sub_id' => $encMusicId)
                );
            } else {
                $wpdb->insert(
                        $wpdb->base_prefix . KKG_MUSIC_TABLE,
                        array('sub_musicurl' => $url, 'mtype' => '1', 'music_title' => $title),
                        array('%s'),
                );
            }
            wp_safe_redirect(esc_url_raw(site_url('/wp-admin/admin.php?page=kkg_musics&status=successu')), 307);

            die;
        } else {
            wp_safe_redirect(esc_url_raw(site_url('/wp-admin/admin.php?page=kkg_musics&status=failure')), 307);
            die;
        }
    } else {
        die(esc_html__('Security check', 'kkg-music'));
    }
}


function kkgmusic_getfront() {
    $sMusic = new kkgmusic_music();
    return $sMusic->getAllMusic();
}

// The shortcode function

function kkgmusic_shortcode() {
    wp_enqueue_style('fa-css-file', plugin_dir_url(__FILE__) . 'fontawesome/css/all.css');
    wp_enqueue_style('fa-css1-file', plugin_dir_url(__FILE__) . 'frontend/css/kkgmp_style.css');
    wp_enqueue_script('fa-js-file', plugin_dir_url(__FILE__) . 'fontawesome/js/all.js');
    include( plugin_dir_path(__FILE__) . 'frontend/kkgmp_view.php' );
}

// Register shortcode
add_shortcode('view_kkg_music', 'kkgmusic_shortcode');