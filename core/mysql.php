<?php class mysql
{
	public $mysql;
	private static $instance = null;

	public function __construct()
	{
		if (self::$instance === null) {
			$config = parse_ini_file(ROOT . SEP . 'app.ini');
			try {
				$this->mysql = new PDO(
					"mysql:host=$config[db_server];port=$config[db_port];dbname=$config[db_name];charset=utf8mb4",
					$config['db_user'],
					$config['db_pass'],
					[
						PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
					]
				);
				$this->mysql->exec("SET NAMES utf8mb4");
				self::$instance = $this;
			} catch (PDOException $e) {
				die("Veritabanı bağlantı hatası: " . $e->getMessage());
			}
		}
	}

	public static function getInstance()
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function query($sql, $params = [])
	{
		$stmt = $this->mysql->prepare($sql);
		$stmt->execute($params);

		return [
			"column" => $this->columnList($stmt),
			"columnCount" => $stmt->columnCount(),
			"error" => $stmt->errorInfo(),
			"dataCount" => $stmt->rowCount(),
			"data" => $stmt->fetchAll()
		];
	}

	public function execute($sql, $params = [])
	{
		$stmt = $this->mysql->prepare($sql);
		return $stmt->execute($params);
	}

	private function columnList($stmt)
	{
		$columns = [];
		for ($i = 0; $i < $stmt->columnCount(); $i++) {
			$col = $stmt->getColumnMeta($i);
			$columns[] = ['data' => $col['name']];
		}
		return $columns;
	}
}
