<?php

class AdminController extends Application {
	
	function __construct() {
		
		global $app;
		
		// If user is admin or first user then let them pass otherwise exit
		
		if ($app->uri['action'] == 'setup') {
			// Setup page called so make sure user count = 0
			
			if (count(Admin::list_users()) != 0) {
				
				$app->page->name = 'Page not found';
				$this->loadView('partials/header');
				$this->loadView('partials/footer');
				exit;
				
			}
			
		}
		
		if (in_array($_SESSION['user']['id'], $app->config->admin_users) != TRUE) {
			// User not an admin
			
			$app->page->name = 'Page not found';
			$this->loadView('partials/header');
			$this->loadView('partials/footer');
			exit;
			
		}
		
	}
	
	// Show admin dashboard
	function index() {
		
		global $app;
		
		$app->page->users = Admin::list_users();
		$app->page->users_beta = Admin::list_users_beta();
		$this->loadLayout('admin/index', 'admin');
		
	}
	
	// Setup your rat installation
	function setup() {
		
		global $app;
		
		$app->page->name = 'Setup';
		
		if (isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
			// Do setup
			
			$user_id = User::add($_POST['email']);
			User::signup($user_id, $_POST['username'], $_POST['password']);
			
			$user = User::get_by_email($_POST['email']);
			$_SESSION['user'] = $user;
			
			// Log login
			if (isset($app->plugins->log))
				$app->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'signup');
			
			$app->page->message = 'Rat is now setup and you are logged in!';
			
			// Go forth!
			if (SITE_IDENTIFIER == 'live') {
				header('Location: '.$app->config->url.'?message='.urlencode($app->page->message));
			} else {
				header('Location: '.$app->config->dev_url.'?message='.urlencode($app->page->message));
			}
			
			exit();
			
		} else {
			// Show setup form
			
			$app->page->message = 'Welcome to Rat! Please enter your details:';
			$this->loadLayout('admin/setup');

		}
		
	}
	
	// Show list of beta signups
	function signups() {
		
		global $app;
		
		$app->page->users = Admin::list_users_beta();
		$this->loadLayout('admin/signups', 'admin');
		
	}
	
	// Show list of users
	function users() {
		
		global $app;
		
		$app->page->users = Admin::list_users();
		$this->loadLayout('admin/users', 'admin');
		
	}
	
	// Show most recent entries in the log (not named log to avoid conflict with native PHP function)
	function history() {
		
		global $app;
		
		if (isset($app->plugins->log)) {
			
			$this->loadView('partials/header');
			$this->loadView('admin/menu');
			$app->plugins->log->view();
			$this->loadView('partials/footer');
			
		}
		
	}
	
	// Grant access to a beta signup
	function invite() {
		
		global $app;
		
		$email = $_POST['email'];
		
		if ($email != '') {
			
			// Add invite to database
			$id = Invite::add($_SESSION['user']['id'], $email);
			
			// Log invite
			if (isset($app->plugins->log))
				$app->plugins->log->add($_SESSION['user']['id'], 'invite', $id, 'admin_add', $email);
			
			if (SITE_IDENTIFIER == 'live') {
				$to		= "{$_POST['username']} <{$email}>";
			} else {
				$to		= "{$_POST['username']} <davehs@gmail.com>";
			}
			
			$link		= $app->config->url.'signup.php?code='.$id.'&email='.urlencode($email);
			$headers	= "From: {$_SESSION['user']['username']} <{$_SESSION['user']['email']}>\r\nContent-type: text/html\r\n";
			
			// Load template into $body variable
			$this->loadView('email/invite_admin');
			
			if ($app->config->send_emails == TRUE) {
				// Email user
				mail($to, $subject, $body, $headers);
			}
			
			$app->page->message = 'User invited!';
			
		}

		$this->signups();
		
	}
	
	function grant_invites() {
		
		global $app;
		
		if ($_GET['count'] > 0) {
			
			Admin::update_invites($_GET['count']);
			
			$app->page->message = 'Invites updated!';
			
		}
		
		$this->users();
		
	}
	
}

?>