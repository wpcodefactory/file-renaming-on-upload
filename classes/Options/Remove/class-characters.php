<?php
/**
 * File renaming on upload - Remove Characters Option
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options\Remove;

use FROU\Options\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Remove\Characters' ) ) {
	class Characters extends Option {

		const OPTION_CHARACTERS      = 'characters';
		const OPTION_CHARACTERS_TEXT = 'characters_text';

		function init() {
			parent::init();
			add_action( 'sanitize_file_name_chars', array( $this, 'sanitize_file_name_chars' ) );
		}

		public function sanitize_file_name_chars( $chars ) {
			$remove_chars = filter_var( $this->get_option( self::OPTION_CHARACTERS ), FILTER_VALIDATE_BOOLEAN );
			if ( $remove_chars ) {
				$chars_to_remove = sanitize_text_field( $this->get_option( self::OPTION_CHARACTERS_TEXT, 'frou_remove_opt' ) );
				$chars           = explode( " ", $chars_to_remove );
			}

			return $chars;
		}

		public function add_fields( $fields, $section ) {

			$new_options = array(
				array(
					'name'    => self::OPTION_CHARACTERS,
					'label'   => __( 'Characters', 'file-renaming-on-upload' ),
					'desc'    => __( 'Removes characters', 'file-renaming-on-upload' ),
					'type'    => 'checkbox',
					'default' => 'on',
				),
				array(
					'name'    => self::OPTION_CHARACTERS_TEXT,
					'desc'    => __( 'Characters to be removed (space separated)', 'file-renaming-on-upload' ),
					'type'    => 'textarea',
					'default' => '? + [ ] / \ = < > : ; , \' " & $ # * ( ) | ~ ` ! { } ¨ % @ ^',
				),
			);

			return array_merge( $fields, $new_options );

			return $fields;
		}
	}
}