<?php
/**
 * File renaming on upload - Option
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Option' ) ) {
	class Option {

		protected $section;

		function __construct( $args = array() ) {
			$args = wp_parse_args( $args, array(
				'section' => null,
			) );

			$this->section = $args['section'];
		}

		function init() {
			add_filter( "frou_fields_{$this->section}", array( $this, 'add_fields' ), 10, 2 );
		}

		function get_option( $option, $default = '' ) {
			$section = $this->section;
			$options = get_option( $section );

			if ( isset( $options[ $option ] ) ) {
				return $options[ $option ];
			}

			return $default;
		}

		public function add_fields( $fields, $section ) {
			return $fields;
		}
	}
}