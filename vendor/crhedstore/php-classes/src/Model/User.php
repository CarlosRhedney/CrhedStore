<?php
namespace Crhedstore\Model;

use \Crhedstore\DB\Sql;
use \Crhedstore\Model;

class User extends Model
{
	const SESSION = "User";
	const ERROR_LOGIN = "UserErrorLogin";

	public static function login($login, $password)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users WHERE login = :login", [
			":login"=>$login
		]);

		if(count($results) === 0){

			throw new \Exception("Usuário e ou senha inválidos!");

		}

		$data = $results[0];

		if(password_verify($password, $data["despassword"]) === true){

			$user = new User();

			$user->setData($data);

			$_SESSION[User::SESSION] = $user->getValues();

			return $user;

		}else{

			throw new \Exception("Usuário e ou senha inválidos!");

		}

	}

	public static function verifyLogin($inadmin = true)
	{

		if(
			!isset($_SESSION[User::SESSION])
			||
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
			||
			(bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
		){

			header("Location: /admin/login");

			exit;

		}

	}

	public static function logout()
	{

		$_SESSION[User::SESSION] = NULL;

	}

	public static function setErrorLogin($msg)
	{

		$_SESSION[User::ERROR_LOGIN] = $msg;

	}

	public static function getErrorLogin()
	{

		$msg = (isset($_SESSION[User::ERROR_LOGIN]) && $_SESSION[User::ERROR_LOGIN]) ? $_SESSION[User::ERROR_LOGIN] : "";

		User::clearErrorLogin();

		return $msg;

	}

	public static function clearErrorLogin()
	{

		$_SESSION[User::ERROR_LOGIN] = NULL;

	}
}
?>