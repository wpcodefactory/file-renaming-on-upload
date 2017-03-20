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


if ( ! class_exists( 'FROU\Admin_Pages\Sections\General_Section' ) ) {
	class General_Section {

		const OPTION_ENABLE_PLUGIN = 'enable_plugin';

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
			$this->id = 'frou_general_opt';
		}

		function get_settings_sections() {
			$section = array(
				'id'    => $this->id,
				'title' => __( 'General Settings', 'file-renaming-on-upload' ),
			);

			return $section;
		}

		/**
		 * Returns all the settings fields
		 *
		 * @return array settings fields
		 */
		function get_settings_fields() {
			$settings_fields = array(
				$this->id   => array(

					array(
						'name'    => self::OPTION_ENABLE_PLUGIN,
						'label'   => __( 'Enable plugin', 'file-renaming-on-upload' ),
						'desc'    => __( 'Enables the plugin', 'file-renaming-on-upload' ),
						'default' => 'on',
						'type'    => 'checkbox',
					),

					/*array(
						'name'    => 'asd',
						'label'   => __( 'Custom', 'file-renaming-on-upload' ),
						'desc'    => __( 'File description', 'file-renaming-on-upload' ),
						'callback' => array($this,'custom'),
						'type'    => 'custom',
						'default' => '',
						'options' => array(
							'button_label' => 'Choose Image',
						),
					),*/
				),
			);

			return $settings_fields;
		}
	}
}