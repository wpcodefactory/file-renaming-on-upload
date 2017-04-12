<?php
/**
 * File renaming on upload - Singleton Design Pattern
 *
 * @version 2.0.0
 * @since   2.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Design_Patterns;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Design_Patterns\Singleton' ) ) {

	class Singleton {

		protected static $instance = null;

		protected function __construct() {
			//Thou shalt not construct that which is unconstructable!
		}

		protected function __clone() {
			//Me not like clones! Me smash clones!
		}

		/**
		 * @return Current_Class_Name
		 */
		public static function getInstance() {
			if ( ! isset( static::$instance ) ) {
				static::$instance = new static;
			}

			return static::$instance;
		}

	}
}




