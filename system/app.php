<?php class app extends init
{

    public function __construct($config)
    {
        $this->app_run($config);

    }

    private function app_run($config)
    {
        $path = $this->get_path();
        $file = $this->get_file();
        $func = $this->get_function();
        $params = $this->get_param($config);

        spl_autoload_register(function ($className) use ($path) {
            if (file_exists(CONTROLLER . $path . $className . ".php"))
                require_once CONTROLLER . $path . $className . ".php";
            $className = str_replace("_Model", "", $className);
            if (file_exists(MODEL . $path . $className . ".php"))
                require_once MODEL . $path . $className . ".php";
        });

        if (class_exists($file)) {
            $nesne = new $file((array) $this);
            if (method_exists($nesne, $func))
                call_user_func(array($nesne, $func), (array) $params);
            else if (method_exists($nesne, "index"))
                call_user_func(array($nesne, "index"), (array) $params);
            else {
                header("HTTP/1.0 404 Not Found");
                die($config["error_404"]);
            }
        } else {
            header("HTTP/1.0 404 Not Found");
            die($config["error_404"]);
        }
    }

    

    private function uri()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = stripslashes(trim($uri));
        $uri = str_replace("//", "/", $uri);
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
        return ($real) ? $real .DIRECTORY_SEPARATOR:DIRECTORY_SEPARATOR;
    }

    private function get_path()
    {
        $php_self = dirname($_SERVER['PHP_SELF']);
        $php_self = str_replace("\\", "/", $php_self);
        $php_real = explode($php_self, $this->uri());
        $php_real = array_filter($php_real);
        $php_real = array_values($php_real);
        $php_real = $this->is_path($php_real);
        $php_real = str_replace("\\", "/", $php_real);
        $php_real = str_replace("//", "/", $php_real);
        return $php_real;
    }

    private function get_file()
    {
        $url_path = $this->get_path();
        $url_path = explode($url_path, $this->uri());
        $url_path = array_filter($url_path);
        $url_path = array_shift($url_path);
        $url_path = explode("/", $url_path);
        $file = array_filter($url_path);
        $file = array_shift($file);
        $file = $this->slug($file);
        $file = file_exists(CONTROLLER . $this->get_path() . $file . '.php') ? $file : 'index';
        return $file;
    }

    private function get_function()
    {
        $url_path = $this->get_path();
        if ($this->get_file() != "index")
            $url_path .= $this->get_file();
        $url_path = explode($url_path, $this->uri());
        $url_path = array_filter($url_path);
        $url_path = array_shift($url_path);
        $url_path = explode("/", $url_path);
        $url_path = array_filter($url_path);
        $url_path = array_shift($url_path);
        $path = $this->get_path();
        $file = $this->get_file();
        spl_autoload_register(function ($className) use ($path) {
            if (file_exists(CONTROLLER . $path . $className . ".php"))
                require_once CONTROLLER . $path . $className . ".php";
            $className = str_replace("_Model", "", $className);
            if (file_exists(MODEL . $path . $className . ".php"))
                require_once MODEL . $path . $className . ".php";
        });
        if (class_exists($file)) {
            $nesne = new $file((array) $this);
            $url_path = method_exists($nesne, $url_path) ? $url_path : "index";
        }
        return $this->slug($url_path);
    }

    private function get_param($param = array())
    {
        $url_path = $this->get_path();
        if ($this->get_file() != "index")
            $url_path .= $this->get_file();
        if ($this->get_function() != "index")
            $url_path .= $this->get_function();

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
                $param['app_uri_' . $key] = $value;
                unset($param[$key]);
            }
 
        }
        
        return $param;
    }

    private function input($param = array())
    {
        $param["app_post"] = $_POST;
        $param["app_get"] = $_GET;
        $param["app_cookie"] = $_COOKIE;
        $param["app_session"] = $_SESSION;
        $param["app_file"] = $this->get_file();
        $param["app_path"] = $this->get_path();
        $param["app_function"] = $this->get_function();
        $param["app_uri"] = $_SERVER["REQUEST_URI"];
        $file_get = file_get_contents("php://input");
        $param["app_raw"] = (array) json_decode($file_get, true);
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
