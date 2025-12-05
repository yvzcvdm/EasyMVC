<?php class postgresql
{
	public $postgresql;
	private static $instance = null;

	public function __construct()
	{
		if (self::$instance === null) {
			$config = parse_ini_file(ROOT . SEP . 'app.ini');
			$db_config = isset($config['postgresql']) ? array_merge($config, $config['postgresql']) : $config;
			try {
				$this->postgresql = new PDO(
					"pgsql:host=$db_config[db_server];port=$db_config[db_port];dbname=$db_config[db_name];",
					$db_config['db_user'],
					$db_config['db_pass'],
					[
						PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
					]
				);
				$this->postgresql->exec("SET client_encoding = 'UTF8'");
				self::$instance = $this;
			} catch (PDOException $e) {
				// die("Veritabanı bağlantı hatası: " . $e->getMessage());	
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
		$stmt = $this->postgresql->prepare($sql);
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
		$stmt = $this->postgresql->prepare($sql);
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
