<?php
use Psr\Http\Message\ResponseInterface AS Response;
use Psr\Http\Message\ServerRequestInterface AS Request;
use \Crhedstore\PageAdmin;
use \Crhedstore\Model\User;

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

?>