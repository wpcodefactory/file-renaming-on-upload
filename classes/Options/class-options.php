<?php
/**
 * File renaming on upload - Options.
 *
 * @version 2.4.1
 * @since   2.0.0
 * @author  WPFactory
 */

namespace FROU\Options;


use FROU\Options\Advanced\Ignore_Empty_Extensions_Option;
use FROU\Options\General\Enable_Option;
use FROU\Options\General\Filename_Structure_Option;

use FROU\Options\Advanced\Ignore_Extensions_Option;
use FROU\Options\Advanced\Ignore_Filenames_Option;
use FROU\Options\General\Gutenberg_Title_Fix_Option;
use FROU\Options\General\Permalink_Update_Option;
use FROU\Options\General\Truncate_Option;
use FROU\Options\Rules\Datetime_Option;
use FROU\Options\Rules\Filename;
use FROU\Options\Rules\Filename_Option;
use FROU\Options\Rules\Post_Title_Option;
use FROU\Options\Rules\SiteURL_Option;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Options' ) ) {
	class Options {

		/**
		 * Constructor.
		 *
		 * @version 2.4.1
		 * @since   2.0.0
		 *
		 * @param array $args
		 */
		function __construct() {
			// General options
			$option = new Enable_Option( array( 'section' => 'frou_general_opt' ) );
			$option->init();
			$option = new Gutenberg_Title_Fix_Option( array( 'section' => 'frou_general_opt' ) );
			$option->init();
			$option = new Permalink_Update_Option( array( 'section' => 'frou_general_opt' ) );
			$option->init();
			$option = new Filename_Structure_Option( array( 'section' => 'frou_general_opt' ) );
			$option->init();

			/*$option = new Truncate_Option( array( 'section' => 'frou_general_opt' ) );
			$option->init();*/

			// Rules
			$option = new Filename_Option( array( 'section' => 'frou_filenaming_rules_opt' ) );
			$option->init();
			$option = new SiteURL_Option( array( 'section' => 'frou_filenaming_rules_opt' ) );
			$option->init();
			$option = new Datetime_Option( array( 'section' => 'frou_filenaming_rules_opt' ) );
			$option->init();
			$option = new Post_Title_Option( array( 'section' => 'frou_filenaming_rules_opt' ) );
			$option->init();

			// Advanced
			$option = new Ignore_Empty_Extensions_Option( array( 'section' => 'frou_advanced_opt' ) );
			$option->init();
			$option = new Ignore_Extensions_Option( array( 'section' => 'frou_advanced_opt' ) );
			$option->init();
			$option = new Ignore_Filenames_Option( array( 'section' => 'frou_advanced_opt' ) );
			$option->init();
		}


	}
}