<?php
/**
 * File renaming on upload - Wordpress Plugin
 *
 * @version 2.0.0
 * @since   2.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\WordPress;

use FROU\Design_Patterns\Singleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\WordPress\Plugin' ) ) {
	class Plugin extends Singleton {
		public $basename;
		public $args;

		protected function __construct() {
		}

		public function setup() {
			$this->basename = plugin_basename( $this->args['plugin_file_path'] );
			add_filter( 'plugin_action_links_' . $this->basename, array( $this, 'action_links' ) );
			$this->handle_localization();
		}

		public function handle_localization() {
			$args   = $this->args;
			$locale = apply_filters( 'plugin_locale', get_locale(), $args['translation']['slug'] );
			load_textdomain( $args['translation']['slug'], WP_LANG_DIR . dirname( $this->basename ) . 'file-renaming-on-upload' . '-' . $locale . '.mo' );
			load_plugin_textdomain( $args['translation']['slug'], false, dirname( $this->basename ) . '/' . $args['translation']['folder'] . '/' );
		}

		function action_links( $links ) {
			$args         = $this->args;
			$action_links = $args['action_links'];
			$custom_links = array();

			foreach ( $action_links as $action_link ) {
				if (
					isset( $action_link['url'] ) && ! empty( $action_link['url'] ) &&
					isset( $action_link['text'] ) && ! empty( $action_link['text'] )
				) {
					$url            = sanitize_text_field( $action_link['url'] );
					$text           = sanitize_text_field( $action_link['text'] );
					$custom_links[] = '<a href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a>';
				}
			}

			return array_merge( $custom_links, $links );
		}

		public function init( $args = array() ) {
			$args = wp_parse_args( $args, array(
				'plugin_file_path' => null,
				'translation'      => array(
					'slug'   => 'my_plugin',
					'folder' => 'languages',
				),
				'action_links'     => array(
					array(
						'url'  => '',
						'text' => '',
					),
				),
			) );

			if ( ! isset( $args['translation']['folder'] ) ) {
				$args['translation']['folder'] = 'languages';
			}

			$this->args = $args;
			$this->setup();
		}
	}
}