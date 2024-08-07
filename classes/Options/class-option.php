<?php
/**
 * File renaming on upload - Option.
 *
 * @version 2.5.9
 * @since   2.0.0
 * @author  WPFactory
 */

namespace FROU\Options;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Option' ) ) {
	class Option {

		/**
		 * fields.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		public $fields = array();

		/**
		 * section.
		 *
		 * @since 1.0.0
		 *
		 * @var
		 */
		protected $section;

		/**
		 * option_id.
		 *
		 * @since 1.0.0
		 *
		 * @var
		 */
		public $option_id;

		/**
		 * Constructor.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param   array  $args
		 */
		function __construct( $args = array() ) {
			$args = wp_parse_args( $args, array(
				'section' => null,
			) );

			$this->section = $args['section'];
			$this->add_fields( array(), $this->section );
		}

		/**
		 * Initializes.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 */
		function init() {
			add_filter( "frou_fields_{$this->section}", array( $this, 'add_fields' ), 10, 2 );
		}

		/**
		 * Gets option from this option section.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param           $option
		 * @param   string  $default
		 * @param   null    $section
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
		 * Add settings fields.
		 *
		 * @version 2.5.5
		 * @since   2.0.0
		 */
		public function add_fields( $fields, $section ) {
			$this->fields = $fields;
			foreach ( $this->fields as $k => $v ) {
				if ( ! isset( $v['sanitize_callback'] ) ) {
					switch ( $v['type'] ) {
						case '':
							$this->fields[ $k ]['sanitize_callback'] = 'sanitize_textarea_field';
							break;
						case 'text':
							$this->fields[ $k ]['sanitize_callback'] = 'sanitize_text_field';
							break;
						case 'multiselect':
							$this->fields[ $k ]['sanitize_callback'] = array( $this, 'sanitize_multiselect' );
						case 'multicheck':
							$this->fields[ $k ]['sanitize_callback'] = array( $this, 'sanitize_multicheck' );
							break;
						default:
							$this->fields[ $k ]['sanitize_callback'] = 'sanitize_text_field';
					}

				}
			}

			return $this->fields;
		}

		/**
		 * sanitize_multicheck.
		 *
		 * @version 2.5.9
		 * @since   2.5.9
		 *
		 * @param $value
		 *
		 * @return array
		 */
		function sanitize_multicheck( $value ) {
			$result = array();
			foreach ( $value as $k => $v ) {
				$result[ sanitize_text_field( $k ) ] = sanitize_text_field( $v );
			}

			return $result;
		}

		/**
		 * sanitize_multiselect.
		 *
		 * @version 2.5.5
		 * @since   2.0.0
		 *
		 * @param $value
		 *
		 * @return array
		 */
		function sanitize_multiselect( $value ) {
			$value = array_map( 'sanitize_text_field', $value );

			return $value;
		}
	}
}