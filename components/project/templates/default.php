<?php
/**
 * Component: Project
 * Template: Default
 * @param {object} $templateArgs
 */

use Symmetry\Helper;

$templateData = $templateArgs->data;
$id = $templateData->id;
$title = $templateData->title;
$content = $templateData->content;
$thumbnail = $templateData->thumbnail;
$startDate = $templateData->startDate;
$endDate = $templateData->endDate;
$teamMembers = $templateData->teamMembers;
$index = $templateData->index;

$startDateTime = DateTime::createFromFormat('Ymd', $startDate);
$endDateTime = DateTime::createFromFormat('Ymd', $endDate);
$isWithinSameYear = $startDateTime->format('Y') === $endDateTime->format('Y');
?>

<div
  data-component="project"
  data-template="default"
>
  <div class="overlay">
    <div class="gradient-box">
      <?php // gradient box ?>
    </div>
  </div>
  
  <div class="date <?php echo 'same-year' ?>" title="<?php echo __('Project date') ?>">
    <time class="year">
	    <?php echo $endDateTime->format('Y') ?>
    </time>
    <time class="duration">
			<?php echo $startDateTime->format('M') . ' - ' . $endDateTime->format('M') ?>
    </time>
  </div>

  <div class="main-content">
    <div class="thumbnail">
      <img src="<?php echo Helper::getFeaturedImage($id); ?>" alt="project-thumbnail">
    </div>

    <div class="title">
      <h2><?php echo $title ?></h2>
    </div>

    <div class="content">
			<?php echo apply_filters('the_content', $content) ?>
    </div>
  </div>

  <div class="team-members">
    <span class="label">
      <?php echo __('Meet the team') ?>
    </span>
    <ul class="team-members-list">
			<?php foreach($teamMembers as $teamMemberId) : ?>
				<?php $teamMemberEntry = get_post($teamMemberId); ?>

        <li class="team-member-item">

          <div class="team-member-box" title="<?php echo $teamMemberEntry->post_title ?>">
            <div class="team-member-thumbnail" style="background-image: url(<?php echo Helper::getFeaturedImage($teamMemberId) ?>);">
              <?php // team member thumbnail ?>
            </div>
          </div>

        </li>
			<?php endforeach ?>
    </ul>
  </div>
</div>