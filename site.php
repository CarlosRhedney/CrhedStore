<?php
use Psr\Http\Message\ResponseInterface AS Response;
use Psr\Http\Message\ServerRequestInterface AS Request;
use \Crhedstore\Page;
use \Crhedstore\Model\Category;

$app->get('/', function(Request $request, Response $response, array $args){

	$page = new Page();

	$page->setTpl("index");

});

$app->get('/categories/{idcategory}', function(Request $request, Response $response, array $args){

	$category = new Category();

	$category->get((int)$args["idcategory"]);

	$page = new Page();

	$page->setTpl("category", [
		"category"=>$category->getValues(),
		"products"=>[]
	]);

});

?>