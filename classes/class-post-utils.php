<?php
/**
 * File renaming on upload - WordPress Post Utils.
 *
 * @version 2.6.1
 * @since   2.6.1
 * @author  WPFactory
 */

namespace FROU;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Post_Utils' ) ) {
	class Post_Utils {

		/**
		 * $current_media_post.
		 *
		 * @since 2.6.1
		 *
		 * @var null
		 */
		protected $current_media_post = null;

		/**
		 * $current_media_post_id.
		 *
		 * @since 2.6.1
		 *
		 * @var null
		 */
		protected $current_media_post_id = null;

		/**
		 * Initializes the class.
		 *
		 * @version 2.6.1
		 * @since   2.6.1
		 */
		public function init() {
			add_action( 'init', array( $this, 'get_current_media_post_id' ) );
			add_action( 'init', array( $this, 'get_current_media_post' ) );

			// PlUpload.
			add_filter( 'plupload_default_settings', array( $this, 'add_current_media_post_id_to_plupload' ) );
			add_filter( 'plupload_default_params', array( $this, 'add_current_media_post_id_to_plupload' ) );
		}

		/**
		 * add_current_media_post_id_to_plupload.
		 *
		 * @version 2.6.1
		 * @since   2.6.1
		 *
		 * @param $settings
		 *
		 * @return mixed
		 */
		function add_current_media_post_id_to_plupload( $settings ) {
			$post_id = $this->get_current_media_post_id();
			if ( ! empty( $post_id ) ) {
				$settings['frou_query_string_post_id'] = $post_id;
			}

			return $settings;
		}

		/**
		 * get_current_media_post_id.
		 *
		 * @version 2.6.1
		 * @since   2.6.1
		 *
		 * @return int|mixed|null
		 */
		function get_current_media_post_id() {
			if ( null === $this->current_media_post_id ) {
				$this->current_media_post_id = $this->get_post_id_from_query_string();
				if ( empty( $this->current_media_post_id ) ) {
					global $post;
					if ( ! empty( $post ) && is_a( $post, 'WP_Post' ) ) {
						$this->current_media_post_id = $post->ID;
					}
				}
			}

			return apply_filters( 'frou_current_media_post_id', $this->current_media_post_id );
		}

		/**
		 * get_current_media_post.
		 *
		 * @version 2.6.1
		 * @since   2.6.1
		 *
		 * @return array|false|mixed|\WP_Post|null
		 */
		function get_current_media_post() {
			if ( null === $this->current_media_post ) {
				$current_post_id          = $this->get_current_media_post_id();
				$this->current_media_post = get_post( $current_post_id );
				if ( ! is_a( $this->current_media_post, 'WP_Post' ) ) {
					$this->current_media_post = false;
				}
			}

			return $this->current_media_post;
		}

		/**
		 * get_media_post_slug.
		 *
		 * @version 2.6.1
		 * @since   2.6.1
		 *
		 * @param $the_post
		 *
		 * @return mixed|null
		 */
		function get_media_post_slug( $the_post = null ) {
			$post_title = $this->get_media_post_title( $the_post );
			$post_slug  = '';
			if ( ! empty( $post_title ) ) {
				$url_decoded   = urldecode( $post_title );
				$new_post_name = remove_accents( $url_decoded );
				$new_post_name = preg_replace( "/[^a-zA-Z0-9-_.\s]/", "", $new_post_name );
				$post_slug     = sanitize_title( $new_post_name );
			}

			return apply_filters( 'frou_get_media_post_slug', $post_slug );
		}

		/**
		 * get_media_post_title.
		 *
		 * @version 2.6.1
		 * @since   2.6.1
		 *
		 * @param $the_post
		 *
		 * @return mixed|null
		 */
		function get_media_post_title( $the_post = null ) {
			if ( empty( $the_post ) ) {
				$the_post = $this->get_current_media_post();
			}
			$post_title = '';
			$info = array();
			if ( ! empty( $the_post ) && is_a( $the_post, 'WP_Post' ) ) {
				$url_decoded        = urldecode( $the_post->post_title );
				$new_post_name      = remove_accents( $url_decoded );
				$new_post_name      = preg_replace( "/[^a-zA-Z0-9-_.\s]/", "", $new_post_name );
				$post_title         = sanitize_title( $new_post_name );
				$info['raw_string'] = $the_post->post_title;
				$info['object']     = $the_post;
			}

			return apply_filters( 'frou_get_media_post_title', $post_title, $info );
		}

		/**
		 * get_post_id_from_query_string.
		 *
		 * @version 2.6.1
		 * @since   2.6.1
		 *
		 * @return mixed
		 */
		function get_post_id_from_query_string() {
			$post_id_possibilities = apply_filters( 'frou_post_id_query_string_params', array( 'post_id', 'post_ID', 'post', 'product_id', 'frou_query_string_post_id' ) );
			$post_id               = null;
			foreach ( $post_id_possibilities as $key ) {
				if ( isset( $_REQUEST[ $key ] ) ) {
					$post_id = $_REQUEST[ $key ];
					break;
				}
			}

			return filter_var( $post_id, FILTER_SANITIZE_NUMBER_INT );
		}
	}
}