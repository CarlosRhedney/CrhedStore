<?php
use Psr\Http\Message\ResponseInterface AS Response;
use Psr\Http\Message\ServerRequestInterface AS Request;
use \Crhedstore\PageAdmin;
use \Crhedstore\Model\User;
use \Crhedstore\Model\Product;

$app->get('/admin/products', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$products = Product::listAll();

	$page = new PageAdmin();

	$page->setTpl("products", [
		"products"=>$products
	]);
	
});

$app->get('/admin/products/create', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("products-create");

});

$app->post('/admin/products/create', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$product = new Product();

	$product->setData($_POST);

	$product->save();

	$product->addPhoto($_FILES["file"]);

	header("Location: /admin/products");

	exit;
	
});

$app->get('/admin/products/{idproduct}', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$product = new Product();

	$product->get((int)$args["idproduct"]);

	$page = new PageAdmin();

	$page->setTpl("products-update", [
		"product"=>$product->getValues()
	]);

});

$app->post('/admin/products/{idproduct}', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$product = new Product();

	$product->get((int)$args["idproduct"]);

	$product->setData($_POST);

	$product->save();

	$product->addPhoto($_FILES["file"]);

	header("Location: /admin/products");

	exit;

});

$app->get('/admin/products/{idproduct}/delete', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$product = new Product();

	$product->get((int)$args["idproduct"]);

	$product->delete();

	header("Location: /admin/products");

	exit;
	
});

?>