<?php

class app
{
    private $root;
    private $segments;
    private $controller_class;
    private $method_name;
    private $current_path;
    private $params;
    private $uri;
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
        
        // Parse URL segments başlayarak folder/file/method'u bul
        $this->parse_segments();
        $this->params = $this->get_param();
    }

    private function get_uri()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
        $uri = stripslashes(trim($uri) . '/');
        $uri = preg_replace('/(\/+)/', '/', $uri);
        return $uri;
    }

    private function get_root()
    {
        $url_path = dirname($_SERVER['PHP_SELF']) . '/';
        $url_path = stripslashes(trim($url_path));
        $url_path = preg_replace('/(\/+)/', '/', $url_path);
        return $url_path;
    }

    private function parse_segments()
    {
        // URI'den root'u kaldır
        $remaining = substr($this->uri, strlen($this->root));
        $remaining = trim($remaining, '/');
        
        // Boş string ise root
        if (empty($remaining)) {
            $this->segments = [];
            $this->resolve_controller('', []);
            return;
        }
        
        // URL segments'i al
        $segments = explode('/', $remaining);
        $segments = array_filter($segments);
        $segments = array_values($segments);
        
        // İlk segmenti klasör olarak denetle, sonra dosya ve method
        // segments'i kopyala çünkü resolve_controller içinde array_shift yapılacak
        $remaining_segments = $segments;
        $this->resolve_controller('', $remaining_segments);
    }

    private function resolve_controller($current_path, $remaining_segments)
    {
        // Eğer segment kalmadıysa veya 0. indexi olmadıysa
        if (empty($remaining_segments)) {
            // Dosya bulunamadı, index.php kullan
            $this->load_controller($current_path, 'index', []);
            return;
        }
        
        $segment = array_shift($remaining_segments);
        $segment = init::slug($segment);
        
        // Segment adını validate et
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $segment)) {
            http_response_code(404);
            require_once CORE . SEP . "error.php";
            exit;
        }
        
        // Path'i düzgun şekilde oluştur
        $next_path = empty($current_path) ? SEP . $segment : $current_path . SEP . $segment;
        
        $folder_path = CONTROLLER . $next_path;
        $file_path = CONTROLLER . $next_path . '.php';
        
        // 1. Klasör mü diye kontrol et
        if (is_dir($folder_path)) {
            // Klasör bulundu, klasör içinde devam et
            $this->resolve_controller($next_path, $remaining_segments);
            return;
        }
        
        // 2. Dosya mı diye kontrol et
        if (file_exists($file_path)) {
            // Dosya bulundu, bu dosyada method'u ara
            $this->load_controller($current_path, $segment, $remaining_segments);
            return;
        }
        
        // 3. Ne klasör ne dosya, önceki klasöre index.php'de method ara
        if ($current_path === '') {
            // Root'ta index.php'de segment adını method olarak dene
            $this->load_controller('', 'index', [$segment, ...$remaining_segments]);
            return;
        }
        
        // current_path'te index.php'de segment adını method olarak dene
        $this->load_controller($current_path, 'index', [$segment, ...$remaining_segments]);
    }

    private function load_controller($folder_path, $file_name, $remaining_segments)
    {
        // Dosya yolu kontrol et
        $path = empty($folder_path) ? '' : $folder_path;
        $controller_file = CONTROLLER . $path . SEP . $file_name . '.php';
        
        // Current path'i sakla
        $this->current_path = $folder_path;
        
        if (!file_exists($controller_file)) {
            // Eğer index değilse, index.php'ye geri dön
            if ($file_name !== 'index') {
                $this->load_controller($folder_path, 'index', [$file_name, ...$remaining_segments]);
                return;
            }
            
            // index.php bile yok, 404
            http_response_code(404);
            require_once CORE . SEP . "error.php";
            exit;
        }
        
        // Dosya class adını belirle (file name = class name)
        $this->controller_class = $file_name;
        
        // require dosya
        require_once $controller_file;
        
        // Sınıf var mı kontrol et
        if (!class_exists($this->controller_class)) {
            http_response_code(404);
            require_once CORE . SEP . "error.php";
            exit;
        }
        
        // Method adını belirle
        $method = !empty($remaining_segments) ? array_shift($remaining_segments) : 'index';
        $method = init::slug($method);
        
        // Method adı validate et
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $method)) {
            $method = 'index';
        }
        
        // Sınıfı instantiate et ve method kontrolü
        $instance = new $this->controller_class([]);
        
        if (!method_exists($instance, $method)) {
            $method = 'index';
            
            // index method bile yok mu?
            if (!method_exists($instance, 'index')) {
                http_response_code(404);
                require_once CORE . SEP . "error.php";
                exit;
            }
        }
        
        $this->method_name = $method;
        // Kalan segments parametreler olarak sakla
        $this->segments = $remaining_segments;
    }

    private function get_param($param = [])
    {
        // URI parametreleri (segments)
        $uri_params = [];
        foreach ($this->segments as $key => $value) {
            $uri_params['uri_' . $key] = $value;
        }
        
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

    private function get_current_path()
    {
        return empty($this->current_path) ? '/' : $this->current_path . '/';
    }

    private function input()
    {
        return [
            "app" => [
                "root" => $this->root,
                "controller_class" => $this->controller_class,
                "method" => $this->method_name,
                "uri" => $this->uri,
                "folder" => $this->get_current_path(),
                "file" => $this->controller_class,
                "method" => $this->method_name,
                "post" => $_POST,
                "get" => $_GET,
                "cookie" => $_COOKIE,
                "session" => $_SESSION ?? [],
                "files" => $_FILES,
                "raw" => $this->get_input_raw(),
                "request_method" => $_SERVER['REQUEST_METHOD'] ?? 'GET',
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
        if ($this->controller_class && class_exists($this->controller_class)) {
            try {
                $instance = new $this->controller_class($this->params);
                if ($this->method_name && method_exists($instance, $this->method_name)) {
                    call_user_func([$instance, $this->method_name], (array) $this->params);
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
