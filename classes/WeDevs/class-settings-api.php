<?php
/**
 * File renaming on upload - Settings APi
 *
 * @version 2.2.0
 * @since   2.0.0
 * @author  Pablo S G Pacheco
 */

namespace FROU\WeDevs;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'FROU\WeDevs\Settings_Api' ) ) {
	class Settings_Api extends \FROU\WeDevs\WeDevs_Settings_API_Original {

	    public $ajax_action_progress_bar='frou_progress_ajax_action';

	    /*public function __construct() {
	        parent::__
	    }*/

	    public function __construct() {
	        parent::__construct();

	        $action=$this->ajax_action_progress_bar;
		    add_action( "wp_ajax_{$action}", array( $this, 'ajax_callback_progress_bar' ) );
		    add_action( "wp_ajax_nopriv_{$action}", array( $this, 'ajax_callback_progress_bar' ) );
	    }

		/**
		 * Displays a title field for a settings field
		 *
		 * @param array   $args settings field args
		 */
		function callback_progress_bar( $args ) {
			$args = wp_parse_args( $args, array(
				'option_queue_count' => '',
				'option_total_count' => '',
				'option_action'      => ''
			) );

			$total      = sanitize_text_field( $args['option_total_count'] );
			$queue      = sanitize_text_field( $args['option_queue_count'] );
			$action     = sanitize_text_field( $args['option_action'] );
			$action_get = isset( $_GET['action'] ) ? $_GET['action'] : '';
			$id         = $args['id'];

			echo '
			<div id="'.$id.'" class="progress_bar_field">
			    <div class="bar">
                    <span class="percent-value"></span>
                    <div class="percent"></div>
			    </div>
			</div>';

            ?>
            <script>
                jQuery(document).ready(function($){
	                var interval;
	                var action_called = <?php echo $action_get == $action ? 'true' : 'false'; ?>;
	                var percent = 0;
	                var count = 0;
	                var no_queue=false;
	                function call_ajax(){
		                var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
		                var data = {
			                'action': '<?php echo $this->ajax_action_progress_bar; ?>',
			                'option_queue_count': '<?php echo $queue; ?>',
			                'option_total_count': '<?php echo $total; ?>'
		                };
		                // We can also pass the url value separately from ajaxurl for front end AJAX implementations
		                jQuery.post(ajax_url, data, function(response) {
			                count++;
                            percent = response.data.percent;
			                if(response.data.no_queue==true){
				                no_queue=true;
				                if(count>1 || action_called){
					                percent=100;
				                }else{
				                	percent=0;
                                }
			                }
			                $('.progress_bar_field#<?php echo $id?> .percent').css('width',percent+'%');
			                $('.progress_bar_field#<?php echo $id?> .percent-value').html(percent+'%');
		                });
                    }
	                interval = setInterval(handle_interval, 1500);
	                function handle_interval(){
	                	if(percent<100 && !no_queue){
	                		call_ajax();
                        }else{
			                clearInterval(interval);
                        }
                    }
                    call_ajax();
                })
            </script>
            <?php
		}

		public function ajax_callback_progress_bar(){
			$args = wp_parse_args($_REQUEST,array(
				'option_queue_count'=>'',
				'option_total_count'=>'',
			));

			$total              = sanitize_text_field( $args['option_total_count'] );
			$queue              = sanitize_text_field( $args['option_queue_count'] );
			$option_total       = get_option( $total );
			$option_queue       = get_option( $queue );
			$option_total_count = is_array( $option_total ) ? count( $option_total ) : 0;
			$option_queue_count = is_array( $option_queue ) ? count( $option_queue ) : 0;
			$no_queue=false;

			if ( $option_total_count != 0 && $option_queue_count != 0  ) {
			    if($option_total_count != $option_queue_count){
				    $final_percent = round( ( ( $option_total_count - $option_queue_count ) / $option_total_count ) * 100 );
                }else{
				    $final_percent=0;
                }
			} else {
				$final_percent = 0;
				$no_queue      = true;
			}

			wp_send_json_success(array('no_queue'=>$no_queue, 'total_count'=>$option_total_count,'queue_count'=>$option_queue_count,'percent'=>$final_percent));
			wp_die();
        }

		/**
		 * settings sections array
		 *
		 * @var array
		 */
		protected $settings_sections = array();

