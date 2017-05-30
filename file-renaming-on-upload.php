<?php
/*
Plugin Name: File Renaming on upload
Plugin URI: https://wordpress.org/plugins/file-renaming-on-upload/
Description: Fixes file uploads with accents and special characters by renaming them. It also improves your SEO.
Version: 2.1.4
Author: Pablo S G Pacheco
Author URI: https://github.com/pablo-sg-pacheco
License: GPL2
Text Domain: file-renaming-on-upload
Domain Path: /languages
*/

namespace FROU;

use Pablo_Pacheco\WP_Namespace_Autoloader\WP_Namespace_Autoloader;

if ( ! function_exists( 'file_renaming_on_upload_autoload' ) ) {

	/**
	 * Setups autoloader
	 *
	 * @version 2.1.2
	 * @since   2.1.2
	 * @return  Plugin_Core
	 */
	function file_renaming_on_upload_autoload() {		
		$autoloader = new WP_Namespace_Autoloader( array(
			'directory'        => __DIR__,
			'namespace_prefix' => __NAMESPACE__,
			'classes_dir'      => 'classes',
		) );
		$autoloader->init();
	}
}

if ( ! function_exists( 'file_renaming_on_upload' ) ) {

	/**
	 * Returns the main instance of Plugin_Core
	 *
	 * @version 2.1.2
	 * @since   2.1.2
	 * @return  Plugin_Core
	 */
	function file_renaming_on_upload() {
		$frou = Plugin_Core::get_instance();
		$frou->set_args( array(
			'plugin_file_path' => __FILE__,
			'action_links'     => array(
				array(
					'url'  => admin_url( 'options-general.php?page=file-renaming-on-upload' ),
					'text' => __( 'Settings', 'file-renaming-on-upload' ),
				),
			),
			'translation'      => array(
				'text_domain' => 'file-renaming-on-upload',
			),
		) );

		return $frou;
	}
}

add_action( 'plugins_loaded', function(){
	require __DIR__ . '/vendor/autoload.php';
	file_renaming_on_upload_autoload();
	$frou = file_renaming_on_upload();
	$frou->init();
} );