<?php

$subject = "[$app->config->name] {$user['username']} is now following you on $app->config->name!";

$body = '<p>Hi '.$friend['username'].',</p>
<p>Just to let you know that you have a new follower on '.$app->config->name.':</p>
<p>'.$link.'</p>
<p>You should publish another '.$app->config->items['name'].' to celebrate!</p>
<p>Best regards,</p>
<p>David Haywood Smith, creator of '.$app->config->name.'</p>';

?>