<?php
/**
 * File renaming on upload - General Settings Page
 *
 * @version 2.0.0
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

		function admin_init() {

			// Sections
			/*$general_section = new General_Section( $this->core );
			$add_section     = new Add_Section( $this->core );
			$remove_section  = new Remove_Section( $this->core );
			$convert_section = new Convert_Section( $this->core );

			$this->settings_api->add_section( $general_section->get_settings_sections() );
			$this->settings_api->add_section( $add_section->get_settings_sections() );
			$this->settings_api->add_section( $remove_section->get_settings_sections() );
			$this->settings_api->add_section( $convert_section->get_settings_sections() );

			// Fields
			$this->settings_api->set_fields(
				array_merge(
					$general_section->get_settings_fields(),
					$add_section->get_settings_fields(),
					$remove_section->get_settings_fields(),
					$convert_section->get_settings_fields()
				)
			);*/

			// Sections
			$sections = array(
				array(
					'id'    => 'frou_general_opt',
					'title' => __( 'General Settings', 'file-renaming-on-upload' ),
				),
				array(
					'id'    => 'frou_remove_opt',
					'title' => __( 'Remove', 'file-renaming-on-upload' ),
				),
				array(
					'id'    => 'frou_add_opt',
					'title' => __( 'Add', 'file-renaming-on-upload' ),
				),
				array(
					'id'    => 'frou_convert_opt',
					'title' => __( 'Convert', 'file-renaming-on-upload' ),
				),
			);
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

		function get_option( $option, $section, $default = '' ) {
			$options = get_option( $section );

			if ( isset( $options[ $option ] ) ) {
				return $options[ $option ];
			}

			return $default;
		}

		function admin_menu() {
			add_options_page( 'File renaming', 'File renaming (new)', 'delete_posts', 'frou', array(
				$this,
				'plugin_page',
			) );
		}

		/*function custom( $args ) {

			$value       = esc_attr( $this->settings_api->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$type        = isset( $args['type'] ) ? $args['type'] : 'text';
			$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';

			$html = sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder );
			$html .= $this->settings_api->get_field_description( $args );

			echo $html;
		}*/

		function plugin_page() {
			echo '<div class="wrap">';
			echo '<h2>File Renaming on upload</h2>';
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