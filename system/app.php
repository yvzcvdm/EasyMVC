<?php class app extends init
{

    public function __construct()
    {
        //$this->loginControl();
        $this->app_run();
        echo '<hr>';
        echo 'File : ' . $this->get_file() . '<hr>';
        echo 'Path : ' . $this->get_path() . '<hr>';
        echo 'Func : ' . $this->get_function() . '<hr>';
        echo '<pre>';
        echo var_dump($this->get_param());
        echo '</pre>';

    }

    public function loginControl()
    {
        if (strpos($_SERVER['REQUEST_URI'], "login") && isset($_SESSION["user"]))
            header('Location: /dash');
        else if ($_SERVER['REQUEST_URI'] == "/" && isset($_SESSION["user"]))
            header('Location: /dash');
        else if (!strpos($_SERVER['REQUEST_URI'], "login") && !isset($_SESSION["user"]))
            header('Location: /login');
    }

    private function app_run()
    {
        $file = $this->get_file();
        $path = $this->get_path();
        $func = $this->get_function();
        $params = $this->get_param();

        spl_autoload_register(function ($className) use ($path) {
            if (file_exists(CONTROLLER . $path . $className . ".php"))
                require_once CONTROLLER . $path . $className . ".php";
            $className = str_replace("_Model","", $className);
            if (file_exists(MODEL . $path . $className . ".php"))
                require_once MODEL . $path . $className . ".php";
        });
   
        if (class_exists($file)) {
            $nesne = new $file((array) $this);
            if (method_exists($nesne, $func))
                call_user_func(array($nesne, $func), (array) $params);
            else if (method_exists($nesne, "index"))
                call_user_func(array($nesne, "index"), (array) $params);
            else
                die("Kullanılmayan controller");
        }
    }

    private function uri()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = stripslashes(trim($uri));
        return addslashes($uri);
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

    private function get_path()
    {
        $php_self = dirname($_SERVER['PHP_SELF']);
        $php_real = explode($php_self, $this->uri());
        $php_real = array_filter($php_real);
        $php_real = array_shift($php_real);
        $php_real = explode('/', $php_real);
        $php_real = array_filter($php_real);
        return $this->is_path($php_real);
    }

    private function get_file()
    {
        $url_path = str_replace("\\", "/", $this->get_path());
        $url_path = explode($url_path, $this->uri());
        $url_path = array_filter($url_path);
        $url_path = array_shift($url_path);
        $url_path = explode("/", $url_path);
        $file = array_filter($url_path);
        $file = array_shift($file);
        $file = $this->slug($file);
        $file = file_exists(CONTROLLER . $this->get_path() . $file . '.php') ? $file : 'index';
        $file = file_exists(CONTROLLER . $this->get_path() . $file . '.php') ? $file : 'not_found';
        return $file;
    }

    private function get_function()
    {
        $url_path = str_replace("\\", "/", $this->get_path().$this->get_file());
        $url_path = explode($url_path, $this->uri());
        $url_path = array_filter($url_path);
        $url_path = array_shift($url_path);
        $url_path = explode("/", $url_path);
        $url_path = array_filter($url_path);
        $url_path = array_shift($url_path);
        $file = $this->get_file();
        if (class_exists($file)) {
            $nesne = new $file((array) $this);
            $url_path = method_exists($nesne, $url_path) ? $url_path : "index";
        }
        return $this->slug($url_path);
    }

    private function get_param($param = array())
    {
        if($this->get_function() == "index")
            $url_path = $this->get_path().$this->get_file();
        else
            $url_path = $this->get_path().$this->get_file().'/'.$this->get_function();
        $url_path = str_replace("\\", "/", $url_path);
        $url_path = explode($url_path, $this->uri());
        $url_path = array_filter($url_path);
        $url_path = array_shift($url_path);
        $param = array_merge($param, $this->uri_get($url_path));
        $param = array_merge($param, $this->input());
        return $this->array_clear($param);
    }

    private function uri_get($url_path)
    {
        $param = array();
        if (isset($url_path)) {
            $url_path = explode("/", $url_path);
            $url_path = array_filter($url_path);
            foreach ($url_path as $key => $value) {
                $param['get_' . $key] = $value;
                unset($param[$key]);
            }
        }
        return $param;
    }

    private function input($param = array())
    {
        $param["POST"] = $_POST;
        $param["GET"] = $_GET;
        $param["COOKIE"] = $_COOKIE;
        $param["SESSION"] = $_SESSION;
        $param["URI"] = $_SERVER["REQUEST_URI"];
        $file_get = file_get_contents("php://input");
        $param["RAW"] = (array) json_decode($file_get, true);
        return array_filter($param);
    }

    private function array_clear($array)
    {
        array_walk_recursive($array, function (&$item) {
            $item = addslashes(stripslashes(trim($item)));
        });
        return $array;
    }
}
