<?php
/**
 * File renaming on upload - Wordpress Post
 *
 * @version 2.1.7
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
		public static function get_post_slug( $post_obj = null ) {
			if ( $post_obj == null ) {
				$post_obj = self::get_post();
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
		 * Get post
		 *
		 * @version 2.1.7
		 * @since   2.0.0
		 *
		 * @return null|\WP_Post
		 */
		public static function get_post() {
			if ( isset( $_REQUEST['post_id'] ) ) {
				$post_id = $_REQUEST['post_id'];
			} else if ( isset( $_REQUEST['post_ID'] ) ) {
				$post_id = $_REQUEST['post_ID'];
			} else {
				$post_id = false;
			}

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