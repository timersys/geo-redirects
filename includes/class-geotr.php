<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Geotr
 * @subpackage Geotr/includes
 */
use GeotFunctions\Setting\GeotSettings;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 *
 * @since      1.0.0
 * @package    Geotr
 * @subpackage Geotr/includes
 * @author     Damian Logghe <damian@timersys.com>
 */
class Geotr {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Geotr_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Public Class instance
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      Geotr_Public    $public Public class instance
	 */
	public $public;

	/**
	 * Admin Class instance
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      Geotr_Admin    $public Admin class instance
	 */
	public $admin;

	/**
	 * Plugin Instance
	 * @since 1.0.0
	 * @var The Fbl plugin instance
	 */
	protected static $_instance = null;

	/**
	 * Main plugin_name Instance
	 *
	 * Ensures only one instance of WSI is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Geotr()
	 * @return plugin_name - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 * @param mixed $key
	 * @since 1.0.0
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( in_array( $key, array( 'payment_gateways', 'shipping', 'mailer', 'checkout' ) ) ) {
			return $this->$key();
		}
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'geotr';
		$this->version = GEOTR_VERSION;

		$this->load_dependencies();
		GeotSettings::init();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_global_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-geotr-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-geotr-rules.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-geotr-helper.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-geotr-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/class-geotr-metaboxes.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-geotr-public.php';

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Geotr_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Geotr_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		add_action( 'plugins_loaded', array( $plugin_i18n, 'load_plugin_textdomain' ) );

	}

	/**
	 * Register all of the hooks that run globally
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_global_hooks() {

		add_action( 'init', array( $this, 'register_cpt' ) );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$this->admin = new Geotr_Admin( $this->get_plugin_name(), $this->get_version() );
		$metaboxes = new Geotr_Metaboxes( $this->get_plugin_name(), $this->get_version() );

		Geotr_Rules::set_rules_fields();

		add_filter('geot/plugin_version', function (){ return GEOTR_VERSION;});
		
		add_filter( 'plugin_action_links_' . GEOTR_PLUGIN_HOOK, array( $this->admin, 'add_action_links' ) );
		add_action( 'add_meta_boxes_geotr_cpt', array( $metaboxes, 'add_meta_boxes' ) );
		add_action( 'save_post_geotr_cpt', array( $metaboxes, 'save_meta_options' ) );

		add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_scripts' ) );

		// Settings
		add_action( 'admin_menu' , [ $this->admin, 'add_settings_menu' ],8);
		add_action( 'admin_init', [ $this->admin, 'save_settings' ] );

		//AJAX Actions
		add_action('wp_ajax_geotr/field_group/render_rules', array( 'Geotr_Helper', 'ajax_render_rules' ) );
		add_action('wp_ajax_geotr/field_group/render_operator', array( 'Geotr_Helper', 'ajax_render_operator' ) );

		add_filter( 'manage_edit-geotr_cpt_columns' ,  array( $this->admin, 'set_custom_cpt_columns'), 10, 2 );
		add_action( 'manage_geotr_cpt_posts_custom_column' ,  array( $this->admin, 'custom_columns'), 10, 2 );

		// License and Updates
		add_action( 'admin_init' , [ $this->admin, 'handle_updates'], 0 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$this->public = new Geotr_Public();
		add_action('plugins_loaded', array( $this->public, 'init_geot' ) ,-2);
		$action_hook = defined('WP_CACHE') ? 'init' : 'wp';
		if( ! is_admin() && ! $this->is_backend() && ! defined('DOING_AJAX') && ! defined('DOING_CRON') )
            add_action( apply_filters('geotr/action_hook',$action_hook), array( $this->public, 'handle_redirects' ) );
		add_action( 'wp_enqueue_scripts', array( $this->public, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_nopriv_geo_redirects', array( $this->public, 'handle_ajax_redirects' ),1 );
		add_action( 'wp_ajax_geo_redirects', array( $this->public, 'handle_ajax_redirects' ),1 );
	}

	/**
	 * Check if we are trying to login
	 * @return bool
	 */
	private function is_backend(){
		$ABSPATH_MY = str_replace(array('\\','/'), DIRECTORY_SEPARATOR, ABSPATH);
		return ((in_array($ABSPATH_MY.'wp-login.php', get_included_files()) || in_array($ABSPATH_MY.'wp-register.php', get_included_files()) ) || $GLOBALS['pagenow'] === 'wp-login.php' || $_SERVER['PHP_SELF']== '/wp-login.php');
	}


	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register custom post types
	 * @since     1.0.0
	 * @return void
	 */
	public function register_cpt() {

		$labels = array(
			'name'               => 'Geo Redirects v'.GEOTR_VERSION,
			'singular_name'      => _x( 'Geo Redirects', 'post type singular name', 'popups' ),
			'menu_name'          => _x( 'Geo Redirects', 'admin menu', 'popups' ),
			'name_admin_bar'     => _x( 'Geo Redirects', 'add new on admin bar', 'popups' ),
			'add_new'            => _x( 'Add New', 'Geo Redirection', 'popups' ),
			'add_new_item'       => __( 'Add New Geo Redirection', 'popups' ),
			'new_item'           => __( 'New Geo Redirection', 'popups' ),
			'edit_item'          => __( 'Edit Geo Redirection', 'popups' ),
			'view_item'          => __( 'View Geo Redirection', 'popups' ),
			'all_items'          => __( 'Geo Redirects', 'popups' ),
			'search_items'       => __( 'Search Geo Redirection', 'popups' ),
			'parent_item_colon'  => __( 'Parent Geo Redirection:', 'popups' ),
			'not_found'          => __( 'No Geo Redirection found.', 'popups' ),
			'not_found_in_trash' => __( 'No Geo Redirection found in Trash.', 'popups' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'geot-settings',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'geotr_cpt' ),
			'capability_type'    => 'post',
			'capabilities' => array(
		        'publish_posts' 		=> apply_filters( 'geotr/settings_page/roles', 'manage_options'),
		        'edit_posts' 			=> apply_filters( 'geotr/settings_page/roles', 'manage_options'),
		        'edit_others_posts' 	=> apply_filters( 'geotr/settings_page/roles', 'manage_options'),
		        'delete_posts' 			=> apply_filters( 'geotr/settings_page/roles', 'manage_options'),
		        'delete_others_posts' 	=> apply_filters( 'geotr/settings_page/roles', 'manage_options'),
		        'read_private_posts' 	=> apply_filters( 'geotr/settings_page/roles', 'manage_options'),
		        'edit_post' 			=> apply_filters( 'geotr/settings_page/roles', 'manage_options'),
		        'delete_post' 			=> apply_filters( 'geotr/settings_page/roles', 'manage_options'),
		        'read_post' 			=> apply_filters( 'geotr/settings_page/roles', 'manage_options'),
		    ),
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 10,
			'supports'           => array( 'title' )
		);

		register_post_type( 'geotr_cpt', $args );

	}

}
