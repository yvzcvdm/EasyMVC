<?php class login extends login_Model
{
    public $init;

    public function __construct()
    {
        $this->init = new init();
    }
    
    public function index($param)
    {
        
        echo "index";
    }

    public function yavuz($param)
    {
        
        echo $param["p_email"];
        
    }
}