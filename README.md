# EasyMVC

EasyMVC, PHP tabanlı, hafif ve özelleştirilebilir bir Model-View-Controller (MVC) framework'üdür. Küçük ve orta ölçekli web projeleri için hızlı geliştirme imkanı sunar.

## Özellikler

- Sade ve anlaşılır MVC mimarisi
- Otomatik URL yönlendirme sistemi
- PDO tabanlı veritabanı bağlantı yönetimi
- E-posta gönderme fonksiyonları
- Dosya yükleme desteği
- Form doğrulama yardımcıları
- HTML, JSON çıktı formatları
- Şablon sistemi ve layout desteği

## Sistem Gereksinimleri

- PHP 7.0 veya üzeri
- MySQL 5.6 veya üzeri
- Apache/Nginx Web Sunucusu
- mod_rewrite modülü (Apache için)

## Kurulum

1. Dosyaları web sunucunuza yükleyin
2. `app.ini` dosyasını projenize göre düzenleyin:
   ```ini
   [info]
   site_title = 'Sitenizin Adı'
   site_domain = 'example.com'
   site_url   = 'https://example.com'
   site_mail   = 'info@example.com'
   site_logo   = 'https://example.com/logo.png'

   [database]
   db_server = "localhost"
   db_name   = "veritabani_adi"
   db_user   = "kullanici_adi"
   db_pass   = "sifre"
   db_port   = "3306"

   [email]
   mail_server = 'smtp.example.com'
   mail_port   = '465'
   mail_secure = 'ssl'
   mail_user   = 'user@example.com'
   mail_pass   = 'mail_sifre'
   ```
3. `.htaccess` dosyasını ana dizine ekleyin veya Nginx için uygun yönlendirmeleri yapın
4. Klasör izinlerini düzenleyin:
   ```
   chmod 755 -R /
   chmod 777 -R /assets/upload/
   ```

## Dizin Yapısı

```
/
├── app/
│   ├── controller/      # Controller sınıfları
│   ├── model/           # Model sınıfları
│   ├── view/            # View dosyaları
│   └── layout/          # Şablon dosyaları
├── assets/
│   ├── css/             # CSS dosyaları
│   ├── js/              # JavaScript dosyaları
│   ├── images/          # Görsel dosyaları
│   ├── upload/          # Yüklenen dosyalar
│   └── html/            # HTML şablonları
├── system/
│   ├── app.php          # Ana uygulama sınıfı
│   ├── db.php           # Veritabanı sınıfı
│   ├── view.php         # Görünüm sınıfı
│   ├── init.php         # Yardımcı fonksiyonlar
│   ├── mail.php         # E-posta sınıfı
│   └── error.php        # Hata yönetimi
├── index.php            # Giriş noktası
└── app.ini              # Yapılandırma dosyası
```

## Kullanım

### Controller Oluşturma

Controller dosyalarınızı `app/controller/` dizini altında oluşturun:

```php
<?php 
class blog extends app
{
    public function __construct()
    {
        $this->blog_Model = new blog_Model();
    }
    
    public function index($data)
    {
        $data["title"] = "Blog Sayfası";
        $data["posts"] = $this->blog_Model->get_posts();
        view::layout("blog", $data);
    }
    
    public function detail($data)
    {
        $id = $data["app_uri_0"] ?? 0;
        $data["post"] = $this->blog_Model->get_post($id);
        $data["title"] = $data["post"]["title"] ?? "Yazı Bulunamadı";
        view::layout("blog_detail", $data);
    }
}
```

### Model Oluşturma

Model dosyalarınızı `app/model/` dizini altında oluşturun:

```php
<?php
class blog_Model extends db
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
        return $this->proc("SELECT * FROM posts WHERE id = $id")[0] ?? [];
    }
}
```

### View Oluşturma

View dosyalarınızı `app/view/` dizini altında oluşturun:

```php
<!-- app/view/blog_view.php -->
<div class="container">
    <h1><?= $title ?></h1>
    <div class="row">
        <?php foreach ($posts["data"] as $post): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?= $post["title"] ?></h5>
                        <p class="card-text"><?= text_short($post["content"], 150) ?></p>
                        <a href="<?= $app_root ?>blog/detail/<?= $post["id"] ?>" class="btn btn-primary">Devamını Oku</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
```

### URL Yapısı

Sistemin URL yapısı şu şekildedir:

```
http://example.com/[controller]/[method]/[param1]/[param2]/...
```

Örnek:
- `http://example.com/blog` - blog controller, index metodu
- `http://example.com/blog/detail/1` - blog controller, detail metodu, 1 parametresi

## Form İşlemleri

```php
// Controller
public function login($data)
{
    if (isset($data["app_post"]["submit"])) {
        $email = $data["app_post"]["email"];
        $password = $data["app_post"]["password"];
        
        if (!init::valid_email($email)) {
            $data["message"] = init::show_message("danger", "Geçersiz e-posta adresi!");
        } else {
            $user = $this->user_Model->login($email, $password);
            if ($user) {
                // Login success
            } else {
                $data["message"] = init::show_message("danger", "Hatalı giriş bilgileri!");
            }
        }
    }
    
    view::layout("login", $data);
}
```

## Veritabanı Sorgulama

```php
// Tüm kayıtları getir
$result = $this->proc("SELECT * FROM users");
$users = $result["data"];

// Tekil kayıt getir
$result = $this->proc("SELECT * FROM users WHERE id = 1");
$user = $result["data"][0] ?? null;

// İnsert işlemi
$this->proc("INSERT INTO logs (user_id, action, date) VALUES (1, 'login', NOW())");
```

## Yardımcı Fonksiyonlar

```php
// SEO dostu URL oluşturma
$slug = init::slug("Türkçe Başlık"); // turkce-baslik

// Kısa metin oluşturma
$short = init::text_short($longText, 100);

// E-posta doğrulama
if (init::valid_email($email)) { /* ... */ }

// Telefon doğrulama
if (init::valid_phone($phone)) { /* ... */ }

// T.C. Kimlik doğrulama
if (init::valid_tc_number($tcno)) { /* ... */ }

// E-posta gönderme
init::send_mail("user@example.com", "Konu", "İçerik");
```

## Dosya Yükleme

```php
// Controller
public function upload($data)
{
    $data["uploads"] = $this->upload_img("/assets/upload/images/");
    view::layout("upload", $data);
}

// View
<form method="post" enctype="multipart/form-data">
    <input type="file" name="image">
    <button type="submit">Yükle</button>
</form>

<?php if (isset($uploads) && !empty($uploads)): ?>
    <img src="<?= $uploads["image"] ?>" alt="Yüklenen Resim">
<?php endif; ?>
```

## Katkıda Bulunma

1. Bu depoyu fork edin
2. Özellik branch'i oluşturun (`git checkout -b ozellik/yeni-ozellik`)
3. Değişikliklerinizi commit edin (`git commit -am 'Yeni özellik: açıklama'`)
4. Branch'inizi push edin (`git push origin ozellik/yeni-ozellik`)
5. Yeni bir Pull Request oluşturun

## Lisans

Bu proje [MIT Lisansı](LICENSE) altında lisanslanmıştır.
