<?php
/*
Plugin Name: File Renaming on upload
Plugin URI: http://wordpress.org/extend/plugins/file-renaming-on-upload/
Description: Fixes file uploads with accents and special characters by renaming them. It also improves your SEO.
Version: 2.0.3
Author: Pablo S G Pacheco
Author URI: https://github.com/pablo-pacheco
License: GPL2
Text Domain: file-renaming-on-upload
Domain Path: /languages
*/

namespace FROU;

require __DIR__ . '/vendor/autoload.php';

use Pablo_Pacheco\WP_Namespace_Autoloader\WP_Namespace_Autoloader;

// Setups Autoloader
$autoloader = new WP_Namespace_Autoloader( array(
	'directory'        => __DIR__,
	'namespace_prefix' => __NAMESPACE__,
	'classes_dir'      => 'classes',
) );
$autoloader->init();

// Setups the plugin
$plugin = Plugin_Core::getInstance();
$plugin->init( array(
	'plugin_file_path' => __FILE__,
	'translation'      => array(
		'slug' => 'file-renaming-on-upload',
	),
	'action_links'     => array(
		array(
			'url'  => admin_url( 'options-general.php?page=file-renaming-on-upload' ),
			'text' => __( 'Settings', 'file-renaming-on-upload' ),
		),
	),
) );