<? class index extends init
{
    public function  __construct()
    {
        $this->index_Model = new index_Model();
        $this->db = new db();
    }

    public function index($data)
    {
        $data["title"] = "Home";
        $data["text_code"] = $this->index_Model->yavuz("çivdem");
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

    public function yavuz($data)
    {
        $data["title"] = "Yavuz";
        $data["text_code"] = init::random_text_code();
        view::html("index", $data);
    }
}
