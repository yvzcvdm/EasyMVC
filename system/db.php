<?php class db
{
	public $db_host = "localhost";
	public $db_user = "root";
	public $db_pass = "";
	public $db_name = "admin_my";
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
		return $this->json_view($stmt);
	}

	public function json_view($stmt, $status=200)
	{
		$stmt->execute();
		$result["info"]["status"] = $status;
		$result["info"]["message"] = "Kaydedildi";
		if($stmt->errorInfo()[2])
			$result["info"]["error"] = $stmt->errorInfo();
		$result["info"]["columns"] = $this->columnList($stmt);
		$result["info"]["column_count"] = $stmt->columnCount();
		$result["info"]["recordsTotal"] = $stmt->rowCount();
		$result["info"]["recordsFiltered"] = $stmt->rowCount();
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$result["data"] = $this->rec_array_values($data);
		return $result;
	}

	public function rec_array_values(&$item){
		if(is_object($item)){
			foreach ($item as $key => &$value) {
				$key = $this->rec_array_values($value);
			}
		}else if(is_array($item)){
			foreach ($item as $key => &$value) {
				$key = $this->rec_array_values($value);
			}
			$item = array_values($item);
		}
		return $item;
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