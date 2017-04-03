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

		/**
		 * Initialize
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param array $args
		 */
		public function init( $args = array() ) {
			parent::init( $args );
			add_action( 'init', array( $this, 'handle_settings_page' ) );
			add_action( 'init', array( $this, 'add_options' ) );
			add_filter( 'sanitize_file_name', array( $this, 'sanitize_filename' ) );

			//add_action( 'add_attachment', array( $this, 'add_attachment' ) );
			//add_filter('wp_insert_attachment_data',array($this,'insert_attachment_data'),10,2);
			//add_action('wp_insert_post',array($this,'insert_post'));
		}

		protected function remove_unused_rules( $filename, $args ) {

			preg_match_all( '/\{.*\}/U', $filename, $filename_rules_arr );
			if ( is_array( $filename_rules_arr ) && count( $filename_rules_arr ) > 0 ) {
				foreach ( $filename_rules_arr[0] as $rule ) {
					$rule_without_bracket = substr( $rule, 1, - 1 );
					if ( ! array_key_exists( $rule_without_bracket, $args['structure']['translation'] ) ) {
						$filename = str_replace( $rule, '', $filename );
					}
				}
			}

			return $filename;
		}

		protected function translate_rules( $filename, $args ) {
			foreach ( $args['structure']['translation'] as $key => $translation ) {
				$filename = str_replace( "{" . $key . "}", $translation, $filename );
			}

			return $filename;
		}

		protected function add_separator( $filename, $args ) {
			$separator = $args['structure']['separator'];

			return preg_replace( '/\}\{/U', "}{$separator}{", $filename );
		}

		/**
		 * Sanitizes filename.
		 *
		 * It's the main function of this plugin
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $filename
		 *
		 * @return mixed|string
		 */
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
						'separator'   => '-',
						'translation' => array( 'filename' => $filename_original ),
					),
				)
			);

			$filename = $filename_arr['structure']['rules'];
			$filename = $this->remove_unused_rules( $filename, $filename_arr );
			$filename = $this->add_separator( $filename, $filename_arr );
			$filename = $this->translate_rules( $filename, $filename_arr );
			$filename = $filename . '.' . $extension;

			return $filename;
		}

		/**
		 * Manages the settings page
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function handle_settings_page() {
			if ( ! is_admin() ) {
				return;
			}

			$this->settings_api = new Settings_Api();
			new Settings_Page( $this );
		}

		/**
		 * Creates the options inside settings pages
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
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