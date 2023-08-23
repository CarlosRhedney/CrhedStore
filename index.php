<?php
require_once("vendor/autoload.php");

use Psr\Http\Message\ResponseInterface AS Response;
use Psr\Http\Message\ServerRequestInterface AS Request;

use \Slim\App;
use \Crhedstore\Page;
use \Crhedstore\PageAdmin;

$config = array(
	"settings"=>[
		"displayErrorDetails"=>true
	]
);

$app = new App($config);

$app->get('/', function(Request $request, Response $response, array $args){

	$page = new Page();

	$page->setTpl("index");

});

$app->get('/admin', function(Request $request, Response $response, array $args){

	$page = new PageAdmin();

	$page->setTpl("index");
	
});

$app->run();
?>