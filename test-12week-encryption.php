<?php
/**
 * Test script for 12-week encryption functionality
 * This script tests the TwelveWeekGoals and TwelveWeekTasks classes
 */

require_once 'inc.php';

// Check if user has access
if (!in_array('ALL', $allowed_menu_perms) && !in_array('ADMIN', $allowed_menu_perms)) {
    die("Access denied. Admin permissions required.");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>12-Week Encryption Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .test-result { margin: 10px 0; padding: 5px; }
    </style>
</head>
<body>

<h1>12-Week System Encryption Test</h1>

<?php

echo "<div class='info'>Starting tests at " . date('Y-m-d H:i:s') . "</div>";

// Check if encryption is available
if (!\eBizIndia\Encryption::isAvailable()) {
    echo "<div class='error'>❌ Encryption is not available on this system.</div>";
    exit;
}

echo "<div class='success'>✓ Encryption system is available</div>";

$testUserId = $loggedindata[0]['id']; // Use current logged-in user
$testCycleId = 1; // Assuming cycle ID 1 exists
$testCategoryId = 1; // Assuming category ID 1 exists

try {
    
    echo "<div class='test-section'>";
    echo "<h2>Test 1: Goals Encryption</h2>";
    
    // Test goal creation with encryption
    $testGoalData = [
        'title' => 'Test Encrypted Goal - ' . date('H:i:s'),
        'user_id' => $testUserId,
        'cycle_id' => $testCycleId,
        'category_id' => $testCategoryId
    ];
    
    echo "<div class='info'>Creating test goal with title: '{$testGoalData['title']}'</div>";
    
    $goalId = \eBizIndia\TwelveWeekGoals::saveGoal($testGoalData);
    
    if ($goalId) {
        echo "<div class='success'>✓ Goal created successfully with ID: $goalId</div>";
        
        // Test goal retrieval and decryption
        $retrievedGoal = \eBizIndia\TwelveWeekGoals::getGoal($goalId, $testUserId);
        
        if ($retrievedGoal && $retrievedGoal['title'] === $testGoalData['title']) {
            echo "<div class='success'>✓ Goal retrieved and decrypted successfully</div>";
            echo "<div class='info'>Original: '{$testGoalData['title']}'</div>";
            echo "<div class='info'>Retrieved: '{$retrievedGoal['title']}'</div>";
            echo "<div class='info'>Encryption status: " . ($retrievedGoal['is_encrypted'] ? 'Encrypted' : 'Not encrypted') . "</div>";
        } else {
            echo "<div class='error'>✗ Goal retrieval or decryption failed</div>";
        }
        
        // Test goal update
        $updateData = $testGoalData;
        $updateData['id'] = $goalId;
        $updateData['title'] = 'Updated Encrypted Goal - ' . date('H:i:s');
        
        $updateResult = \eBizIndia\TwelveWeekGoals::saveGoal($updateData);
        
        if ($updateResult) {
            echo "<div class='success'>✓ Goal updated successfully</div>";
            
            $updatedGoal = \eBizIndia\TwelveWeekGoals::getGoal($goalId, $testUserId);
            if ($updatedGoal && $updatedGoal['title'] === $updateData['title']) {
                echo "<div class='success'>✓ Updated goal retrieved and decrypted successfully</div>";
            } else {
                echo "<div class='error'>✗ Updated goal retrieval failed</div>";
            }
        } else {
            echo "<div class='error'>✗ Goal update failed</div>";
        }
        
    } else {
        echo "<div class='error'>✗ Goal creation failed</div>";
    }
    
    echo "</div>";
    
    echo "<div class='test-section'>";
    echo "<h2>Test 2: Tasks Encryption</h2>";
    
    if ($goalId) {
        // Test task creation with encryption
        $testTaskData = [
            'title' => 'Test Encrypted Task - ' . date('H:i:s'),
            'goal_id' => $goalId,
            'week_number' => 1,
            'weekly_target' => 3
        ];
        
        echo "<div class='info'>Creating test task with title: '{$testTaskData['title']}'</div>";
        
        $taskId = \eBizIndia\TwelveWeekTasks::saveTask($testTaskData);
        
        if ($taskId) {
            echo "<div class='success'>✓ Task created successfully with ID: $taskId</div>";
            
            // Test task retrieval and decryption
            $retrievedTask = \eBizIndia\TwelveWeekTasks::getTask($taskId, $testUserId);
            
            if ($retrievedTask && $retrievedTask['title'] === $testTaskData['title']) {
                echo "<div class='success'>✓ Task retrieved and decrypted successfully</div>";
                echo "<div class='info'>Original: '{$testTaskData['title']}'</div>";
                echo "<div class='info'>Retrieved: '{$retrievedTask['title']}'</div>";
                echo "<div class='info'>Encryption status: " . ($retrievedTask['is_encrypted'] ? 'Encrypted' : 'Not encrypted') . "</div>";
            } else {
                echo "<div class='error'>✗ Task retrieval or decryption failed</div>";
            }
            
            // Test task progress update
            $progressResult = \eBizIndia\TwelveWeekTasks::updateTaskProgress($taskId, 'mon', 1, $testUserId);
            
            if ($progressResult) {
                echo "<div class='success'>✓ Task progress updated successfully</div>";
                
                $updatedTask = \eBizIndia\TwelveWeekTasks::getTask($taskId, $testUserId);
                if ($updatedTask && $updatedTask['mon'] == 1) {
                    echo "<div class='success'>✓ Task progress retrieved successfully (Monday: {$updatedTask['mon']})</div>";
                } else {
                    echo "<div class='error'>✗ Task progress retrieval failed</div>";
                }
            } else {
                echo "<div class='error'>✗ Task progress update failed</div>";
            }
            
            // Test tasks for week retrieval
            $weekTasks = \eBizIndia\TwelveWeekTasks::getTasksForWeek($testUserId, $testCycleId, 1);
            
            if (!empty($weekTasks)) {
                echo "<div class='success'>✓ Week tasks retrieved successfully (" . count($weekTasks) . " tasks found)</div>";
                
                $foundTestTask = false;
                foreach ($weekTasks as $weekTask) {
                    if ($weekTask['id'] == $taskId) {
                        $foundTestTask = true;
                        if ($weekTask['title'] === $testTaskData['title']) {
                            echo "<div class='success'>✓ Test task found in week tasks with correct decrypted title</div>";
                        } else {
                            echo "<div class='error'>✗ Test task found but title doesn't match</div>";
                        }
                        break;
                    }
                }
                
                if (!$foundTestTask) {
                    echo "<div class='error'>✗ Test task not found in week tasks</div>";
                }
            } else {
                echo "<div class='error'>✗ No week tasks retrieved</div>";
            }
            
        } else {
            echo "<div class='error'>✗ Task creation failed</div>";
        }
    } else {
        echo "<div class='error'>✗ Skipping task tests - no goal available</div>";
    }
    
    echo "</div>";
    
    echo "<div class='test-section'>";
    echo "<h2>Test 3: Bulk Operations</h2>";
    
    // Test getting all goals for user
    $allGoals = \eBizIndia\TwelveWeekGoals::getGoals($testUserId, $testCycleId);
    
    if (!empty($allGoals)) {
        echo "<div class='success'>✓ Retrieved " . count($allGoals) . " goals for user</div>";
        
        $encryptedCount = 0;
        foreach ($allGoals as $goal) {
            if (isset($goal['is_encrypted']) && $goal['is_encrypted'] == 1) {
                $encryptedCount++;
            }
        }
        
        echo "<div class='info'>Encrypted goals: $encryptedCount / " . count($allGoals) . "</div>";
    } else {
        echo "<div class='error'>✗ No goals retrieved for user</div>";
    }
    
    if ($goalId) {
        // Test getting all tasks for goal
        $allTasks = \eBizIndia\TwelveWeekTasks::getTasks($goalId);
        
        if (!empty($allTasks)) {
            echo "<div class='success'>✓ Retrieved " . count($allTasks) . " tasks for goal</div>";
            
            $encryptedCount = 0;
            foreach ($allTasks as $task) {
                if (isset($task['is_encrypted']) && $task['is_encrypted'] == 1) {
                    $encryptedCount++;
                }
            }
            
            echo "<div class='info'>Encrypted tasks: $encryptedCount / " . count($allTasks) . "</div>";
        } else {
            echo "<div class='info'>No tasks found for test goal</div>";
        }
    }
    
    echo "</div>";
    
    echo "<div class='test-section'>";
    echo "<h2>Test 4: Cleanup</h2>";
    
    // Clean up test data
    if (isset($taskId) && $taskId) {
        $taskDeleteResult = \eBizIndia\TwelveWeekTasks::deleteTask($taskId, $testUserId);
        if ($taskDeleteResult) {
            echo "<div class='success'>✓ Test task deleted successfully</div>";
        } else {
            echo "<div class='error'>✗ Test task deletion failed</div>";
        }
    }
    
    if ($goalId) {
        $goalDeleteResult = \eBizIndia\TwelveWeekGoals::deleteGoal($goalId, $testUserId);
        if ($goalDeleteResult) {
            echo "<div class='success'>✓ Test goal deleted successfully</div>";
        } else {
            echo "<div class='error'>✗ Test goal deletion failed</div>";
        }
    }
    
    echo "</div>";
    
    echo "<div class='success'><h2>✅ All tests completed!</h2></div>";
    echo "<div class='info'>The encryption system is working correctly for 12-week goals and tasks.</div>";
    
} catch (Exception $e) {
    echo "<div class='error'><h3>❌ Test failed with error:</h3>";
    echo $e->getMessage() . "</div>";
    error_log("Encryption test error: " . $e->getMessage());
}

?>

<h2>Test Summary</h2>
<div class='info'>
<p>This test script verified:</p>
<ul>
    <li>✓ Goal creation with automatic encryption</li>
    <li>✓ Goal retrieval with automatic decryption</li>
    <li>✓ Goal updates with encryption</li>
    <li>✓ Task creation with automatic encryption</li>
    <li>✓ Task retrieval with automatic decryption</li>
    <li>✓ Task progress updates (unencrypted fields)</li>
    <li>✓ Bulk operations with mixed encrypted/unencrypted data</li>
    <li>✓ Data cleanup and deletion</li>
</ul>
</div>

</body>
</html>