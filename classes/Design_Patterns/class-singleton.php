<?php
/**
 * File renaming on upload - Singleton Design Pattern.
 *
 * @version 2.0.0
 * @since   2.0.0
 * @author  WPFactory
 */

namespace FROU\Design_Patterns;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Design_Patterns\Singleton' ) ) {

	class Singleton {

		/**
		 * instance
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @var null
		 */
		protected static $instance = null;

		/**
		 * * Singleton constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 */
		protected function __construct() {
			//Thou shalt not construct that which is unconstructable!
		}

		/**
		 * clone.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		protected function __clone() {
			//Me not like clones! Me smash clones!
		}

		/**
		 * getInstance.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
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




