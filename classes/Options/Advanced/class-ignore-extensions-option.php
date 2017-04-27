<?php
/**
 * File renaming on upload - Ignore Extensions Option
 *
 * @version 2.1.1
 * @since   2.0.3
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options\Advanced;

use FROU\Options\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Advanced\Ignore_Extensions_Option' ) ) {
	class Ignore_Extensions_Option extends Option {

		public $option_extensions_ignored = 'extensions_ignored';

		/**
		 * Initializes
		 *
		 * @version 2.0.3
		 * @since   2.0.3
		 */
		function init() {
			parent::init();
		}

		/**
		 * Constructor
		 *
		 * @version 2.0.3
		 * @since   2.0.3
		 *
		 * @param array $args
		 */
		function __construct( array $args = array() ) {
			parent::__construct( $args );
			$this->option_id = 'ignore_extensions';
		}

		/**
		 * Adds settings fields
		 *
		 * @version 2.1.1
		 * @since   2.0.3
		 *
		 * @param $fields
		 * @param $section
		 *
		 * @return mixed
		 */
		public function add_fields( $fields, $section ) {
			$new_options = array(
				array(
					'name'    => $this->option_id,
					'label'   => __( 'Ignore file extensions', 'file-renaming-on-upload' ),
					'desc'    => __( 'Does not rename filenames with these extensions', 'file-renaming-on-upload' ) . ' (' . __( 'comma separated', 'file-renaming-on-upload' ) . ')',
					'default' => 'on',
					'type'    => 'checkbox',
				),
				array(
					'name'        => $this->option_extensions_ignored,
					'placeholder' => 'Comma separated extensions',
					'default'     => 'tmp',
					'type'        => 'text',
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