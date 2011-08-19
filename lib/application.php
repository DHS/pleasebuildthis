<?php

// Define app class
class application {

	public $uri, $config;

	function __construct($uri = NULL) {
		
		$this->uri = $uri;
		
		$this->loadConfig();
		$this->loadModels();
		$this->loadPlugins();
		
	}

	function loadConfig() {
		
		require_once 'config/config.php';
		$this->config = new config;
	
		$domain = substr(substr($this->config->url, 0, -1), 7);

		if ($_SERVER['HTTP_HOST'] == $domain || $_SERVER['HTTP_HOST'] == 'www.'.$domain) {
			define('SITE_IDENTIFIER', 'live');
			define('BASE_DIR', $this->config->base_dir);
		} else {
			define('SITE_IDENTIFIER', 'dev');
			define('BASE_DIR', $this->config->dev_base_dir);
		}
		
	}

	function loadModels() {
	
		$handle = opendir('models');	
		while (false != ($file = readdir($handle))) {
			$model = substr($file, 0, -4);
			if ($file[0] != '.') {
				require_once "models/$model.php";
				$this->$model = new $model;
			}
		}

	}

	function loadPlugins() {
		
		foreach ($this->config->plugins as $key => $value) {
			if ($value == TRUE) {
				require_once "plugins/$key.php";
				$this->plugins->$key = new $key;
			}
		}

	}

	function loadController($c) {
 
		global $app;
		
		require_once "controllers/{$c}.php";

		$classname = ucfirst($c).'Controller';

		$controller = new $classname;
		
		if (method_exists($controller, $this->uri['action'])) {
			$controller->{$this->uri['action']}($this->uri['id']);
		} else {
			$controller->index();
		}
		
	}

	function loadView($view) {
		
		global $app;
		
		include "themes/{$this->config->theme}/{$view}.php";
		
	}
	
	function loadLayout($view, $layout = NULL) {
		
		global $app;
		
		if (is_null($layout))
			$layout = 'default';
		
		include "themes/{$this->config->theme}/layouts/{$layout}.php";
		
	}
	
	function loadPartial($partial) {
		
		global $app;	
		
		include "themes/{$this->config->theme}/partials/{$partial}.php";
		
	}

	public function isPublic() {
		
		return TRUE;
		
	}

}

?>