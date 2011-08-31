<?php

class SessionsController extends Application {
	
	function add() {
		
		if (isset($_POST['email']) && isset($_POST['password'])) {
			
			$user = User::get_by_email($_POST['email']);
			
			if ($user->authenticate($_POST['password'], $this->config->encryption_salt) == TRUE) {
				
				// Get redirected
				if (isset($_GET['redirect_to'])) {
					header('Location: '.$_GET['redirect_to']);
					exit();
				}
				
				// Go forth
				if (SITE_IDENTIFIER == 'live') {
					header('Location: '.$this->config->url);
				} else {
					header('Location: '.$this->config->dev_url);
				}
				
				exit();
				
			} else {
				
				Application::flash('error', 'Something isn\'t quite right. Please try again...');
				$email = $_POST['email'];
				
			}
			
		}
		
		if (!isset($_SESSION['user_id'])) {
			
			$this->title = 'Login';
			$this->loadView('sessions/add');
			
		} else {
			
			Application::flash('error', 'You are already logged in! '.$this->link_to('Click here', 'sessions', 'remove').' to logout.');
			$this->loadView();
			
		}
		
	}
	
	function remove() {
		
		$user = User::get_by_id($_SESSION['user_id']);
		
		if ($user->deauthenticate() == TRUE) {
			
			Application::flash('info', 'You are now logged out.');
			Application::redirect_to($this->config->default_controller);
			
		} else {
			
			Application::flash('info', 'Nothing to see here.');
			$this->loadView();
			
		}
		
	}
	
}

?>