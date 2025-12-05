<?php class mongodb
{
	public $mongodb;
	private static $instance = null;

	public function __construct()
	{
		if (self::$instance === null) {
			$config = parse_ini_file(ROOT . SEP . 'app.ini');
			$db_config = isset($config['mongodb']) ? array_merge($config, $config['mongodb']) : $config;
			try {
				$uri = "mongodb://";
				
				if (!empty($db_config['db_user']) && !empty($db_config['db_pass'])) {
					$uri .= $db_config['db_user'] . ":" . $db_config['db_pass'] . "@";
				}
				
				$uri .= $db_config['db_server'] . ":" . $db_config['db_port'];
				
				$this->mongodb = new MongoDB\Client($uri);
				$this->mongodb->selectDatabase($db_config['db_name']);
				
				self::$instance = $this;
			} catch (Exception $e) {
				// die("MongoDB bağlantı hatası: " . $e->getMessage());	
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

	public function query($collection, $filter = [], $options = [])
	{
		$db = $this->mongodb->selectDatabase($this->getCurrentDatabase());
		$col = $db->selectCollection($collection);
		$cursor = $col->find($filter, $options);

		$data = [];
		foreach ($cursor as $document) {
			$data[] = (array) $document;
		}

		return [
			"column" => [],
			"columnCount" => 0,
			"error" => [],
			"dataCount" => count($data),
			"data" => $data
		];
	}

	public function execute($collection, $operation, $data = [])
	{
		$db = $this->mongodb->selectDatabase($this->getCurrentDatabase());
		$col = $db->selectCollection($collection);

		try {
			switch (strtolower($operation)) {
				case 'insert':
					$result = $col->insertOne($data);
					return $result->getInsertedId();
				case 'update':
					$filter = $data['filter'] ?? [];
					$update = $data['update'] ?? [];
					$col->updateOne($filter, ['$set' => $update]);
					return true;
				case 'delete':
					$filter = $data['filter'] ?? [];
					$col->deleteOne($filter);
					return true;
				default:
					return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}

	private function getCurrentDatabase()
	{
		$config = parse_ini_file(ROOT . SEP . 'app.ini');
		$db_config = isset($config['mongodb']) ? array_merge($config, $config['mongodb']) : $config;
		return $db_config['db_name'] ?? 'test';
	}
}
