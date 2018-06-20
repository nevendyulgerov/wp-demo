<?php
/*
 * Template name: Home
 */

use	Symmetry\ProjectsSlider;

$posts_per_page = 5;
$projects_query = array(
	'post_type' => 'project',
	'posts_per_page' => $posts_per_page,
	'meta_key' => 'project_end_date',
	'orderby' => 'meta_value',
	'order' => 'DESC'
);
$latest_projects = get_posts($projects_query);
?>

<?php get_header(); ?>

<div data-view="home">
	
	<?php ProjectsSlider::render(array(
		'data' => array(
			'projects' => $latest_projects,
			'speed' => 1000
		)
	)) ?>

</div>

<?php get_footer(); ?>
