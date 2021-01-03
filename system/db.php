<?php class db
{
	public $db_host = "localhost";
	public $db_user = "root";
	public $db_pass = "";
	public $db_name = "weebim_course";
	public $db_port = "3306";
	public $db;

	public function __construct()
	{
		try { 
			$this->db = new PDO("mysql:host=$this->db_host;port=$this->db_port;dbname=$this->db_name", $this->db_user, $this->db_pass);
			$this->db->exec("set names utf8");
		} catch (PDOException $e) {
			echo "Veritabanı bağlantı hatası!";
			exit;
		}
	}

	public function proc($param){
		$stmt = $this->db->prepare($param);
		return $this->data_export($stmt);
	}

	public function data_export($stmt, $status=200)
	{
		$stmt->execute();
		$result["column"] = $this->columnList($stmt);
		$result["columnCount"] = $stmt->columnCount();
		$result["dataCount"] = $stmt->rowCount();
		$result["data"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	public function columnList($stmt)
	{
		for ($i = 0; $i < $stmt->columnCount(); $i++) {
			$col = $stmt->getColumnMeta($i);
			$columns[]['data'] = $col['name'];
		} 
		return isset($columns) ? $columns:null;
	}	
}