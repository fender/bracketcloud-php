bracketcloud-php
================

This is a PHP wrapper for the BracketCloud API. BracketCloud integrates easy-to-use tournament generation, management and social tools in one platform accessible from any computer, phone or tablet you use.

## Usage

* You must have a BracketCloud user account to use the API. http://bracketcloud.com/user/register
* You must read and agree to the BracketCloud API Terms of Service. http://bracketcloud.com/legal
* Learn about the REST API from the documentation pages. http://bracketcloud.com/developers/api

## Development

This library is under development. Matches and participant interactions coming soon!

## Getting started

```php
<?php

include('bracketcloud.lib.php');

/**
 * Replace API_KEY with your BracketCloud API Key.
 */
$request = new BracketCloudAPIRequest(API_KEY);

// ... do something with $request!

?>
```

## Examples

### Tournament searching
```php
<?php

/**
 * Let's try searching for tournaments using addArgument() and execute().
 * addArgument() can be chained as seen below.
 * For more details about arguments, check out the API docs!
 */
$tournaments = $request->addArgument('type', 'bracket')
  ->addArgument('sort', 'title-desc')
  ->execute();

/**
 * We now have an array of full tournament objects in $tournaments.
 */
foreach ($tournaments as $tournament) {
  echo $tournament->title;
}

?>
```

### Get a single tournament
```php
<?php

/**
 * All request methods will return FALSE if there was an API error.
 * You can access details about the error from $request->error;
 */
if ($tournament = $request->getTournament(123)) {
  echo $tournament->title;
}
else {
  echo 'API error returned: ' . $request->error;
}

?>
```

### Create a tournament
```php
<?php

$params = array('type' => 'bracket', 'title' => 'My first tournament');

if ($tournament = $request->createTournament($params)) {
  echo $tournament->title . ' created successfully!';
}

?>
```

### Update a tournament
```php
<?php

$params = array('title' => 'My new title', 'signup' => FALSE);

if ($tournament = $request->updateTournament(123, $params)) {
  echo $tournament->title . ' updated successfully!';
}

?>
```

### Delete a tournament
```php
<?php

if ($request->deleteTournament(123)) {
  echo 'Tournament deleted successfully!';
}

?>
```
