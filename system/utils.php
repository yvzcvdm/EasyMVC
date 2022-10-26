<?php class utils
{

	private $params = array();

	private function __construct()
	{
		$this->params["key_1"] = "Data 1";
	}

	private function export1()
	{
		$this->params["key_2"] = "Data 1";
	}

	private function export2()
	{
		$this->params["key_3"] = "Data 2";
	}

	private function export3()
	{
		$this->params["key_4"] = "Data 3";
	}

	public function export()
	{
		$this->params["key_5"] = "Data 1";
		return $this->params;
	}
	
}
