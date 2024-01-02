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

if (!defined('WPINC')) {
    die;
}

define('KKG_MP_WP_ADMIN_VERSION', '1.0.0');
define('KKG_MP_WP_ADMIN_DIR', 'kkg_music');
define('KKG_MUSIC_TABLE', 'kkg_music_submissions');
require_once plugin_dir_path(__FILE__) . 'includes/kkgmp_functions.php';
/*
 * Register activation hook
 */
global $kkgmusic_db_version;
$kkgmusic_db_version = '1.0';

function kkgmusic_create_table(){
    global $wpdb;
    $kkgmdb   = apply_filters( 'kkgmusic_database', $wpdb );
    add_option( 'kkgmusic_view_install_date', date('Y-m-d G:i:s'), '', 'yes');
}

function kkgmusic_on_activate( $network_wide ){

    global $wpdb;
    if ( is_multisite() && $network_wide ) {
        // Get all blogs in the network and activate plugin on each one
        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
        foreach ( $blog_ids as $blog_id ) {
            switch_to_blog( $blog_id );
            kkgmusic_create_table();
            restore_current_blog();
        }
    } else {
        kkgmusic_create_table();
    }

	// Add custom capability
	$role = get_role( 'administrator' );
	$role->add_cap( 'kkgmp_access' );
}

register_activation_hook( __FILE__, 'kkgmusic_on_activate' );


function kkgmusic_upgrade_function( $upgrader_object, $options ) {

    $upload_dir    = wp_upload_dir();
    $kkgmdb7_dirname = $upload_dir['basedir'].'/kkgmusic_uploads';

    if ( file_exists( $kkgmdb7_dirname.'/index.php' ) ) return;
        
    if ( file_exists( $kkgmdb7_dirname ) ) {
        $fp = fopen( $kkgmdb7_dirname.'/index.php', 'w');
        fwrite($fp, "<?php \n\t // Silence is golden.");
        fclose( $fp );
    }

}

add_action( 'upgrader_process_complete', 'kkgmusic_upgrade_function',10, 2);

function kkgmusic_update_db() {
    global $kkgmusic_db_version;
    if (get_site_option('kkgmusic_db_version') != $kkgmusic_db_version) {
        kkgmusic_create_table();
    }
}
add_action('plugins_loaded', 'kkgmusic_update_db');


function kkgmusic_on_deactivate() {

	// Remove custom capability from all roles
	global $wp_roles;

	foreach( array_keys( $wp_roles->roles ) as $role ) {
		$wp_roles->remove_cap( $role, 'kkgmp_access' );
	}
}
register_deactivation_hook( __FILE__, 'kkgmusic_on_deactivate' );