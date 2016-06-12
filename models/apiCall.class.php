<?php

class apiCall {
	public $db;

	public function __construct($db) {
		$this->db = $db;
	}

	public function refreshAlbums() {
		$endpoint = 'http://ws.audioscrobbler.com/2.0/?method=artist.gettopalbums&artist=nightwish&api_key=7e61a847a55e42f97f26b6258b65cb14&format=json';

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = json_decode(curl_exec($ch));
		$albums = array();
		$albums_infos = array(
			'album_name' => array(),
			'album_img'  => array(),
			'url' 		 => array(),
			);

		foreach ($result->topalbums as $_album) {
			$albums[] = $_album;
		}

		for($i = 0; $i < count($albums); $i++) {
			
			for($j = 0; $j < count($albums[$i]) - 1; $j++) {
				$albums_infos['album_name'][] = $albums[$i][$j]->name;
				$albums_infos['album_img'][] = $albums[$i][$j]->image[3]->{'#text'};
				$albums_infos['url'][] = $albums[$i][$j]->url;
			}
		}
		return $albums_infos;
	}

	public function membersInfosCall() {
		$return = array(
			'names'   => array(),
			'infos'   => array(),
			'img_url' => array(),
		);
		$members = array(
			'floor%20jansen',
			'tuomas%20holopainen',
			'marco%20hietala',
			'Emppu%20Vuorinen',
			'Troy%20Donockley',
			'Kai%20Hahto',
			'Jukka%20Nevalainen',
		);

		$first_url_string = 'http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=';
		$last_url_string  = '&api_key=7e61a847a55e42f97f26b6258b65cb14&format=json';


		foreach ($members as $_member) {
			
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $first_url_string.$_member.$last_url_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$result = json_decode(curl_exec($ch));

			array_push($return['names'], $result->artist->name);
			array_push($return['infos'], strip_tags($result->artist->bio->summary));
			array_push($return['img_url'], $result->artist->image[3]->{'#text'});

		}
		return $return;
	}

	public function getBiography() {
		$endpoint = 'http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=nightwish&api_key=7e61a847a55e42f97f26b6258b65cb14&format=json';
		$infos = array(
			'summary' => null,
			'more' 	  => null,

		);
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$result = json_decode(curl_exec($ch));

		$infos['summary'] = $result->artist->bio->summary;
		$infos['more'] = $result->artist->bio->content;
		return $infos;

	}
}