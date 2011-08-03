<?php

// Config file contains lots of handy variables
require_once 'config/config.php';

// Convert config vars to object, create app object, add config vars to app object
$app_vars = $app;
unset($app);
class app {
}
$app = new app;
$app = (object) $app_vars;

require_once 'models/item.php';
$app->item = new item;

// Setup database
require_once 'config/database.php';

// Start session
session_start();

// Load admin model
require_once 'models/admin.php';
$app->admin = new admin;

// Finds page name
preg_match("/[a-zA-Z0-9]+\.php/", $_SERVER['PHP_SELF'], $result);

// If user is logged out, app is private and page is not in public_pages then show splash page
if ($_SESSION['user'] == NULL && $app->private == TRUE && in_array($result[0], $app->public_pages) == FALSE) {

	if (count($admin->list_users()) == 0 && $result[0] == 'admin.php') {

		// Make an exception for setup
		
		// So at the moment, setup requires $app['private'] to be TRUE
		// and admin.php must NOT be in public_pages
		
	} else {

		// Show splash page
		include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
		include 'themes/'.$GLOBALS['app']['theme'].'/splash.php';
		include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

		// Stop processing the rest of the page
		exit();		
		
	}

}

// Load other models
require_once 'models/user.php';
$app->user = new user;
require_once 'models/invite.php';
$app->invite = new invite;
require_once 'models/friend.php';
$app->friend = new friend;
require_once 'models/item.php';
$app->item = new item;
require_once 'models/comment.php';
$app->comment = new comment;
require_once 'models/like.php';
$app->like = new like;

?>