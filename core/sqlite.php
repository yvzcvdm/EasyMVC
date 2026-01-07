<?php class sqlite
{
	public $sqlite;
	private static $instance = null;

	private function __construct()
	{
		$config = app::get_config();
		$db_config = isset($config['sqlite']) ? $config['sqlite'] : $config;
		
		// SQLite database path
		$db_dir = ROOT . SEP . 'database';
		if (!is_dir($db_dir)) {
			mkdir($db_dir, 0755, true);
		}
		
		$db_path = $db_dir . SEP . ($db_config['db_name'] ?? 'app') . '.db';
		
		try {
			$this->sqlite = new PDO(
				"sqlite:" . $db_path,
				null,
				null,
				[
					PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				]
			);
			$this->sqlite->exec("PRAGMA foreign_keys = ON");
		} catch (PDOException $e) {
			// error_log("SQLite bağlantı hatası: " . $e->getMessage());	
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
		$stmt = $this->sqlite->prepare($sql);
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
		$stmt = $this->sqlite->prepare($sql);
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