		/**
		 * Settings fields array
		 *
		 * @var array
		 */
		protected $settings_fields = array();

		function _style_fix() {
			global $wp_version;

			if (version_compare($wp_version, '3.8', '<=')):
				?>
                <style type="text/css">
                    /** WordPress 3.8 Fix **/
                    .form-table th { padding: 20px 10px; }
                    #wpbody-content .metabox-holder { padding-top: 5px; }
                </style>
				<?php
			endif;

			?>
            <style type="text/css">
                .frou-title-field{
                    font-weight:bold;
                    /*margin-bottom:-15px;*/
                    /*margin-top:10px;
                    margin-bottom:-9px;*/
                    color:#888;
                }
                [id*="frou_"] .progress_bar_field .bar {
                    background:#ccc;
                    width:100%;
                    height:35px;
                    position:relative;
                }
                [id*="frou_"] .progress_bar_field .percent {
                    content:' ';
                    position:absolute;
                    left:0;
                    top:0;
                    width:0;
                    height:100%;
                    background: #00cb00;
                    z-index:2;
                    transition:all 1s ease-in-out;
                }
                [id*="frou_"] .progress_bar_field .percent-value{
                    position:absolute;
                    z-index:3;
                    display:block;
                    left:0;
                    top:0;
                    font-weight:bold;
                    line-height:32px;
                    font-size:19px;
                    color:#fff;
                    width:100%;
                    text-align:center;
                    height:100%;
                    transition:all 1s ease-in-out;
                }
                [id*="frou_"] .desc_secondary{
                    color:#888;
                    margin-top:5px;
                }
                [id*="frou_"] h2{
                    font-weight:bold;
                    display:none;
                }
                [id*="frou_"] .form-table td fieldset label{
                    margin:0 !important;
                }
                [id*="frou_"] .form-table th label{
                    font-weight:bold;
                    color:#333;
                    font-size:14px;
                }
            </style>
            <script>
                // Activate correct tab
                jQuery(document).ready(function($){
	                if(window.location.hash) {
                        var tab = $('a[href="'+window.location.hash+'"]');
                        if(tab.length){
	                        tab.click();
                        }
	                }
                })
            </script>
            <?php
		}

		/**
		 * Displays a checkbox for a settings field
		 *
		 * @param array   $args settings field args
		 */
		function callback_checkbox( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

			$html  = '<fieldset>';
			$html  .= sprintf( '<label for="wpuf-%1$s[%2$s]">', $args['section'], $args['id'] );
			$html  .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id'] );
			$html  .= sprintf( '<input type="checkbox" class="checkbox" id="wpuf-%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />', $args['section'], $args['id'], checked( $value, 'on', false ) );
			$html  .= sprintf( '%1$s</label>', $args['desc'] );
			$html  .= sprintf( '%1$s', $this->get_field_description_full($args));
			$html  .= '</fieldset>';

			echo $html;
		}

		/**
		 * Get field description for display
		 *
		 * @param array   $args settings field args
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
		 * Displays a text field for a settings field
		 *
		 * @param array   $args settings field args
		 */
		function callback_text( $args ) {

			$value       = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size        = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
			$type        = isset( $args['type'] ) ? $args['type'] : 'text';
			$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';

			$html        = sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder );
			$html       .= $this->get_field_description( $args );
			$html       .= $this->get_field_description_full( $args );

			echo $html;
		}

		/**
		 * Displays a title field for a settings field
		 *
		 * @param array   $args settings field args
		 */
		function callback_title( $args ) {

			$value       = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$type        = isset( $args['type'] ) ? $args['type'] : 'text';
			$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';
			$std         = isset( $args['std'] ) ? $args['std'] : '';

			//$html        = sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder );
			$html        = '<div class="frou-title-field">'.$std.'</div>';
			$html       .= $this->get_field_description( $args );
			$html       .= $this->get_field_description_full( $args );

			echo $html;
		}

	    function callback_separator( $args){
	        //echo '<hr />';
		    /*$value       = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		    $size        = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
		    $type        = isset( $args['type'] ) ? $args['type'] : 'text';
		    $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';

		    $html        = sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder );
		    $html       .= $this->get_field_description( $args );

		    echo $html;*/
        }

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