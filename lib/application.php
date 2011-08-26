<?php

class Application {
	
	public $uri, $config;
	
	private function __construct() {}
	
	public static function initialise($uri, $config) {
		
 		$controller = ucfirst($uri['controller']).'Controller';
		require_once "controllers/{$uri['controller']}_controller.php";
		$app = new $controller;
		
		$app->loadConfig($config);
		$app->loadModels();
		$app->loadPlugins();
		
		$app->uri = $uri;
		
		$app->route();
		
		unset($_SESSION['flash']);
	}
	
	private function loadConfig($config) {
		
		$this->config = $config;
		
		$domain = substr(substr($this->config->url, 0, -1), 7);
		
		if ($_SERVER['HTTP_HOST'] == $domain || $_SERVER['HTTP_HOST'] == 'www.'.$domain) {
			define('SITE_IDENTIFIER', 'live');
			$base_dir = $this->config->base_dir;
		} else {
			define('SITE_IDENTIFIER', 'dev');
			$base_dir = $this->config->dev_base_dir;
		}

		if (is_null($base_dir)) {
			$base_dir = '/';
		}
		
		define('BASE_DIR', $base_dir);
		
	}
	
	private function loadModels() {
		
    require_once 'lib/mysql.php';
		
		$handle = opendir('models');	
		while (false != ($file = readdir($handle))) {
			$model = substr($file, 0, -4);
			if ($file[0] != '.') {
				require_once "models/$model.php";
			}
		}
		
	}
	
	private function loadPlugins() {
		
		foreach ($this->config->plugins as $key => $value) {
			if ($value == TRUE) {
				require_once "plugins/$key.php";
				$this->plugins->$key = new $key;
			}
		}
		
	}
	
	private function route() {

		try {

			if (method_exists($this, $this->uri['action'])) {
				$this->{$this->uri['action']}($this->uri['id']);
			} elseif (empty($this->uri['action']) && method_exists($this, 'index')) {
				$this->index($this->uri['id']);
			} else {
				// Load 404
				$uri = array(	'controller'	=> 'pages',
								'action'		=> 'show',
								'id'			=> '404'
							);
				$this->initialise($uri, $this->config);
			}
		
		} catch (Exception $e) {
		
			$this->flash('error', $e->getMessage());
			header('Location: ' . $_SERVER['HTTP_REFERER']);

		}
	}
	
	protected function loadView($view, $layout = NULL) {
		
		if (is_null($layout)) {
			$layout = 'default';
		}
		
		include "themes/{$this->config->theme}/layouts/{$layout}.php";
		
	}
	
	protected function loadPartial($partial) {
		
		include "themes/{$this->config->theme}/partials/{$partial}.php";
		
	}
	
	public function url_for($controller, $action = "", $id = "") {
	
		$url = BASE_DIR . "/{$controller}";
		
		if (!empty($action)) {
			$url .= "/$action";
		}
		
		if (!empty($id)) {
			$url .= "/$id";
		}

		return $url;

	}

	public function link_to($link_text, $controller, $action = "", $id = "") {
		
		return '<a href="'.$this->url_for($controller, $action, $id).'">'.$link_text.'</a>';
					
	}

	public function redirect_to($controller, $action = "", $id = "") {
	
		header('Location: ' . $this->url_for($controller, $action, $id));
			
	}

	public function flash($category, $message) {
	
		if (! in_array($category, array('error', 'notice', 'success'))) {
			$category = 'success';
		}

		$_SESSION['flash'] = array('category' => $category, 'message' => $message);
	
	}
	
}

?>
