<?php class autoload
{

    private $data =null;

    public function __construct($data)
    {
        $this->data = $data;
        $this->yavuz();
    }

    private function yavuz()
    {
        var_dump($this->data);
    }

}
