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
require_once plugin_dir_path(__FILE__) . 'includes/kkgmp_functions.php';

define('KKG_MP_WP_ADMIN_VERSION', '1.0.0');
define('KKG_MP_WP_ADMIN_DIR', 'kkg_music');
define('KKG_MUSIC_TABLE', 'kkg_music_submissions');
define("KKGMP_LIST_URL", sanitize_url(site_url('/wp-admin/admin.php?page=kkg_musics'), array('http', 'https')));
define("KKGMP_SUCCF_URL", sanitize_url(site_url('/wp-admin/admin.php?page=kkg_musics&status=successf'), array('http', 'https')));
define("KKGMP_SUCCU_URL", sanitize_url(site_url('/wp-admin/admin.php?page=kkg_musics&status=successu'), array('http', 'https')));
define("KKGMP_FAIL_URL", sanitize_url(site_url('/wp-admin/admin.php?page=kkg_musics&status=failure'), array('http', 'https')));
define("KKGMP_FAILF_URL", sanitize_url(site_url('/wp-admin/admin.php?page=kkg_musics&status=failuref'), array('http', 'https')));
define("KKGMP_DEL_URL", sanitize_url(site_url('/wp-admin/admin.php?page=kkg_musics&status=delete'), array('http', 'https')));
define("KKGMP_ADD_URL", sanitize_url(site_url('/wp-admin/admin.php?page=add_music'), array('http', 'https')));
define("KKGMP_UP_URL", sanitize_url(site_url('/wp-admin/admin.php?page=up_music'), array('http', 'https')));
/*
 * Register activation hook
 */
global $kkgmusic_db_version;
$kkgmusic_db_version = '1.0';

function kkgmusic_install() {

    global $wpdb;
    $kkgmdb = apply_filters('kkgmusic_database', $wpdb);
    $table_name = $kkgmdb->prefix . KKG_MUSIC_TABLE;
    $charset_collate = $kkgmdb->get_charset_collate();
    $sql = "CREATE OR REPLACE TABLE $table_name (
		sub_id bigint(9) NOT NULL AUTO_INCREMENT,
        music_title text NOT NULL,
        sub_musicurl longtext NOT NULL,
        mtype ENUM('1','2') NOT NULL,
		created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
		PRIMARY KEY  (sub_id)
	) $charset_collate;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    add_option( 'kkgmusic_install_date', date('Y-m-d G:i:s'), '', 'yes');
}
register_activation_hook(__FILE__, 'kkgmusic_install');