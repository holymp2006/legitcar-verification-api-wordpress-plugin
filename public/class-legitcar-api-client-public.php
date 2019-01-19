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
		if (!is_array($response) || !isset($response['response']) || !isset($response['response']['code'])) {
			//$response is not structured how we want
			return false;
		}
		return strpos(wp_remote_retrieve_response_code($response['response']['code']), '401');
	}
	protected function getToken()
	{
		if (!$response = get_transient('LEGITCAR_TOKEN')) {
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
			if (is_wp_error($response) || $this->unauthorised($response)) {
				// WP error || 401 UNAUTHORIZED
				return false;
			}
			//cache token for 540 seconds (9mins), using wp transients
			set_transient('LEGITCAR_TOKEN', $response, 540);
		}

		return json_decode($response['body'])->token;
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
		if (!defined('DOING_AJAX') || !DOING_AJAX) {
			die();
		}
		$vin = $_REQUEST['vin'];
		if (!$this->isValidVin($vin)) {
			//failed validation. tell frontend.
			wp_send_json_error('invalid vin', 400);
		}

		if (!$response = get_transient('LEGITCAR_' . $vin)) {
			//we couldn't get result from cache, make external request
			if (!$token = $this->getToken()) {
				//failed to get token. tell frontend.
				wp_send_json_error('error getting token for your request. please try again later', 401);
			}

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
				wp_send_json_error('error completing your request. please try again later', 422);
			}
			//cache result of search for set duration
			set_transient('LEGITCAR_' . $vin, $response, $this->cacheDuration());
		}

		$response = json_decode($response['body']);
		$this->saveToSession($response);
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
	protected function containsShortcodeAndParam($array)
	{
		return (!empty($array[2]) &&
			in_array('legitcar_verification_result', $array[2]) &&
			!empty($array[3]) && strpos($array[3][0], 'strict'));
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
	/**
	 * define miscellaneous actions
	 *
	 * @return void
	 */
	public function miscActions()
	{
		add_action('template_redirect', array($this, 'force404'));
	}
	public function force404()
	{
		if (!is_singular()) return;
		global $post;
		if (!empty($post->post_content)) {
			$regex = get_shortcode_regex();
			preg_match_all('/' . $regex . '/', $post->post_content, $matches);
			if ($this->containsShortcodeAndParam($matches) && !isset($_SESSION['LEGITCAR_VERIFICATION_RESULT'])) {
				global $wp_query;
				$wp_query->set_404();
				status_header(404);
				get_template_part(404);
				exit;
			}
		}
	}
	public function verificationResult($params)
	{
		extract(shortcode_atts(array(
			'strict' => null
		), $params));

		session_start(array('read_and_close' => true));
		if (!isset($_SESSION['LEGITCAR_VERIFICATION_RESULT'])) {
			return 'No Result';
			exit;
		}
		$result = json_decode(json_encode($_SESSION['LEGITCAR_VERIFICATION_RESULT']), true);
		if (empty($result)) {
			return 'No Result';
			exit;
		}

		return $this->verificationResultHTML($result);
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

		<form id="legitcar-verification-form" class="<?= $form_classes ?>" action="#" data-url="<?= home_url() . '/' . $form_action ?>" method="post">
			<label><?= $label ?></label>
			<input name="legitcar_verification_vin" type="text" id="legitcar-verification-vin" placeholder="<?= $placeholder ?>">

			<input name="legitcar_verification_submit" id="legitcar-verification-submit" type="submit" value="<?= $submit_text ?>">
		</form>
	
	<?php return ob_get_clean();
}
/**
 * Structure the html to display as verification result.
 * You can structure the html however you want.
 * For simplicty, we've used <ul> tags.
 * The variable '$array' is the result of the VIN verification -
 * as seen in the LegitCar API documentation - converted to array.
 *
 * @param array $array
 * @return void
 */
protected function verificationResultHTML($array)
{
	ob_start(); //don't touch this line
	?>

	<div class="legitcar-verification-result">
		<ul><?php 
		foreach ($array as $key => $value):
			if(is_string($value)): ?>
			<li>
				<span>
					<?= ucwords(str_replace("_", " ", $key)); ?>:
				</span><?= $value ?>
			</li>
		<?php 		
			endif;
		endforeach;
		
		?></ul>
		<?php 
		if(is_array($array['vehicle'])): ?>
			<ul>
				<?php 
				foreach ($array['vehicle'] as $key => $value):
					if(is_string($value)): ?>
						<li>
							<span>
								<?= ucwords(str_replace("_", " ", $key)); ?>:
							</span><?= $value ?>
						</li>
					<?php 
					endif;
				endforeach; ?>		
			</ul>
			<?php if(isset($array['vehicle']['decoded_details'])): ?>
				<h4>Decoded</h4>
				<ul>
					<?php 
					foreach ($array['vehicle']['decoded_details'] as $key => $value):
						if(is_string($value) && !empty($value)): ?>
							<li>
								<span>
									<?= ucwords(str_replace("_", " ", $key)); ?>:
								</span><?= $value ?>
							</li>
						<?php 
						endif;
					endforeach; ?>		
				</ul>
			<?php endif; ?>
		<?php endif; ?>
	</div>

<?php return ob_get_clean(); //don't touch this line
}

}
