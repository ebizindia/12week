<?php
$page = '12-week-plan-tasks'; // Use same permissions as plan tasks
require_once("inc.php");

// Check if user has access to this module
if (!in_array('EDIT', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$user_id = $loggedindata[0]['id'];
$response = ['success' => false, 'message' => ''];

try {
    $task_id = (int)($_POST['task_id'] ?? 0);
    $day = trim($_POST['day'] ?? '');
    $completed = (int)($_POST['completed'] ?? 0);
    
    // Validate inputs
    if ($task_id <= 0) {
        throw new Exception('Invalid task ID');
    }
    
    $valid_days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
    if (!in_array($day, $valid_days)) {
        throw new Exception('Invalid day');
    }
    
    $completed = $completed ? 1 : 0;
    
    // Get current cycle based on date
    $current_cycle = \eBizIndia\getCurrentCycleByDate();
    
    if (!$current_cycle) {
        throw new Exception('No active cycle found');
    }
    
    // Verify task belongs to current user
    $task_check_sql = "SELECT t.id, t.goal_id, t.week_number 
                       FROM tasks t 
                       JOIN goals g ON t.goal_id = g.id 
                       WHERE t.id = :task_id AND g.user_id = :user_id AND g.cycle_id = :cycle_id";
    
    $task_check_stmt = \eBizIndia\PDOConn::query($task_check_sql, [
        ':task_id' => $task_id,
        ':user_id' => $user_id,
        ':cycle_id' => $current_cycle['id']
    ]);
    
    $task_data = $task_check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$task_data) {
        throw new Exception('Task not found or access denied');
    }
    
    // Update task completion
    $conn = \eBizIndia\PDOConn::getInstance();
    $update_sql = "UPDATE tasks SET {$day} = :completed, updated_at = NOW() WHERE id = :task_id";
    
    $stmt = $conn->prepare($update_sql);
    $stmt->execute([
        ':completed' => $completed,
        ':task_id' => $task_id
    ]);
    
    // Calculate new completion stats
    $stats_sql = "SELECT mon, tue, wed, thu, fri, sat, sun FROM tasks WHERE id = :task_id";
    $stats_stmt = \eBizIndia\PDOConn::query($stats_sql, [':task_id' => $task_id]);
    $task_stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
    
    $completed_days = $task_stats['mon'] + $task_stats['tue'] + $task_stats['wed'] + 
                     $task_stats['thu'] + $task_stats['fri'] + $task_stats['sat'] + $task_stats['sun'];
    
    $completion_percentage = round(($completed_days / 7) * 100);
    
    // Update user activity
    $activity_sql = "INSERT INTO user_activity_12week (user_id, last_task_update) 
                     VALUES (:user_id, NOW()) 
                     ON DUPLICATE KEY UPDATE last_task_update = NOW()";
    
    $activity_stmt = $conn->prepare($activity_sql);
    $activity_stmt->execute([':user_id' => $user_id]);
    
    // Update gamification stats if task was completed
    if ($completed) {
        \eBizIndia\Gamification::updateUserStats($user_id, $current_cycle['id'], 'task_completed', 1);
    }
    
    // Calculate and update weekly score
    $week_tasks_sql = "SELECT 
                           COUNT(*) * 7 as total_checkboxes,
                           SUM(mon + tue + wed + thu + fri + sat + sun) as completed_checkboxes
                       FROM tasks t
                       JOIN goals g ON t.goal_id = g.id
                       WHERE g.user_id = :user_id 
                         AND g.cycle_id = :cycle_id 
                         AND t.week_number = :week_number";
    
    $week_tasks_stmt = \eBizIndia\PDOConn::query($week_tasks_sql, [
        ':user_id' => $user_id,
        ':cycle_id' => $current_cycle['id'],
        ':week_number' => $task_data['week_number']
    ]);
    
    $week_stats = $week_tasks_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($week_stats && $week_stats['total_checkboxes'] > 0) {
        $week_score = ($week_stats['completed_checkboxes'] / $week_stats['total_checkboxes']) * 100;
        
        $score_sql = "INSERT INTO weekly_scores (user_id, cycle_id, week_number, total_checkboxes, completed_checkboxes, score_percentage) 
                      VALUES (:user_id, :cycle_id, :week_number, :total_checkboxes, :completed_checkboxes, :score_percentage)
                      ON DUPLICATE KEY UPDATE 
                          total_checkboxes = VALUES(total_checkboxes),
                          completed_checkboxes = VALUES(completed_checkboxes),
                          score_percentage = VALUES(score_percentage)";
        
        $score_stmt = $conn->prepare($score_sql);
        $score_stmt->execute([
            ':user_id' => $user_id,
            ':cycle_id' => $current_cycle['id'],
            ':week_number' => $task_data['week_number'],
            ':total_checkboxes' => $week_stats['total_checkboxes'],
            ':completed_checkboxes' => $week_stats['completed_checkboxes'],
            ':score_percentage' => $week_score
        ]);
    }
    
    $response = [
        'success' => true,
        'message' => 'Task updated successfully',
        'data' => [
            'task_id' => $task_id,
            'day' => $day,
            'completed' => $completed,
            'completed_days' => $completed_days,
            'completion_percentage' => $completion_percentage,
            'week_score' => isset($week_score) ? round($week_score, 1) : 0
        ]
    ];
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>