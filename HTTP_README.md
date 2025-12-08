# HTTP Client Sınıfı Kullanım Kılavuzu

EasyMVC framework'ünde bulunan `http` sınıfı, REST API'lere istek yapmak için kullanılan profesyonel bir HTTP client'ıdır.

## Kurulum

HTTP sınıfı `/core/http.php` dosyasında bulunur ve framework tarafından otomatik olarak yüklenir.

## Temel Kullanım

### Yöntem 1: Constructor ile Config Adı Belirtme

```php
$http = new http('api_hubspot');
$result = $http->get('/crm/v3/objects/contacts');
```

### Yöntem 2: Static Method Kullanma

```php
$result = http::request('GET', '/crm/v3/objects/contacts', [], 'api_hubspot');
```

### Yöntem 3: Fluent Interface (Method Chaining)

```php
$result = (new http('api_hubspot'))
    ->withParams(['limit' => 100])
    ->get('/crm/v3/objects/contacts');
```

## HTTP Yöntemleri

### GET - Veri Almak

Sunucudan veri almak için kullanılır.

```php
// Basit GET
$http = new http('api_hubspot');
$result = $http->get('/crm/v3/objects/contacts');

// Query parametreleri ile
$result = $http->get('/crm/v3/objects/contacts', [
    'limit' => 100,
    'after' => 'page-123'
]);

// veya withParams() ile
$result = $http
    ->withParams(['limit' => 100])
    ->get('/crm/v3/objects/contacts');
```

### POST - Veri Göndermek

Yeni bir kayıt oluşturmak için kullanılır.

```php
$http = new http('api_hubspot');
$result = $http->post('/crm/v3/objects/contacts', [
    'properties' => [
        ['name' => 'firstname', 'value' => 'John'],
        ['name' => 'lastname', 'value' => 'Doe'],
        ['name' => 'email', 'value' => 'john@example.com']
    ]
]);
```

### PUT - Tüm Veriyi Değiştirmek

Tamamını yeniden yazarak güncellemek için kullanılır.

```php
$result = http::request('PUT', '/crm/v3/objects/contacts/123', [
    'properties' => [
        ['name' => 'firstname', 'value' => 'Jane'],
        ['name' => 'lastname', 'value' => 'Smith'],
        ['name' => 'email', 'value' => 'jane@example.com']
    ]
], 'api_hubspot');
```

### PATCH - Kısmi Veri Değiştirmek

Sadece belirtilen alanları güncellemek için kullanılır.

```php
$http = new http('api_hubspot');
$result = $http->patch('/crm/v3/objects/contacts/123', [
    'properties' => [
        ['name' => 'firstname', 'value' => 'Jane']
    ]
]);
```

### DELETE - Veri Silmek

Bir kaydı silmek için kullanılır.

```php
$result = http::request('DELETE', '/crm/v3/objects/contacts/123', [], 'api_hubspot');

// veya
$http = new http('api_hubspot');
$result = $http->delete('/crm/v3/objects/contacts/123');
```

### HEAD - Başlık Bilgisi Almak

Yalnızca response başlıklarını almak için kullanılır.

```php
$result = $http->head('/crm/v3/objects/contacts');
```

### OPTIONS - İzin Verilen Yöntemleri Öğrenmek

Endpoint'in hangi HTTP yöntemlerini desteklediğini öğrenmek için kullanılır.

```php
$result = $http->options('/crm/v3/objects/contacts');
```

## Config Kullanımı

API bilgileri `app.ini` dosyasında tanımlanır:

```ini
[api_hubspot]
base_url = 'https://api.hubapi.com'
api_key = 'your-hubspot-api-key'
timeout = 30
verify_ssl = true

[api_wiveda]
base_url = 'https://api.wiveda.com'
api_key = 'your-wiveda-api-key'
api_token = ''
timeout = 30
verify_ssl = true
```

Config'de tanımlanan API'ler otomatik olarak yüklenir:

```php
// HubSpot API
$http = new http('api_hubspot');

// Wiveda API
$http = new http('api_wiveda');

// Özel URL
$http = new http('https://custom-api.com');
```

## Advanced Kullanım

### Custom Header Ekleme

```php
$http = new http('api_hubspot');
$result = $http
    ->withHeader('X-Custom-Header', 'value')
    ->withHeader('X-Request-ID', '12345')
    ->get('/endpoint');

// Birden fazla header eklemek
$result = $http->withHeaders([
    'X-Custom-Header' => 'value',
    'X-Request-ID' => '12345'
])->get('/endpoint');
```

### Authentication

#### Bearer Token
```php
$http = new http('api_hubspot');
$result = $http
    ->withAuth('your-custom-token')
    ->get('/protected-endpoint');
```

#### Basic Authentication
```php
$http = new http('https://api.example.com');
$result = $http
    ->withBasicAuth('username', 'password')
    ->get('/protected-endpoint');
```

### Timeout Ayarlama

```php
$http = new http('api_hubspot');
$result = $http
    ->setTimeout(60)  // 60 saniye
    ->get('/crm/v3/objects/contacts');
```

### SSL Sertifikası Doğrulamasını Kapatma

```php
$http = new http('api_hubspot');
$result = $http
    ->verifySSL(false)
    ->get('/endpoint');
```

### Content-Type Değiştirmek

```php
// Form data gönderimi
$http = new http('api_hubspot');
$result = $http
    ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
    ->post('/endpoint', [
        'field1' => 'value1',
        'field2' => 'value2'
    ]);
```

