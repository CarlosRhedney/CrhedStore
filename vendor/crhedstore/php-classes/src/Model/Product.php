<?php
namespace Crhedstore\Model;

use \Crhedstore\DB\Sql;
use \Crhedstore\Model;

class Product extends Model
{

	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_products ORDER BY product");

	}

	public static function checkList($list)
	{

		foreach($list as &$row){

			$p = new Product();

			$p->setData($row);

			$row = $p->getValues();

		}

		return $list;
		
	}

	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_products_save(:idproduct, :product, :price, :width, :height, :length, :weight, :url)", [
			":idproduct"=>$this->getidproduct(),
			":product"=>$this->getproduct(),
			":price"=>$this->getprice(),
			":width"=>$this->getwidth(),
			":height"=>$this->getheight(),
			":length"=>$this->getlength(),
			":weight"=>$this->getweight(),
			":url"=>$this->geturl()
		]);

		$this->setData($results[0]);

	}

	public function get($idproduct)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", [
			":idproduct"=>$idproduct
		]);

		$this->setData($results[0]);

	}

	public function getPhotos()
	{

		$sql = new Sql();

		return $sql->select("SELECT a.photo FROM tb_photos a INNER JOIN tb_products b ON a.idproduct = b.idproduct WHERE a.idproduct = :idproduct", [
			":idproduct"=>$this->getidproduct()
		]);

	}

	public function delete()
	{

		$sql = new Sql();

		$sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", [
			":idproduct"=>$this->getidproduct()
		]);

	}

	public function getValues()
	{

		$this->checkPhoto();

		$values = parent::getValues();

		return $values;

	}

	public function checkPhoto()
	{

		if(file_exists("assets/site/img/products/" . $this->getproduct() . ".jpg")){

			$url = "/assets/site/img/products/" . $this->getproduct() . ".jpg";

		}else{

			$url = "/assets/site/img/product-1.jpg";

		}

		return $this->setphoto($url);

	}

	public function addPhoto($file)
	{

		$ext = explode(".", $file["name"]);
		$extension = end($ext);
		$name = reset($ext);

		switch($extension){

			case "jpeg":
			case "jpg":
				$image = imagecreatefromjpeg($file["tmp_name"]);
			break;

			case "gif":
				$image = imagecreatefromgif($file["tmp_name"]);
			break;

			case "png":
				$image = imagecreatefrompng($file["tmp_name"]);
			break;

		}

		$dist = "assets/site/img/products/" . $this->getproduct() . ".jpg";

		$sql = new Sql();

		$sql->query("INSERT INTO tb_photos(idproduct, photo) VALUES(:idproduct, :photo)", [
			":idproduct"=>$this->getidproduct(),
			":photo"=>$dist
		]);

		imagejpeg($image, $dist);
		imagedestroy($image);

		$this->checkPhoto();


	}

}
?>