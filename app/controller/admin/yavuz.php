<? class yavuz extends init
{
    public function  __construct()
    {
        $this->index_Model = new index_Model();
    }

    public function index($data)
    {
        $data["title"] = "Admin GOG Yavuz Anasayfa";
        $data["text_code"] = init::random_text_code();
        $data["user_list"] = $this->index_Model->index($data);
        view::html("index", $data);
    }

    public function corporate($data)
    {
        $data["title"] = "Admin GOG Yavuz Kurumsal";
        $data["text_code"] = init::random_text_code();
        view::html("index", $data);
    }

    public function contact($data)
    {
        $data["title"] = "Admin GOG Yavuz İletişim";
        $data["text_code"] = init::random_text_code();
        view::html("index", $data);
    }
}