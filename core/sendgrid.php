<?php
/**
 * SendGrid Email API Client
 * 
 * SendGrid v3 API kullanarak email gönderme işlemlerini yönetir
 * Dökümantasyon: https://docs.sendgrid.com/api-reference/mail-send/mail-send
 */
class sendgrid
{
    private $api_key;
    private $api_url = 'https://api.sendgrid.com/v3/mail/send';
    private $from_email;
    private $from_name;
    
    /**
     * Constructor
     * app.ini'den SendGrid yapılandırmasını yükler
     */
    public function __construct()
    {
        $config = app::get_config();
        
        if (!isset($config['sendgrid'])) {
            throw new Exception("SendGrid configuration not found in app.ini");
        }
        
        $this->api_key = $config['sendgrid']['api_key'] ?? '';
        $this->from_email = $config['sendgrid']['from_email'] ?? '';
        $this->from_name = $config['sendgrid']['from_name'] ?? 'Wiveda';
        
        if (empty($this->api_key)) {
            throw new Exception("SendGrid API key is required");
        }
        
        if (empty($this->from_email)) {
            throw new Exception("SendGrid from_email is required");
        }
    }
    
    /**
     * Email gönder
     * 
     * @param array $params Email parametreleri
     * @return array ['success' => bool, 'message' => string, 'data' => array]
     */
    public function send($params = [])
    {
        // Zorunlu alanları kontrol et
        if (empty($params['to_email'])) {
            return [
                'success' => false,
                'message' => 'Recipient email address is required',
                'data' => null
            ];
        }
        
        if (empty($params['subject'])) {
            return [
                'success' => false,
                'message' => 'Email subject is required',
                'data' => null
            ];
        }
        
        if (empty($params['html_content']) && empty($params['text_content'])) {
            return [
                'success' => false,
                'message' => 'Email content (html or text) is required',
                'data' => null
            ];
        }
        
        // SendGrid API payload'ını hazırla
        $payload = [
            'personalizations' => [
                [
                    'to' => [
                        [
                            'email' => $params['to_email'],
                            'name' => $params['to_name'] ?? ''
                        ]
                    ],
                    'subject' => $params['subject']
                ]
            ],
            'from' => [
                'email' => $params['from_email'] ?? $this->from_email,
                'name' => $params['from_name'] ?? $this->from_name
            ],
            'content' => []
        ];
        
        // HTML içerik varsa ekle
        if (!empty($params['html_content'])) {
            $payload['content'][] = [
                'type' => 'text/html',
                'value' => $params['html_content']
            ];
        }
        
        // Plain text içerik varsa ekle
        if (!empty($params['text_content'])) {
            $payload['content'][] = [
                'type' => 'text/plain',
                'value' => $params['text_content']
            ];
        }
        
        // Reply-to ekle
        if (!empty($params['reply_to'])) {
            $payload['reply_to'] = [
                'email' => $params['reply_to']
            ];
        }
        
        // CC ekle
        if (!empty($params['cc'])) {
            $ccList = is_array($params['cc']) ? $params['cc'] : [$params['cc']];
            $payload['personalizations'][0]['cc'] = array_map(function($email) {
                return ['email' => $email];
            }, $ccList);
        }
        
        // BCC ekle
        if (!empty($params['bcc'])) {
            $bccList = is_array($params['bcc']) ? $params['bcc'] : [$params['bcc']];
            $payload['personalizations'][0]['bcc'] = array_map(function($email) {
                return ['email' => $email];
            }, $bccList);
        }
        
        // Attachments ekle
        if (!empty($params['attachments']) && is_array($params['attachments'])) {
            $payload['attachments'] = $params['attachments'];
        }
        
        // API isteğini gönder
        try {
            $response = $this->makeRequest($payload);
            return $response;
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'SendGrid API error: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
    
    /**
     * SendGrid API'ye HTTP isteği gönder
     * 
     * @param array $payload JSON payload
     * @return array Response
     */
    private function makeRequest($payload)
    {
        $ch = curl_init($this->api_url);
        
        $headers = [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ];
        
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        // cURL hatası
        if ($error) {
            throw new Exception("cURL error: $error");
        }
        
        // SendGrid başarılı yanıt kodları: 200, 202
        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'message' => 'Email sent successfully',
                'data' => [
                    'http_code' => $httpCode,
                    'response' => $response
                ]
            ];
        }
        
        // Hata yanıtı
        $errorData = json_decode($response, true);
        $errorMessage = 'Unknown error';
        
        if (isset($errorData['errors']) && is_array($errorData['errors'])) {
            $errorMessage = implode(', ', array_map(function($err) {
                return $err['message'] ?? 'Unknown error';
            }, $errorData['errors']));
        }
        
        return [
            'success' => false,
            'message' => "SendGrid API error (HTTP $httpCode): $errorMessage",
            'data' => [
                'http_code' => $httpCode,
                'response' => $errorData
            ]
        ];
    }
    
    /**
     * Toplu email gönder
     * 
     * @param array $recipients [['email' => '', 'name' => '', 'subject' => '', ...], ...]
     * @param array $commonParams Tüm emailler için ortak parametreler
     * @return array ['success' => int, 'failed' => int, 'results' => array]
     */
    public function sendBatch($recipients = [], $commonParams = [])
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'results' => []
        ];
        
        foreach ($recipients as $recipient) {
            // Ortak parametreleri birleştir
            $params = array_merge($commonParams, $recipient);
            
            $result = $this->send($params);
            
            $results['results'][] = [
                'email' => $recipient['to_email'] ?? 'unknown',
                'success' => $result['success'],
                'message' => $result['message']
            ];
            
            if ($result['success']) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
            
            // Rate limiting için kısa bekleme (SendGrid limiti: 100 email/saniye)
            usleep(10000); // 10ms = 100 email/saniye
        }
        
        return $results;
    }
    
    /**
     * Mail queue verisiyle email gönder
     * 
     * @param array $mailData Mail queue'dan gelen veri
     * @return array SendGrid response
     */
    public function sendFromQueue($mailData)
    {
        $params = [
            'to_email' => $mailData['to_email'] ?? '',
            'to_name' => $mailData['to_name'] ?? '',
            'subject' => $mailData['subject'] ?? ''
        ];
        
        // Body alanını HTML content olarak kullan
        if (!empty($mailData['body'])) {
            $params['html_content'] = $mailData['body'];
        }
        
        // Alternatif alan adları (esneklik için)
        if (!empty($mailData['body_html'])) {
            $params['html_content'] = $mailData['body_html'];
        }
        
        if (!empty($mailData['body_text'])) {
            $params['text_content'] = $mailData['body_text'];
        }
        
        // Mail type'a göre reply-to ayarla
        if (!empty($mailData['reply_to'])) {
            $params['reply_to'] = $mailData['reply_to'];
        }
        
        return $this->send($params);
    }
}
