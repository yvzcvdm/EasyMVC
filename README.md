# EasyMVC

**EasyMVC**, Pure PHP ile yazılmış, minimum kod footprint'i ile maksimum verimlilik sağlayan, ultra-hafif ve modüler bir **Model-View-Controller (MVC)** framework'üdür. 

Dependency ve karmaşık yapılardan uzak, tamamen PHP Core üzerine inşa edilmiş, büyük ölçekli web projeleri için **hızlı geliştirme** ve **kolay bakım** imkanı sunar.

---

## 🎯 Core Felsefesi

EasyMVC'nin temel prensibi: **"Minimal, Sade, Hızlı, Güçlü"**

- ✅ **Pure PHP**: Hiçbir dış dependency, tamamen Core PHP
- ✅ **Micro Framework**: 10+ core dosyası ile tam işlevsellik
- ✅ **Auto-Routing**: Klasör yapısı = URL yapısı (Otomatik yönlendirme)
- ✅ **Global App Variable**: Tüm veriye tek `$app` array'inden erişim
- ✅ **Auto-Loading**: Controller ve Model sınıfları otomatik yüklenir
- ✅ **Modular Core**: İhtiyaca göre ek core dosyaları eklenebilir (MySQL, PostgreSQL, SQLite, Mail, vb.)
- ✅ **Extensible Init**: Genişletilebilir helper fonksiyonları

---

## 📋 Özellikler

### 1. **Otomatik Routing Sistemi**
EasyMVC'nin kalbi, tamamen otomatik ve klasör-tabanlı routing sistemidir. URL yapınız direkt olarak dosya yapınıza karşılık gelir:

```
URL Yapısı:
http://example.com/[controller]/[method]/[param1]/[param2]/...

Dosya Yapısı:
app/controller/[controller]/[method].php ← otomatik yüklenir
                          ↓
                    /app/controller/blog.php
                    /app/controller/admin/user.php
                    
Örnek Rota:
http://example.com/blog              → app/controller/blog.php → index() metodu
http://example.com/blog/detail/5     → app/controller/blog.php → detail() metodu (param: 5)
http://example.com/admin/user/edit   → app/controller/admin/user.php → edit() metodu
```

**Yapı Tabanlı Otomatik Yönlendirme Avantajları:**
- Hiçbir route tanımlamasına gerek yok
- Klasör yapısı artarken routes otomatik oluşur
- SEO-friendly URL yapısı
- Bakım ve geliştirme kolaylığı

---

### 2. **Aşırı Derece Sade Yapı**

**Toplam 10-12 Core Dosyası:**
```
core/
├── app.php              (Ana uygulama sınıfı - 275 satır)
├── init.php             (Helper fonksiyonlar - Genişletilebilir)
├── view.php             (Minimal template engine)
├── error.php            (Hata yönetimi)
├── File.php             (Dosya yükleme)
├── mysql.php            (MySQL PDO bağlantısı - İsteğe bağlı)
├── postgresql.php       (PostgreSQL PDO bağlantısı - İsteğe bağlı)
├── sqlite.php           (SQLite PDO bağlantısı - İsteğe bağlı)
└── mail.php             (E-posta gönderme - İsteğe bağlı)
```

**Toplam Satır Sayısı:** ~2000 satır (Yorum ve boş satırlar dahil)

Hiçbir external library, hiçbir package manager gerekli değildir.

---

### 3. **Core Otomatik Class Entegrasyonu (SPL Autoloader)**

```php
// index.php
spl_autoload_register(function ($className) {
    // CORE klasöründen yükle
    if (file_exists(CORE . SEP . $className . ".php")) {
        require_once CORE . SEP . $className . ".php";
        return;
    }
    // CONTROLLER ve MODEL'den yükle
    if (file_exists(CONTROLLER . SEP . $className . ".php")) {
        require_once CONTROLLER . SEP . $className . ".php";
        return;
    }
});
```

**Kullanım:**
```php
$file = new File();           // core/File.php otomatik yüklenir
$user = new user_Model();     // app/model/user.php otomatik yüklenir
$blog = new blog();           // app/controller/blog.php otomatik yüklenir
```

Hiçbir manual require/include yazmaya gerek yok!

---

