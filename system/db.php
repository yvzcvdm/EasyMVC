<? class db extends init
{


	public $db;

	public function __construct()
	{
		$dbinfo = $this->config();

		try {
			$this->db = new PDO("mysql:host=$dbinfo[db_server];port=$dbinfo[db_port];dbname=$dbinfo[db_name]", $dbinfo['db_user'], $dbinfo['db_pass']);
			$this->db->exec("set names utf8");
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		} catch (PDOException $e) {
			echo "Veritabanı bağlantı hatası!";
			exit;
		}
	}

	public function proc($param)
	{
		$stmt = $this->db->prepare($param);
		return $this->data_export($stmt);
	}

	public function proc_key($param)
	{
		$stmt = $this->db->prepare($param);
		return $this->data_export_key($stmt);
	}

	public function data_export_key($stmt)
	{
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
		$stmt = null;
		return $result;
	}

	public function data_export($stmt)
	{
		$stmt->execute();
		$result["column"] = $this->columnList($stmt);
		$result["columnCount"] = $stmt->columnCount();
		$result["error"] = $stmt->errorInfo();
		$result["dataCount"] = $stmt->rowCount();
		$result["data"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = null;
		return $result;
	}

	public function columnList($stmt)
	{
		for ($i = 0; $i < $stmt->columnCount(); $i++) {
			$col = $stmt->getColumnMeta($i);
			$columns[]['data'] = $col['name'];
		}
		return isset($columns) ? $columns : null;
	}
}
