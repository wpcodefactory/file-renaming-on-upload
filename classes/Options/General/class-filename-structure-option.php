<?php
/**
 * File renaming on upload - Filename structure option.
 *
 * @version 2.4.8
 * @since   2.0.0
 * @author  WPFactory
 */

namespace FROU\Options\General;

use FROU\Options\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\General\Filename_Structure_Option' ) ) {
	class Filename_Structure_Option extends Option {

		public $option_characters_between = 'filename_structure_chars_between';
		public $option_max_length = 'filename_structure_max_length';

		/**
		 * Initializes.
		 *
		 * @version 2.4.8
		 * @since   2.0.0
		 */
		function init() {
			parent::init();
			add_filter( 'frou_sanitize_file_name', array( $this, 'frou_sanitize_file_name' ), 1 );
			add_filter( 'frou_structure_rules_list', array( $this, 'create_list_with_structure_rules' ) );
			add_filter( 'frou_after_sanitize_file_name', array( $this, 'truncate_filename' ) );
		}

		/**
		 * truncate_filename.
		 *
		 * @version 2.4.8
		 * @since   2.4.8
		 *
		 * @param $filename
		 *
		 * @return bool|string
		 */
		function truncate_filename( $filename ) {
			if ( ! empty( (int) $max_length = $this->get_option( $this->option_max_length, 200 ) ) ) {
				$filename = substr( $filename, 0, $max_length );
			}
			return $filename;
		}

		/**
		 * Constructor.
		 *
		 * @version 2.3.6
		 * @since   2.0.0
		 *
		 * @param array $args
		 */
		function __construct( array $args = array() ) {
			$this->option_id = 'filename_structure';
			parent::__construct( $args );
		}

		/**
		 * Creates a list with available filename structure rules.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param $list
		 *
		 * @return string
		 */
		public function create_list_with_structure_rules( $list ) {
			$list = apply_filters( 'frou_structure_rules', array() );

			return
				'
			<ul style="list-style:inside">
				<li><strong>{' . implode( '}</strong></li><li><strong>{', $list ) . '}</strong></li>
			</ul>
			';
		}

		/**
		 * Puts what the user wants as rules inside the filter "frou_structure_rules_list".
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 *
		 * @return mixed
		 */
		public function frou_sanitize_file_name( $filename_infs ) {
			$filename_infs['structure']['rules']     = sanitize_text_field( $this->get_option( $this->option_id ) );
			$filename_infs['structure']['separator'] = sanitize_text_field( $this->get_option( $this->option_characters_between ) );
			return $filename_infs;
		}

		/**
		 * Adds settings fields.
		 *
		 * @version 2.4.8
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
					'label'   => __( 'Filename structure', 'file-renaming-on-upload' ),
					'desc'    => __( 'Manages the filename structure using rules. If you want only the original filename, leave it as <b>{filename}.</b>', 'file-renaming-on-upload' ) . ' ' . __( 'Rules available:', 'file-renaming-on-upload' ) . apply_filters( 'frou_structure_rules_list', '' ),
					'default' => '{siteurl}{posttitle}{filename}',
					'type'    => 'text',
				),
				array(
					'name'    => $this->option_characters_between,
					'desc'    => __( 'Character(s) used to separate rules', 'file-renaming-on-upload' ),
					'size'    => 'small',
					'default' => '-',
					'type'    => 'text',
				),
				array(
					'name'    => $this->option_max_length,
					'desc'    => __( 'Max length', 'file-renaming-on-upload' ),
					'size'    => 'small',
					'default' => 200,
					'type'    => 'number',
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}