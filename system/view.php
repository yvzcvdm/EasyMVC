<?php
class view
{

    public function view($path, $param)
    {
        ob_start("sanitize_output");
        $param = $param;
        if (file_exists(VIEW . '/' . $path . '_view.php'))
            require VIEW . '/' . $path . '_view.php';

        $view = ob_get_contents();
        return $view;
        ob_end_clean();
    }
}

function sanitize_output($buffer)
{

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
