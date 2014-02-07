<?php
class NerdPress {

	function __construct() {
		add_action('init', array( &$this, 'init_filesystem' ) );
		add_action( 'widgets_init', array( &$this, 'register_widget_areas' ) );
		add_action( 'init', array( &$this, 'integrations' ) );
		add_filter( 'roots_display_sidebar', array( &$this, 'hide_sidebar_on' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'load_scripts' ), 200 );
		add_shortcode( 'nerdpress_sitemap', array( &$this, 'sitemap' ) );
		add_action( 'login_enqueue_scripts', array( &$this, 'login_logo' ) );
		add_filter( 'login_headerurl', array( &$this, 'login_url' ) );
		add_filter( 'login_headertitle', array( &$this, 'login_title' ) );
		add_action( 'plugins_loaded', array( &$this, 'limit_revisions' ) );
		add_shortcode( 'nerdpress_social_networks', array( &$this, 'social_networks' ) );
		add_filter( 'wp_title', array( &$this, 'seo_title' ), 10, 2 );
		add_action( 'wp_head', array( &$this, 'seo_description') );
		add_action( 'tgmpa_register', array( &$this, 'register_required_plugins' ) );
		add_filter( 'scpt_show_admin_menu', '__return_false' ); // Hide SuperCPT's usless icon menu item
		add_action( 'after_setup_theme', array( &$this, 'setup_post_types' ) );
		add_filter('comment_reply_link', array( &$this, 'bootstrap_reply_link_class' ) );
		add_filter( 'the_password_form', array( &$this, 'bootstrap_password_form' ) );
		add_filter( 'bbp_no_breadcrumb', array(&$this, 'bbpress_no_breadcrumbs' ) );
	}

