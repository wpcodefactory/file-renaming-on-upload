<?php
/**
 * File renaming on upload - Enable Option
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options\Convert;

use FROU\Options\Option;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Convert\Lowercase_Option' ) ) {
	class Lowercase_Option extends Option {

		const OPTION_LOWERCASE = 'lowercase';

		function init() {
			parent::init();
			add_filter( 'frou_sanitize_file_name', array( $this, 'frou_sanitize_file_name' ), 11 );
		}

		public function frou_sanitize_file_name( $filename_infs ) {
			if ( ! filter_var( $this->get_option( self::OPTION_LOWERCASE, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			$filename                                              = $filename_infs['structure']['translation']['filename'];
			$filename_infs['structure']['translation']['filename'] = strtolower( $filename );

			return $filename_infs;
		}

		public function add_fields( $fields, $section ) {
			$new_options = array(
				array(
					'name'    => self::OPTION_LOWERCASE,
					'label'   => __( 'Lowercase', 'file-renaming-on-upload' ),
					'desc'    => __( 'Converts all characters to lowercase', 'file-renaming-on-upload' ),
					'type'    => 'checkbox',
					'default' => 'on',
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}