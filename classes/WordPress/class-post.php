<?php
/**
 * File renaming on upload - Wordpress Post
 *
 * @version 2.0.0
 * @since   2.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\WordPress;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\WordPress\Post' ) ) {
	class Post {
		public static function get_post_slug( $postObj = null ) {
			if ( $postObj == null ) {
				$postObj = self::get_post();
			}

			$postSlug = '';

			if ( $postObj != null ) {
				$postSlug = $postObj->post_name;
			}

			return $postSlug;
		}

		public static function get_post() {
			if ( isset( $_REQUEST['post_id'] ) ) {
				$post_id = $_REQUEST['post_id'];
			} else {
				$post_id = false;
			}

			$postObj = null;
			if ( $post_id && is_numeric( $post_id ) ) {
				$postObj = get_post( $post_id );
			}

			return $postObj;
		}
	}
}