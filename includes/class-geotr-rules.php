<?php

/**
 * Main Rules class
 *
 * @package    Geotr
 * @subpackage Geotr/includes
 */
class Geotr_Rules {

    /**
	 * Hook each rule to a field to print
	 */
	public static function set_rules_fields() {

		// User
		add_action('geotr/rules/print_user_type_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_logged_user_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_left_comment_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_search_engine_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_same_site_field', array('Geotr_Helper', 'print_select'), 10, 2);

		// Post
		add_action('geotr/rules/print_post_type_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_post_id_field', array('Geotr_Helper', 'print_textfield'), 10, 1);
		add_action('geotr/rules/print_post_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_post_category_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_post_format_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_post_status_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_taxonomy_field', array('Geotr_Helper', 'print_select'), 10, 2);

		// Page
		add_action('geotr/rules/print_page_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_page_type_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_page_parent_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_page_template_field', array('Geotr_Helper', 'print_select'), 10, 2);

		//Other
		add_action('geotr/rules/print_mobiles_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_desktop_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_tablets_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_crawlers_field', array('Geotr_Helper', 'print_select'), 10, 2);
		add_action('geotr/rules/print_referrer_field', array('Geotr_Helper', 'print_textfield'), 10, 1);
		add_action('geotr/rules/print_query_string_field', array('Geotr_Helper', 'print_textfield'), 10, 1);
	}

    public static function get_rules_choices() {
    		$choices = array(
    			__("User", 'geotr' ) => array(
    				'user_type'		    =>	__("User role", 'geotr' ),
    				'logged_user'	    =>	__("User is logged", 'geotr' ),
    				'left_comment'	    =>	__("User never left a comment", 'geotr' ),
    				'search_engine'	    =>	__("User came via a search engine", 'geotr' ),
    				'same_site'		    =>	__("User did not arrive via another page on your site", 'geotr' ),
    			),
    			__("Post", 'geotr' ) => array(
    				'post'			=>	__("Post", 'geotr' ),
    				'post_id'		=>	__("Post ID", 'geotr' ),
    				'post_type'		=>	__("Post Type", 'geotr' ),
    				'post_category'	=>	__("Post Category", 'geotr' ),
    				'post_format'	=>	__("Post Format", 'geotr' ),
    				'post_status'	=>	__("Post Status", 'geotr' ),
    				'taxonomy'		=>	__("Post Taxonomy", 'geotr' ),
    			),
    			__("Page", 'geotr' ) => array(
    				'page'			=>	__("Page", 'geotr' ),
    				'page_type'		=>	__("Page Type", 'geotr' ),
    				'page_parent'	=>	__("Page Parent", 'geotr' ),
    				'page_template'	=>	__("Page Template", 'geotr' ),
    			),
    			__("Other", 'geotr' ) => array(
    				'referrer'		=>	__("Referrer", 'geotr' ),
    				'query_string'		=>	__("Query String", 'geotr' ),
    				'mobiles'		=>	__("Mobile Phone", 'geotr' ),
    				'tablets'		=>	__("Tablet", 'geotr' ),
    				'desktop'		=>	__("Dekstop", 'geotr' ),
    				'crawlers'		=>	__("Bots/Crawlers", 'geotr' ),
    			)
    		);
    		// allow custom rules rules
    		return apply_filters( 'geotr/metaboxes/rule_types', $choices );
    }
}
