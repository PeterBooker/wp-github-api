# WP Github

WP Github handles communication between WordPress and the Github API. It can be included in your theme/plugin or the /mu-plugins/ folder. It is designed to allow easy fetching of data from Github to use/display on your WordPress website.

It is licensed under [GPL v2](http://www.gnu.org/licenses/gpl-2.0.html).

You can see more information about Github's v3 API here: https://developer.github.com/v3/

## Authentication

A personal (oAuth) access token is used for authentication, which you can get from your 'Account Settings -> Applications' page. It only requires the 'repo' and 'public_repo' scopes.

## Supported Endpoints

Not all endpoints are supported yet, but most GET endpoints are planned. PUT and DELETE Endpoints will be considered if there is interest/demand. You can currently access the following methods:

* get_user_profile( $user ) - Gets the Profile of given Username.

* get_own_repos() - List all Repos owned by the authenticated User.

* get_user_repos( $user ) - List all Repositories of given User.

* get_org_repos( $organization ) - List all Repositories of given Organization.

* get_repo( $owner, $repo ) - Get Repository Information by given Owner and Repository.

* get_repo_languages( $owner, $repo ) - List the Languages in given Repository.

* get_repo_tags( $owner, $repo ) - List the Tags in given Repository.

* get_repo_branches( $owner, $repo ) - List the Branches in given Repository.

* get_repo_branch( $owner, $repo, $branch ) - Get the Branch by given Branch Name and Repository.

* get_repo_teams( $owner, $repo ) - List the Teams in given Repository.

* get_repo_commits( $owner, $repo ) - Lists the Commits by given Repository, Owner and optionally Time Period.

* get_user_gists( $user, $since ) - List the Gists owned by given Username.

* get_repo_issues( $owner, $repo ) - List the Issues by given Repository and Owner.

## Usage Examples

To get started check the examples below.

### Basic Example

This basic example shows you how to fetch the details of your User Profile.

```php
<?php

$oauth_token = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

$github = new WP_Github( $oauth_token );

$response = $github->get_user_profile();

print_r( $response );

?>
```

### Advanced Example

This advanced example shows you how to fetch the Commits for a particular Repository. It involves setting pagination options as well as a time period to return the data for and a timezone for that time period.

```php
<?php

$oauth_token = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

$github = new WP_Github( $oauth_token );

$github->set_page( 1 );

$github->set_per_page( 100 );

$github->set_timezone( 'Europe/London' );

$github->set_time_period_until( date( 'Y-m-d' ), '3 months' );

$owner = 'XXXXXXXXXX';

$repo = 'XXXXXXXXXX';

$response = $github->get_repo_commits( $owner, $repo );

print_r( $response );

?>
```