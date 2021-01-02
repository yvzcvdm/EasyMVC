<?php class app extends init
{

    public function __construct()
    {
        $this->app_run();
    }

    // Uygulamanın dosyları ve dosyalara ait fonksiyonları bulunup parametreler işlenerek uygulama çalıştırılıyor
    private function app_run()
    {
        $file = $this->get_file();
        $func = $this->run_function();
        $params = $this->params();
        if (file_exists(CONTROLLER . '/' . $file . '.php')) {
            if (file_exists(MODEL . '/' . $file . '.php'))
                require MODEL . '/' . $file . '.php';
            require CONTROLLER . '/' . $file . '.php';
            $nesne = new $file();
            if (method_exists($nesne, $func))
                call_user_func(array($nesne, $func), $params);
            else if (method_exists($nesne, "index"))
                call_user_func(array($nesne, "index"), $params);
            else
                require SYSTEM . '/404.php';
        } else {
            require SYSTEM . '/404.php';
        }
    }

    // Uygulamanın adres çubuğu ile dosya dizini karşılaştırılıyor
    private function path()
    {
        $php_self = dirname($_SERVER['PHP_SELF']);
        $php_uri = $_SERVER['REQUEST_URI'];
        return explode($php_self, $php_uri)[0];
    }

    // Tüm girişler birleştiriliyor
    public function input($param = array())
    {
        $file_get = file_get_contents("php://input");
        $file_get = json_decode($file_get, true);
        if (is_Array($_POST))
            $param = array_merge($param, $_POST);
        if (is_Array($_GET))
            $param = array_merge($param, $_GET);
        if ($file_get)
            $param = array_merge($param, $file_get);
        return $param;
    }

    // Yüklenecek kontroller bulunuyor
    private function get_file()
    {
        $url_path = explode('/', $this->path());
        $url_path = array_filter($url_path);
        if (isset($url_path[1])) {
            return $this->slug($url_path[1]);
        }
    }

    // Yüklenen controller dosyasında fonksiyon çağırılıyor
    private function run_function()
    {
        $url_path = explode('/', $this->path());
        $url_path = array_filter($url_path);
        if (isset($url_path[2])) {
            return $this->slug($url_path[2]);
        }
    }

    // Yüklenen kontroller dosyasına ve çağırılan fonksiyona tüm php girişleri birleştirilip parametre olarak gönderiliyor
    private function params()
    {
        $param = array();
        $url_path = explode('/', $this->path());
        if (isset($url_path[2]) && !empty($url_path[2])) {
            $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
            $param = explode($url_path[2], $uri_parts[0]);
            $param = explode('/', $param[1]);
            $param = array_filter($param);
            foreach ($param as $key => $value) {
                $param['param_' . $key] = $param[$key];
                unset($param[$key]);
            }
        }
        if ($this->input())
            $param = array_merge($param, $this->input());
        return $param;
    }

    private function view()
    {
  
        return "view";
    }
}
