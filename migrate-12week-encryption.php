<?php
/**
 * Migration script to encrypt existing 12-week goals and tasks data
 * This script will encrypt all existing unencrypted goals and tasks
 */

// Set execution time limit for large datasets
set_time_limit(300); // 5 minutes

require_once 'inc.php';

// Check if user has admin access
if (!in_array('ALL', $allowed_menu_perms) && !in_array('ADMIN', $allowed_menu_perms)) {
    die("Access denied. Admin permissions required.");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>12-Week System Encryption Migration</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        .stats { background: #f5f5f5; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .progress { margin: 10px 0; }
    </style>
</head>
<body>

<h1>12-Week System Encryption Migration</h1>

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

// Check if encryption is available
if (!\eBizIndia\Encryption::isAvailable()) {
    echo "<div class='error'>❌ Encryption is not available on this system. Please check OpenSSL extension.</div>";
    exit;
}

echo "<div class='success'>✓ Encryption system is available</div>";

try {
    $conn = \eBizIndia\PDOConn::getInstance();
    
    echo "<h2>Phase 1: Migrating Goals</h2>";
    
    // Get all goals that are not encrypted
    $goals_sql = "SELECT id, title, user_id FROM goals WHERE is_encrypted = 0 OR is_encrypted IS NULL";
    $goals_stmt = $conn->query($goals_sql);
    $goals = $goals_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='info'>Found " . count($goals) . " goals to encrypt</div>";
    
    if (count($goals) > 0) {
        echo "<div class='progress'>Processing goals:</div>";
        
        foreach ($goals as $goal) {
            $stats['goals_processed']++;
            
            try {
                if (!empty($goal['title'])) {
                    $encrypted = \eBizIndia\Encryption::encryptShared($goal['title'], 'twelve_week_goals');
                    
                    if ($encrypted !== false) {
                        $update_sql = "UPDATE goals SET 
                                      title = :title, 
                                      is_encrypted = 1, 
                                      encryption_key_id = :key_id,
                                      updated_at = CURRENT_TIMESTAMP
                                      WHERE id = :id";
                        
                        $update_stmt = $conn->prepare($update_sql);
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
                    $update_sql = "UPDATE goals SET is_encrypted = 0 WHERE id = :id";
                    $update_stmt = $conn->prepare($update_sql);
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
    $tasks_sql = "SELECT id, title, goal_id FROM tasks WHERE is_encrypted = 0 OR is_encrypted IS NULL";
    $tasks_stmt = $conn->query($tasks_sql);
    $tasks = $tasks_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='info'>Found " . count($tasks) . " tasks to encrypt</div>";
    
    if (count($tasks) > 0) {
        echo "<div class='progress'>Processing tasks:</div>";
        
        foreach ($tasks as $task) {
            $stats['tasks_processed']++;
            
            try {
                if (!empty($task['title'])) {
                    $encrypted = \eBizIndia\Encryption::encryptShared($task['title'], 'twelve_week_tasks');
                    
                    if ($encrypted !== false) {
                        $update_sql = "UPDATE tasks SET 
                                      title = :title, 
                                      is_encrypted = 1, 
                                      encryption_key_id = :key_id,
                                      updated_at = CURRENT_TIMESTAMP
                                      WHERE id = :id";
                        
                        $update_stmt = $conn->prepare($update_sql);
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
                    $update_sql = "UPDATE tasks SET is_encrypted = 0 WHERE id = :id";
                    $update_stmt = $conn->prepare($update_sql);
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
    <li>Update your application files to use the new TwelveWeekGoals and TwelveWeekTasks classes</li>
    <li>Test the encryption/decryption functionality thoroughly</li>
    <li>Monitor application performance after the changes</li>
</ol>
</div>

<h2>Rollback Instructions</h2>
<div class='warning'>
<p><strong>If you need to rollback this migration:</strong></p>
<ol>
    <li>Backup your current database</li>
    <li>Run the following SQL to decrypt and restore original data (if you have backups)</li>
    <li>Or restore from your pre-migration database backup</li>
</ol>
</div>

</body>
</html>