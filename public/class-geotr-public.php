<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Geotr
 * @subpackage Geotr/public
 */
use GeotFunctions\Session\GeotSession;
use function GeotFunctions\textarea_to_array;
use function GeotWP\getUserIP;
use function GeotWP\is_session_started;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

/**
 * @package    Geotr
 * @subpackage Geotr/public
 * @author     Damian Logghe <damian@timersys.com>
 */
class Geotr_Public {
	/**
	 * @var Array of Redirection posts
	 */
	private $redirections;

	// Call geot once to init session handling
	// otherwise it will fail with georedirects and cache mode turned on
	public function init_geot(){
		geot();
	}

	public function handle_redirects(){

		Geotr_Rules::init();
		$this->redirections = $this->get_redirections();
		$opts = geotr_settings();
		if( !empty( $opts['ajax_mode'] ) )
			add_action( 'wp_footer', [ $this, 'ajax_placeholder' ] );
		else
			$this->check_for_rules();
	}


	/**
	 * Check for rules and redirect if needed
	 * This will be normal behaviour on site where cache is not active
	 */
	private function check_for_rules() {
		if( !empty($this->redirections) ) {
			foreach ( $this->redirections as $r ) {
				if( ! $this->pass_basic_rules($r) )
					continue;
				$rules = !empty($r->geotr_rules) ? unserialize($r->geotr_rules) : array();
				$do_redirect = Geotr_Rules::do_redirection( $rules );
				if ( $do_redirect ) {
					$this->perform_redirect( $r );
					break; // ajax mode won't redirect instantly so we need to break
				}
			}
		}
	}

	/**
	* Handle Ajax call for redirections, Basically
	 * we call normal redirect logic but cancel it and print results
	*/
	public function handle_ajax_redirects(){
		Geotr_Rules::init();
		$this->redirections = $this->get_redirections();
		add_filter('geotr/cancel_redirect', function( $redirect, $opts){
			echo apply_filters( 'geotr/ajax_cancel_redirect',json_encode($opts), $opts);
			return true;
		},15, 3);
		$this->check_for_rules();
		die();
	}

	/**
	 * Grab all redirections posts and associated rules
	 * @return mixed
	 */
	private function get_redirections() {
		global $wpdb;

		$sql = "SELECT ID, 
		MAX(CASE WHEN pm1.meta_key = 'geotr_rules' then pm1.meta_value ELSE NULL END) as geotr_rules,
		MAX(CASE WHEN pm1.meta_key = 'geotr_options' then pm1.meta_value ELSE NULL END) as geotr_options
        FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta pm1 ON ( pm1.post_id = p.ID)  WHERE post_type='geotr_cpt' AND post_status='publish' GROUP BY p.ID";

		$redirections = wp_cache_get(md5($sql), 'geotr_posts');
		if( $redirections === false) {
			$redirections = $wpdb->get_results($sql, OBJECT );
			wp_cache_add (md5($sql), $redirections, 'geotr_posts');
		}
		return $redirections;
	}

	/**
	 * Before Even checking rules, we need some basic validation
	 *
	 * @param $redirection
	 *
	 * @return bool
	 */
	private function pass_basic_rules( $redirection ) {
		if( empty( $redirection->geotr_options ) )
			return false;

		$opts = maybe_unserialize($redirection->geotr_options);

		$current_url = \GeotFunctions\get_current_url();

		// check for destination url
		if( empty( $opts['url'] ) || $current_url == $this->replaceShortcodes($opts, true) )
			return false;

		// check for crawlers
		if( isset($opts['exclude_se']) && 1 === absint( $opts['exclude_se'] ) ) {
			$detect = new CrawlerDetect();

			if( $detect->isCrawler() )
				return false;
		}

		// check user IP
		if( !empty($opts['whitelist']) && $this->user_is_whitelisted( $opts['whitelist'] ) )
			return false;

		return true;
	}

	/**
	 * Perform the actual redirection
	 * @param $redirection
	 */
	private function perform_redirect( $redirection ) {
		$opts = maybe_unserialize($redirection->geotr_options);
		// redirect one time uses cookies
		if( (int)$opts['one_time_redirect'] === 1 ){
			if( isset( $_COOKIE['geotr_redirect_'.$redirection->ID]) )
				return;
			setcookie( 'geotr_redirect_'.$redirection->ID, true, time() + apply_filters('geotr/cookie_expiration', YEAR_IN_SECONDS),'/');
		}

		// redirect 1 per session
		if( (int)$opts['one_time_redirect'] === 2 ){
            $session = geot()->getSession();

			if( !empty($session->get('geotr_redirect_'.$redirection->ID) ) )
				return;
            $session->set('geotr_redirect_'.$redirection->ID, true);
		}

		// status code is set?
		if( !isset($opts['status']) || ! is_numeric($opts['status']))
			$opts['status'] = 302;

		$opts['url'] = $this->replaceShortcodes($opts);
		$opts['url'] = $this->fixRedirect($opts['url']);

		//last chance to abort
		if( ! apply_filters('geotr/cancel_redirect', false, $opts, $redirection) ) {
			wp_redirect(apply_filters('geotr/final_url', $opts['url']), $opts['status']);
			exit;
		}
	}

