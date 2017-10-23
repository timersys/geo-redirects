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
use GeotFunctions\GeotUpdates;

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
	 * Add menu for Settings page of the plugin
	 * @since  1.0.3
	 * @return  void
	 */
	public function add_settings_menu() {

		add_submenu_page( 'geot-settings', 'Redirects Settings', 'Redirects Settings', apply_filters( 'geotr/settings_page_role', 'manage_options'), 'geotr-settings',array($this, 'settings_page') );
	}

	/**
	 * Settings page for plugin
	 * @since 1.0.3
	 */
	public function settings_page() {
		$defaults = [
			'ajax_mode'                 => '0',
		];
		$opts = wp_parse_args( geotr_settings(),  $defaults );
		include  dirname( __FILE__ )  . '/partials/settings-page.php';
	}
	/**
	 * Save Settings page
	 * @since 1.0.3
	 */
	function save_settings(){
		if (  isset( $_POST['geot_nonce'] ) && wp_verify_nonce( $_POST['geot_nonce'], 'geotr_save_settings' ) ) {
			$settings = isset($_POST['geotr_settings']) ? esc_sql( $_POST['geotr_settings'] ) : '';

			update_option( 'geotr_settings' ,  $settings);
		}
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

	/**
	 * Add callbacks for custom colums
	 * @param  array $column  [description]
	 * @param  int $post_id [description]
	 * @return echo html
	 * @since  1.2
	 */
	function custom_columns( $column, $post_id ) {
		global $wpdb;

		$opts =  Geotr_Helper::get_options($post_id);

		switch ( $column ) {

			case 'url' :
				echo esc_attr($opts['url']);
				break;
		}
	}

	/**
	 * Handle Licences and updates
	 * @since 1.0.0
	 */
	public function handle_updates(){
		$opts = geot_settings();
		// Setup the updater
		return new GeotUpdates( GEOTR_PLUGIN_FILE, [
				'version'   => $this->version,
				'license'   => isset($opts['license']) ?$opts['license'] : ''
			]
		);
	}

	/**
	 * Add custom columns to cpt
	 *
	 * @param [type] $columns [description]
	 *
	 * @since  1.2
	 * @return mixed
	 */
	public function set_custom_cpt_columns( $columns ){
		$new_column = [];

		foreach ($columns as $key => $value ){
			if( $key == 'date')
				$new_column['url']        = __( 'Destination URL', 'geotr' );
			$new_column[$key] = $value;
		}

		return $new_column;
	}
}
