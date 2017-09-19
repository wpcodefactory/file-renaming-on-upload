<?php
/**
 * File renaming on upload - Wordpress Post
 *
 * @version 2.1.9
 * @since   2.0.0
 * @author  Pablo S G Pacheco
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
		 * @version 2.1.7
		 * @since   2.0.0
		 *
		 * @param null $post_obj
		 *
		 * @return string
		 */
		public static function get_parent_post_slug( $post_obj = null ) {
			if ( $post_obj == null ) {
				$post_obj = self::get_parent_post();
			}

			$postSlug = '';

			if ( $post_obj != null ) {

				if ( $post_obj->post_name ) {
					$postSlug = $post_obj->post_name;
				} else {
					$postSlug = sanitize_title( $post_obj->post_title );
				}
			}

			return $postSlug;
		}

		/**
		 * Get parent post id
		 *
		 * @version 2.1.9
		 * @since   2.1.9
		 *
		 * @return null|\WP_Post
		 */
		public static function get_parent_post_id() {
			if ( isset( $_REQUEST['post_id'] ) ) {
				$post_id = $_REQUEST['post_id'];
			} else if ( isset( $_REQUEST['post_ID'] ) ) {
				$post_id = $_REQUEST['post_ID'];
			} else {
				$post_id = false;
			}

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