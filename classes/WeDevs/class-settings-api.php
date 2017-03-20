<?php
/**
 * File renaming on upload - Settings APi
 *
 * @version 2.0.0
 * @since   2.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\WeDevs;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'FROU\WeDevs\Settings_Api' ) ) {
	class Settings_Api extends \WeDevs_Settings_API {

		function show_forms() {
			//parent::show_forms();
			?>
			<div class="metabox-holder">
				<?php foreach ( $this->settings_sections as $form ) { ?>
					<div id="<?php echo $form['id']; ?>" class="group" style="display: none;">
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

		function admin_init() {
			//parent::admin_init();

			//register settings sections
			foreach ( $this->settings_sections as $section ) {
				if ( false == get_option( $section['id'] ) ) {
					add_option( $section['id'] );
				}

				if ( isset( $section['desc'] ) && ! empty( $section['desc'] ) ) {
					$section['desc'] = '<div class="inside">' . $section['desc'] . '</div>';
					$callback        = create_function( '', 'echo "' . str_replace( '"', '\"', $section['desc'] ) . '";' );
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
						'id'                => $name,
						'class'             => isset( $option['class'] ) ? $option['class'] : $name,
						'label_for'         => "wpuf-{$section}[{$name}]",
						'desc'              => isset( $option['desc'] ) ? $option['desc'] : '',
						'name'              => $label,
						'section'           => $section,
						'size'              => isset( $option['size'] ) ? $option['size'] : null,
						'options'           => isset( $option['options'] ) ? $option['options'] : '',
						'std'               => isset( $option['default'] ) ? $option['default'] : '',
						'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
						'type'              => $type,
						'placeholder'       => isset( $option['placeholder'] ) ? $option['placeholder'] : '',
						'min'               => isset( $option['min'] ) ? $option['min'] : '',
						'max'               => isset( $option['max'] ) ? $option['max'] : '',
						'step'              => isset( $option['step'] ) ? $option['step'] : '',
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