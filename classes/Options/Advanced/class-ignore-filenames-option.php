<?php
/**
 * File renaming on upload - Ignore Pages Option
 *
 * @version 2.2.9
 * @since   2.1.1
 * @author  Pablo S G Pacheco
 */

namespace FROU\Options\Advanced;

use FROU\Options\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'FROU\Options\Advanced\Ignore_Filenames_Option' ) ) {
	class Ignore_Filenames_Option extends Option {

		public $option_filenames_ignored = 'filenames_ignored';
		public $option_ignore_without_extension = 'ignore_without_extension';

		/**
		 * Initializes
		 *
		 * @version 2.1.1
		 * @since   2.1.1
		 */
		function init() {
			parent::init();
		}

		/**
		 * Constructor
		 *
		 * @version 2.1.1
		 * @since   2.1.1
		 *
		 * @param array $args
		 */
		function __construct( array $args = array() ) {
			parent::__construct( $args );
			$this->option_id = 'ignore_filenames';
		}

		/**
		 * Adds settings fields
		 *
		 * @version 2.2.9
		 * @since   2.1.1
		 *
		 * @param $fields
		 * @param $section
		 *
		 * @return mixed
		 */
		public function add_fields( $fields, $section ) {
			$new_options = array(
				array(
					'name'    => $this->option_id,
					'label'   => __( 'Ignore filenames', 'file-renaming-on-upload' ),
					'desc'    => __( 'Does not rename these filenames', 'file-renaming-on-upload' ) . ' (' . __( 'comma separated', 'file-renaming-on-upload' ) . ')',
					'default' => 'on',
					'type'    => 'checkbox',
				),
				array(
					'name'    => $this->option_filenames_ignored,
					'placeholder' => 'Comma separated values',
					'default' => 'robots, sitemap, path, scheme, host, owner, repo, owner_repo, base_uri, uri, option_page, action, wpnonce, wp_http_referer, github-updater, github_updater_install_repo, github_updater_repo, github_updater_branch, github_updater_api, github_access_token, bitbucket_username, bitbucket_password, gitlab_enterprise_token, gitlab_access_token, submit, db_version, branch_switch, grid, grid-tall, page-options', 
					'type'    => 'textarea',
				),
				array(
					'name'           => $this->option_ignore_without_extension,
					'desc'           => __( 'Ignores filename only when extension cannot be found', 'file-renaming-on-upload' ),
					'desc_secondary' => __( 'If you have issues with some other plugins try to unmark this option', 'file-renaming-on-upload' ),
					'default'        => 'on',
					'type'           => 'checkbox',
				),
			);

			return parent::add_fields( array_merge( $fields, $new_options ), $section );
		}
	}
}