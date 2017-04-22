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
     * Include the metabox view for rules
     * @param  object $post    geotrcpt post object
     * @param  array $metabox full metabox items array
     * @since 1.0.0
     */
    public function geotr_opts( $post, $metabox ) {

        $groups = apply_filters('geotr/metaboxes/get_rules', Geotr_Helper::get_rules( $post->ID ), $post->ID);

        include GEOTR_DIR . '/admin/partials/metaboxes/opts.php';
    }

}
