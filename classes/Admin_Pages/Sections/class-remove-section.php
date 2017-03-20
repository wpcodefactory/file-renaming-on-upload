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


if ( ! class_exists( 'FROU\Admin_Pages\Sections\Remove_Section' ) ) {
	class Remove_Section {


		const OPTION_SPACES='spaces';

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
			$this->id           = 'frou_remove_opt';
		}

		function get_settings_sections() {
			//$sections = $this->settings_api->sett
			$section = array(
				'id'    => $this->id,
				'title' => __( 'Remove', 'file-renaming-on-upload' ),
			);

			return $section;
		}

		function get_settings_fields() {
			/*$settings_fields = array(
				$this->id => array(
					array(
						'name'    => self::OPTION_CHARACTERS,
						'label'   => __( 'Characters', 'file-renaming-on-upload' ),
						'desc'    => __( 'Removes characters', 'file-renaming-on-upload' ),
						'type'    => 'checkbox',
						'default' => 'on',
					),
					array(
						'name'    => self::OPTION_CHARACTERS_TEXT,
						'desc'    => __( 'Characters to be removed (space separated)', 'file-renaming-on-upload' ),
						'type'    => 'textarea',
						'default' => '? + [ ] / \ = < > : ; , \' " & $ # * ( ) | ~ ` ! { } ¨ % @ ^',
					),
					array(
						'name'    => self::OPTION_SPACES,
						'label'   => __( 'Spaces', 'file-renaming-on-upload' ),
						'desc'    => __( 'Removes spaces from the beggining / end of the file name', 'file-renaming-on-upload' ),
						'type'    => 'multicheck',
						'default' => array( 'beginning' => 'beginning', 'end' => 'end'),
						'options' => array(
							'beginning' => 'Beginning',
							'end'       => 'End',
						),
					),
				),
			);*/

			$section = $this->id;
			$settings_fields = apply_filters("frou_fields_{$section}",array(),$section);

			return $settings_fields;
		}
	}
}