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

$app->get('/admin/users', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdmin();

	$page->setTpl("users", [
		"users"=>$users
	]);

});

$app->get('/admin/users/create', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");

});

$app->get('/admin/users/{iduser}/delete', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$args["iduser"]);

	$user->delete();

	header("Location: /admin/users");

	exit;

});

$app->get('/admin/users/{iduser}', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$args["iduser"]);

	$page = new PageAdmin();

	$page->setTpl("users-update", [
		"user"=>$user->getValues()
	]);

});

$app->post('/admin/users/create', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");

	exit;

});

$app->post('/admin/users/{iduser}', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$args["iduser"]);

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");

	exit;


});

$app->get('/admin/forgot', function(Request $request, Response $response, array $args){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");

});

$app->post('/admin/forgot', function(Request $request, Response $response, array $args){

	$user = User::getForgot($_POST["mail"]);

	header("Location: /admin/forgot/sent");

	exit;

});

$app->get('/admin/forgot/sent', function(Request $request, Response $response, array $args){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-sent");
	
});

$app->get('/admin/forgot/reset', function(Request $request, Response $response, array $args){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset", [
		"name"=>$user["person"],
		"code"=>$_GET["code"]
	]);

});

$app->post('/admin/forgot/reset', function(Request $request, Response $response, array $args){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost"=>12
	]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset-success");

});

$app->run();
?>