## Response Yapısı

Tüm istekler aşağıdaki yapıda bir array döndürür:

```php
[
    'success' => true,                          // bool - İstek başarılı mı?
    'status_code' => 200,                       // int - HTTP status kodu
    'data' => [],                               // mixed - Response verisi (JSON decode edilmiş)
    'headers' => [],                            // array - Response başlıkları
    'error' => null,                            // string|null - Hata mesajı
    'message' => 'İstek başarılı'              // string - Durum mesajı
]
```

### Response Kontrolü

```php
$result = http::request('GET', '/system/user', [], 'api_wiveda');

if ($result['success']) {
    echo "Başarılı!\n";
    echo "Status Code: " . $result['status_code'] . "\n";
    echo "Data: " . json_encode($result['data']) . "\n";
} else {
    echo "Hata: " . $result['error'] . "\n";
}
```

## Helper Methods

### Status Kodu Almak

```php
$http = new http('api_hubspot');
$result = $http->get('/endpoint');
$status = $http->getStatusCode();  // 200
```

### Başarı Durumunu Kontrol Etmek

```php
$http = new http('api_hubspot');
$result = $http->get('/endpoint');

if ($http->isSuccess()) {  // 200-299 arasında mı?
    echo "İstek başarılı!";
}
```

### Hata Mesajı Almak

```php
$http = new http('api_hubspot');
$result = $http->get('/endpoint');

if (!$http->isSuccess()) {
    echo $http->getLastError();
}
```

### Response Header Almak

```php
$http = new http('api_hubspot');
$result = $http->get('/endpoint');

// Tek header
$content_type = $http->getResponseHeader('Content-Type');

// Tüm headerlar
$headers = $http->getResponseHeaders();
```

## Pratik Örnekler

### Örnek 1: Paginated Data Alma

```php
$http = new http('api_hubspot');

$page = 1;
$limit = 100;
$all_contacts = [];

do {
    $result = $http->get('/crm/v3/objects/contacts', [
        'limit' => $limit,
        'offset' => ($page - 1) * $limit
    ]);
    
    if ($result['success']) {
        $all_contacts = array_merge($all_contacts, $result['data']['results'] ?? []);
        $page++;
    } else {
        break;
    }
} while (count($result['data']['results'] ?? []) === $limit);
```

### Örnek 2: Hata Handling

```php
$http = new http('api_wiveda');
$result = $http->post('/system/user', [
    'name' => 'John',
    'email' => 'john@example.com'
]);

if ($result['success'] && $result['status_code'] === 201) {
    echo "Kullanıcı başarıyla oluşturuldu: " . $result['data']['id'];
} elseif ($result['status_code'] === 409) {
    echo "Kullanıcı zaten mevcut";
} else {
    echo "Hata: " . $result['error'];
}
```

### Örnek 3: Batch İşlem

```php
$http = new http('api_hubspot');
$contacts = [
    ['email' => 'john@example.com', 'firstname' => 'John'],
    ['email' => 'jane@example.com', 'firstname' => 'Jane'],
];

foreach ($contacts as $contact) {
    $result = $http->post('/crm/v3/objects/contacts', [
        'properties' => [
            ['name' => 'email', 'value' => $contact['email']],
            ['name' => 'firstname', 'value' => $contact['firstname']]
        ]
    ]);
    
    if ($result['success']) {
        echo "Eklendi: " . $contact['email'] . "\n";
    } else {
        echo "Hata: " . $result['error'] . "\n";
    }
}
```

### Örnek 4: Custom Headers ile

```php
$http = new http('api_wiveda');
$result = $http
    ->withHeaders([
        'X-Request-ID' => uniqid(),
        'X-Client-Version' => '1.0'
    ])
    ->post('/system/user', [
        'name' => 'John',
        'email' => 'john@example.com'
    ]);
```

## Desteklenen Content-Type'lar

- **application/json** (varsayılan) - JSON formatında veri
- **application/x-www-form-urlencoded** - Form verisi
- **Custom** - Özel formatlar için

## Performans İpuçları

1. **Reuse HTTP Client**: Birden fazla istek yapıyorsanız aynı client'ı kullanın
   ```php
   $http = new http('api_hubspot');
   $result1 = $http->get('/endpoint1');
   $result2 = $http->get('/endpoint2');
   ```

2. **Timeout'ı Uygun Ayarlayın**: Yavaş API'ler için timeout'ı artırın
   ```php
   $http = new http('api_hubspot');
   $http->setTimeout(60);
   ```

3. **Error Handling**: Hataları düzgün işleyin
   ```php
   if (!$result['success']) {
       // Log hatasını
       // Retry mekanizması ekleyin
   }
   ```

## Hata Ayıklama

İstek başarısız olmuş olabilir:

- **Network hatası**: `$result['error']` mesajını kontrol edin
- **API key geçersiz**: `app.ini` dosyasındaki API key'i kontrol edin
- **SSL hatası**: Development ortamında `verifySSL(false)` kullanın
- **Timeout**: `setTimeout()` ile timeout'ı artırın
- **Invalid endpoint**: Endpoint path'ini kontrol edin

## Sonuç

HTTP sınıfı, EasyMVC framework'ünde REST API'lere bağlanmak için en kolay ve güvenli yoldur. Config-based yapısı sayesinde API bilgilerinizi merkezi olarak yönetebilir ve kolayca birden fazla API'ye bağlanabilirsiniz.
