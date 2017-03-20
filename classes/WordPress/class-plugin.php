<?php
/**
 * File renaming on upload - Wordpress Plugin
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\WordPress;

use FROU\Design_Patterns\Singleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\WordPress\Plugin' ) ) {
	class Plugin extends Singleton {
		
	}
}