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
        $data["title"] = "Admin Genel Index Sayfa";
        $data["text_code"] = $this->init->text_code();
        $data["model_get"] = $this->test_Model->index();
        
        $this->view->html("test", $data);
    }

    public function home($data)
    {
        $data["title"] = "Admin Genel Anasayfa Sayfa";
        $data["text_code"] = $this->init->text_code();
        $data["model_get"] = $this->test_Model->home();
        
        $this->view->html("test", $data);
    }

    public function corporate($data)
    {
        $data["title"] = "Admin Genel Kurumsal Sayfa";
        $data["text_code"] = $this->init->text_code();
        $data["model_get"] = $this->test_Model->corporate();
        
        $this->view->html("test", $data);
    }

    public function contact($data)
    {

        $data["title"] = "Admin Genel İletişim Sayfa";
        $data["text_code"] = $this->init->text_code();
        $data["model_get"] = $this->test_Model->contact();
        
        $this->view->html("test", $data);
    }
}
