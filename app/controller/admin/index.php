<?php class index
{
    public function __construct($data)
    {

    }

    public function index($data)
    {
        $data["title"] = "Admin";
        $data["text_code"] = init::random_text_code(5);
        view::layout("index", $data);
    }

    public function logo($data)
    {
        $data["title"] = "Logo";
        $data["text_code"] = init::random_text_code(10);
        view::layout("index", $data);
    }

    public function settings($data)
    {
        $data["title"] = "Settings";
        $data["text_code"] = init::random_text_code(15);
        view::layout("index", $data);
    }
}
