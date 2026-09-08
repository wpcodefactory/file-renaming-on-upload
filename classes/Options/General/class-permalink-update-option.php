<?php
/**
 * File renaming on upload - Permalink update Option.
 *
 * @version 2.7.0
 * @since   2.0.0
 * @author  WPFactory
 */

namespace FROU\Options\General;

use FROU\Options\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\General\Permalink_Update_Option' ) ) {
	class Permalink_Update_Option extends Option {

		/**
		 * current_filename_modified.
		 *
		 * @since 1.0.0
		 *
		 * @var
		 */
		public $current_filename_modified;

		/**
		 * current_filename_original.
		 *
		 * @since 1.0.0
		 *
		 * @var
		 */
		public $current_filename_original;

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
			$this->option_id = 'update_permalink';
		}

		/**
		 * Initializes.
		 *
		 * @version 2.7.0
		 * @since   2.0.0
		 */
		function init() {
			parent::init();
			add_filter( 'frou_before_sanitize_file_name', array( $this, 'sanitize_filename_before' ), 9, 2 );
			add_filter( 'frou_after_sanitize_file_name', array( $this, 'sanitize_filename_after' ), PHP_INT_MAX, 2 );
			add_action( 'add_attachment', array( $this, 'add_attachment' ), PHP_INT_MAX );
		}

		/**
		 * Gets the modified filename when a file is uploaded and the plugin has done its work.
		 *
		 * @param $filename
		 * @param $info
		 *
		 * @version 2.7.0
		 * @since   2.0.0
		 * @return mixed
		 */
		public function sanitize_filename_after( $filename, $info ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename;
			}

			$this->current_filename_modified = $filename;

			return $filename;
		}

		/**
		 * Gets the original filename when a file is uploaded.
		 *
		 * @param $filename
		 * @param $info
		 *
		 * @version 2.7.0
		 * @since   2.0.0
		 * @return mixed
		 */
		public function sanitize_filename_before( $filename, $info ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename;
			}

			$this->current_filename_original = $info['filename'];

			return $filename;
		}

		/**
		 * After a file is uploaded, make its name unique.
		 *
		 * @version 2.7.0
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
			if ( empty( $this->current_filename_modified ) ) {
				return;
			}
			$unique_slug     = wp_unique_post_slug( $this->current_filename_modified, $post->ID, $post->post_status, $post->post_type, $post->post_parent );
			$post->post_name = $unique_slug;
			//do_action('frou_update_post_before', $post_id);
			wp_update_post( $post );
			//do_action('frou_update_post_after', $post_id);
		}

		/**
		 * Adds settings fields.
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
					'name'            => $this->option_id,
					'label'           => __( 'Update permalink', 'file-renaming-on-upload' ),
					'desc'            => __( 'Update permalinks from new added attachment based on the filename structure', 'file-renaming-on-upload' ),					
					'default'         => 'off',
					'type'            => 'checkbox',
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}