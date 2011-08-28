<?php

class Routes {

	// Simple alias
	public $aliases = array(
	
		'/signup' =>array('controller' => 'users', 'action' => 'new'),
		'/*' => array('controller' => 'users', 'action' => 'show', 'params' => '$1'),
		'/*/item/*' => array('controller' => 'items', 'action' => 'show', 'params' => '$2')


	);
}

?>
