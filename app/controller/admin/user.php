<?php class user extends app
{
    public function  __construct()
    {
        $this->index_Model = new index_Model();
    }

    public function index($data)
    {
        $data["title"] = "User List";
        view::layout("index", $data);
    }

    public function edit($data)
    {
        $data["title"] = "Edit";
        view::layout("index", $data);
    }

    public function add($data)
    {
        $data["title"] = "Add";
        view::layout("index", $data);
    }
}
