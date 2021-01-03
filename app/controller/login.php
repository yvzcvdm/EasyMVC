<?php class login
{

    public function __construct()
    {
        $this->init = new init();
        $this->view = new view();
        $this->login_Model = new login_Model();
    }

    public function index($data)
    {
        $data["title"] = "Login";
        $data["user"] = $this->login_Model->loginGet();
        $this->view->export("login/index", $data);
    }

    public function test($data)
    {
        $data["title"] = "Login Test";
        $data["user"] = $this->login_Model->loginGet();
        $this->view->export("login/test", $data);
    }
}
