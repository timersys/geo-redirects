<?php
//TODO add custom columsn to post type to show redirect settings
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Geotr
 * @subpackage Geotr/admin
 */

/**
 * @subpackage Geotr/admin
 * @author     Damian Logghe <damian@timersys.com>
 */
class Geotr_Admin {

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
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $pagenow, $post;

		if ( get_post_type() !== 'geotr_cpt' || !in_array( $pagenow, array( 'post-new.php', 'edit.php', 'post.php' ) ) )
			return;

		$post_id = isset( $post->ID ) ? $post->ID : '';

		wp_enqueue_script( 'geotr-admin-js', plugin_dir_url( __FILE__ ) . 'js/geotr-admin.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_style( 'geotr-admin-css', plugin_dir_url( __FILE__ ) . 'css/geotr-admin.css', array(), $this->version, 'all' );

		wp_localize_script( 'geotr-admin-js', 'geotr_js',
				array(
					'admin_url' => admin_url( ),
					'nonce' 	=> wp_create_nonce( 'geotr_nonce' ),
					'l10n'		=> array (
							'or'	=> '<span>'.__('OR', 'geotr' ).'</span>'
						),
					'opts'      => Geotr_Helper::get_options($post_id)
				)
		);
	}

	/**
	 * Register direct access link
	 *
	 * @since    1.0.0
	 * @return 	Array
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'edit.php?post_type=geotr_cpt' ) . '">' . __( 'Create Redirection', 'geotr' ) . '</a>'
			),
			$links
		);

	}


}
