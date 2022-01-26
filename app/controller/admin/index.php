<? class index extends init
{
    public function  __construct()
    {
        $this->index_Model = new index_Model();
    }

    public function index($data)
    {
        $data["title"] = "Admin Anasayfa Sayfa";
        $data["text_code"] = init::random_text_code();
        $data["user_list"] = $this->index_Model->index($data);
        view::html("index", $data);
    }

    public function corporate($data)
    {
        if($data["app_function"] == "corporate") {
            $data["tamam"] = "ok";
        }
        $data["title"] = "Admin Kurumsal Sayfa";
        $data["text_code"] = init::random_text_code();
        view::html("index", $data);
    }

    public function contact($data)
    {
        $data["title"] = "Admin İletişim Sayfa";
        $data["text_code"] = init::random_text_code();
        view::html("index", $data);
    }
}