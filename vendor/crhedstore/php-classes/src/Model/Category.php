<?php
namespace Crhedstore\Model;

use \Crhedstore\DB\Sql;
use \Crhedstore\Model;

class Category extends Model
{

	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_categories ORDER BY category");

	}

	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_categories_save(:idcategory, :category)", [
			":idcategory"=>$this->getidcategory(),
			":category"=>$this->getcategory()
		]);

		$this->setData($results[0]);

		Category::updateFile();

	}

	public function get($idcategory)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", [
			":idcategory"=>$idcategory
		]);

		$this->setData($results[0]);

	}

	public function delete()
	{

		$sql = new Sql();

		$sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", [
			":idcategory"=>$this->getidcategory()
		]);

		Category::updateFile();

	}

	public static function updateFile()
	{

		$categories = Category::listAll();

		$html = [];

		foreach($categories as $row){

			array_push($html, '<li><a href="/categories/'.$row["idcategory"].'">'.$row["category"].'</a></li>');

		}

		file_put_contents($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode("", $html));

	}

	public function getProducts($related = true)
	{

		$sql = new Sql();

		if($related){

			return $sql->select("
				SELECT * FROM tb_products WHERE idproduct IN(
					SELECT a.idproduct FROM tb_products a INNER JOIN tb_categoriesproducts b ON a.idproduct = b.idproduct WHERE b.idcategory = :idcategory
				)
			", [
				":idcategory"=>$this->getidcategory()
			]);

		}else{

			return $sql->select("
				SELECT * FROM tb_products WHERE idproduct NOT IN(
					SELECT a.idproduct FROM tb_products a INNER JOIN tb_categoriesproducts b ON a.idproduct = b.idproduct WHERE b.idcategory = :idcategory
				)
			", [
				":idcategory"=>$this->getidcategory()
			]);

		}
	}

	public function addProduct(Product $product)
	{

		$sql = new Sql();

		$sql->query("INSERT INTO tb_categoriesproducts(idcategory, idproduct) VALUES(:idcategory, :idproduct)", [
			":idcategory"=>$this->getidcategory(),
			":idproduct"=>$product->getidproduct()
		]);

	}

	public function removeProduct(Product $product)
	{

		$sql = new Sql();

		$sql->query("DELETE FROM tb_categoriesproducts WHERE idcategory = :idcategory AND idproduct = :idproduct", [
			":idcategory"=>$this->getidcategory(),
			":idproduct"=>$product->getidproduct()
		]);
		
	}

}
?>