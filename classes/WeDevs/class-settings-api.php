<?php
/**
 * File renaming on upload - Settings API.
 *
 * @version 2.7.0
 * @since   2.0.0
 * @author  WPFactory
 */

namespace FROU\WeDevs;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'FROU\WeDevs\Settings_Api' ) ) {
	class Settings_Api extends \WeDevs_Settings_API {

		/**
		 * ajax_action_progress_bar.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $ajax_action_progress_bar = 'frou_progress_ajax_action';

		/**
		 * Settings_Api constructor.
		 *
		 * @version 2.7.0
		 * @since   1.0.0
		 */
		public function __construct() {
			parent::__construct();

			$action = $this->ajax_action_progress_bar;
			add_action( "wp_ajax_{$action}", array( $this, 'ajax_callback_progress_bar' ) );
		}

		/**
		 * admin_enqueue_scripts.
		 *
		 * @version 2.6.9
		 * @since   2.5.9
		 *
		 * @return void
		 */
		public function admin_enqueue_scripts() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET param to detect admin page context, not processing form data.
			$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
			if ( 'file-renaming-on-upload' === $page ) {
				parent::admin_enqueue_scripts();
			}
		}

		/**
		 * Displays a title field for a settings field.
		 *
		 * @version 2.7.0
		 * @since   1.0.0
		 *
		 * @param   array  $args  settings field args
		 */
		function callback_progress_bar( $args ) {
			$args = wp_parse_args( $args, array(
				'option_queue_count' => '',
				'option_total_count' => '',
				'option_action'      => ''
			) );

			$total  = sanitize_text_field( $args['option_total_count'] );
			$queue  = sanitize_text_field( $args['option_queue_count'] );
			$action = sanitize_text_field( $args['option_action'] );
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET param to detect admin page context, not processing form data.
			$action_get = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
			$id         = $args['id'];

			echo '
			<div id="' . esc_attr( $id ) . '" class="progress_bar_field">
				<div class="bar">
					<span class="values"><span class="absolute-value"></span><span class="percent-value"></span></span>
					<div class="percent-fill"></div>
				</div>
			</div>';

			?>
			<script>
				jQuery( document ).ready( function ( $ ) {
					var interval;
					var action_called = <?php echo ( $action_get === $action ) ? 'true' : 'false'; ?>;
					var percent = 0;
					var count = 0;
					var no_queue = false;
					var completed = false;

					function update_ui( response ) {
						var data = response && response.data ? response.data : {};
						var total = parseInt( data.total_count, 10 ) || 0;
						var queue = parseInt( data.queue_count, 10 ) || 0;

						if ( total > 0 ) {
							if ( queue >= total ) {
								percent = 100;
								completed = true;
							} else {
								percent = Math.round( ( queue / total ) * 100 );
							}
						} else {
							percent = 100;
							completed = true;
						}

						$( '.progress_bar_field#<?php echo esc_attr( $id ); ?> .percent-fill' ).css( 'width', percent + '%' );
						$( '.progress_bar_field#<?php echo esc_attr( $id ); ?> .percent-value' ).html( percent + '%' );
						$( '.progress_bar_field#<?php echo esc_attr( $id ); ?> .absolute-value' ).html( queue + '/' + total );
					}

					function call_ajax() {
						var ajax_url = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
						var data = {
							'action': '<?php echo esc_js( $this->ajax_action_progress_bar ); ?>',
							'nonce': '<?php echo esc_js( wp_create_nonce( $this->ajax_action_progress_bar ) ); ?>',
							'option_queue_count': '<?php echo esc_js( $queue ); ?>',
							'option_total_count': '<?php echo esc_js( $total ); ?>'
						};
						// We can also pass the url value separately from ajaxurl for front end AJAX implementations
						jQuery.post( ajax_url, data, function ( response ) {
							count++;
							var data = response && response.data ? response.data : {};
							var total = parseInt( data.total_count, 10 ) || 0;
							var queue = parseInt( data.queue_count, 10 ) || 0;
							var server_percent = parseInt( data.percent, 10 );

							if ( isNaN( server_percent ) ) {
								server_percent = total > 0 ? Math.round( ( queue / total ) * 100 ) : 0;
							}

							if ( data.no_queue === true || queue >= total ) {
								no_queue = true;
								server_percent = 100;
								completed = true;
							}

							percent = server_percent;
							$( '.progress_bar_field#<?php echo esc_attr( $id ); ?> .percent-fill' ).css( 'width', percent + '%' );
							$( '.progress_bar_field#<?php echo esc_attr( $id ); ?> .percent-value' ).html( percent + '%' );
							if ( total > 0 ) {
								$( '.progress_bar_field#<?php echo esc_attr( $id ); ?> .absolute-value' ).html( queue + '/' + total );
							} else {
								complete_percent();
							}
						} );
					}

					function complete_percent() {
						$( '.progress_bar_field#<?php echo esc_attr( $id ); ?> .absolute-value' ).html( ' ' )
					}

					call_ajax();
					interval = setInterval( handle_interval, 1500 );

					function handle_interval() {
						if ( completed ) {
							clearInterval( interval );
							return;
						}
						call_ajax();
					}
				} )
			</script>
			<?php
		}

