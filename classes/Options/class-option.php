<?php
/**
 * File renaming on upload - Option
 *
 * @version 2.0.0
 * @since   2.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Option' ) ) {
	class Option {

		public $fields = array();
		protected $section;
		public $option_id;

		/**
		 * Constructor
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param array $args
		 */
		function __construct( $args = array() ) {
			$args = wp_parse_args( $args, array(
				'section' => null,
			) );

			$this->section = $args['section'];
			$this->add_fields(array(),$this->section);
		}

		/**
		 * Initializes
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 */
		function init() {
			add_filter( "frou_fields_{$this->section}", array( $this, 'add_fields' ), 10, 2 );
		}

		/**
		 * Gets option from this option section
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param        $option
		 * @param string $default
		 * @param null   $section
		 *
		 * @return string
		 */
		function get_option( $option, $default = '', $section = null ) {
			if ( ! $section ) {
				$section = $this->section;
			}

			$options = get_option( $section );

			if ( isset( $options[ $option ] ) ) {
				return $options[ $option ];
			}

			foreach ( $this->fields as $index => $field ) {
				if ( $field['name'] == $option && isset( $field['default'] ) ) {
					return $field['default'];
					break;
				}
			}

			return $default;
		}

		/**
		 * Add settings fields
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 */
		public function add_fields( $fields, $section ) {
			$this->fields = $fields;

			return $fields;
		}
	}
}