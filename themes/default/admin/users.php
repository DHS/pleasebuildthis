
<p>Users: <strong><?php echo count($page['users']); ?></strong></p>

<?php $this->loadView('admin/grant_invites'); ?>

<?php if (is_array($page['users'])) { ?>

<table>
<tr><td></td><td>Credits</td><td>Last login</td></tr>

<?php foreach ($page['users'] as $user) { ?>
	
	<tr><td><a href="/<?php echo $user['username']; ?>"><?php echo $user['username']; ?></a></td><td><?php echo $user['points']; ?></td><td><?php echo $user['last_login']; ?></td></tr>

<?php } ?>

</table>

<?php } ?>