		/**
		 * ajax_callback_progress_bar.
		 *
		 * @version 2.7.0
		 * @since   1.0.0
		 */
		public function ajax_callback_progress_bar() {
			// Check the nonce and capabilities before returning any option-derived data.
			check_ajax_referer( $this->ajax_action_progress_bar, 'nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'You do not have permission to perform this action.', 'file-renaming-on-upload' ) ) );
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce already verified via check_ajax_referer() above.
			$args = wp_parse_args( wp_unslash( $_REQUEST ), array(
				'option_queue_count' => '',
				'option_total_count' => '',
			) );

			$total        = sanitize_key( $args['option_total_count'] );
			$queue        = sanitize_key( $args['option_queue_count'] );
			$option_total = get_option( $total );
			$option_queue = get_option( $queue );

			if ( is_array( $option_total ) && ! empty( $option_total ) ) {
				$option_total_count = count( $option_total );
			} elseif ( is_numeric( $option_total ) ) {
				$option_total_count = $option_total;
			} else {
				$option_total_count = 0;
			}
			if ( is_array( $option_queue ) && ! empty( $option_queue ) ) {
				$option_queue_count = count( $option_queue );
			} elseif ( is_numeric( $option_queue ) ) {
				$option_queue_count = $option_queue;
			} else {
				$option_queue_count = 0;
			}

			$no_queue = false;

			if ( $option_total_count > 0 ) {
				if ( $option_queue_count >= $option_total_count ) {
					$final_percent = 100;
					$no_queue      = true;
				} else {
					$final_percent = round( ( $option_queue_count / $option_total_count ) * 100 );
				}
			} else {
				$final_percent = 0;
				$no_queue      = true;
			}

			if ( $option_queue_count > $option_total_count ) {
				$option_queue_count = $option_total_count;
			}

			wp_send_json_success( array( 'no_queue' => $no_queue, 'total_count' => $option_total_count, 'queue_count' => $option_queue_count, 'percent' => $final_percent ) );
			wp_die();
		}

		/**
		 * settings sections array.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		protected $settings_sections = array();

		/**
		 * Settings fields array
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		protected $settings_fields = array();

		/**
		 * style_fix.
		 *
		 * @version 2.5.9
		 * @since   1.0.0
		 */
		function _style_fix() {
			global $wp_version;

			if ( version_compare( $wp_version, '3.8', '<=' ) ):
				?>
				<style type="text/css">
					/** WordPress 3.8 Fix **/
					.form-table th {
						padding: 20px 10px;
					}

					#wpbody-content .metabox-holder {
						padding-top: 5px;
					}
				</style>
			<?php
			endif;

			?>
			<style type="text/css">
				.frou-title-field {
					font-weight: bold;
					/*margin-bottom:-15px;*/
					/*margin-top:10px;
					margin-bottom:-9px;*/
					color: #888;
				}

				[id*="frou_"] .progress_bar_field .bar {
					background: #ccc;
					width: 100%;
					height: 35px;
					position: relative;
				}

				[id*="frou_"] .progress_bar_field .percent-fill {
					content: ' ';
					position: absolute;
					left: 0;
					top: 0;
					width: 0;
					height: 100%;
					background: #00cb00;
					z-index: 2;
					transition: all 1s ease-in-out;
				}

				[id*="frou_"] .progress_bar_field .absolute-value {
					/*margin:0 10px 0 0px;*/
					color: #116d01;
					display: inline-block;
					float: left;
					margin: 0 0 0 15px;
				}

				[id*="frou_"] .progress_bar_field .percent-value {
					float: right;
					display: inline-block;
					margin: 0 15px 0 0;
				}

				[id*="frou_"] .progress_bar_field .values {
					position: absolute;
					z-index: 3;
					display: block;
					left: 0;
					top: 0;
					font-weight: bold;
					line-height: 32px;
					font-size: 19px;
					color: #fff;
					width: 100%;
					text-align: center;
					height: 100%;
					transition: all 1s ease-in-out;
				}

				[id*="frou_"] .desc_secondary {
					color: #888;
					margin-top: 5px;
				}

				[id*="frou_"] h2 {
					font-weight: bold;
					display: none;
				}

				[id*="frou_"] .form-table td fieldset label {
					margin: 0 !important;
				}

				[id*="frou_"] .form-table th label {
					font-weight: bold;
					color: #333;
					font-size: 14px;
				}
			</style>
			<script>
				// Activate correct tab
				jQuery( document ).ready( function ( $ ) {
					if ( window.location.hash ) {
						var tab = $( 'a[href="' + window.location.hash + '"]' );
						if ( tab.length ) {
							tab.click();
						}
					}
				} )
			</script>
			<?php
		}

