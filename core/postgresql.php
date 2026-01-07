<?php class postgresql
{
	public $postgresql;
	private static $instance = null;

	private function __construct()
	{
		$config = app::get_config();
		$db_config = isset($config['postgresql']) ? $config['postgresql'] : $config;
		try {
			$this->postgresql = new PDO(
				"pgsql:host=$db_config[db_server];port=$db_config[db_port];dbname=$db_config[db_name];",
				$db_config['db_user'],
				$db_config['db_pass'],
				[
					PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				]
			);
			$this->postgresql->exec("SET client_encoding = 'UTF8'");
		} catch (PDOException $e) {
			error_log('Postgres connection error: ' . $e->getMessage());
			$this->postgresql = null;
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
		if ($this->postgresql === null) {
			return [
				"column" => [],
				"columnCount" => 0,
				"error" => ["00000", 0, "No database connection"],
				"dataCount" => 0,
				"data" => []
			];
		}

		$stmt = $this->postgresql->prepare($sql);
		if ($stmt === false) {
			$err = $this->postgresql->errorInfo();
			return [
				"column" => [],
				"columnCount" => 0,
				"error" => $err,
				"dataCount" => 0,
				"data" => []
			];
		}

		$ok = $stmt->execute($params);
		if ($ok === false) {
			$err = $stmt->errorInfo();
			return [
				"column" => [],
				"columnCount" => 0,
				"error" => $err,
				"dataCount" => 0,
				"data" => []
			];
		}

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
