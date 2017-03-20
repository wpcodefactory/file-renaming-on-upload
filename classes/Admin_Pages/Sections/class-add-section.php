<?php
/**
 * File renaming on upload - General section
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Admin_Pages\Sections;

use FROU\Plugin_Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Admin_Pages\Sections\Add_Section' ) ) {
	class Add_Section {

		const OPTION_SITE_URL          = 'site_url';
		const OPTION_SITE_URL_POSITION = 'site_url_position';
		const OPTION_SITE_URL_TEXT     = 'site_url_text';

		const OPTION_DATETIME          = 'datetime';
		const OPTION_DATETIME_POSITION = 'datetime_position';

		const OPTION_CHARACTERS_PREPEND      = 'characters_prepend';
		const OPTION_CHARACTERS_PREPEND_TEXT = 'characters_prepend_text';

		const OPTION_CHARACTERS_APPEND      = 'characters_append';
		const OPTION_CHARACTERS_APPEND_TEXT = 'characters_append_text';

		/**
		 * @var Plugin_Core
		 */
		private $core;

		/**
		 * @var \WeDevs_Settings_API
		 */
		private $settings_api;

		protected $id;

		function __construct( Plugin_Core $core ) {
			$this->settings_api = $core->settings_api;
			$this->id           = 'frou_add_opt';
		}

		function get_settings_sections() {
			//$sections = $this->settings_api->sett
			$section = array(
				'id'    => $this->id,
				'title' => __( 'Add', 'file-renaming-on-upload' ),
			);

			return $section;
		}

		function get_settings_fields() {
			$frou      = Plugin_Core::getInstance();
			$functions = $frou->get_functions();

			$settings_fields = array(
				$this->id => array(

					// Site URL
					array(
						'name'    => self::OPTION_SITE_URL,
						'label'   => __( 'Site URL', 'file-renaming-on-upload' ),
						'desc'    => __( 'Inserts site URL', 'file-renaming-on-upload' ),
						'type'    => 'checkbox',
						'default' => 'on',
					),
					array(
						'name'    => self::OPTION_SITE_URL_POSITION,
						'type'    => 'radio',
						'default' => 'beginning',
						'options' => array(
							'beginning' => __( 'Inserts at the beggining of the file name', 'file-renaming-on-upload' ),
							'end'       => __( 'Inserts at the end of the file name', 'file-renaming-on-upload' ),
						),
					),
					array(
						'name'        => self::OPTION_SITE_URL_TEXT,
						'desc'        => __( 'The site URL (change it the way you like)', 'file-renaming-on-upload' ),
						'type'        => 'text',
						'placeholder' => $functions->get_site_url(),
						'default'     => $functions->get_site_url(),
					),

					// Prepend Characters
					array(
						'name'    => self::OPTION_CHARACTERS_PREPEND,
						'label'   => __( 'Prepend Characters', 'file-renaming-on-upload' ),
						'desc'    => __( 'Inserts Characters at the beginning of the file name', 'file-renaming-on-upload' ),
						'type'    => 'checkbox',
						'default' => 'off',
					),
					array(
						'name'    => self::OPTION_CHARACTERS_PREPEND_TEXT,
						'type'    => 'text',
						'default' => '',
					),

					// Append characters
					array(
						'name'    => self::OPTION_CHARACTERS_APPEND,
						'label'   => __( 'Append Characters', 'file-renaming-on-upload' ),
						'desc'    => __( 'Inserts Characters at the end of the file name', 'file-renaming-on-upload' ),
						'type'    => 'checkbox',
						'default' => 'off',
					),
					array(
						'name'    => self::OPTION_CHARACTERS_APPEND_TEXT,
						'type'    => 'text',
						'default' => '',
					),


					/*array(
						'name'    => self::OPTION_DATETIME,
						'label'   => __( 'Datetime', 'file-renaming-on-upload' ),
						'desc'    => __( 'Inserts Datetime', 'file-renaming-on-upload' ),
						'type'    => 'checkbox',
						'default' => 'on',
					),
					array(
						'name'    => self::OPTION_DATETIME_POSITION,
						'type'    => 'radio',
						'default' => 'beginning',
						'options' => array(
							'beginning' => __( 'Inserts at the beggining of the file name', 'file-renaming-on-upload' ),
							'end'       => __( 'Inserts at the end of the file name', 'file-renaming-on-upload' ),
						),
					),*/


				),
			);

			return $settings_fields;
		}
	}
}