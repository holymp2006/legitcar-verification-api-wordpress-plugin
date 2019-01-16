<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://legitcar.ng
 * @since      1.0.0
 *
 * @package    LegitCar_API_Client
 * @subpackage LegitCar_API_Client/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    LegitCar_API_Client
 * @subpackage LegitCar_API_Client/public
 * @author     Samuel Ogbujimma <sam@legitcar.ng>
 */
class LegitCar_API_Client_Public
{

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
	 * @param      string    $legitcar_api_client       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($legitcar_api_client, $version)
	{

		$this->legitcar_api_client = $legitcar_api_client;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

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

		wp_enqueue_style($this->legitcar_api_client, plugin_dir_url(__FILE__) . 'css/legitcar-api-client-public.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

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

		wp_enqueue_script($this->legitcar_api_client, plugin_dir_url(__FILE__) . 'js/legitcar-api-client-public.js', array('jquery'), $this->version, false);

		wp_localize_script($this->legitcar_api_client, 'legitcar_data', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'verification_url' => $this->verificationUrl()
		));
	}

	protected function email()
	{
		return LEGITCAR_EMAIL;
	}
	protected function password()
	{
		return LEGITCAR_PASSWORD;
	}
	protected function cacheDuration()
	{
		return LEGITCAR_CACHE_DURATION;
	}
	protected function baseUrl()
	{
		return 'http://127.0.0.1:8000/api';
	}
	protected function unauthorised($response)
	{
		return strpos(wp_remote_retrieve_response_code($response['response']['code']), '401');
	}
	protected function getToken()
	{
		// todo: inspect cache. it's not working
		if (!$response = wp_cache_get('LEGITCAR_TOKEN', 'LEGITCAR')) {
			$response = wp_remote_post($this->baseUrl() . '/user/login', [
				'headers' => [
					'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
					'Cache-Control' => 'no-cache'
				],
				'body' => [
					'email' => $this->email(),
					'password' => $this->password()
				]
			]);
			if ($this->unauthorised($response) || is_wp_error($response)) {
				//401 UNAUTHORIZED || WP error
				return false;
			}
			//cache token for 540 seconds (9mins)
			wp_cache_set('LEGITCAR_TOKEN', $response, 'LEGITCAR', 540);
		}

		print_r($response['body']);
		die();
		return $response['body']['token'];
	}
	protected function isValidVin($vin)
	{
		//empty
		if (empty($vin)) {
			return false;
		}
		//not equal to 17 chars
		if (strlen(trim($vin)) != 17) {
			return false;
		}
		//incorrect vin
		if (!preg_match('/^[^IO]+$/i', $vin)) {
			return false;
		}
		//passed successfully
		return $vin;
	}
	public function verify()
	{
		$vin = $_REQUEST['vin'];
		if (!defined('DOING_AJAX') || !DOING_AJAX) {
			die();
		}
		if (!$this->isValidVin($vin)) {
			//failed validation. tell frontend.
			status_header(400);
			exit;
		}
		// if (!$response = wp_cache_get('LEGITCAR_' . $vin, 'LEGITCAR')) {
			//we couldn't get result from cache, make external request
		if (!$token = $this->getToken()) {
				//failed to get token. tell frontend.
			status_header(401);
				//header("HTTP/1.1 401 Unauthorized");
			exit;
		}
		echo $token;
		die();
		$response = wp_remote_post($this->baseUrl() . '/vehicle/verify', [
			'headers' => [
				'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
				'Cache-Control' => 'no-cache',
				'Authorization' => 'Bearer ' . $token
			],
			'body' => [
				'vin' => $vin
			]
		]);
		if (is_wp_error($response)) {
				//WP error
			status_header(400);
				// wp_send_json_error()
			exit;
		}
			//cache result of search for set duration
		wp_cache_set('LEGITCAR_' . $vin, $response, 'LEGITCAR', $this->cacheDuration());
		// }
		$this->saveToSession($response['body']);
		wp_send_json_success($response);
	}
	protected function saveToSession($data)
	{
		if (!session_id()) {
			session_start();
		}
		$_SESSION['LEGITCAR_VERIFICATION_RESULT'] = $data;
	}
	protected function verificationUrl()
	{
		return 'legitcar_verify';
	}
	/**
	 * Register shortcodes for the public-facing side of the site.
	 *
	 * @return void
	 */
	public function shortcodes()
	{
		add_shortcode('legitcar_verification_form', array($this, 'verificationForm'));
		add_shortcode('legitcar_verification_result', array($this, 'verificationResult'));
	}
	/**
	 * define routes to be used by js frontend
	 *
	 * @return void
	 */
	public function apiRoutes()
	{
		add_action('wp_ajax_nopriv_' . $this->verificationUrl(), array($this, 'verify'));
		add_action('wp_ajax_' . $this->verificationUrl(), array($this, 'verify'));
	}
	public function verificationResult()
	{
		if (!isset($_SESSION['LEGITCAR_VERIFICATION_RESULT'])) {
			return 'No Result';
		}
		$result = $_SESSION['LEGITCAR_VERIFICATION_RESULT'];

	}
	public function verificationForm($params)
	{
		extract(shortcode_atts(array(
			'form_action' => 'verified',
			'form_classes' => 'legitcar-verification-form',
			'label' => false,
			'placeholder' => 'Enter VIN',
			'submit_text' => 'Verify'
		), $params));

		ob_start(); ?>

		<form id="legitcar-verification-form" class="<?= $form_classes ?>" action="#" data-action="<?= home_url() . '/' . $form_action ?>" method="post">
			<label><?= $label ?></label>
			<input name="legitcar_verification_vin" type="text" id="legitcar-verification-vin" placeholder="<?= $placeholder ?>">

			<input name="legitcar_verification_submit" id="legitcar-verification-submit" type="submit" value="<?= $submit_text ?>">
		</form>
	
	<?php return ob_get_clean();
}

}
