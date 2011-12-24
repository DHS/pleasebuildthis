<?php

/*
*	A log plugin for Rat by @DHS
*
*	Installation
*	
*		Comes installed by default
*
*	Usage
*	
*		To log an event:
*		
*			if (isset($this->plugins->log)) {
*				$this->plugins->log->add($_SESSION['user_id'], 'user', NULL, 'signup');
*			}
*
*/

class log extends Application {

	function add($user_id, $object_type = NULL, $object_id = NULL, $action, $params = NULL) {
		// Add a new entry to the log
		
		$user_id = sanitize_input($user_id);	
		$object_type = sanitize_input($object_type);
		$object_id = sanitize_input($object_id);
		$action = sanitize_input($action);
		$params = sanitize_input($params);
		
		$query = mysql_query("INSERT INTO log SET user_id = $user_id, object_type = $object_type, object_id = $object_id, action = $action, params = $params");
		
	}
	
	function view() {
		// View the log
		
		$sql = "SELECT * FROM log ORDER BY id DESC LIMIT 10";
		$query = mysql_query($sql);
		
		$entries = array();
		while ($entry = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$entry['user'] = User::get_by_id($entry['user_id']);
			$entries[] = $entry;
		}
		
		if (is_array($entries)) {
			//echo '<pre>';
			//var_dump($entries);
			//echo '</pre>';
echo '<table class="common-table zebra-striped">
  <thead>
    <tr>
      <th>User</th>
      <th>Object</th>
      <th>Action</th>
      <th>Params</th>
      <th>Timestamp</th>
    </tr>
  </thead>
  <tbody>';
			foreach ($entries as $entry) {
				echo '<tr><td>';
				if ($entry['user']->username != NULL) {
					echo $this->link_to($entry['user']->username, 'users', 'show', $entry['user']->id);
				}
				echo '</td><td>';
				echo $entry['object_type'];
				echo '</td><td>';
				echo $entry['action'];
				echo '</td><td>';
				if ($entry['params'] != NULL) {
					echo $entry['params'];
				}
				echo '</td><td>'.$entry['date'].'</td></tr>';
			}
echo '</tbody></table>';
		
		}
	
	}

}

?>