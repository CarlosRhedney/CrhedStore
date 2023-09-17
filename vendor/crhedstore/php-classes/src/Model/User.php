<?php
namespace Crhedstore\Model;

use \Crhedstore\DB\Sql;
use \Crhedstore\Model;
use \Crhedstore\Mailer;

class User extends Model
{
	const SESSION = "User";
	const SECRET = "CarlosRhedneySan"; //Primeira chave precisa ter 16 caracteres
	const SECRET_IV = "CarlosRhedneySantos"; //Segunda 19 caracteres
	const ERROR_LOGIN = "UserErrorLogin";
	const ERROR_REGISTER = "UserErrorRegister";
	const UPDATE_SUCCESS = "UserUpdateSuccess";

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

	public static function getFromSession()
	{

		$idperson = (int)$_SESSION[User::SESSION]["idperson"];

		$user = new User();

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.idperson = :idperson", [
			":idperson"=>$idperson
		]);

		$user->setData($results[0]);

		return $user;

	}

	public static function checkLogin($inadmin = true)
	{

		if(
			!isset($_SESSION[User::SESSION])
			||
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
		){

			return false;

		}else{

			if($inadmin === true && (bool)$_SESSION[User::SESSION]["inadmin"] === true){

				return true;

			}else if($inadmin === false){

				return true;

			}else{

				return false;

			}
		}
	}

	public static function verifyLogin($inadmin = true)
	{

		if(!User::checkLogin($inadmin)){

			if($inadmin){

				header("Location: /admin/login");

				exit;

			}else{

				header("Location: /login");

				exit;

			}

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

	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.person");

	}

	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_users_save(:person, :login, :despassword, :mail, :nrphone, :inadmin)", [
			":person"=>$this->getperson(),
			":login"=>$this->getlogin(),
			":despassword"=>User::getPasswordHash($this->getdespassword()),
			":mail"=>$this->getmail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		]);

		$this->setData($results[0]);

	}

	public function get($iduser)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", [
			":iduser"=>$iduser
		]);

		$this->setData($results[0]);

	}

	public function update()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_usersupdate_save(:iduser, :person, :login, :despassword, :mail, :nrphone, :inadmin)", [
			":iduser"=>$this->getiduser(),
			":person"=>$this->getperson(),
			":login"=>$this->getlogin(),
			":despassword"=>$this->getdespassword(),
			":mail"=>$this->getmail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		]);

		$this->setData($results[0]);

	}

	public function delete()
	{

		$sql = new Sql();

		$sql->query("CALL sp_users_delete(:iduser)", [
			":iduser"=>$this->getiduser()
		]);
		
	}

	public static function getForgot($mail, $inadmin = true)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.mail = :mail", [
			":mail"=>$mail
		]);

		if(count($results) === 0){

			throw new \Exception("Não foi possível recuperar a senha!");

		}else{

			$data = $results[0];

			$resultsRecovery = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :ip)", [
				":iduser"=>$data["iduser"],
				":ip"=>$_SERVER["REMOTE_ADDR"]
			]);

			if(count($resultsRecovery) === 0){

				throw new \Exception("Não foi possível recuperar a senha!");

			}else{

				$dataRecovery = $resultsRecovery[0];

				$cod = openssl_encrypt($dataRecovery["idrecovery"], 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

				$code = base64_encode($cod);

				if($inadmin === true){

					$link = "http://www.crhedstore.com.br/admin/forgot/reset?code=$code";

				}else{

					$link = "http://www.crhedstore.com.br/forgot/reset?code=$code";

				}

				$mailer = new Mailer($data["mail"], $data["person"], "Redefinir senha CrhedStore", "forgot", [
					"name"=>$data["person"],
					"link"=>$link
				]);

				$mailer->send();

				return $data;

			}

		}

	}

	public static function validForgotDecrypt($code)
	{

		$code = base64_decode($code);

		$idrecovery = openssl_decrypt($code, 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_userspasswordsrecoveries a INNER JOIN tb_users b USING(iduser) INNER JOIN tb_persons c USING(idperson) WHERE a.idrecovery = :idrecovery AND a.dtrecovery IS NULL AND DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW()", [
			":idrecovery"=>$idrecovery
		]);

		if(count($results) === 0){

			throw new \Exception("Não foi possível recuperar a senha!");

		}else{

			return $results[0];

		}

	}

	public static function setForgotUsed($idrecovery)
	{

		$sql = new Sql();

		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", [
			":idrecovery"=>$idrecovery
		]);

	}

	public function setPassword($password)
	{

		$sql = new Sql();

		$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", [
			":password"=>$password,
			":iduser"=>$this->getiduser()
		]);
		
	}

	public static function getPasswordHash($password)
	{

		return password_hash($password, PASSWORD_DEFAULT, [
			"cost"=>12
		]);

	}

	public static function setErrorRegister($msg)
	{

		$_SESSION[User::ERROR_REGISTER] = $msg;

	}

	public static function getErrorRegister()
	{

		$msg = (isset($_SESSION[User::ERROR_REGISTER]) && $_SESSION[User::ERROR_REGISTER]) ? $_SESSION[User::ERROR_REGISTER] : "";

		User::clearErrorRegister();

		return $msg;

	}

	public static function clearErrorRegister()
	{

		$_SESSION[User::ERROR_REGISTER] = NULL;

	}

	public static function checkLoginExists($login)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users WHERE login = :login", [
			":login"=>$login
		]);

		return (count($results) > 0);

	}

	public static function setSuccess($msg)
	{

		$_SESSION[User::UPDATE_SUCCESS] = $msg;

	}

	public static function getSuccess()
	{

		$msg = (isset($_SESSION[User::UPDATE_SUCCESS]) && $_SESSION[User::UPDATE_SUCCESS]) ? $_SESSION[User::UPDATE_SUCCESS] : "";

		User::clearSuccess();

		return $msg;

	}

	public static function clearSuccess()
	{

		$_SESSION[User::UPDATE_SUCCESS] = NULL;
		
	}

}
?>