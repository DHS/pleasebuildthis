<?php

require_once 'config/init.php';

// Critical: Setup wizard creates admin as first user

if (count($app->admin->list_users()) == 0 && $_GET['page'] == '') {
	
	$page['name'] = 'Setup';
	
	$_GET['id'] = 1;
	$password = generate_password();
	
	$message = 'Welcome to Rat! Please enter your details:';
	include 'themes/'.$app->theme.'/header.php';
	include 'themes/'.$app->theme.'/admin_setup.php';
	include 'themes/'.$app->theme.'/footer.php';
	
	exit();
	
} elseif (count($app->admin->list_users()) == 0 && $_GET['page'] == 'invite') {
	
	$page['name'] = 'Setup';
	
	// Do signup

	$user_id = $app->user->add($_POST['email']);
	$app->user->signup($user_id, $_POST['username'], $_POST['password']);
	
	$user = $app->user->get_by_email($_POST['email']);
	$_SESSION['user'] = $user;
	
	// Log login
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_SESSION['user']['id'], 'user', NULL, 'signup');
	
	$message = 'Rat is now setup and you are logged in!';
	
	// Go forth!
	if (SITE_IDENTIFIER == 'live') {
		header('Location: '.$app->url.'?message='.urlencode($message));
	} else {
		header('Location: '.$app->dev_url.'?message='.urlencode($message));
	}
		
	exit();
	
}

//	Critical: User must have admin capability

if (in_array($_SESSION['user']['id'], $app->admin_users) != TRUE) {

	$page['name'] = 'Page not found';
	include 'themes/'.$app->theme.'/header.php';
	include 'themes/'.$app->theme.'/footer.php';
	exit;

}

/*	
*	Admin page functions
*
*		1. Dashboard
*		2. List beta signups
*		3. Invite beta signups
*		4. List users
*		5. Give users invites
*	
*/

function dashboard() {
	
	global $app;
	
	$user_count = count($app->admin->list_users());
	$waiting_user_count = count($app->admin->list_users_beta());
	
	include 'themes/'.$app->theme.'/admin_dashboard.php';
	
}

function signups() {
	
	global $app;
	
	$waiting_users = $app->admin->list_users_beta();
	$waiting_user_count = count($waiting_users);
	
	include 'themes/'.$app->theme.'/admin_signups.php';
	
}

function invite() {

	global $app;

	if ($_GET['email'] != '') {
		
		// add invite to database
		$id = $app->invite->add($_SESSION['user']['id'], $_GET['email']);
		
		// log invite
		if (is_object($GLOBALS['log']))
			$GLOBALS['log']->add($_SESSION['user']['id'], 'invite', $id, 'admin_add', $_GET['email']);
		
		if (SITE_IDENTIFIER == 'live') {
			$to		= "{$_POST['username']} <{$_GET['email']}>";
		} else {
			$to		= "{$_POST['username']} <davehs@gmail.com>";
		}
		
		$url		= $app->url.'signup.php?code='.$id.'&email='.urlencode($_GET['email']);
		$headers	= "From: {$_SESSION['user']['username']} <{$_SESSION['user']['email']}>\r\nContent-type: text/html\r\n";
		
		// Load template into $body variable
		include 'themes/'.$app->theme.'/email_invite_admin.php';
		
		if ($app->send_emails == TRUE) {
			// Email user
			mail($to, $subject, $body, $headers);
		}
		
		$message = 'User invited!';
		include 'themes/'.$app->theme.'/message.php';
		
		signups();
		
	}
	
}

function users() {
	
	global $app;
	
	$users = $app->admin->list_users();
	$user_count = count($users);
	
	include 'themes/'.$app->theme.'/admin_users.php';

}

function grant_invites() {
	
	global $app;
		
	if ($_GET['count'] > 0) {
		
		$app->admin->update_invites($_GET['count']);
		
		$message = 'Invites updated!';
		include 'themes/'.$app->theme.'/message.php';
		
		users();
		
	}
	
}

function history() {
	// Should be log() but that's a native PHP function
	// Show most recent entries in the log
	
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->view();

}

function generate_password() {
	$password = '';
	$source = 'abcdefghijklmnpqrstuvwxyz123456789';
	while (strlen($password) < 6) {
		$password .= $source[rand(0, strlen($source))];
	}
	return $password;
}


// Selector

$page['selector'] = $_GET['page'];
if ($page['selector'] == NULL)
	$page['selector'] = 'dashboard';

// Header

$page['name'] = 'Admin - '.ucfirst(strtolower($page['selector']));
include 'themes/'.$app->theme.'/header.php';
include 'themes/'.$app->theme.'/admin_menu.php';

// Show page determined by selector

$page['selector']();

// Footer

include 'themes/'.$app->theme.'/footer.php';

?>