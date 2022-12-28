<?php class user extends app
{
    public function __construct()
    {
        $this->yavuz_Model = new yavuz_Model();
    }

    public function index($data)
    {
        $data["title"] = "User List";
        $data["user_list"] = $this->yavuz_Model->index();
        $data["text_code"] = init::random_text_code(10);
        view::layout("index", $data);
    }

    public function edit($data)
    {
        $data["title"] = "Edit";
        $data["text_code"] = init::random_text_code(5);
        view::layout("index", $data);
    }

    public function add($data)
    {
        $data["title"] = "Add";
        $data["text_code"] = init::random_text_code(15);
        view::layout("index", $data);
    }
}
