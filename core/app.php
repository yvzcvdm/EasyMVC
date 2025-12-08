<?php

class app
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
    
    // Statik config cache
    private static $config_cache = null;

    public function __construct()
    {
        $this->error = true;
        $this->config = self::get_config();
        $this->uri = $this->get_uri();
        $this->root = $this->get_root();
        $this->path = $this->get_path();
        $this->file = $this->get_file();
        $this->func = $this->get_function();
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
        $file = init::slug($file);
        
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
        
        return init::slug($func);
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
        
        // Parametreleri al
        $uri_params = $this->uri_get($parts);
        
        // Her parametreyi numeric kontrol et
        foreach ($uri_params as $key => $value) {
            if (!ctype_digit($value)) {
                // Non-numeric parametre bulundu, 404 döndür
                http_response_code(404);
                require_once CORE . SEP . "error.php";
                exit;
            }
        }
        
        $param = array_merge($param, $uri_params);
        $param = array_merge($param, $this->input());
        
        return init::array_clear($param);
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
                "raw" => $this->get_input_raw(),
                "method" => $_SERVER['REQUEST_METHOD'] ?? 'GET',
                "ip" => $this->get_client_ip(),
                "host" => $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost',
                "user_agent" => $_SERVER['HTTP_USER_AGENT'] ?? '',
                "referer" => $_SERVER['HTTP_REFERER'] ?? '',
                "https" => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? true : false,
                "query_string" => $_SERVER['QUERY_STRING'] ?? '',
                "content_type" => $_SERVER['CONTENT_TYPE'] ?? '',
                "content_length" => $_SERVER['CONTENT_LENGTH'] ?? 0,
                "request_time" => $_SERVER['REQUEST_TIME'] ?? time(),
                "microtime" => microtime(true),
                "port" => $_SERVER['SERVER_PORT'] ?? 80,
                "protocol" => $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1',
                "accept" => $_SERVER['HTTP_ACCEPT'] ?? '',
                "language" => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
                "authorization" => $this->get_auth_header(),
                "is_mobile" => $this->is_mobile()
            ]
        ];
    }

    private function get_client_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        
        return filter_var(trim($ip), FILTER_VALIDATE_IP) ?: '0.0.0.0';
    }

    private function get_auth_header()
    {
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            return $_SERVER['HTTP_AUTHORIZATION'];
        }
        
        if (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            return $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        
        return '';
    }

    private function is_mobile()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $mobile_patterns = [
            'Mobile',
            'Android',
            'iPhone',
            'iPad',
            'Windows Phone',
            'BlackBerry',
            'Opera Mini',
            'IEMobile'
        ];
        
        foreach ($mobile_patterns as $pattern) {
            if (stripos($user_agent, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
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

    public static function get_config()
    {
        // Eğer cache'de varsa, cache'den dön
        if (self::$config_cache !== null) {
            return self::$config_cache;
        }
        
        $config_file = ROOT . SEP . 'app.ini';
        if (!is_file($config_file)) {
            self::$config_cache = false;
            return false;
        }
        
        $data = parse_ini_file($config_file);
        self::$config_cache = is_array($data) ? $data : false;
        
        return self::$config_cache;
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
