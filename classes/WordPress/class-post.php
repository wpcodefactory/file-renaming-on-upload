<?php
/**
 * File renaming on upload - WordPress Post.
 *
 * @version 2.5.4
 * @since   2.0.0
 * @author  WPFactory
 */

namespace FROU\WordPress;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\WordPress\Post' ) ) {
	class Post {

		/**
		 * Get post slug
		 *
		 * @version 2.5.4
		 * @since   2.0.0
		 *
		 * @param null $post_obj
		 *
		 * @return string
		 */
		public static function get_parent_post_title( $post_obj = null ) {
			if ( $post_obj == null ) {
				$post_obj = self::get_parent_post();
			}
			$post_title = '';
			$info = array();
			if ( $post_obj != null ) {
				$url_decoded        = urldecode( $post_obj->post_title );
				$new_post_name      = remove_accents( $url_decoded );
				$new_post_name      = preg_replace( "/[^a-zA-Z0-9-_.\s]/", "", $new_post_name );
				$post_title         = sanitize_title( $new_post_name );
				$info['raw_string'] = $post_obj->post_title;
				$info['object']     = $post_obj;
			}
			return apply_filters( 'frou_get_parent_post_title', $post_title, $info );
		}

		/**
		 * Get parent post id
		 *
		 * @version 2.3.6
		 * @since   2.1.9
		 *
		 * @return null|\WP_Post
		 */
		public static function get_parent_post_id() {
			if ( isset( $_REQUEST['post_id'] ) ) {
				$post_id = $_REQUEST['post_id'];
			} elseif ( isset( $_REQUEST['post_ID'] ) ) {
				$post_id = $_REQUEST['post_ID'];
			} elseif ( isset( $_REQUEST['post'] ) ) {
				$post_id = $_REQUEST['post'];
			} else {
				$post_id = false;
			}
			$post_id = filter_var( $post_id, FILTER_SANITIZE_NUMBER_INT );
			$post_id = apply_filters( 'frou_parent_post_id', $post_id );
			return $post_id;
		}

		/**
		 * Get post
		 *
		 * @version 2.1.7
		 * @since   2.0.0
		 *
		 * @return null|\WP_Post
		 */
		public static function get_parent_post() {
			$post_id = self::get_parent_post_id();
			$post_obj = null;
			if ( $post_id && is_numeric( $post_id ) ) {
				$posts = get_posts( array(
					'include'     => $post_id,
					'post_type'   => get_post_type( $post_id ),
					'post_status' => 'any'
				) );
				foreach ( $posts as $post ) {
					setup_postdata( $post );
					$post_obj = $post;
				}
				wp_reset_postdata();
			}

			return $post_obj;
		}
	}
}