<?php

class SearchController extends Application {
	
	protected $requireLoggedIn = array('index', 'show');

	function index() {
		
		if (isset($this->uri->params['q'])) {
			$this->show($this->uri->params['q']);
		} else {
			$this->add();
		}
		
	}
	
	function add() {
		
		$this->loadView('search/add');
		
	}
	
	function show($q) {
		
		include 'lib/search.php';
		$search = new Search;
		
		$this->items = $search->do_search($q);
		
		if (isset($this->plugins->log)) {
			$result_count = count($this->items);
			$this->plugins->log->add($_SESSION['user_id'], 'search', NULL, 'new', "Term = $q\nResult_count = $result_count");
		}
		
		$this->loadView('search/index');
		
	}
	
	function json($q) {
		
		include 'lib/search.php';
		$search = new Search;
		
		$items['items'] = $search->do_search($q);
		$this->json = $items;
		$this->loadView('pages/json', 'none');
		
	}

}

?>
