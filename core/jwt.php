<?php
/**
 * Pure PHP JWT (JSON Web Token) Implementation
 * Zero dependencies - HS256 algorithm only
 */
class jwt
{
    /**
     * Generate JWT token
     * @param array $payload Token data
     * @param string $secret Secret key
     * @param int $expiry Expiry time in seconds (default: 24 hours)
     * @return string JWT token
     */
    public static function encode($payload, $secret, $expiry = 86400)
    {
        // Add standard claims
        $payload['iat'] = time(); // Issued at
        $payload['exp'] = time() + $expiry; // Expiration time
        
        // Create header
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);
        
        // Encode header and payload
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));
        
        // Create signature
        $signature = hash_hmac(
            'sha256',
            $base64UrlHeader . "." . $base64UrlPayload,
            $secret,
            true
        );
        $base64UrlSignature = self::base64UrlEncode($signature);
        
        // Return complete JWT
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
    
    /**
     * Decode and verify JWT token
     * @param string $jwt JWT token
     * @param string $secret Secret key
     * @return array|false Decoded payload or false on error
     */
    public static function decode($jwt, $secret)
    {
        // Split JWT into parts
        $parts = explode('.', $jwt);
        
        if (count($parts) !== 3) {
            return false;
        }
        
        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;
        
        // Verify signature
        $signature = self::base64UrlDecode($base64UrlSignature);
        $expectedSignature = hash_hmac(
            'sha256',
            $base64UrlHeader . "." . $base64UrlPayload,
            $secret,
            true
        );
        
        if (!hash_equals($signature, $expectedSignature)) {
            return false; // Invalid signature
        }
        
        // Decode payload
        $payload = json_decode(self::base64UrlDecode($base64UrlPayload), true);
        
        if (!$payload) {
            return false;
        }
        
        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false; // Token expired
        }
        
        return $payload;
    }
    
    /**
     * Verify JWT token without decoding
     * @param string $jwt JWT token
     * @param string $secret Secret key
     * @return bool True if valid, false otherwise
     */
    public static function verify($jwt, $secret)
    {
        return self::decode($jwt, $secret) !== false;
    }
    
    /**
     * Get JWT from Authorization header
     * @return string|null JWT token or null
     */
    public static function getFromHeader()
    {
        $headers = getallheaders();
        
        if (isset($headers['Authorization'])) {
            // Bearer token format: "Bearer <token>"
            if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
            return $headers['Authorization'];
        }
        
        return null;
    }
    
    /**
     * Get JWT from cookie
     * @param string $cookieName Cookie name (default: 'jwt_token')
     * @return string|null JWT token or null
     */
    public static function getFromCookie($cookieName = 'jwt_token')
    {
        return $_COOKIE[$cookieName] ?? null;
    }
    
    /**
     * Base64 URL encode
     * @param string $data Data to encode
     * @return string Encoded string
     */
    private static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL decode
     * @param string $data Data to decode
     * @return string Decoded string
     */
    private static function base64UrlDecode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
    
    /**
     * Create refresh token
     * @param int $length Token length (default: 64)
     * @return string Random token
     */
    public static function createRefreshToken($length = 64)
    {
        return bin2hex(random_bytes($length / 2));
    }
}
