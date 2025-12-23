<?php

class http
{
    private $curl;
    private $config = [];
    private $headers = [];
    private $options = [];
    private $timeout = 30;
    private $verify_ssl = true;
    private $response_code = null;
    private $response_headers = [];
    private $last_error = null;
    private $base_url = '';
    
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';
    const HEAD = 'HEAD';
    const OPTIONS = 'OPTIONS';

    public function __construct($config_name = null)
    {
        $this->headers = [
            'User-Agent' => 'EasyMVC-HTTP-Client/1.0',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
        
        $this->options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_ENCODING => 'gzip, deflate',
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ];

        if ($config_name) {
            $this->loadConfig($config_name);
        }
    }

    private function loadConfig($config_name)
    {
        $app_config = app::get_config();
        
        if (strpos($config_name, 'api_') === 0 && isset($app_config[$config_name])) {
            $this->config = $app_config[$config_name];
            $this->base_url = isset($this->config['base_url']) ? $this->config['base_url'] : '';
            $this->timeout = isset($this->config['timeout']) ? $this->config['timeout'] : 30;
            $this->verify_ssl = isset($this->config['verify_ssl']) ? $this->config['verify_ssl'] : true;
            
            if (isset($this->config['api_key']) && $this->config['api_key']) {
                $this->headers['Authorization'] = 'Bearer ' . $this->config['api_key'];
            }
            
            if (isset($this->config['api_token']) && $this->config['api_token']) {
                $this->headers['X-API-Token'] = $this->config['api_token'];
            }
        } else {
            $this->base_url = $config_name;
            $http_config = isset($app_config['http']) ? $app_config['http'] : [];
            $this->timeout = isset($http_config['timeout']) ? $http_config['timeout'] : 30;
            $this->verify_ssl = isset($http_config['verify_ssl']) ? $http_config['verify_ssl'] : true;
        }
    }

    public static function request($method, $endpoint, $data = [], $config_name = 'api_hubspot')
    {
        $client = new self($config_name);
        $method = strtoupper($method);

        switch ($method) {
            case self::GET:
                return $client->get($endpoint, $data);
            case self::POST:
                return $client->post($endpoint, $data);
            case self::PUT:
                return $client->put($endpoint, $data);
            case self::PATCH:
                return $client->patch($endpoint, $data);
            case self::DELETE:
                return $client->delete($endpoint, $data);
            case self::HEAD:
                return $client->head($endpoint);
            case self::OPTIONS:
                return $client->options($endpoint);
            default:
                return [
                    'success' => false,
                    'status_code' => null,
                    'data' => null,
                    'headers' => [],
                    'error' => 'Bilinmeyen HTTP yöntemi: ' . $method,
                    'message' => 'Bilinmeyen HTTP yöntemi: ' . $method
                ];
        }
    }

    public function withParams(array $params)
    {
        $separator = (strpos($this->base_url, '?') !== false) ? '&' : '?';
        $this->base_url .= $separator . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        return $this;
    }

    public function withHeader($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function withHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function withAuth($token)
    {
        $this->headers['Authorization'] = 'Bearer ' . $token;
        return $this;
    }

    public function withBasicAuth($username, $password)
    {
        $this->headers['Authorization'] = 'Basic ' . base64_encode("$username:$password");
        return $this;
    }

    public function setTimeout($seconds)
    {
        $this->timeout = $seconds;
        return $this;
    }

    public function verifySSL($verify = true)
    {
        $this->verify_ssl = $verify;
        return $this;
    }

    public function withOption($option, $value)
    {
        $this->options[$option] = $value;
        return $this;
    }

    public function get($endpoint = '', $params = [])
    {
        $url = $this->buildUrl($endpoint);
        
        if (!empty($params)) {
            $separator = (strpos($url, '?') !== false) ? '&' : '?';
            $url .= $separator . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        }
        
        return $this->executeRequest(self::GET, $url);
    }

    public function post($endpoint = '', $body = [])
    {
        $url = $this->buildUrl($endpoint);
        return $this->executeRequest(self::POST, $url, $body);
    }

    public function put($endpoint = '', $body = [])
    {
        $url = $this->buildUrl($endpoint);
        return $this->executeRequest(self::PUT, $url, $body);
    }

    public function patch($endpoint = '', $body = [])
    {
        $url = $this->buildUrl($endpoint);
        return $this->executeRequest(self::PATCH, $url, $body);
    }

    public function delete($endpoint = '', $body = [])
    {
        $url = $this->buildUrl($endpoint);
        return $this->executeRequest(self::DELETE, $url, $body);
    }

    public function head($endpoint = '')
    {
        $url = $this->buildUrl($endpoint);
        return $this->executeRequest(self::HEAD, $url);
    }

    public function options($endpoint = '')
    {
        $url = $this->buildUrl($endpoint);
        return $this->executeRequest(self::OPTIONS, $url);
    }

    private function buildUrl($endpoint)
    {
        if (filter_var($endpoint, FILTER_VALIDATE_URL)) {
            return $endpoint;
        }

        if (empty($this->base_url)) {
            return $endpoint;
        }

        $base = rtrim($this->base_url, '/');
        $endpoint = '/' . ltrim($endpoint, '/');
        
        return $base . $endpoint;
    }

    private function executeRequest($method, $url, $body = [])
    {
        if (!$url) {
            $this->last_error = 'URL belirtilmedi';
            return $this->formatResponse(null, false);
        }

        $this->curl = curl_init($url);

        $header_array = [];
        foreach ($this->headers as $key => $value) {
            $header_array[] = "$key: $value";
        }

        curl_setopt_array($this->curl, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $header_array,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        if (!$this->verify_ssl) {
            curl_setopt_array($this->curl, [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);
        }

        curl_setopt_array($this->curl, $this->options);

        if (!empty($body) && in_array($method, [self::POST, self::PUT, self::PATCH, self::DELETE])) {
            if (isset($this->headers['Content-Type'])) {
                if (strpos($this->headers['Content-Type'], 'application/json') !== false) {
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($body));
                } elseif (strpos($this->headers['Content-Type'], 'application/x-www-form-urlencoded') !== false) {
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($body));
                } else {
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);
                }
            } else {
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($body));
            }
        }

        curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, function($curl, $header) {
            $len = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) return $len;
            
            $name = strtolower(trim($header[0]));
            $value = trim($header[1]);
            
            $this->response_headers[$name] = $value;
            return $len;
        });

        $response = curl_exec($this->curl);
        $this->response_code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $this->last_error = curl_error($this->curl);
            return $this->formatResponse(null, false);
        }

        $decoded_response = $this->parseResponse($response);
        
        return $this->formatResponse($decoded_response, true);
    }

    private function parseResponse($response)
    {
        if (empty($response)) {
            return null;
        }

        $json = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }

        return $response;
    }

    private function formatResponse($data, $success)
    {
        return [
            'success' => $success,
            'status_code' => $this->response_code,
            'data' => $data,
            'headers' => $this->response_headers,
            'error' => $this->last_error,
            'message' => $success ? 'İstek başarılı' : ('İstek başarısız: ' . $this->last_error)
        ];
    }

    public function getStatusCode()
    {
        return $this->response_code;
    }

    public function getLastError()
    {
        return $this->last_error;
    }

    public function getResponseHeader($header)
    {
        return isset($this->response_headers[strtolower($header)]) 
            ? $this->response_headers[strtolower($header)] 
            : null;
    }

    public function getResponseHeaders()
    {
        return $this->response_headers;
    }

    public function isSuccess()
    {
        return $this->response_code >= 200 && $this->response_code < 300;
    }
}
