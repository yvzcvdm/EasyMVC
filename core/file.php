<?php
/**
 * File Class
 * Dosya yükleme, silme, yeniden adlandırma ve yönetimi işlemlerini gerçekleştiren sınıf
 */
class File
{
    /**
     * Yükleme konfigürasyonu
     */
    private $config = array();
    
    /**
     * Yükleme sonuçları (başarılı ve başarısız)
     */
    private $items = array();

    /**
     * Constructor - Konfigürasyonu yükler
     */
    public function __construct()
    {
        $this->loadConfig();
    }

    /**
     * app.ini dosyasından yükleme konfigürasyonunu yükle
     */
    private function loadConfig()
    {
        $config = app::get_config();
        if ($config && isset($config['upload'])) {
            $this->config = $config['upload'];
        } else {
            $this->setDefaultConfig();
        }
    }

    /**
     * Varsayılan konfigürasyonu ayarla
     */
    private function setDefaultConfig()
    {
        $this->config = array(
            'max_file_size' => 5242880, // 5MB
            'allowed_extensions' => 'jpg,jpeg,png,gif,pdf,zip,rar,doc,docx',
            'upload_path' => '/public/uploads/',
            'filename_format' => 'timestamp'
        );
    }

    /**
     * İzin verilen uzantıları al
     */
    private function getAllowedExtensions()
    {
        $extensions = isset($this->config['allowed_extensions']) ? $this->config['allowed_extensions'] : '';
        return array_filter(array_map('trim', explode(',', strtolower($extensions))));
    }

    /**
     * Maksimum dosya boyutunu al (byte cinsinden)
     */
    private function getMaxFileSize()
    {
        return isset($this->config['max_file_size']) ? (int)$this->config['max_file_size'] : 5242880;
    }

    /**
     * Türkçe karakterleri Latin harflere dönüştür
     */
    private function slugify($text)
    {
        // Türkçe karakterler
        $turkish = array('ç', 'Ç', 'ğ', 'Ğ', 'ı', 'İ', 'ö', 'Ö', 'ş', 'Ş', 'ü', 'Ü');
        $latin = array('c', 'c', 'g', 'g', 'i', 'i', 'o', 'o', 's', 's', 'u', 'u');
        
        $text = str_replace($turkish, $latin, $text);
        
        // Boşlukları alt tire yap
        $text = preg_replace('/\s+/', '_', $text);
        
        // Sadece alfanumerik, alt tire ve tire bırak
        $text = preg_replace('/[^a-zA-Z0-9_-]/', '', $text);
        
        // Birden fazla alt tire veya tire varsa tekleştir
        $text = preg_replace('/([_-])\1+/', '$1', $text);
        
        // Başında/sonunda alt tire varsa kaldır
        $text = trim($text, '_-');
        
        return strtolower($text);
    }

    /**
     * Dosya adı oluştur (security ve uniqueness için)
     */
    private function generateFileName($original_name, $format = 'timestamp')
    {
        $path_info = pathinfo($original_name);
        $extension = strtolower($path_info['extension']);
        $base_name = $this->slugify($path_info['filename']);

        switch ($format) {
            case 'random':
                return bin2hex(random_bytes(16)) . '.' . $extension;
            case 'original':
                return $base_name . '.' . $extension;
            case 'timestamp':
            default:
                return $base_name . '-' . time() . '-' . mt_rand(1000, 9999) . '.' . $extension;
        }
    }

    /**
     * Dosya validasyonu
     */
    private function validateFile($file_name, $file_type, $file_size, $tmp_path)
    {
        $errors = array();
        $allowed_extensions = $this->getAllowedExtensions();
        $max_size = $this->getMaxFileSize();

        // Dosya adı kontrolü
        if (empty($file_name)) {
            $errors[] = 'Dosya adı boş olamaz';
            return $errors;
        }

        // Boyut kontrolü
        if ($file_size > $max_size) {
            $errors[] = 'Dosya boyutu ' . $this->formatBytes($max_size) . ' değerini aşıyor. Yüklenen: ' . $this->formatBytes($file_size);
        }

        // Uzantı kontrolü
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!empty($allowed_extensions) && !in_array($extension, $allowed_extensions)) {
            $errors[] = 'Dosya uzantısı izin verilmiyor: .' . $extension;
        }

