<?php

class dbCall {

	public $db;

	public function __construct($db) {
		$this->db = $db;
	}
	public function deleteNews() {
		$query = $this->db->query('DELETE FROM news');
	}
	public function getLastNewsImportDate() {
		//GET THE LAST IMPORTED NEWS' DATE FROM THE 'news' TABLE, ORDER BY id DESC AND LIMIT 1 IN ORDER TO TAKE THE LAST ONE
		$query = $this->db->query('SELECT import_time FROM news ORDER BY id DESC LIMIT 1');
		$lastImportDate = $query->fetch();
		return $lastImportDate;
	}

	public function getNews() {
		$query = $this->db->query('SELECT * FROM news');
		$news  = $query->fetchAll();
		return $news;
	}

	public function sendData($articles) {
		for($i = 0; $i < count($articles['text']); $i++) {

			if($articles['img'][$i] == 'NULL') $article['img'][$i] = null;

			$prepare = $this->db->prepare('INSERT INTO news(title, date, text, url_img) VALUES(:title, :date, :text, :img)');
			
			$prepare->bindValue(':title', $articles['title'][$i]);
			$prepare->bindValue(':date',  $articles['date'][$i]);
			$prepare->bindValue(':text',  $articles['text'][$i]);
			$prepare->bindValue(':img',   $articles['img'][$i]);

			$prepare->execute();

			//RESET IDs
			$query = $this->db->query('SET  @num := 0; UPDATE news SET id = @num := (@num+1); ALTER TABLE news AUTO_INCREMENT =1;');

		}
	}

	public function deleteAlbums() {
		$query = $this->db->query('DELETE FROM albums');
	}
	
	public function getLastAlbumsImportDate() {
		$query 			= $this->db->query('SELECT import_date FROM albums ORDER BY id DESC LIMIT 1');
		$lastImportDate = $query->fetch();
		return $lastImportDate;
	}

	public function sendAlbums($albums_infos) {

		for($i = 0; $i < count($albums_infos['album_name']); $i++) {

			if($albums_infos['album_img'][$i] == 'NULL') $article['img'][$i] = null;

			$prepare = $this->db->prepare('INSERT INTO albums(name, img, url) VALUES(:name, :img, :url)');
			
			$prepare->bindValue(':name', $albums_infos['album_name'][$i]);
			$prepare->bindValue(':img',  $albums_infos['album_img'][$i]);
			$prepare->bindValue(':url',  $albums_infos['url'][$i]);

			$prepare->execute();

			//RESET IDs
			$query = $this->db->query('SET  @num := 0; UPDATE albums SET id = @num := (@num+1); ALTER TABLE albums AUTO_INCREMENT =1;');

		}

	}

	public function getAlbums() {
		$query  = $this->db->query('SELECT * FROM albums');
		$albums = $query->fetchAll();
		return $albums;
	}

	public function deleteMembersInfos() {
		$query = $this->db->query('DELETE FROM band_infos');
	}

	public function getLastMembersImportDate() {
		$query 			= $this->db->query('SELECT import_date FROM band_infos ORDER BY id DESC LIMIT 1');
		$lastImportDate = $query->fetch();
		return $lastImportDate;
	}

	public function getMembersInfos() {
		$query 		   = $this->db->query('SELECT * FROM band_infos');
		$members_infos = $query->fetchAll();
		return $members_infos;
	}

	public function sendMembersInfos($members_infos) {

		for($i = 0; $i < count($members_infos['names']); $i++) {
			$prepare = $this->db->prepare('INSERT INTO band_infos(member_name, infos, img_url) VALUES(:member_name, :infos, :url_img)');
			
			$prepare->bindValue(':member_name', $members_infos['names'][$i]);
			$prepare->bindValue(':infos',  $members_infos['infos'][$i]);
			$prepare->bindValue(':url_img',  $members_infos['img_url'][$i]);

			$prepare->execute();
		}

	}

	public function sendBiographyInfos($infos) {
		
		$prepare = $this->db->prepare('INSERT INTO band_biography(summary, more) VALUES(:summary, :more)');

		$prepare->bindValue(':summary', strip_tags($infos['summary']));
		$prepare->bindValue(':more',  strip_tags($infos['more']));

		$prepare->execute();
	}

	public function getBio() {
		$query = $this->db->query('SELECT * FROM band_biography');
		$bio = $query->fetch();
		return $bio;
	}

}