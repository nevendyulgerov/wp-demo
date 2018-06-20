<?php
/**
 * Theme functions
 */

add_action('init', 'setupTheme', 10);
add_action('init', 'registerMenus', 20);
add_action('wp_enqueue_scripts', 'enqueueResources', 30);
add_action('admin_enqueue_scripts', 'enqueueAdminResources', 40);
add_filter('wp_title', 'getPageTitle', 10, 2);
add_action('wp_footer', 'dequeScripts');
add_action('wp_ajax_get_personalized_data', 'getPersonalizedDataCallback');
add_action('wp_ajax_nopriv_get_personalized_data', 'getPersonalizedDataCallback');
add_filter('wp_nav_menu_items', 'addLanguageSwitcher', 10, 2);

/**
 * Setup theme
 */
function setupTheme() {
	
	// define constant for js script extension
	define('JS_SUFFIX', WEBSITE_MODE === 'dev' ? '.js' : '.min.js');
	
	// add support for thumbnails
	add_theme_support('post-thumbnails');
}

/**
 * Register menus
 */
function registerMenus() {
	register_nav_menus(array(
		'top-menu' => __('Top Menu'),
		'header-menu' => __('Header Menu'),
		'resources-menu' => __('Resources Menu'),
		'footer-menu' => __('Footer Menu'),
		'sub-footer-menu' => __('Sub-footer Menu')
	));
}

/**
 * Enqueue resources
 */
function enqueueResources() {
	wp_enqueue_style('makingchange-text-font', 'https://fonts.googleapis.com/css?family=Roboto+Slab:300,400');
	wp_enqueue_style('makingchange-title-font', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400i,700,700i,900,900i');
	wp_enqueue_style('makingchange-style', get_template_directory_uri() . '/style.css', array(), filemtime(get_template_directory() . '/style.css'));
	wp_enqueue_style('makingchange-icons', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
	wp_enqueue_script('jquery');
	wp_enqueue_script('makingchange-script', get_template_directory_uri() . '/assets/js/main.js', array('jquery'));
	
	// DEV NOTE: Add any global javascript properties here
	wp_localize_script('makingchange-script', 'makingChangeSettings', array(
		"ajax_url" => admin_url("admin-ajax.php")
	));
}

/**
 * Enqueue admin resources
 */
function enqueueAdminResources() {
	wp_enqueue_style('makingchange-admin-style', get_template_directory_uri() . '/admin-style.css', array(), filemtime(get_template_directory() . '/admin-style.css'));
}


/**
 * Get page title
 * @param $title
 * @param $sep
 * @return string
 */
function getPageTitle($title, $sep) {
	global $paged, $page;
	
	if (is_feed()) {
		return $title;
	}
	
	// add site name
	$title .= get_bloginfo('name');
	
	// add the site description for the home/front page
	$siteDescription = get_bloginfo('description', 'display');
	
	if ($siteDescription && (is_home() || is_front_page())) {
		$title = "$title $sep $siteDescription";
	}
	
	// add a page number if necessary
	if ($paged >= 2 || $page >= 2) {
		$title = "$title $sep " . sprintf(__('Page %s', 'website'), max($paged, $page));
	}
	
	return $title;
}


/**
 * De-queue scripts.
 *
 */
function dequeScripts() {
	// remove the wp-embed JavaScript
	wp_dequeue_script('wp-embed');
}


/**
 * AJAX: Get personalized data callback
 * Delegates response to class AJAX
 */
function getPersonalizedDataCallback() {
	\Forci\Ajax::getPersonalizationData();
}