<?php
/**
 * File renaming on upload - Enable Option
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options\General;

use FROU\Options\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\General\Enable_Option' ) ) {
	class Enable_Option extends Option {

		function init() {
			parent::init();
		}

		function __construct( array $args = array() ) {
			parent::__construct( $args );
			$this->option_id = 'enable_plugin';
		}

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