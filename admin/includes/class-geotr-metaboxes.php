<?php

/**
 * The cpt metaboxes functionality of the plugin.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Geotr
 * @subpackage Geotr/admin/includes
 */

/**
 * @subpackage Geotr/admin/includes
 * @author     Damian Logghe <damian@timersys.com>
 */
class Geotr_Metaboxes{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}


	/**
	 * Register the metaboxes for our cpt
	 * @since    1.0.0
	 * @return   void
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'geotr-rules',
			 __( 'Redirection Rules', 'geotr' ),
			array( $this, 'geotr_rules' ),
			'geotr_cpt',
			'normal',
			'core'
		);
		add_meta_box(
			'geotr-opts',
			 __( 'Redirection Options', 'geotr' ),
			array( $this, 'geotr_opts' ),
			'geotr_cpt',
			'normal',
			'core'
		);
	}

	/**
	 * Saves the post meta of redirections
	 * @since 1.0.0
	 */
	function  save_meta_options( $post_id ){

		// Verify that the nonce is set and valid.
		if ( !isset( $_POST['geotr_options_nonce'] ) || ! wp_verify_nonce( $_POST['geotr_options_nonce'], 'geotr_options' ) ) {
			return $post_id;
		}
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// same for ajax
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return $post_id;
		}
		// same for cron
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return $post_id;
		}
		// same for posts revisions
		if ( is_int( wp_is_post_autosave( $post_id ) ) ) {
			return $post_id;
		}

		// can user edit this post?
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$opts = $_POST['geotr'];
		unset( $_POST['geotr'] );

		$post = get_post($post_id);

		// sanitize settings
		$opts['whitelist']	 	    = sanitize_textarea_field( $opts['whitelist'] );
		$opts['url']	 	        = sanitize_text_field( $opts['url'] );
		$opts['exclude_se']         = absint( sanitize_text_field( $opts['exclude_se'] ) );
		$opts['one_time_redirect'] 	= absint( sanitize_text_field( $opts['one_time_redirect'] ) );
		$opts['status'] 	        = absint( sanitize_text_field( $opts['status'] ) );

		// save box settings
		update_post_meta( $post_id, 'geotr_options', apply_filters( 'geotr/metaboxes/sanitized_options', $opts ) );

		// Start with rules
		if( isset($_POST['geotr_rules']) && is_array($_POST['geotr_rules']) )
		{
			// clean array keys
			$groups = array_values( $_POST['geotr_rules'] );
			unset( $_POST['geotr_rules'] );

			foreach($groups as $group_id => $group )
			{
				if( is_array($group) )
				{
					// clean array keys
					$groups_a[] = array_values( $group );

				}
			}

			update_post_meta( $post_id, 'geotr_rules', apply_filters( 'geotr/metaboxes/sanitized_rules', $groups_a ) );

		}
	}

    /**
     * Include the metabox view for rules
     * @param  object $post    spucpt post object
     * @param  array $metabox full metabox items array
     * @since 1.0.0
     */
    public function geotr_rules( $post, $metabox ) {

	    $groups = apply_filters('geotr/metaboxes/get_rules', Geotr_Helper::get_rules( $post->ID ), $post->ID);

        include GEOTR_DIR . '/admin/partials/metaboxes/rules.php';
    }

    /**
     * Include the metabox view for opts
     * @param  object $post    geotrcpt post object
     * @param  array $metabox full metabox items array
     * @since 1.0.0
     */
    public function geotr_opts( $post, $metabox ) {

        $opts = apply_filters('geotr/metaboxes/get_options', Geotr_Helper::get_options( $post->ID ), $post->ID);

        include GEOTR_DIR . '/admin/partials/metaboxes/opts.php';
    }

}
