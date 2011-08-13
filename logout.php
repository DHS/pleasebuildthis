<?php

require_once 'config/init.php';

if (!empty($_SESSION['user'])) {
	// do logout

	$user_id = $_SESSION['user']['id'];

	session_unset();
	session_destroy();

	// log logout
	if (isset($app->plugins->log))
		$app->plugins->log->add($user_id, 'user', NULL, 'logout');

	$_SESSION['user'] = array();
	
	$message = 'You are now logged out.';
	
}

// Header

include 'themes/'.$app->config->theme.'/header.php';

// Show login form

include 'themes/'.$app->config->theme.'/login.php';

// Footer

include 'themes/'.$app->config->theme.'/footer.php';

?>