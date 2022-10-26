<?php class app extends view
{
    private $root;
    private $path;
    private $file;
    private $func;
    private $params;
    private $uri;
    private $method;
    private $error;
    public $config;
    public $app_params;

    public function __construct()
    {
        $this->error = true;
        $this->config = $this->get_config();
        $this->uri = $this->get_uri();
        $this->root = $this->get_root();
        $this->path = $this->get_path();
        $this->file = $this->get_file();
        $this->func = $this->get_function();
        $this->app_params = array();
        $this->params = $this->get_param();
    }

    private function get_root()
    {
        $url_path = dirname($_SERVER['PHP_SELF']) . '/';
        $url_path = stripslashes(trim($url_path));
        $url_path = preg_replace('/(\/+)/', '/', $url_path);
        return $url_path;
    }

    private function get_uri()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = stripslashes(trim($uri) . '/');
        $uri = preg_replace('/(\/+)/', '/', $uri);
        return addslashes($uri);
    }

    private function get_path()
    {
        $url_path = explode($this->root, $this->uri);
        $url_path = array_filter($url_path);
        $url_path = array_values($url_path);
        if (count($url_path) < 2) {
            $url_path = array_shift($url_path);
            $url_path = explode("/", $url_path);
            $url_path = array_filter($url_path);
        }
        $url_path = $this->is_path($url_path);
        $url_path = str_replace("\\", "/", $url_path);
        $url_path = preg_replace('/(\/+)/', '/', $url_path);
        return $url_path;
    }

    private function get_file()
    {
        $url_path = $this->root . $this->path;
        $url_path = str_replace("\\", "/", $url_path);
        $url_path = preg_replace('/(\/+)/', '/', $url_path);
        $url_path = explode($url_path, $this->uri);
        $url_path = array_filter($url_path);
        $url_path = array_shift($url_path);
        $url_path = explode("/", $url_path);
        $file = array_filter($url_path);
        $file = array_shift($file);
        $file = $this->slug($file);
        $file = file_exists(CONTROLLER . $this->path . $file . '.php') ? $file : 'index';
        return $file;
    }

    private function get_function()
    {
        $url_path = $this->root . $this->path;
        $url_path = str_replace("\\", "/", $url_path);
        $url_path = preg_replace('/(\/+)/', '/', $url_path);
        if ($this->file != "index")
            $url_path .= $this->file;
        $url_path = explode($url_path, $this->uri);
        $url_path = array_filter($url_path);
        $url_path = array_shift($url_path);
        $url_path = explode("/", $url_path);
        $url_path = array_filter($url_path);
        $url_path = array_shift($url_path);
        if (class_exists($this->file)) {
            $this->method = new $this->file($this);
            $url_path = method_exists($this->method, $url_path) ? $url_path : "index";
        }
        $url_path = empty($url_path) ? "index" : $url_path;
        return $this->slug($url_path);
    }

    private function get_param($param = array())
    {
        $url_path = $this->path;
        if ($this->file != "index")
            $url_path .= $this->file . '/';
        if ($this->func != "index")
            $url_path .= $this->func . '/';
        $url_path = explode($url_path, $this->uri . '/');
        $url_path = array_filter($url_path);
        if (count($url_path) < 2) {
            $url_path = array_shift($url_path);
            $url_path = '/' . $url_path;
            $url_path = explode("/", $url_path);
            $url_path = array_filter($url_path);
        }
        $param = array_merge($param, $this->uri_get($url_path));
        $param = array_merge($param, $this->input());
        return $this->array_clear($param);
    }

    private function input($param = array())
    {
        $param["app_root"] = $this->root;
        $param["app_path"] = $this->path;
        $param["app_file"] = $this->file;
        $param["app_params"] = $this->app_params;
        $param["app_function"] = $this->func;
        $param["app_uri"] = $this->uri;
        $param["app_post"] = $_POST;
        $param["app_get"] = $_GET;
        $param["app_cookie"] = $_COOKIE;
        $param["app_session"] = $_SESSION;
        $param["app_files"] = $_FILES;
        $param["app_raw"] = $this->get_input_raw();
        return init::array_clear(array_filter($param));
    }

    public function get_input_raw()
    {
        $input_raw = file_get_contents("php://input");
        $input_array = (array) json_decode($input_raw, true);
        return is_array($input_array) ? $input_array : $input_raw;
    }

    public function get_config()
    {
        $data = parse_ini_file(ROOT . SEP . 'app.ini');
        return ($data) ? $data : false;
    }

    private function is_path($path = null)
    {
        $real = null;
        $re = null;
        foreach ($path as $ff) {
            $re .= SEP . $ff;
            if (!is_dir(CONTROLLER . $re))
                break;
            $real .= SEP  . $ff;
        }
        return ($real) ? $real . SEP : SEP;
    }

    private function uri_get($url_path = null)
    {
        $param = array();
        if (isset($url_path)) {
            foreach ($url_path as $key => $value) {
                $param['app_uri_' . $key] = $value;
                unset($param[$key]);
            }
        }
        return $param;
    }

    private function get_model($file_name = null, $dir = null)
    {
        $dir = ($dir) ? $dir : MODEL;
        if (!is_dir($dir) || empty($file_name)) return false;

        $root = array_diff(scandir($dir), array('.', '..'));

        foreach ($root as $value) {
            if (is_file($dir . SEP . $value)) {
                $pathinfo = pathinfo($dir . SEP . $value);
                if ($pathinfo['extension'] == 'php' && $file_name == $pathinfo['filename']) {
                    return $dir . SEP . $value;
                }
            }

            if (is_dir($dir . SEP . $value)) {
                $file = $this->get_model($file_name, $dir . SEP . $value);
                if ($file) return $file;
            }
        }
    }

    public function __destruct()
    {
        $path = $this->path;
        spl_autoload_register(function ($className) use ($path) {

            if (is_file(CONTROLLER . $path . $className . ".php"))
                require_once CONTROLLER . $path . $className . ".php";


            $className = str_replace("_Model", "", $className);

            $get_model = $this->get_model($className);

            if (is_file($get_model))
                require_once $get_model;
        });


        if (class_exists($this->file)) {
            $this->method = new $this->file($this->params);
            if (method_exists($this->method, $this->func)) {
                call_user_func(array($this->method, $this->func), (array) $this->params);
                $this->error = false;
            }
        }

        if ($this->error) {
            require_once SYSTEM . SEP . "error.php";
        }
    }
}