### 4. **Ultra-Minimal View Sistemi**

EasyMVC view'ler pure PHP dosyalarıdır. Herhangi bir template syntax yok:

```php
// app/view/blog_view.php
<div class="blog-post">
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <p><?= htmlspecialchars($post['content']) ?></p>
    <p class="meta">Yazar: <?= htmlspecialchars($post['author']) ?></p>
</div>
```

**Layout Sistemi:**
```php
view::layout("blog", $data);  // View'i layout içine embed eder
```

Layout dosyası (app/layout/header.php ve footer.php):
```php
<!-- app/layout/header.php -->
<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
</head>
<body>

<!-- View otomatik buraya gömülür -->

<!-- app/layout/footer.php -->
</body>
</html>
```

---

### 5. **Global $app Array'i - Tüm Veriye Erişim**

**Tüm uygulama parametreleri tek bir array'de:**

```php
// Controller ve View'de her yerde erişilebilir:
$app = [
    "root"      => "/",                        // Site root path
    "path"      => "blog/",                    // Controller path
    "file"      => "blog",                     // Controller filename
    "function"  => "detail",                   // Method name
    "uri"       => "/blog/detail/5/",          // Full URI
    "post"      => $_POST,                     // POST verileri
    "get"       => $_GET,                      // GET verileri
    "cookie"    => $_COOKIE,                   // Cookie verileri
    "session"   => $_SESSION,                  // Session verileri
    "files"     => $_FILES,                    // Upload files
    "raw"       => $raw_input,                 // Raw JSON input
    "uri_0" => "detail",                       // URI parametresi 1
    "uri_1" => "5",                            // URI parametresi 2
];
```

**View'de Kullanım:**
```php
<!-- View'de $app direkt erişilebilir -->
<a href="<?= $app["root"] ?>blog/detail/<?= $post['id'] ?>">
    <?= htmlspecialchars($post['title']) ?>
</a>

<!-- Veya Controller'den geçilen $data array'i -->
<h1><?= $title ?></h1>
<p><?= $content ?></p>
```

---

### 6. **Üzerine İnşa Edilebilir Init Dosyası**

`core/init.php` helper fonksiyonlarla doludur ve kolayca genişletilebilir:

```php
// core/init.php
class init
{
    // Slug oluşturma (SEO-friendly URL)
    public static function slug($text) { /* ... */ }
    
    // Metin kısaltma
    public static function text_short($text, $length) { /* ... */ }
    
    // E-posta doğrulama
    public static function valid_email($email) { /* ... */ }
    
    // Telefon doğrulama
    public static function valid_phone($phone) { /* ... */ }
    
    // T.C. Kimlik doğrulama
    public static function valid_tc_number($tcno) { /* ... */ }
    
    // Rastgele kod oluşturma
    public static function random_text_code($length) { /* ... */ }
}
```

**Kendi Helper Fonksiyonlarınızı Ekleyin:**
```php
// core/init.php içine ekleyin
public static function my_custom_function() {
    return "Custom logic...";
}

// Herhangi yerden kullanın
init::my_custom_function();
```

---

### 7. **Otomatik Model ve Controller Yükleme**

**Sınıf Adlandırma Konvansiyonu:**
```
Model Dosyası: app/model/blog.php          → class: blog
Model Dosyası: app/model/user_post.php     → class: user_post
Controller Dosyası: app/controller/blog.php → class: blog
Controller Dosyası: app/controller/admin/user.php → class: admin_user
```

**Controller'de Model Kullanımı:**
```php
<?php
class blog
{
    private $blog_Model;
    
    public function __construct()
    {
        $this->blog_Model = new blog();  // Otomatik yüklenir!
    }
    
    public function index($data)
    {
        $data["posts"] = $this->blog_Model->get_posts();
        view::layout("blog", $data);
    }
}
```

---

### 8. **Modular Core - İhtiyaca Bağlı Ek Dosyalar**

Framework minimal gelmesine rağmen, ihtiyacınız olan core dosyaları ekleyebilirsiniz:

```
İsteğe Bağlı Core Dosyaları:
├── mysql.php            → MySQL PDO wrapper
├── postgresql.php       → PostgreSQL PDO wrapper
├── sqlite.php           → SQLite PDO wrapper
└── mail.php             → SMTP e-posta gönderimi
```

