<?php

/**
 * Field Class
 */

if ( ! ABSPATH ) exit;

class CF7Cuit
{
	/**
	 * Function init plugin
	**/
	public function __construct() {

		add_action( 'plugins_loaded', [ $this, 'hooks' ] , 20 );
	}

	public function hooks() {

		add_action( 'wpcf7_init',           [ $this, 'add_shortcode' ] );
		add_action( 'wp_enqueue_scripts',   [ $this, 'enqueue_scripts' ] );
		add_filter( 'wpcf7_validate_cuit', [ $this, 'validation' ], 10, 2 );
		add_filter( 'wpcf7_validate_cuit*', [ $this, 'validation' ], 10, 2 );

		load_plugin_textdomain( 'cf7-cuit-field', false, dirname( plugin_dir_path( __FILE__ ) ) . '/assets/languages/' );
	}

	/**
	 * Function enqueu script
	 * @version 1.0
	**/
	public function enqueue_scripts() {

		wp_enqueue_script( 'cf7-cuit-jquery-mask', plugins_url( 'assets/js/jquery.maskedinput.min.js', __FILE__ ), array('jquery'), '1.4', true );
		wp_enqueue_script( 'cf7-cuit-functions', plugins_url( 'assets/js/cf7-cuit.js', __FILE__ ), array('jquery'), '1.0', true );
	}

	/**
	 * Function add cuit field in wpcf7
	 * @version 1.0
	**/
	public function add_shortcode() {

		if ( ! function_exists( 'wpcf7_add_form_tag' ) ) return;

		wpcf7_add_form_tag( ['cuit','cuit*'], [$this,'shortcode_handler'], true);
	}

	/**
	 * Function add shortcodes handler
	 * @version 1.0
	**/
	public function shortcode_handler( $tag ) {

		if ( ! class_exists( 'WPCF7_FormTag' ) ) return;

		$tag = new WPCF7_FormTag( $tag );

		if ( empty( $tag->name ) ) return '';

		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type, 'wpcf7-cuit' );

		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		$mask = '__-________-_';

		// the attributes of the tag
		$atts = array(
			'type'           => 'text',
			'value'          => '',
			'name'           => $tag->name,
			'id'             => $tag->get_id_option(),
			'class'          => $tag->get_class_option( $class ),
			'size'           => $tag->get_size_option( '40' ),
			'tabindex'       => $tag->get_option( 'tabindex', 'int', true ),

			'maxlength'      => 13,
			'minlength'      => 13,

			'autocomplete'   => $tag->get_option( 'autocomplete', '[-0-9a-zA-Z]+', true ),
			'readonly'       => $tag->has_option( 'readonly' ),
			'aria-required'  => (string) $tag->is_required(),
			'aria-invalid'   => (string) $validation_error,
			'data-numberonly' => 'true',
			'data-mask'      => $mask,
			'data-autoclear' => $tag->has_option( 'autoclear' ),
		);

		// extract mask and placeholder from $tag->values
		extract( $this->get_markers( $tag->values ) );

		// set tag type
		if ( $tag->has_option( 'type' ) ) {
			$atts['type'] = $tag->get_option( 'type', '[-0-9a-zA-Z]+', true );
		}

		$value = $tag->get_default_option( $value );
		$value = wpcf7_get_hangover( $tag->name, $value );

		$atts['value'] = $value;

		$atts = wpcf7_format_atts( $atts );

		$html = sprintf(
			'<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
			sanitize_html_class( $tag->name ), $atts, $validation_error
		);

		return $html;
	}

	/**
	 * Function check mask field
	 * @version 1.0
	**/
	public function validation( $result, $tag ) {

		if ( ! class_exists( 'WPCF7_FormTag' ) ) return;

		$tag = new WPCF7_FormTag( $tag );

		$name = $tag->name;

		$value = isset( $_POST[$name] )
			? trim( wp_unslash( strtr( (string) $_POST[$name], "\n", " " ) ) )
			: '';

		$value = $this->clear_value( $value );

		if ( $tag->basetype == 'cuit' ) {

			if ( $tag->is_required() && empty($value) ) {
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
			}
		}

		if ( !empty($value) ) {

			$maxlength = $tag->get_maxlength_option();
			$minlength = $tag->get_minlength_option();

			if ( $maxlength && $minlength && $maxlength < $minlength ) {
				$maxlength = $minlength = null;
			}

			$value = preg_replace('/[^0-9]/', '', $value );

			if( !$this->check_cuit($value) )
			{
				$result->invalidate( $tag, __('Invalid CUIT number') );
			}

		}

		return $result;
	}

	/**
	 * Function get mask and placeholder
	 *
	 * @param $values array[]
	 * @return $result array[mask, placeholder]
	 * @version 1.0
	**/
	private function get_markers( $values ) {

		$definitions = '_';

		$result = [ 'mask' => '', 'placeholder' => '' ];

		foreach ( $values as $val ) {
			if ( strpbrk( $val, $definitions ) ) {
				$result['mask'] = $val;
				continue;
			}

			$result['placeholder'] = $val;
		}

		return $result;
	}

    private function check_cuit($value)
    {

        // Cast to string
        $value = (string) $value;

        if (!is_numeric($value)) return false;

        if (strlen($value) != 11)
            return false;

        $prefijo = (int) substr($value, 0,2);

        if (!in_array($prefijo, array(20,23,24,27,30,33,34)))
            return false;

        $coeficiente = array(5,4,3,2,7,6,5,4,3,2);

        $sum=0;

        for ($i=0; $i < 10 ; $i++) {
            $sum += ($value[$i]*$coeficiente[$i]);
        }

        $resto = $sum % 11;

        if($resto==0)
            return ($resto==$value[10]);

        if ($value[10] != 11 - $resto)
            return false;

        return true;
    }


	/**
	 * Function clear string
	 *
	 * @param $string string
	 * @return $result string
	 * @version 1.0
	**/
	private function clear_value( $string ) {

		return str_replace( '_', '', $string );
	}

}

new CF7Cuit;