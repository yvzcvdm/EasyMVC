<?php class index extends app
{
    public function __construct($data)
    {
        $this->index_Model = new index_Model();
    }

    public function index($data)
    {
        $data["title"] = "Admin";
        $data["user_list"] = $this->index_Model->index();
        $data["text_code"] = init::random_text_code(1);
        view::layout("index", $data);
    }

    public function users($data)
    {
        $data["title"] = "Users";
        $data["text_code"] = init::random_text_code(2);
        view::layout("index", $data);
    }

    public function settings($data)
    {
        $data["title"] = "Settings";
        $data["text_code"] = init::random_text_code(3);
        view::layout("index", $data);
    }
}
