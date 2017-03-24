<?php
/**
 * File renaming on upload - Add Option
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Add_Option' ) ) {
	class Add_Option extends Option {
		public $structure_rule = '';

		function __construct( array $args = array() ) {
			parent::__construct( $args );

			$args = wp_parse_args( $args, array(
				'section'        => null,
				'structure_rule' => '',
			) );

			$this->structure_rule = $args['structure_rule'];
		}

		function init() {
			parent::init();
			add_filter( 'frou_structure_rules', array( $this, 'add_structure_rule' ) );
		}


	}
}