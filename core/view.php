<?php class view
{
    private static function sanitize_output($buffer)
    {
        $search = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/<!--(.|\s)*?-->/');
        $replace = array('>', '<', '\\1', '');
        $buffer = preg_replace($search, $replace, $buffer);
        return $buffer;
    }

    public static function html($path, $data)
    {
        if (isset($data["app"]["get"]["view_json"]))
            return self::json($data) . die();
        elseif (isset($data["app"]["get"]["view_layout"]))
            return self::layout($path, $data) . die();

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

    public static function layout($path, $data)
    {
        if (isset($data["app"]["get"]["view_json"]))
            return self::json($data) . die();
        elseif (isset($data["app"]["get"]["view_html"]))
            return self::html($path, $data) . die();

        ob_start(array("view", "sanitize_output"));
        header('Content-Type:text/html; charset=UTF-8');
        extract($data, EXTR_SKIP);
        if (file_exists(LAYOUT . SEP . 'header.php'))
            require LAYOUT . SEP . 'header.php';

        if (file_exists(VIEW . '/' . $path . '_view.php'))
            require VIEW . '/' . $path . '_view.php';
        else
            print("View file not found!.\n");

        if (file_exists(LAYOUT . SEP . 'footer.php'))
            require LAYOUT . SEP . 'footer.php';
        ob_end_flush();
        $view = ob_get_contents();
        return $view;
        ob_end_clean();
    }

    public static function json($data)
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
