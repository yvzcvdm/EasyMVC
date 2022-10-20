<?php class index extends app
{
    public function __construct($data)
    {
        $this->index_Model = new index_Model();
        $this->yavuz_Model = new yavuz_Model();
    }

    public function index($data)
    {

        $data["title"] = "Home";
        $data["text_code"] = init::random_text_code(10);
        $data["user_list"] = $this->index_Model->index();
        view::layout("index", $data);
    }

    public function corporate($data)
    {
        $data["title"] = "Corporate";
        $data["text_code"] = init::random_text_code(2);
        view::layout("index", $data);
    }

    public function contact($data)
    {
        $data["title"] = "Contact";
        $data["text_code"] = init::random_text_code(3);
        view::layout("index", $data);
    }
}
