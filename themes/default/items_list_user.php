<?php

if (is_array($items)) {
	
echo '<table class="small" width="100%">';

foreach ($items as $item) {

?>

	<tr>
		<td>
		
			<p><?php echo $item['content']; ?></p>
			<p style="font-size: 50%; line-height: 50%; color: gray;">

			<p>
			<?php include 'items_meta.php'; ?>
			</p>

			<?php

			if ($app->items['likes'] == TRUE)
				include 'themes/'.$app->theme.'/likes_list.php';
			
			if ($app->items['comments'] == TRUE) {

				include 'themes/'.$app->theme.'/comments_list.php';
				if (count($item['comments']) > 0) {
					$comments_add_show = TRUE;
				} else {
					$comments_add_show = FALSE;
				}
				include 'themes/'.$app->theme.'/comments_add.php';
				
			}

			?>
			
		</td>
	</tr>
	
	<tr>
		<td style="border-top: 1px solid #CCCCCC; height: 10px;"></td>
	</tr>
	
<?php

}
// end foreach loop

echo '</table>';

}
// end if is_array

?>