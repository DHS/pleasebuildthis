
<div class="row">
  <div class="span8 columns offset4">

<?php if (isset($this->invites_remaining) && $this->invites_remaining == 1) { ?>
	
	<p>You have one invite remaining.</p>
	
	<?php $this->loadView('invites/add'); ?>
	
<?php } elseif (isset($this->invites_remaining) && $this->invites_remaining > 1) { ?>

	<p>You have <?php echo $this->invites_remaining; ?> invites remaining.</p>
	
	<?php $this->loadView('invites/add'); ?>
	
<?php } else { ?>
	
	<p>You have no remaining invites.</p>
	
<?php } ?>

<p>&nbsp;</p>

<?php if (!empty($this->invites)) { ?>

	<h2>Sent invites</h2>
	
	<?php foreach ($this->invites as $key => $value) { ?>
		<?php echo htmlentities($value->email); ?>
		<?php if ($value->result >= 1) { ?>
			&middot; <span class="good_news">Accepted</span>
		<?php } else { ?>
			&middot; Still waiting
		<?php } ?>
		<br />
	<?php } ?>

<?php } ?>

  </div>
</div>

<p>&nbsp;</p>
