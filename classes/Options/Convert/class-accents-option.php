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


if ( ! class_exists( 'FROU\Options\Convert\Accents_Option' ) ) {
	class Accents_Option extends Option {

		const OPTION_ACCENTS = 'accents';

		function init() {
			parent::init();
			add_filter( 'frou_sanitize_file_name', array( $this, 'frou_sanitize_file_name' ), 11 );
		}

		public function frou_sanitize_file_name( $filename_infs ) {
			if ( ! filter_var( $this->get_option( self::OPTION_ACCENTS, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			$filename                                              = $filename_infs['structure']['translation']['filename'];
			$filename_infs['structure']['translation']['filename'] = remove_accents( $filename );

			return $filename_infs;
		}

		public function add_fields( $fields, $section ) {
			$new_options = array(
				array(
					'name'    => self::OPTION_ACCENTS,
					'label'   => __( 'Accents', 'file-renaming-on-upload' ),
					'desc'    => __( 'Converts all accent characters to ASCII characters', 'file-renaming-on-upload' ),
					'type'    => 'checkbox',
					'default' => 'on',
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}