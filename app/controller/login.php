<?php class login extends login_Model
{
    public $init;

    public function __construct()
    {
        $this->init = new init();
    }
    
    public function index($param)
    {
        echo "index controller <br><hr><pre>".var_dump($param);
    }

    public function test($param)
    {
        echo "test controller<hr>";
        echo '<pre>' . var_export($param, true) . '</pre>';
    }
}