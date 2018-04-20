<?php
/**
 * File renaming on upload - Permalink update Option
 *
 * @version 2.2.8
 * @since   2.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options\General;

use FROU\Options\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\General\Permalink_Update_Option' ) ) {
	class Permalink_Update_Option extends Option {

		public $current_filename_modified;
		public $current_filename_original;

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
			$this->option_id = 'update_permalink';
		}

		/**
		 * Initializes
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 */
		function init() {
			parent::init();
			add_filter( 'sanitize_file_name', array( $this, 'sanitize_filename_before' ), 9 );
			add_filter( 'sanitize_file_name', array( $this, 'sanitize_filename_after' ), PHP_INT_MAX );
			add_action( 'add_attachment', array( $this, 'add_attachment' ), PHP_INT_MAX );
		}

		/**
		 * Gets original filename when a file is uploaded
		 *
		 * @param $filename
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 * @return mixed
		 */
		public function sanitize_filename_after( $filename ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename;
			}

			$info                            = pathinfo( $filename );
			$filename_original               = $info['filename'];
			$this->current_filename_modified = $filename_original;

			return $filename;
		}

		/**
		 * Gets the modified filename when a file is uploaded and the plugin has done its work
		 *
		 * @param $filename
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 * @return mixed
		 */
		public function sanitize_filename_before( $filename ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename;
			}

			$info                            = pathinfo( $filename );
			$filename_original               = $info['filename'];
			$this->current_filename_original = $filename_original;

			return $filename;
		}

		/**
		 * After a file is uploaded, make its name unique
		 *
		 * @version 2.2.8
		 * @since   2.0.0
		 *
		 * @param $post_id
		 */
		public function add_attachment( $post_id ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return;
			}
			$post = get_post( $post_id );
			if ( $post->post_type != 'attachment' ) {
				return;
			}
			$unique_slug     = wp_unique_post_slug( $this->current_filename_modified, $post->ID, $post->post_status, $post->post_type, $post->post_parent );
			$post->post_name = $unique_slug;
			wp_update_post( $post );
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
					'name'    => $this->option_id,
					'label'   => __( 'Update permalink', 'file-renaming-on-upload' ),
					'desc'    => __( 'Updates attachment permalink following the filename structure', 'file-renaming-on-upload' ),
					'default' => 'off',
					'type'    => 'checkbox',
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}