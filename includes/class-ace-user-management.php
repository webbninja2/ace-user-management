<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_User_Management
 * @subpackage Ace_User_Management/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ace_User_Management
 * @subpackage Ace_User_Management/includes
 * @author     Webbninja <webbninja2@gmail.com>
 */
class Ace_User_Management {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ace_User_Management_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ace-user-management';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ace_User_Management_Loader. Orchestrates the hooks of the plugin.
	 * - Ace_User_Management_i18n. Defines internationalization functionality.
	 * - Ace_User_Management_Admin. Defines all hooks for the admin area.
	 * - Ace_User_Management_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ace-user-management-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ace-user-management-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ace-user-management-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ace-user-management-public.php';

		/**
		 * The class responsible for defining all the static function
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ace-user-management-function.php';

		$this->loader = new Ace_User_Management_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ace_User_Management_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Ace_User_Management_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ace_User_Management_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'ace_register_options_page' );

		$this->loader->add_action( 'show_user_profile', $plugin_admin,'ace_additional_profile_fields' );
		$this->loader->add_action( 'edit_user_profile', $plugin_admin,'ace_additional_profile_fields' );
		$this->loader->add_action( 'personal_options_update',$plugin_admin, 'ace_user_interests_fields_save');

        $this->loader->add_action( 'wp_ajax_delete_user',$plugin_admin, 'ace_delete_user' );
        $this->loader->add_action( 'wp_ajax_nopriv_delete_user',$plugin_admin, 'ace_delete_user' );
        $this->loader->add_action( 'init', $plugin_admin, 'language_load_textdomain');
  	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ace_User_Management_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_logout', $plugin_public, 'ace_custom_logout_page' );

		$this->loader->add_filter( 'login_redirect', $plugin_public, 'ace_my_login_redirect', 10, 3);
		$this->loader->add_action( 'wp', $plugin_public, 'ace_page_load_action_hooks' );
		$this->loader->add_action( 'init', $plugin_public, 'ace_subscriber_login' );
		
		$this->loader->add_action( 'template_redirect', $plugin_public, 'ace_redirect_to_specific_page' );
		$this->loader->add_filter( 'register',$plugin_public,'ace_register_url' );
		
		$this->loader->add_filter( 'wp_nav_menu_items',$plugin_public, 'ace_loginout_menu_link', 10, 2);
		$this->loader->add_action( 'wp_authenticate', $plugin_public, 'ace_catch_empty_user', 1, 2 );
		$this->loader->add_filter( 'show_admin_bar', $plugin_public, 'ace_show_admin_bar_status' );
		$this->loader->add_action( 'parse_query', $plugin_public, 'ace_pages_permalink' );

	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
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
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ace_User_Management_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
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

}