		/**
		 * Returns allowed HTML tags for settings field output.
		 *
		 * @version 2.6.9
		 * @since   2.6.9
		 *
		 * @return array
		 */
		private function get_settings_field_allowed_html() {
			return array(
				'fieldset' => array(),
				'label'    => array(
					'for' => true,
				),
				'input'    => array(
					'type'        => true,
					'class'       => true,
					'id'          => true,
					'name'        => true,
					'value'       => true,
					'checked'     => true,
					'placeholder' => true,
					'min'         => true,
					'max'         => true,
					'step'        => true,
					'size'        => true,
				),
				'select'   => array(
					'multiple' => true,
					'class'    => true,
					'name'     => true,
					'id'       => true,
				),
				'option'   => array(
					'value'    => true,
					'selected' => true,
				),
				'div'      => array(
					'class' => true,
				),
				'p'        => array(
					'class' => true,
				),
				'strong'   => array(),
				'code'     => array(),
				'a'        => array(
					'href'   => true,
					'target' => true,
				),
				'br'       => array(),
				'span'     => array(
					'class' => true,
				),
			);
		}

		/**
		 * Displays a checkbox for a settings field.
		 *
		 * @version 2.6.9
		 * @since   1.0.0
		 *
		 * @param   array  $args  settings field args
		 */
		function callback_checkbox( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

			$html = '<fieldset>';
			$html .= sprintf( '<label for="wpuf-%1$s[%2$s]">', esc_attr( $args['section'] ), esc_attr( $args['id'] ) );
			$html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', esc_attr( $args['section'] ), esc_attr( $args['id'] ) );
			$html .= sprintf( '<input type="checkbox" class="checkbox" id="wpuf-%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />', esc_attr( $args['section'] ), esc_attr( $args['id'] ), checked( $value, 'on', false ) );
			$html .= sprintf( '%1$s</label>', wp_kses_post( $args['desc'] ) );
			$html .= sprintf( '%1$s', $this->get_field_description_full( $args ) );
			$html .= '</fieldset>';

			echo wp_kses( $html, $this->get_settings_field_allowed_html() );
		}

		/**
		 * Get field description for display.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param   array  $args  settings field args
		 *
		 * @return string
		 */
		public function get_field_description_full( $args ) {
			if ( ! empty( $args['desc_secondary'] ) ) {
				$desc = sprintf( '<div class="desc_secondary">%s</div>', $args['desc_secondary'] );
			} else {
				$desc = '';
			}

			return $desc;
		}

		/**
		 * Displays a text field for a settings field.
		 *
		 * @version 2.6.9
		 * @since   1.0.0
		 *
		 * @param   array  $args  settings field args
		 */
		function callback_text( $args ) {

			$value       = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$type        = isset( $args['type'] ) ? $args['type'] : 'text';
			$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';

			$html = sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', esc_attr( $type ), esc_attr( $size ), esc_attr( $args['section'] ), esc_attr( $args['id'] ), $value, $placeholder );
			$html .= $this->get_field_description( $args );
			$html .= $this->get_field_description_full( $args );

			echo wp_kses( $html, $this->get_settings_field_allowed_html() );
		}

		/**
		 * Displays a title field for a settings field.
		 *
		 * @version 2.6.9
		 * @since   1.0.0
		 *
		 * @param   array  $args  settings field args
		 */
		function callback_title( $args ) {
			$std  = isset( $args['std'] ) ? esc_html( $args['std'] ) : '';
			$html = '<div class="frou-title-field">' . $std . '</div>';
			$html .= $this->get_field_description( $args );
			$html .= $this->get_field_description_full( $args );
			echo wp_kses( $html, $this->get_settings_field_allowed_html() );
		}

