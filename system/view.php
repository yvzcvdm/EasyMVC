<?php class view
{
    public function __construct()
    {
        $this->init = new init();
    }

    public function export($path, $data)
    {
        ob_start("sanitize_output");
        extract($data);

        require VIEW . '/layout/dash_head.php';

        if (file_exists(VIEW . '/' . $path . '_view.php'))
            require VIEW . '/' . $path . '_view.php';
        else
            require VIEW . '/not_found_view.php';

        require VIEW . '/layout/dash_footer.php';

        ob_end_flush();
        $view = ob_get_contents();
        return $view;
        ob_end_clean();
    }

    private function sanitize_output($buffer)
    {
        $search = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s','/<!--(.|\s)*?-->/');
        $replace = array('>','<','\\1','');
        $buffer = preg_replace($search, $replace, $buffer);
        return $buffer;
    }
}