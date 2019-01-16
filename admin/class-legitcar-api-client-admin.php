<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://legitcar.ng
 * @since      1.0.0
 *
 * @package    LegitCar_API_Client
 * @subpackage LegitCar_API_Client/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    LegitCar_API_Client
 * @subpackage LegitCar_API_Client/admin
 * @author     Samuel Ogbujimma <sam@legitcar.ng>
 */
class LegitCar_API_Client_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $legitcar_api_client    The ID of this plugin.
	 */
	private $legitcar_api_client;

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
	 * @param      string    $legitcar_api_client       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $legitcar_api_client, $version ) {

		$this->legitcar_api_client = $legitcar_api_client;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in LegitCar_API_Client_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The LegitCar_API_Client_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->legitcar_api_client, plugin_dir_url( __FILE__ ) . 'css/legitcar-api-client-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in LegitCar_API_Client_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The LegitCar_API_Client_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->legitcar_api_client, plugin_dir_url( __FILE__ ) . 'js/legitcar-api-client-admin.js', array( 'jquery' ), $this->version, false );

	}

}
