<?php
use Psr\Http\Message\ResponseInterface AS Response;
use Psr\Http\Message\ServerRequestInterface AS Request;
use \Crhedstore\PageAdmin;
use \Crhedstore\Model\User;
use \Crhedstore\Model\Category;

$app->get('/admin/categories', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$categories = Category::listAll();

	$page = new PageAdmin();

	$page->setTpl("categories", [
		"categories"=>$categories
	]);

});

$app->get('/admin/categories/create', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create");

});

$app->post('/admin/categories/create', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");

	exit;

});

$app->get('/admin/categories/{idcategory}/delete', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$args["idcategory"]);

	$category->delete();

	header("Location: /admin/categories");

	exit;

});

$app->get('/admin/categories/{idcategory}', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$args["idcategory"]);

	$page = new PageAdmin();

	$page->setTpl("categories-update", [
		"category"=>$category->getValues()
	]);

});

$app->post('/admin/categories/{idcategory}', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$args["idcategory"]);

	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");

	exit;

});

?>