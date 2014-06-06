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
         * Timezone e.g. Europe/London
         * See full list: https://en.wikipedia.org/wiki/List_of_tz_database_time_zones
         *
         * @var string
         */
        private $timezone;

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
        public function get_own_repos( $type = 'all', $sort = 'full_name', $direction = 'desc' ) {

            $url = $this->api_url . '/user/repos';

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

            $url = $this->api_url . '/users/' . $user . '/repos';

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

            $url = $this->api_url . '/orgs/' . $organization . '/repos';

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

            $url = $this->api_url . '/repos/' . $owner . '/' . $repo . '';

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

            $default_args = array(
                'method' => $method,
                'timeout' => 5,
                'httpversion' => '1.1',
                'headers' => array(
                    'Authorization' => 'token ' . $this->oauth_token,
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