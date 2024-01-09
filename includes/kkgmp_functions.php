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
// Hook the 'init' action hook, run the function named 'kkgmusic_postType()'

add_action( 'init', 'kkgmusic_postType' );

function kkgmusic_postType() {
    $supports = array(
        'title', // post title
        'thumbnail', // featured images
        'post-formats', // post formats
        'tags'
    );
    $labels = array(
        'name' => _x( 'KKG music', 'plural' ),
        'singular_name' => _x( 'KKG music', 'singular' ),
        'menu_name' => _x( 'KKG music', 'admin menu' ),
        'name_admin_bar' => _x( 'KKG music', 'admin bar' ),
        'add_new' => _x( 'Add New KKG music', 'add new' ),
        'add_new_item' => __( 'Add New KKG music' ),
        'new_item' => __( 'New KKG music' ),
        'edit_item' => __( 'Edit KKG music' ),
        'view_item' => __( 'View KKG music' ),
        'all_items' => __( 'All KKG musics' ),
        'search_items' => __( 'Search KKG music' ),
        'not_found' => __( 'No KKG music found.' ),
    );
    $args = array(
        'supports' => $supports,
        'labels' => $labels,
        'public' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'kkgmusics' ),
        'has_archive' => true,
        'hierarchical' => false,
    );
    register_post_type( 'kkg_musics', $args );
}

function kkgmusic_meta_box_callback( $post ) {
    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'kkgmusic_nonce', 'kkgmusic_nonce' );
    $musicmode = get_post_meta( $post->ID, '_kkgmusic_mode', true );
    $choosen_url = $choosen_up = '';
    $choosen_url_display = $choosen_up_display = 'style="display: none;"';
    if ( $musicmode == 'url' ) {
        $choosen_url = 'checked';
        $choosen_url_display = '';
    } else if ( $musicmode == 'upload' ) {
        $choosen_up = 'checked';
        $choosen_up_display = '';
    }
    $musicurl = get_post_meta( $post->ID, '_kkgmusic', true );
    $musicfilename = get_post_meta( $post->ID, '_kkgmusic_filename', true );
    include( plugin_dir_path( __FILE__ ) . 'inner/kkgmp_metabox.php' );
}

function kkgmusic_meta_box_shortcode( $post ) {
    // Add a nonce field so we can check for it later.
    $str = '<div class="mt-3 mb-3">Use the shortcode <b>[viewkkgmusic id='.$post->ID.']</b> to display the music in your pages or posts</div>';
    $allow = array( 'b' => array(),'div' => array('class'=>array()));
    echo wp_kses( $str, $allow );
}

function kkgmusic_meta_box() {

    $screens = array( 'kkg_musics' );

    foreach ( $screens as $screen ) {
        add_meta_box(
            'KKG-Music',
            __( 'Music Field', 'kkg-music' ),
            'kkgmusic_meta_box_callback',
            $screen,
            'advanced',
            'high'
        );
        add_meta_box(
            'KKG-Music1',
            __( 'Music ShortCode', 'kkg-music' ),
            'kkgmusic_meta_box_shortcode',
            $screen,
            'advanced',
            'high'
        );
    }
}

add_action( 'add_meta_boxes', 'kkgmusic_meta_box' );

/**
* When the post is saved, saves our custom data.
*
* @param int $post_id
*/

function kkgmusic_save_meta_box_data( $post_id ) {

    // Check if our nonce is set.
    if ( ! isset( $_POST[ 'kkgmusic_nonce' ] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST[ 'kkgmusic_nonce' ], 'kkgmusic_nonce' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST[ 'post_type' ] ) && 'kkg_musics' == $_POST[ 'post_type' ] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    } else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */

    // Make sure that it is set.
    if ( ! isset( $_POST['kkgmusic_nonce'] ) ) {
        return;
    }
    $selectData = sanitize_text_field( $_POST['kkgmusic_chooseType'] );
    $updateUrl = '';
    update_post_meta( $post_id, '_kkgmusic_mode', $selectData );
    if($selectData == 'url'){  
        // Sanitize user input.      
        $updateUrl = sanitize_text_field( $_POST['kkgmusic_url'] );
    }else{
        $updateUrl = sanitize_text_field( $_POST['kkgmusic_file'] );
        $updateUrlName = sanitize_text_field( $_POST['kkgmusic_filename'] );
        update_post_meta( $post_id, '_kkgmusic_filename', $updateUrlName );
    } 
    // Update the meta field in the database.
    update_post_meta( $post_id, '_kkgmusic', $updateUrl );
}

