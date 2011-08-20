<?php

class SessionsController {
	
	function index() {
		
		global $app;
		
		// Not needed?
		
	}
	
	function add() {
		
		global $app;
		
		if ($_POST['email'] && $_POST['password']) {
			
			$user = $app->user->get_by_email($_POST['email']);
			$encrypted_password = md5($_POST['password'].$app->config->encryption_salt);
			
			if ($user['password'] == $encrypted_password) {
				
				$_SESSION['user'] = $user;
				
				// Log login
				if (isset($app->plugins->log))
					$app->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'login');
				
				// Get redirected
				if ($_GET['redirect_to']) {
					header('Location: '.$_GET['redirect_to']);
					exit();
				}
				
				// Go forth
				if (SITE_IDENTIFIER == 'live') {
					header('Location: '.$app->config->url);
				} else {
					header('Location: '.$app->config->dev_url);
				}
				
				exit();
				
			} else {
				
				$app->page->message .= 'Something isn\'t quite right.<br />Please try again...';
				$email = $_POST['email'];
				
			}
			
		}
		
		if (empty($_SESSION['user'])) {
			$app->loadLayout('sessions/add');
		} else {
			$app->page->message = 'You are already logged in!<br />';
			$app->page->message .= $this->link_to('Click here', 'sessions', 'remove').' to logout.';
			$app->loadLayout('partials/message');
		}
		
	}
	
	function remove() {
		
		global $app;
		
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

			// Go forth!
			if (SITE_IDENTIFIER == 'live') {
				header('Location: '.$app->config->url.$app->config->default_controller.'/?message='.urlencode($message));
			} else {
				header('Location: '.$app->config->dev_url.$app->config->default_controller.'/?message='.urlencode($message));
			}
			
			exit();
			
		}
		
		$app->page->message = 'Nothing to see here';
		$app->loadLayout();
		
	}
	
}

?>