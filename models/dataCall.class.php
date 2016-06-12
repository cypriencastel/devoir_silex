<?php

class dataCall {
	public $db;

	public function __construct($db) {
		$this->db = $db;
	}

	public function callData() {
		require_once 'simple_html_dom.php';
		$html = new simple_html_dom();
		$html->load_file('http://nightwish.com/en/news');
		$data = array(
			'title' => array(),
			'date' => array(),
			'text' => array(),
			'img' => array(),
		);

		$articles = $html->find('article');

		for($i = 0; $i < count($articles); $i++) {
			foreach($articles[$i]->find('h2') as $_title) {
				
				$title = str_get_html($_title->innertext);
				$title_value = $title->plaintext;
				array_push($data['title'], $title_value);
			}

			foreach($articles[$i]->find('time') as $_time) {
				
				$time = str_get_html($_time->innertext);
				$time_value = $time->plaintext;
				array_push($data['date'], $time_value);
			}
			foreach($articles[$i]->find('p') as $_text) {
				
				$text = str_get_html($_text->innertext);
				$text_value = $text->plaintext;
				array_push($data['text'], $text_value);
			}
			
			if(!empty($articles[$i]->find('img'))) {
				$url_img = $articles[$i]->find('img', 0)->getAttribute('src');
				array_push($data['img'], 'http://nightwish.com'.$url_img);
		
			} else {
				array_push($data['img'], 'NULL');
			}

		}

		return $data;
	}
}