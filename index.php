<?php
session_start();

require_once("vendor/autoload.php");

use Psr\Http\Message\ResponseInterface AS Response;
use Psr\Http\Message\ServerRequestInterface AS Request;

use \Slim\App;
use \Crhedstore\Page;
use \Crhedstore\PageAdmin;
use \Crhedstore\Model\User;

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

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");
	
});

$app->get('/admin/login', function(Request $request, Response $response, array $args){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login", [
		"errorLogin"=>User::getErrorLogin()
	]);

});

$app->post('/admin/login', function(Request $request, Response $response, array $args){

	try{

		User::login($_POST["login"], $_POST["password"]);

		header("Location: /admin");

		exit;

	}catch(Exception $e){

		User::setErrorLogin($e->getMessage());

		header("Location: /admin/login");

		exit;

	}

});

$app->get('/admin/logout', function(Request $request, Response $response, array $args){

	User::logout();

	header("Location: /admin/login");

	exit;

});

$app->run();
?>