<?php abstract class view extends init
{

    private function sanitize_output($buffer)
    {
        $search = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/<!--(.|\s)*?-->/');
        $replace = array('>', '<', '\\1', '');
        $buffer = preg_replace($search, $replace, $buffer);
        return $buffer;
    }

    public function html($path, $data)
    {
        if (isset($data["app_get"]["view_json"]))
            return $this->json($data) . die();
        elseif (isset($data["app_get"]["view_layout"]))
            return $this->layout($path, $data) . die();

        ob_start(array("view", "sanitize_output"));
        header('Content-Type:text/html; charset=UTF-8');
        extract($data, EXTR_SKIP);
        if (file_exists(VIEW . '/' . $path . '_view.php'))
            require VIEW . '/' . $path . '_view.php';
        else
            print("View file not found!.\n");
        ob_end_flush();
        $view = ob_get_contents();
        return $view;
        ob_end_clean();
    }

    public function layout($path, $data)
    {
        if (isset($data["app_get"]["view_json"]))
            return $this->json($data) . die();
        elseif (isset($data["app_get"]["view_html"]))
            return $this->html($path, $data) . die();

        ob_start(array("view", "sanitize_output"));
        header('Content-Type:text/html; charset=UTF-8');
        extract($data, EXTR_SKIP);
        if (file_exists(LAYOUT . SEP . 'header.php'))
            require LAYOUT . SEP . 'header.php';

        if (file_exists(VIEW . '/' . $path . '_view.php'))
            require VIEW . '/' . $path . '_view.php';
        else
            print("View file not found!.\n");

        if (file_exists(LAYOUT . SEP . 'header.php'))
            require LAYOUT . SEP . 'header.php';
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
}
