<?php

class AppConfig extends ServerConfig {

  /*
  *  Contents
  *
  *   1. Basic app variables
  *   2. Beta
  *   3. Privacy
  *   4. Items
  *   5. Invites
  *   6. Friends
  *   7. Admin
  *   8. Themes
  *   9. Plugins
  *
  */

  // Basic app variables
  public $name                = 'PleaseBuildThis';
  public $tagline             = 'Share and vote on app ideas. If you\'re lucky somebody might build it for you!';
  public $default_controller  = 'items';

  // Beta - users can't signup, can only enter their email addresses
  public $beta = FALSE;

  // Private app - requires login to view pages (except public_pages)
  // no share buttons
  public $private = FALSE;
  public $signup_email_notifications = TRUE;

  // Items
  // Notes about uploads: max-size is in bytes (default: 5MB), directory
  // should contain three subdirectories: originals, thumbnails, stream
  public $items = array(
    'name'        => 'idea',
    'name_plural' => 'ideas',

    'titles' => array(
      'enabled'     => TRUE,
      'name'        => 'Name',
      'name_plural' => 'Names'
    ),

    'content' => array(
      'enabled'     => TRUE,
      'name'        => 'Description',
      'name_plural'  => 'Descriptions'
    ),

    // Remember to update the permissions for your
    // upload dir e.g. chmod -R 777 uploads
    'uploads'       => array(
      'enabled'     => TRUE,
      'name'        => 'Image',
      'directory'   => 'uploads',
      'max-size'    => '5242880',
      'mime-types'  => array(
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/pjpeg'
      )
    ),

    'comments' => array(
      'enabled'     => TRUE,
      'name'        => 'Leave feeback',
      'name_plural' => 'Feedback'
    ),

    'likes' => array(
      'enabled'       => TRUE,
      'name'          => 'Please build this!',
      'name_plural'   => 'Votes',
      'opposite_name' => 'Actually don\'t build this',
      'past_tense'    => 'People who want this built'
    )

  );

  // Locale
  public $timezone = 'Europe/London';

  // Invites system
  public $invites = array('enabled' => TRUE);

  // Friends - still testing, works with asymmetric set to true... just!
  // (Shows 'Follow' link & generates homepage feed)
  public $friends = array(
    'enabled'     => FALSE,
    'asymmetric'  => FALSE
  );

  // Admin users - array of user IDs who have access to admin area
  public $admin_users = array(1);

  // Theme
  public $theme = 'default';

  // Plugins
  public $plugins = array(
    'log'       => TRUE,
    'gravatar'  => TRUE,
    'points'    => FALSE,
    'analytics' => FALSE
  );

	// Send emails from what address?
	public $send_emails_from = 'davehs@gmail.com';

}
