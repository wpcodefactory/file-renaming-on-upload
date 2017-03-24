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


if ( ! class_exists( 'FROU\Options\General\Enable_Option' ) ) {
	class Enable_Option extends Option {

		const OPTION_ENABLE_PLUGIN = 'enable_plugin';
		public $original_file_name;

		function init() {
			parent::init();
			add_filter( 'sanitize_file_name', array( $this, 'sanitize_filename' ) );
		}

		public function sanitize_filename( $filename ) {

			if ( ! filter_var( $this->get_option( self::OPTION_ENABLE_PLUGIN, true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return $filename;
			}

			$this->original_file_name = $filename;

			$filename_arr = apply_filters( 'frou_sanitize_file_name',
				array(
					'filename_original' => $filename,
					'structure'         => array(
						'rules'       => '',
						'translation' => array( 'filename' => $filename ),
					),
				)
			);
			$filename     = $filename_arr['structure']['rules'];
			foreach ( $filename_arr['structure']['translation'] as $key => $translation ) {
				$filename = str_replace( "{" . $key . "}", $translation, $filename );
			}
			$filename = preg_replace('/\{.*\}/', "", $filename);
			//debug( $filename );
			//debug( $filename_arr );

			return $filename;
		}

		public function add_fields( $fields, $section ) {
			$new_options = array(
				array(
					'name'    => self::OPTION_ENABLE_PLUGIN,
					'label'   => __( 'Enable plugin', 'file-renaming-on-upload' ),
					'desc'    => __( 'Enables the plugin', 'file-renaming-on-upload' ),
					'default' => 'on',
					'type'    => 'checkbox',
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}