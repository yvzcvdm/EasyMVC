<?php class user extends app
{
    public function __construct($data)
    {

    }

    public function index($data)
    {
        $data["title"] = "User List";
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
