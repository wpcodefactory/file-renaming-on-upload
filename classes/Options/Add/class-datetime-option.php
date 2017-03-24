<?php
/**
 * File renaming on upload - Enable Option
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options\Add;

use FROU\Options\Add_Option;
use FROU\Options\Option;
use FROU\Plugin_Core;
use FROU\WordPress\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Add\Datetime_Option' ) ) {
	class Datetime_Option extends Add_Option {

		const OPTION_DATETIME      = 'datetime';
		const OPTION_DATETIME_TEXT = 'datetime_text';

		function init() {
			parent::init();
			add_filter( 'frou_sanitize_file_name', array( $this, 'frou_sanitize_file_name' ), 11 );
		}

		public function add_structure_rule( $structure_rules ) {
			if ( ! filter_var( $this->get_option( self::OPTION_DATETIME, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $structure_rules;
			}
			$structure_rules[] = $this->structure_rule;

			return $structure_rules;
		}

		public function frou_sanitize_file_name( $filename_infs ) {
			if ( ! filter_var( $this->get_option( self::OPTION_DATETIME, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			$structure_rules = $filename_infs['structure']['rules'];
			if ( strpos( $structure_rules, '{' . $this->structure_rule . '}' ) !== false ) {
				$datetime                                                           = \DateTime::createFromFormat( 'U.u', microtime( true ) );
				$format                                                             = $datetime->format( $this->get_option( self::OPTION_DATETIME_TEXT, 'Y-m-d_H-i-s_u' ) );
				$filename_infs['structure']['translation'][ $this->structure_rule ] = $format;
			}

			return $filename_infs;
		}

		public function add_fields( $fields, $section ) {
			$datetime = \DateTime::createFromFormat( 'U.u', microtime( true ) );
			$format   = $datetime->format( $this->get_option( self::OPTION_DATETIME_TEXT, 'Y-m-d_H-i-s_u' ) );

			$new_options = array(
				array(
					'name'    => self::OPTION_DATETIME,
					'label'   => __( 'Datetime', 'file-renaming-on-upload' ),
					'desc'    => __( 'Inserts datetime', 'file-renaming-on-upload' ) . ' - ' . '<strong>{' . $this->structure_rule . '}</strong>',
					'type'    => 'checkbox',
					'default' => 'no',
				),

				array(
					'name'        => self::OPTION_DATETIME_TEXT,
					'desc'        => __( 'Datetime format. ', 'file-renaming-on-upload' ) . sprintf( __( 'You can see more formats <a target="_blank" href="%s">here</a>', 'file-renaming-on-upload' ), 'http://php.net/manual/function.date.php' ).'<br />'.sprintf( __( 'Result: <b>%s</b>', 'file-renaming-on-upload' ), $format ),
					'type'        => 'text',
					'default'     => 'Y-m-d_H-i-s_u',
					'placeholder' => 'Y-m-d_H-i-s_u',
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}