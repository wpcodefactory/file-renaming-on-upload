<?php
/**
 * File renaming on upload - Gutenberg Title Fix Option.
 *
 * @version 2.4.1
 * @since   2.4.1
 * @author  WPFactory
 */

namespace FROU\Options\General;

use FROU\Options\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\General\Gutenberg_Title_Fix_Option' ) ) {
	class Gutenberg_Title_Fix_Option extends Option {

		/**
		 * Initializes.
		 *
		 * @version 2.4.1
		 * @since   2.4.1
		 */
		function init() {
			parent::init();
			add_filter( 'rest_pre_insert_' . 'attachment', array( $this, 'fix_gutenberg_upload_empty_title' ), 10, 2 );
		}

		/**
		 * fix_gutenberg_upload_empty_post_title.
		 *
		 * @version 2.4.1
		 * @since   2.4.1
		 *
		 * @param $prepared_post
		 * @param $request
		 *
		 * @return mixed
		 */
		function fix_gutenberg_upload_empty_title( $prepared_post, $request ) {
			if (
				filter_var( $this->get_option( $this->option_id, false ), FILTER_VALIDATE_BOOLEAN )
				&& ( ! property_exists( $prepared_post, 'post_title' ) || empty( $prepared_post->post_title ) )
				&& isset( $request->get_file_params()['file']['name'] )
				&& $name = $request->get_file_params()['file']['name']
			) {
				$filetype = wp_check_filetype( $name );
				if ( ! empty( $filetype['ext'] ) ) {
					$title = str_replace( '.' . $filetype['ext'], '', $name );
				} else {
					$title = $name;
				}
				if ( trim( $title ) && ! is_numeric( sanitize_title( $title ) ) ) {
					$prepared_post->post_title = $title;
				}
			}
			return $prepared_post;
		}

		/**
		 * Constructor.
		 *
		 * @version 2.4.1
		 * @since   2.4.1
		 *
		 * @param array $args
		 */
		function __construct( array $args = array() ) {
			parent::__construct( $args );
			$this->option_id = 'gb_title_fix';
		}

		/**
		 * Adds settings fields.
		 *
		 * @version 2.4.1
		 * @since   2.4.1
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
					'label'           => __( 'Gutenberg title fix', 'file-renaming-on-upload' ),
					'desc'            => __( 'Fix empty title on Gutenberg attachment upload', 'file-renaming-on-upload' ), 
					'desc_secondary'  => __( 'The attachment uploaded from Gutenberg editor will have the post title set from the original filename.', 'file-renaming-on-upload' ),
					'default'         => 'off',
					'type'            => 'checkbox',
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}