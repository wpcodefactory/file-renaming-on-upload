<?php
/**
 * File renaming on upload - Site URL Option
 *
 * @version 2.0.0
 * @since   2.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options\Rules;

use FROU\Options\Add_Option;
use FROU\Options\Option;
use FROU\Options\Rule_Option;
use FROU\Plugin_Core;
use FROU\WordPress\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Rules\SiteURL_Option' ) ) {
	class SiteURL_Option extends Rule_Option {

		public $option_siteurl_text = 'siteurl_text';

		/**
		 * Constructor
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param array $args
		 */
		function __construct( array $args = array() ) {
			parent::__construct( $args );
			$this->option_id = 'siteurl';
		}

		/**
		 * Gets site url
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 * @return mixed|string
		 */
		function get_site_url() {
			$siteURL           = get_home_url();
			$lastChar          = substr( $siteURL, strlen( $siteURL ) - 1 );
			$siteURL           = ( $lastChar == '/' ) ? substr( $siteURL, 0, strlen( $siteURL ) - 1 ) : $siteURL;
			$noProtocolSiteURL = preg_replace( '/http:\/\/|https:\/\//', '', $siteURL );
			$lastBarIndex      = strrpos( $noProtocolSiteURL, '/' );
			if ( $lastBarIndex ) {
				$finalSiteURL = substr( $noProtocolSiteURL, $lastBarIndex + 1 );
			} else {
				$finalSiteURL = $noProtocolSiteURL;
			}

			return $finalSiteURL;
		}

		/**
		 * Initializes
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 */
		function init() {
			parent::init();
			add_filter( 'frou_sanitize_file_name', array( $this, 'frou_sanitize_file_name' ), 11 );
		}

		/**
		 * Inserts site url on 'frou_sanitize_file_name' filter
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param $filename_infs
		 *
		 * @return mixed
		 */
		public function frou_sanitize_file_name( $filename_infs ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			$structure_rules = $filename_infs['structure']['rules'];
			if ( strpos( $structure_rules, '{' . $this->option_id . '}' ) !== false ) {
				$site_url                                                      = $this->get_option( $this->option_siteurl_text );
				$filename_infs['structure']['translation'][ $this->option_id ] = $site_url;
			}

			return $filename_infs;
		}



		/**
		 * Adds settings fields
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
					'name'    => $this->option_id,
					'label'   => __( 'Site URL', 'file-renaming-on-upload' ),
					'desc'    => __( 'Enables site URL rule', 'file-renaming-on-upload' ) . ' - ' . '<strong>{' . $this->option_id . '}</strong>',
					'type'    => 'checkbox',
					'default' => 'on',
				),

				array(
					'name'        => $this->option_siteurl_text,
					'desc'        => __( 'The site URL (Change it the way you like)', 'file-renaming-on-upload' ),
					'type'        => 'text',
					'placeholder' => $this->get_site_url(),
					'default'     => $this->get_site_url(),
				),
				array(
					'name' => 'siteurl_separator',
					'type' => 'separator',
				),

			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}