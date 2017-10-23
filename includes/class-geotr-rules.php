<?php
use Jaybizzle\CrawlerDetect\CrawlerDetect;
/**
 * Main Rules class
 *
 * @package    Geotr
 * @subpackage Geotr/includes
 */
class Geotr_Rules {

	private static $post_id;
	private static $detect;
	private static $referrer;
	private static $query_string;
	private static $is_category;
	private static $is_archive;
	private static $is_search;

	public static function init() {

		self::$post_id      = \GeotFunctions\grab_post_id();
		self::$detect       = new Mobile_Detect;
		self::$referrer     = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
		self::$query_string = isset( $_SERVER['QUERY_STRING'] ) ? $_SERVER['QUERY_STRING'] : '';

		if( defined('DOING_AJAX') ) {

			if ( isset( $_REQUEST['pid'] ) ) {
				self::$post_id = $_REQUEST['pid'];
			}
			if( !empty( $_REQUEST['referrer'] ) ) {
				self::$referrer = $_REQUEST['referrer'];
			}
			if( !empty( $_REQUEST['query_string'] ) ) {
				self::$query_string = $_REQUEST['query_string'];
			}
			if( !empty( $_REQUEST['is_category'] ) ) {
				self::$is_category = true;
			}
			if( !empty( $_REQUEST['is_archive'] ) ) {
				self::$is_archive = true;
			}
			if( !empty( $_REQUEST['is_search'] ) ) {
				self::$is_search = true;
			}
		}
		// Geotargeting
		add_filter( 'geotr/rules/rule_match/country', array( self::class, 'rule_match_country' ) );
		add_filter( 'geotr/rules/rule_match/country_region', array( self::class, 'rule_match_country_region' ) );
		add_filter( 'geotr/rules/rule_match/city', array( self::class, 'rule_match_city' ) );
		add_filter( 'geotr/rules/rule_match/city_region', array( self::class, 'rule_match_city_region' ) );
		add_filter( 'geotr/rules/rule_match/state', array( self::class, 'rule_match_state' ) );

		// User
		add_filter( 'geotr/rules/rule_match/user_type', array( self::class, 'rule_match_user_type') );
		add_filter( 'geotr/rules/rule_match/logged_user', array( self::class, 'rule_match_logged_user') );
		add_filter( 'geotr/rules/rule_match/left_comment', array( self::class, 'rule_match_left_comment') );
		add_filter( 'geotr/rules/rule_match/search_engine', array( self::class, 'rule_match_search_engine') );
		add_filter( 'geotr/rules/rule_match/same_site', array( self::class, 'rule_match_same_site') );

		// Post
		add_filter( 'geotr/rules/rule_match/post_type', array( self::class, 'rule_match_post_type') );
		add_filter( 'geotr/rules/rule_match/post_id', array( self::class, 'rule_match_post') );
		add_filter( 'geotr/rules/rule_match/post', array( self::class, 'rule_match_post') );
		add_filter( 'geotr/rules/rule_match/post_category', array( self::class, 'rule_match_post_category') );
		add_filter( 'geotr/rules/rule_match/post_format', array( self::class, 'rule_match_post_format') );
		add_filter( 'geotr/rules/rule_match/post_status', array( self::class, 'rule_match_post_status') );
		add_filter( 'geotr/rules/rule_match/taxonomy', array( self::class, 'rule_match_taxonomy') );

		// Page
		add_filter( 'geotr/rules/rule_match/page', array( self::class, 'rule_match_post') );
		add_filter( 'geotr/rules/rule_match/page_type', array( self::class, 'rule_match_page_type') );
		add_filter( 'geotr/rules/rule_match/page_parent', array( self::class, 'rule_match_page_parent') );
		add_filter( 'geotr/rules/rule_match/page_template', array( self::class, 'rule_match_page_template') );

		//Other
		add_filter( 'geotr/rules/rule_match/custom_url', array( self::class, 'rule_match_custom_url') );
		add_filter( 'geotr/rules/rule_match/mobiles', array( self::class, 'rule_match_mobiles') );
		add_filter( 'geotr/rules/rule_match/tablets', array( self::class, 'rule_match_tablets') );
		add_filter( 'geotr/rules/rule_match/desktop', array( self::class, 'rule_match_desktop') );
		add_filter( 'geotr/rules/rule_match/referrer', array( self::class, 'rule_match_referrer') );
		add_filter( 'geotr/rules/rule_match/crawlers', array( self::class, 'rule_match_crawlers') );
		add_filter( 'geotr/rules/rule_match/query_string', array( self::class, 'rule_match_query_string') );
	}

