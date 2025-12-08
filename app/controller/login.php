<?php class login
{
    public function __construct()
    {

    }

    public function index($data)
    {
        $data["title"] = "Login";
        $data["text_code"] = init::random_text_code(10);
        view::layout("index", $data);
    }

    public function register($data)
    {
        $data["title"] = "Login - Register";
        $data["text_code"] = init::random_text_code(5);
        view::layout("index", $data);
    }

    public function iforgot($data)
    {
        $data["title"] = "Register - I forgot my password";
        $data["text_code"] = init::random_text_code(15);
        view::layout("index", $data);
    }
}
