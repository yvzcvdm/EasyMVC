<? class index
{
    public function  __construct()
    {
        $this->init = new init();
        $this->view = new view();
    }

    public function index($data)
    {
        $this->view->html("index", $data);
    }
}
