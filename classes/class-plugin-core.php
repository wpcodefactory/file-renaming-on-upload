<?php
/**
 * File renaming on upload - Plugin core.
 *
 * @version 2.5.2
 * @since   2.0.0
 * @author  WPFactory
 */

namespace FROU;

use FROU\Admin_Pages\Sections\Remove_Section;
use FROU\Admin_Pages\Settings_Page;
use FROU\Functions\Functions;

use FROU\Options\Advanced\Ignore_Empty_Extensions_Option;
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
		 * settings_api.
		 *
		 * @since 1.0.0
		 *
		 * @var \WeDevs_Settings_API
		 */
		public $settings_api;

		/**
		 * current_filename_modified.
		 *
		 * @since 1.0.0
		 *
		 * @var
		 */
		public $current_filename_modified;

		/**
		 * current_filename_original.
		 *
		 * @since 1.0.0
		 *
		 * @var
		 */
		public $current_filename_original;

		/**
		 * options.
		 *
		 * @since 1.0.0
		 *
		 * @var Options
		 */
		protected $options;

		/**
		 * instance.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Plugin_Core
		 */
		public static function getInstance() {
			return parent::getInstance();
		}

		/**
		 * Initializes.
		 *
		 * @version 2.4.5
		 * @since   2.0.0
		 *
		 * @param array $args
		 */
		public function init( $args = array() ) {
			parent::init( $args );
			add_action( 'init', array( $this, 'handle_settings_page' ) );
			add_action( 'init', array( $this, 'add_options' ), 1 );
			add_filter( 'sanitize_file_name', array( $this, 'sanitize_filename' ), 10, 2 );
			add_action( 'admin_init', array( $this, 'add_promoting_notice' ) );
			//add_action( 'admin_notices', array( $this, 'create_notice' ) );
			add_filter( 'frou_filename_allowed', array( $this, 'block_ignored_filenames' ), 10, 3 );
			add_filter( 'frou_filename_allowed', array( $this, 'block_renaming_by_extension' ), 10, 3 );
			add_filter( 'frou_renaming_validation', array( $this, 'disable_renaming_on_wc_export' ),10,2 );
			add_action( 'add_attachment', array( $this, 'save_original_file_name' ) );
			//add_action( 'add_attachment', array( $this, 'add_attachment' ) );
			//add_filter('wp_insert_attachment_data',array($this,'insert_attachment_data'),10,2);
			//add_action('wp_insert_post',array($this,'insert_post'));
			//add_filter('wp_insert_attachment_data',array($this,'wp_insert_attachment_data'),10,3);
		}

		/**
		 * get_current_media_id.
		 *
		 * @version 2.4.6
		 * @since   2.4.6
		 *
		 * @return int|null
		 */
		static function get_current_media_id() {
			return apply_filters( 'frou_current_media_id', null );
		}

		/**
		 * save_original_file_name.
		 *
		 * @version 2.4.5
		 * @since   2.4.5
		 *
		 * @param $post_id
		 */
		function save_original_file_name( $post_id ) {
			$post = get_post( $post_id );
			update_post_meta( $post_id, '_frou_original_filename', $post->post_name );
		}

		/**
		 * Disables renaming when using WooCommerce Export Products.
		 *
		 * @version 2.3.9
		 * @since   2.3.9
		 *
		 * @param $validation
		 * @param $info
		 *
		 * @return mixed
		 */
		function disable_renaming_on_wc_export( $validation, $info ) {
			if (
				! isset( $info['request'] ) ||
				! isset( $info['request']['action'] ) ||
				empty( $info['request']['action'] )
			) {
				return $validation;
			}
			if (
				'woocommerce_do_ajax_product_export' == $info['request']['action'] ||
				'download_product_csv' == $info['request']['action']
			) {
				$validation = false;
			}
			return $validation;
		}

		/**
         * Blocks renaming by extension
         *
		 * @version 2.3.9
		 * @since   2.3.1
         *
		 * @param $allowed
		 * @param $filename
		 * @param $infs
		 *
		 * @return mixed
		 */
		public function block_renaming_by_extension( $allowed, $filename, $infs ) {
			$extension = isset( $infs['extension'] ) ? $infs['extension'] : '';
			if ( ! empty( $extension ) && ! $this->is_extension_allowed( $extension ) ) {
				$allowed = false;
			}
			return $allowed;
		}

		/**
         * Blocks renaming by filename
         *
		 * @param $allowed
		 * @param $filename
		 * @param $infs
		 *
		 * @return bool
		 */
		public function block_ignored_filenames( $allowed, $filename, $infs ) {
			$info = isset( $infs['info'] ) ? $infs['info'] : '';
			if ( ! empty( $info ) && ! $this->is_filename_allowed( $info ) ) {
				$allowed = false;
			}
			return $allowed;
		}

		/**
		 * add_promoting_notice.
		 *
		 * @version 2.4.5
		 * @since   2.4.5
		 */
		public function add_promoting_notice() {
			$promoting_notice = wpfactory_promoting_notice();
			$promoting_notice->set_args( array(
				'url_requirements'              => array(
					'page_filename' => 'options-general.php',
					'params'        => array( 'page' => 'file-renaming-on-upload' ),
				),
				'enable'                        => true === apply_filters( 'frou_is_free_version', true ),
				'optimize_plugin_icon_contrast' => true,
				'template_variables'            => array(
					'%notice_class%'       => 'wpfactory-promoting-notice notice notice-info',
					'%pro_version_url%'    => 'https://wpfactory.com/item/file-renaming-on-upload-wordpress-plugin/',
					'%plugin_icon_url%'    => 'https://ps.w.org/file-renaming-on-upload/assets/icon-128x128.png',
					'%pro_version_title%'  => __( 'File Renaming on upload Pro', 'file-renaming-on-upload' ),
					'%main_text%'          => __( 'Unlock more options with <a href="%pro_version_url%" target="_blank"><strong>%pro_version_title%</strong></a>', 'file-renaming-on-upload' ),
					'%btn_call_to_action%' => __( 'Upgrade to Pro version', 'file-renaming-on-upload' ),
				),
			) );
			$promoting_notice->init();
		}

		/**
		 * Removes unused rules.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param $filename
		 * @param array $args
		 *
		 * @return mixed
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
		 * Translates rules.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param $filename
		 * @param array $args
		 *
		 * @return mixed
		 */
		protected function translate_rules( $filename, $args ) {
			foreach ( $args['structure']['translation'] as $key => $translation ) {
				$filename = str_replace( "{" . $key . "}", $translation, $filename );
			}

			return $filename;
		}

		/**
		 * Adds separator.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
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

		/**.
		 * Checks if extension is allowed for renaming.
		 *
		 * @version 2.1.8
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
				$ignored_extensions_arr = apply_filters( 'frou_ignored_extensions', $ignored_extensions_arr );
				$ignored_extensions_arr = array_map( 'trim', $ignored_extensions_arr );
				$ignored_extensions_arr = array_map( 'sanitize_text_field', $ignored_extensions_arr );
				$ignored_extensions_arr = array_unique( $ignored_extensions_arr );
				if ( ! empty( $ignored_extensions_str ) && in_array( $extension, $ignored_extensions_arr ) ) {
					return false;
				}
			}
			return true;
		}

		/**
		 * Checks if filename is allowed for renaming.
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
		 * It's the main function of this plugin.
		 *
		 * @version 2.5.2
		 * @since   2.0.0
		 *
		 * @param $filename
		 *
		 * @return mixed|string
		 */
		public function sanitize_filename( $filename, $filename_raw ) {
			//error_log('---');
			//error_log(print_r($_REQUEST,true));
			//error_log(print_r($filename,true));

			//Does nothing if plugin is not enabled
			$option = new Enable_Option( array( 'section' => 'frou_general_opt' ) );
			if ( ! filter_var( $option->get_option( $option->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename;
			}

			// Gets Info about the filename
			$info              = pathinfo( $filename );
			$extension         = empty( $info['extension'] ) ? '' : $info['extension'];
			$filename_original = $info['filename'];

			$allowed = apply_filters( 'frou_filename_allowed', true, $filename, array( 'info' => $info, 'extension' => $extension ) );
			$allowed_to_rename = apply_filters( 'frou_renaming_validation', true, array( 'request' => $_REQUEST, 'info' => $info, 'extension' => $extension, 'filename' => $filename, 'filename_raw' => $filename_raw ) );
			if ( ! $allowed || ! $allowed_to_rename ) {
				return $filename;
			}

			$ignore_empty_ext_opt = new Ignore_Empty_Extensions_Option( array( 'section' => 'frou_advanced_opt' ) );
			if (
				'on' === $ignore_empty_ext_opt->get_option( $ignore_empty_ext_opt->option_id, 'on' ) &&
				empty( $extension )
			) {
				return $filename;
			}

			// Cancels in case of using github-updater option_page
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'github_updater' ) {
				return $filename;
			}
			if ( isset( $_POST['option_page'] ) && $_POST['option_page'] == 'github_updater' ) {
				return $filename;
			}

			// Gets plugin rules
			$filename_arr = apply_filters( 'frou_sanitize_file_name',
				array(
					'filename_original' => $filename_original,
					'extension'         => $extension,
					'new_extension'     => '',
					'structure'         => array(
						'rules'       => '',
						'separator'   => '-',
						'translation' => array( 'filename' => $filename_original ),
					),
				)
			);

			$filename_arr_rules = $filename_arr['structure']['rules'];
			if( empty( $filename_arr_rules ) ){
				return $filename;
			}

			// Applies plugin's rules
			$filename = $filename_arr['structure']['rules'];
			$filename = $this->remove_unused_rules( $filename, $filename_arr );
			$filename = $this->remove_empty_rules( $filename, $filename_arr );
			$filename = $this->add_separator( $filename, $filename_arr );
			$filename = $this->translate_rules( $filename, $filename_arr );

			$filename = apply_filters( 'frou_after_sanitize_file_name', $filename, $info );

			if ( ! empty( $info['extension'] ) ) {
				$extension = ! empty( $filename_arr['new_extension'] ) ? $filename_arr['new_extension'] : $extension;
				$filename  = $filename . '.' . $extension;
			}
			//error_log('FINAL: '.print_r($filename,true));
			return $filename;
		}

		/**
		 * Translates rules.
		 *
		 * @version 2.5.2
		 * @since   2.5.2
		 *
		 * @param $filename
		 * @param array $args
		 *
		 * @return mixed
		 */
		protected function remove_empty_rules( $filename, $args ) {
			foreach ( $args['structure']['translation'] as $key => $translation ) {
				if ( empty( $translation ) ) {
					$filename = str_replace( "{" . $key . "}", $translation, $filename );
				}
			}
			return $filename;
		}

		/**
		 * try_to_get_natural_string.
		 *
		 * @version 2.5.2
		 * @since   2.5.2
		 *
		 * @param $string
		 * @param $info
		 *
		 * @return string
		 */
		public function try_to_get_natural_string( $string, $info ) {
			if ( isset( $info['raw_string'] ) && ! empty( $info['raw_string'] ) ) {
				$string = $info['raw_string'];
			} elseif (
				isset( $info['object'] ) &&
				! empty( $object = $info['object'] )
			) {
				if ( is_a( $object, 'WP_Post' ) ) {
					$string = $object->post_title;
				}
			}
			return $string;
		}

		/**
		 * Manages the settings page.
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
		 * Creates the options inside settings pages.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 */
		public function add_options() {
			$this->set_options( new Options() );
		}

		/**
		 * get options.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
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
		 * set options.
		 *
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