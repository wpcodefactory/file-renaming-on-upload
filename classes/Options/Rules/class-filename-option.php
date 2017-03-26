<?php
/**
 * File renaming on upload - Remove Characters Option
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options\Rules;

use FROU\Options\Option;
use FROU\Options\Rule_Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Rules\Filename_Option' ) ) {
	class Filename_Option extends Rule_Option {

		// Convert
		public $option_convert_accents = 'filename_convert_accents';
		public $option_convert_lowercase = 'filename_convert_lowercase';

		// Remove
		public $option_remove_characters = 'filename_remove_chars';
		public $option_remove_characters_text = 'filename_remove_chars_text';

		function __construct( array $args = array() ) {
			parent::__construct( $args );
			$this->option_id = 'filename';
		}

		function init() {
			parent::init();
			add_filter( 'frou_sanitize_file_name', array( $this, 'convert_accents' ), 11 );
			add_filter( 'frou_sanitize_file_name', array( $this, 'convert_lowercase' ), 11 );
			add_action( 'sanitize_file_name_chars', array( $this, 'remove_chars' ) );
		}

		public function remove_chars( $chars ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $chars;
			}

			if ( ! filter_var( $this->get_option( $this->option_remove_characters ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $chars;
			}

			$chars_to_remove = sanitize_text_field( $this->get_option( $this->option_remove_characters_text, 'frou_remove_opt' ) );
			$chars           = explode( " ", $chars_to_remove );

			return $chars;
		}

		public function convert_lowercase( $filename_infs ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			if ( ! filter_var( $this->get_option( $this->option_convert_lowercase, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			$filename                                              = $filename_infs['structure']['translation']['filename'];
			$filename_infs['structure']['translation']['filename'] = strtolower( $filename );

			return $filename_infs;
		}

		public function convert_accents( $filename_infs ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			if ( ! filter_var( $this->get_option( $this->option_convert_accents, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			$filename                                              = $filename_infs['structure']['translation']['filename'];
			$filename_infs['structure']['translation']['filename'] = remove_accents( $filename );

			return $filename_infs;
		}

		public function add_fields( $fields, $section ) {
			$new_options = array(
				array(
					'name'    => $this->option_id,
					'label'   => __( 'Filename', 'file-renaming-on-upload' ),
					'desc'    => __( 'Enables filename rule', 'file-renaming-on-upload' ) . ' - ' . '<strong>{' . $this->option_id . '}</strong>',
					'type'    => 'checkbox',
					'default' => 'on',
				),

				array(
					'name'    => $this->option_convert_accents,
					//'label'   => __( 'Accents', 'file-renaming-on-upload' ),
					'desc'    => __( 'Converts all accent characters from filename to ASCII characters', 'file-renaming-on-upload' ),
					'type'    => 'checkbox',
					'default' => 'on',
				),
				array(
					'name'    => $this->option_convert_lowercase,
					//'label'   => __( 'Lowercase', 'file-renaming-on-upload' ),
					'desc'    => __( 'Converts all characters from filename to lowercase', 'file-renaming-on-upload' ),
					'type'    => 'checkbox',
					'default' => 'on',
				),
				array(
					'name'    => $this->option_remove_characters,
					//'label'   => __( 'Characters', 'file-renaming-on-upload' ),
					'desc'    => __( 'Removes characters from filename', 'file-renaming-on-upload' ),
					'type'    => 'checkbox',
					'default' => 'on',
				),
				array(
					'name'    => $this->option_remove_characters_text,
					//'desc'    => __( 'Characters to be removed (space separated)', 'file-renaming-on-upload' ),
					'type'    => 'textarea',
					'default' => '? + [ ] / \ = < > : ; , \' " & $ # * ( ) | ~ ` ! { } ¨ % @ ^',
				),
				array(
					'name' => 'filename_separator',
					'type' => 'separator',
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}