<?php class index extends app
{
    public function  __construct()
    {
        $this->index_Model = new index_Model();
        $this->test_Model = new test_Model();
    }

    public function index($data)
    {
        $data["title"] = "Home";
        $data["text_code"] = init::random_text_code(10);
        view::html("index", $data);
    }

    public function corporate($data)
    {
        $data["title"] = "Corporate";
        $data["text_code"] = init::random_text_code(2); 
        view::html("index", $data);
    }

    public function contact($data)
    {
        $data["title"] = "Contact";
        $data["text_code"] = init::random_text_code(3);
        view::html("index", $data);
    }
}
