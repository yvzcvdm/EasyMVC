<? class yavuz extends init
{
    public function  __construct()
    {
        // $this->test_Model = new test_Model();
    }

    public function index($data)
    {
        $data["title"] = "Home";
        // $data["text_code"] = $this->test_Model->test("çivdem1");
        view::html("index", $data);
    }

}
