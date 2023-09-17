<?php
use Psr\Http\Message\ResponseInterface AS Response;
use Psr\Http\Message\ServerRequestInterface AS Request;
use \Crhedstore\Page;
use \Crhedstore\Model\Category;
use \Crhedstore\Model\Product;
use \Crhedstore\Model\Cart;
use \Crhedstore\Model\User;
use \Crhedstore\Model\Address;

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

$app->get('/checkout', function(Request $request, Response $response, array $args){

	User::verifyLogin(false);

	$cart = Cart::getFromSession();

	$address = new Address();

	$page = new Page();

	$page->setTpl("checkout", [
		"cart"=>$cart->getValues(),
		"address"=>$address->getValues()
	]);

});

$app->get('/login', function(Request $request, Response $response, array $args){

	$page = new Page();

	$page->setTpl("login", [
		"errorLogin"=>User::getErrorLogin(),
		"errorRegister"=>User::getErrorRegister(),
		"registerValues"=>(isset($_SESSION["registerValues"])) ? $_SESSION["registerValues"] : ["name"=>"", "email"=>"","phone"=>""]
	]);

});

$app->post('/login', function(Request $request, Response $response, array $args){

	try{

		User::login($_POST["login"], $_POST["password"]);

	}catch(Exception $e){

		User::setErrorLogin($e->getMessage());

	}

	header("Location: /checkout");

	exit;

});

$app->get('/logout', function(Request $request, Response $response, array $args){

	User::logout();

	header("Location: /login");

	exit;

});

$app->post('/register', function(Request $request, Response $response, array $args){

	$_SESSION["registerValues"] = $_POST;

	if(!isset($_POST["name"]) || $_POST["name"] === ""){

		User::setErrorRegister("Preencha seu nome!");

		header("Location: /login");

		exit;

	}

	if(!isset($_POST["email"]) || $_POST["email"] === ""){

		User::setErrorRegister("Preencha seu email!");

		header("Location: /login");

		exit;

	}

	if(!isset($_POST["phone"]) || $_POST["phone"] === ""){

		User::setErrorRegister("Preencha seu telefone!");

		header("Location: /login");

		exit;

	}

	if(!isset($_POST["password"]) || $_POST["password"] === ""){

		User::setErrorRegister("Preencha sua senha!");

		header("Location: /login");

		exit;

	}

	if(User::checkLoginExists($_POST["email"]) === true){

		User::setErrorRegister("Email já esta sendo utilizado!");

		header("Location: /login");

		exit;

	}

	$user = new User();

	$user->setData([
		"person"=>$_POST["name"],
		"login"=>$_POST["email"],
		"despassword"=>$_POST["password"],
		"mail"=>$_POST["email"],
		"nrphone"=>$_POST["phone"],
		"inadmin"=>0
	]);

	$user->save();

	User::login($_POST["email"], $_POST["password"]);

	header("Location: /checkout");

	exit;

});

$app->get('/forgot', function(Request $request, Response $response, array $args){

	$page = new Page();

	$page->setTpl("forgot");

});

$app->post('/forgot', function(Request $request, Response $response, array $args){

	$user = User::getForgot($_POST["mail"], false);

	header("Location: /forgot/sent");

	exit;

});

$app->get('/forgot/sent', function(Request $request, Response $response, array $args){

	$page = new Page();

	$page->setTpl("forgot-sent");
	
});

$app->get('/forgot/reset', function(Request $request, Response $response, array $args){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new Page();

	$page->setTpl("forgot-reset", [
		"name"=>$user["person"],
		"code"=>$_GET["code"]
	]);

});

$app->post('/forgot/reset', function(Request $request, Response $response, array $args){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost"=>12
	]);

	$user->setPassword($password);

	$page = new Page();

	$page->setTpl("forgot-reset-success");

});

$app->get('/profile', function(Request $request, Response $response, array $args){

	User::verifyLogin(false);

	$user = User::getFromSession();

	$page = new Page();

	$page->setTpl("profile", [
		"user"=>$user->getValues(),
		"profileMsg"=>User::getSuccess(),
		"profileError"=>User::getErrorRegister()
	]);

});

$app->post('/profile', function(Request $request, Response $response, array $args){

	User::verifyLogin(false);

	$user = User::getFromSession();

	if(!isset($_POST["person"]) || $_POST["person"] === ""){

		User::setErrorRegister("Preencha seu nome!");

		header("Location: /profile");

		exit;

	}

	if(!isset($_POST["mail"]) || $_POST["mail"] === ""){

		User::setErrorRegister("Preencha seu email!");

		header("Location: /profile");

		exit;

	}

	if(!isset($_POST["nrphone"]) || $_POST["nrphone"] === ""){

		User::setErrorRegister("Preencha seu telefone!");

		header("Location: /profile");

		exit;

	}

	if($_POST["mail"] !== $user->getmail()){

		if(User::checkLoginExists($_POST["mail"]) === true){

			User::setErrorRegister("E-mail já cadastrado!");

			header("Location: /profile");

			exit;

		}

	}

	$_POST["inadmin"] = $user->getinadmin();
	$_POST["password"] = $user->getdespassword();
	$_POST["login"] = $_POST["mail"];

	$user->setData($_POST);

	$user->update();

	User::setSuccess("Dados alterados com sucesso!");

	header("Location: /profile");

	exit;

});

?>