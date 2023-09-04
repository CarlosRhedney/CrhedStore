<?php
use Psr\Http\Message\ResponseInterface AS Response;
use Psr\Http\Message\ServerRequestInterface AS Request;
use \Crhedstore\Page;
use \Crhedstore\Model\Category;
use \Crhedstore\Model\Product;

$app->get('/', function(Request $request, Response $response, array $args){

	$products = Product::listAll();

	$page = new Page();

	$page->setTpl("index", [
		"products"=>Product::checkList($products)
	]);

});

$app->get('/categories/{idcategory}', function(Request $request, Response $response, array $args){

	$pag = (isset($_GET["page"])) ? (int)$_GET["page"] : 1;

	$category = new Category();

	$category->get((int)$args["idcategory"]);

	$pagination = $category->getProductsPage($pag);

	$pages = [];

	for($i = 1; $i <= $pagination["pages"]; $i++){

		array_push($pages, [
			"link"=>"/categories/" . $category->getidcategory() . "?page=" . $i,
			"page"=>$i
		]);
	}

	$page = new Page();

	$page->setTpl("category", [
		"category"=>$category->getValues(),
		"products"=>$pagination["data"],
		"pages"=>$pages
	]);

});

$app->get('/products/{url}', function(Request $request, Response $response, array $args){

	$product = new Product();

	$product->getFromUrl($args["url"]);

	$page = new Page();

	$page->setTpl("product-detail", [
		"product"=>$product->getValues(),
		"categories"=>$product->getCategories(),
		"photos"=>$product->getPhotos()
	]);

});

?>