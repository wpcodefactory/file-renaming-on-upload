<?php
/**
 * File renaming on upload - Plugin core
 *
 * @version 2.2.9
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
		 * @version 2.2.7
		 * @since   2.0.0
		 *
		 * @param array $args
		 */
		public function init( $args = array() ) {
			parent::init( $args );
			add_action( 'init', array( $this, 'handle_settings_page' ) );
			add_action( 'init', array( $this, 'add_options' ), 1 );
			add_filter( 'sanitize_file_name', array( $this, 'sanitize_filename' ), 10, 2 );
			add_action( 'admin_notices', array( $this, 'create_premium_notice' ) );
			add_action( 'admin_notices', array( $this, 'create_notice' ) );
			//add_action( 'add_attachment', array( $this, 'add_attachment' ) );
			//add_filter('wp_insert_attachment_data',array($this,'insert_attachment_data'),10,2);
			//add_action('wp_insert_post',array($this,'insert_post'));
		}

		public function create_notice(){
			$current_screen = get_current_screen();
			if (
				function_exists( 'FROUP\file_renaming_on_upload_pro' ) ||
				//$current_screen->id != 'plugins' ||
                ! get_transient( 'frou_activated_or_updated' )
			) {
				return;
			}
			delete_transient( 'frou_activated_or_updated' );
			?>
            <div class="notice notice-warning frou-notice is-dismissible">
                <h3 class="title">File Renaming on Upload</h3>
                <p>
                    <?php echo __('Do you like this plugin and find it useful? Help me!','file-renaming-on-upload'); ?> <?php echo sprintf(__('<a href="%s" target="_blank">Write a review</a> telling how it is useful for you.','file-renaming-on-upload'),'https://wordpress.org/support/plugin/file-renaming-on-upload/reviews/#new-post'); ?><br />
                    <?php echo __('That will help spread the word making other people know about it too.','file-renaming-on-upload'); ?><br />
                    <a target="_blank" class="button-secondary frou-call-to-action" style="margin-top:20px !important; margin-bottom:5px !important" href="https://wordpress.org/support/plugin/file-renaming-on-upload/reviews/#new-post"><?php echo __('Write a review','file-renaming-on-upload'); ?></a>
                </p>
                <p>
                    <hr style="margin-top:10px;margin-bottom:10px" />
                </p>

                <h3 class="title" style="margin-top:4px !important"><?php echo __('Premium version','file-renaming-on-upload'); ?></h3>
                <p>
                    <?php echo __('Did you know this plugin has a premium version?','file-renaming-on-upload'); ?><br />
                    <strong><?php echo __('Take a look at some of its features:','file-renaming-on-upload'); ?></strong>
                </p>

                <ul class="frou-notice-ul">
                    <li><?php echo __('Edit filenames and permalinks manually','file-renaming-on-upload'); ?></li>
                    <li><?php echo __('Update old media','file-renaming-on-upload'); ?></li>
                    <li><?php echo __('Autofill ALT tag','file-renaming-on-upload'); ?></li>
                    <li><?php echo __('Custom field rule','file-renaming-on-upload'); ?></li>
                    <li><?php echo __('New rules','file-renaming-on-upload'); ?></li>
                </ul>
                <p>
                    <a target="_blank" class="button-primary frou-call-to-action" href="https://wpcodefactory.com/item/file-renaming-on-upload-wordpress-plugin/"><?php echo __('Upgrade to premium version','file-renaming-on-upload'); ?></a>
                </p>
            </div>

			<?php
            $this->create_notice_style();
        }

        public function create_notice_style(){
		    ?>
            <style>
                .frou-notice h3{
                    margin: 18px 0 15px 2px;
                }
                .frou-notice{
                    margin-top:13px !important;
                    padding-left:20px ;
                }
                .frou-notice-ul{
                    margin-top:18px !important;
                    margin-left:3px;
                    list-style: disc inside;
                }
                .frou-call-to-action{
                    display:inline-block;
                    margin-bottom:14px !important;
                    margin-top:8px !important;
                    border:1px solid red;
                }
            </style>
            <?php
        }

		public function create_premium_notice() {
			$current_screen = get_current_screen();
			if (
				function_exists( 'FROUP\file_renaming_on_upload_pro' ) ||
				$current_screen->id != 'settings_page_file-renaming-on-upload'
			) {
				return;
			}
			?>
			<div class="notice notice-warning frou-notice">
				<h3 class="title"><?php echo __('Premium version','file-renaming-on-upload'); ?></h3>
				<p><?php echo __('Do you like the free version of this plugin? Imagine what the <strong>Premium</strong> version can do for you!','file-renaming-on-upload'); ?>
					<br/><?php echo sprintf(__('Check it out <a target="_blank" href="%1$s">here</a> or on this link: <a target="_blank" href="%1$s">%1$s</a>','file-renaming-on-upload'),'https://wpcodefactory.com/item/file-renaming-on-upload-wordpress-plugin/'); ?>					
				</p>
				<p  style="margin:12px 0 10px"><strong><?php echo __('Take a look at some of its features:','file-renaming-on-upload'); ?></strong></p>
				<ul class="frou-notice-ul">
					<li><?php echo __('Edit filenames and permalinks manually','file-renaming-on-upload'); ?></li>
                    <li><?php echo __('Update old media','file-renaming-on-upload'); ?></li>
                    <li><?php echo __('Autofill ALT tag','file-renaming-on-upload'); ?></li>
                    <li><?php echo __('Custom field rule','file-renaming-on-upload'); ?></li>
                    <li><?php echo __('New rules','file-renaming-on-upload'); ?></li>
				</ul>
				<p>
					<a target="_blank" class="button-primary frou-call-to-action" href="https://wpcodefactory.com/item/file-renaming-on-upload-wordpress-plugin/">Upgrade to Premium version</a>
				</p>
			</div>
			<?php
			$this->create_notice_style();
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
		 * @version 2.2.9
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

			$filename_arr_rules = $filename_arr['structure']['rules']; 
			if( empty( $filename_arr_rules ) ){
				return $filename;
			}

			// Applies plugin's rules
			$filename = $filename_arr['structure']['rules'];
			$filename = $this->remove_unused_rules( $filename, $filename_arr );
			$filename = $this->add_separator( $filename, $filename_arr );
			$filename = $this->translate_rules( $filename, $filename_arr );

			if ( ! empty( $info['extension'] ) ) {
				$filename = $filename . '.' . $extension;
			}else{
				$filename = $filename;
			}
			//error_log(print_r($filename,true));
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