<?php
/**
 * File renaming on upload - Plugin core
 *
 * @version 2.1.1
 * @since   2.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU;

use FROU\Admin_Pages\Sections\Remove_Section;
use FROU\Admin_Pages\Settings_Page;
use FROU\Functions\Functions;

use FROU\Options\General\Enable_Option;
use FROU\Options\Advanced\Ignore_Extensions_Option;
use FROU\Options\Advanced\Ignore_Filenames_Option;
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
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param array $args
		 */
		public function init( $args = array() ) {
			parent::init( $args );
			add_action( 'init', array( $this, 'handle_settings_page' ) );
			add_action( 'init', array( $this, 'add_options' ) );
			add_filter( 'sanitize_file_name', array( $this, 'sanitize_filename' ), 10, 2 );

			//add_action( 'add_attachment', array( $this, 'add_attachment' ) );
			//add_filter('wp_insert_attachment_data',array($this,'insert_attachment_data'),10,2);
			//add_action('wp_insert_post',array($this,'insert_post'));
		}

		/**
		 * Removes unused rules
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param array $args
		 */
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

		/**
		 * Translates rules
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param array $args
		 */
		protected function translate_rules( $filename, $args ) {
			foreach ( $args['structure']['translation'] as $key => $translation ) {
				$filename = str_replace( "{" . $key . "}", $translation, $filename );
			}

			return $filename;
		}

		/**
		 * Adds separator
		 *
		 * @param $filename
		 * @param $args
		 *
		 * @return mixed
		 */
		protected function add_separator( $filename, $args ) {
			$separator = $args['structure']['separator'];
			return preg_replace( '/\}\{/U', "}{$separator}{", $filename );
		}

		/**
		 * Checks if extension is allowed for renaming
		 *
		 * @version 2.1.1
		 * @since   2.1.1
		 *
		 * @param $extension
		 *
		 * @return bool
		 */
		protected function is_extension_allowed( $extension ) {
			$option = new Ignore_Extensions_Option( array( 'section' => 'frou_advanced_opt' ) );
			if ( filter_var( $option->get_option( $option->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				$ignored_extensions_str = $option->get_option( $option->option_extensions_ignored );
				$ignored_extensions_arr = explode( ",", $ignored_extensions_str );
				$ignored_extensions_arr = array_map( 'trim', $ignored_extensions_arr );
				$ignored_extensions_arr = array_map( 'sanitize_text_field', $ignored_extensions_arr );
				if ( ! empty( $ignored_extensions_str ) && in_array( $extension, $ignored_extensions_arr ) ) {
					return false;
				}
			}
			return true;
		}

		/**
		 * Checks if filename is allowed for renaming
		 *
		 * @version 2.1.1
		 * @since   2.1.1
		 *
		 * @param $filename
		 *
		 * @return bool
		 */
		protected function is_filename_allowed( $info ) {
			$option = new Ignore_Filenames_Option( array( 'section' => 'frou_advanced_opt' ) );
			if ( ! filter_var( $option->get_option( $option->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return true;
			}

			$ignore_without_extension = filter_var( $option->get_option( $option->option_ignore_without_extension ), FILTER_VALIDATE_BOOLEAN );

			// Gets extension
			$extension = empty( $info['extension'] ) ? '' : $info['extension'];

			if ( $ignore_without_extension ) {
				if ( ! empty( $extension ) ) {
					return true;
				}
			}

			if ( ! empty( $info['filename'] ) ) {
				$ignored_filenames_str = $option->get_option( $option->option_filenames_ignored );
				$ignored_filenames_arr = explode( ",", $ignored_filenames_str );
				$ignored_filenames_arr = array_map( 'trim', $ignored_filenames_arr );
				$ignored_filenames_arr = array_map( 'sanitize_text_field', $ignored_filenames_arr );
				if ( in_array( $info['filename'], $ignored_filenames_arr ) ) {
					return false;
				}
			}
			return true;
		}

		/**
		 * Sanitizes filename.
		 *
		 * It's the main function of this plugin
		 *
		 * @version 2.0.8
		 * @since   2.0.0
		 *
		 * @param $filename
		 *
		 * @return mixed|string
		 */
		public function sanitize_filename( $filename, $filename_raw ) {

			// Does nothing if plugin is not enabled
			$option = new Enable_Option( array( 'section' => 'frou_general_opt' ) );
			if ( ! filter_var( $option->get_option( $option->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename;
			}

			// Gets Info about the filename
			$info              = pathinfo( $filename );
			$extension         = empty( $info['extension'] ) ? '' : $info['extension'];
			$filename_original = $info['filename'];

			// Cancels in case of using github-updater option_page
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'github_updater' ) {
				return $filename;
			}
			if ( isset( $_POST['option_page'] ) && $_POST['option_page'] == 'github_updater' ) {
				return $filename;
			}

			// Cancels in case of specific filenames (this happens using some plugins like github-updater or All in one SEO PACK for example)
			if ( ! empty( $info ) && ! $this->is_filename_allowed( $info ) ) {
				return $filename;
			}

			// Ignores specific filename extensions
			if ( ! empty( $extension ) && ! $this->is_extension_allowed( $extension ) ) {
				return $filename;
			}

			// Gets plugin rules
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

			// Applies plugin's rules
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
		 * @version 2.0.0
		 * @since   2.0.0
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
		 * @version 2.0.0
		 * @since   2.0.0
		 */
		public function add_options() {
			$this->set_options( new Options() );
		}

		/**
		 * @version 2.0.0
		 * @since   2.0.0
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
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param Options $options
		 */
		public function set_options( $options ) {
			$this->options = $options;
		}


	}
}