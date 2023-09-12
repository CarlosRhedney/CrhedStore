<?php
namespace Crhedstore\Model;

use \Crhedstore\DB\Sql;
use \Crhedstore\Model;
use \Crhedstore\Model\User;

class Cart extends Model
{
	const SESSION = "Cart";
	const SESSION_ERROR = "CartError";

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

	public function addProduct(Product $product)
	{

		$sql = new Sql();

		$sql->query("INSERT INTO tb_cartsproducts(idcart, idproduct) VALUES(:idcart, :idproduct)", [
			":idcart"=>$this->getidcart(),
			":idproduct"=>$product->getidproduct()
		]);

		$this->getCalculateTotal();

	}

	public function removeProduct(Product $product, $all = false)
	{

		$sql = new Sql();

		if($all){

			$sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart AND idproduct = :idproduct AND dtremoved IS NULL", [
				":idcart"=>$this->getidcart(),
				":idproduct"=>$product->getidproduct()
			]);
		}else{

			$sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart AND idproduct = :idproduct AND dtremoved IS NULL LIMIT 1", [
				":idcart"=>$this->getidcart(),
				":idproduct"=>$product->getidproduct()
			]);

		}

		$this->getCalculateTotal();

	}

	public function getProducts()
	{

		$sql = new Sql();

		$results = $sql->select("SELECT b.idproduct, b.product, b.price, b.width, b.height, b.length, b.weight, b.url, COUNT(*) AS nrqtd, SUM(b.price) AS vltotal FROM tb_cartsproducts a INNER JOIN tb_products b ON a.idproduct = b.idproduct WHERE a.idcart = :idcart AND a.dtremoved IS NULL GROUP BY b.idproduct, b.product, b.price, b.width, b.height, b.length, b.weight, b.url ORDER BY b.product", array(
			":idcart"=>$this->getidcart()
		));

		return Product::checkList($results);
		
	}

	public function getProductsTotals()
	{

		$sql = new Sql();

		$results = $sql->select("SELECT SUM(price) AS price, SUM(width) AS width, SUM(height) AS height, SUM(length) AS length, SUM(weight) AS weight, COUNT(*) AS nrqtd FROM tb_products a INNER JOIN tb_cartsproducts b ON a.idproduct = b.idproduct WHERE b.idcart = :idcart AND b.dtremoved IS NULL", [
			":idcart"=>$this->getidcart()
		]);

		if(count($results) > 0){

			return $results[0];

		}else{

			return [];

		}

	}

	public function addFreight($zipcode)
	{

		$zipcode = str_replace("-", "", $zipcode);

		$totals = $this->getProductsTotals();

		if($totals["nrqtd"] > 0){

			if($totals["height"] < 2) $totals["height"] = 2;
			if($totals["length"] < 16) $totals["length"] = 16;
			if($totals["width"] < 11) $totals["width"] = 11;

			$qs = http_build_query([
				"nCdEmpresa"=>"",
				"sDsSenha"=>"",
				"nCdServico"=>"40010",
				"sCepOrigem"=>"05821050",
				"sCepDestino"=>$zipcode,
				"nVlPeso"=>$totals["weight"],
				"nCdFormato"=>1,
				"nVlComprimento"=>$totals["length"],
				"nVlAltura"=>$totals["height"],
				"nVlLargura"=>$totals["width"],
				"nVlDiametro"=>"0",
				"sCdMaoPropria"=>"S",
				"nVlValorDeclarado"=>$totals["price"],
				"sCdAvisoRecebimento"=>"S"
			]);

			$xml = simplexml_load_file("http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?" . $qs);

			$result = $xml->Servicos->cServico;

			if($result->MsgErro != ''){

				Cart::setMsgError($result->MsgErro);

			}else{

				Cart::clearMsgError();

			}

			$this->setnrdays($result->PrazoEntrega);
			$this->setfreight(Cart::formatValueToDecimal($result->Valor));
			$this->setzipcode($zipcode);

			$this->save();

			return $result;


		}else{


		}

	}

	public static function formatValueToDecimal($value):float
	{

		$value = str_replace(".", "", $value);

		return str_replace(",", ".", $value);

	}

	public static function setMsgError($msg)
	{

		$_SESSION[Cart::SESSION_ERROR] = $msg;

	}

	public static function getMsgError()
	{

		$msg = (isset($_SESSION[Cart::SESSION_ERROR]) && $_SESSION[Cart::SESSION_ERROR]) ? $_SESSION[Cart::SESSION_ERROR] : "";

		Cart::clearMsgError();

		return $msg;

	}

	public static function clearMsgError()
	{

		$_SESSION[Cart::SESSION_ERROR] = NULL;

	}

	public function updateFreight()
	{

		if($this->getzipcode() != ""){

			$this->addFreight($this->getzipcode());

		}

	}

	public function getValues()
	{

		$this->getCalculateTotal();

		return parent::getValues();

	}

	public function getCalculateTotal()
	{

		$this->updateFreight();

		$totals = $this->getProductsTotals();

		$this->setsubtotal($totals["price"]);
		$this->settotal($totals["price"] + $this->getfreight());

	}
	

}
?>