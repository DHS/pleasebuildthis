<?php

if (is_array($items)) {
	
echo '<table style="width: 100%;">';

foreach ($items as $item) { ?>

	<tr>
		<?php 
		if (isset($GLOBALS['gravatar'])) {
			echo '<td style="text-align: center;" valign="top">';
			echo $GLOBALS['gravatar']->show($item['user']['email'], array('user_id' => $item['user']['id'], 'size' => 48, 'style' => "margin-right: 5px;"));
			echo '</td>';
		}
		?>
		<td style="padding-bottom: 10px;">
		
			<?php if ($app->config->items['titles']['enabled'] == TRUE) { ?>
				
				<p><a href="item.php?id=<?php echo $item['id']; ?>"><?php echo $item['title']; ?></a> by <a href="user.php?id=<?php echo $item['user']['id']; ?>"><?php echo $item['user']['username']; ?></a></p>
				<?php if ($app->config->items['uploads']['enabled'] == TRUE && $item['image'] != NULL) { ?>
					<a href="item.php?id=<?php echo $item['id']; ?>"><img src="<?php echo $app->config->items['uploads']['directory']; ?>/stream/<?php echo $item['image']; ?>" /></a>
				<?php } ?>
				<p><?php echo $item['content']; ?></p>

			<?php } else { ?>
				
				<p><a href="user.php?id=<?php echo $item['user']['id']; ?>"><?php echo $item['user']['username']; ?></a> <?php echo $item['content']; ?></p>
			
			<?php } ?>

			<?php include 'items_meta.php'; ?>

			<?php
			
			if ($app->config->items['likes']['enabled'] == TRUE)
				include 'themes/'.$app->config->theme.'/likes_list.php';
			
			if ($app->config->items['comments']['enabled'] == TRUE) {

				include 'themes/'.$app->config->theme.'/comments_list.php';
				if (count($item['comments']) > 0) {
					$show_comment_form = TRUE;
				} else {
					$show_comment_form = FALSE;
				}
				include 'themes/'.$app->config->theme.'/comments_add.php';

			}

			?>
			
		</td>
	</tr>
	
	<tr>
		<?php 
		if (isset($GLOBALS['gravatar'])) {
			echo '<td colspan="2" style="border-top: 1px solid #CCCCCC; height: 10px;"></td>';
		} else {
			echo '<td colspan="2" style="border-top: 1px solid #CCCCCC; height: 10px;"></td>';
		}
		?>
	</tr>
	
<?php

}
// end foreach loop

echo '</table>';

}
// end if is_array

?>