		/**
		 * Displays a multi select dropdown for a settings field.
		 *
		 * @version 2.6.9
		 * @since   1.0.0
		 *
		 * @param   array  $args  settings field args
		 */
		function callback_multiselect( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
			$value = is_array( $value ) ? (array) $value : array();
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$html  = sprintf( '<select multiple="multiple" class="%1$s" name="%2$s[%3$s][]" id="%2$s[%3$s]">', esc_attr( $size ), esc_attr( $args['section'] ), esc_attr( $args['id'] ) );

			foreach ( $args['options'] as $key => $label ) {
				$checked = in_array( $key, $value ) ? $key : '0';
				$html    .= sprintf( '<option value="%s"%s>%s</option>', esc_attr( $key ), selected( $checked, $key, false ), esc_html( $label ) );
			}

			$html .= sprintf( '</select>' );
			$html .= $this->get_field_description( $args );

			echo wp_kses( $html, $this->get_settings_field_allowed_html() );
		}

		/**
		 * callback_separator.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $args
		 */
		function callback_separator( $args ) {
			//echo '<hr />';
			/*$value       = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size        = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
			$type        = isset( $args['type'] ) ? $args['type'] : 'text';
			$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';

			$html        = sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder );
			$html       .= $this->get_field_description( $args );

			echo $html;*/
		}

		/**
		 * show_forms.
		 *
		 * @version 2.6.9
		 * @since   1.0.0
		 */
		function show_forms() {
			//parent::show_forms();
			?>
			<div class="metabox-holder">
				<?php foreach ( $this->settings_sections as $form ) { ?>
					<div id="<?php echo esc_attr( $form['id'] ); ?>" class="group" style="display: none;">
						<form method="post" action="options.php">
							<?php
							do_action( 'wsa_form_top_' . $form['id'], $form );
							settings_fields( $form['id'] );
							do_settings_sections( $form['id'] );
							do_action( 'wsa_form_bottom_' . $form['id'], $form );
							if ( isset( $this->settings_fields[ $form['id'] ] ) ):
								?>
								<div style="padding-left: 0px">
									<?php submit_button(); ?>
								</div>
							<?php endif; ?>
						</form>
					</div>
				<?php } ?>
			</div>
			<?php
			$this->script();
		}

		/**
		 * admin_init.
		 *
		 * @version 2.6.9
		 * @since   1.0.0
		 */
		function admin_init() {
			//parent::admin_init();

			//register settings sections
			foreach ( $this->settings_sections as $section ) {
				if ( false == get_option( $section['id'] ) ) {
					add_option( $section['id'] );
				}

				if ( isset( $section['desc'] ) && ! empty( $section['desc'] ) ) {
					$section_desc = '<div class="inside">' . wp_kses_post( $section['desc'] ) . '</div>';
					$callback     = function () use ( $section_desc ) {
						echo wp_kses_post( $section_desc );
					};
				} else if ( isset( $section['callback'] ) ) {
					$callback = $section['callback'];
				} else {
					$callback = null;
				}

				add_settings_section( $section['id'], $section['title'], $callback, $section['id'] );
			}

			//register settings fields
			foreach ( $this->settings_fields as $section => $field ) {
				foreach ( $field as $option ) {

					$name     = $option['name'];
					$type     = isset( $option['type'] ) ? $option['type'] : 'text';
					$label    = isset( $option['label'] ) ? $option['label'] : '';
					$callback = isset( $option['callback'] ) ? $option['callback'] : array(
						$this,
						'callback_' . $type,
					);

					$args = array(
						'id'                 => $name,
						'class'              => isset( $option['class'] ) ? $option['class'] : $name,
						'label_for'          => "wpuf-{$section}[{$name}]",
						'desc'               => isset( $option['desc'] ) ? $option['desc'] : '',
						'desc_secondary'     => isset( $option['desc_secondary'] ) ? $option['desc_secondary'] : '',
						'option_queue_count' => isset( $option['option_queue_count'] ) ? $option['option_queue_count'] : '',
						'option_total_count' => isset( $option['option_total_count'] ) ? $option['option_total_count'] : '',
						'option_action'      => isset( $option['option_action'] ) ? $option['option_action'] : '',
						'name'               => $label,
						'section'            => $section,
						'size'               => isset( $option['size'] ) ? $option['size'] : null,
						'options'            => isset( $option['options'] ) ? $option['options'] : '',
						'std'                => isset( $option['default'] ) ? $option['default'] : '',
						'sanitize_callback'  => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
						'type'               => $type,
						'placeholder'        => isset( $option['placeholder'] ) ? $option['placeholder'] : '',
						'min'                => isset( $option['min'] ) ? $option['min'] : '',
						'max'                => isset( $option['max'] ) ? $option['max'] : '',
						'step'               => isset( $option['step'] ) ? $option['step'] : '',
					);

					add_settings_field( "{$section}[{$name}]", $label, $callback, $section, $section, $args );
				}
			}

			// creates our settings in the options table
			foreach ( $this->settings_sections as $section ) {
				register_setting( $section['id'], $section['id'], array( $this, 'sanitize_options' ) );
			}
		}
	}
}