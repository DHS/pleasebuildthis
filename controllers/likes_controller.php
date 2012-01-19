<?php

class LikesController extends Application {
	
	protected $requireLoggedIn = array('add', 'remove');

	function add($item_id) {
		
		$like_id = Like::add($_SESSION['user_id'], $item_id);
		
		if (isset($this->plugins->log)) {
			$this->plugins->log->add($_SESSION['user_id'], 'like', $like_id, 'add');
		}
		
		$this->show($item_id);
		
	}
	
	function remove($item_id) {
		
		$like = Like::get_by_user_item($_SESSION['user_id'], $item_id);
		
		$like->remove();
		
		if (isset($this->plugins->log)) {
			$this->plugins->log->add($_SESSION['user_id'], 'like', $like->id, 'remove');
		}
		
		$this->show($item_id);
		
	}
	
	private function show($item_id) {
		
		$this->item = Item::get_by_id($item_id);
		$item->content = process_content($item->content);
		foreach ($item->comments as $comment) {
			$comment->content = process_content($comment->content);
		}
		
		$this->loadPartial('likes');
		
	}
	
	function json($item_id) {
		
		$item = Item::get_by_id($item_id);
		$this->json = $item->likes;
		$this->loadView('pages/json', NULL, 'none');
		
	}
	
}

?>