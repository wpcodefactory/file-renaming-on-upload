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

		//protected $current_media_post;

		protected $current_media_post = null;

		protected $current_media_post_id = null;

		/**
		 * WP_Post.
		 *
		 * @version 2.6.1
		 * @since   2.6.1
		 *
		 * @var \WP_Post
		 */
		//protected $media_wp_post;

		/**
		 * $query_string_post_id/
		 *
		 * @version 2.6.1
		 * @since   2.6.1
		 *
		 * @var int
		 */
		//protected $query_string_media_post_id;

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

			/*add_action( 'init', array( $this, 'store_query_string_media_post_id' ) );
			add_action( 'init', array( $this, 'store_global_media_wp_post' ) );

			// PlUpload.
			add_filter( 'plupload_default_settings', array( $this, 'add_media_post_id_to_plupload' ) );
			add_filter( 'plupload_default_params', array( $this, 'add_media_post_id_to_plupload' ) );*/
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
			if ( null !== $this->current_media_post_id ) {
				return $this->current_media_post_id;
			}
			$this->current_media_post_id = $this->get_post_id_from_query_string();
			if ( empty( $this->current_media_post_id ) ) {
				global $post;
				if ( ! empty( $post ) && is_a( $post, 'WP_Post' ) ) {
					$this->current_media_post_id = $post->ID;
				}
			}

			return $this->current_media_post_id;
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
			if ( null !== $this->current_media_post ) {
				return $this->current_media_post;
			}
			$current_post_id          = $this->get_current_media_post_id();
			$this->current_media_post = get_post( $current_post_id );
			if ( is_a( $this->current_media_post, 'WP_Post' ) ) {
				return $this->current_media_post;
			} else {
				return false;
			}
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
				//$info['raw_string'] = $the_post->post_title;
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
			if ( ! empty( $the_post ) && is_a( $the_post, 'WP_Post' ) ) {
				$url_decoded        = urldecode( $the_post->post_title );
				$new_post_name      = remove_accents( $url_decoded );
				$new_post_name      = preg_replace( "/[^a-zA-Z0-9-_.\s]/", "", $new_post_name );
				$post_title         = sanitize_title( $new_post_name );
				//$info['raw_string'] = $the_post->post_title;
				//$info['object']     = $the_post;
			}

			return apply_filters( 'frou_get_media_post_title', $post_title );
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

		/**
		 * add_media_post_id_to_plupload.
		 *
		 * @version 2.6.1
		 * @since   2.6.1
		 *
		 * @param $settings
		 *
		 * @return mixed
		 */
		/*function add_media_post_id_to_plupload( $settings ) {
			$post_id = $this->get_post_id_from_query_string();
			if ( ! empty( $post_id ) ) {
				$settings['frou_query_string_post_id'] = $post_id;
			}

			return $settings;
		}*/

		/**
		 * store_query_string_media_post_id.
		 *
		 * @version 2.6.1
		 * @since   2.6.1
		 *
		 * @return void
		 */
		/*function store_query_string_media_post_id() {
			$this->query_string_media_post_id = $this->get_post_id_from_query_string();
		}*/

		/**
		 * store_global_media_wp_post.
		 *
		 * @version 2.6.1
		 * @since   2.6.1
		 *
		 * @return void
		 */
		/*function store_global_media_wp_post() {
			$this->media_wp_post = $this->get_global_media_wp_post();
		}*/

		/**
		 * Returns a post object from the query string. If there isn't, gets it from global $post object.
		 *
		 * @version 2.6.1
		 * @since   2.6.1
		 *
		 * @return array|\WP_Post|null
		 */
		/*function get_media_wp_post_smart() {
			$post_id = $this->get_post_id_from_query_string();
			if ( ! empty( $post_id ) && is_a( $post = get_post( $post_id ), 'WP_Post' ) ) {
				return $post;
			} else {
				return $this->get_global_media_wp_post();
			}
		}*/

		/**
		 * Get gloam media wp post.
		 *
		 * @version 2.6.1
		 * @since   2.6.1
		 *
		 * @return array|\WP_Post|null
		 */
		/*function get_global_media_wp_post() {
			if ( ! empty( $this->media_wp_post ) ) {
				return $this->media_wp_post;
			}
			global $post;

			return $post;
		}*/

		/**
		 * get_post_id_from_query_string.
		 *
		 * @version 2.6.1
		 * @since   2.6.1
		 *
		 * @return int
		 */
		/*function get_post_id_from_query_string() {
			if ( ! empty( $this->query_string_media_post_id ) ) {
				return $this->query_string_media_post_id;
			}
			$post_id_possibilities = apply_filters( 'frou_post_id_query_string_params', array( 'post_id', 'post_ID', 'post', 'product_id', 'frou_query_string_post_id' ) );
			$post_id               = null;
			foreach ( $post_id_possibilities as $key ) {
				if ( isset( $_REQUEST[ $key ] ) ) {
					$post_id = $_REQUEST[ $key ];
					break;
				}
			}
			return filter_var( $post_id, FILTER_SANITIZE_NUMBER_INT );
		}*/
	}
}