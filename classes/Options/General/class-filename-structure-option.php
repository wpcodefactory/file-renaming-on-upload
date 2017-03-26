<?php
/**
 * File renaming on upload - Filename structure Option
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options\General;

use FROU\Options\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\General\Filename_Structure_Option' ) ) {
	class Filename_Structure_Option extends Option {


		//const BETWEEN_CHARACTERS = 'between_characters';

		function init() {
			parent::init();
			add_filter( 'frou_sanitize_file_name', array( $this, 'frou_sanitize_file_name' ) );
			add_filter( 'frou_structure_rules_list', array( $this, 'possible_structure_rules' ) );
		}

		function __construct( array $args = array() ) {
			parent::__construct( $args );
			$this->option_id = 'filename_structure';
		}

		public function possible_structure_rules( $list ) {
			$list = apply_filters( 'frou_structure_rules', array() );

			return
				'
			<ul style="list-style:inside">
				<li><strong>{' . implode( '}</strong></li><li><strong>{', $list ) . '}</strong></li>
			</ul>
			';
		}

		public function frou_sanitize_file_name( $filename_infs ) {
			$filename_infs['structure']['rules'] = $this->get_option( $this->option_id );

			return $filename_infs;
		}

		public function add_fields( $fields, $section ) {
			$new_options = array(
				array(
					'name'    => $this->option_id,
					'label'   => __( 'Filename structure', 'file-renaming-on-upload' ),
					'desc'    => __( 'Manages the filename structure using rules. If you want only the filename, leave it as <b>{filename}</b>', 'file-renaming-on-upload' ) . '<br /><br />' . __( 'Rules available:', 'file-renaming-on-upload' ) . apply_filters( 'frou_structure_rules_list', '' ),
					'default' => '{siteurl}-{filename}',
					'type'    => 'text',
				),
				/*array(
					'name'    => self::BETWEEN_CHARACTERS,
					'desc'    => __( 'Characters used to separate rules', 'file-renaming-on-upload' ),
					'default' => '-',
					'type'    => 'text',
				),*/
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}