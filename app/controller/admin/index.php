<? class index extends app
{
    public function  __construct()
    {
        $this->index_Model = new index_Model();
    }

    public function index($data)
    {
        $data["title"] = "Admin";
        view::html("index", $data);
    }

    public function users($data)
    {
        $data["title"] = "Users";
        view::html("index", $data);
    }

    public function settings($data)
    {
        $data["title"] = "Settings";
        view::html("index", $data);
    }
}
