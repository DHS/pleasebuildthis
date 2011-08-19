<?php

class SearchController {
	
	function index($q = NULL) {
		
		global $app;
		
		$app->loadView('header');
		$app->loadView('search');
		if (isset($q))
			$this->show($q);
		$app->loadView('footer');
		
	}
	
	function show($q) {
		
		global $app;

		$app->page->items = $app->searches->do_search($q);
		$app->loadView('items_list');
		
	}
	
	function json($q) {
		
		global $app;
		
		$items['items'] = $app->searches->do_search($q);
		$app->page->json = $items;
		$app->loadView('pages/json');
		
	}

}

?>
