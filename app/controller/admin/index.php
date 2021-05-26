<? class index
{
    public function  __construct()
    {
        $this->init = new init();
        $this->view = new view();
        $this->test_Model = new test_Model();
    }

    public function index($data)
    {
        $data["title"] = "Admin Index Sayfa";
        $data["text_code"] = $this->init->text_code();
        $data["model_get"] = $this->test_Model->index();
        $data["content"] = $data["lorem_200"];
        $this->view->html("test", $data);
    }

    public function home($data)
    {
        $data["title"] = "Admin Anasayfa Sayfa";
        $data["text_code"] = $this->init->text_code();
        $data["model_get"] = $this->test_Model->home();
        $data["content"] = $data["lorem_300"];
        $this->view->html("test", $data);
    }

    public function corporate($data)
    {
        $data["title"] = "Admin Kurumsal Sayfa";
        $data["text_code"] = $this->init->text_code();
        $data["model_get"] = $this->test_Model->corporate();
        $data["content"] = $data["lorem_200"];
        $this->view->html("test", $data);
    }

    public function contact($data)
    {

        $data["title"] = "Admin İletişim Sayfa";
        $data["text_code"] = $this->init->text_code();
        $data["model_get"] = $this->test_Model->contact();
        $data["content"] = $data["lorem_300"];
        $this->view->html("test", $data);
    }
}
