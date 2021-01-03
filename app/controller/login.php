<?php class login extends login_Model
{
    public $init;
    
    public function __construct()
    {   
        $this->init = new init();
        $this->view = new view();
    }

    public function index($param)
    {

        $this->view->view("login/index" ,$param);
    }

    public function test($param)
    {
        echo "test controller<hr>";
        echo '<pre>' . var_export($param, true) . '</pre>';
    }
}