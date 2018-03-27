<?php
/**
 * File renaming on upload - General Settings Page
 *
 * @version 2.2.3
 * @since   2.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Admin_Pages;

use FROU\Admin_Pages\Sections\Convert_Section;
use FROU\Admin_Pages\Sections\General_Section;
use FROU\Admin_Pages\Sections\Add_Section;
use FROU\Admin_Pages\Sections\Remove_Section;
use FROU\Admin_Pages\Sections\Url_Section;
use FROU\Plugin_Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Admin_Pages\Settings_Page' ) ) {
	class Settings_Page {

		/**
		 * @var \WeDevs_Settings_API
		 */
		private $settings_api;

		/**
		 * @var Plugin_Core
		 */
		private $core;

		function __construct( Plugin_Core $core ) {
			$this->core         = $core;
			$this->settings_api = $core->settings_api;
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}

		/**
		 * Creates settings separated by tabs
		 *
		 * @version 2.1.6
		 * @since   2.0.0
		 */
		function admin_init() {
			// Sections
			$sections = array(
				array(
					'id'    => 'frou_general_opt',
					'title' => __( 'General Settings', 'file-renaming-on-upload' ),
				),
				array(
					'id'    => 'frou_filenaming_rules_opt',
					'title' => __( 'Rules', 'file-renaming-on-upload' ),
				),
				array(
					'id'    => 'frou_advanced_opt',
					'title' => __( 'Advanced', 'file-renaming-on-upload' ),
				),
			);
			$sections = apply_filters( 'frou_admin_sections', $sections );
			$this->settings_api->set_sections( $sections );

			// Fields
			$fields = array();
			foreach ( $sections as $section ) {
				$fields[ $section['id'] ] = apply_filters( "frou_fields_{$section['id']}", array(), $section['id'] );
			}
			$this->settings_api->set_fields( $fields );

			//initialize settings
			$this->settings_api->admin_init();
		}

		/**
		 * Gets an option inside a setting
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param        $option
		 * @param        $section
		 * @param string $default
		 *
		 * @return string
		 */
		function get_option( $option, $section, $default = '' ) {
			$options = get_option( $section );

			if ( isset( $options[ $option ] ) ) {
				return $options[ $option ];
			}

			return $default;
		}

		/**
		 * Creates the menu
		 *
		 * @version 2.2.3
		 * @since   2.0.0
		 */
		function admin_menu() {
			add_options_page( __('File renaming','file-renaming-on-upload'), __('File renaming','file-renaming-on-upload'), 'edit_users', 'file-renaming-on-upload', array(
				$this,
				'plugin_page',
			) );
		}

		/**
		 * Shows the admin page
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 */
		function plugin_page() {
			echo '<div class="wrap">';
			echo '<h2>'.__('File Renaming on upload','file-renaming-on-upload').'</h2>';

			$this->settings_api->show_navigation();
			$this->settings_api->show_forms();
			echo '</div>';
		}

		/**
		 * Get all the pages
		 *
		 * @return array page names with key value pairs
		 */
		function get_pages() {
			$pages         = get_pages();
			$pages_options = array();
			if ( $pages ) {
				foreach ( $pages as $page ) {
					$pages_options[ $page->ID ] = $page->post_title;
				}
			}

			return $pages_options;
		}
	}

}