**Örnek - Veritabanı Seçimi:**
```php
// Sadece ihtiyacınız olanı yükleyin
require_once "core/mysql.php";      // MySQL kullanılacaksa
// veya
require_once "core/postgresql.php"; // PostgreSQL kullanılacaksa
```

---

### 9. **PDO Tabanlı Veritabanı Yönetimi**

Tüm veritabanı sınıfları PDO kullanır (Secure, prepared statements):

```php
<?php
class blog_Model extends mysql  // veya postgres, sqlite
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function get_posts()
    {
        return $this->proc("SELECT * FROM posts ORDER BY date DESC");
    }
    
    public function get_post($id)
    {
        $id = intval($id);
        return $this->proc("SELECT * FROM posts WHERE id = $id");
    }
}
```

---

### 10. **Dosya Yükleme Sistemi**

Tamamen security odaklı, esnek dosya yükleme:

```php
// Controller
public function upload($data)
{
    $file = new File();
    $result = $file->upload("file_input[]", "/public/uploads/");
    
    // Sonuç array'inde her item'in status ve detayları vardır
    $data["items"] = $result['items'];  
    view::layout("upload", $data);
}
```

**Yapılandırma (app.ini):**
```ini
[upload]
max_file_size = 5242880              ; 5MB
allowed_extensions = "jpg,jpeg,png,pdf,zip,doc,docx"
upload_path = "/public/uploads/"
filename_format = "timestamp"         ; timestamp, random, original
```

---

### 11. **E-Posta Gönderme**

SMTP üzerinden güvenli e-posta gönderimi:

```php
// Yapılandırma (app.ini)
[email]
mail_server = "smtp.gmail.com"
mail_port = "465"
mail_secure = "ssl"
mail_user = "your-email@gmail.com"
mail_pass = "your-password"

// Kullanım
init::send_mail("recipient@example.com", "Konu", "E-posta içeriği");
```

---

### 12. **Form Doğrulama ve Yardımcılar**

Yerleşik doğrulama fonksiyonları:

```php
// E-posta doğrulama
if (init::valid_email($email)) { /* ... */ }

// Telefon doğrulama (Türk telefon formatı)
if (init::valid_phone($phone)) { /* ... */ }

// T.C. Kimlik numarası doğrulama
if (init::valid_tc_number($tcno)) { /* ... */ }

// URL slug oluşturma
$slug = init::slug("Türkçe Başlık");  // "turkce-baslik"

// Metin kısaltma
$short = init::text_short($long_text, 100);  // İlk 100 karakter + "..."

// Rastgele kod oluşturma
$code = init::random_text_code(10);  // 10 karakterli random kod
```

---

## 📊 Sistem Gereksinimleri

- **PHP:** 7.0 veya üzeri (5.6 ile de uyumlu)
- **Veritabanı:** MySQL 5.6+, PostgreSQL 9.0+, veya SQLite 3
- **Web Sunucusu:** Apache (mod_rewrite) veya Nginx
- **Harici Kütüphane:** YOK - Pure PHP!

---

## 🚀 Kurulum

### 1. Dosyaları İndirin
```bash
git clone https://github.com/yvzcvdm/EasyMVC.git
cd EasyMVC
```

### 2. app.ini Dosyasını Düzenleyin

```ini
[info]
site_title = 'Sitenizin Adı'
site_domain = 'example.com'
site_url = 'https://example.com'
site_mail = 'info@example.com'
site_logo = 'https://example.com/logo.png'

[mysql]
db_server = "localhost"
db_name = "veritabani_adi"
db_user = "root"
db_pass = "sifre"
db_port = "3306"

[upload]
max_file_size = 5242880
allowed_extensions = "jpg,jpeg,png,gif,pdf,zip,rar,doc,docx"
upload_path = "/public/uploads/"
filename_format = "timestamp"

[email]
mail_server = 'smtp.example.com'
mail_port = '465'
mail_secure = 'ssl'
mail_user = 'user@example.com'
mail_pass = 'mail_sifre'
```

### 3. Apache Yapılandırması

`.htaccess` dosyası zaten mevcuttur. Root dizinine bakın:

