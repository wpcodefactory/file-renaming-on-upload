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
		}

		public function frou_sanitize_file_name( $filename_infs ) {
			$filename_infs['structure']['rules'] = $this->get_option( self::OPTION_FILE_NAME_STRUCTURE );

			return $filename_infs;
		}

		public function add_fields( $fields, $section ) {
			$new_options = array(
				array(
					'name'    => self::OPTION_FILE_NAME_STRUCTURE,
					'label'   => __( 'File name structure', 'file-renaming-on-upload' ),
					'desc'    => __( 'Manages all the filename rules. If you want only the filename, leave it as <b>{filename}</b> only', 'file-renaming-on-upload' ),
					'default' => '{siteurl}-{filename}',
					'type'    => 'text',
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}