<?php
/**
 * File renaming on upload - Options
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Options' ) ) {
	class Options {

		function __construct() {
			$option = new Remove\Characters( array( 'section' => 'frou_remove_opt' ) );
			$option->init();
		}

	}
}