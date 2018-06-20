<?php
/**
 * Custom post type: Project
 */

use Symmetry\Helper,
	Symmetry\Collection;

/**
 * Actions
 */
add_action('init', 'register_project_post_type');
add_filter('manage_project_posts_columns', 'extend_project_columns');
add_action('manage_project_posts_custom_column', 'fill_project_columns', 10, 2);


/**
 * Register post type
 */
function register_project_post_type() {
	$labels = array(
		'name' => __('Project', 'post type general name'),
		'singular_name' => __('Project', 'post type singular name'),
		'add_new' => __('Add Project', 'Project'),
		'add_new_item' => __('Add New Project'),
		'edit_item' => __('Edit Project'),
		'new_item' => __('New Project'),
		'all_items' => __('All Projects'),
		'view_item' => __('View Projects'),
		'search_items' => __('Search Projects'),
		'not_found' => __('No projects found'),
		'not_found_in_trash' => __('No projects found in Trash'),
		'parent_item_colon' => '',
		'menu_name' => 'Projects'
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
		'supports' => array('title', 'editor', 'thumbnail'),
		'menu_icon' => 'dashicons-star-filled'
	);
	
	// register post type 'customer'
	register_post_type('project', $args);
}

/**
 * Extend columns
 * @param $column
 * @return mixed
 */
function extend_project_columns($column) {
	$column['team_members'] = __('Team Members');
	$column['project_date'] = __('Project Date');
	$column['project_id'] = __('Project ID');
	$column['featured_img'] = __('Thumbnail');
	return $column;
}


/**
 * Fill columns
 * @param $columnName
 * @param $postId
 */
function fill_project_columns($columnName, $postId) {
	$post = get_post($postId);
	$defaultVal = 'N/A';
	
	switch ($columnName) {
		case 'team_members':
			$team_member_ids = get_field('team_members', $postId);
			$team_members = Collection::getPosts('employee', -1, array(), array(
				'include' => $team_member_ids
			));
			
			foreach($team_members as $index => $team_member) {
				echo '<span class="tag-team-member">' . $team_member->post_title . '</span>';
			}
			break;
		case 'project_date':
			$project_start_date = get_field('project_start_date', $postId);
			$project_end_date = get_field('project_end_date', $postId);
			$project_start_date_time = DateTime::createFromFormat('d/m/Y', $project_start_date);
			$project_end_date_time = DateTime::createFromFormat('d/m/Y', $project_end_date);

			echo 'From <span class="project-start-date">' . $project_start_date_time->format('M. d, Y') . '</span> to <span class="project-end-date">' . $project_end_date_time->format('M. d, Y') . '</span>';
			break;
		case 'project_id':
			echo $postId;
			break;
		case 'featured_img':
			$style = 'background-image: url(' . Helper::getFeaturedImage($post->ID) . ');';
			echo Helper::getFeaturedImage($post->ID)
				? '<div class="project-thumbnail" style="' . $style . '" " title="' . __('Project thumbnail') . '" />'
				: $defaultVal;
			break;
		default:
			break;
	}
}