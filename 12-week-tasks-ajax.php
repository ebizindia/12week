<?php
$page = '12-week-goals';
require_once("inc.php");

// Check if user has access to this module
if (!in_array('VIEW', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

header('Content-Type: application/json');
$user_id = $loggedindata[0]['id'];
$action = $_REQUEST['action'] ?? '';

try {
    $conn = \eBizIndia\PDOConn::getInstance();
    
    switch ($action) {
        case 'get_tasks':
            $goal_id = (int)($_GET['goal_id'] ?? 0);
            
            // Verify goal belongs to current user and get tasks with decryption
            $goalExists = \eBizIndia\TwelveWeekGoals::getGoal($goal_id, $user_id);
            if (!$goalExists) {
                throw new Exception("Goal not found or access denied");
            }
            
            $tasks = \eBizIndia\TwelveWeekTasks::getTasks($goal_id);
            
            echo json_encode(['success' => true, 'tasks' => $tasks]);
            break;
            
        case 'get_task_count':
            $goal_id = (int)($_GET['goal_id'] ?? 0);
            
            // Verify goal belongs to current user
            $goalExists = \eBizIndia\TwelveWeekGoals::getGoal($goal_id, $user_id);
            if (!$goalExists) {
                throw new Exception("Goal not found or access denied");
            }
            
            $count = \eBizIndia\TwelveWeekTasks::getTaskCount($goal_id);
            
            echo json_encode(['success' => true, 'count' => $count]);
            break;
            
        case 'add_task':
            if (!in_array('ADD', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
                throw new Exception("You don't have permission to add tasks");
            }
            
            $goal_id = (int)($_POST['goal_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $week_number = (int)($_POST['week_number'] ?? 0);
            $weekly_target = (int)($_POST['weekly_target'] ?? 1);
            
            if (empty($title)) {
                throw new Exception("Task title is required");
            }
            
            if ($week_number < 1 || $week_number > 12) {
                throw new Exception("Week number must be between 1 and 12");
            }
            
            if ($weekly_target < 1 || $weekly_target > 7) {
                throw new Exception("Weekly target must be between 1 and 7 days");
            }
            
            // Verify goal belongs to current user
            $goalExists = \eBizIndia\TwelveWeekGoals::getGoal($goal_id, $user_id);
            if (!$goalExists) {
                throw new Exception("Goal not found or access denied");
            }
            
            // Use encrypted task creation
            $taskData = [
                'title' => $title,
                'goal_id' => $goal_id,
                'week_number' => $week_number,
                'weekly_target' => $weekly_target
            ];
            
            $task_id = \eBizIndia\TwelveWeekTasks::saveTask($taskData);
            
            if (!$task_id) {
                throw new Exception("Failed to create task. Please try again.");
            }
            
            // Update gamification stats
            try {
                if (class_exists('\eBizIndia\Gamification')) {
                    // Get current cycle
                    $cycle_sql = "SELECT c.* FROM cycles c 
                                 JOIN goals g ON c.id = g.cycle_id 
                                 WHERE g.id = :goal_id";
                    $cycle_stmt = $conn->prepare($cycle_sql);
                    $cycle_stmt->execute([':goal_id' => $goal_id]);
                    $cycle = $cycle_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($cycle) {
                        \eBizIndia\Gamification::updateUserStats($user_id, $cycle['id'], 'task_created', 1);
                    }
                }
            } catch (Exception $gamification_error) {
                // Log the error but don't fail the task creation
                error_log("Gamification error: " . $gamification_error->getMessage());
            }
            
            echo json_encode(['success' => true, 'task_id' => $task_id]);
            break;
            
        case 'delete_task':
            if (!in_array('DELETE', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
                throw new Exception("You don't have permission to delete tasks");
            }
            
            $task_id = (int)($_POST['task_id'] ?? 0);
            
            // Use encrypted task deletion (includes security checks)
            $result = \eBizIndia\TwelveWeekTasks::deleteTask($task_id, $user_id);
            
            if (!$result) {
                throw new Exception("Task not found or access denied");
            }
            
            echo json_encode(['success' => true]);
            break;
            
        case 'update_day_completion':
            if (!in_array('EDIT', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
                throw new Exception("You don't have permission to update tasks");
            }
            
            $task_id = (int)($_POST['task_id'] ?? 0);
            $day = $_POST['day'] ?? '';
            $completed = (int)($_POST['completed'] ?? 0);
            
            // Use encrypted task progress update (includes security checks)
            $result = \eBizIndia\TwelveWeekTasks::updateTaskProgress($task_id, $day, $completed, $user_id);
            
            if (!$result) {
                throw new Exception("Task not found or access denied");
            }
            
            // Get task info for further processing
            $task_info = \eBizIndia\TwelveWeekTasks::getTask($task_id, $user_id);
            if (!$task_info) {
                throw new Exception("Unable to retrieve task information");
            }
            
            // Get goal details for score calculation
            $goal_info = \eBizIndia\TwelveWeekGoals::getGoal($task_info['goal_id'], $user_id);
            
            // Recalculate weekly score after checkbox update
            if ($goal_info) {
                recalculateWeeklyScore($user_id, $goal_info['cycle_id'], $task_info['week_number'], $conn);
            }
            
            // Update gamification stats for task completion
            try {
                if (class_exists('\eBizIndia\Gamification') && $completed == 1) {
                    // Use goal info we already retrieved
                    if ($goal_info) {
                        \eBizIndia\Gamification::updateUserStats($user_id, $goal_info['cycle_id'], 'task_completed', 1);
                    }
                }
            } catch (Exception $gamification_error) {
                // Log the error but don't fail the task update
                error_log("Gamification error: " . $gamification_error->getMessage());
            }
            
            echo json_encode(['success' => true, 'goal_id' => $task_info['goal_id']]);
            break;
            
        case 'update_task_target':
            if (!in_array('EDIT', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
                throw new Exception("You don't have permission to update tasks");
            }
            
            $task_id = (int)($_POST['task_id'] ?? 0);
            $weekly_target = (int)($_POST['weekly_target'] ?? 1);
            
            // Validate target
            if ($weekly_target < 1 || $weekly_target > 7) {
                throw new Exception("Weekly target must be between 1 and 7 days");
            }
            
            // Get current task info with security check
            $task_info = \eBizIndia\TwelveWeekTasks::getTask($task_id, $user_id);
            if (!$task_info) {
                throw new Exception("Task not found or access denied");
            }
            
            // Get goal info for cycle_id
            $goal_info = \eBizIndia\TwelveWeekGoals::getGoal($task_info['goal_id'], $user_id);
            if (!$goal_info) {
                throw new Exception("Goal not found or access denied");
            }
            
            // Update the task with new weekly target (this will re-encrypt if needed)
            $taskData = [
                'id' => $task_id,
                'title' => $task_info['title'], // Keep existing title
                'goal_id' => $task_info['goal_id'],
                'week_number' => $task_info['week_number'],
                'weekly_target' => $weekly_target
            ];
            
            $result = \eBizIndia\TwelveWeekTasks::saveTask($taskData);
            
            if (!$result) {
                throw new Exception("Failed to update task target");
            }
            
            // Recalculate weekly score after target change
            recalculateWeeklyScore($user_id, $goal_info['cycle_id'], $task_info['week_number'], $conn);
            
            echo json_encode(['success' => true]);
            break;
            
        default:
            throw new Exception("Invalid action");
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

/**
 * Recalculate weekly score for a user's specific week
 * Updated to match existing weekly_scores table structure
 */
function recalculateWeeklyScore($user_id, $cycle_id, $week_number, $conn) {
    try {
        // Get all tasks for this user, cycle, and week
        $tasks_sql = "SELECT t.* FROM tasks t 
                     JOIN goals g ON t.goal_id = g.id 
                     WHERE g.user_id = :user_id 
                     AND g.cycle_id = :cycle_id 
                     AND t.week_number = :week_number";
        
        $tasks_stmt = $conn->prepare($tasks_sql);
        $tasks_stmt->execute([
            ':user_id' => $user_id,
            ':cycle_id' => $cycle_id,
            ':week_number' => $week_number
        ]);
        $tasks = $tasks_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($tasks)) {
            // No tasks for this week - set score to 0
            $total_checkboxes = 0;
            $completed_checkboxes = 0;
            $score_percentage = 0;
        } else {
            $total_checkboxes = 0;
            $completed_checkboxes = 0;
            
            // Calculate totals across all tasks for this week
            foreach ($tasks as $task) {
                $weekly_target = $task['weekly_target'] ?? 1;
                $completed_days = ($task['mon'] ?? 0) + ($task['tue'] ?? 0) + ($task['wed'] ?? 0) + 
                                 ($task['thu'] ?? 0) + ($task['fri'] ?? 0) + ($task['sat'] ?? 0) + ($task['sun'] ?? 0);
                
                // Total possible checkboxes = weekly target for each task
                $total_checkboxes += $weekly_target;
                
                // Completed checkboxes = actual completed days (capped at target)
                $completed_checkboxes += min($completed_days, $weekly_target);
            }
            
            // Calculate percentage
            $score_percentage = $total_checkboxes > 0 ? round(($completed_checkboxes / $total_checkboxes) * 100) : 0;
        }
        
        // Update or insert weekly score using existing table structure
        $score_sql = "INSERT INTO weekly_scores (user_id, cycle_id, week_number, total_checkboxes, completed_checkboxes, score_percentage, calculated_at) 
                     VALUES (:user_id, :cycle_id, :week_number, :total_checkboxes, :completed_checkboxes, :score_percentage, NOW())
                     ON DUPLICATE KEY UPDATE 
                     total_checkboxes = :total_checkboxes2, 
                     completed_checkboxes = :completed_checkboxes2, 
                     score_percentage = :score_percentage2, 
                     calculated_at = NOW()";
        
        $score_stmt = $conn->prepare($score_sql);
        $score_stmt->execute([
            ':user_id' => $user_id,
            ':cycle_id' => $cycle_id,
            ':week_number' => $week_number,
            ':total_checkboxes' => $total_checkboxes,
            ':completed_checkboxes' => $completed_checkboxes,
            ':score_percentage' => $score_percentage,
            ':total_checkboxes2' => $total_checkboxes,
            ':completed_checkboxes2' => $completed_checkboxes,
            ':score_percentage2' => $score_percentage
        ]);
        
        error_log("Weekly score updated: User $user_id, Week $week_number, Score: $score_percentage% ($completed_checkboxes/$total_checkboxes)");
        
    } catch (Exception $e) {
        error_log("Error calculating weekly score: " . $e->getMessage());
    }
}
?>