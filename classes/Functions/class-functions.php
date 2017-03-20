<?php
/**
 * File renaming on upload - Functions
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Functions\Functions' ) ) {
	class Functions {
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
	}
}