```apacheconf
<IfModule mod_rewrite.c>
  RewriteEngine on
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_URI} !^/public/ [NC]
  RewriteRule ^(.*)$ /index.php [L]
</IfModule>
```

### 4. Nginx Yapılandırması

```nginx
location / {
    if (!-e $request_filename) {
        rewrite ^(.*)$ /index.php last;
    }
}

location ^~ /public/ {
    # Doğrudan dosyaları serve et, rewrite yapma
}
```

### 5. Klasör İzinleri

```bash
chmod 755 -R /
chmod 777 -R /public/uploads/
chmod 777 -R /tmp/
```

---

## 📁 Dizin Yapısı

```
EasyMVC/
├── app/
│   ├── controller/                 # Controller sınıfları
│   │   ├── index.php               # Varsayılan controller
│   │   └── admin/
│   │       ├── index.php
│   │       └── user.php
│   ├── model/                      # Model sınıfları
│   │   ├── index.php
│   │   └── blog.php
│   ├── view/                       # View şablonları
│   │   ├── index_view.php
│   │   └── upload_view.php
│   └── layout/                     # Layout şablonları
│       ├── header.php
│       └── footer.php
│
├── core/                           # Framework core
│   ├── app.php                     # Ana uygulama (275 satır)
│   ├── init.php                    # Helper fonksiyonlar
│   ├── view.php                    # Minimal template engine
│   ├── error.php                   # Hata yönetimi
│   ├── File.php                    # Dosya yükleme
│   ├── mysql.php                   # MySQL (isteğe bağlı)
│   ├── postgresql.php              # PostgreSQL (isteğe bağlı)
│   ├── sqlite.php                  # SQLite (isteğe bağlı)
│   └── mail.php                    # E-posta (isteğe bağlı)
│
├── public/                         # Public assets
│   ├── css/                        # CSS dosyaları
│   ├── js/                         # JavaScript dosyaları
│   ├── images/                     # Görsel dosyaları
│   ├── templates/                  # E-posta şablonları
│   └── uploads/                    # Yüklenen dosyalar
│
├── .htaccess                       # Apache rewrite rules
├── index.php                       # Giriş noktası (39 satır)
├── app.ini                         # Yapılandırma dosyası
└── README.md
```

---

## 💻 Hızlı Başlangıç

### 1. Simple Controller Oluşturma

```php
<?php
// app/controller/blog.php
class blog
{
    public function index($data)
    {
        $data["title"] = "Blog";
        $data["message"] = "Hoş geldiniz!";
        view::layout("blog", $data);
    }
    
    public function post($data)
    {
        $post_id = $data["uri_0"] ?? 1;
        $data["title"] = "Blog Yazısı #" . $post_id;
        view::layout("blog_post", $data);
    }
}
```

### 2. Model ile Controller

```php
<?php
// app/model/blog.php
class blog
{
    public function get_posts()
    {
        // Sabit veri döndür (gerçek uygulamada DB'den)
        return [
            ["id" => 1, "title" => "İlk Yazı", "content" => "..."],
            ["id" => 2, "title" => "İkinci Yazı", "content" => "..."],
        ];
    }
}

// app/controller/blog.php
class blog
{
    private $blog_Model;
    
    public function __construct()
    {
        $this->blog_Model = new blog();
    }
    
    public function index($data)
    {
        $data["posts"] = $this->blog_Model->get_posts();
        view::layout("blog", $data);
    }
}
```

### 3. View Oluşturma

```php
<?php
// app/view/blog_view.php
?>
<div class="blog-container">
    <h1><?= htmlspecialchars($title) ?></h1>
    
    <div class="posts-list">
        <?php foreach ($posts as $post): ?>
            <article class="post-card">
                <h2><?= htmlspecialchars($post['title']) ?></h2>
                <p><?= htmlspecialchars($post['content']) ?></p>
                <a href="<?= $app["root"] ?>blog/post/<?= $post['id'] ?>">
                    Devamını Oku →
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</div>
```

### 4. URL Rota Örnekleri

