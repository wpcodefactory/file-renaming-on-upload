<?php
/**
 * File renaming on upload - Filename Option
 *
 * @version 2.1.1
 * @since   2.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options\Rules;

use FROU\Options\Option;
use FROU\Options\Rule_Option;
use FROU\WordPress\Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Rules\Filename_Option' ) ) {
	class Filename_Option extends Rule_Option {

		// Convert
		public $option_convert = 'convert';
		public $option_convert_accents = 'accents';
		public $option_convert_lowercase = 'lowercase';
		public $option_convert_posttitle = 'posttitle';

		// Remove
		public $option_remove = 'remove';
		public $option_remove_specific_characters = 'specific_chars';
		public $option_remove_specific_characters_text = 'specific_chars_text';
		public $option_remove_non_ascii_characters = 'non_ascii_chars';

		//public $current_filename_modified;
		//public $current_filename_original;

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
			$this->option_id = 'filename';
		}

		/**
		 * Initializes
		 *
		 * @version 2.0.9
		 * @since   2.0.0
		 */
		function init() {
			parent::init();
			add_filter( 'frou_sanitize_file_name', array( $this, 'convert_posttitle' ), 11 );
			add_filter( 'frou_sanitize_file_name', array( $this, 'convert_accents' ), 12 );
			add_filter( 'frou_sanitize_file_name', array( $this, 'convert_lowercase' ), 12 );
			add_filter( 'frou_sanitize_file_name', array( $this, 'remove_non_ascii_chars' ), 13 );
			add_action( 'sanitize_file_name_chars', array( $this, 'remove_specific_chars' ) );
		}

		/**
		 * Removes non english chars from filename
		 *
		 * @version 2.1.1
		 * @since   2.0.0
		 *
		 * @param $chars
		 *
		 * @return array
		 */
		public function remove_non_ascii_chars( $filename_infs ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			$remove = $this->get_option( $this->option_remove );
			if ( empty( $remove ) ) {
				return $filename_infs;
			}

			if ( $remove != $this->option_remove_non_ascii_characters ) {
				return $filename_infs;
			}

			$filename                                              = $filename_infs['structure']['translation']['filename'];
			$filename                                              = preg_replace( "/[^a-zA-Z0-9-_.]/", "", $filename );
			$filename_infs['structure']['translation']['filename'] = $filename;

			return $filename_infs;
		}

		/**
		 * Removes specific chars from filename
		 *
		 * @version 2.0.9
		 * @since   2.0.0
		 *
		 * @param $chars
		 *
		 * @return array
		 */
		public function remove_specific_chars( $chars ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $chars;
			}

			$remove = $this->get_option( $this->option_remove );
			if ( empty( $remove ) ) {
				return $chars;
			}

			if ( $remove != $this->option_remove_specific_characters ) {
				return $chars;
			}

			$chars_to_remove = sanitize_text_field( $this->get_option( $this->option_remove_specific_characters_text, 'frou_remove_opt' ) );
			$chars           = explode( " ", $chars_to_remove );

			return $chars;
		}

		/**
		 * Converts filename to posttitle
		 *
		 * @version 2.0.9
		 * @since   2.0.0
		 *
		 * @param $filename_infs
		 *
		 * @return mixed
		 */
		public function convert_posttitle( $filename_infs ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			$convert = $this->get_option( $this->option_convert );
			if ( ! is_array( $convert ) || ! in_array( $this->option_convert_posttitle, $convert ) ) {
				return $filename_infs;
			}

			$post_slug = Post::get_post_slug();
			if ( empty( $post_slug ) ) {
				return $filename_infs;
			}

			$filename_infs['structure']['translation']['filename'] = $post_slug;

			return $filename_infs;
		}

		/**
		 * Converts filename to lowercase
		 *
		 * @version 2.0.9
		 * @since   2.0.0
		 *
		 * @param $filename_infs
		 *
		 * @return mixed
		 */
		public function convert_lowercase( $filename_infs ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			$convert = $this->get_option( $this->option_convert );
			if ( ! is_array( $convert ) || ! in_array( $this->option_convert_lowercase, $convert ) ) {
				return $filename_infs;
			}

			$filename                                              = $filename_infs['structure']['translation']['filename'];
			$filename_infs['structure']['translation']['filename'] = strtolower( $filename );

			return $filename_infs;
		}

		/**
		 * Converts all accent characters from filename to ASCII characters
		 *
		 * @version 2.0.9
		 * @since   2.0.0
		 *
		 * @param $filename_infs
		 *
		 * @return mixed
		 */
		public function convert_accents( $filename_infs ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			$convert = $this->get_option( $this->option_convert );
			if ( ! is_array( $convert ) || ! in_array( $this->option_convert_accents, $convert ) ) {
				return $filename_infs;
			}

			$filename                                              = $filename_infs['structure']['translation']['filename'];
			$filename                                              = remove_accents( $filename );
			$filename_infs['structure']['translation']['filename'] = $filename;

			return $filename_infs;
		}

		/**
		 * Adds settings fields
		 *
		 * @version 2.0.9
		 * @since   2.0.0
		 *
		 * @param $fields
		 * @param $section
		 *
		 * @return mixed
		 */
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
					'name'    => 'convert_chars_title',
					'type'    => 'title',
					'default' => 'Convert Characters',
				),
				array(
					'name'    => $this->option_convert,
					'type'    => 'multicheck',
					'default' => array(
						$this->option_convert_accents   => $this->option_convert_accents,
						$this->option_convert_lowercase => $this->option_convert_lowercase,
					),
					'options' => array(
						$this->option_convert_accents   => '<strong>' . __( 'Accents', 'file-renaming-on-upload' ) . '</strong>' . ' - ' . __( 'Converts all filename accent characters to ASCII characters', 'file-renaming-on-upload' ),
						$this->option_convert_lowercase => '<strong>' . __( 'Lowercase', 'file-renaming-on-upload' ) . '</strong>' . ' - ' . __( 'Converts all filename characters to lowercase', 'file-renaming-on-upload' ),
						$this->option_convert_posttitle => '<strong>' . __( 'Post title', 'file-renaming-on-upload' ) . '</strong>' . ' - ' . __( 'Converts filename to post title whenever it is possible', 'file-renaming-on-upload' ),
					),
				),
				array(
					'name'    => 'remove_chars_title',
					'type'    => 'title',
					'default' => 'Remove Characters',
				),
				array(
					'name'    => $this->option_remove,
					'type'    => 'radio',
					'default' => $this->option_remove_non_ascii_characters,
					'options' => array(
						$this->option_remove_non_ascii_characters => __( 'All non ASCII characters', 'file-renaming-on-upload' ),
						$this->option_remove_specific_characters  => __( 'Specific characters', 'file-renaming-on-upload' ),
					),
				),
				array(
					'name'    => $this->option_remove_specific_characters_text,
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