	/*
	*  check_rules
	*
	* @since 1.0.0
	*/
	public static function do_redirection( $rules = '' ) {
		if ( empty( $rules ) ) {
			return false;
		}

		$do_redirect = false;
		foreach ( $rules as $group_id => $group ) {

			$match_group = true;
			if ( is_array( $group ) ) {
				foreach ( $group as $rule_id => $rule ) {
					$match = apply_filters( 'geotr/rules/rule_match/' . $rule['param'], $rule );
					if ( ! $match ) {
						$match_group = false;
						// if one rule fails we don't need to check the rest of the rules in the group
						// that way if we add geo rules down it won't get executed and will save credits
						break;
					}
				}
			}
			// all rules must have matched!
			if ( $match_group ) {
				$do_redirect = true;
			}
		}

		return $do_redirect;
	}

	/**
	 * Hook each rule to a field to print
	 */
	public static function set_rules_fields() {
		// GEO
		add_action( 'geotr/rules/print_country_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_country_region_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_city_region_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_city_field', array( 'Geotr_Helper', 'print_textfield' ), 10, 2 );
		add_action( 'geotr/rules/print_state_field', array( 'Geotr_Helper', 'print_textfield' ), 10, 1 );

		// User
		add_action( 'geotr/rules/print_user_type_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_logged_user_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_left_comment_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_search_engine_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_same_site_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );

		// Post
		add_action( 'geotr/rules/print_post_type_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_post_id_field', array( 'Geotr_Helper', 'print_textfield' ), 10, 1 );
		add_action( 'geotr/rules/print_post_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_post_category_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_post_format_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_post_status_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_taxonomy_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );

		// Page
		add_action( 'geotr/rules/print_page_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_page_type_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_page_parent_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_page_template_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );

		//Other
		add_action( 'geotr/rules/print_custom_url_field', array( 'Geotr_Helper', 'print_textfield' ), 10, 1 );
		add_action( 'geotr/rules/print_mobiles_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_desktop_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_tablets_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_crawlers_field', array( 'Geotr_Helper', 'print_select' ), 10, 2 );
		add_action( 'geotr/rules/print_referrer_field', array( 'Geotr_Helper', 'print_textfield' ), 10, 1 );
		add_action( 'geotr/rules/print_query_string_field', array( 'Geotr_Helper', 'print_textfield' ), 10, 1 );
	}

	/**
	 * Rules options
	 * @return mixed
	 */
	public static function get_rules_choices() {
		$choices = array(
			__( "Geotargeting", 'geotr' ) => array(
				'country'        => __( 'Country', 'geotr' ),
				'country_region' => __( 'Country Region', 'geotr' ),
				'city'           => __( 'City', 'geotr' ),
				'city_region'    => __( 'City Region', 'geotr' ),
				'state'          => __( 'State', 'geotr' ),
			),
			__( "User", 'geotr' )         => array(
				'user_type'     => __( "User role", 'geotr' ),
				'logged_user'   => __( "User is logged", 'geotr' ),
				'left_comment'  => __( "User never left a comment", 'geotr' ) . ' *',
				'search_engine' => __( "User came via a search engine", 'geotr' ),
				'same_site'     => __( "User did not arrive via another page on your site", 'geotr' ),
			),
			__( "Post", 'geotr' )         => array(
				'post'          => __( "Post", 'geotr' ),
				'post_id'       => __( "Post ID", 'geotr' ),
				'post_type'     => __( "Post Type", 'geotr' ) ,
				'post_category' => __( "Post Category", 'geotr' ),
				'post_format'   => __( "Post Format", 'geotr' ) ,
				'post_status'   => __( "Post Status", 'geotr' ) ,
				'taxonomy'      => __( "Post Taxonomy", 'geotr' ),
			),
			__( "Page", 'geotr' )         => array(
				'page'          => __( "Page", 'geotr' ),
				'page_type'     => __( "Page Type", 'geotr' ),
				'page_parent'   => __( "Page Parent", 'geotr' ),
				'page_template' => __( "Page Template", 'geotr' ),
			),
			__( "Other", 'geotr' )        => array(
				'custom_url'   => __( "Custom Url", 'geotr' ),
				'referrer'     => __( "Referrer", 'geotr' ),
				'query_string' => __( "Query String", 'geotr' ),
				'mobiles'      => __( "Mobile Phone", 'geotr' ),
				'tablets'      => __( "Tablet", 'geotr' ),
				'desktop'      => __( "Dekstop", 'geotr' ),
				'crawlers'     => __( "Bots/Crawlers", 'geotr' ),
			)
		);

		// allow custom rules rules
		return apply_filters( 'geotr/metaboxes/rule_types', $choices );
	}


	/*
	* rule_match_country
	* @since 1.0.0
	*/
	public static function rule_match_country( $rule ) {

		$country_code = geot_country_code();

		if ( $rule['operator'] == "==" ) {
			return ( $country_code == $rule['value'] );
		}

		return ( $country_code != $rule['value'] );

	}

	/*
	* rule_match_country_region
	* @since 1.0.0
	*/
	public static function rule_match_country_region( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return ( geot_target('', $rule['value'] ) );
		}

		return ( ! geot_target('', $rule['value'] ) );

	}

	/*
		* rule_match_city
		* @since 1.0.0
		*/
	public static function rule_match_city( $rule ) {

		$city = geot_city_name();

		if ( $rule['operator'] == "==" ) {
			return ( strtolower($city) == strtolower($rule['value']) );
		}

		return ! ( strtolower($city) == strtolower($rule['value']) );

	}

	/*
	* rule_match_state
	* @since 1.0.0
	*/
	public static function rule_match_state( $rule ) {

		$state = geot_state_name();
		$state_code = geot_state_code();

		if ( $rule['operator'] == "==" ) {
			return ( strtolower($state) == strtolower($rule['value']) || strtolower($state_code) == strtolower($rule['value']) );
		}

		return ! ( strtolower($state) == strtolower($rule['value']) || strtolower($state_code) == strtolower($rule['value']) );

	}

	/*
	* rule_match_city_region
	* @since 1.0.0
	*/
	public static function rule_match_city_region( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return ( geot_target_city('', $rule['value'] ) );
		}

		return ( ! geot_target_city('', $rule['value'] ) );

	}

	/*
	*  rule_match_post
	*
	* @since 1.0.0
	*/
	public static function rule_match_post( $rule ) {

		$post_id = self::$post_id;

		if ( $rule['operator'] == "==" ) {
			return ( $post_id == $rule['value'] );
		}

		return ( $post_id != $rule['value'] );

	}

	/**
	 * [rule_match_logged_user description]
	 *
	 * @param  array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_logged_user( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return is_user_logged_in();
		}

		return ! is_user_logged_in();
	}

	/**
	 * [rule_match_mobiles description]
	 *
	 * @param  array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_mobiles( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return self::$detect->isMobile();
		}

		return ! self::$detect->isMobile();
	}

	/**
	 * [rule_match_tablets description]
	 *
	 * @param  array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_tablets( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return self::$detect->isTablet();
		}

		return ! self::$detect->isTablet();
	}

	/**
	 * [rule_match_desktop description]
	 *
	 * @param  array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_desktop( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return ( ! self::$detect->isTablet() && ! self::$detect->isMobile() );
		}

		return ( self::$detect->isTablet() || self::$detect->isMobile() );

	}

	/**
	 * [rule_match_left_comment description]
	 *
	 * @param  array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_left_comment( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return ! empty( $_COOKIE[ 'comment_author_' . COOKIEHASH ] );
		}

		return empty( $_COOKIE[ 'comment_author_' . COOKIEHASH ] );
	}

	/**
	 * [rule_match_search_engine description]
	 *
	 * @param  array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_search_engine( $rule ) {

		$ref = self::$referrer;

		$SE = apply_filters( 'geotr/rules/search_engines', array(
			'/search?',
			'.google.',
			'web.info.com',
			'search.',
			'del.icio.us/search',
			'soso.com',
			'/search/',
			'.yahoo.',
			'.bing.'
		) );
		foreach ( $SE as $url ) {
			if ( strpos( $ref, $url ) !== false ) {
				return $rule['operator'] == "==" ? true : false;
			}
		}

		return $rule['operator'] == "==" ? false : true;

	}

	/**
	 * Check for user referrer
	 *
	 * @param  array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_referrer( $rule ) {

		$ref = self::$referrer;

		if ( strpos( $ref, $rule['value'] ) !== false ) {
			return $rule['operator'] == "==" ? true : false;
		}

		return $rule['operator'] == "==" ? false : true;

	}

	/**
	 * Check for custom url
	 *
	 * @param  array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_custom_url( $rule ) {

		$current_url = \GeotFunctions\get_current_url();

		if( $rule['operator'] == "==" )
			return ($current_url == $rule['value']);

		return ! ($current_url == $rule['value']);

	}
	/**
	 * Check for crawlers / bots
	 *
	 * @param  array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_crawlers( $rule ) {

		$detect = new CrawlerDetect;

		if ( $rule['operator'] == "==" ) {
			return $detect->isCrawler();
		}

		return ! $detect->isCrawler();

	}

	/**
	 * Check for query string to see if matchs all given ones
	 *
	 * @param  array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_query_string( $rule ) {

		parse_str( str_replace( '?', '', self::$query_string ), $request );
		parse_str( $rule['value'], $rule_query );

		if ( is_array( $request ) && is_array( $rule_query ) ) {
			sort( $request );
			sort( $rule_query );
		}

		if ( $rule['operator'] == "==" ) {
			return ( $request == $rule_query );
		}

		return ( $request != $rule_query );

	}

	/**
	 * [rule_match_same_site description]
	 *
	 * @param  array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_same_site( $rule ) {

		$ref = self::$referrer;

		$internal = str_replace( array( 'http://', 'https://' ), '', home_url() );

		if ( $rule['operator'] == "==" ) {
			return ! preg_match( '~' . $internal . '~i', $ref );
		}

		return preg_match( '~' . $internal . '~i', $ref );


	}

	/*
	*  rule_match_post_type
	*
	* @since 1.0.0
	*/

	public static function rule_match_post_type( $rule ) {

		$post_type = get_post_type(self::$post_id);

		if ( $rule['operator'] == "==" ) {
			return ( $post_type === $rule['value'] );
		}

		return ( $post_type !== $rule['value'] );
	}

	/*
	*  rule_match_page_type
	*
	* @since 1.0.0
	*/

	public static function rule_match_page_type( $rule ) {


		$post        = get_post( self::$post_id );
		$post_parent = isset( $post->post_parent ) ? $post->post_parent : '';
		$post_type   = get_post_type(self::$post_id);

		if ( $rule['value'] == 'front_page' ) {

			$front_page = (int) get_option( 'page_on_front' );
			if ( $front_page !== 0 ) {
				if ( $rule['operator'] == "==" ) {
					return ( $front_page == self::$post_id );
				}

				return ( $front_page != self::$post_id );
			}

			if ( $rule['operator'] == "==" ) {
				return (home_url() == \GeotFunctions\get_current_url());
			}

			return ! (home_url() == \GeotFunctions\get_current_url());


		} elseif ( $rule['value'] == 'category_page' ) {
			if ( $rule['operator'] == "==" ) {
				return is_category();
			}

			return ! is_category();

		} elseif ( $rule['value'] == 'archive_page' ) {
			if ( $rule['operator'] == "==" ) {
				return is_archive();
			}

			return ! is_archive();
		} elseif ( $rule['value'] == 'search_page' ) {
			if ( $rule['operator'] == "==" ) {
				return is_search();
			}

			return ! is_search();
		} elseif ( $rule['value'] == 'posts_page' ) {

			$posts_page = (int) get_option( 'page_for_posts' );

			if ( $posts_page !== 0 ) {
				if ( $rule['operator'] == "==" ) {
					return ( $posts_page == self::$post_id );
				}

				return ( $posts_page != self::$post_id );
			} else {
				if ( $rule['operator'] == "==" ) {
					return is_home();
				}

				return ! is_home();
			}

		} elseif ( $rule['value'] == 'top_level' ) {
			if ( $rule['operator'] == "==" ) {
				return ( $post_parent == 0 );
			}

			return ( $post_parent != 0 );
		} elseif ( $rule['value'] == 'parent' ) {

			$children = get_pages( array(
				'post_type' => $post_type,
				'child_of'  => self::$post_id,
			) );

			if ( $rule['operator'] == "==" ) {
				return ( count( $children ) > 0 );
			}

			return ( count( $children ) == 0 );
		} elseif ( $rule['value'] == 'child' ) {
			if ( $rule['operator'] == "==" ) {
				return ( $post_parent != 0 );
			}

			return ( $post_parent == 0 );

		}

		return true;

	}


	/*
	*  rule_match_page_parent
	*
	* @since 1.0.0
	*/

	public static function rule_match_page_parent( $rule ) {

		// validation
		if ( ! self::$post_id ) {
			return false;
		}

		// vars
		$post = get_post( self::$post_id );

		$post_parent = $post->post_parent;

		if ( $rule['operator'] == "==" ) {
			return ( $post_parent == $rule['value'] );
		}

		return ( $post_parent != $rule['value'] );
	}


	/*
	*  rule_match_page_template
	*
	* @since 1.0.0
	*/

	public static function rule_match_page_template( $rule ) {

		$page_template = get_post_meta( self::$post_id, '_wp_page_template', true );

		if ( ! $page_template ) {
			if ( 'page' == get_post_type( self::$post_id ) ) {
				$page_template = "default";
			}
		}

		if ( $rule['operator'] == "==" ) {
			return ( $page_template === $rule['value'] );
		}

		return ( $page_template !== $rule['value'] );

	}


	/*
	*  rule_match_post_category
	*
	* @since 1.0.0
	*/

	public static function rule_match_post_category( $rule ) {

		if ( ! self::$post_id ) {
			return false;
		}

		// post type
		$post_type = get_post_type(self::$post_id );
		// vars
		$taxonomies = get_object_taxonomies( $post_type );

		$all_terms = get_the_terms( self::$post_id, 'category' );
		if ( $all_terms ) {
			foreach ( $all_terms as $all_term ) {
				$terms[] = $all_term->term_id;
			}
		}

		// no terms at all?
		if ( empty( $terms ) ) {
			// If no ters, this is a new post and should be treated as if it has the "Uncategorized" (1) category ticked
			if ( is_array( $taxonomies ) && in_array( 'category', $taxonomies ) ) {
				$terms[] = '1';
			}
		}


		if ( $rule['operator'] == "==" ) {
			return ( is_array( $terms ) && in_array( $rule['value'], $terms ) );
		}

		return ! ( is_array( $terms ) && in_array( $rule['value'], $terms ) );
	}


	/*
	*  rule_match_user_type
	*
	* @since 1.0.0
	*/

	public static function rule_match_user_type( $rule ) {
		$user = wp_get_current_user();

		if ( $rule['value'] == 'super_admin' ) {
			if ( $rule['operator'] == "==" )
				return is_super_admin( $user->ID );
			return ! is_super_admin( $user->ID );
		}
		if ( $rule['operator'] == "==" )
			return in_array( $rule['value'], $user->roles );

		return ! in_array( $rule['value'], $user->roles ) ;

	}

	/*
	*  rule_match_post_format
	*
	* @since 1.0.0
	*/

	public static function rule_match_post_format( $rule ) {
		if ( ! self::$post_id ) {
			return false;
		}

		$post_type = get_post_type(self::$post_id);

		// does post_type support 'post-format'
		if ( post_type_supports( $post_type, 'post-formats' ) ) {
			$post_format = get_post_format( self::$post_id );

			if ( $post_format === false ) {
				$post_format = 'standard';
			}

		}


		if ( $rule['operator'] == "==" ) {
			return ( $post_format === $rule['value'] );
		}

		return ( $post_format !== $rule['value'] );

	}


	/*
	*  rule_match_post_status
	*
	* @since 1.0.0
	*/

	public static function rule_match_post_status( $rule ) {
		if ( ! self::$post_id ) {
			return false;
		}
		// vars
		$post_status = get_post_status( self::$post_id );

		// auto-draft = draft
		if ( $post_status == 'auto-draft' ) {
			$post_status = 'draft';
		}

		// match
		if ( $rule['operator'] == "==" ) {
			return ( $post_status === $rule['value'] );
		}

		return ( $post_status !== $rule['value'] );

	}

	/*
	*  rule_match_taxonomy
	*
	* @since 1.0.0
	*/

	public static function rule_match_taxonomy( $rule ) {

		if ( ! self::$post_id ) {
			return false;
		}

		// post type
		$post_type = get_post_type(self::$post_id);

		// vars
		$taxonomies = get_object_taxonomies( $post_type );

		if ( is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $tax ) {
				$all_terms = get_the_terms( self::$post_id, $tax );
				if ( $all_terms ) {
					foreach ( $all_terms as $all_term ) {
						$terms[] = $all_term->term_id;
					}
				}
			}
		}

		// no terms at all?
		if ( empty( $terms ) ) {
			// If no ters, this is a new post and should be treated as if it has the "Uncategorized" (1) category ticked
			if ( is_array( $taxonomies ) && in_array( 'category', $taxonomies ) ) {
				$terms[] = '1';
			}

		}

		if ( $rule['operator'] == "==" ) {
			return ( is_array( $terms ) && in_array( $rule['value'], $terms ) );
		}

		return ! ( is_array( $terms ) && in_array( $rule['value'], $terms ) );

	}
}