	function init_filesystem() {
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH .'/wp-admin/includes/file.php' );
			WP_Filesystem();
		}
	}
	
	function variable( $var ) {
		global $nerdpress_config;
		return $nerdpress_config[ $var ];
	}

	function register_widget_areas() {
		$widget_areas = self::variable( 'widget_areas' );
		
		if ( $widget_areas ) :
			foreach ( $widget_areas as $widget_area => $data ) :
				register_sidebar(array(
					'name' 				=> __( $widget_area, 'nerdpress' ),
					'id' 					=> 'widget-area-' . strtolower( str_replace( ' ', '-', $widget_area ) ),
					'class' 				=> $data['class'],
					'before_widget' 	=> '<section class="widget %1$s %2$s ' . $data['mode'] . '"><div class="widget-inner">',
					'after_widget' 		=> '</div></section>',
					'before_title' 		=> '<h3>',
					'after_title' 			=> '</h3>',
				));
			endforeach;
		endif;
	}
	
	function widget_area( $widget_area_id ) {
		if ( is_dynamic_sidebar( $widget_area_id ) ) dynamic_sidebar( $widget_area_id );
		include( locate_template( 'templates/edit-link.php' ) );
	}
	
	function integrations() {
		$integrations = self::variable( 'integrations' );
		
		if ( $integrations ) :		
			foreach ( $integrations as $integration ) {
				require_once( themePATH . '/' . themeFOLDER . '/lib/modules/nerdpress.core/integrations/' . $integration . '.php' );
			}		
		endif;
	}

	function container_class() {
		global $post;
			
		if ( get_field( 'nrd_full_width' ) ) return 'full-width';
		else return 'container';
	}

	function navbar_class( $navbar = 'main' ) {
	  $fixed    = variable( 'navbar_fixed' );
	  $fixedpos = variable( 'navbar_fixed_position' );
	
	  if ( $fixed != 1 )
	    $class = 'navbar navbar-static-top';
	  else
	    $class = ( $fixedpos == 1 ) ? 'navbar navbar-fixed-bottom' : 'navbar navbar-fixed-top';
	
	  if ( $navbar != 'secondary' )
	    return $class;
	  else
	    return 'navbar';
	}

	function display_sidebar() {
		$sidebar_config = new Roots_Sidebar(
			self::variable( 'hide_sidebar_conditions' ),
			self::variable( 'hide_sidebar_templates' )
		);
		
		return apply_filters('roots_display_sidebar', $sidebar_config->display);
	}

	function hide_sidebar_on( $sidebar ) {
		if ( get_field( 'nrd_hide_sidebar' ) ) return false;
		
		return $sidebar;
	}

	function main_class() {
		if ( self::display_sidebar() ) $class = self::variable( 'main_class' );
		else $class = 'col-sm-12';
		
		return $class;
	}

	function sidebar_class() {
		return self::variable( 'sidebar_class' );
	}
	
	function load_scripts() {
	
		wp_deregister_script( 'roots_scripts' );
		
		wp_register_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css' );
		wp_enqueue_style( 'font-awesome' );
		
		wp_enqueue_script( 'bootstrap', '//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js', array( 'jquery' ), NULL, true );
		wp_enqueue_script( 'main', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), NULL, true );
		wp_enqueue_script( 'placeholder', get_template_directory_uri() . '/assets/js/vendor/jquery.placeholder.js', array( 'jquery'), NULL, true );		
		wp_enqueue_script( 'retina', get_template_directory_uri() . '/assets/js/vendor/retina.js', NULL, NULL, true );
		if ( self::variable( 'analytics_id' ) ) 
			wp_enqueue_script( 'analytics', get_template_directory_uri() . '/assets/js/analytics.php', array( 'jquery' ), NULL, NULL );
		
		if ( self::variable( 'script_animatecss' ) ) :
			wp_register_style( 'animate-css', get_template_directory_uri() . '/assets/css/animate.min.css' );
			wp_enqueue_style( 'animate-css' );
		endif;
		
		if ( self::variable( 'script_flexslider' ) ) 
			wp_enqueue_script( 'flexslider', get_template_directory_uri() . '/assets/js/vendor/jquery.flexslider-min.js', array( 'jquery'), '2.2.0', true );
			
		if ( self::variable( 'script_lightbox' ) ) 
			wp_enqueue_script( 'lightbox', get_template_directory_uri() . '/assets/js/vendor/ekko-lightbox.js', array( 'jquery'), NULL, true );
			
		if ( self::variable( 'script_vimeo' ) ) 
			wp_enqueue_script( 'froogaloop', '//a.vimeocdn.com/js/froogaloop2.min.js', NULL, NULL, true );
			
		if ( self::variable( 'script_bootstrap_hover' ) ) 
			wp_enqueue_script( 'bootstrap-hover', get_template_directory_uri() . '/assets/js/vendor/bootstrap-hover-dropdown.js', array( 'jquery' ), NULL, true );
			
		if ( self::variable( 'script_header' ) ) :
			$script_header = self::variable( 'script_header' );
			
			foreach ( $script_header as $script ) :
				wp_enqueue_script( $script, $script );
			endforeach;
		endif;
		
		if ( self::variable( 'script_footer' ) ) :
			$script_footer = self::variable( 'script_footer' );
			
			foreach ( $script_footer as $script ) :
				wp_enqueue_script( $script, $script, NULL, NULL, true );
			endforeach;
		endif;
	}
	
	function make_crumb( $url = false, $text ) {
		global $breadcrumbs;
		
		$breadcrumbs[] = array(
			'url' => $url,
			'text' => $text,
		);
		
		return $breadcrumbs;
	}
	
	function breadcrumbs() {
		global $breadcrumbs, $post, $wp_query;
		
		$skip_post_types = array(
			'forum',
			'topic',
			'reply',
		);
		
		$skip_taxonomies = array(
			'topic-tag',
		);
		
		$term = get_query_var( 'term' );
		$taxonomy = get_query_var( 'taxonomy' );
		$paged = get_query_var( 'paged' );
		
		// WooCommerce check
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
			$woo_active = true;
			
		// bbPress check
		if ( class_exists( 'bbPress' ) ) 
			$bbpress_active = true;
	
		$breadcrumbs = array();
		
		// Home URL
		self::make_crumb( home_url(), '<i class="fa fa-home fa-lg"></i>' );
		
		// Page -- Parents murdered in an alley
		if ( is_page() && !$post->post_parent ) 
			self::make_crumb( null, get_the_title() );
		
		// Page -- Has parents
		if ( is_page() && $post->post_parent ) :
		
			$parent_id = $post->post_parent;
			
			// We have to put ancestors in the own array first because 
			// they're in a reverse order. We'll order them correctly in a second.
			$subcrumbs = array();
			
			while ( $parent_id ) :
				$page = get_page( $parent_id );
				
				$subcrumbs[] = array(
					'url' => get_permalink( $page->ID ),
					'text' => get_the_title( $page->ID ),
				);
				
				$parent_id = $page->post_parent;
			endwhile;
			
			// Reverse the ancestor order
			$subcrumbs = array_reverse( $subcrumbs );
			
			foreach ( $subcrumbs as $crumb => $data ) {
				// Make crumbs for each ancestor
				self::make_crumb( $data['url'], $data['text'] );
			}
			
			// Finally, make crumb for current page
			self::make_crumb( null, get_the_title() );
			
		endif; // Page with parents
		
		// Author archive
		if ( is_author() ) :
			global $author;
		
			$the_author = get_userdata( $author );
			self::make_crumb( null, $the_author->display_name . '\'s Posts' );
		
		endif; // Author archive
		
		if ( is_category() ) :
		
			$cat_object = $wp_query->get_queried_object();		
	        $this_cat = get_category( $cat_object->term_id );
	        
	        // Category has parents
	        // This is all hacky because we put the categories into a pipe-separated
	        // list and then split them out. This leaves the last element in the array
	        // as blank. So we cut that and make crumbs for the rest. Should 
	        // work nicely for nested categories.
	        if ( $this_cat->parent != 0 ) :
	        	$parents = get_category_parents( get_category( $this_cat->parent ), false, '|', false );
	        	$parents = explode( '|', $parents );
	        	unset( $parents[ count( $parents ) - 1 ] );
	        	
	        	foreach ( $parents as $parent ) {
	        		$the_cat = get_term_by( 'name', $parent, 'category' );
	        		$the_link = get_term_link( $the_cat );
		        	self::make_crumb( $the_link, $parent );
	        	}
	        endif;
	        
	        // The current category
	        self::make_crumb( null, single_cat_title( null, false ) );
	        
		endif; // Is category
		
		// Tagged
		if ( is_tag() ) :
		
			$tag_object = $wp_query->get_queried_object();
			$this_tag = get_tag( $tag_object );
			
			self::make_crumb( null, single_cat_title( null, false ) );
		
		endif; // Is tag
		
		// 404
		if ( is_404() ) :
			self::make_crumb( null, 'File Not Found (Error 404)' );
		endif; // 404
		
		// Search results
		if ( is_search() ) :
			self::make_crumb( null, 'Search Results for "' . get_search_query() . '"' );
		endif; // Search
		
		// Single post, except products
		if ( is_single() && !in_array( get_post_type(), $skip_post_types ) ) :
		
			$post_type = get_post_type_object( get_post_type() );
			
			$post_type_config = self::variable( 'post_types' );
					
			if ( array_key_exists( $post_type->name, $post_type_config ) ) :
			
				if ( $post_type_config[ $post_type->name ]['breadcrumb'] ) 
					self::make_crumb( get_permalink( $post_type_config[ $post_type->name ]['breadcrumb'] ), get_the_title( $post_type_config[ $post_type->name ]['breadcrumb'] ) );
					
				elseif ( $post_type->has_archive == 1 ) 
					self::make_crumb( home_url( $post_type->rewrite['slug'] ), $post_type->labels->name );
					
				elseif ( $post_type->has_archive ) 
					self::make_crumb( home_url( $post_type->has_archive ), get_the_title( get_page_by_path( $post_type->has_archive ) ) );
					
				else 
					self::make_crumb( home_url(), $post_type->labels->name );
			
			endif;
			
			// get the taxonomy names of this object
			$taxonomy_names = get_object_taxonomies( $post_type->name );
			
			// Detect any hierarchical taxonomies that might exist on this post type
			$hierarchical = false;
			
			foreach ( $taxonomy_names as $taxonomy_name ) :
				if ( !$hierarchical ) {
					$hierarchical = ( is_taxonomy_hierarchical( $taxonomy_name ) ) ? true : $hierarchical;
					$tn = $taxonomy_name;
				}
			endforeach;
			
			$args = ( is_taxonomy_hierarchical( $tn ) ) ? array( 'orderby' => 'parent', 'order' => 'DESC' ) : '';
			
			if ( $terms = wp_get_post_terms( $post->ID, $tn, $args ) ) {
				$main_term = $terms[0];
				
				if ( is_taxonomy_hierarchical( $tn ) ) {
					$ancestors = get_ancestors( $main_term->term_id, $tn );
					$ancestors = array_reverse( $ancestors );
					
					foreach ( $ancestors as $ancestor ) {
						$ancestor = get_term( $ancestor, $tn );
						self::make_crumb( get_term_link( $ancestor->slug, $tn ), $ancestor->name );
					}
				}
				self::make_crumb( get_term_link( $main_term->slug, $tn ), $main_term->name );
			}
			
			self::make_crumb( null, get_the_title() );
			
		endif; // Single post
		
		// Post type archive
		if ( is_post_type_archive() ) :
			
			$post_type = get_post_type_object( get_post_type() );
			
			$post_type_config = self::variable( 'post_types' );
					
			if ( array_key_exists( $post_type->name, $post_type_config ) ) :
			
				if ( $post_type_config[ $post_type->name ]['breadcrumb'] ) 
					self::make_crumb( null, get_the_title( $post_type_config[ $post_type->name ]['breadcrumb'] ) );
					
				elseif ( $post_type->has_archive == 1 ) 
					self::make_crumb( null, $post_type->labels->name );
					
				elseif ( $post_type->has_archive ) 
					self::make_crumb( null, get_the_title( get_page_by_path( $post_type->has_archive ) ) );
					
				else 
					self::make_crumb( null, $post_type->labels->name );
					
			endif;
		
		endif; // Post type archive
		
		// Generic taxonomy
		if ( is_tax() ) :
		
			$tax_object = $wp_query->get_queried_object();
			
			if ( !in_array( $tax_object->taxonomy, $skip_taxonomies ) ) :
						
				$this_term = get_term( $tax_object->term_id, $tax_object->taxonomy );
				$the_tax = get_taxonomy( $tax_object->taxonomy );
				
				$post_type = get_post_type_object( get_post_type() );
				
				$post_type_config = self::variable( 'post_types' );
						
				if ( array_key_exists( $post_type->name, $post_type_config ) ) :
				
					if ( $post_type_config[ $post_type->name ]['breadcrumb'] ) 
						self::make_crumb( get_permalink( $post_type_config[ $post_type->name ]['breadcrumb'] ), get_the_title( $post_type_config[ $post_type->name ]['breadcrumb'] ) );
						
					elseif ( $post_type->has_archive == 1 ) 
						self::make_crumb( home_url( $post_type->rewrite['slug'] ), $post_type->labels->name );
						
					elseif ( $post_type->has_archive ) 
						self::make_crumb( home_url( $post_type->has_archive ), get_the_title( get_page_by_path( $post_type->has_archive ) ) );
						
					else 
						self::make_crumb( home_url(), $post_type->labels->name );
				
				endif;
				
				//self::make_crumb( home_url(), $post_type->labels->name );
				
				$parent_id = $this_term->parent;
				$subcrumbs = array();
				
				while ( $parent_id ) :
					$term = get_term_by( 'id', $parent_id, $tax_object->taxonomy );
					
					$subcrumbs[] = array(
						'url' => get_term_link( $term->term_id, $tax_object->taxonomy ),
						'text' => $term->name,
					);
					
					$parent_id = $term->parent;
				endwhile;
				
				$subcrumbs = array_reverse( $subcrumbs );
				
				foreach ( $subcrumbs as $crumb => $data ) {
					// Make crumbs for each ancestor
					self::make_crumb( $data['url'], $data['text'] );
				}
		        
		        // The current term
		        self::make_crumb( null, single_cat_title( null, false ) );
	        
	        endif;
		
		endif; // Generic taxonomy
		
		// Day archive
		if ( is_day() ) :
	        self::make_crumb( get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ); // Year
	        self::make_crumb( get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ), get_the_time( 'F' ) ); // Month
	        self::make_crumb( null, get_the_time( 'd' ) ); // Day
		endif; // Day archive
		
		// Month archive
		if ( is_month() ) :
			self::make_crumb( get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ); // Year
			self::make_crumb( null, get_the_time( 'F' ) ); // Month
		endif; // Month archive
		
		// Year archive
		if ( is_year() ) :
			self::make_crumb( null, get_the_time( 'Y' ) ); // Year
		endif; // Year archive
		
		// bbPress wrap
		// We check for the existence of the BBpress class
		if ( $bbpress_active && 
			( bbp_is_topic_archive() || 
				bbp_is_search() || 
				bbp_is_forum_archive() || 
				bbp_is_single_view() || 
				bbp_is_single_forum() || 
				bbp_is_single_topic() || 
				bbp_is_single_reply() || 
				bbp_is_topic_tag() || 
				bbp_is_user_home() ) ) :
			
			self::make_crumb( home_url( get_option( '_bbp_root_slug' ) ), 'Forums' );
		
			$ancestors = (array) get_post_ancestors( get_the_ID() );
			
			// Ancestors exist
			if ( !empty( $ancestors ) ) :
				// Loop through parents
				foreach ( (array) $ancestors as $parent_id ) :
				// Parents
				$parent = get_post( $parent_id );
				
				// Skip parent if empty or error
				if ( empty( $parent ) || is_wp_error( $parent ) ) continue;
				
					// Switch through post_type to ensure correct filters are applied
					switch ( $parent->post_type ) {
					
						// Forum
						case bbp_get_forum_post_type() :
							self::make_crumb( esc_url( bbp_get_forum_permalink( $parent->ID ) ), bbp_get_forum_title( $parent->ID ) );
						break;
						
						// Topic
						case bbp_get_topic_post_type() :
							self::make_crumb( esc_url( bbp_get_topic_permalink( $parent->ID ) ), bbp_get_topic_title( $parent->ID ) );
						break;
						
						// Reply
						case bbp_get_reply_post_type() :
							self::make_crumb( esc_url( bbp_get_reply_permalink( $parent->ID ) ), bbp_get_reply_title( $parent->ID ) );
						break;
						
					}
				endforeach;
			endif;
			
			// Topic archive
			if ( bbp_is_topic_archive() ) 
				self::make_crumb( null, bbp_get_topic_archive_title() );
			
			// Search page
			if ( bbp_is_search() ) 
				self::make_crumb( null, bbp_get_search_title() );
			
			// Forum archive
