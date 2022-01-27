<? class view
{
    public function __construct()
    {
        $this->init = new init();
    }

    public function html($path, $data)
    {
        if (isset($data["app_get"]["json"]))
            return $this->json($data);

        ob_start(array("view", "sanitize_output"));
        header('Content-Type:text/html; charset=UTF-8');
        extract($data, EXTR_SKIP);
        if (file_exists(VIEW . '/' . $path . '_view.php'))
            require VIEW . '/' . $path . '_view.php';
        else
            http_response_code(404) . die("404 Sayfa Bulunamadı.\n");
        ob_end_flush();
        $view = ob_get_contents();
        return $view;
        ob_end_clean();
    }

    public function json($data)
    {
        ob_start(array("view", "sanitize_output"));
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        ob_end_flush();
        $view = ob_get_contents();
        return $view;
        ob_end_clean();
    }

    private function sanitize_output($buffer)
    {
        $search = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/<!--(.|\s)*?-->/');
        $replace = array('>', '<', '\\1', '');
        $buffer = preg_replace($search, $replace, $buffer);
        return $buffer;
    }
}
