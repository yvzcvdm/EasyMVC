<?php

class app extends view
{
    private $root;
    private $path;
    private $file;
    private $func;
    private $params;
    private $uri;
    private $method;
    private $error;
    private static $autoload_registered = false;
    public $config;

    public function __construct()
    {
        $this->error = true;
        $this->config = $this->get_config();
        $this->uri = $this->get_uri();
        $this->root = $this->get_root();
        $this->path = $this->get_path();
        $this->file = $this->get_file();
        $this->func = $this->get_function();
        $this->params = $this->get_param();
        $this->register_autoload();
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
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
        $uri = stripslashes(trim($uri) . '/');
        $uri = preg_replace('/(\/+)/', '/', $uri);
        return $uri;
    }

    private function get_path()
    {
        $url_path = explode($this->root, $this->uri ?? '');
        $url_path = array_filter($url_path);
        $url_path = array_values($url_path);
        
        if (count($url_path) < 2) {
            $url_path = array_shift($url_path) ?? '';
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
        $parts = explode($url_path, $this->uri ?? '');
        $parts = array_filter($parts);
        $file = array_shift($parts) ?? '';
        $file = explode("/", $file);
        $file = array_filter($file);
        $file = array_shift($file) ?? 'index';
        $file = $this->slug($file);
        
        // Validate filename format
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $file)) {
            return 'index';
        }
        
        $controller_file = CONTROLLER . $this->path . $file . '.php';
        return file_exists($controller_file) ? $file : 'index';
    }

    private function get_function()
    {
        $url_path = $this->root . $this->path;
        $url_path = str_replace("\\", "/", $url_path);
        $url_path = preg_replace('/(\/+)/', '/', $url_path);
        
        if ($this->file != "index") {
            $url_path .= $this->file;
        }
        
        $parts = explode($url_path, $this->uri);
        $parts = array_filter($parts);
        $func = array_shift($parts) ?? '';
        $func = explode("/", $func);
        $func = array_filter($func);
        $func = array_shift($func) ?? 'index';
        
        // Validate function name format
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $func)) {
            $func = 'index';
        }
        
        if ($this->file && class_exists($this->file)) {
            $this->method = new $this->file($this);
            $func = (method_exists($this->method, $func)) ? $func : "index";
        }
        
        return $this->slug($func);
    }

    private function get_param($param = [])
    {
        $url_path = $this->path;
        
        if ($this->file != "index") {
            $url_path .= $this->file . '/';
        }
        if ($this->func != "index") {
            $url_path .= $this->func . '/';
        }
        
        $parts = explode($url_path, $this->uri . '/');
        $parts = array_filter($parts);
        
        if (count($parts) < 2) {
            $parts = array_shift($parts) ?? '';
            $parts = '/' . $parts;
            $parts = explode("/", $parts);
            $parts = array_filter($parts);
        }
        
        $param = array_merge($param, $this->uri_get($parts));
        $param = array_merge($param, $this->input());
        
        return $this->array_clear($param);
    }

    private function input()
    {
        return [
            "app" => [
                "root" => $this->root,
                "path" => $this->path,
                "file" => $this->file,
                "function" => $this->func,
                "uri" => $this->uri,
                "post" => $_POST,
                "get" => $_GET,
                "cookie" => $_COOKIE,
                "session" => $_SESSION ?? [],
                "files" => $_FILES,
                "raw" => $this->get_input_raw()
            ]
        ];
    }

    public function get_input_raw()
    {
        $input_raw = file_get_contents("php://input");
        if ($input_raw === false) {
            return [];
        }
        
        $input_array = json_decode($input_raw, true);
        return is_array($input_array) ? $input_array : $input_raw;
    }

    public function get_config()
    {
        $config_file = ROOT . SEP . 'app.ini';
        if (!is_file($config_file)) {
            return false;
        }
        
        $data = parse_ini_file($config_file);
        return is_array($data) ? $data : false;
    }

    private function is_path($path = null)
    {
        $real = '';
        $re = '';
        
        if (!is_array($path)) {
            return SEP;
        }
        
        foreach ($path as $ff) {
            if (empty($ff)) {
                continue;
            }
            
            $re .= SEP . $ff;
            
            if (!is_dir(CONTROLLER . $re)) {
                break;
            }
            
            $real .= SEP . $ff;
        }
        
        return !empty($real) ? $real . SEP : SEP;
    }

    private function uri_get($url_path = null)
    {
        $param = [];
        
        if (isset($url_path) && is_array($url_path)) {
            foreach ($url_path as $key => $value) {
                $param['uri_' . $key] = $value;
            }
        }
        
        return $param;
    }

    private function get_model($file_name = null, $dir = null)
    {
        $dir = $dir ?: MODEL;
        
        if (!is_dir($dir) || empty($file_name)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        
        if (!is_array($files)) {
            return false;
        }

        foreach ($files as $value) {
            $full_path = $dir . SEP . $value;
            
            if (is_file($full_path)) {
                $pathinfo = pathinfo($full_path);
                if (($pathinfo['extension'] ?? null) === 'php' && $file_name === $pathinfo['filename']) {
                    return $full_path;
                }
            } elseif (is_dir($full_path)) {
                $file = $this->get_model($file_name, $full_path);
                if ($file) {
                    return $file;
                }
            }
        }
        
        return false;
    }

    private function register_autoload()
    {
        if (self::$autoload_registered) {
            return;
        }

        $path = $this->path;
        spl_autoload_register(function ($className) use ($path) {
            // Validate class name format
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $className)) {
                return;
            }
            
            // Load Controller
            $controller_file = CONTROLLER . $path . $className . ".php";
            if (is_file($controller_file)) {
                require_once $controller_file;
                return;
            }

            // Load Model
            $model_name = str_replace("_Model", "", $className);
            $model_file = $this->get_model($model_name);
            if ($model_file && is_file($model_file)) {
                require_once $model_file;
            }
        });

        self::$autoload_registered = true;
    }
    
    public function __destruct()
    {
        if ($this->file && class_exists($this->file)) {
            try {
                $this->method = new $this->file($this->params);
                if ($this->func && method_exists($this->method, $this->func)) {
                    call_user_func([$this->method, $this->func], (array) $this->params);
                    $this->error = false;
                }
            } catch (Exception $e) {
                error_log("Error in controller: " . $e->getMessage());
                $this->error = true;
            }
        }

        if ($this->error) {
            require_once CORE . SEP . "error.php";
        }
    }
}