/*
			if ( bbp_is_forum_archive() ) 
				self::make_crumb( null, bbp_get_forum_archive_title() );
*/
			
			// View
			elseif ( bbp_is_single_view() ) 
				self::make_crumb( null, bbp_get_view_title() );
			
			if ( bbp_is_single_forum() ) 
				self::make_crumb( null, bbp_get_forum_title() );
			
			if ( bbp_is_single_topic() ) 
				self::make_crumb( null, bbp_get_topic_title() );
			
			if ( bbp_is_single_reply() ) 
				self::make_crumb( null, bbp_get_reply_title() );
				
			if ( bbp_is_topic_tag() ) {
				$topic_tag = $wp_query->get_queried_object();
				self::make_crumb( null, $topic_tag->name );
			}				
				
			if ( bbp_is_user_home() ) 
				self::make_crumb( null, 'Profile' );
				
		endif; // bbPress
		
		// Paged content
		if ( $paged ) :
			self::make_crumb( null, 'Page ' . $paged );
		endif; // Paged
		
		if ( self::variable( 'breadcrumbs' ) && !is_front_page() && !is_home() ) get_template_part( 'templates/breadcrumbs' );
	} // breadcrumbs
	
	function sitemap() {
		get_template_part( 'templates/sitemap' );
	}
	
	function login_logo() {
		if ( !locate_template('assets/img/site-logo.png') ) return;
		get_template_part( 'templates/login', 'logo' );
	}
	
	function login_url() {
		return get_bloginfo( 'url' );
	}
	
	function login_title() {
		return get_bloginfo( 'name' );
	}
	
	function limit_revisions() {
		define( 'WP_POST_REVISIONS', 2 );
	}
	
	function social_networks() {
		get_template_part( 'templates/social', 'networks' );
	}
	
	function seo_title( $title, $sep ) {
		global $post;
		
		$seo_title = get_field( 'nrd_seo_title' );
		
		if ( $seo_title ) $title = $seo_title . ' ' . $sep . ' ';
		
		return $title;
	}
	
	function page_title() {
		global $post;
		
		$seo_heading = get_field( 'nrd_seo_heading' );
		
		if ( is_home() ) {
			if ( get_option( 'page_for_posts', true ) ) {
				return get_the_title(get_option('page_for_posts', true));
		} else {
			return __('Latest Posts', 'nerdpress');
		}
		} elseif (is_archive()) {
			$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
		if ($term) {
		return apply_filters('single_term_title', $term->name);
		} elseif (is_post_type_archive()) {
			return apply_filters('the_title', get_queried_object()->labels->name);
		} elseif (is_day()) {
			return sprintf(__('Daily Archives: %s', 'nerdpress'), get_the_date());
		} elseif (is_month()) {
			return sprintf(__('Monthly Archives: %s', 'nerdpress'), get_the_date('F Y'));
		} elseif (is_year()) {
			return sprintf(__('Yearly Archives: %s', 'nerdpress'), get_the_date('Y'));
		} elseif (is_author()) {
			$author = get_queried_object();
			return sprintf(__('Author Archives: %s', 'nerdpress'), $author->display_name);
		} else {
			return single_cat_title('', false);
		}
		} elseif (is_search()) {
			return sprintf(__('Search Results for %s', 'nerdpress'), get_search_query());
		} elseif (is_404()) {
			return __('Not Found', 'nerdpress');
		} elseif ( $seo_heading ) {
			return $seo_heading;
		} else {
			return get_the_title();
		}
	}
	
	function seo_description() {
		global $post;
		
		$seo_desc = get_field( 'nrd_seo_desc' );
		
		if ( !$seo_desc ) return;
		
		echo '<meta name="description" content="' . htmlspecialchars_decode( $seo_desc, ENT_QUOTES ) . '"/>';	
	}
	
	function register_required_plugins() {
	
		$plugins_list = wp_remote_get( 'http://repo.nerdymind.com/nerdpress-helpers/plugin-list.php' );
		$plugins = json_decode( $plugins_list['body'], true );
	
		// Change this to your theme text domain, used for internationalising strings
		$theme_text_domain = 'nerdpress';
	
		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'domain'       		=> $theme_text_domain,         	// Text domain - likely want to be the same as your theme.
			'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
			'parent_menu_slug' 	=> 'plugins.php', 				// Default parent menu slug
			'parent_url_slug' 	=> 'plugins.php', 				// Default parent URL slug
			'menu'         		=> 'nerdpress-plugins', 	// Menu slug
			'has_notices'      	=> true,                       	// Show admin notices or not
			'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
			'message' 			=> '',							// Message to output right before the plugins table
			'strings'      		=> array(
				'page_title' 	=> __( 'NerdPress Plugins', $theme_text_domain ),
				'menu_title' 	=> __( 'NerdPress Plugins', $theme_text_domain ),
				'installing' 		=> __( 'Installing Plugin: %s', $theme_text_domain ), // %1$s = plugin name
				'oops' 			=> __( 'Something went wrong with the plugin API.', $theme_text_domain ),
				'notice_can_install_required' 	=> _n_noop( 'NerdPress requires the following plugin: %1$s.', 'NerdPress requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
				'notice_can_install_recommended' 	=> _n_noop( 'NerdPress recommends the following plugin: %1$s.', 'NerdPress recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_install' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
				'notice_can_activate_required' 		=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
				'notice_ask_to_update' 					=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_update' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
				'install_link' 									=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
				'activate_link'							 		=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
				'return' 											=> __( 'Return to NerdPress Plugins', $theme_text_domain ),
				'plugin_activated' 							=> __( 'Plugin activated successfully.', $theme_text_domain ),
				'complete' 										=> __( 'All plugins installed and activated successfully. %s', $theme_text_domain ), // %1$s = dashboard link
				'nag_type' 										=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
			)
		);
	
		tgmpa( $plugins, $config );	
	}
	
	function setup_post_types() {
		
		// Check to make sure our class is here before we waste our time!
		if ( !class_exists( 'Super_Custom_Post_Type' ) ) 
			return;
		
		$post_types = self::variable( 'post_types' );
		$taxonomies = self::variable( 'taxonomies' );
		
		if ( is_array( $post_types ) ) :
		
			foreach ( $post_types as $post_type => $options ) :
				if ( $options['create'] != true ) continue;
				
				if ( $options['singular'] && $options['plural'] ) 
					$$post_type = new Super_Custom_Post_Type( $post_type, $options['singular'], $options['plural'] );
					
				elseif( $options['singular'] ) 
					$$post_type = new Super_Custom_Post_Type( $post_type, $options['singular'] );
					
				else 
					$$post_type = new Super_Custom_Post_Type( $post_type );					
					
				if ( $options['icon'] != '' ) 
					$$post_type->set_icon( $options['icon'] );
					
				if ( $options['slug'] != '' ) 
					$$post_type->cpt['rewrite'] = array(
						'slug' => $options['slug'],
						'with_front' => true,
						'pages' => true,
						'feeds' => true,
					);
			endforeach;
		
		endif;
		
		if ( is_array( $taxonomies ) ) :
		
			foreach ( $taxonomies as $taxonomy => $options ) :
			
				if ( $options['create'] != true ) continue;
				
				$$taxonomy = new Super_Custom_Taxonomy( $taxonomy );
				
				if ( $options['connect_to'] != '' ) 
					connect_types_and_taxes( $$options['connect_to'], $$taxonomy );
			
			endforeach;
		
		endif;
		
	}
	
	function bootstrap_reply_link_class( $class ) {
		$class = str_replace( "class='comment-reply-link", "class='comment-reply-link btn btn-primary btn-small", $class );
		return $class;
	}
	
	function bootstrap_password_form() {
		global $post;
		
		$label = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );
		$content  = '<form action="';
		$content .= esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) );
		$content .= '" method="post">';
		$content .= __( 'This post is password protected. To view it please enter your password below:', 'nerdpress' );
		$content .= '<div class="input-group">';
		$content .= '<input name="post_password" id="' . $label . '" type="password" size="20" />';
		$content .= '<span class="input-group-btn">';
		$content .= '<input type="submit" name="Submit" value="' . esc_attr__( "Submit" ) . '" class="btn btn-default" />';
		$content .= '</span></div></form>';
		
		return $content;
	}
	
	function bbpress_no_breadcrumbs( $param ) {
		return true;
	}
	
} // End class

$nerdpress = new NerdPress();
global $nerdpress;
?>