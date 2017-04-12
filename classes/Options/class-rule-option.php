<?php
/**
 * File renaming on upload - Filenaming Rule Option
 *
 * @version 2.0.0
 * @since   2.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Rule_Option' ) ) {
	class Rule_Option extends Option {

		/**
		 * Add option_id as rule as default
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param $structure_rules
		 *
		 * @return array
		 */
		public function add_structure_rule( $structure_rules ) {
			if ( ! filter_var( $this->get_option( $this->option_id, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $structure_rules;
			}
			$structure_rules[] = $this->option_id;

			return $structure_rules;
		}

		/**
		 * Initializes
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 */
		function init() {
			parent::init();
			add_filter( 'frou_structure_rules', array( $this, 'add_structure_rule' ) );
		}


	}
}