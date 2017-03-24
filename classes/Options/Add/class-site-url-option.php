<?php
/**
 * File renaming on upload - Enable Option
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options\Add;

use FROU\Options\Option;
use FROU\Plugin_Core;
use FROU\WordPress\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Add\Site_URL_Option' ) ) {
	class Site_URL_Option extends Option {

		const OPTION_SITE_URL      = 'site_url';
		const OPTION_SITE_URL_TEXT = 'site_url_text';

		/*
		const OPTION_SITE_URL_POSITION = 'site_url_position';

		const OPTION_DATETIME          = 'datetime';
		const OPTION_DATETIME_POSITION = 'datetime_position';

		const OPTION_CHARACTERS_PREPEND      = 'characters_prepend';
		const OPTION_CHARACTERS_PREPEND_TEXT = 'characters_prepend_text';

		const OPTION_CHARACTERS_APPEND      = 'characters_append';
		const OPTION_CHARACTERS_APPEND_TEXT = 'characters_append_text';
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

		function init() {
			parent::init();
			add_filter( 'frou_sanitize_file_name', array( $this, 'frou_sanitize_file_name' ), 11 );
		}

		public function frou_sanitize_file_name( $filename_infs ) {
			if ( ! filter_var( $this->get_option( self::OPTION_SITE_URL, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename_infs;
			}

			$structure_rules = $filename_infs['structure']['rules'];
			if ( strpos( $structure_rules, '{siteurl}' ) !== false ) {
				$site_url                                             = $this->get_option( self::OPTION_SITE_URL_TEXT );
				$filename_infs['structure']['translation']['siteurl'] = $site_url;
			}


			return $filename_infs;
		}

		public function add_fields( $fields, $section ) {
			$new_options = array(
				// Site URL
				array(
					'name'    => self::OPTION_SITE_URL,
					'label'   => __( 'Site URL', 'file-renaming-on-upload' ),
					'desc'    => __( 'Inserts site URL', 'file-renaming-on-upload' ) . ' - ' . '<strong>{siteurl}</strong>',
					'type'    => 'checkbox',
					'default' => 'on',
				),

				array(
					'name'        => self::OPTION_SITE_URL_TEXT,
					'desc'        => __( 'The site URL (Change it the way you like)', 'file-renaming-on-upload' ),
					'type'        => 'text',
					'placeholder' => $this->get_site_url(),
					'default'     => $this->get_site_url(),
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}