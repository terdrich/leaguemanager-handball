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
<section>

<?php if ( $time && $time == 'next') : ?>
	<header>Nächstes Spiel:</header>
<?php elseif ( $time && $time == 'prev') : ?>
	<header>Letztes Spiel:</header>
<?php else : ?>
	<header>Weitere Spiele:</header>
<?php endif; ?>

<?php foreach ( $matches AS $match ) : ?>

<?php if ( $time && $time == 'next') : ?>
	<span><?php echo date_i18n('d.m.Y', strtotime($match->date)) ?> <?php echo $match->start_time ?></span>
	<span><?php echo $match->title ?></span>
<?php elseif ( $time && $time == 'prev') : ?>
	<span><?php echo $match->title ?></span>
	<span><?php echo $match->score ?></span>
<?php else : ?>
	<span><?php echo date_i18n('d.m.Y', strtotime($match->date)) ?> <?php echo $match->start_time ?></span>
	<span><?php echo $match->title ?></span>
<?php endif; ?>

<?php endforeach; ?>
</section>
<?php else : ?>
<p><?php echo __( 'No Matches found', 'leaguemanager' ) ?></p>

<?php endif; ?>

<?php endif; ?>
