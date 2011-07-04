<?php

require_once 'config/initialize.php';

//	Critical: Feature must be enabled and user must be logged in

if ($app['invites']['enabled'] == FALSE || empty($_SESSION['user'])) {
	
	$app['page_name'] = 'Page not found';
	include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';
	exit;
	
}

/* Header */

$app['page_name'] = 'Invites';
include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

/* Process new invites */

if ($_POST['email'] != '') {
	
	if ($_SESSION['user']['invites'] < 1)
		$error .= 'You don\'t have any invites remaining.<br />';

	// Check if email contains spaces
	if (user_contains_spaces($_POST['email']) == TRUE)
		$error .= 'Email address cannot contain spaces.<br />';

	// Check if already invited
	if (invites_already_invited($_SESSION['user']['id'], $_POST['email']) == TRUE)
		$error .= 'You have already invited this person.<br />';
	
	// Check if already a user
	if (user_get_by_email($_POST['email']) == TRUE)
		$error .= 'This person is already using '.$GLOBALS['app']['name'].'!<br />';

	if ($error == '') {
		// no problems so do signup + login

		// add invite to database
		$id = invites_add($_SESSION['user']['id'], $_POST['email']);

		// decrement invites in users table
		user_invites_update($_SESSION['user']['id'], -1);

		// award points
		if (is_object($GLOBALS['points']))
			$GLOBALS['points']->update($_SESSION['user']['id'], $app['points']['per_invite_sent']);

		// log invite
		if (is_object($GLOBALS['log']))
			$GLOBALS['log']->add($_SESSION['user']['id'], 'invite', $id, 'add', $_POST['email']);

		if (SITE_IDENTIFIER == 'live') {
			$to		= "{$_POST['username']} <{$_POST['email']}>";
		} else {
			$to		= "{$_POST['username']} <davehs@gmail.com>";
		}

		$link = $GLOBALS['app']['url'].'signup.php?code='.$id.'&email='.urlencode($_POST['email']);

		$subject	= "[{$GLOBALS['app']['name']}] An invitation from {$_SESSION['user']['username']}";
		$body		= "Hi there,\n\nI think you should check out {$GLOBALS['app']['name']}! Click the following link to get started:\n\n{$link}\n\nRegards,\n\n{$_SESSION['user']['username']}";
		$headers	= "From: {$_SESSION['user']['username']} <{$_SESSION['user']['email']}>\r\nBcc: davehs@gmail.com\r\n";

		if ($GLOBALS['app']['send_emails'] == TRUE) {
			// Email user
			mail($to, $subject, $body, $headers);
		}

		$message = 'Invite sent!';
		include 'themes/'.$GLOBALS['app']['theme'].'/message.php';

	} else {
		
		$_GET['email'] = $_POST['email'];
		
		$message = $error;
		include 'themes/'.$GLOBALS['app']['theme'].'/message.php';
		
	}
	
}

/* Show invite form */

$invites_remaining = $_SESSION['user']['invites'];
include 'themes/'.$GLOBALS['app']['theme'].'/invites.php';

/* Show sent invites */

$invites_sent = invites_sent($_SESSION['user']['id']);
include 'themes/'.$GLOBALS['app']['theme'].'/invites_sent.php';

/* Footer */

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>