```
http://example.com/
  ↓
app/controller/index.php → index()

http://example.com/blog
  ↓
app/controller/blog.php → index()

http://example.com/blog/post/5
  ↓
app/controller/blog.php → post()
$data["uri_0"] = "5"

http://example.com/admin/user/edit/10
  ↓
app/controller/admin/user.php → edit()
$data["uri_0"] = "10"
```

---

## 🔧 Gelişmiş Özellikler

### POST ve GET Verisi

```php
public function contact($data)
{
    if (isset($data["post"]["submit"])) {
        $email = $data["post"]["email"] ?? "";
        $message = $data["post"]["message"] ?? "";
        
        // Doğrulama
        if (!init::valid_email($email)) {
            $data["error"] = "Geçersiz e-posta!";
        } else {
            // E-posta gönder
            init::send_mail("admin@example.com", "Yeni İletişim", $message);
            $data["success"] = "Mesajınız alındı!";
        }
    }
    
    view::layout("contact", $data);
}
```

### Dosya Yükleme

```php
public function upload($data)
{
    $file = new File();
    $result = $file->upload("file_input[]", "/public/uploads/");
    
    $data["items"] = $result['items'];  // Tüm upload işlemleri
    
    // Sonuç formatı:
    // [
    //     ["file" => "dosya.jpg", "status" => "success", "path" => "/public/uploads/..."],
    //     ["file" => "hata.txt", "status" => "error", "message" => "..."],
    // ]
    
    view::layout("upload", $data);
}
```

View'de:
```php
<table>
    <tr>
        <th>Dosya</th>
        <th>Durum</th>
        <th>Detay</th>
    </tr>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['file']) ?></td>
            <td>
                <?php if ($item['status'] === 'success'): ?>
                    <span style="color: green;">✓ Başarılı</span>
                <?php else: ?>
                    <span style="color: red;">✗ Hata</span>
                <?php endif; ?>
            </td>
            <td>
                <?= $item['status'] === 'success' ? 
                    $item['path'] : 
                    $item['message'] ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
```

### Helper Fonksiyonları Kullanımı

```php
// Controller
public function blog($data)
{
    $data["title"] = "Blog Sayfası";
    $data["slug"] = init::slug($data["title"]);  // "blog-sayfasi"
    $data["random_code"] = init::random_text_code(8);
    $data["short_text"] = init::text_short("Çok uzun metin...", 50);
    
    view::layout("blog", $data);
}
```

---

## 🛡️ Security Best Practices

EasyMVC içinde kullanılan security uygulamaları:

```php
// 1. XSS Protection - Her zaman output sanitize edin
<?= htmlspecialchars($user_input) ?>
<?= htmlspecialchars($data, ENT_QUOTES, 'UTF-8') ?>

// 2. SQL Injection Protection - PDO prepared statements
$id = intval($id);  // veya parametrized query

// 3. File Upload Security
- Dosya türü doğrulaması
- Boyut kontrolü
- Tehlikeli uzantı engelleme
- Random dosya adlandırma (Varsayılan: timestamp format)

// 4. Session Security (app.ini)
session.cookie_secure = 1
session.cookie_httponly = 1
session.cookie_samesite = Strict
```

---

## 📈 Performance

EasyMVC'nin performance avantajları:

| Metrik | Değer |
|--------|-------|
| Framework Boyutu | ~2000 satır (comments dahil) |
| Core Dosya Sayısı | 9-13 dosya |
| Load Time | < 10ms (boş sayfa) |
| Memory Usage | < 1MB |
| Setup Süresi | < 5 dakika |
| External Dependencies | 0 (Pure PHP) |

---

## 🤝 Katkıda Bulunma

1. Depoyu fork edin
2. Feature branch oluşturun: `git checkout -b feature/new-feature`
3. Değişiklikleri commit edin: `git commit -am 'Add new feature'`
4. Push edin: `git push origin feature/new-feature`
5. Pull Request açın

---

## 📝 Lisans

Bu proje [MIT Lisansı](LICENSE) altında lisanslanmıştır.

---

## 👨‍💻 Hakkında

EasyMVC, maksimum verimlilik ile minimum kompleksiteyi hedefleyen bir framework'tür. Modular yapısı sayesinde siz de özel core dosyalarınızı ekleyerek framework'u genişletebilirsiniz.

**Framework Felsefesi:** "Keep it Simple, Keep it Fast"
