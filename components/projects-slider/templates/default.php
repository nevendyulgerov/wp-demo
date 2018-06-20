<?php
/**
 * Component: Slider
 * Template: Default
 * @param {object} $templateArgs
 */

use Symmetry\Project;

$templateData = $templateArgs->data;
$projects = $templateData->projects;
$speed = $templateData->speed;

if (!$projects || count($projects) === 0) {
	return;
}
?>

<div
  data-component="projects-slider"
  data-template="default"
  data-speed="<?php echo $speed ?>"
>
  <ul class="slider-items">
		<?php foreach ($projects as $index => $project) : ?>
      <li class="slider-item <?php echo $index === 0 ? 'active' : '' ?>">
				
				<?php Project::render(array(
					'data' => array(
						'id' => $project->ID,
						'title' => $project->post_title,
						'content' => $project->post_content,
						'thumbnail' => '',
						'startDate' => get_post_meta($project->ID, 'project_start_date', true),
						'endDate' => get_post_meta($project->ID, 'project_end_date', true),
						'teamMembers' => get_post_meta($project->ID, 'team_members', true),
						'index' => $index
					)
				)) ?>

        <button
          class="trigger go-to-next-project"
          title="Go to next project"
        >
          <span class="icon fa fa-long-arrow-down"></span>
        </button>

      </li>
		<?php endforeach ?>
  </ul>

</div>


