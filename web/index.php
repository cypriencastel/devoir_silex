<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../models/data.class.php';
require_once __DIR__.'/../models/dataCall.class.php';
require_once __DIR__.'/../models/apiCall.class.php';

$app = new Silex\Application();

switch ($_SERVER['HTTP_HOST']) {
	case 'localhost':
	$app['debug'] = true;
	break;
	
	default:
	$app['debug'] = false;
	break;
}

/*
	*SERVICES
*/

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__.'/../views',
	));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
	'db.options' => array (
		'driver'    => 'pdo_mysql',
		'host'      => 'localhost',
		'dbname'    => 'exercice_silex',
		'user'      => 'root',
		'password'  => 'root',
		'charset'   => 'utf8'
		),
	));

$app['db']->setFetchMode(PDO::FETCH_OBJ);

/*
	* SET CLASSES
*/

$dataCall  = new dataCall($app['db']);
$dbCall    = new dbCall($app['db']);
$apiCall   = new apiCall($app['db']);

/*
	*BEFORES
*/

$news_before = function() use($app, $dataCall, $dbCall) {

	// GET THE DATE OF THE LAST IMPORTED NEWS
	$lastImportDate = $dbCall->getLastNewsImportDate();
	// GET TODAY'S DATE AND DO THE CALCULATION TO COMPARE LATER
	$date_diff = strtotime(date("Y-m-d h:i:s")) - strtotime($lastImportDate->import_time);
	// IF THE LAST IMPORT DATE > TWO DAYS
	if($date_diff > 172800) {	// 172 800 == NUMBER OF SECONDS DURING TWO DAYS
		$dbCall->deleteNews();
		$articles_infos = $dataCall->callData(); // GET LATEST NEWS
		$dbCall->sendData($articles_infos); // SEND THEM TO THE DB
	}

};

$albums_before = function() use($app, $dbCall, $apiCall) {
	
	$lastImportDate = $dbCall->getLastAlbumsImportDate();
	$date_diff = strtotime(date("Y-m-d h:i:s")) - strtotime($lastImportDate->import_date);
	
	if($date_diff > 8035200) { //NUMBER OF SECONDS IN THREE MONTHS -> UPDATE EVERY MONTH
		$dbCall->deleteAlbums();
		$albums_infos = $apiCall->refreshAlbums();
		$dbCall->sendAlbums($albums_infos);

	}
};

$members_before = function() use($app, $apiCall, $dbCall) {

	$lastImportDate = $dbCall->getLastMembersImportDate();
	$date_diff = strtotime(date("Y-m-d h:i:s")) - strtotime($lastImportDate->import_date);

	if($date_diff > 31190400) { //NUMBER OF SECONDS EVERY YEAR -> UPDATE EVERY YEAR
		$dbCall->deleteMembersInfos();
		$members_infos = $apiCall->membersInfosCall();
		$dbCall->sendMembersInfos($members_infos);
	}

};


/*
	* ROUTES
*/

	//HOME

$app->get('/', function() use($app, $dataCall, $dbCall) {

	$news = $dbCall->getNews();

	$data = array(
		'news' 		   => $news,
		'js_file_name' => 'home',
	);

	return $app['twig']->render('pages/home.twig',$data);
})
->bind('home')
->before($news_before);

	//ALBUMS

$app->get('/albums', function() use($app, $dbCall, $apiCall) {

	$getAlbums = $dbCall->getAlbums();

	$data = array(
		'albums' => $getAlbums,
		'js_file_name' => 'albums',
	);

	return $app['twig']->render('pages/albums.twig',$data);
})
->before($albums_before)
->bind('albums');

	//BAND MEMBERS

$app->get('/band_members', function() use($app, $dbCall) {

	$members_infos = $dbCall->getMembersInfos();

	$data = array(
		'members' => $members_infos,
		'js_file_name' => 'band_members',
	);

	return $app['twig']->render('pages/band_members.twig',$data);
})
->before($members_before)
->bind('band_members');

	//BIOGRAPHY

$app->get('/biography', function() use($app, $dbCall, $apiCall) {

	$bio = $dbCall->getBio();

	$data = array(
		'js_file_name' => 'biography',
		'bio' => $bio,
	);

	return $app['twig']->render('pages/biography.twig',$data);
})
->bind('biography');

// 404

$app->error(function(\Exception $e, $code) use($app) {

	$data = array(

	);
    return $app['twig']->render('pages/error.twig',$data);
});

$app->run();
