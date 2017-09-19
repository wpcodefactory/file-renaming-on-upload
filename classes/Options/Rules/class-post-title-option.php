<?php
/**
 * File renaming on upload - Filename Option
 *
 * @version 2.0.0
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


if ( ! class_exists( 'FROU\Options\Rules\Post_Title_Option' ) ) {
	class Post_Title_Option extends Rule_Option {

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
			$this->option_id = 'posttitle';
		}

		/**
		 * Initializes
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 */
		function init() {
			parent::init();
			//add_action( 'add_attachment', array( $this, 'add_attachment' ) );
			add_filter( 'frou_sanitize_file_name', array( $this, 'frou_sanitize_file_name' ), 11 );
		}



		/**
		 * Inserts post title on 'frou_sanitize_file_name' filter
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param $filename_infs
		 *
		 * @return mixed
		 */
		public function frou_sanitize_file_name( $filename_infs ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			$structure_rules = $filename_infs['structure']['rules'];
			if ( strpos( $structure_rules, '{' . $this->option_id . '}' ) !== false ) {
				$post_slug = Post::get_parent_post_slug();
				if ( ! empty( $post_slug ) ) {
					$filename_infs['structure']['translation'][ $this->option_id ] = $post_slug;
				}
			}

			return $filename_infs;
		}

		/**
		 * Adds settings fields
		 *
		 * @version 2.0.0
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
					'name'      => $this->option_id,
					'label'     => __( 'Post title', 'file-renaming-on-upload' ),
					'desc'      => __( 'Enables post title rule', 'file-renaming-on-upload' ) . ' - ' . '<strong>{' . $this->option_id . '}</strong>',
					'desc_secondary' => __( 'Adds post title whenever it is possible', 'file-renaming-on-upload' ),
					'type'      => 'checkbox',
					'default'   => 'on',
				),
				array(
					'name' => 'posttitle_separator',
					'type' => 'separator',
				),

			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}