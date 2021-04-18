<?php class route extends init
{
    public $user_id;
    public function __construct()
    {
        $this->user_id = isset($_SESSION["user"]["user_id"]) ? $_SESSION["user"]["user_id"] : 0;
        //$this->loginControl();
        //$this->app_run();

        $this->get_file = $this->get_file();
        $this->get_path = $this->get_path();
        $this->get_func = $this->get_function();
        $this->get_param = $this->get_param();

        echo 'Get Path : ' . $this->get_path;
        echo '<hr>';
        echo 'Get File : ' . $this->get_file;
        echo '<hr>';
        echo 'Get Function : ' . $this->get_func;
        echo '<hr>';
        var_dump($this->get_param);
        echo '<hr>';
    }

    public function user()
    {
        return $this->user_id = isset($_SESSION["user"]["user_id"]) ? $_SESSION["user"]["user_id"] : 0;
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

    // Uygulamanın dosyaları ve dosyalara ait fonksiyonları bulunup parametreler işlenerek uygulama çalıştırılıyor
    private function app_run()
    {
        $file = $this->get_file();
        $path = $this->get_path();
        $func = $this->get_function();
        $params = $this->get_param();
        if (file_exists(CONTROLLER . $path . '/' . $file . '.php')) {
            if (file_exists(MODEL . $path . '/' . $file . '.php'))
                require MODEL . $path . '/' . $file . '.php';
            require CONTROLLER . $path . '/' . $file . '.php';
            $nesne = new $file();
            if (method_exists($nesne, $func))
                call_user_func(array($nesne, $func), $params);
            else if (method_exists($nesne, "index"))
                call_user_func(array($nesne, "index"), $params);
            else
                require VIEW . '/not_found_view.php';
        } else {
            require VIEW . '/not_found_view.php';
        }
    }

    // Uygulamanın adres çubuğu ile dosya dizini karşılaştırılıyor

    private function get_path()
    {
        $real = null;
        $re = null;
        $php_self = dirname($_SERVER['PHP_SELF']);
        $php_uri = $_SERVER['REQUEST_URI'];
        $php_real = explode($php_self, $php_uri)[0];
        $path = array_filter(explode('/', $php_real));
        foreach ($path as $ff) {
            $re .= '/' . $ff;
            if (!is_dir(CONTROLLER . $re)) {
                break;
            }
            $real .= '/' . $ff;
        }
        $real = ($real) ? $real : "/";
        $real = is_dir(CONTROLLER . $real) ? $real : "404";
        return $real;
    }

    // Yüklenecek kontroller dosyası bulunuyor
    private function get_file()
    {
        $url_path = explode($this->get_path(), $_SERVER['REQUEST_URI']);
        $url_path = array_filter($url_path);
        $url_path = array_values($url_path);
        $url_path = array_shift($url_path);
        $url_path = explode("/", $url_path);
        $url_path = array_filter($url_path);
        $url_path = array_shift($url_path);
        $url_path = $this->slug($url_path);
        $url_path = $url_path ? $url_path : "index";
        $url_path = file_exists(CONTROLLER . $this->get_path() . '/' . $url_path . '.php') ? $url_path : "index";
        return $url_path;
    }

    // Yüklenen controller dosyasında fonksiyon çağırılıyor
    private function get_function()
    {
        $url_path = $this->get_path() . $this->get_file();
        $url_path = explode($url_path, $_SERVER['REQUEST_URI']);
        $url_path = array_filter($url_path);
        $url_path = array_values($url_path);
        $url_path = array_shift($url_path);
        $url_path = explode("/", $url_path);
        $url_path = array_filter($url_path);
        $url_path = array_shift($url_path);
        $url_path = $url_path ? $url_path : "index";
        return $this->slug($url_path);
    }

    // Yüklenen kontroller dosyasına ve çağırılan fonksiyona tüm php girişleri birleştirilip parametre olarak gönderiliyor
    private function get_param()
    {
        $param = array();
        $url_path = explode('/', $this->get_function());
        $url_path = array_filter($url_path);
        $url_path = array_shift($url_path);
        $url_path = explode($url_path, $_SERVER['REQUEST_URI']);
        if (isset($url_path[1])) {
            $url_path = explode("/", $url_path[1]);
            $url_path = array_filter($url_path);
            foreach ($url_path as $key => $value) {
                $param['get_' . $key] = $value;
                unset($param[$key]);
            }
        }
        if ($this->input())
            $param = array_merge($param, $this->input());
        array_walk_recursive($param, function (&$v) {
            $v = trim($v);
        });
        return $param;
    }

    // Tüm girişler birleştiriliyor
    public function input($param = array())
    {
        $file_get = file_get_contents("php://input");
        $file_get = json_decode($file_get, true);
        if (is_Array($_POST))
            $param["POST"] = array_merge($param, $_POST);
        if (is_Array($_GET))
            $param["GET"] = array_merge($param, $_GET);
        if ($file_get)
            $param["RAW"] = array_merge($param, $file_get);
        return $param;
    }
}
