<?php

/*
Plugin Name: File Renaming on upload
Plugin URI: http://wordpress.org/extend/plugins/file-renaming-on-upload/
Description: Renames files on upload
Version: 2.0.0
Author: Pablo S G Pacheco
Author URI: https://github.com/pablo-pacheco
License: GPL2
*/

namespace FROU;

require __DIR__ . '/vendor/autoload.php';

use Pablo_Pacheco\WP_Namespace_Autoloader\WP_Namespace_Autoloader;

// Configures Autoloader
$autoloader = new WP_Namespace_Autoloader( array(
	'directory'        => __DIR__,
	'namespace_prefix' => __NAMESPACE__,
	'classes_dir'      => 'classes',
) );
$autoloader->init();

$plugin = Plugin_Core::getInstance();
$plugin->init( array(
	'plugin_file_path' => __FILE__,
	'translation'      => array(
		'slug' => 'file-renaming-on-upload',
	),
	'action_links'     => array(
		array(
			'url'  => admin_url( 'options-general.php?page=frou' ),
			'text' => __( 'Settings', 'file-renaming-on-upload' ),
		),
	),
) );