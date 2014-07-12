<?php

class Leaguemanager_API_League {
	/**
	 * Register the league-related routes
	 *
	 * @param array $routes Existing routes
	 * @return array Modified routes
	 */
	public function registerRoutes( $routes ) {
		$league_routes = array(
			// League endpoints
			'/leaguemanager/leagues'             => array(
				array( array( $this, 'getLeagues' ), WP_JSON_Server::READABLE )
			),

			'/leaguemanager/leagues/(?P<id>\d+)' => array(
				array( array( $this, 'getLeague' ), WP_JSON_Server::READABLE )
			),

			// Matches
			'/leaguemanager/leagues/(?P<id>\d+)/matches'                => array(
				array( array( $this, 'getMatches' ), WP_JSON_Server::READABLE ),
			),
			'/leaguemanager/leagues/(?P<id>\d+)/matches/(?P<match>\d+)' => array(
				array( array( $this, 'getMatch' ), WP_JSON_Server::READABLE ),
				array( array( $this, 'editMatch' ), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
			),
		);
		return array_merge( $routes, $league_routes );
	}

	/**
	 * Retrieve leagues.
	 *
	 * The optional $fields parameter specifies what fields will be included
	 * in the response array.
	 *
	 * @return array contains a collection of League entities.
	 */
	public function getLeagues() {
		global $wp_json_server, $leaguemanager;

		$leagues_list = $leaguemanager->getLeagues();

		if ( ! $leagues_list )
			return array();

		// holds all the leagues data
		$struct = array();

		foreach ( $leagues_list as $league ) {
			$league = get_object_vars( $league );

			$wp_json_server->link_header( 'item', json_url( '/leaguemanager/leagues/' . $league['id'] ), array( 'title' => $league['title'] ) );
			$struct[] = $this->prepare_league( $league );
		}

		return $struct;
	}

	/**
	 * Retrieve a league.
	 *
	 * @param int $id League ID
	 * @param array $fields League fields to return (optional)
	 * @return array League entity
	 */
	public function getLeague( $id ) {
		global $wp_json_server, $leaguemanager;
		$id = (int) $id;

		if ( empty( $id ) )
			return new WP_Error( 'json_league_invalid_id', __( 'Invalid league ID.' ), array( 'status' => 404 ) );

		$league = (array)$leaguemanager->getLeague( $id );

		if ( empty( $league['id'] ) )
			return new WP_Error( 'json_league_invalid_id', __( 'Invalid league ID.' ), array( 'status' => 404 ) );

		$league = $this->prepare_league( $league );
		if ( is_wp_error( $league ) )
			return $league;

		foreach ( $league['meta']['links'] as $rel => $url ) {
			$wp_json_server->link_header( $rel, $url );
		}
		$wp_json_server->link_header( 'alternate',  get_permalink( $id ), array( 'type' => 'text/html' ) );

		return $league;
	}

	/**
	 * Retrieve matches
	 *
	 * @param int $id League ID to retrieve match for
	 * @return array List of Match entities
	 */
	public function getMatches( $id ) {
		global $leaguemanager;

		$matches_list = $leaguemanager->getMatches( "`league_id` = ".$id );

		$struct = array();
		foreach ( $matches_list as $match ) {
			$struct[] = $this->prepare_match( $match );
		}
		return $struct;
	}

	/**
	 * Retrieve a single match
	 *
	 * @param int $match Match ID
	 * @return array Match entity
	 */
	public function getMatch( $match ) {
		global $leaguemanager;

		$match = $leaguemanager->getMatch( $match );
		$data = $this->prepare_match( $match );
		return $data;
	}

	/**
	 * Edit a match.
	 *
	 * The $data parameter only needs to contain fields that should be changed.
	 * All other fields will retain their existing values.
	 *
	 * @param int $id Match id to edit
	 * @param array $data Data construct
	 * @param array $_headers Header data
	 * @return true on success
	 */
	function editMatch( $match, $data, $_headers = array() ) {
		global $leaguemanager;

		$match = (int) $match;

		if ( empty( $match ) )
			return new WP_Error( 'json_match_invalid_id', __( 'Invalid match ID.' ), array( 'status' => 404 ) );

		$match = $leaguemanager->getMatch( $match );

		if ( empty( $match->id ) )
			return new WP_Error( 'json_match_invalid_id', __( 'Invalid match ID.' ), array( 'status' => 404 ) );

		$data['id'] = $match->id;

		$retval = $this->insert_match( $data );
		if ( is_wp_error( $retval ) ) {
			return $retval;
		}

		return $this->getMatch( $match->id );
	}


	/**
	 * Prepares league data for return in an XML-RPC object.
	 *
	 * @access protected
	 *
	 * @param array $league The unprepared league data
	 * @param array $fields The subset of league type fields to return
	 * @return array The prepared league data
	 */
	protected function prepare_league( $league ) {
		// holds the data for this league. built up based on $fields
		$_league = array(
			'id' => (int) $league['id'],
		);

		// prepare common league fields
		$league_fields = array(
			'title'        => $league['title'],
			'seasons'      => $league['seasons'],
			'sport'        => $league['sport']
			// 'link'          => get_permalink( $league['id'] ),
		);

		// Merge requested $league_fields fields into $_league
		$_league = array_merge( $_league, $league_fields );

		// Entity meta
		$_league['meta'] = array(
			'links' => array(
				'self'            => json_url( '/leaguemanager/leagues/' . $league['id'] ),
				'collection'      => json_url( '/leaguemanager/leagues' ),
			),
		);

		return apply_filters( 'json_prepare_league', $_league, $league );
	}



	/**
	 * Prepares match data for returning as a JSON response.
	 *
	 * @param stdClass $match Match object
	 * @return array Match data for JSON serialization
	 */
	protected function prepare_match( $match ) {
		global $wp_json_server, $leaguemanager;

		// holds the data for this match. built up based on $fields
		$_match = array(
			'id' => (int) $match->id,
		);

		// prepare common match fields
		$match_fields = array(
			'home_team'         => (int) $match->home_team,
			'away_team'         => (int) $match->away_team,
			'home_team_name'    => $leaguemanager->getTeam($match->home_team)->title,
			'away_team_name'    => $leaguemanager->getTeam($match->away_team)->title,
			'location'          => $match->location,
			'date'              => $match->date,
			'league_id'         => (int) $match->league_id,
			'home_points'       => $match->home_points,
			'away_points'       => $match->away_points,
			'season'            => $match->season,
			// 'link'           => get_permalink( $match->id ),
		);

		// Merge requested $match_fields fields into $_match
		$_match = array_merge( $_match, $match_fields );

		// Entity meta
		$_match['meta'] = array(
			'links' => array(
				'up' => json_url( sprintf( '/leaguemanager/leagues/%d', (int) $match->league_id ) ),
				'self' => json_url( sprintf( '/leaguemanager/leagues/%d/matches/%d', (int) $match->league_id, (int) $match->id ) )
			),
		);

		return apply_filters( 'json_prepare_match', $_match, $match );
	}

	/**
	 * Helper method for wp_newMatch and wp_editMatch, containing shared logic.
	 *
	 * @param array $content_struct Match data to insert.
	 */
	protected function insert_match( $data ) {
		global $wpdb, $leaguemanager;

		$match = array();
		$update = ! empty( $data['id'] );

		if ( $update ) {
			$current_match = $leaguemanager->getMatch( $data['id'] );
			if ( ! $current_match )
				return new WP_Error( 'json_match_invalid_id', __( 'Invalid match ID.' ), array( 'status' => 400 ) );
			$match['id'] = absint( $data['id'] );
		}
		else {
			// Defaults
		}

		// Match Home Points
		if ( isset( $data['home_points'] ) ) {
			$match['home_points'] = $data['home_points'];
		}

		// Match Away Points
		if ( isset( $data['away_points'] ) ) {
			$match['away_points'] = $data['away_points'];
		}

		// Pre-insert hook
		$can_insert = apply_filters( 'json_pre_insert_match', true, $match, $data, $update );
		if ( is_wp_error( $can_insert ) ) {
			return $can_insert;
		}

		// Match meta
		// TODO: implement this
		if ( $update ) {
			// $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_matches} SET `home_points` = '%s', `away_points` = '%s' WHERE `id` = '%d'", $match['home_points'], $match['away_points'], $match['id'] ) );
			$wpdb->update( $wpdb->leaguemanager_matches, array( 'home_points' => $match['home_points'], 'away_points' => $match['away_points'] ), array( 'id' => $match['id'] ), array( '%s', '%s' ), array( '%d' ) );
			$match_ID = $match['id'];
		}
		else {
			print_r("insert");die();
			// $wpdb->insert( $wpdb->leaguemanager_matches, array( 'home_points' => $match['home_points'], 'away_points' => $match['away_points'] ), array( '%s', '%s' ) );
			$match_ID = (int) $wpdb->insert_id;
		}

		if ( is_wp_error( $match_ID ) ) {
			return $match_ID;
		}

		do_action( 'json_insert_match', $match, $data, $update );

		return $match_ID;
	}
}
