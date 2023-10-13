<?php
/*
Plugin Name: KKG Music
Description: It used to create a music link!
Version: 1.0 
Requires at least:6.2.2
Requires PHP: 8.0
Author: Karthigesh
License: GPLv2
*/
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
define( 'KKG_MP_WP_ADMIN_VERSION', '1.0.0' );
define( 'KKG_MP_WP_ADMIN_DIR', 'kkg_music' );
require_once plugin_dir_path(__FILE__) . 'includes/kkgmp_functions.php';
