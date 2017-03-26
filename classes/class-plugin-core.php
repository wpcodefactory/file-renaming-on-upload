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

use FROU\Options\General\Enable_Option;
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

		public $current_filename_modified;
		public $current_filename_original;

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

		public function init( $args = array() ) {
			parent::init( $args );
			add_action( 'init', array( $this, 'handle_settings_page' ) );
			add_action( 'init', array( $this, 'add_options' ) );
			add_filter( 'sanitize_file_name', array( $this, 'sanitize_filename' ) );
			//add_action( 'add_attachment', array( $this, 'add_attachment' ) );
			//add_filter('wp_insert_attachment_data',array($this,'insert_attachment_data'),10,2);
			//add_action('wp_insert_post',array($this,'insert_post'));
		}


		public function sanitize_filename( $filename ) {
			$option = new Enable_Option( array( 'section' => 'frou_general_opt' ) );
			if ( ! filter_var( $option->get_option( $option->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename;
			}

			$info              = pathinfo( $filename );
			$extension         = empty( $info['extension'] ) ? '' : $info['extension'];
			$filename_original = $info['filename'];


			$filename_arr = apply_filters( 'frou_sanitize_file_name',
				array(
					'filename_original' => $filename_original,
					'extension'         => $extension,
					'structure'         => array(
						'rules'       => '',
						'translation' => array( 'filename' => $filename_original ),
					),
				)
			);

			$filename = $filename_arr['structure']['rules'];
			foreach ( $filename_arr['structure']['translation'] as $key => $translation ) {
				$filename = str_replace( "{" . $key . "}", $translation, $filename );
			}
			$filename_only = preg_replace( '/\{.*\}/U', "", $filename );
			$filename      = $filename_only . '.' . $extension;


			return $filename;
		}

		public function handle_settings_page() {
			if ( ! is_admin() ) {
				return;
			}

			$this->settings_api = new Settings_Api();
			new Settings_Page( $this );
		}

		public function add_options() {
			$this->set_options( new Options() );
		}

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