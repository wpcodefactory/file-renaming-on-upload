<?php
/**
 * File renaming on upload - Filename Option.
 *
 * @version 2.4.9
 * @since   2.0.0
 * @author  WPFactory
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
		/**
		 * option_convert.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $option_convert = 'convert';

		/**
		 * option_convert_accents.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $option_convert_accents = 'accents';

		/**
		 * option_convert_lowercase.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $option_convert_lowercase = 'lowercase';

		/**
		 * option_convert_posttitle.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $option_convert_posttitle = 'posttitle';

		/**
		 * option_convert_to_dash_chars.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $option_convert_to_dash_chars = 'converttodash_chars';

		/**
		 * option_accent_conversion_method.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $option_accent_conversion_method = 'accent_conversion_method';

		/**
		 * option_remove.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $option_remove = 'remove';

		/**
		 * option_remove_specific_characters.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $option_remove_specific_characters = 'specific_chars';

		/**
		 * option_remove_specific_characters_text.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $option_remove_specific_characters_text = 'specific_chars_text';

		/**
		 * option_remove_non_ascii_characters.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $option_remove_non_ascii_characters = 'non_ascii_chars';

		// Truncate
		public $option_truncate = 'truncate';

		//public $current_filename_modified;
		//public $current_filename_original;

		/**
		 * Constructor.
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
		 * Initializes.
		 *
		 * @version 2.4.9
		 * @since   2.0.0
		 */
		function init() {
			parent::init();
			add_filter( 'frou_sanitize_file_name', array( $this, 'convert_post_title' ), 11 );
			add_filter( 'frou_sanitize_file_name', array( $this, 'convert_accents' ), 12 );
			add_filter( 'frou_sanitize_file_name', array( $this, 'convert_lowercase' ), 12 );
			add_filter( 'frou_sanitize_file_name', array( $this, 'truncate_filename' ), 30 );
			add_filter( 'frou_sanitize_file_name', array( $this, 'remove_non_ascii_chars' ), 13 );
			add_action( 'sanitize_file_name_chars', array( $this, 'remove_specific_chars' ) );
			add_filter( 'frou_sanitize_file_name', array( $this, 'convert_to_dash' ) );
		}

		/**
		 * Truncates filename.
		 *
		 * @version 2.2.5
		 * @since   2.2.4
		 */
		public function truncate_filename( $filename_infs ) {
			$truncate_option = $this->get_option( $this->option_truncate );
			if ( empty( $truncate_option ) ) {
				return $filename_infs;
			}

			$max_length = $this->get_option( $this->option_truncate );

			$filename                                              = $filename_infs['structure']['translation']['filename'];
			$filename_shortened                                    = substr( $filename, 0, $max_length );
			$filename_infs['structure']['translation']['filename'] = $filename_shortened;

			return $filename_infs;
		}

		/**
		 * Convert characters to dash.
		 *
		 * @version 2.4.9
		 * @since   2.0.0
		 * @param $filename_infs
		 *
		 * @return mixed
		 */
		public function convert_to_dash( $filename_infs ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}
			$option    = $this->get_option( $this->option_convert_to_dash_chars );
			$chars     = sanitize_text_field( $option );
			$chars_arr = explode( " ", $chars );
			if ( ! is_array( $chars_arr ) || count( $chars_arr ) == 0 ) {
				return $filename_infs;
			}
			$filename_infs['structure']['translation']['filename'] = str_replace( $chars_arr, '-', $filename_infs['structure']['translation']['filename'] );
			return $filename_infs;
		}

		/**
		 * Removes non english chars from filename.
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
		 * Removes specific chars from filename.
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
		 * Converts filename to posttitle.
		 *
		 * @version 2.0.9
		 * @since   2.0.0
		 *
		 * @param $filename_infs
		 *
		 * @return mixed
		 */
		public function convert_post_title( $filename_infs ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			$convert = $this->get_option( $this->option_convert );
			if ( ! is_array( $convert ) || ! in_array( $this->option_convert_posttitle, $convert ) ) {
				return $filename_infs;
			}

			$post_slug = Post::get_parent_post_title();
			if ( empty( $post_slug ) ) {
				return $filename_infs;
			}

			$filename_infs['structure']['translation']['filename'] = $post_slug;

			return $filename_infs;
		}

		/**
		 * Converts filename to lowercase.
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
		 * Converts all accent characters from filename to ASCII characters.
		 *
		 * @version 2.4.2
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
			$filename = $filename_infs['structure']['translation']['filename'];
			$function = $this->get_option( $this->option_accent_conversion_method, 'remove_accents' );
			if ( function_exists( $function ) ) {
				$function_info = array(
					'remove_accents'               => array( 'params' => array( $filename ) ),
					'transliterator_transliterate' => array( 'params' => array( 'Any-Latin; Latin-ASCII;', $filename ) ),
				);
				$filename      = call_user_func_array( $this->get_option( $this->option_accent_conversion_method, 'remove_accents' ), $function_info[ $function ]['params'] );
			}
			$filename_infs['structure']['translation']['filename'] = $filename;
			return $filename_infs;
		}

		/**
		 * Adds settings fields.
		 *
		 * @version 2.4.3
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
					'default' => __('Convert','file-renaming-on-upload'),
				),
				array(
					'name'    => $this->option_convert,
					'type'    => 'multicheck',
					'default' => array(
						$this->option_convert_accents   => $this->option_convert_accents,
						$this->option_convert_lowercase => $this->option_convert_lowercase,
					),
					'options' => array(
						$this->option_convert_accents   => '<strong>' . __( 'Characters', 'file-renaming-on-upload' ) . '</strong>' . ' - ' . __( 'Converts special characters by their ASCII replacement, fixing accents and some other special characters', 'file-renaming-on-upload' ),
						$this->option_convert_lowercase => '<strong>' . __( 'Lowercase', 'file-renaming-on-upload' ) . '</strong>' . ' - ' . __( 'Converts all filename characters to lowercase', 'file-renaming-on-upload' ),
						$this->option_convert_posttitle => '<strong>' . __( 'Post title', 'file-renaming-on-upload' ) . '</strong>' . ' - ' . __( 'Converts filename to post title whenever it is possible', 'file-renaming-on-upload' ),
					),
				),
				array(
					'name'    => $this->option_convert_to_dash_chars,
					'desc'    => __( 'Besides whitespaces, converts the following characters to a dash', 'file-renaming-on-upload' ) . ' ' . __( '(space separated).', 'file-renaming-on-upload' ),
					'type'    => 'text',
					'default' => '_',
				),
				array(
					'name'    => $this->option_accent_conversion_method,
					'desc'    => __( 'Character conversion method.', 'file-renaming-on-upload' ),
					'default' => 'remove_accents',
					'options' => array(
						'remove_accents'               => __( 'Remove Accents: remove_accents()', 'file-renaming-on-upload' ),
						'transliterator_transliterate' => sprintf( __( 'Transliterator: transliterate()%s', 'file-renaming-on-upload' ), ! function_exists( 'transliterator_transliterate' ) ? ' - ' . __( 'Disabled on the server', 'file-renaming-on-upload' ) : '' )
					),
					'type'    => 'select',
				),
				array(
					'name'    => 'remove_chars_title',
					'type'    => 'title',
					'default' => __('Remove','file-renaming-on-upload'),
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
					'name'    => 'truncate_title',
					'type'    => 'title',
					'default' => __('Truncate','file-renaming-on-upload'),
				),
				array(
					'name'    => $this->option_truncate,
					'desc'    => __( 'Max length of a filename. Leave it empty if you do not want to use this feature.', 'file-renaming-on-upload' ),
					'default' => '',
					'min'     => 0,
					'step'    => '1',
					'type'    => 'number',
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