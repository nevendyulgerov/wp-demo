<?php
/**
 * Custom post type: Employee
 */

use Symmetry\Helper,
	Symmetry\Collection;

/**
 * Actions
 */
add_action('init', 'register_employee_post_type');
add_filter('manage_employee_posts_columns', 'extend_employee_columns');
add_action('manage_employee_posts_custom_column', 'fill_employee_columns', 10, 2);

/**
 * Register post type
 */
function register_employee_post_type() {
	$labels = array(
		'name' => __('Employee', 'post type general name'),
		'singular_name' => __('Employee', 'post type singular name'),
		'add_new' => __('Add Employee', 'Employee'),
		'add_new_item' => __('Add New Employee'),
		'edit_item' => __('Edit Employee'),
		'new_item' => __('New Employee'),
		'all_items' => __('All Employees'),
		'view_item' => __('View Employees'),
		'search_items' => __('Search Employees'),
		'not_found' => __('No employees found'),
		'not_found_in_trash' => __('No employees found in Trash'),
		'parent_item_colon' => '',
		'menu_name' => 'Employees'
	);
	
	$args = array(
		'labels' => $labels,
		'public' => false,
		'publicly_queryable' => false,
		'has_archive' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => false,
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'supports' => array('title', 'thumbnail'),
		'menu_icon' => 'dashicons-groups'
	);
	
	// register post type 'customer'
	register_post_type('employee', $args);
}

/**
 * Extend columns
 * @param $column
 * @return mixed
 */
function extend_employee_columns($column) {
	$column['employee_projects'] = __('Employee Projects');
	$column['employee_id'] = __('Employee ID');
	$column['featured_img'] = __('Thumbnail');
	return $column;
}

/**
 * Fill columns
 * @param $columnName
 * @param $postId
 */
function fill_employee_columns($columnName, $postId) {
	$post = get_post($postId);
	$defaultVal = 'N/A';
	
	switch ($columnName) {
		case 'employee_projects':
			$employee_projects = Collection::getPosts('project', -1, array(
				array(
					'key' => 'team_members',
					'value' => $postId,
					'compare' => 'LIKE'
				)
			));
			
			foreach($employee_projects as $index => $project) {
				echo '<span class="tag-project">' . $project->post_title . '</span>';
			}
			break;
			
		case 'employee_id':
			echo $postId;
			break;
		case 'featured_img':
			$style = 'background-image: url(' . Helper::getFeaturedImage($post->ID) . ');';
			echo Helper::getFeaturedImage($post->ID) ? '<div class="employee-thumbnail" style="' . $style . '" " title="' . __('Employee thumbnail') . '" />' : $defaultVal;
			break;
		default:
			break;
	}
}