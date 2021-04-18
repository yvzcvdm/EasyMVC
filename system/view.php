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

        if (file_exists(VIEW . '/' . $path . '_view.php'))
            require VIEW . '/' . $path . '_view.php';
        else
            require VIEW . '/not_found_view.php';
        ob_end_flush();
        $view = ob_get_contents();
        return $view;
        ob_end_clean();
    }

}

function sanitize_output($buffer) {
    $search = array(
        '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
        '/[^\S ]+\</s',     // strip whitespaces before tags, except space
        '/(\s)+/s',         // shorten multiple whitespace sequences
        '/<!--(.|\s)*?-->/' // Remove HTML comments
    );
    $replace = array(
        '>',
        '<',
        '\\1',
        ''
    );
    $buffer = preg_replace($search, $replace, $buffer);
    return $buffer;
}