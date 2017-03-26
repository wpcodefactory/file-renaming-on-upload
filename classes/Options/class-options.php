<?php
/**
 * File renaming on upload - Options
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options;


use FROU\Options\General\Enable_Option;
use FROU\Options\General\Filename_Structure_Option;

use FROU\Options\General\Permalink_Update_Option;
use FROU\Options\Rules\Datetime_Option;
use FROU\Options\Rules\Filename;
use FROU\Options\Rules\Filename_Option;
use FROU\Options\Rules\SiteURL_Option;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Options' ) ) {
	class Options {


		function __construct() {


			// Remove options
			/*$option = new Characters_Option( array( 'section' => 'frou_remove_opt' ) );
			$option->init();

			// General options
			$option = new Enable_Option( array( 'section' => 'frou_general_opt' ) );
			$option->init();
			$option = new Filename_Structure_Option( array( 'section' => 'frou_general_opt' ) );
			$option->init();

			// Add options
			$option = new Site_URL_Option( array( 'section' => 'frou_add_opt', 'structure_rule' => 'siteurl' ) );
			$option->init();
			$option = new Datetime_Option( array( 'section' => 'frou_add_opt', 'structure_rule' => 'datetime' ) );
			$option->init();

			// Convert options
			$option = new Accents_Option( array( 'section' => 'frou_convert_opt' ) );
			$option->init();
			$option = new Lowercase_Option( array( 'section' => 'frou_convert_opt' ) );
			$option->init();*/

			// General options
			$option = new Enable_Option( array( 'section' => 'frou_general_opt' ) );
			$option->init();
			$option = new Permalink_Update_Option(array( 'section' => 'frou_general_opt' ));
			$option->init();
			$option = new Filename_Structure_Option( array( 'section' => 'frou_general_opt' ) );
			$option->init();

			// Rules
			$option = new Filename_Option( array( 'section' => 'frou_filenaming_rules_opt' ) );
			$option->init();
			$option = new SiteURL_Option( array( 'section' => 'frou_filenaming_rules_opt' ) );
			$option->init();
			$option = new Datetime_Option( array( 'section' => 'frou_filenaming_rules_opt' ) );
			$option->init();

			/*$option = new Characters_Option( array( 'section' => 'frou_filenaming_rules_opt' ) );
			$option->init();



			// Add options
			$option = new Site_URL_Option( array( 'section' => 'frou_filenaming_rules_opt', 'structure_rule' => 'siteurl' ) );
			$option->init();
			$option = new Datetime_Option( array( 'section' => 'frou_filenaming_rules_opt', 'structure_rule' => 'datetime' ) );
			$option->init();

			// Convert options
			$option = new Accents_Option( array( 'section' => 'frou_filenaming_rules_opt' ) );
			$option->init();
			$option = new Lowercase_Option( array( 'section' => 'frou_filenaming_rules_opt' ) );
			$option->init();*/
		}


	}
}