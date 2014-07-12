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
<?php
	$league_info = array(
		1 => array('link' => site_url()."/herren/herren-i/", 'short_title' => 'M-BK'),
		2 => array('link' => site_url()."/herren/herren-iI/", 'short_title' => 'M-KKB'),
		3 => array('link' => site_url()."/damen/damen-i/", 'short_title' => 'F-SL'),
		4 => array('link' => site_url()."/damen/damen-ii/", 'short_title' => 'F-BK'),
		5 => array('link' => site_url()."/jugend/jugend-a-maennlich", 'short_title' => 'J-mA'),
		6 => array('link' => site_url()."/jugend/jugend-a-weiblich", 'short_title' => 'J-wA'),
		7 => array('link' => site_url()."/jugend/jugend-b-maennlich", 'short_title' => 'J-mB'),
		8 => array('link' => site_url()."/jugend/jugend-c-maennlich", 'short_title' => 'J-mC'),
		9 => array('link' => site_url()."/jugend/jugend-c-weiblich", 'short_title' => 'J-wC'),
		10 => array('link' => site_url()."/jugend/jugend-d-maennlich", 'short_title' => 'J-mD'),
		11 => array('link' => site_url()."/jugend/jugend-d-weiblich", 'short_title' => 'J-wD'),
		12 => array('link' => site_url()."/jugend/jugend-e-maennlich", 'short_title' => 'J-mE'),
		13 => array('link' => site_url()."/jugend/jugend-e-weiblich", 'short_title' => 'J-wE')
	);
?>

<?php if (isset($_GET['match']) ) : ?>
	<?php leaguemanager_match($_GET['match']); ?>
<?php else : ?>

<?php if ( $matches ) : ?>
<div id="team-matches" class="no-more-tables">
<table class='leaguemanager matchtable matchtable-homepage table' title='<?php echo __( 'Match Plan', 'leaguemanager' ) ?>'>
<thead>
<tr>
	<th class='league'><?php _e( 'League', 'leaguemanager' ) ?></th>
	<th class='match_date'><?php _e( 'Date', 'leaguemanager' ) ?></th>
	<th class='match_time'><?php _e( 'Time', 'leaguemanager' ) ?></th>
	<th class='match_title'><?php _e( 'Match', 'leaguemanager' ) ?></th>
	<th class='match_location'><?php _e( 'Location', 'leaguemanager' ) ?></th>
</tr>
</thead>
<?php foreach ( $matches AS $match ) : ?>
<?php $league = $leaguemanager->getLeague($match->league_id) ?>
<tr class='<?php echo $match->class ?>'>
	<td class='league' data-title='<?php _e( 'League', 'leaguemanager' ) ?>'>
		<a href="<?php echo $league_info[$league->id]['link'] ?>"><?php echo $league_info[$league->id]['short_title'] ?></a>
	</td>
	<?php //echo date_i18n('d.m.Y', strtotime($match->date));?>
	<td class='match_date' data-title='<?php _e( 'Date', 'leaguemanager' ) ?>'><?php echo $match->date ?></td>
	<td class='match_time' data-title='<?php _e( 'Time', 'leaguemanager' ) ?>'><?php echo $match->start_time ?></td>
	<td class='match_title' data-title='<?php _e( 'Match', 'leaguemanager' ) ?>'><?php echo $match->title ?></td>
	<td class='match_location' data-title='<?php _e( 'Location', 'leaguemanager' ) ?>'><?php echo $match->location ?></td>
</tr>

<?php endforeach; ?>
</table>
</div>
<?php else : ?>
<p><?php echo __( 'No Matches found', 'leaguemanager' ) ?></p>

<?php endif; ?>

<?php endif; ?>
