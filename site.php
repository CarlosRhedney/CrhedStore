<?php
use Psr\Http\Message\ResponseInterface AS Response;
use Psr\Http\Message\ServerRequestInterface AS Request;
use \Crhedstore\Page;
use \Crhedstore\Model\Category;
use \Crhedstore\Model\Product;
use \Crhedstore\Model\Cart;

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

$app->get('/cart', function(Request $request, Response $response, array $args){

	$cart = Cart::getFromSession();

	$page = new Page();

	$page->setTpl("cart", [
		"cart"=>$cart->getValues(),
		"products"=>$cart->getProducts(),
		"error"=>Cart::getMsgError()
	]);
	
});

$app->get('/cart/{idproduct}/add', function(Request $request, Response $response, array $args){

	$product = new Product();

	$product->get((int)$args["idproduct"]);

	$cart = Cart::getFromSession();

	$qtd = (isset($_GET["qtd"])) ? (int)$_GET["qtd"] : 1;

	for($i = 0; $i < $qtd; $i++){

		$cart->addProduct($product);

	}

	header("Location: /cart");

	exit;

});

$app->get('/cart/{idproduct}/minus', function(Request $request, Response $response, array $args){

	$product = new Product();

	$product->get((int)$args["idproduct"]);

	$cart = Cart::getFromSession();

	$cart->removeProduct($product);

	header("Location: /cart");

	exit;

});

$app->get('/cart/{idproduct}/remove', function(Request $request, Response $response, array $args){

	$product = new Product();

	$product->get((int)$args["idproduct"]);

	$cart = Cart::getFromSession();

	$cart->removeProduct($product, true);

	header("Location: /cart");

	exit;

});

$app->post('/cart/freight', function(Request $request, Response $response, array $args){

	$cart = Cart::getFromSession();

	$cart->addFreight($_POST["zipcode"]);

	header("Location: /cart");

	exit;

});

?>