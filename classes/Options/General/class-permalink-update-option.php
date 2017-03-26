<?php
/**
 * File renaming on upload - Enable Option
 *
 * @version 1.0.0
 * @since   1.0.0
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

		function __construct( array $args = array() ) {
			parent::__construct( $args );
			$this->option_id = 'update_permalink';
		}

		function init() {
			parent::init();
			add_filter( 'sanitize_file_name', array( $this, 'sanitize_filename_before' ), 9 );
			add_filter( 'sanitize_file_name', array( $this, 'sanitize_filename_after' ), PHP_INT_MAX );
			add_action( 'add_attachment', array( $this, 'add_attachment' ) );
		}

		public function sanitize_filename_after( $filename ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename;
			}

			$info                            = pathinfo( $filename );
			$filename_original               = $info['filename'];
			$this->current_filename_modified = $filename_original;

			return $filename;
		}

		public function sanitize_filename_before( $filename ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename;
			}

			$info                            = pathinfo( $filename );
			$filename_original               = $info['filename'];
			$this->current_filename_original = $filename_original;

			return $filename;
		}

		public function add_attachment( $post_id ) {
			$post = get_post( $post_id );
			if ( $post->post_type != 'attachment' ) {
				return;
			}
			$unique_slug     = wp_unique_post_slug( $this->current_filename_modified, $post->ID, $post->post_status, $post->post_type, $post->post_parent );
			$post->post_name = $unique_slug;
			wp_update_post( $post );
		}

		public function add_fields( $fields, $section ) {
			$new_options = array(
				array(
					'name'    => $this->option_id,
					'label'   => __( 'Update permalink', 'file-renaming-on-upload' ),
					'desc'    => __( 'Updates attachment permalink following filename rules', 'file-renaming-on-upload' ),
					'default' => 'off',
					'type'    => 'checkbox',
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}