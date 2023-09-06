<?php
namespace Crhedstore\Model;

use \Crhedstore\DB\Sql;
use \Crhedstore\Model;
use \Crhedstore\Model\User;

class Cart extends Model
{
	const SESSION = "Cart";

	public static function getFromSession()
	{

		$cart = new Cart();

		if(isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]["idcart"] > 0){

			$cart->get((int)$_SESSION[Cart::SESSION]["idcart"]);

		}else{

			$cart->getFromSessionId();

			if(!(int)$cart->getidcart() > 0){

				$data = [
					"sessionid"=>session_id()
				];

				if(User::checkLogin(false)){

					$user = User::getFromSession();

					$data["iduser"] = $user->getiduser();

				}

				$cart->setData($data);

				$cart->save();

				$cart->setToSession();

			}
		}

		return $cart;

	}

	public function setToSession()
	{

		$_SESSION[Cart::SESSION] = $this->getValues();
		
	}

	public function getFromSessionId()
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_carts WHERE sessionid = :sessionid", [
			":sessionid"=>session_id()
		]);

		if(count($results) > 0){

			$this->setData($results[0]);

		}
	}

	public function get(int $idcart)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart", [
			":idcart"=>$idcart
		]);

		if(count($results) > 0){

			$this->setData($results[0]);

		}
	}

	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_carts_save(:idcart, :sessionid, :iduser, :zipcode, :freight, :nrdays)", [
			":idcart"=>$this->getidcart(),
			":sessionid"=>$this->getsessionid(),
			":iduser"=>$this->getiduser(),
			":zipcode"=>$this->getzipcode(),
			":freight"=>$this->getfreight(),
			":nrdays"=>$this->getnrdays()
		]);

		$this->setData($results[0]);

	}
}
?>