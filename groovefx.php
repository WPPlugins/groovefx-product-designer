<?php
/**
 * @package GROOVEFX
 * @version 1.0.1
 */
/*
Plugin Name: GrooveFX
Plugin URI: http://www.groovefx.fr/
Description: The first real web to print product designer plugin
Author: netfxs
Version: 1.0.1
Author URI: http://www.groovefx.fr/
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $wpdb;

//define constants
define( 'GROOVEFX_PATH', plugin_dir_path( __FILE__ ) );
define( 'GROOVEFX_INCLUDES', GROOVEFX_PATH . 'includes/' );
define( 'GROOVEFX_URL', plugins_url( '/', __FILE__ ) );
define( 'GROOVEFX_FILES_URL', GROOVEFX_URL . 'file/' );

//includes
register_activation_hook( __FILE__, 'groovefx_install' );

 
//add action to load my plugin files
add_action('plugins_loaded', 'groovefx_load_translation_files');



if( is_admin() ) {
    //---- ADMIN ----//   
    require_once( GROOVEFX_INCLUDES . 'class-wc-groovefx-back.php' );	
} else {
    //---- FRONT END ----//
    require_once( GROOVEFX_INCLUDES . 'class-wc-groovefx-front.php' );
}

/*
  * this function loads my plugin translation files
  */
function groovefx_load_translation_files() {
	load_plugin_textdomain('woocommerce-groovefx', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
}


function groovefx_install() {

	global $wpdb;
 
	//variable par efaut 
	delete_option( 'api_groovefx');
	delete_option( 'bucket_groovefx');
	
	add_option( 'api_groovefx', '' );
	add_option( 'bucket_groovefx', 'groovefx' );
	
	
	$table_name = $wpdb->prefix . 'groovefx_list_product';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id_list mediumint(9) NOT NULL AUTO_INCREMENT,		
		id_groo tinytext NOT NULL,		
		descr VARCHAR(255) DEFAULT '' NOT NULL,			
		UNIQUE KEY id_list (id_list)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	
	$table_name = $wpdb->prefix . 'groovefx_list_taille';
	
	$sql = "CREATE TABLE $table_name (
		id_taill mediumint(9) NOT NULL AUTO_INCREMENT,
		id_groo tinytext NOT NULL,	
		UNIQUE KEY id_taill (id_taill)
	) $charset_collate;";

	
	dbDelta( $sql );
	

}



?>