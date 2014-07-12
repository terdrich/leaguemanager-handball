<?php
/**
Template page to display single team

The following variables are usable:
	
	$league: league object
	$team: team object
	$next_match: next match object
	$prev_match: previous match object

	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>

<div class="teampage">

	<?php if ( isset($_GET['show']) ) : ?>
		<!-- Single Team Member -->
		<?php dataset($_GET['show']); ?>
	<?php else : ?>

	<?php if ( !empty($team->roster['id']) && function_exists('project') ) : ?>
		<h4 style="clear: both;"><?php _e( 'Team Roster', 'leaguemanager' ) ?></h4>
		<?php project($team->roster['id'], array('selections' => false) ); ?>
	<?php endif; ?>
	
	<?php endif; ?>
</div>
