<?php

/**
 * Plugin Settings
 */

if ( ! ABSPATH ) exit;

class CF7CuitSettings
{

    public function __construct() {

        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts_admin' ] );
        add_action( 'wpcf7_admin_init' , [ $this, 'add_tag_generator_field' ] , 100 );
    }

	/**
	 * Function enqueu script for admin panel
	 * @version 1.0
	**/
	private function enqueue_scripts_admin( $hook_suffix ) {

        if ( false === strpos( $hook_suffix, 'wpcf7' ) ) return;

    }

    /**
     * Function cell Tag GeneratorWPCF7
     *
     * @version 1.0
    **/
    public function add_tag_generator_field() {

        if ( ! class_exists( 'WPCF7_TagGenerator' ) ) return;

        $tag_generator = WPCF7_TagGenerator::get_instance();
        $tag_generator->add('cuit', __( 'cuit', 'cf7-cuit-field' ), [ $this, 'tag_generator_field'] );
    }

    /**
     * Function generating new field
     * @version 1.0
    **/
    public function tag_generator_field( $contact_form , $args = '' ) {

        $args = wp_parse_args( $args, [] );
        $type = $args['id'];

    ?>
    <div class="control-box">
        <fieldset>

            <legend>
                <?php
                    _e( 'Generate a form-tag for a single-line plain text input field in which you enter a CUIT number.', 'cf7-cuit-field' );
                ?>
            </legend>

            <table class="form-table">
            <tbody>
                <tr>
                   <th scope="row"><?php _e( 'Field type', 'contact-form-7' ); ?></th>
                    <td>
                        <fieldset>
                        <legend class="screen-reader-text"><?php _e( 'Field type', 'contact-form-7' ); ?></legend>
                        <label><input type="checkbox" name="required" /> <?php _e( 'Required field', 'contact-form-7' ); ?></label>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php _e( 'Name', 'contact-form-7' ); ?></label></th>
                    <td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
                </tr>

                <tr>
                    <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php _e( 'Id attribute', 'contact-form-7' ); ?></label></th>
                    <td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
                </tr>

                <tr>
                    <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php _e( 'Class attribute', 'contact-form-7' ); ?></label></th>
                    <td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
                </tr>

            </tbody>
            </table>

        </fieldset>
    </div>

    <div class="insert-box">
        <input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

        <div class="submitbox">
           <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
        </div>

        <br class="clear" />
    </div>

    <?php
    }

}

new CF7CuitSettings;
