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

use FROU\Admin\General_Settings_Page;


/**
 * Autoloads all classes
 *
 * @version 2.0.0
 * @since   2.0.0
 *
 * @param   type $class
 */
spl_autoload_register( function ( $class ) {
	if ( false !== strpos( $class, __NAMESPACE__ ) ) {

		if ( ! class_exists( $class ) ) {

			$main_dir   = 'classes';
			$dir        = untrailingslashit( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . $main_dir . DIRECTORY_SEPARATOR;
			$class      = str_replace( __NAMESPACE__ . DIRECTORY_SEPARATOR, '', $class );
			$class_file = strtolower( str_replace( array( '_', "\0" ), array( '-', '' ), $class ) . '.php' );

			$class_file_arr = explode( DIRECTORY_SEPARATOR, $class_file );
			$the_file       = 'class-' . array_pop( $class_file_arr );
			error_log( print_r( $class_file_arr, true ) );

			$file       = $dir . $class_file;




			//error_log(print_r($dir,true));
			/*$classes_dir = $dir . 'classes' . DIRECTORY_SEPARATOR;
			$class_file  = str_replace( '\\', DIRECTORY_SEPARATOR, $class ) . '.php';
			$class_file  = str_replace( __NAMESPACE__ . DIRECTORY_SEPARATOR, '', $class_file );
			$file        = $classes_dir . $class_file;
			error_log( print_r( $class, true ) );
			if ( file_exists( $file ) ) {
				require_once $file;
			} else {
				error_log( 'ERROR LOADING FILE: ' . print_r( $file, true ) );
			}*/
		}
	}
} );

// Load composer dependencies
require __DIR__ . '/vendor/autoload.php';

new General_Settings_Page();
