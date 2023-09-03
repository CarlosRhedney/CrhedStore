<?php
session_start();

require_once("vendor/autoload.php");

use Psr\Http\Message\ResponseInterface AS Response;
use Psr\Http\Message\ServerRequestInterface AS Request;

use \Slim\App;

$config = array(
	"settings"=>[
		"displayErrorDetails"=>true
	]
);

$app = new App($config);

require_once("site.php");
require_once("admin.php");
require_once("admin-login.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-products.php");

$app->run();
?>