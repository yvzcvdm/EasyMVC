<? class index
{
    public function  __construct()
    {
        $this->init = new init();
        $this->view = new view();
        $this->test_Model = new test_Model();
        $this->index_Model = new index_Model();
    }

    public function index($data)
    {

        $data["title"] = "Root Index Sayfa";
        $data["text_code"] = $this->init->text_code();
        $data["user_list"] = $this->index_Model->index($data);
        
        $this->view->html("test", $data);
    }

    public function home($data)
    {
        $data["title"] = "Root Anasayfa Sayfa";
        $data["text_code"] = $this->init->text_code();
        $data["model_get"] = $this->test_Model->home();
        
        $this->view->html("test", $data);
    }

    public function corporate($data)
    {
        $data["title"] = "Root Kurumsal Sayfa";
        $data["text_code"] = $this->init->text_code();
        $data["model_get"] = $this->test_Model->corporate();
        
        $this->view->html("test", $data);
    }

    public function contact($data)
    {

        $data["title"] = "Root İletişim Sayfa";
        $data["text_code"] = $this->init->text_code();
        $data["model_get"] = $this->test_Model->contact();
        
        $this->view->html("test", $data);
    }
}
