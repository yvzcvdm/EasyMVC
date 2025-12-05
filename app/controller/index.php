<?php class index
{
    private $file;
    public function __construct()
    {
        $this->file = new File();
    }

    public function index($data)
    {

        $data["title"] = "Home";
        $data["text_code"] = init::random_text_code(10);
        view::layout("index", $data);
    }

    public function corporate($data)
    {
        $data["title"] = "Corporate";
        $data["text_code"] = init::random_text_code(2);
        view::layout("index", $data);
    }

    public function contact($data)
    {
        $data["title"] = "Contact";
        $data["text_code"] = init::random_text_code(3);
        view::layout("index", $data);
    }    
    
    public function upload($data)
    {
        $data["title"] = "Upload";
        $result = $this->file->upload("file_input[]", "/public/uploads/yavuz/");
        $data["list_upload"] = $result['uploads'];
        $data["errors"] = $result['errors'];
        view::layout("upload", $data);
    }
}
