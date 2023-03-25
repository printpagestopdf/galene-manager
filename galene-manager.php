<?php
/**
 * Plugin Name: Manager for Galène videoconference
 * Plugin URI: https://github.com/printpagestopdf/galene-manager
 * Description: Management system for the Galène videoconferencing server
 * Text Domain: galene-mgr
 * Domain Path: /languages/
 * Version: 0.5.0
 * Author: The Ripper
 * Author URI: https://profiles.wordpress.org/theripper
 * Requires at least: 4.7
 * Tested up to: 6.1
 * Requires PHP: 7.4
 * License: GPLv3 or later
 *
 * @package galene
 *
 * Copyright (C) 2023 The Ripper
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *  
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once 'includes/class-gal-util.php';

// Define the plugin version.
define( 'GALENE_PLUGIN_VERSION', 0.5 );
define( 'GALENE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'GALENE_PLUGIN_URL', plugins_url('', __FILE__ ) );
define( 'GALENE_DB_DRIVER', GALENE_PLUGIN_PATH. 'includes/class-gal-db-driver.php');
define( 'GALENE_DEF_BFK','WGVTmmsVy,ylh3jqnQqhu*9W/bHq#/TP');
define( 'GALENE_WP_ROLE_TYPE',1);
define( 'GALENE_WP_ROLE_NAME','galene_mgr');
define( 'GALENE_WP_ROLE_DISPLAYNAME','GaleneManager');
define( 'GALENE_ROOM_AUTH_KID','gmgr2023');


register_activation_hook( __FILE__, function() {
	global $wpdb;
	
	if(!file_exists(__DIR__ . '/data/bfk.php'))
		file_put_contents(__DIR__ . '/data/bfk.php','<?php return base64_decode("' . base64_encode(random_bytes(32)) . '");' );
	
	
	$charset_collate = $wpdb->get_charset_collate();
	$user_table_name = $wpdb->prefix . 'galene_user';
	$room_table_name = $wpdb->prefix . 'galene_room';
	$access_table_name = $wpdb->prefix . 'galene_access';
	$settings_table_name = $wpdb->prefix . 'galene_settings';
	$res=array();


	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	
	$sql = "CREATE TABLE $room_table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  displayName varchar(255) DEFAULT NULL,
	  description varchar(255) DEFAULT NULL,
	  galene_group varchar(255) NOT NULL,
	  key64 varchar(255) NOT NULL,
	  access varchar(25) NOT NULL,
	  attrs longtext DEFAULT NULL,
	  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  UNIQUE KEY id (id)
	) $charset_collate;";

	$res += dbDelta( $sql );
	
	$sql = "CREATE TABLE $user_table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  displayName varchar(255) DEFAULT NULL,
	  password varchar(255) NOT NULL,
	  login varchar(255) NOT NULL UNIQUE,
	  type smallint(4) DEFAULT 0,
	  isAdmin int(1) DEFAULT 0,
	  UNIQUE KEY id (id)
	) $charset_collate;";

	$res += dbDelta( $sql );
	
	
	$sql = "CREATE TABLE $access_table_name (
		room_id mediumint(9) NOT NULL,
		user_id mediumint(9) NOT NULL,
		is_operator int(1) DEFAULT 0,
		is_presenter int(1) DEFAULT 0,
		is_other int(1) DEFAULT 0
	  ) $charset_collate;";
  
	  $res += dbDelta( $sql );
	  	  
	$sql = "CREATE TABLE $settings_table_name (
	  setting_key varchar(40) NOT NULL,
	  setting_subkey varchar(40) DEFAULT NULL,
	  type varchar(20) DEFAULT NULL,
	  data longtext DEFAULT NULL,
	  PRIMARY KEY  setting_key (setting_key)
	) $charset_collate;";

	$res += dbDelta( $sql );
	
	// error_log(print_r($res,true));
	
	//Add WP galene admin Role
	add_role(GALENE_WP_ROLE_NAME,GALENE_WP_ROLE_DISPLAYNAME);

	require_once GALENE_DB_DRIVER;

	/* install app admin if no users exists */
	if(count(Gal_DB_Driver::inst()->get_users()) == 0) {
		Gal_DB_Driver::inst()->insert_user(array(
			'displayName' => 'Galene Administrator',
			'login' => 'galene_admin',
			'password' => password_hash('galene', PASSWORD_DEFAULT),
			'isAdmin' => 1,
			'galene_user' => -1,
		));
	}

	/* add Wordpress roles to user Table */
	Gal_DB_Driver::inst()->sync_wp_roles();
 
} );

add_action('init', function() {
	// switch_to_locale('en_US');
	load_plugin_textdomain( 'galene-mgr', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	
	if(strpos(@$_REQUEST['galene_action']?:'', 'admin_') === 0) {
		session_name("PHPSESSID_GALENE");
		if (session_status() === PHP_SESSION_NONE ) {			
			session_start();
		}
 		
		if(!empty(@$_SESSION['galene_user']) && @$_SESSION['galene_is_logged_in'] === true && @$_REQUEST['galene_action'] != 'admin_action_logout')
			session_write_close();		
	}
	
    wp_register_script( 'galene_js', plugins_url('/js/galene.js', __FILE__), ["jquery"] );
    wp_register_style( 'galene_fw_style', plugins_url('/css/bulma.min.css', __FILE__));
    wp_register_style( 'galene_fw_list_style', plugins_url('/css/bulma-list.min.css', __FILE__),['galene_fw_style']);
    // wp_register_style( 'galene_fw_cc_style', plugins_url('/css/bulma-radio-checkbox.min.css', __FILE__),['galene_fw_style']);
    wp_register_style( 'galene_fw_cc_style', plugins_url('/css/bulma-checkradio.min.css', __FILE__),['galene_fw_style']);
    wp_register_style( 'galene_style', plugins_url('/css/galene.css', __FILE__), ["galene_fw_style"]);


	add_action('update_option_' . wp_roles()->role_key,function($old_value,  $value,  $option){
		require_once GALENE_DB_DRIVER;
		Gal_DB_Driver::inst()->sync_wp_roles($value);	
	},10,3);
});

require_once 'includes/class-gal-shortcode.php';
require_once 'includes/class-gal-template-registrar.php';

