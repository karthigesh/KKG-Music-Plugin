<?php
/*
Plugin Name: KKG Music
Description: It used to create a music link!
Version: 1.0
Requires at least:6.2.2
Requires PHP: 8.0
Author: Karthigesh
License: GPLv2
GITHUB URL:https://github.com/karthigesh/KKG-Music-Plugin/tree/main
*/
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
define( 'KKG_MP_WP_ADMIN_VERSION', '1.0.0' );
define( 'KKG_MP_WP_ADMIN_DIR', 'kkg_music' );
require_once plugin_dir_path( __FILE__ ) . 'includes/kkgmp_functions.php';
define( 'KKG_MUSIC_TABLE', 'kkg_music_submissions' );

/*
* Register activation hook
*/
global $kkgmusic_db_version;
$kkgmusic_db_version = '1.0';

function kkgmusic_install() {
    global $wpdb;
    global $kkgmusic_db_version;
    $table_name = $wpdb->prefix . KKG_MUSIC_TABLE;
    $charset_collate = $wpdb->get_charset_collate();
    if ( $wpdb->get_var( "show tables like '$table_name'" ) != $table_name ) {
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
}
register_activation_hook( __FILE__, 'kkgmusic_install' );