	/**
	*	Verify if the URL has protocol
	*/
	public function fixRedirect($redirect) {

		$site = preg_replace('#^https?://#', '', site_url());

		$site_scheme = parse_url(site_url(), PHP_URL_SCHEME);
		$redirect_scheme = parse_url($redirect, PHP_URL_SCHEME);

		if( strpos( $redirect, $site) !== FALSE && $site_scheme != $redirect_scheme ) { //internal URL
			$redirect = str_replace($redirect_scheme, $site_scheme, $redirect);
		}
		elseif(empty($redirect_scheme)) { //external URL without scheme
			$redirect = 'http://' . $redirect;
		}

		return $redirect;
	}

	/**
	 * Enqueue script file
	 */
	public function enqueue_scripts(){
		wp_enqueue_script( 'geotr-js',  plugins_url( 'js/geotr-public.js', __FILE__ ), array( 'jquery' ), GEOTR_VERSION, true );
		wp_localize_script( 'geotr-js', 'geotr', [
			'ajax_url'						=> admin_url('admin-ajax.php'),
			'pid'						    => get_queried_object_id(),
			'is_front_page'				    => is_front_page(),
			'is_category'				    => is_category(),
			'site_url'				        => site_url(),
			'is_archive'				    => is_archive(),
			'is_search'				        => is_search()
		]);
	}

	/**
	 * Check if current user IP is whitelisted
	 *
	 * @param $ips
	 *
	 * @return bool
	 */
	private function user_is_whitelisted( $ips ) {
		$ips = textarea_to_array( $ips );
		if( in_array( apply_filters( 'geot/user_ip', getUserIP()), apply_filters( 'geotr/whitelist_ips', $ips ) ) )
			return true;
		return false;
	}

	/**
	 * Print placeholder in front end
	 */
	public static function ajax_placeholder(){
		?><!-- Geo Redirects plugin https://geotargetingwp.com-->
		<div class="geotr-ajax" style="display: none">
			<div>
				<?php do_action('geotr/ajax_placeholder');?>
				<img src="<?php echo plugin_dir_url(__FILE__);?>img/loading.svg" alt="loading"/>
				<p><?php _e('Please wait while you are redirected to the right page...', 'geotr');?></p>
			</div>
		</div>
		<style>
			<?php do_action('geotr/ajax_placeholder_styles');?>
			.geotr-ajax{
				position: fixed;
				width: 100%;
				height: 100%;
				background: #fff;
				top: 0;
				left: 0;
				z-index: 9999999999;
				color: #000;
			}
			.geotr-ajax img{
				display: block;
				margin: auto;
			}
			.geotr-ajax div{
				position: absolute;
				top:0;
				bottom: 0;
				left: 0;
				right: 0;
				margin: auto;
				width: 320px;
				height: 140px;
				font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
				text-align: center;
			}
		</style>
		<?php
	}

		/**
		 * Replace shortcodes on url
		 *
		 * @param $opts
		 *
		 * @param bool $basic_rules When calling this func from basic rules we don't need to execute geolocation or will consume extra credits
		 *
		 * @return mixed
		 */
	private function replaceShortcodes( $opts , $basic_rules = false ) {
		$url = defined('DOING_AJAX') && isset($_REQUEST['url']) ? $_REQUEST['url'] : ( (is_ssl() ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" );

		// remove query string from URL
		$query_string = parse_url($url, PHP_URL_QUERY);
        $url = str_replace('?'.$query_string,'',$url);

		$replaces = [
			'{{requested_uri}}' => trim($url,'/') ?: '',
			'{{requested_path}}' => trim(parse_url($url, PHP_URL_PATH),'/') ?: '',
		];
		if( ! $basic_rules ) {
			$replaces['{{country_code}}']  = geot_country_code();
			$replaces['{{state_code}}']    = geot_state_code();
			$replaces['{{zip}}']           = geot_zip();
		}
		// do the replaces
		$replaces = apply_filters('geotr/placeholders', array_map('strtolower', $replaces) );
		$final_url = str_replace(array_keys($replaces), array_values($replaces), $opts['url']);
		// add back query string
        if( isset($opts['pass_query_string']) && $opts['pass_query_string'] == 1 && !empty($query_string) ){
        	// check if a query string already exist in final url
        	if( strpos($final_url, '?') !== false ){
		        return $final_url . '&'. $query_string;
	        } else {
		        return $final_url . '?'. $query_string;
	        }
        }

        return apply_filters('geotr/shortcodes_url',$final_url, $opts, $url );
    }

}