<?php
/**
 * File renaming on upload - Enable Option
 *
 * @version 2.0.0
 * @since   2.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options\General;

use FROU\Options\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\General\Enable_Option' ) ) {
	class Enable_Option extends Option {

		/**
		 * Initializes
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 */
		function init() {
			parent::init();
		}

		/**
		 * Constructor
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param array $args
		 */
		function __construct( array $args = array() ) {
			parent::__construct( $args );
			$this->option_id = 'enable_plugin';
		}

		/**
		 * Adds settings fields
		 *
		 * @version 2.0.0
		 * @since   2.0.0
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
					'label'   => __( 'Enable plugin', 'file-renaming-on-upload' ),
					'desc'    => __( 'Enables the plugin', 'file-renaming-on-upload' ),
					'default' => 'on',
					'type'    => 'checkbox',
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}