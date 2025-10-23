<?php
namespace eBizIndia;

/**
 * Encryption utility class for sensitive data
 * Uses AES-256-GCM encryption for secure data storage
 */
class Encryption {
    
    private static $preferredCiphers = [
        'aes-256-cbc',      // Your server supports this one
        'aes-256-gcm',
        'aes-128-cbc',
        'AES-256-GCM',
        'AES-256-CBC',
        'AES-128-CBC'
    ];
    private static $keyLength = 32; // 256 bits
    
    /**
     * Get the best available cipher
     * @return string|false
     */
    private static function getBestCipher() {
        if (!extension_loaded('openssl')) {
            return false;
        }
        
        $availableCiphers = openssl_get_cipher_methods();
        foreach (self::$preferredCiphers as $cipher) {
            if (in_array($cipher, $availableCiphers)) {
                return $cipher;
            }
        }
        return false;
    }
    
    /**
     * Generate encryption key based on user ID and system secret
     * @param int $userId
     * @return string
     */
    private static function generateKey($userId) {
        $systemSecret = CONST_SECRET_ACCESS_KEY;
        $userSalt = hash('sha256', $userId . '_goals_encryption');
        return hash('sha256', $systemSecret . $userSalt, true);
    }
    
    /**
     * Generate shared encryption key for meetings module
     * @return string
     */
    private static function generateMeetingsKey() {
        $systemSecret = CONST_SECRET_ACCESS_KEY;
        $modulesSalt = hash('sha256', 'meetings_shared_encryption');
        return hash('sha256', $systemSecret . $modulesSalt, true);
    }
    
    /**
     * Generate shared encryption key for any module
     * @param string $moduleName
     * @return string
     */
    private static function generateSharedKey($moduleName) {
        $systemSecret = CONST_SECRET_ACCESS_KEY;
        $modulesSalt = hash('sha256', $moduleName . '_shared_encryption');
        return hash('sha256', $systemSecret . $modulesSalt, true);
    }
    