        // Çalıştırılabilir dosya kontrolü
        if ($this->isExecutableFile($file_name, $tmp_path)) {
            $errors[] = 'Çalıştırılabilir dosyalar yüklenemiyor';
        }

        return $errors;
    }

    /**
     * Dosyanın çalıştırılabilir olup olmadığını kontrol et
     */
    private function isExecutableFile($filename, $tmp_path)
    {
        $dangerous_extensions = array('php', 'php3', 'php4', 'php5', 'phtml', 'pht', 'exe', 'sh', 'bat', 'com', 'vbs');
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($extension, $dangerous_extensions)) {
            return true;
        }

        // PHP içeriği taraması
        if ($extension === 'pdf' || $extension === 'txt') {
            $content = file_get_contents($tmp_path, false, null, 0, 512);
            if (strpos($content, '<?php') !== false || strpos($content, '<? ') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Dizin varlığını kontrol et ve oluştur
     */
    private function ensureDirectory($dir)
    {
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0755, true)) {
                return false;
            }
        }
        return true;
    }

    /**
     * PHP dosya yükleme hatasını Türkçe açıklamasına dönüştür
     */
    private function getUploadErrorMessage($error_code)
    {
        $error_messages = array(
            UPLOAD_ERR_OK => 'Dosya başarıyla yüklendi',
            UPLOAD_ERR_INI_SIZE => 'Dosya php.ini\'de belirtilen upload_max_filesize boyutunu aşıyor',
            UPLOAD_ERR_FORM_SIZE => 'Dosya HTML form\'da belirtilen MAX_FILE_SIZE boyutunu aşıyor',
            UPLOAD_ERR_PARTIAL => 'Dosya kısmen yüklendi',
            UPLOAD_ERR_NO_FILE => 'Dosya yüklenmedi',
            UPLOAD_ERR_NO_TMP_DIR => 'Geçici dosya dizini eksik',
            UPLOAD_ERR_CANT_WRITE => 'Dosya diske yazılamadı',
            UPLOAD_ERR_EXTENSION => 'Dosya uzantısı tarafından yükleme durduruldu'
        );
        
        return isset($error_messages[$error_code]) ? $error_messages[$error_code] : 'Bilinmeyen hata';
    }

    /**
     * Byte formatını insanlar tarafından okunabilir forma dönüştür
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB');
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Tek veya çoklu dosya yükle
     * 
     * @param string $form_input_name - HTML form input name attribute
     * @param string $upload_dir - Yükleme dizini (ROOT'a göre)
     * @param array $specific_config - Belirli yükleme için özel konfigürasyon (opsiyonel)
     * @return array - Yüklenen dosyaların yolu veya hata mesajları
     */
    public function upload($form_input_name, $upload_dir = null, $specific_config = array())
    {
        $this->items = array();

        if ($upload_dir === null) {
            $upload_dir = isset($this->config['upload_path']) ? $this->config['upload_path'] : '/public/uploads/';
        }

        // Özel konfigürasyon varsa geçici olarak değiştir
        if (!empty($specific_config)) {
            $backup_config = $this->config;
            $this->config = array_merge($this->config, $specific_config);
        }

        $full_dir = ROOT . $upload_dir;
        
        // Dizinin var olup olmadığını kontrol et
        if (!$this->ensureDirectory($full_dir)) {
            $this->items[] = array(
                'status' => 'error',
                'message' => 'Yükleme dizini oluşturulamadı: ' . $upload_dir
            );
            if (!empty($specific_config)) {
                $this->config = $backup_config;
            }
            return $this->getResponse();
        }

        // $_FILES kontrol et - form_input_name'deki [] karakterlerini kaldır
        $files_key = str_replace('[]', '', $form_input_name);
        
        if (!isset($_FILES[$files_key])) {
            $this->items[] = array(
                'status' => 'error',
                'message' => 'Dosya bulunamadı'
            );
            if (!empty($specific_config)) {
                $this->config = $backup_config;
            }
            return $this->getResponse();
        }

        $files = $_FILES[$files_key];

        // $_FILES yapısını normalize et (çoklu dosya için array'e dönüştür)
        // HTML multiple: ["name"] => string (tek dosya için) veya ["name"] => array (çoklu)
        if (!is_array($files['name']) || (is_array($files['name']) && !isset($files['name'][0]))) {
            // Tek dosya - array yapısına dönüştür
            $files = array(
                'name'      => array($files['name']),
                'tmp_name'  => array($files['tmp_name']),
                'type'      => array($files['type']),
                'size'      => array($files['size']),
                'error'     => array($files['error'] ?? 0)
            );
        }

        // Tüm dosyaları işle
        $file_count = count($files['name']);
        for ($i = 0; $i < $file_count; $i++) {
            // PHP upload hata kodlarını kontrol et
            $error_code = isset($files['error'][$i]) ? $files['error'][$i] : UPLOAD_ERR_NO_FILE;
            
            if ($error_code !== UPLOAD_ERR_OK) {
                $this->items[] = array(
                    'file' => $files['name'][$i],
                    'status' => 'error',
                    'message' => $this->getUploadErrorMessage($error_code)
                );
                continue;
            }
            
            if (!empty($files['name'][$i])) {
                $this->processFile(
                    $files['name'][$i],
                    $files['tmp_name'][$i],
                    $files['type'][$i],
                    $files['size'][$i],
                    $full_dir,
                    $upload_dir
                );
            }
        }

        // Özel konfigürasyon varsa geri yükle
        if (!empty($specific_config)) {
            $this->config = $backup_config;
        }

        return $this->getResponse();
    }

    /**
     * Tekil dosyayı işle
     */
    private function processFile($file_name, $tmp_path, $file_type, $file_size, $full_dir, $upload_dir)
    {
        // Validasyon
        $validation_errors = $this->validateFile($file_name, $file_type, $file_size, $tmp_path);
        if (!empty($validation_errors)) {
            foreach ($validation_errors as $error) {
                $this->items[] = array(
                    'file' => $file_name,
                    'status' => 'error',
                    'message' => $error
                );
            }
            return;
        }

        // Dosya adı oluştur
        $filename_format = isset($this->config['filename_format']) ? $this->config['filename_format'] : 'timestamp';
        $new_filename = $this->generateFileName($file_name, $filename_format);
        $destination = $full_dir . $new_filename;

        // Dosyayı taşı
        if (move_uploaded_file($tmp_path, $destination)) {
            // İzinleri ayarla
            @chmod($destination, 0644);
            $this->items[] = array(
                'file' => $file_name,
                'status' => 'success',
                'path' => $upload_dir . $new_filename
            );
        } else {
            $this->items[] = array(
                'file' => $file_name,
                'status' => 'error',
                'message' => 'Dosya taşıma başarısız'
            );
        }
    }

    /**
     * Yanıtı hazırla
     */
    private function getResponse()
    {
        $errors = array_filter($this->items, function($item) {
            return $item['status'] === 'error';
        });
        
        return array(
            'success' => empty($errors),
            'items' => $this->items,
            'uploads' => array_filter($this->items, function($item) {
                return $item['status'] === 'success';
            }),
            'errors' => array_filter($this->items, function($item) {
                return $item['status'] === 'error';
            })
        );
    }

    /**
     * Hata mesajlarını al
     */
    public function getErrors()
    {
        return array_filter($this->items, function($item) {
            return $item['status'] === 'error';
        });
    }

    /**
     * Yüklenen dosyaları al
     */
    public function getUploads()
    {
        return array_filter($this->items, function($item) {
            return $item['status'] === 'success';
        });
    }

    /**
     * Dosya silme işlemi
     * 
     * @param string $file_path - Silinecek dosya yolu (ROOT'a göre veya tam yol)
     * @return bool - Başarılı olup olmadığı
     */
    public function delete($file_path)
    {
        // Güvenlik: ROOT dizininin üstüne çıkılmasını engelle
        $real_path = realpath(ROOT . $file_path);
        $root_path = realpath(ROOT);
        
        if ($real_path === false || strpos($real_path, $root_path) !== 0) {
            return false;
        }

        if (file_exists($real_path) && is_file($real_path)) {
            return @unlink($real_path);
        }

        return false;
    }

    /**
     * Dosya adı değiştir
     * 
     * @param string $old_path - Eski dosya yolu (ROOT'a göre)
     * @param string $new_name - Yeni dosya adı (uzantı olmadan veya uzantı ile)
     * @return bool - Başarılı olup olmadığı
     */
    public function rename($old_path, $new_name)
    {
        // Güvenlik: ROOT dizininin üstüne çıkılmasını engelle
        $real_old_path = realpath(ROOT . $old_path);
        $root_path = realpath(ROOT);
        
        if ($real_old_path === false || strpos($real_old_path, $root_path) !== 0) {
            return false;
        }

        if (!file_exists($real_old_path) || !is_file($real_old_path)) {
            return false;
        }

        // Dosya adını slug formatına dönüştür (Türkçe -> Latin, boşluk -> alt_tire)
        $new_name = $this->slugify($new_name);
        
        $directory = dirname($real_old_path);
        $extension = pathinfo($real_old_path, PATHINFO_EXTENSION);
        
        // Eğer yeni adda uzantı yoksa ekle
        if (!strpos($new_name, '.')) {
            $new_name = $new_name . '.' . $extension;
        }

        $new_path = $directory . '/' . $new_name;

        // Aynı adla dosya var mı kontrol et
        if (file_exists($new_path) && $new_path !== $real_old_path) {
            return false;
        }

        return @rename($real_old_path, $new_path);
    }

    /**
     * Dosya bilgilerini al
     * 
     * @param string $file_path - Dosya yolu (ROOT'a göre)
     * @return array - Dosya bilgileri veya empty array
     */
    public function getFileInfo($file_path)
    {
        $real_path = realpath(ROOT . $file_path);
        $root_path = realpath(ROOT);
        
        if ($real_path === false || strpos($real_path, $root_path) !== 0) {
            return array();
        }

        if (!file_exists($real_path) || !is_file($real_path)) {
            return array();
        }

        return array(
            'path' => $file_path,
            'real_path' => $real_path,
            'name' => basename($real_path),
            'extension' => pathinfo($real_path, PATHINFO_EXTENSION),
            'size' => filesize($real_path),
            'size_formatted' => $this->formatBytes(filesize($real_path)),
            'mime' => mime_content_type($real_path),
            'created' => filectime($real_path),
            'modified' => filemtime($real_path),
            'is_readable' => is_readable($real_path),
            'is_writable' => is_writable($real_path)
        );
    }

    /**
     * Dizindeki dosyaları listele
     * 
     * @param string $dir_path - Dizin yolu (ROOT'a göre)
     * @param string $extension - Filtreleme için dosya uzantısı (opsiyonel)
     * @return array - Dosyaların listesi
     */
    public function listFiles($dir_path, $extension = null)
    {
        $real_dir = realpath(ROOT . $dir_path);
        $root_path = realpath(ROOT);
        
        if ($real_dir === false || strpos($real_dir, $root_path) !== 0) {
            return array();
        }

        if (!is_dir($real_dir)) {
            return array();
        }

        $files = array();
        $dir_relative = rtrim($dir_path, '/');

        $items = @scandir($real_dir);
        if ($items === false) {
            return array();
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $item_path = $real_dir . '/' . $item;
            
            if (!is_file($item_path)) {
                continue;
            }

            if ($extension !== null && strtolower(pathinfo($item, PATHINFO_EXTENSION)) !== strtolower($extension)) {
                continue;
            }

            $files[] = array(
                'name' => $item,
                'path' => $dir_relative . '/' . $item,
                'size' => filesize($item_path),
                'size_formatted' => $this->formatBytes(filesize($item_path)),
                'modified' => filemtime($item_path)
            );
        }

        return $files;
    }

    /**
     * Konfigürasyonu al
     */
    public function getConfig()
    {
        return $this->config;
    }
}

