<?php
/**
Template page for the match table

The following variables are usable:
	
	$leagues: contains data of current leagues
	$matches: contains all matches for current league
	$season: current season
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>
<?php if (isset($_GET['match']) ) : ?>
	<?php leaguemanager_match($_GET['match']); ?>
<?php else : ?>

<?php if ( $matches ) : ?>
<div id="team-matches" class="collapse no-more-tables">
<table class='leaguemanager matchtable table' title='<?php echo __( 'Match Plan', 'leaguemanager' ) ?>'>
<thead>
<tr>
	<th class='match'><?php _e( 'Match', 'leaguemanager' ) ?></th>
	<th class='score'><?php _e( 'Score', 'leaguemanager' ) ?></th>
</tr>
</thead>
<?php foreach ( $matches AS $match ) : ?>

<tr class='<?php echo $match->class ?>'>
	<td class='match' data-title='<?php _e( 'Match', 'leaguemanager' ) ?>'>
		<span class='match_date'><?php echo $match->date." "?></span>
		<span class='match_time'><?php echo $match->start_time." "?></span>
		<span class='match_location'><?php echo $match->location ?></span>
		<br />
		<span class='match_title'><?php echo $match->title ?></span> <?php echo $match->report ?>
	</td>
	<td class='score' data-title='<?php _e( 'Score', 'leaguemanager' ) ?>' valign='bottom'><?php echo $match->score ?></td>
</tr>

<?php endforeach; ?>
</table>
</div>
<button type="button" class="btn btn-default btn-block collapsed toggle-matches" data-toggle="collapse" data-target="#team-matches">
  <span class="show-hide-matches"></span> Spiele anzeigen&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-chevron-down"></span>
</button>
<?php else : ?>
<p><?php echo __( 'No Matches found', 'leaguemanager' ) ?></p>

<?php endif; ?>

<?php endif; ?>
