
<div class="row">
  <div class="span8 columns offset3">

	<?php
	// List all items for this user

	if (count($this->items) > 0) {

		if (is_array($this->items)) {

			foreach ($this->items as $this->item) {
				$this->loadPartial('item');
			}
		
			$this->loadPartial('pagination');
		
		}


	} else {

		// If own page and no post_permission OR someone else's page show 'no articles yet'
		if (($_SESSION['user']['id'] == $this->user->id && $_SESSION['user']['post_permission'] == 0) || $_SESSION['user']['id'] != $this->user->id) {
			echo '<p>'.$this->user->username.' hasn\'t published any '.$this->config->items['name_plural'].' yet.</p>';
		}

	}

	?>

  </div>
  <div class="span4 columns">

<?php if ($this->user->full_name != NULL || $this->user->bio != NULL || $this->user->url != NULL) { // Only show profile if there is some data ?>

<h3>Profile</h3>

<?php if ($this->user->full_name != NULL) { ?>
      <strong>Name</strong> <?php echo $this->user->full_name; ?>
<?php } if ($this->user->bio != NULL) { ?>
      <br /><strong>Bio</strong> <?php echo $this->user->bio; ?>
<?php } if ($this->user->url != NULL) { ?>
      <br /><strong>URL</strong> <a href="<?php echo $this->user->url; ?>" target="_new"><?php echo $this->user->url; ?></a>
<?php } ?>

<p />

<?php
}

// Show follow button

if ($this->config->friends['enabled'] == TRUE) {
	$this->loadView('friends/index');
}

// Show number of points

if (isset($this->plugins->points)) {
	$this->plugins->points->view();
}

// Show new item form

//if ($_SESSION['user']['post_permission'] == 1) {
//	$this->loadView('items/add');
//}

?>

  </div>
</div>
