<? class index extends app
{
    public function  __construct()
    {
        $this->index_Model = new index_Model();
        $this->test_Model = new test_Model();
    }

    public function index($data)
    {
        $data["title"] = "Admin";
        view::html("index", $data);
    }


}
