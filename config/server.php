<?php

class ServerConfig {

	// URLs - must include http:// and no trailing slash
	public $url     = 'http://pleasebuildthis.com';
	public $dev_url = 'http://127.0.0.1';

	// Base directory - the directory in which your site resides if not in the server root
	public $base_dir      = '/';
	public $dev_base_dir  = '/';

	// Email enabled - search project for "// Email user" to find what this affects
	public $send_emails = TRUE;

	// Encryption salt - change to a random six character string, do not change after first use of application
	public $encryption_salt;

	// Set timezone
	public $timezone = 'Europe/London';

	// Database
	public $database;

  public function __construct() {

    $this->encryption_salt = getenv('SALT');

    $this->database = array(
  	  'dev'	=> array(
  	    'host'      => 'localhost',
        'username'  => 'root',
        'password'	=> 'root',
        'database'	=> 'pleasebuildthis',
        'prefix'	=> ''
  		),
      'live'	=> array(
          'host'		=> getenv('MYSQL_DB_HOST'),
          'username'	=> getenv('MYSQL_USERNAME'),
          'password'	=> getenv('MYSQL_PASSWORD'),
          'database'	=> getenv('MYSQL_DB_NAME'),
          'prefix'	=> ''
      )
    );

  }

}