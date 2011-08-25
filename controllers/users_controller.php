<?php

class UsersController extends Application {
	
	function __construct() {
		
		// Check if user is logged in and trying to signup
		if ($this->uri['action'] == 'add' && !empty($_SESSION['user'])) {

			$this->title = 'Signup';
			$this->message = 'You are already logged in!';
			$this->loadView('partials/header');
			$this->loadView('partials/footer');
			exit;

		}
		
	}
	
	// Show a list of users / not used
	function index() {
		
		// Not needed?
		
	}
	
	// Add a user / signup
	function add($code) {
		
		if ($_POST['email'] != '') {
			
			if ($_POST['code'] != '') {
				
				$this->do_signup('code');
				
			} else {
				
				if ($this->config->beta == TRUE) {
					
					$this->do_signup('beta');
					
				} else {
					
					$this->do_signup('full');
					
				}
				
			}
			
		} else {
			
			// Show signup form
			
			if ($this->config->beta == TRUE) {
				// Show beta signup form
				$this->title = 'Beta signup';
				$this->loadLayout('users/add_beta');
			} else {
				// Show full signup form
				
				if (isset($code)) {
					$this->page['code'] = $code;
				}
				
				$this->title = 'Signup';
				$this->loadLayout('users/add');
				
			}
			
		}
		
	}
	
	// Show a user / user page
	function show($id) {
		
		$this->user = User::get_by_id($id);
		$this->items = User::items($id);

		$this->title = $this->user->username;		
		$this->loadLayout('users/show');
		
	}
	
	// Update user: change passsword, update profile
	function update($page) {
		
		if (!isset($page)) {
			
			$page = 'password';
			
		} elseif ($page == 'password') {
			
			if (isset($_POST['old_password']) && isset($_POST['new_password1']) && isset($_POST['new_password2'])) {
				$this->update_password($this->config->encryption_salt);
			}
			
		} elseif ($page == 'profile') {
			
			if (isset($_POST['full_name']) || isset($_POST['bio']) || isset($_POST['url'])) {
				$this->update_profile();
			}
			
		}
		
		$this->user = User::get_by_id($_SESSION['user']['id']);

		$this->title = 'Settings';
		$this->loadLayout('users/update_'.$page);
		
	}
	
