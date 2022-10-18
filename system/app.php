<? class app extends view
{
    private $root;
    private $path;
    private $file;
    private $func;
    private $params;
    private $uri;
    private $method;
    public $config;

    public function __construct()
    {
        $this->config = $this->get_config();
        $this->uri = $this->get_uri();
        $this->root = $this->get_root();
        $this->path = $this->get_path();
        $this->file = $this->get_file();
        $this->func = $this->get_function();
        $this->params = $this->get_param();
        $this->app_run();
    }

    private function app_run()
    {

        $path = $this->path;
        spl_autoload_register(function ($className) use ($path) {

            if (file_exists(CONTROLLER . $path . $className . ".php"))
                require_once CONTROLLER . $path . $className . ".php";

            $className = str_replace("_Model", "", $className);

            if (file_exists(MODEL . $path . $className . ".php"))
                require_once MODEL . $path . $className . ".php";
        });

        if (class_exists($this->file)) {
            $this->method = new $this->file($this);
            if (method_exists($this->method, $this->func))
                call_user_func(array($this->method, $this->func), (array) $this->params);
            else
                http_response_code(404) . die("404 Sayfa Bulunamadı.\n");
        } else
            http_response_code(404) . die("404 Sayfa Bulunamadı.\n");
    }

    private function get_path()
    {
        $php_real = dirname($_SERVER['PHP_SELF']);
        $php_real = str_replace("\\", "/", $php_real);
        $php_real = preg_replace('/(\/+)/', '/', $php_real);
        $php_real = explode($php_real, $this->uri);
        $php_real = array_filter($php_real);
        $php_real = array_values($php_real);
        if (count($php_real) < 2) {
            $php_real = array_shift($php_real);
            $php_real = explode("/", $php_real);
            $php_real = array_filter($php_real);
        }
        $php_real = $this->is_path($php_real);
        $php_real = str_replace("\\", "/", $php_real);
        $php_real = preg_replace('/(\/+)/', '/', $php_real);
        return $php_real;
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
        // $url_path = method_exists($this->method, $url_path) ? $url_path : "index";
        $url_path = empty($url_path) ? "index" : $url_path;
        return $this->slug($url_path);
    }

    private function get_param($param = array())
    {
        $url_path = $this->root . $this->path;
        $url_path = str_replace("\\", "/", $url_path);
        $url_path = preg_replace('/(\/+)/', '/', $url_path);
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
        $param["app_function"] = $this->func;
        $param["app_uri"] = $this->uri;
        $param["app_post"] = $_POST;
        $param["app_get"] = $_GET;
        $param["app_cookie"] = $_COOKIE;
        $param["app_session"] = $_SESSION;
        $param["app_files"] = $_FILES;
        $input_raw = file_get_contents("php://input");
        $input_array = (array) json_decode($input_raw, true);
        $param["app_raw"] = is_array($input_array) ? $input_array : $input_raw;
        return init::array_clear(array_filter($param));
    }

    public function get_config($length = null)
    {
        return parse_ini_file(ROOT . '/app.ini');
    }

    private function get_uri()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = stripslashes(trim($uri) . '/');
        $uri = preg_replace('/(\/+)/', '/', $uri);
        return addslashes($uri);
    }

    private function get_root()
    {
        return dirname($_SERVER['PHP_SELF']) . '/';
    }

    private function is_path($path)
    {
        $real = null;
        $re = null;
        foreach ($path as $ff) {
            $re .= DIRECTORY_SEPARATOR . $ff;

            if (!is_dir(CONTROLLER . $re))
                break;
            $real .= DIRECTORY_SEPARATOR  . $ff;
        }
        return ($real) ? $real . DIRECTORY_SEPARATOR : DIRECTORY_SEPARATOR;
    }

    private function uri_get($url_path)
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
}
