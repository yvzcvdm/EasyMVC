<?php class user
{

    public function __construct()
    {
        $this->init = new init();
        $this->view = new view();
        $this->user_Model = new user_Model();
    }

    public function index($data)
    {
        $data["title"] = "User";
        $data["user"] = $this->user_Model->userGet();
        $this->view->export("user/index", $data);
    }

    public function test($data)
    {
        $data["title"] = "User Test";
        $data["user"] = $this->user_Model->userGet();
        $this->view->export("user/test", $data);
    }
}