	// Password reset
	function reset($code) {
		
		if (isset($_SESSION['user'])) {
			
			$this->title = 'Page not found';
			$this->loadLayout();
			exit;
			
		}
		
		if (isset($code)) {
			// Process reset
			
			// If two passwords submitted then check, otherwise show form
			if (isset($_POST['password1']) && isset($_POST['password2'])) {
				
				if (User::check_password_reset_code($code) == FALSE) {
					exit();
				}
				
				if ($_POST['password1'] == '' || $_POST['password2'] == '') {
					$error .= 'Please enter your password twice.<br />';
				}
				
				if ($_POST['password1'] != $_POST['password2']) {
					$error .= 'Passwords do not match.<br />';
				}
				
				// Error processing
				if ($error == '') {
					
					$user_id = User::check_password_reset_code($code);
					
					// Do update
					User::update_password($user_id, $_POST['password1'], $this->config->encryption_salt);
					
					$user = User::get_by_id($user_id);
					
					// Start session
					foreach ($user as $key => $value) {
						$user_array[$key] = $value;
					}
					$_SESSION['user'] = $user_array;
					
					// Log login
					if (isset($this->plugins->log)) {
						$this->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'login');
					}
					
					// Set welcome message
					$this->message = urlencode('Password updated! Welcome back to '.$this->config->name.'!');
					
					// If redirect_to is set then redirect
					if (isset($_GET['redirect_to'])) {
						header('Location: '.$_GET['redirect_to'].'?message='.$this->message);
						exit();
					}
					
					// Go forth!
					if (SITE_IDENTIFIER == 'live') {
						header('Location: '.$this->config->url.$this->config->default_controller.'/?message='.$this->message);
					} else {
						header('Location: '.$this->config->dev_url.$this->config->default_controller.'/?message='.$this->message);
					}
					
					exit();
					
				} else {
					// Show error message
					
					$this->message = $error;
					$this->loadView('partials/header');
					if (User::check_password_reset_code($code) != FALSE) {
						$this->loadView('reset');
					}
					$this->loadView('partials/footer');
					
				}
				
			} else {
				// Code present so show password reset form
				
				if (User::check_password_reset_code($code) == TRUE) {
					// Invite code valid
					
					$this->code = $code;
					$this->loadLayout('users/reset');

				} else {
					
					$this->title = 'Page not found';
					$this->loadLayout();
					exit;
					
				}
				
			}
			
		} elseif (empty($_SESSION['user'])) {
			// No code in URL so show new reset form
			
			if (isset($_POST['email'])) {
				// Email submitted so send password reset email
				
				$user = User::get_by_email($_POST['email']);
				
				// Check is a user
				if ($user != NULL) {
					
					// Generate code
					$code = User::generate_password_reset_code($user->id);
					
					$to = $_POST['email'];
					$link = substr($this->config->url, 0, -1).$this->link_to(NULL, 'users', 'reset', $code);
					$headers = "From: {$this->config->name} <robot@blah.com>\r\nContent-type: text/html\r\n";
					
					// Load subject and body from template
					$this->loadView('emails/password_reset');
					
					// Email user
					if ($this->config->send_emails == TRUE) {
						mail($to, $subject, $body, $headers);
					}
					
				}
				
				$this->message = 'Check your email for instructions about how to reset your password!';
				$this->loadLayout();
				
			} else {
				
				$this->loadLayout('users/reset_new');
				
			}
			
		} else {
			
			$this->title = 'Page not found';
			$this->loadView('partials/header');
			$this->loadView('partials/footer');
			exit;
			
		}
		
	}
	
	// Confirm email address
	function confirm($email) {
		
		
		
	}
	
	// Show user profile in json format
	function json($username) {
		
		$user['user'] = User::get_by_username($username);
		$user['items'] = User::items($user['user']->id);
		$this->json = $user;
		$this->loadView('pages/json');
		
	}
	
	// Helper function: update password
	private function update_password($salt) {
		
		if (md5($_POST['old_password'].$salt) == $_SESSION['user']['password']) {
			// Check old passwords match
			
			if ($_POST['new_password1'] == $_POST['new_password2']) {
				// New passwords match
				
				// Call update_password in user model
				User::update_password($_SESSION['user']['id'], $_POST['new_password1'], $salt);
				
				// Update session
				$_SESSION['user']['password'] = md5($_POST['new_password1'].$salt);
				
				// Log password update
				if (isset($this->plugins->log)) {
					$this->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'change_password');
				}
				
				$this->message = 'Password udpated!';
				
			} else {
				// New passwords don't match
				
				$this->message = 'There was a problem, please try again.';
				
			}
			
		} else {
			// Old passwords don't match
			
			$this->message = 'There was a problem, please try again.';
			
		}
		
	}
	
	//  Helper function: update profile
	private function update_profile() {

		$error = '';

		// Validate URL
        
		// Check for empty URL. Default value: http://
		if ($_POST['url'] == 'http://') {
			$_POST['url'] = NULL;
		}
        
		// Ensure URL begins with http://
		if ($_POST['url'] != NULL && (substr($_POST['url'], 0, 7) != 'http://' && substr($_POST['url'], 0, 8) != 'https://')) {
			$_POST['url'] = 'http://'.$_POST['url'];
		}
        
		// Check for spaces
		if (User::check_contains_spaces($_POST['url']) == TRUE) {
			$error = 'URL cannot contain spaces.';
		}
        
		// End URL validation
        
		if ($error == '') {
        
			// Update session vars
			$_SESSION['user']['full_name'] = $_POST['full_name'];
			$_SESSION['user']['bio'] = $_POST['bio'];
			$_SESSION['user']['url'] = $_POST['url'];
			
			// Call user_update_profile in user model
			User::update_profile($_SESSION['user']['id'], $_POST['full_name'], $_POST['bio'], $_POST['url']);
        
			// Set success message
			$this->message = 'Profile information updated!';
        
		} else {
        
			$this->message = $error;
        
		}
        
	}
	
}

?>