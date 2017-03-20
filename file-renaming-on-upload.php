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

use FROU\Admin_Pages\Settings_Page;
use Pablo_Pacheco\WP_Namespace_Autoloader\WP_Namespace_Autoloader;

// Configures Autoloader
$autoloader = new WP_Namespace_Autoloader( array(
	'directory'        => __DIR__,
	'namespace_prefix' => __NAMESPACE__,
	'classes_dir'      => 'classes',
) );
$autoloader->init();


//new General_Settings_Page();

//\Frou\Plugin_Core::getInstance();

Plugin_Core::getInstance();






//require __DIR__ . '/vendor/pablo-pacheco/wp-namespace-autoloader/src/class-wp-namespace-autoloader.php';





// Load composer dependencies


/*new WP_Namespace_Autoloader( array(
	'directory'   => __DIR__,
	'namespace'   => __NAMESPACE__,
	'classes_dir' => 'classes',
	'namespace_to_lowercase' => true

) );*/




