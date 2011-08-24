<?php

class InvitesController {
	
	function index() {
		
		global $app;
		
		$app->page->invites_remaining = $_SESSION['user']['invites'];
		$app->page->invites = $app->invite->list_sent($_SESSION['user']['id']);
		
		$app->page->name = 'Invites';
		$app->loadLayout('invites/index');
		
	}
	
	function add() {
		
		global $app;
		
		
		
	}
	
	function remove() {
		
		global $app;
		
		
		
	}
	
}

/*

//	Critical: Feature must be enabled and user must be logged in

if ($app->config->invites['enabled'] == FALSE || empty($_SESSION['user'])) {
	
	$app->page->name = 'Page not found';
	$app->loadView('partials/header');
	$app->loadView('partials/footer');
	exit;
	
}

// Header

$app->page->name = 'Invites';
$app->loadView('partials/header');

// Process new invites

if (isset($_POST['email'])) {
	
	if ($_SESSION['user']['invites'] < 1)
		$error .= 'You don\'t have any invites remaining.<br />';

	// Check if email contains spaces
	if (User::check_contains_spaces($_POST['email']) == TRUE)
		$error .= 'Email address cannot contain spaces.<br />';

	// Check if already invited
	if ($app->invite->check_invited($_SESSION['user']['id'], $_POST['email']) == TRUE)
		$error .= 'You have already invited this person.<br />';
	
	// Check if already a user
	if (User::get_by_email($_POST['email']) == TRUE)
		$error .= 'This person is already using '.$app->config->name.'!<br />';

	if ($error == '') {
		// no problems so do signup + login

		// add invite to database
		$id = $app->invite->add($_SESSION['user']['id'], $_POST['email']);

		// decrement invites in users table
		User::update_invites($_SESSION['user']['id'], -1);

		// award points
		if (isset($app->plugins->points))
			$app->plugins->points->update($_SESSION['user']['id'], $app->plugins->points['per_invite_sent']);

		// log invite
		if (isset($app->plugins->log))
			$app->plugins->log->add($_SESSION['user']['id'], 'invite', $id, 'add', $_POST['email']);

		if (SITE_IDENTIFIER == 'live') {
			$to		= "{$_POST['username']} <{$_POST['email']}>";
		} else {
			$to		= "{$_POST['username']} <davehs@gmail.com>";
		}

		$link = $app->config->url.'signup.php?code='.$id.'&email='.urlencode($_POST['email']);
		$headers = "From: {$_SESSION['user']['username']} <{$_SESSION['user']['email']}>\r\nBcc: davehs@gmail.com\r\nContent-type: text/html\r\n";

		// Load subject and body from template
		$app->loadView('email/invite_friend');

		if ($app->config->send_emails == TRUE) {
			// Email user
			mail($to, $subject, $body, $headers);
		}

		$app->page->message = 'Invite sent!';
		$app->loadView('partials/message');

	} else {
		
		$_GET['email'] = $_POST['email'];
		
		$app->page->message = $error;
		$app->loadView('partials/message');
		
	}
	
}

// Show invite form

$invites_remaining = $_SESSION['user']['invites'];
$app->loadView('invites/index');

// Show sent invites

$app->page->invites = $app->invite->list_sent($_SESSION['user']['id']);
$app->loadView('invites_list');

// Footer

$app->loadView('partials/footer');

*/

?>