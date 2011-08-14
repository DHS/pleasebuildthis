<?php

require_once 'config/init.php';

$segment1 = $_GET['segment1'];
$segment2 = $_GET['segment2'];
$segment3 = $_GET['segment3'];
$segment4 = $_GET['segment4'];
$segment5 = $_GET['segment5'];
$segment6 = $_GET['segment6'];

// If user is logged out, app is private and page is not in public_pages then show splash page
if ($_SESSION['user'] == NULL && $app->config->private == TRUE && in_array($segment1, $app->config->public_pages) == FALSE) {

	if (count($app->admin->list_users()) == 0 && $result[0] == 'admin.php') {

		// Make an exception for setup
		
		// So at the moment, setup requires $app->config->private to be TRUE
		// and admin.php must NOT be in public_pages
		
	} else {

		// Show splash page
		$app->loadView('header');
		$app->loadView('splash');
		$app->loadView('footer');

		// Stop processing the rest of the page
		exit();		
		
	}

}

if ($segment1 == '' && $segment2 == '' && $segment3 == '' && $segment4 == '' && $segment5 == '' && $segment6 == '') {
	
	include "controllers/index.php";
	exit();
	
} elseif (substr($segment1, -5) == '.json') {
	
	if (substr($segment1, 0, 6) == 'search') {
		echo 'Search results json for: "'.urlencode($segment2).'"';
	} else {
		echo 'User json for: "'.substr($segment1, 0, -5).'"';
	}

	exit();
	
} elseif (file_exists("controllers/$segment1.php") == TRUE) {
	// Controller exists, call with function and params if available
	
	include "controllers/$segment1.php";
	exit();
	
	//echo "Call $segment2($segment3) in $segment1.php";

} elseif (is_numeric($segment2) == TRUE) {
	// Paginated user view
	
	echo "Username: $segment1, page: $segment2";
	
} elseif ($segment2 == $app->config->items['name']) {
	
	if ($segment3 == '' || $segment3 == 'add') {
		// No third part so show new item form
		
		include 'controllers/item.php';
		exit();
		
	} elseif (substr($segment3, -5) == '.json') {
		// Item json
		
		echo 'JSON for #'.substr($segment3, 0, -5);
		
	} elseif ($segment4 == 'update') {
		// Update item
		
		echo "Update #$segment3";
		
	} elseif ($segment4 == 'remove') {
		// Remove item
		
		echo "Remove #$segment3";
		
	} elseif ($segment4 == 'likes') {
		// Likes
	
		echo "Likes for #$segment3";
	
	} elseif ($segment4 == 'likes.json') {
		// Likes json
		
		echo "Likes JSON for #$segment3";

	} elseif ($segment4 == 'like') {
		// Likes json
		
		if ($segment5 == 'add') {
			
			echo "Add like to #$segment3";
			
		} elseif($segment5 == 'remove') {
			
			echo "Remove like from #$segment3";
			
		} else {
			
			echo "Error w/likes #$segment3";
			
		}

	} elseif ($segment4 == 'comments') {
	// Comments

		echo "Comments for #$segment3";

	} elseif ($segment4 == 'comments.json') {
		// Comments json
   
		echo "Comments JSON for #$segment3";
   
	} elseif ($segment4 == 'comment') {
   
		if ($segment5 == 'add') {
   
			echo "Add comment to #$segment3";
   
		} elseif($segment5 == 'remove') {
   
			echo "Remove comment from #$segment3";
   
		} else {
   
			echo "Error w/comments #$segment3";
   
		}

	} else {
		
		$_GET['id'] = $segment3;
		include "controllers/item.php";
		exit();
				
	}
		
} elseif ($segment1 != '') {
	// User profile

	$user = $app->user->get_by_username($segment1);
	
	if ($user == NULL) {
		
		echo '404 - Page not found';
		
	} else {
		
		$_GET['id'] = $user['id'];
		include "controllers/user.php";
		exit();
		
	}

} else {
	
	echo '404 - Page not found';
	
}

?>