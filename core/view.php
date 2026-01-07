<?php class view
{    // Dosya varlık cache'i - her istekte aynı dosyaları tekrar kontrol etme
    private static $file_cache = [];

    private static function file_exists_cached($path)
    {
        if (!isset(self::$file_cache[$path])) {
            self::$file_cache[$path] = file_exists($path);
        }
        return self::$file_cache[$path];
    }

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
            return self::json($data);
        elseif (isset($data["app"]["get"]["view_layout"]))
            return self::layout($path, $data);

        ob_start(array("view", "sanitize_output"));
        header('Content-Type:text/html; charset=UTF-8');
        extract($data, EXTR_SKIP);

        $view_path = VIEW . '/' . $path . '_view.php';
        if (self::file_exists_cached($view_path))
            require $view_path;
        else
            print("View file not found!.\n");

        ob_end_flush();
    }

    public static function layout($path, $data)
    {
        // ob_start(array("view", "sanitize_output"));
        ob_start();
        header('Content-Type:text/html; charset=UTF-8');
        extract($data, EXTR_SKIP);

        $header_path = LAYOUT . SEP . 'header.php';
        if (self::file_exists_cached($header_path))
            require $header_path;

        $view_path = VIEW . '/' . $path . '_view.php';
        if (self::file_exists_cached($view_path))
            require $view_path;
        else
            print("View file not found!.\n");

        $footer_path = LAYOUT . SEP . 'footer.php';
        if (self::file_exists_cached($footer_path))
            require $footer_path;

        ob_end_flush();
    }

    public static function json($data)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}
