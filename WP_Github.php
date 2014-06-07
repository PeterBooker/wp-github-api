<?php
/**
 * Github API
 *
 * Handles communication between WordPress and the Github API
 * Supports v3 of the API - https://developer.github.com/v3/
 *
 * @version 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WP_Github' ) ) {

    class WP_Github {

        /**
         * Github API URL
         *
         * @var string
         */
        private $api_url = 'https://api.github.com';

        /**
         * Github oAuth Token
         * More information - https://github.com/blog/1509-personal-api-tokens
         *
         * @var string
         */
        private $oauth_token;

        /**
         * Items per Page
         *
         * @var int
         */
        private $per_page = 30;

        /**
         * Page Number
         *
         * @var int
         */
        private $page = 1;

        /**
         * Timezone e.g. Europe/London
         * See full list: https://en.wikipedia.org/wiki/List_of_tz_database_time_zones
         *
         * @var string
         */
        private $timezone = 'UTC';

        /**
         * Time Period
         * since - until
         *
         * @var array
         */
        private $time_period;

        /**
         * Custom WP HTTP API Args
         *
         * @var array
         */
        private $custom_http_args = array();

        /**
         * Gets the API URL
         *
         * @return string
         */
        public function get_api_url() {

            return $this->api_url;

        }

        /**
         * Sets the API URL
         *
         * @param string $api_url
         */
        public function set_api_url( $api_url ) {

            $this->api_url = $api_url;

        }

        /**
         * Gets the Items per Page
         *
         * @return string
         */
        public function get_per_page() {

            return $this->per_page;

        }

        /**
         * Sets the Items per Page
         *
         * @param string $per_page
         */
        public function set_per_page( $per_page ) {

            $this->per_page = $per_page;

        }

        /**
         * Gets the Page Number
         *
         * @return string
         */
        public function get_page() {

            return $this->page;

        }

        /**
         * Sets the Page Number
         *
         * @param string $page
         */
        public function set_page( $page ) {

            $this->page = $page;

        }

        /**
         * Gets the Timezone
         *
         * @return string
         */
        public function get_timezone() {

            return $this->timezone;

        }

        /**
         * Sets the Timezone
         *
         * @param string $timezone
         */
        public function set_timezone( $timezone ) {

            $this->timezone = $timezone;

        }

        /**
         * Gets the Time Period
         *
         * @return array
         */
        public function get_time_period() {

            return $this->time_period;

        }

        /**
         * Sets the Time Period
         *
         * @param string $since
         * @param string $until
         */
        public function set_time_period( $since = null, $until = null ) {

            $this->timezone = array(
                'since' => $since,
                'until' => $until,
            );

        }

        /**
         * Helper - Set the Time Period using Since datetime and an Offset
         * e.g. $since = '2014-06-07', $offset = '6 months'
         *
         * @param string $since
         * @param string $offset
         */
        public function set_time_period_since( $since, $offset ) {

            $until = new DateTime( $since, new DateTimeZone( $this->timezone ) );
            $since = new DateTime( $since, new DateTimeZone( $this->timezone ) );

            $since->modify( '+' . $this->sanitize_offset( $offset ) );

            $this->time_period = array(
                'since' => $since->format( 'c' ),
                'until' => $until->format( 'c' ),
            );

        }

        /**
         * Helper - Set the Time Period using Until datetime and an Offset
         * e.g. $since = '2014-04', $offset = '6 months'
         *
         * @param string $until
         * @param string $offset
         */
        public function set_time_period_until( $until, $offset ) {

            $since = new DateTime( $until, new DateTimeZone( $this->timezone ) );
            $until = new DateTime( $until, new DateTimeZone( $this->timezone ) );

            $since->modify( '-' . $this->sanitize_offset( $offset ) );

            $this->time_period = array(
                'since' => $since->format( 'c' ),
                'until' => $until->format( 'c' ),
            );

        }

        /**
         * Sanitizes the Offset value to remove negative/positive operators.
         *
         * @param string $offset
         * @return string
         */
        private function sanitize_offset( $offset ) {

            if ( '-' == substr( $offset, 0, 1 ) || '+' == substr( $offset, 0, 1 ) ) {

                return substr( $offset, 1 );

            } else {

                return $offset;

            }

        }

        /**
         * Gets Custom HTTP API Args
         * See defaults: http://codex.wordpress.org/Function_Reference/wp_remote_get#Default_Usage
         *
         * @return array
         */
        public function get_http_args() {

            return $this->custom_http_args;

        }

        /**
         * Sets the Custom HTTP API Args
         * See defaults: http://codex.wordpress.org/Function_Reference/wp_remote_get#Default_Usage
         *
         * @param array $custom_http_args
         */
        public function set_http_args( $custom_http_args ) {

            $this->custom_http_args = $custom_http_args;

        }

        /**
         * Constructor
         *
         * @param string $oauth_token
         */
        public function __construct( $oauth_token = null ) {

            $this->oauth_token = $oauth_token;

        }

        /**
         * Gets the Profile of given Username.
         *
         * @param $username
         * @return array|mixed
         */
        public function get_user_profile( $username ) {

            $url = $this->api_url . 'users/' . $username;

            $response = $this->make_request( $url );

            return $response;

        }

        /**
         * List all Repos owned by the authenticated user.
         *
         * @param string $type
         * @param string $sort
         * @param string $direction
         * @return array|mixed
         */
        public function get_own_repos( $type = null, $sort = null, $direction = null ) {

            $params = array(
                'page' => $this->page,
                'per_page' => $this->per_page,
                'type' => $type,
                'sort' => $sort,
                'direction' => $direction,
            );

            $url = $this->api_url . '/user/repos?' . http_build_query( $params, '', '&amp;' );

            $response = $this->make_request( $url );

            return $response;

        }

        /**
         * List all Repositories of given User.
         *
         * @param $user
         * @return array|mixed
         */
        public function get_user_repos( $user ) {

            $params = array(
                'page' => $this->page,
                'per_page' => $this->per_page,
            );

            $url = $this->api_url . '/users/' . $user . '/repos?' . http_build_query( $params, '', '&amp;' );

            $response = $this->make_request( $url );

            return $response;

        }

        /**
         * List all Repositories of given Organization.
         *
         * @param $organization
         * @return array|mixed
         */
        public function get_org_repos( $organization ) {

            $params = array(
                'page' => $this->page,
                'per_page' => $this->per_page,
            );

            $url = $this->api_url . '/orgs/' . $organization . '/repos?' . http_build_query( $params, '', '&amp;' );

            $response = $this->make_request( $url );

            return $response;

        }

        /**
         * Get Repository Information by given Owner and Repository.
         *
         * @param $owner
         * @param $repo
         * @return array|mixed
         */
        public function get_repo( $owner, $repo ) {

            $url = $this->api_url . '/repos/' . $owner . '/' . $repo;

            $response = $this->make_request( $url );

            return $response;

        }

        /**
         * List the Languages in given Repository.
         *
         * @param $owner
         * @param $repo
         * @return array|mixed
         */
        public function get_repo_languages( $owner, $repo ) {

            $url = $this->api_url . '/repos/' . $owner . '/' . $repo . '/languages';

            $response = $this->make_request( $url );

            return $response;

        }

        /**
         * List the Tags in given Repository.
         *
         * @param $owner
         * @param $repo
         * @return array|mixed
         */
        public function get_repo_tags( $owner, $repo ) {

            $params = array(
                'page' => $this->page,
                'per_page' => $this->per_page,
            );

            $url = $this->api_url . '/repos/' . $owner . '/' . $repo . '/tags?' . http_build_query( $params, '', '&amp;' );

            $response = $this->make_request( $url );

            return $response;

        }

        /**
         * List the Branches in given Repository.
         *
         * @param $owner
         * @param $repo
         * @return array|mixed
         */
        public function get_repo_branches( $owner, $repo ) {

            $params = array(
                'page' => $this->page,
                'per_page' => $this->per_page,
            );

            $url = $this->api_url . '/repos/' . $owner . '/' . $repo . '/branches?' . http_build_query( $params, '', '&amp;' );

            $response = $this->make_request( $url );

            return $response;

        }

        /**
         * Get the Branch by given Branch Name and Repository.
         *
         * @param $owner
         * @param $repo
         * @param $branch
         * @return array|mixed
         */
        public function get_repo_branch( $owner, $repo, $branch ) {

            $url = $this->api_url . '/repos/' . $owner . '/' . $repo . '/branches/' . $branch;

            $response = $this->make_request( $url );

            return $response;

        }

        /**
         * List the Teams in given Repository.
         *
         * @param $owner
         * @param $repo
         * @return array|mixed
         */
        public function get_repo_teams( $owner, $repo ) {

            $params = array(
                'page' => $this->page,
                'per_page' => $this->per_page,
            );

            $url = $this->api_url . '/repos/' . $owner . '/' . $repo . '/teams?' . http_build_query( $params, '', '&amp;' );

            $response = $this->make_request( $url );

            return $response;

        }

        /**
         * Lists the Commits by given Repository, Owner and optionally Time Period.
         *
         * @param string $owner
         * @param string $repo
         * @return array|mixed
         */
        public function get_repo_commits( $owner, $repo ) {

            $params = array(
                'page' => $this->page,
                'per_page' => $this->per_page,
                'since' => $this->time_period['since'],
                'until' => $this->time_period['until'],
            );

            $url = $this->api_url . '/repos/' . $owner . '/' . $repo . '/commits?' . http_build_query( $params, '', '&amp;' );

            $response = $this->make_request( $url );

            echo $url;

            return $response;

        }

        /**
         * List the Gists owned by given Username.
         *
         * @param string $user
         * @param string $since
         * @return array|mixed
         */
        public function get_user_gists( $user, $since ) {

            $params = array(
                'page' => $this->page,
                'per_page' => $this->per_page,
                'since' => $since,
            );

            $url = $this->api_url . '/users/' . $user . '/gists?' . http_build_query( $params, '', '&amp;' );

            $response = $this->make_request( $url );

            return $response;

        }

        /**
         * List the Issues by given Repository and Owner.
         *
         * @param string $owner
         * @param string $repo
         * @param string|int $milestone
         * @param string $state
         * @param string $assignee
         * @param string $creator
         * @param string $mentioned
         * @param string $labels
         * @param string $sort
         * @param string $direction
         * @param string $since
         * @return array|mixed
         */
        public function get_repo_issues( $owner, $repo, $milestone = null, $state = null, $assignee = null, $creator = null, $mentioned = null, $labels = null, $sort = null, $direction = null, $since = null ) {

            $params = array(
                'page' => $this->page,
                'per_page' => $this->per_page,
                'milestone' => $milestone,
                'state' => $state,
                'assignee' => $assignee,
                'creator' => $creator,
                'mentioned' => $mentioned,
                'labels' => $labels,
                'sort' => $sort,
                'direction' => $direction,
                'since' => $since,
            );

            $url = $this->api_url . '/repos/' . $owner . '/' . $repo . '/issues?' . http_build_query( $params, '', '&amp;' );

            $response = $this->make_request( $url );

            return $response;

        }

        /**
         * Make the HTTP Request
         *
         * @param string $url
         * @param string $method
         * @return array|mixed
         */
        public function make_request( $url, $method = 'GET' ) {

            echo $url;

            $default_args = array(
                'method' => $method,
                'timeout' => 5,
                'httpversion' => '1.1',
                'headers' => array(
                    'Authorization' => 'token ' . $this->oauth_token,
                    'Time-Zone' => $this->timezone,
                ),
                'body' => null,
            );

            $args = wp_parse_args( $this->custom_http_args, $default_args );

            $response = wp_remote_request( $url, $args );

            /*
             * Check for HTTP API Error
             */
            if ( is_wp_error( $response ) ) {

                return $response->errors;

            } else {

                $status = absint( wp_remote_retrieve_response_code( $response ) );

                if ( 200 == $status ) {

                    return json_decode( $response['body'] );

                } else {

                    //return $response['code'] . ' - ' . $response['message'];
                    return $response['response'];

                }

            }

        }

    }

}