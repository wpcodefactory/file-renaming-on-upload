<?php
/**
 * File renaming on upload - Datetime Option
 *
 * @version 2.0.7
 * @since   2.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options\Rules;

use FROU\Options\Add_Option;
use FROU\Options\Option;
use FROU\Options\Rule_Option;
use FROU\Plugin_Core;
use FROU\WordPress\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Rules\Datetime_Option' ) ) {
	class Datetime_Option extends Rule_Option {

		public $option_datetime_format = 'datetime_format';

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
			$this->option_id = 'datetime';
		}

		/**
		 * Initializes
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 */
		function init() {
			parent::init();
			add_filter( 'frou_sanitize_file_name', array( $this, 'frou_sanitize_file_name' ), 11 );
		}

		/**
		 * Turns this rule off as default
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param $structure_rules
		 *
		 * @return array
		 */
		public function add_structure_rule( $structure_rules ) {
			if ( ! filter_var( $this->get_option( $this->option_id, false ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $structure_rules;
			}
			$structure_rules[] = $this->option_id;

			return $structure_rules;
		}

		/**
		 * Inserts datetime on 'frou_sanitize_file_name' filter
		 *
		 * @version 2.0.7
		 * @since   2.0.0
		 *
		 * @param $filename_infs
		 *
		 * @return mixed
		 */
		public function frou_sanitize_file_name( $filename_infs ) {
			if ( ! filter_var( $this->get_option( $this->option_id, false ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			$structure_rules = $filename_infs['structure']['rules'];
			if ( strpos( $structure_rules, '{' . $this->option_id . '}' ) !== false ) {
				$datetime                                                      = \DateTime::createFromFormat( 'U.u', number_format(microtime(true), 6, '.', '') );
				$format                                                        = $datetime->format( $this->get_option( $this->option_datetime_format, 'Y-m-d_H-i-s_u' ) );
				$filename_infs['structure']['translation'][ $this->option_id ] = $format;
			}

			return $filename_infs;
		}

		/**
		 * Adds settings fields
		 *
		 * @version 2.0.7
		 * @since   2.0.0
		 *
		 * @param $fields
		 * @param $section
		 *
		 * @return mixed
		 */
		public function add_fields( $fields, $section ) {
			$datetime = \DateTime::createFromFormat( 'U.u', number_format(microtime(true), 6, '.', '') );
			$format   = $datetime->format( $this->get_option( $this->option_datetime_format, 'Y-m-d_H-i-s_u' ) );

			$new_options = array(
				array(
					'name'    => $this->option_id,
					'label'   => __( 'Datetime', 'file-renaming-on-upload' ),
					'desc'    => __( 'Enables Datetime rule', 'file-renaming-on-upload' ) . ' - ' . '<strong>{' . $this->option_id . '}</strong>',
					'type'    => 'checkbox',
					'default' => 'no',
				),

				array(
					'name'        => $this->option_datetime_format,
					'desc'        => __( 'Datetime format ', 'file-renaming-on-upload' ) . sprintf( __( 'You can see more formats <a target="_blank" href="%s">here</a>', 'file-renaming-on-upload' ), 'http://php.net/manual/function.date.php' ) . '<br />' . sprintf( __( 'Result: <b>%s</b>', 'file-renaming-on-upload' ), $format ),
					'type'        => 'text',
					'default'     => 'Y-m-d_H-i-s_u',
					'placeholder' => 'Y-m-d_H-i-s_u',
				),
				array(
					'name' => 'datetime_separator',
					'type' => 'separator',
				),

			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}