add_action( 'save_post', 'kkgmusic_save_meta_box_data' );

add_action( 'admin_enqueue_scripts', 'kkgmusic_scripts' );

function kkgmusic_scripts() {
    wp_enqueue_style( 'kkgmp-fa', plugin_dir_url( __FILE__ ) . 'fontawesome/css/all.css' );
    wp_enqueue_script( 'kkgmp-fa', plugin_dir_url( __FILE__ ) . 'fontawesome/js/all.js' );
    wp_enqueue_style( 'kkgmp', plugin_dir_url( __FILE__ ) . 'css/css-file.css' );
    wp_enqueue_style( 'kkgmp-bs', plugin_dir_url( __FILE__ ) . 'bootstrap/css/bootstrap.min.css' );
    wp_enqueue_script( 'kkgmp-bs', plugin_dir_url( __FILE__ ) . 'bootstrap/js/bootstrap.min.js' );
    wp_enqueue_script( 'kkgmp', plugin_dir_url( __FILE__ ) . 'js/kkgmp_scripts.js' );
}


function kkgmusic_getfront($element) {
    $sMusic = new kkgmusic_music();
    if($element != 0){
        $sMusic->setMusicId( $element );
        return $sMusic->getSingleMusic();
    }else{        
        return $sMusic->getAllMusic();
    }
}

// The shortcode function

function kkgmusic_shortcode($atts) {
    extract(shortcode_atts(array(
        'id' => 0,
     ), $atts));
     return kkgmusic_displayMusic($id);
}
// Register shortcode
add_shortcode( 'viewkkgmusic', 'kkgmusic_shortcode' );

function kkgmusic_displayMusic($id){
    wp_enqueue_style( 'fa-css-file', plugin_dir_url( __FILE__ ) . 'fontawesome/css/all.css' );
    wp_enqueue_style( 'fa-css1-file', plugin_dir_url( __FILE__ ) . 'frontend/css/kkgmp_style.css' );
    wp_enqueue_script( 'fa-js-file', plugin_dir_url( __FILE__ ) . 'fontawesome/js/all.js' );
    wp_enqueue_script( 'fa-js1-file', plugin_dir_url( __FILE__ ) . 'js/kkgmp_music.js', array(), false, true);

    $musicSect = kkgmusic_getfront($id);
    $html = "<div style = 'width: 50px;
    height: 50px;
    '></div>
    <div class = 'audio-player' data-id = '$musicSect'>
    <audio controls style = 'display:none'></audio>
    <div class = 'timeline'>
    <div class = 'progress'></div>
    </div>
    <div class = 'controls'>
    <div class = 'play-container'>
    <div class = 'toggle-play play'>
    </div>
    </div>
    <div class = 'time'>
    <div class = 'current'>0:00</div>
    <div class = 'divider'>/</div>
    <div class = 'length'></div>
    </div>
    <div class = 'name'>Music Song</div>
    <div class = 'volume-container'>
    <div class = 'volume-button'>
    <div class = 'volume icono-volumeMedium'></div>
    </div>

    <div class = 'volume-slider'>
    <div class = 'volume-percentage'></div>
    </div>
    </div>
    </div>
    </div>";
  return $html;
}

add_action( 'the_post', 'kkgmusic_wpautop' );
function kkgmusic_wpautop( $post ) {
    if( 'kkg_musics' == $post->post_type ) {
        if( is_main_query() ) {
            add_filter( 'the_content', 'kkgmusic_wpautopcontent' );
        }
    }
    
  }
function kkgmusic_wpautopcontent($content) {
    global $post;
    if( is_singular() ) {
        if( 'kkg_musics' == $post->post_type ) {
            $postId = get_the_ID();
            remove_filter( 'the_content', 'kkgmusic_wpautopcontent' );
            $wpse_261935_meta = kkgmusic_displayMusic($postId);
            return  $content.$wpse_261935_meta;
        }
    }
}
add_action( 'wp_content', 'kkgmusic_wpautop' );

