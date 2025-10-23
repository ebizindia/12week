<?php
echo "<h2>OpenSSL Check</h2>";

echo "<h3>Extension Status:</h3>";
if (extension_loaded('openssl')) {
    echo "<p style='color: green;'>✓ OpenSSL extension is loaded</p>";
    
    if (defined('OPENSSL_VERSION_TEXT')) {
        echo "<p>OpenSSL Version: " . OPENSSL_VERSION_TEXT . "</p>";
    }
    
    echo "<h3>Available Functions:</h3>";
    $functions = [
        'openssl_encrypt',
        'openssl_decrypt', 
        'openssl_get_cipher_methods',
        'openssl_random_pseudo_bytes',
        'openssl_cipher_iv_length'
    ];
    
    foreach ($functions as $func) {
        if (function_exists($func)) {
            echo "<p style='color: green;'>✓ {$func}</p>";
        } else {
            echo "<p style='color: red;'>✗ {$func}</p>";
        }
    }
    
    if (function_exists('openssl_get_cipher_methods')) {
        echo "<h3>Available Ciphers (first 10):</h3>";
        $ciphers = openssl_get_cipher_methods();
        echo "<p>Total: " . count($ciphers) . "</p>";
        for ($i = 0; $i < min(10, count($ciphers)); $i++) {
            echo "<p>" . htmlspecialchars($ciphers[$i]) . "</p>";
        }
        
        echo "<h3>Specific Cipher Check:</h3>";
        $testCiphers = ['AES-256-GCM', 'AES-256-CBC', 'aes-256-cbc'];
        foreach ($testCiphers as $cipher) {
            if (in_array($cipher, $ciphers)) {
                echo "<p style='color: green;'>✓ {$cipher}</p>";
            } else {
                echo "<p style='color: red;'>✗ {$cipher}</p>";
            }
        }
    }
    
} else {
    echo "<p style='color: red;'>✗ OpenSSL extension is NOT loaded</p>";
    echo "<p>This is why encryption is not available.</p>";
    echo "<p>Contact your hosting provider to enable the OpenSSL PHP extension.</p>";
}

// Test our Encryption class
echo "<h3>Encryption Class Test:</h3>";
require_once 'cls/Encryption.php';

if (class_exists('\\eBizIndia\\Encryption')) {
    $diagnostics = \eBizIndia\Encryption::getDiagnostics();
    echo "<h4>Diagnostics:</h4>";
    echo "<pre>" . print_r($diagnostics, true) . "</pre>";
    
    if (\eBizIndia\Encryption::isAvailable()) {
        echo "<p style='color: green;'>✓ Encryption class reports AVAILABLE</p>";
        
        // Test encryption/decryption
        $testData = "Hello World Test Data";
        $testUserId = 123;
        
        $encrypted = \eBizIndia\Encryption::encrypt($testData, $testUserId);
        if ($encrypted !== false && !empty($encrypted)) {
            echo "<p style='color: green;'>✓ Encryption successful</p>";
            echo "<p>Encrypted length: " . strlen($encrypted) . "</p>";
            
            $decrypted = \eBizIndia\Encryption::decrypt($encrypted, $testUserId);
            if ($decrypted === $testData) {
                echo "<p style='color: green;'>✓ Decryption successful - ENCRYPTION IS WORKING!</p>";
            } else {
                echo "<p style='color: red;'>✗ Decryption failed</p>";
                echo "<p>Expected: " . htmlspecialchars($testData) . "</p>";
                echo "<p>Got: " . htmlspecialchars($decrypted ?: 'false') . "</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Encryption failed</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Encryption class reports NOT AVAILABLE</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Encryption class not found</p>";
}

echo "<p><a href='goals.php'>← Back to Goals</a></p>";
?>