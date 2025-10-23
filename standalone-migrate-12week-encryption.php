<?php
/**
 * Standalone Migration Script for 12-Week System Encryption
 * This script runs independently without requiring menu permissions
 * It directly uses database configuration from config.php
 */

// Set execution time limit for large datasets
set_time_limit(300); // 5 minutes

// Include config.php to get database credentials
require_once 'config.php';

// Check if we have the required database configuration
if (!defined('CONST_DB_CREDS') || !isset(CONST_DB_CREDS['mysql'])) {
    die("❌ Database configuration not found in config.php");
}

$db_config = CONST_DB_CREDS['mysql'];

?>
<!DOCTYPE html>
<html>
<head>
    <title>12-Week System Encryption Migration (Standalone)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        .stats { background: #f5f5f5; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .progress { margin: 10px 0; }
        .config-info { background: #e8f4f8; padding: 10px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>

<h1>12-Week System Encryption Migration (Standalone)</h1>

<?php
$startTime = microtime(true);
$stats = [
    'goals_processed' => 0,
    'goals_encrypted' => 0,
    'goals_failed' => 0,
    'tasks_processed' => 0,
    'tasks_encrypted' => 0,
    'tasks_failed' => 0
];

echo "<div class='info'>Starting migration at " . date('Y-m-d H:i:s') . "</div>";

// Display database configuration (without password)
echo "<div class='config-info'>";
echo "<strong>Database Configuration:</strong><br>";
echo "Host: " . $db_config['host'] . "<br>";
echo "Port: " . $db_config['port'] . "<br>";
echo "Database: " . $db_config['db'] . "<br>";
echo "User: " . $db_config['user'] . "<br>";
echo "</div>";

try {
    // Create PDO connection using config.php credentials
    $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['db']};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_config['user'], $db_config['pswd'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    
    echo "<div class='success'>✓ Database connection established</div>";
    
    // Check if OpenSSL extension is available
    if (!extension_loaded('openssl')) {
        echo "<div class='error'>❌ OpenSSL extension is not available. Encryption cannot proceed.</div>";
        exit;
    }
    
    echo "<div class='success'>✓ OpenSSL extension is available</div>";
    
    // Check if encryption columns exist
    $check_goals_columns = $pdo->query("SHOW COLUMNS FROM goals LIKE 'is_encrypted'");
    $check_tasks_columns = $pdo->query("SHOW COLUMNS FROM tasks LIKE 'is_encrypted'");
    
    if ($check_goals_columns->rowCount() == 0 || $check_tasks_columns->rowCount() == 0) {
        echo "<div class='error'>❌ Encryption columns not found. Please run the SQL schema updates first:</div>";
        echo "<div class='info'>Execute: <code>mysql -u {$db_config['user']} -p {$db_config['db']} < add-encryption-columns.sql</code></div>";
        exit;
    }
    
    echo "<div class='success'>✓ Encryption columns found in database</div>";
    
    // Define encryption functions (simplified versions from Encryption.php)
    function getBestCipher() {
        $preferredCiphers = ['aes-256-cbc', 'aes-256-gcm', 'aes-128-cbc'];
        $availableCiphers = openssl_get_cipher_methods();
        foreach ($preferredCiphers as $cipher) {
            if (in_array($cipher, $availableCiphers)) {
                return $cipher;
            }
        }
        return false;
    }
    
    function generateSharedKey($moduleName) {
        $systemSecret = defined('CONST_SECRET_ACCESS_KEY') ? CONST_SECRET_ACCESS_KEY : 'default_secret_key';
        $modulesSalt = hash('sha256', $moduleName . '_shared_encryption');
        return hash('sha256', $systemSecret . $modulesSalt, true);
    }
    
    function encryptShared($plaintext, $moduleName) {
        if (empty($plaintext)) {
            return '';
        }
        
        $cipher = getBestCipher();
        if ($cipher === false) {
            return false;
        }
        
        try {
            $key = generateSharedKey($moduleName);
            $ivLength = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($ivLength);
            
            if (strpos(strtoupper($cipher), 'GCM') !== false) {
                $tag = '';
                $encrypted = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);
                if ($encrypted === false) {
                    throw new Exception('GCM Encryption failed');
                }
                return base64_encode('GCM:' . $cipher . ':' . $iv . $tag . $encrypted);
            } else {
                $encrypted = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
                if ($encrypted === false) {
                    throw new Exception('CBC Encryption failed');
                }
                return base64_encode('CBC:' . $cipher . ':' . $iv . $encrypted);
            }
        } catch (Exception $e) {
            error_log("Encryption error: " . $e->getMessage());
            return false;
        }
    }
    
    echo "<div class='success'>✓ Encryption functions loaded</div>";
    
    echo "<h2>Phase 1: Migrating Goals</h2>";
    
    // Get all goals that are not encrypted
    $goals_stmt = $pdo->query("SELECT id, title, user_id FROM goals WHERE is_encrypted = 0 OR is_encrypted IS NULL");
    $goals = $goals_stmt->fetchAll();
    
    echo "<div class='info'>Found " . count($goals) . " goals to encrypt</div>";
    
    if (count($goals) > 0) {
        echo "<div class='progress'>Processing goals:</div>";
        
        foreach ($goals as $goal) {
            $stats['goals_processed']++;
            
            try {
                if (!empty($goal['title'])) {
                    $encrypted = encryptShared($goal['title'], 'twelve_week_goals');
                    
                    if ($encrypted !== false) {
                        $update_stmt = $pdo->prepare("UPDATE goals SET 
                                                     title = :title, 
                                                     is_encrypted = 1, 
                                                     encryption_key_id = :key_id,
                                                     updated_at = CURRENT_TIMESTAMP
                                                     WHERE id = :id");
                        
                        $result = $update_stmt->execute([
                            ':title' => $encrypted,
                            ':key_id' => 'twelve_week_goals_shared_' . date('Ym'),
                            ':id' => $goal['id']
                        ]);
                        
                        if ($result) {
                            $stats['goals_encrypted']++;
                            echo "<span class='success'>✓</span> ";
                        } else {
                            $stats['goals_failed']++;
                            echo "<span class='error'>✗</span> ";
                        }
                    } else {
                        $stats['goals_failed']++;
                        echo "<span class='error'>✗</span> ";
                        error_log("Failed to encrypt goal ID: {$goal['id']} - encryption returned false");
                    }
                } else {
                    // Empty title, just mark as processed
                    $update_stmt = $pdo->prepare("UPDATE goals SET is_encrypted = 0 WHERE id = :id");
                    $update_stmt->execute([':id' => $goal['id']]);
                    echo "<span class='warning'>○</span> ";
                }
                
                // Flush output every 50 goals
                if ($stats['goals_processed'] % 50 == 0) {
                    echo "<br>Processed {$stats['goals_processed']} goals...<br>";
                    flush();
                }
                
            } catch (Exception $e) {
                $stats['goals_failed']++;
                echo "<span class='error'>✗</span> ";
                error_log("Error encrypting goal ID {$goal['id']}: " . $e->getMessage());
            }
        }
        
        echo "<br>";
    }
    
    echo "<h2>Phase 2: Migrating Tasks</h2>";
    
    // Get all tasks that are not encrypted
    $tasks_stmt = $pdo->query("SELECT id, title, goal_id FROM tasks WHERE is_encrypted = 0 OR is_encrypted IS NULL");
    $tasks = $tasks_stmt->fetchAll();
    
    echo "<div class='info'>Found " . count($tasks) . " tasks to encrypt</div>";
    
    if (count($tasks) > 0) {
        echo "<div class='progress'>Processing tasks:</div>";
        
        foreach ($tasks as $task) {
            $stats['tasks_processed']++;
            
            try {
                if (!empty($task['title'])) {
                    $encrypted = encryptShared($task['title'], 'twelve_week_tasks');
                    
                    if ($encrypted !== false) {
                        $update_stmt = $pdo->prepare("UPDATE tasks SET 
                                                     title = :title, 
                                                     is_encrypted = 1, 
                                                     encryption_key_id = :key_id,
                                                     updated_at = CURRENT_TIMESTAMP
                                                     WHERE id = :id");
                        
                        $result = $update_stmt->execute([
                            ':title' => $encrypted,
                            ':key_id' => 'twelve_week_tasks_shared_' . date('Ym'),
                            ':id' => $task['id']
                        ]);
                        
                        if ($result) {
                            $stats['tasks_encrypted']++;
                            echo "<span class='success'>✓</span> ";
                        } else {
                            $stats['tasks_failed']++;
                            echo "<span class='error'>✗</span> ";
                        }
                    } else {
                        $stats['tasks_failed']++;
                        echo "<span class='error'>✗</span> ";
                        error_log("Failed to encrypt task ID: {$task['id']} - encryption returned false");
                    }
                } else {
                    // Empty title, just mark as processed
                    $update_stmt = $pdo->prepare("UPDATE tasks SET is_encrypted = 0 WHERE id = :id");
                    $update_stmt->execute([':id' => $task['id']]);
                    echo "<span class='warning'>○</span> ";
                }
                
                // Flush output every 50 tasks
                if ($stats['tasks_processed'] % 50 == 0) {
                    echo "<br>Processed {$stats['tasks_processed']} tasks...<br>";
                    flush();
                }
                
            } catch (Exception $e) {
                $stats['tasks_failed']++;
                echo "<span class='error'>✗</span> ";
                error_log("Error encrypting task ID {$task['id']}: " . $e->getMessage());
            }
        }
        
        echo "<br>";
    }
    
    $endTime = microtime(true);
    $executionTime = round($endTime - $startTime, 2);
    
    echo "<h2>Migration Summary</h2>";
    echo "<div class='stats'>";
    echo "<strong>Goals:</strong><br>";
    echo "• Processed: {$stats['goals_processed']}<br>";
    echo "• Successfully encrypted: <span class='success'>{$stats['goals_encrypted']}</span><br>";
    echo "• Failed: <span class='error'>{$stats['goals_failed']}</span><br><br>";
    
    echo "<strong>Tasks:</strong><br>";
    echo "• Processed: {$stats['tasks_processed']}<br>";
    echo "• Successfully encrypted: <span class='success'>{$stats['tasks_encrypted']}</span><br>";
    echo "• Failed: <span class='error'>{$stats['tasks_failed']}</span><br><br>";
    
    echo "<strong>Total execution time:</strong> {$executionTime} seconds<br>";
    echo "<strong>Completed at:</strong> " . date('Y-m-d H:i:s');
    echo "</div>";
    
    if ($stats['goals_failed'] == 0 && $stats['tasks_failed'] == 0) {
        echo "<div class='success'><h3>✅ Migration completed successfully!</h3></div>";
        echo "<div class='info'>All existing goals and tasks have been encrypted. New data will be automatically encrypted going forward.</div>";
    } else {
        echo "<div class='warning'><h3>⚠️ Migration completed with some failures</h3></div>";
        echo "<div class='info'>Check the error log for details about failed encryptions. You may need to re-run the migration or manually fix the failed records.</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='error'><h3>❌ Database connection failed:</h3>";
    echo "Error: " . $e->getMessage() . "</div>";
    echo "<div class='info'>Please check your database configuration in config.php</div>";
} catch (Exception $e) {
    echo "<div class='error'><h3>❌ Migration failed with error:</h3>";
    echo $e->getMessage() . "</div>";
    error_log("Migration script error: " . $e->getMessage());
}

?>

<h2>Next Steps</h2>
<div class='info'>
<ol>
    <li>Verify the migration results by checking a few goals and tasks in the application</li>
    <li>Test the encryption/decryption functionality using the test script</li>
    <li>Monitor application performance after the changes</li>
    <li>Check error logs for any issues</li>
</ol>
</div>

<h2>Verification Queries</h2>
<div class='info'>
<p>You can run these SQL queries to verify the migration:</p>
<pre>
-- Check encrypted goals
SELECT COUNT(*) as encrypted_goals FROM goals WHERE is_encrypted = 1;

-- Check encrypted tasks  
SELECT COUNT(*) as encrypted_tasks FROM tasks WHERE is_encrypted = 1;

-- Check unencrypted goals
SELECT COUNT(*) as unencrypted_goals FROM goals WHERE is_encrypted = 0 OR is_encrypted IS NULL;

-- Check unencrypted tasks
SELECT COUNT(*) as unencrypted_tasks FROM tasks WHERE is_encrypted = 0 OR is_encrypted IS NULL;
</pre>
</div>

<h2>Rollback Instructions</h2>
<div class='warning'>
<p><strong>If you need to rollback this migration:</strong></p>
<ol>
    <li>Restore from your pre-migration database backup</li>
    <li>Or manually decrypt the data using the decryption functions</li>
    <li>Remove the encryption columns if needed</li>
</ol>
</div>

</body>
</html>