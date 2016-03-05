<?php

class AdminController extends Application {

  protected $requireAdmin = array('index', 'signups', 'users', 'history',
    'invite', 'grant_invites');

  // Show admin dashboard
  function index() {

    $users = Admin::list_users();
    $users_beta = Admin::list_users_beta();

    $this->loadView('admin/index',
      array('users' => $users, 'users_beta' => $users_beta), 'admin');

  }

  // Show app config
  function config() {

    if ($_POST) {

      // Use the config lib to convert $_POST into something writable
      $conf = Config::prepareConfigToWrite($_POST);

      // Update config
      Config::writeConfig($conf);

      // Set flash message
      Application::flash('success', 'App config updated!');

      // Force redirect to reload app with new config
      header('Location: ' . $_SERVER['HTTP_REFERER']);
      exit();

    }

    $this->loadView('admin/config', null, 'admin');

  }

  // Show list of beta signups
  function signups() {

    $users = Admin::list_users_beta();

    $this->loadView('admin/signups', array('users' => $users), 'admin');

  }

  // Show list of users
  function users() {

    $users = Admin::list_users();

    $this->loadView('admin/users', array('users' => $users), 'admin');

  }

  // Show most recent entries in the log (not named log to avoid conflict with native PHP function)
  function history() {

    if (isset($this->plugins->log)) {

      // Copying the work of loadView
      $params = array(
        'app'     => $this,
        'session' => $_SESSION,
        'title'   => 'Admin'
      );

      echo $this->twig->render("partials/header.html", $params);
      echo $this->twig->render("partials/admin_menu.html", $params);
      echo $this->plugins->log->view();
      echo $this->twig->render("partials/footer.html", $params);

    }

  }

  // Setup your rat installation
  function setup() {

    if (Admin::count_users() == 0 && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
      // Do setup

      $user_id = User::add($_POST['email']);
      User::signup($user_id, $_POST['username'], $_POST['password'], $this->config->encryption_salt);

      $user = User::get_by_email($_POST['email']);

      // Update session
      $_SESSION['user_id'] = $user->id;

      // Log login
      if (isset($this->plugins->log)) {
        $this->plugins->log->add($_SESSION['user_id'], 'user', NULL, 'signup');
      }

      Application::flash('success', 'You are now logged in to your app!');

      // Go forth!
      header('Location: ' . $this->url_for('admin', 'config'));

      exit();

    } else {
      // Show setup form

      if (Admin::count_users() == 0) {
        Application::flash('info', 'Welcome to Rat!');
        $this->loadView('admin/setup');
      } else {
        throw new RoutingException($this->uri, "Page not found");
      }

    }

  }

  // Grant access to a beta signup
  function invite() {

    $user = User::get_by_id($_SESSION['user_id']);
    $email = $_POST['email'];

    if ($email != '') {

      // Add invite to database
      $id = Invite::add($_SESSION['user_id'], $email);

      // Log invite
      if (isset($this->plugins->log)) {
        $this->plugins->log->add($_SESSION['user_id'], 'invite', $id, 'admin_add', $email);
      }

      // Load template into $body variable
      $to      = array('email' => $email);
      $subject  = '[' . $this->config->name . '] Your ' . $this->config->name . ' invite is here!';
      $link    = $this->config->url . 'users/add/' . $id . '/?email=' . urlencode($email);
      $body    = $this->twig_string->render(file_get_contents("themes/{$this->config->theme}/emails/admin_invite.html"), array('link' => $link, 'app' => $this));

      // Email user
      $this->email->send_email($to, $subject, $body);

      Application::flash('success', 'User invited!');

    }

    $this->signups();

  }

  function grant_invites() {

    if ($this->uri['params']['count'] > 0) {

      Admin::update_invites($this->uri['params']['count']);

      Application::flash('success', 'Invites updated!');

    }

    $this->users();

  }

}
