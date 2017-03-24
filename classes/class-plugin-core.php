<?php
/**
 * File renaming on upload - Plugin core
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU;

use FROU\Admin_Pages\Sections\Remove_Section;
use FROU\Admin_Pages\Settings_Page;
use FROU\Functions\Functions;

use FROU\Options\Options;
use FROU\WeDevs\Settings_Api;
use FROU\WordPress\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Plugin_Core' ) ) {
	class Plugin_Core extends Plugin {
		/**
		 * @var \WeDevs_Settings_API
		 */
		public $settings_api;

		/**
		 * @var Functions
		 */
		//public $functions;

		/**
		 * @var Options
		 */
		protected $options;

		/**
		 * @return Plugin_Core
		 */
		public static function getInstance() {
			return parent::getInstance();
		}

		protected function __construct() {
			parent::__construct();
			//add_action( 'init', array( $this, 'init' ) );
			//add_action( 'init', array( $this, 'add_options' ) );


			//add_filter( 'plugin_action_links_' . ALG_WC_APVA_BASENAME, array( $this, 'action_links' ) );
			//add_action( 'sanitize_file_name_chars', array( $this, 'sanitize_file_name_chars' ) );
		}

		public function init( $args = array() ) {
			parent::init( $args );
			add_action( 'init', array( $this, 'handle_settings_page' ) );
			add_action( 'init', array( $this, 'add_options' ) );
		}

		/*public function init( $args = array()){

			add_action( 'init', array( $this, 'handle_settings_page' ) );
			add_action( 'init', array( $this, 'add_options' ) );
		}*/

		public function handle_settings_page(){
			if ( ! is_admin() ) {
				return;
			}

			$this->settings_api = new Settings_Api();
			new Settings_Page( $this );
		}

		public function add_options(){
			$this->set_options(new Options());
		}

		/*public function sanitize_file_name_chars( $chars ) {
			$frou         = Plugin_Core::getInstance();
			$remove_chars = filter_var( $frou->settings_api->get_option( Remove_Section::OPTION_CHARACTERS, 'frou_remove_opt' ), FILTER_VALIDATE_BOOLEAN );
			if ( $remove_chars ) {
				$chars_to_remove = $frou->settings_api->get_option( Remove_Section::OPTION_CHARACTERS_TEXT, 'frou_remove_opt' );
				$chars           = explode( " ", $chars_to_remove );
			}

			return $chars;
		}*/



		/**
		 * @return Functions
		 */
		/*public function get_functions( $smart = true ) {
			if ( $smart ) {
				if ( ! $this->functions ) {
					$this->set_functions( new Functions() );
				}
			}

			return $this->functions;
		}*/

		/**
		 * @param Functions $functions
		 */
		/*public function set_functions( $functions ) {
			$this->functions = $functions;
		}*/

		/**
		 * @return Options
		 */
		public function get_options( $smart = true ) {
			if ( $smart ) {
				if ( ! $this->options ) {
					$this->set_options( new Options() );
				}
			}

			return $this->options;
		}

		/**
		 * @param Options $options
		 */
		public function set_options( $options ) {
			$this->options = $options;
		}


	}
}