    /**
     * Encrypt sensitive text data for meetings (shared access)
     * @param string $plaintext
     * @return string|false Returns encrypted string or false on failure
     */
    public static function encryptMeetings($plaintext) {
        if (empty($plaintext)) {
            return '';
        }
        
        $cipher = self::getBestCipher();
        if ($cipher === false) {
            return false;
        }
        
        try {
            $key = self::generateMeetingsKey();
            $ivLength = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($ivLength);
            
            // Check if cipher supports GCM mode
            if (strpos(strtoupper($cipher), 'GCM') !== false) {
                $tag = '';
                $encrypted = openssl_encrypt(
                    $plaintext,
                    $cipher,
                    $key,
                    OPENSSL_RAW_DATA,
                    $iv,
                    $tag
                );
                
                if ($encrypted === false) {
                    throw new \Exception('GCM Encryption failed');
                }
                
                // For GCM: IV + tag + encrypted data + cipher identifier
                return base64_encode('GCM:' . $cipher . ':' . $iv . $tag . $encrypted);
            } else {
                // CBC mode
                $encrypted = openssl_encrypt(
                    $plaintext,
                    $cipher,
                    $key,
                    OPENSSL_RAW_DATA,
                    $iv
                );
                
                if ($encrypted === false) {
                    throw new \Exception('CBC Encryption failed');
                }
                
                // For CBC: IV + encrypted data + cipher identifier
                return base64_encode('CBC:' . $cipher . ':' . $iv . $encrypted);
            }
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'Encryption::encryptMeetings',
                'cipher' => $cipher ?? 'unknown',
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Decrypt sensitive text data for meetings (shared access)
     * @param string $encryptedData
     * @return string|false Returns decrypted text or false on failure
     */
    public static function decryptMeetings($encryptedData) {
        if (empty($encryptedData)) {
            return '';
        }
        
        try {
            $key = self::generateMeetingsKey();
            $data = base64_decode($encryptedData);
            
            if ($data === false) {
                throw new \Exception('Invalid base64 data');
            }
            
            // Parse cipher information from encrypted data
            if (strpos($data, ':') !== false) {
                $parts = explode(':', $data, 3);
                if (count($parts) >= 3) {
                    $mode = $parts[0];
                    $cipher = $parts[1];
                    $payload = $parts[2];
                    
                    if ($mode === 'GCM') {
                        $ivLength = openssl_cipher_iv_length($cipher);
                        if (strlen($payload) < $ivLength + 16) {
                            throw new \Exception('Invalid GCM payload length');
                        }
                        
                        $iv = substr($payload, 0, $ivLength);
                        $tag = substr($payload, $ivLength, 16);
                        $encrypted = substr($payload, $ivLength + 16);
                        
                        $decrypted = openssl_decrypt(
                            $encrypted,
                            $cipher,
                            $key,
                            OPENSSL_RAW_DATA,
                            $iv,
                            $tag
                        );
                    } elseif ($mode === 'CBC') {
                        $ivLength = openssl_cipher_iv_length($cipher);
                        if (strlen($payload) < $ivLength) {
                            throw new \Exception('Invalid CBC payload length');
                        }
                        
                        $iv = substr($payload, 0, $ivLength);
                        $encrypted = substr($payload, $ivLength);
                        
                        $decrypted = openssl_decrypt(
                            $encrypted,
                            $cipher,
                            $key,
                            OPENSSL_RAW_DATA,
                            $iv
                        );
                    } else {
                        throw new \Exception('Unknown encryption mode: ' . $mode);
                    }
                } else {
                    throw new \Exception('Invalid encrypted data format');
                }
            } else {
                throw new \Exception('Invalid encrypted data format - missing cipher information');
            }
            
            if ($decrypted === false) {
                throw new \Exception('Decryption failed');
            }
            
            return $decrypted;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'Encryption::decryptMeetings',
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Encrypt sensitive text data
     * @param string $plaintext
     * @param int $userId
     * @return string|false Returns encrypted string or false on failure
     */
    public static function encrypt($plaintext, $userId) {
        if (empty($plaintext)) {
            return '';
        }
        
        $cipher = self::getBestCipher();
        if ($cipher === false) {
            return false;
        }
        
        try {
            $key = self::generateKey($userId);
            $ivLength = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($ivLength);
            
            // Check if cipher supports GCM mode
            if (strpos(strtoupper($cipher), 'GCM') !== false) {
                $tag = '';
                $encrypted = openssl_encrypt(
                    $plaintext,
                    $cipher,
                    $key,
                    OPENSSL_RAW_DATA,
                    $iv,
                    $tag
                );
                
                if ($encrypted === false) {
                    throw new \Exception('GCM Encryption failed');
                }
                
                // For GCM: IV + tag + encrypted data + cipher identifier
                return base64_encode('GCM:' . $cipher . ':' . $iv . $tag . $encrypted);
            } else {
                // CBC mode
                $encrypted = openssl_encrypt(
                    $plaintext,
                    $cipher,
                    $key,
                    OPENSSL_RAW_DATA,
                    $iv
                );
                
                if ($encrypted === false) {
                    throw new \Exception('CBC Encryption failed');
                }
                
                // For CBC: IV + encrypted data + cipher identifier
                return base64_encode('CBC:' . $cipher . ':' . $iv . $encrypted);
            }
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'Encryption::encrypt',
                'user_id' => $userId,
                'cipher' => $cipher ?? 'unknown',
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Decrypt sensitive text data
     * @param string $encryptedData
     * @param int $userId
     * @return string|false Returns decrypted text or false on failure
     */
    public static function decrypt($encryptedData, $userId) {
        if (empty($encryptedData)) {
            return '';
        }
        
        try {
            $key = self::generateKey($userId);
            $data = base64_decode($encryptedData);
            
            if ($data === false) {
                throw new \Exception('Invalid base64 data');
            }
            
            // Check if data has cipher information (new format)
            if (strpos($data, ':') !== false) {
                $parts = explode(':', $data, 3);
                if (count($parts) >= 3) {
                    $mode = $parts[0];
                    $cipher = $parts[1];
                    $payload = $parts[2];
                    
                    if ($mode === 'GCM') {
                        $ivLength = openssl_cipher_iv_length($cipher);
                        if (strlen($payload) < $ivLength + 16) {
                            throw new \Exception('Invalid GCM payload length');
                        }
                        
                        $iv = substr($payload, 0, $ivLength);
                        $tag = substr($payload, $ivLength, 16);
                        $encrypted = substr($payload, $ivLength + 16);
                        
                        $decrypted = openssl_decrypt(
                            $encrypted,
                            $cipher,
                            $key,
                            OPENSSL_RAW_DATA,
                            $iv,
                            $tag
                        );
                    } elseif ($mode === 'CBC') {
                        $ivLength = openssl_cipher_iv_length($cipher);
                        if (strlen($payload) < $ivLength) {
                            throw new \Exception('Invalid CBC payload length');
                        }
                        
                        $iv = substr($payload, 0, $ivLength);
                        $encrypted = substr($payload, $ivLength);
                        
                        $decrypted = openssl_decrypt(
                            $encrypted,
                            $cipher,
                            $key,
                            OPENSSL_RAW_DATA,
                            $iv
                        );
                    } else {
                        throw new \Exception('Unknown encryption mode: ' . $mode);
                    }
                } else {
                    throw new \Exception('Invalid encrypted data format');
                }
            } else {
                // Legacy format - assume AES-256-GCM with fixed structure
                if (strlen($data) < 32) {
                    throw new \Exception('Invalid legacy encrypted data length');
                }
                
                $iv = substr($data, 0, 16);
                $tag = substr($data, 16, 16);
                $encrypted = substr($data, 32);
                
                // Try with available GCM cipher
                $cipher = self::getBestCipher();
                if ($cipher && strpos(strtoupper($cipher), 'GCM') !== false) {
                    $decrypted = openssl_decrypt(
                        $encrypted,
                        $cipher,
                        $key,
                        OPENSSL_RAW_DATA,
                        $iv,
                        $tag
                    );
                } else {
                    throw new \Exception('No compatible cipher for legacy data');
                }
            }
            
            if ($decrypted === false) {
                throw new \Exception('Decryption failed');
            }
            
            return $decrypted;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'Encryption::decrypt',
                'user_id' => $userId,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Check if OpenSSL extension is available
     * @return bool
     */
    public static function isAvailable() {
        return self::getBestCipher() !== false;
    }
    
    /**
     * Get diagnostic information about encryption availability
     * @return array
     */
    public static function getDiagnostics() {
        $diagnostics = [
            'openssl_loaded' => extension_loaded('openssl'),
            'available_ciphers' => [],
            'best_cipher' => false,
            'functions_available' => [
                'openssl_encrypt' => function_exists('openssl_encrypt'),
                'openssl_decrypt' => function_exists('openssl_decrypt'),
                'openssl_get_cipher_methods' => function_exists('openssl_get_cipher_methods'),
                'openssl_random_pseudo_bytes' => function_exists('openssl_random_pseudo_bytes'),
            ]
        ];
        
        if ($diagnostics['openssl_loaded'] && function_exists('openssl_get_cipher_methods')) {
            $allCiphers = openssl_get_cipher_methods();
            foreach (self::$preferredCiphers as $cipher) {
                $diagnostics['available_ciphers'][$cipher] = in_array($cipher, $allCiphers);
            }
            $diagnostics['best_cipher'] = self::getBestCipher();
        }
        
        return $diagnostics;
    }
    
    /**
     * Encrypt sensitive text data for any module (shared access)
     * @param string $plaintext
     * @param string $moduleName
     * @return string|false Returns encrypted string or false on failure
     */
    public static function encryptShared($plaintext, $moduleName) {
        if (empty($plaintext)) {
            return '';
        }
        
        $cipher = self::getBestCipher();
        if ($cipher === false) {
            return false;
        }
        
        try {
            $key = self::generateSharedKey($moduleName);
            $ivLength = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($ivLength);
            
            // Check if cipher supports GCM mode
            if (strpos(strtoupper($cipher), 'GCM') !== false) {
                $tag = '';
                $encrypted = openssl_encrypt(
                    $plaintext,
                    $cipher,
                    $key,
                    OPENSSL_RAW_DATA,
                    $iv,
                    $tag
                );
                
                if ($encrypted === false) {
                    throw new \Exception('GCM Encryption failed');
                }
                
                // For GCM: IV + tag + encrypted data + cipher identifier
                return base64_encode('GCM:' . $cipher . ':' . $iv . $tag . $encrypted);
            } else {
                // CBC mode
                $encrypted = openssl_encrypt(
                    $plaintext,
                    $cipher,
                    $key,
                    OPENSSL_RAW_DATA,
                    $iv
                );
                
                if ($encrypted === false) {
                    throw new \Exception('CBC Encryption failed');
                }
                
                // For CBC: IV + encrypted data + cipher identifier
                return base64_encode('CBC:' . $cipher . ':' . $iv . $encrypted);
            }
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'Encryption::encryptShared',
                'module' => $moduleName,
                'cipher' => $cipher ?? 'unknown',
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Decrypt sensitive text data for any module (shared access)
     * @param string $encryptedData
     * @param string $moduleName
     * @return string|false Returns decrypted text or false on failure
     */
    public static function decryptShared($encryptedData, $moduleName) {
        if (empty($encryptedData)) {
            return '';
        }
        
        try {
            $key = self::generateSharedKey($moduleName);
            $data = base64_decode($encryptedData);
            
            if ($data === false) {
                throw new \Exception('Invalid base64 data');
            }
            
            // Parse cipher information from encrypted data
            if (strpos($data, ':') !== false) {
                $parts = explode(':', $data, 3);
                if (count($parts) >= 3) {
                    $mode = $parts[0];
                    $cipher = $parts[1];
                    $payload = $parts[2];
                    
                    if ($mode === 'GCM') {
                        $ivLength = openssl_cipher_iv_length($cipher);
                        if (strlen($payload) < $ivLength + 16) {
                            throw new \Exception('Invalid GCM payload length');
                        }
                        
                        $iv = substr($payload, 0, $ivLength);
                        $tag = substr($payload, $ivLength, 16);
                        $encrypted = substr($payload, $ivLength + 16);
                        
                        $decrypted = openssl_decrypt(
                            $encrypted,
                            $cipher,
                            $key,
                            OPENSSL_RAW_DATA,
                            $iv,
                            $tag
                        );
                    } elseif ($mode === 'CBC') {
                        $ivLength = openssl_cipher_iv_length($cipher);
                        if (strlen($payload) < $ivLength) {
                            throw new \Exception('Invalid CBC payload length');
                        }
                        
                        $iv = substr($payload, 0, $ivLength);
                        $encrypted = substr($payload, $ivLength);
                        
                        $decrypted = openssl_decrypt(
                            $encrypted,
                            $cipher,
                            $key,
                            OPENSSL_RAW_DATA,
                            $iv
                        );
                    } else {
                        throw new \Exception('Unknown encryption mode: ' . $mode);
                    }
                } else {
                    throw new \Exception('Invalid encrypted data format');
                }
            } else {
                throw new \Exception('Invalid encrypted data format - missing cipher information');
            }
            
            if ($decrypted === false) {
                throw new \Exception('Decryption failed');
            }
            
            return $decrypted;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'Encryption::decryptShared',
                'module' => $moduleName,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Check if data appears to be encrypted (base64 encoded with sufficient length)
     * @param string $data
     * @return bool
     */
    public static function isEncrypted($data) {
        if (empty($data)) {
            return false;
        }
        
        // Check if it's valid base64 and has minimum length for encrypted data
        $decoded = base64_decode($data, true);
        return $decoded !== false && strlen($decoded) >= 32;
    }
}