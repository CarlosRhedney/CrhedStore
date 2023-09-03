<?php
use Psr\Http\Message\ResponseInterface AS Response;
use Psr\Http\Message\ServerRequestInterface AS Request;
use \Crhedstore\PageAdmin;
use \Crhedstore\Model\User;

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

?>