<?php
/**
 * File renaming on upload - Ignore empty Extensions Option
 *
 * @version 2.3.0
 * @since   2.3.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options\Advanced;

use FROU\Options\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Advanced\Ignore_Empty_Extensions_Option' ) ) {
	class Ignore_Empty_Extensions_Option extends Option {

		public $option_extensions_ignored = 'extensions_ignored';

		/**
		 * Initializes
		 *
		 * @version 2.3.0
		 * @since   2.3.0
		 */
		function init() {
			parent::init();
		}

		/**
		 * Constructor
		 *
		 * @version 2.3.0
		 * @since   2.3.0
		 *
		 * @param array $args
		 */
		function __construct( array $args = array() ) {
			parent::__construct( $args );
			$this->option_id = 'ignore_empty_extensions';
		}

		/**
		 * Adds settings fields
		 *
		 * @version 2.3.0
		 * @since   2.3.0
		 *
		 * @param $fields
		 * @param $section
		 *
		 * @return mixed
		 */
		public function add_fields( $fields, $section ) {
			$new_options = array(
				array(
					'name'           => $this->option_id,
					'label'          => __( 'Ignore files with no extension', 'file-renaming-on-upload' ),
					'desc'           => __( 'Does not rename files without extension', 'file-renaming-on-upload' ),
					'desc_secondary' => __( 'If you have to upload files without extensions uncheck this option, but it may create unpredictable incompatibility issues with third party plugins and themes
', 'file-renaming-on-upload' ),
					'default'        => 'on',
					'type'           => 'checkbox',
				),
				array(
					'name' => 'extension_separator',
					'type' => 'separator',
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}