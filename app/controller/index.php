<? class index extends app
{
    public function  __construct()
    {
        $this->index_Model = new index_Model();
        $this->test_Model = new test_Model();
    }

    public function index($data)
    {
        $data["title"] = "Home";
        $data["text_code"] = $this->test_Model->test("test model");
        $data["text_code1"] = $this->index_Model->test("Index model");
        view::html("index", $data);
    }

    public function corporate($data)
    {
        $data["title"] = "Corporate";
        $data["text_code"] = init::random_text_code();
        view::html("index", $data);
    }

    public function contact($data)
    {
        $data["title"] = "Contact";
        $data["text_code"] = init::random_text_code();
        view::html("index", $data);
    }
}
