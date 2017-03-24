<?php
/**
 * File renaming on upload - Enable Option
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


if ( ! class_exists( 'FROU\Options\General\File_Name_Structure' ) ) {
	class File_Name_Structure extends Option {

		const OPTION_FILE_NAME_STRUCTURE = 'file_name_structure';

		function init() {
			parent::init();
			add_filter( 'frou_sanitize_file_name', array( $this, 'frou_sanitize_file_name' ) );
			add_filter( 'frou_structure_rules_list', array( $this, 'possible_structure_rules' ) );
		}

		public function possible_structure_rules( $list ) {
			$list = apply_filters( 'frou_structure_rules', array( 'filename' ) );

			return
				'
			<ul style="list-style:inside">
				<li><strong>{' . implode( '}</strong></li><li><strong>{', $list ) . '}</strong></li>
			</ul>
			';
		}

		public function frou_sanitize_file_name( $filename_infs ) {
			$filename_infs['structure']['rules'] = $this->get_option( self::OPTION_FILE_NAME_STRUCTURE );

			return $filename_infs;
		}

		public function add_fields( $fields, $section ) {

			//'<ul style="list-style:inside"><li>{asdasd}</li><li>{asdkasd}</li><li>{gkk577}</li></ul>'

			$new_options = array(
				array(
					'name'    => self::OPTION_FILE_NAME_STRUCTURE,
					'label'   => __( 'File name structure', 'file-renaming-on-upload' ),
					'desc'    => __( 'Manages all the filename rules. If you want only the filename, leave it as <b>{filename}</b>', 'file-renaming-on-upload' ) . '<br /><br />' . __( 'Rules available:', 'file-renaming-on-upload' ) . apply_filters( 'frou_structure_rules_list', '' ),
					'default' => '{siteurl}-{filename}',
					'type'    => 'text',
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}