<?php
use Psr\Http\Message\ResponseInterface AS Response;
use Psr\Http\Message\ServerRequestInterface AS Request;
use \Crhedstore\PageAdmin;
use \Crhedstore\Model\User;

$app->get('/admin', function(Request $request, Response $response, array $args){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");
	
});

?>