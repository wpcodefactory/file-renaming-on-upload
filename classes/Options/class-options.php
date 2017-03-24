<?php
/**
 * File renaming on upload - Options
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options;


use FROU\Options\Add\Site_URL_Option;
use FROU\Options\Convert\Accents_Option;
use FROU\Options\Convert\Lowercase_Option;
use FROU\Options\General\Enable_Option;
use FROU\Options\General\File_Name_Structure;
use FROU\Options\Remove\Characters_Option;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Options' ) ) {
	class Options {

		function __construct() {

			// Remove options
			$option = new Characters_Option( array( 'section' => 'frou_remove_opt' ) );
			$option->init();

			// General options
			$option = new Enable_Option( array( 'section' => 'frou_general_opt' ) );
			$option->init();
			$option = new File_Name_Structure( array( 'section' => 'frou_general_opt' ) );
			$option->init();

			// Add options
			$option = new Site_URL_Option( array( 'section' => 'frou_add_opt' ) );
			$option->init();

			// Convert options
			$option = new Accents_Option( array( 'section' => 'frou_convert_opt' ) );
			$option->init();
			$option = new Lowercase_Option( array( 'section' => 'frou_convert_opt' ) );
			$option->init();
		}

	}
}