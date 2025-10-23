<?php
$page = '12-week-goals';
require_once("inc.php");

// Check if user has access to this module
if (!in_array('VIEW', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
    header("Location: " . CONST_APP_ABSURL . "/");
    exit;
}

$page_title = "My Goals - 12-Week Year";
$page_description = "Manage your goals organized by categories for the current 12-week cycle";

$user_id = $loggedindata[0]['id'];
$success_message = '';
$error_message = '';

// Get current cycle based on date
$current_cycle = \eBizIndia\getCurrentCycleByDate();

if (!$current_cycle) {
    $error_message = "No active 12-week cycle found. Please contact your administrator to create a new cycle.";
}

// Calculate current week based on cycle start date and current date
$current_week_number = 1;
$week_start_date = null;
$week_end_date = null;

if ($current_cycle) {
    $cycle_start = new DateTime($current_cycle['start_date']);
    $today = new DateTime();
    $days_since_start = $cycle_start->diff($today)->days;
    $current_week_number = min(12, max(1, floor($days_since_start / 7) + 1));
}

// Handle week navigation
$viewing_week = (int)($_GET['week'] ?? $current_week_number);
$viewing_week = min(12, max(1, $viewing_week)); // Ensure week is between 1-12

// Calculate week date range
if ($current_cycle) {
    $cycle_start = new DateTime($current_cycle['start_date']);
    $week_start = clone $cycle_start;
    $week_start->add(new DateInterval('P' . (($viewing_week - 1) * 7) . 'D'));
    $week_end = clone $week_start;
    $week_end->add(new DateInterval('P6D'));
    
    $week_start_date = $week_start->format('M j');
    $week_end_date = $week_end->format('M j, Y');
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Debug: Add temporary logging
    error_log("Goals form submitted - Action: $action, POST data: " . print_r($_POST, true));
    
    try {
        $conn = \eBizIndia\PDOConn::getInstance();
        
        switch ($action) {
            case 'add_goal':
                if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)) {
                    $title = trim($_POST['title'] ?? '');
                    $category_id = (int)($_POST['category_id'] ?? 0);
                    
                    if (empty($title)) {
                        throw new Exception("Goal title is required.");
                    }
                    
                    if ($category_id <= 0) {
                        throw new Exception("Please select a valid category.");
                    }
                    
                    if (!$current_cycle) {
                        throw new Exception("No active cycle found. Please contact your administrator.");
                    }
                    
                    // Use encrypted goal creation
                    $goalData = [
                        'title' => $title,
                        'user_id' => $user_id,
                        'cycle_id' => $current_cycle['id'],
                        'category_id' => $category_id
                    ];
                    
                    $goalId = \eBizIndia\TwelveWeekGoals::saveGoal($goalData);
                    
                    if (!$goalId) {
                        throw new Exception("Failed to create goal. Please try again.");
                    }
                    
                    $success_message = "Goal added successfully!";
                    
                    // Update gamification stats (don't let this fail the main operation)
                    try {
                        if (class_exists('\eBizIndia\Gamification')) {
                            \eBizIndia\Gamification::updateUserStats($user_id, $current_cycle['id'], 'goal_created', 1);
                        }
                    } catch (Exception $gamification_error) {
                        // Log the error but don't fail the goal creation
                        error_log("Gamification error: " . $gamification_error->getMessage());
                    }
                }
                break;
                
            case 'edit_goal':
                if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)) {
                    $goal_id = (int)($_POST['goal_id'] ?? 0);
                    $title = trim($_POST['title'] ?? '');
                    $category_id = (int)($_POST['category_id'] ?? 0);
                    
                    if (empty($title)) {
                        throw new Exception("Goal title is required.");
                    }
                    
                    if ($category_id <= 0) {
                        throw new Exception("Please select a valid category.");
                    }
                    
                    // Use encrypted goal update
                    $goalData = [
                        'id' => $goal_id,
                        'title' => $title,
                        'user_id' => $user_id,
                        'cycle_id' => $current_cycle['id'],
                        'category_id' => $category_id
                    ];
                    
                    $result = \eBizIndia\TwelveWeekGoals::saveGoal($goalData);
                    
                    if (!$result) {
                        throw new Exception("Failed to update goal. Please try again.");
                    }
                    
                    $success_message = "Goal updated successfully!";
                }
                break;
                
            case 'delete_goal':
                if (in_array('DELETE', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)) {
                    $goal_id = (int)($_POST['goal_id'] ?? 0);
                    
                    // Use encrypted goal deletion (includes security checks and task cleanup)
                    $result = \eBizIndia\TwelveWeekGoals::deleteGoal($goal_id, $user_id);
                    
                    if (!$result) {
                        throw new Exception("Goal not found or you don't have permission to delete it.");
                    }
                    
                    $success_message = "Goal deleted successfully!";
                }
                break;
        
            case 'get_tasks':
                $goal_id = (int)($_GET['goal_id'] ?? 0);
                
                // Use encrypted task retrieval (includes security checks and decryption)
                $tasks = \eBizIndia\TwelveWeekTasks::getTasks($goal_id);
                
                // Verify goal belongs to current user by checking if tasks were returned
                // (getTasks method includes security verification)
                $goalExists = \eBizIndia\TwelveWeekGoals::getGoal($goal_id, $user_id);
                if (!$goalExists) {
                    throw new Exception("Goal not found or access denied");
                }
                
                die(json_encode(['success' => true, 'tasks' => $tasks]));
            break;
            
            case 'get_task_count':
                $goal_id = (int)($_GET['goal_id'] ?? 0);
                
                // Verify goal belongs to current user
                $goalExists = \eBizIndia\TwelveWeekGoals::getGoal($goal_id, $user_id);
                if (!$goalExists) {
                    throw new Exception("Goal not found or access denied");
                }
                
                // Use encrypted task count method
                $count = \eBizIndia\TwelveWeekTasks::getTaskCount($goal_id);
                
                die(json_encode(['success' => true, 'count' => $count]));
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
                
                die(json_encode(['success' => true, 'task_id' => $task_id]));
                break;
                
            case 'update_task_title':
                if (!in_array('EDIT', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
                    throw new Exception("You don't have permission to update tasks");
                }
                
                $task_id = (int)($_POST['task_id'] ?? 0);
                $title = trim($_POST['title'] ?? '');
                
                // Validate title
                if (empty($title)) {
                    throw new Exception("Task title cannot be empty");
                }
                
                // Verify task belongs to current user (through goal ownership)
                $check_sql = "SELECT t.id FROM tasks t 
                             JOIN goals g ON t.goal_id = g.id 
                             WHERE t.id = :task_id AND g.user_id = :user_id";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->execute([':task_id' => $task_id, ':user_id' => $user_id]);
                
                if (!$check_stmt->fetch()) {
                    throw new Exception("Task not found or access denied");
                }
                
                // Update the task title
                $update_sql = "UPDATE tasks SET title = :title WHERE id = :task_id";
                $stmt = $conn->prepare($update_sql);
                $stmt->execute([':title' => $title, ':task_id' => $task_id]);
                
                die(json_encode(['success' => true]));
                break;

        case 'delete_task':
                if (!in_array('DELETE', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
                    throw new Exception("You don't have permission to delete tasks");
                }
                
                $task_id = (int)($_POST['task_id'] ?? 0);
                
                // Verify task belongs to current user (through goal ownership)
                $check_sql = "SELECT t.id FROM tasks t 
                             JOIN goals g ON t.goal_id = g.id 
                             WHERE t.id = :task_id AND g.user_id = :user_id";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->execute([':task_id' => $task_id, ':user_id' => $user_id]);
                
                if (!$check_stmt->fetch()) {
                    throw new Exception("Task not found or access denied");
                }
                
                $delete_sql = "DELETE FROM tasks WHERE id = :task_id";
                $stmt = $conn->prepare($delete_sql);
                $stmt->execute([':task_id' => $task_id]);
                
                die(json_encode(['success' => true]));
                break;
                
            case 'update_day_completion':
                if (!in_array('EDIT', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
                    throw new Exception("You don't have permission to update tasks");
                }
                
                $task_id = (int)($_POST['task_id'] ?? 0);
                $day = $_POST['day'] ?? '';
                $completed = (int)($_POST['completed'] ?? 0);
                
                // Validate day
                $valid_days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
                if (!in_array($day, $valid_days)) {
                    throw new Exception("Invalid day specified");
                }
                
                // Verify task belongs to current user (through goal ownership)
                $check_sql = "SELECT t.id, t.goal_id FROM tasks t 
                             JOIN goals g ON t.goal_id = g.id 
                             WHERE t.id = :task_id AND g.user_id = :user_id";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->execute([':task_id' => $task_id, ':user_id' => $user_id]);
                $task_info = $check_stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$task_info) {
                    throw new Exception("Task not found or access denied");
                }
                
                // Update the specific day
                $update_sql = "UPDATE tasks SET `$day` = :completed WHERE id = :task_id";
                $stmt = $conn->prepare($update_sql);
                $stmt->execute([':completed' => $completed, ':task_id' => $task_id]);
                
                // Get task details for score calculation
                $task_details_sql = "SELECT t.*, g.user_id, g.cycle_id FROM tasks t 
                                    JOIN goals g ON t.goal_id = g.id 
                                    WHERE t.id = :task_id";
                $task_details_stmt = $conn->prepare($task_details_sql);
                $task_details_stmt->execute([':task_id' => $task_id]);
                $task_details = $task_details_stmt->fetch(PDO::FETCH_ASSOC);
                
                // Recalculate weekly score after checkbox update
                if ($task_details) {
                    recalculateWeeklyScore($user_id, $task_details['cycle_id'], $task_details['week_number'], $conn);
                }
                
                // Update gamification stats for task completion
                try {
                    if (class_exists('\eBizIndia\Gamification') && $completed == 1) {
                        // Get current cycle
                        $cycle_sql = "SELECT c.* FROM cycles c 
                                     JOIN goals g ON c.id = g.cycle_id 
                                     WHERE g.id = :goal_id";
                        $cycle_stmt = $conn->prepare($cycle_sql);
                        $cycle_stmt->execute([':goal_id' => $task_info['goal_id']]);
                        $cycle = $cycle_stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($cycle) {
                            \eBizIndia\Gamification::updateUserStats($user_id, $cycle['id'], 'task_completed', 1);
                        }
                    }
                } catch (Exception $gamification_error) {
                    // Log the error but don't fail the task update
                    error_log("Gamification error: " . $gamification_error->getMessage());
                }
                
                die(json_encode(['success' => true, 'goal_id' => $task_info['goal_id']]));
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
                
                // Verify task belongs to current user (through goal ownership)
                $check_sql = "SELECT t.id, t.goal_id, t.week_number, g.user_id, g.cycle_id FROM tasks t 
                             JOIN goals g ON t.goal_id = g.id 
                             WHERE t.id = :task_id AND g.user_id = :user_id";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->execute([':task_id' => $task_id, ':user_id' => $user_id]);
                $task_info = $check_stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$task_info) {
                    throw new Exception("Task not found or access denied");
                }
                
                // Update the weekly target
                $update_sql = "UPDATE tasks SET weekly_target = :weekly_target WHERE id = :task_id";
                $stmt = $conn->prepare($update_sql);
                $stmt->execute([':weekly_target' => $weekly_target, ':task_id' => $task_id]);
                
                // Recalculate weekly score after target change
                recalculateWeeklyScore($task_info['user_id'], $task_info['cycle_id'], $task_info['week_number'], $conn);
                
                die(json_encode(['success' => true]));
                break;
                
            

        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Get all active categories
$categories_sql = "SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order, name";
$categories_stmt = \eBizIndia\PDOConn::query($categories_sql);
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug: Check if we have categories and cycle
if (empty($categories)) {
    $error_message = "No categories found. Please contact your administrator to create goal categories first.";
} elseif (!$current_cycle) {
    $error_message = "No active cycle found. Please contact your administrator to create a new cycle.";
}

$actual_current_week = 1;
if ($current_cycle && isset($current_cycle['start_date'])) {
    try {
        $cycle_start = new DateTime($current_cycle['start_date']);
        $today = new DateTime();
        
        // Calculate which week we're actually in today
        $days_since_start = $today->diff($cycle_start)->days;
        if ($days_since_start >= 0) {
            $actual_current_week = floor($days_since_start / 7) + 1;
            $actual_current_week = max(1, min(12, $actual_current_week));
        }
    } catch (Exception $e) {
        $actual_current_week = 1;
    }
}
// Get selected week (default to week 1)
$current_week = (int)($_GET['week'] ?? $actual_current_week);
$current_week = max(1, min(12, $current_week));

// Calculate actual current week and dates
$week_start_date = "Week " . $current_week;
$week_end_date = "";
//$actual_current_week = 1;
$is_current_week = false;

if ($current_cycle && isset($current_cycle['start_date'])) {
    try {
        $cycle_start = new DateTime($current_cycle['start_date']);
        $today = new DateTime();
        
        // Calculate which week we're actually in today
        $days_since_start = $today->diff($cycle_start)->days;
        if ($days_since_start >= 0) {
            $actual_current_week = floor($days_since_start / 7) + 1;
            $actual_current_week = max(1, min(12, $actual_current_week));
        }
        
        // Check if the displayed week is the actual current week
        $is_current_week = ($current_week == $actual_current_week);
        
        // Calculate week dates for display
        $week_start = clone $cycle_start;
        $week_start->add(new DateInterval('P' . (($current_week - 1) * 7) . 'D'));
        $week_end = clone $week_start;
        $week_end->add(new DateInterval('P6D'));
        
        $week_start_date = $week_start->format('d M, Y');
        $week_end_date = $week_end->format('d M, Y');
    } catch (Exception $e) {
        // Use defaults if calculation fails
        $is_current_week = ($current_week == 1);
    }
}

// Get user's goals organized by category with tasks for current week only
$goals_by_category = [];
if ($current_cycle) {
    // Use encrypted goals retrieval
    $goals = \eBizIndia\TwelveWeekGoals::getGoals($user_id, $current_cycle['id']);
    
    // Get tasks for current week only using encrypted task retrieval
    $tasks_by_goal = [];
    foreach ($goals as $goal) {
        $tasks = \eBizIndia\TwelveWeekTasks::getTasks($goal['id'], $current_week);
        
        if (!empty($tasks)) {
            $tasks_by_goal[$goal['id']] = $tasks;
        }
    }
    
    // Organize goals by category and attach tasks (only include goals that have tasks for current week)
    foreach ($goals as $goal) {
        $goal_tasks = $tasks_by_goal[$goal['id']] ?? [];
        
        // Only include goals that have tasks for the current week
        //if (!empty($goal_tasks)) {
            $category_id = $goal['category_id'];
            if (!isset($goals_by_category[$category_id])) {
                $goals_by_category[$category_id] = [
                    'category' => [
                        'id' => $category_id,
                        'name' => $goal['category_name'],
                        'color_code' => $goal['color_code']
                    ],
                    'goals' => [],
                    'total_tasks' => 0
                ];
            }
            
            // Attach tasks to goal
            $goal['tasks'] = $goal_tasks;
            $goals_by_category[$category_id]['goals'][] = $goal;
            $goals_by_category[$category_id]['total_tasks'] += count($goal_tasks);
        //}
    }
}

// Get task counts for each goal using encrypted methods
$task_counts = [];
if ($current_cycle) {
    $goals = \eBizIndia\TwelveWeekGoals::getGoals($user_id, $current_cycle['id']);
    
    foreach ($goals as $goal) {
        $task_counts[$goal['id']] = \eBizIndia\TwelveWeekTasks::getTaskCount($goal['id']);
    }
}

$pageLoadJsCode = '
    // Initialize goal management functionality
    GoalManager.init();
';

$jscode = '
var GoalManager = {
    init: function() {
        this.bindEvents();
    },
    
    bindEvents: function() {
        // Add goal modal
        $(document).on("click", ".btn-add-goal", function() {
            var categoryId = $(this).data("category-id");
            $("#addGoalModal #category_id").val(categoryId);
            $("#addGoalModal").modal("show");
        });
        
        // Edit goal modal
        $(document).on("click", ".btn-edit-goal", function() {
            var goalId = $(this).data("goal-id");
            var title = $(this).data("title");
            var categoryId = $(this).data("category-id");
            
            $("#editGoalModal #goal_id").val(goalId);
            $("#editGoalModal #edit_title").val(title);
            $("#editGoalModal #edit_category_id").val(categoryId);
            $("#editGoalModal").modal("show");
        });
        
        // Delete goal confirmation
        $(document).on("click", ".btn-delete-goal", function() {
            var goalId = $(this).data("goal-id");
            var title = $(this).data("title");
            
            if (confirm("Are you sure you want to delete the goal: " + title + "?\\n\\nThis will also delete all associated tasks.")) {
                $("#deleteGoalForm #delete_goal_id").val(goalId);
                $("#deleteGoalForm").submit();
            }
        });
        
        // Clear modals on close
        $(".modal").on("hidden.bs.modal", function() {
            $(this).find("form")[0].reset();
        });
    }
};
';

// Set template data
$template_data = array(
    'page_title' => $page_title,
    'page_description' => $page_description,
    'current_cycle' => $current_cycle,
    'success_message' => $success_message,
    'error_message' => $error_message,
    'categories' => $categories,
    'goals_by_category' => $goals_by_category,
    'task_counts' => $task_counts,
    'allowed_menu_perms' => $allowed_menu_perms,
    'user_id' => $user_id,
    'current_week' => $current_week,
    'actual_current_week' => $actual_current_week,
    'is_current_week' => $is_current_week,
    'week_start_date' => $week_start_date,
    'week_end_date' => $week_end_date
);

// Register body template
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . '12-week-goals.tpl';
$page_renderer->registerBodyTemplate($body_template_file, $template_data);

// Update base template data
$additional_base_template_data = ['page_title' => $page_title, 'module_name' => $page,'dom_ready_code'=>\scriptProviderFuncs\getDomReadyJsCode($page,['current_week' => $current_week,'actual_current_week' => $actual_current_week])];
$page_renderer->updateBaseTemplateData($additional_base_template_data);

$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');

// Render the page
$page_renderer->renderPage();


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