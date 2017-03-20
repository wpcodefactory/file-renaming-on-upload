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


if ( ! class_exists( 'FROU\Admin_Pages\Sections\Convert_Section' ) ) {
	class Convert_Section {

		const OPTION_LOWERCASE = 'lowercase';
		const OPTION_ACCENTS   = 'accents';
		const OPTION_DATETIME  = 'datetime';

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
			$this->id           = 'frou_convert_opt';
		}

		function get_settings_sections() {
			//$sections = $this->settings_api->sett
			$section = array(
				'id'    => $this->id,
				'title' => __( 'Convert', 'file-renaming-on-upload' ),
			);

			return $section;
		}

		function get_settings_fields() {
			$settings_fields = array(
				$this->id => array(
					array(
						'name'    => self::OPTION_LOWERCASE,
						'label'   => __( 'Lowercase', 'file-renaming-on-upload' ),
						'desc'    => __( 'Converts all characters to lowercase', 'file-renaming-on-upload' ),
						'type'    => 'checkbox',
						'default' => 'on',
					),
					array(
						'name'    => self::OPTION_ACCENTS,
						'label'   => __( 'Accents', 'file-renaming-on-upload' ),
						'desc'    => __( 'Converts all accent characters to ASCII characters', 'file-renaming-on-upload' ),
						'type'    => 'checkbox',
						'default' => 'on',
					),
					array(
						'name'    => self::OPTION_DATETIME,
						'label'   => __( 'Datetime', 'file-renaming-on-upload' ),
						'desc'    => __( 'Replaces filename by datetime', 'file-renaming-on-upload' ),
						'type'    => 'checkbox',
						'default' => 'off',
					),
				),
			);

			return $settings_fields;
		}
	}
}