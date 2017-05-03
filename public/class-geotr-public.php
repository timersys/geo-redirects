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
use function GeotFunctions\textarea_to_array;
use function GeotWP\getUserIP;
use function GeotWP\is_session_started;

/**
 * @package    Geotr
 * @subpackage Geotr/public
 * @author     Damian Logghe <damian@timersys.com>
 */
class Geotr_Public {

	public function handle_redirects(){
		global $wp_query,$post;

		$post_type = isset( $wp_query->query_vars['post_type'] ) ? $wp_query->query_vars['post_type'] : '';

		$post_type = empty( $post_type ) ? get_post_type($post->ID) : get_post_type();


		var_dump($post_type);die();
		Geotr_Rules::init();
		$redirections = $this->get_redirections();
		if( !empty($redirections) ) {
			foreach ( $redirections as $r ) {
				$rules = !empty($r->geotr_rules) ? unserialize($r->geotr_rules) : array();
				$do_redirect = Geotr_Rules::do_redirection( $rules );
				if ( $do_redirect )
					$this->perform_redirect($r);
			}
		}
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
	 * Perform the actual redirection
	 * @param $redirection
	 */
	private function perform_redirect( $redirection ) {

		if( empty( $redirection->geotr_options ) )
			return;

		$opts = maybe_unserialize($redirection->geotr_options);

		if( empty( $opts['url'] ) )
			return;

		// check user IP
		if( !empty($opts['whitelist']) && $this->user_is_whitelisted( $opts['whitelist'] ) )
			return;

		// redirect one time uses cookies
		if( (int)$opts['one_time_redirect'] === 1 ){
			if( isset( $_COOKIE['geotr_redirect_'.$redirection->ID]) )
				return;
			setcookie( 'geotr_redirect_'.$redirection->ID, true, time() + apply_filters('geotr/cookie_expiration', YEAR_IN_SECONDS),'/');
		}

		// redirect 1 per session
		if( (int)$opts['one_time_redirect'] === 2 ){
			if( ! is_session_started() )
				session_start();

			if( isset( $_SESSION['geotr_redirect_'.$redirection->ID]) )
				return;
			$_SESSION['geotr_redirect_'.$redirection->ID] = true;
		}

		// status code is set?
		if( !isset($opts['status']) || ! is_numeric($opts['status']))
			$opts['status'] = 302;

		echo '<pre>';
		var_dump($opts);
		echo '</pre>';
		die();
		#wp_redirect($opts['url'], $opts['status']);
		exit;
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
		if( in_array( getUserIP(), apply_filters( 'geotr/whitelist_ips', $ips ) ) )
			return true;
		return false;
	}

}
