<?php

class CommentsController extends Application {
	
	function add() {
		
		$comment_id = Comment::add($_SESSION['user']['id'], $_POST['item_id'], $_POST['content']);
		
		if (isset($this->plugins->log))
			$this->plugins->log->add($_SESSION['user']['id'], 'comment', $comment_id, 'add', $_POST['content']);
		
		$this->show($_POST['item_id']);
		
	}
	
	function remove($comment_id) {
		
		$comment = Comment::get($comment_id);
		
		Comment::remove($_SESSION['user']['id'], $comment_id);
		
		if (isset($this->plugins->log))
			$this->plugins->log->add($_SESSION['user']['id'], 'comment', $comment_id, 'remove');
		
		$this->show($comment['item_id']);
		
	}
	
	function show($item_id) {
		
		$this->page['item'] = Item::get($item_id);
		$this->loadView('comments/index');
		
	}
	
	function json($item_id) {
		
		$item = Item::get($item_id);
		$this->page['json'] = $item['comments'];		
		$this->loadView('pages/json');
		
	}
	
}

?>