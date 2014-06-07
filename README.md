# WP Github

WP Github handles communication between WordPress and the Github API. It can be included in your theme/plugin or the /mu-plugins/ folder. It is designed to allow easy fetching of data from Github to use/display on your WordPress website.

It is licensed under [GPL v2](http://www.gnu.org/licenses/gpl-2.0.html).

You can see more information about Github's v3 API here: https://developer.github.com/v3/

## Authentication

A personal (oAuth) access token is used for authentication, which you can get from your 'Account Settings -> Applications' page. It only requires the 'repo' and 'public_repo' scopes.

## Supported Endpoints

Not all endpoints are supported yet, but most GET endpoints are planned. PUT and DELETE Endpoints will be considered if there is interest/demand. You can currently access the following methods:

* get_user_profile( $username ) - Gets the Profile of given Username.

* 


## Usage Examples

To get started check the examples below.

### Basic Example

```php
<?php

print_r( $response );

?>
```

### Advanced Example

```php
<?php

print_r( $response );

?>
```