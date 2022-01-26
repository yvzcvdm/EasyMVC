<? class index extends init
{
    public function  __construct()
    {
        $this->index_Model = new index_Model();
    }

    public function index($data)
    {
        $data["title"] = "Anasayfa";
        $data["text_code"] = init::random_text_code();
        $data["user_list"] = $this->index_Model->index($data);
        view::html("index", $data);
    }

    public function corporate($data)
    {
        $data["title"] = "Kurumsal";
        $data["text_code"] = init::random_text_code();
        view::html("index", $data);
    }

    public function contact($data)
    {
        $data["title"] = "İletişim";
        $data["text_code"] = init::random_text_code();
        view::html("index", $data);
    }
}