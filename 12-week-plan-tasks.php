<?php
$page = '12-week-plan-tasks';
require_once("inc.php");

// Check if user has access to this module
if (!in_array('VIEW', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
    header("Location: " . CONST_APP_ABSURL . "/");
    exit;
}

$page_title = "Plan Tasks - 12-Week Year";
$page_description = "Plan tasks for all 12 weeks of your goals";

$user_id = $loggedindata[0]['id'];
$success_message = '';
$error_message = '';

// Get current cycle based on date
$current_cycle = \eBizIndia\getCurrentCycleByDate();

if (!$current_cycle) {
    $error_message = "No active 12-week cycle found. Please contact your administrator to create a new cycle.";
}

// Get selected goal and week
$selected_goal_id = (int)($_GET['goal_id'] ?? $_POST['goal_id'] ?? 0);
$selected_week = (int)($_GET['week'] ?? $_POST['week'] ?? 1);

// Ensure week is within valid range
if ($selected_week < 1) $selected_week = 1;
if ($selected_week > 12) $selected_week = 12;

// Calculate current week
$current_week_number = 1;
if ($current_cycle) {
    $start_date = new DateTime($current_cycle['start_date']);
    $today = new DateTime();
    
    if ($today >= $start_date) {
        $days_passed = $start_date->diff($today)->days;
        $current_week_number = min(floor($days_passed / 7) + 1, 12);
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $conn = \eBizIndia\PDOConn::getInstance();
        
        switch ($action) {
            case 'add_task':
                if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)) {
                    $goal_id = (int)($_POST['goal_id'] ?? 0);
                    $week_number = (int)($_POST['week_number'] ?? 1);
                    $task_description = trim($_POST['task_description'] ?? '');
                    
                    if (empty($task_description)) {
                        throw new Exception("Task description is required.");
                    }
                    
                    if ($week_number < 1 || $week_number > 12) {
                        throw new Exception("Invalid week number.");
                    }
                    
                    // Verify goal belongs to current user
                    $goal_check_sql = "SELECT id FROM goals WHERE id = :goal_id AND user_id = :user_id AND cycle_id = :cycle_id";
                    $goal_check_stmt = \eBizIndia\PDOConn::query($goal_check_sql, [
                        ':goal_id' => $goal_id,
                        ':user_id' => $user_id,
                        ':cycle_id' => $current_cycle['id']
                    ]);
                    
                    if (!$goal_check_stmt->fetch()) {
                        throw new Exception("Goal not found or you don't have permission to add tasks to it.");
                    }
                    
                    // Use encrypted task creation
                    $taskData = [
                        'title' => $task_description,
                        'goal_id' => $goal_id,
                        'week_number' => $week_number,
                        'weekly_target' => 3 // Default weekly target
                    ];
                    
                    $task_id = \eBizIndia\TwelveWeekTasks::saveTask($taskData);
                    
                    if (!$task_id) {
                        throw new Exception("Failed to create task. Please try again.");
                    }
                    
                    $selected_goal_id = $goal_id;
                    $selected_week = $week_number;
                    $success_message = "Task added successfully!";
                    
                    // Update gamification stats
                    \eBizIndia\Gamification::updateUserStats($user_id, $current_cycle['id'], 'task_planned', 1);
                }
                break;
                
            case 'edit_task':
                if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)) {
                    $task_id = (int)($_POST['task_id'] ?? 0);
                    $task_description = trim($_POST['task_description'] ?? '');
                    
                    if (empty($task_description)) {
                        throw new Exception("Task description is required.");
                    }
                    
                    // Get current task info with security check
                    $task_data = \eBizIndia\TwelveWeekTasks::getTask($task_id, $user_id);
                    if (!$task_data) {
                        throw new Exception("Task not found or you don't have permission to edit it.");
                    }
                    
                    // Update task with encrypted data
                    $taskUpdateData = [
                        'id' => $task_id,
                        'title' => $task_description,
                        'goal_id' => $task_data['goal_id'],
                        'week_number' => $task_data['week_number'],
                        'weekly_target' => $task_data['weekly_target']
                    ];
                    
                    $result = \eBizIndia\TwelveWeekTasks::saveTask($taskUpdateData);
                    
                    if (!$result) {
                        throw new Exception("Failed to update task. Please try again.");
                    }
                    
                    $selected_goal_id = $task_data['goal_id'];
                    $selected_week = $task_data['week_number'];
                    $success_message = "Task updated successfully!";
                }
                break;
                
            case 'delete_task':
                if (in_array('DELETE', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)) {
                    $task_id = (int)($_POST['task_id'] ?? 0);
                    
                    // Get task info before deletion
                    $task_data = \eBizIndia\TwelveWeekTasks::getTask($task_id, $user_id);
                    if (!$task_data) {
                        throw new Exception("Task not found or you don't have permission to delete it.");
                    }
                    
                    // Use encrypted task deletion
                    $result = \eBizIndia\TwelveWeekTasks::deleteTask($task_id, $user_id);
                    
                    if (!$result) {
                        throw new Exception("Failed to delete task. Please try again.");
                    }
                    
                    $selected_goal_id = $task_data['goal_id'];
                    $selected_week = $task_data['week_number'];
                    $success_message = "Task deleted successfully!";
                }
                break;
                
            case 'copy_tasks':
                if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)) {
                    $goal_id = (int)($_POST['goal_id'] ?? 0);
                    $from_week = (int)($_POST['from_week'] ?? 1);
                    $to_week = (int)($_POST['to_week'] ?? 1);
                    
                    if ($from_week < 1 || $from_week > 12 || $to_week < 1 || $to_week > 12) {
                        throw new Exception("Invalid week numbers.");
                    }
                    
                    if ($from_week == $to_week) {
                        throw new Exception("Source and destination weeks cannot be the same.");
                    }
                    
                    // Verify goal belongs to current user
                    $goalExists = \eBizIndia\TwelveWeekGoals::getGoal($goal_id, $user_id);
                    if (!$goalExists || $goalExists['cycle_id'] != $current_cycle['id']) {
                        throw new Exception("Goal not found or you don't have permission to copy tasks.");
                    }
                    
                    // Get tasks from source week using encrypted method
                    $source_tasks = \eBizIndia\TwelveWeekTasks::getTasks($goal_id, $from_week);
                    
                    if (empty($source_tasks)) {
                        throw new Exception("No tasks found in week {$from_week} to copy.");
                    }
                    
                    // Copy tasks to destination week using encrypted method
                    $copied_count = 0;
                    
                    foreach ($source_tasks as $task) {
                        $taskData = [
                            'title' => $task['title'],
                            'goal_id' => $goal_id,
                            'week_number' => $to_week,
                            'weekly_target' => $task['weekly_target'] ?? 3
                        ];
                        
                        $new_task_id = \eBizIndia\TwelveWeekTasks::saveTask($taskData);
                        if ($new_task_id) {
                            $copied_count++;
                        }
                    }
                    
                    $selected_goal_id = $goal_id;
                    $selected_week = $to_week;
                    $success_message = "{$copied_count} task(s) copied from week {$from_week} to week {$to_week}!";
                }
                break;
        }
    } catch (Exception $e) {
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }
        $error_message = $e->getMessage();
    }
}

// Get user's goals for the current cycle using encrypted method
$goals = [];
if ($current_cycle) {
    $goals = \eBizIndia\TwelveWeekGoals::getGoals($user_id, $current_cycle['id']);
}

// Get selected goal details
$selected_goal = null;
if ($selected_goal_id > 0) {
    foreach ($goals as $goal) {
        if ($goal['id'] == $selected_goal_id) {
            $selected_goal = $goal;
            break;
        }
    }
}

// If no goal selected but goals exist, select the first one
if (!$selected_goal && !empty($goals)) {
    $selected_goal = $goals[0];
    $selected_goal_id = $selected_goal['id'];
}

// Get tasks for selected goal and week using encrypted method
$tasks = [];
if ($selected_goal) {
    $tasks = \eBizIndia\TwelveWeekTasks::getTasks($selected_goal_id, $selected_week);
}

// Calculate week date range
$week_dates = [];
if ($current_cycle) {
    $cycle_start = new DateTime($current_cycle['start_date']);
    $week_start = clone $cycle_start;
    $week_start->add(new DateInterval('P' . (($selected_week - 1) * 7) . 'D'));
    $week_end = clone $week_start;
    $week_end->add(new DateInterval('P6D'));
    
    $week_dates = [
        'start' => $week_start->format('M j'),
        'end' => $week_end->format('M j, Y'),
        'full_range' => $week_start->format('M j') . ' - ' . $week_end->format('M j, Y')
    ];
}

$pageLoadJsCode = '
    // Initialize task planning functionality
    TaskPlanner.init();
';

$jscode = '
var TaskPlanner = {
    init: function() {
        this.bindEvents();
    },
    
    bindEvents: function() {
        // Goal selection change
        $(document).on("change", "#goalSelector", function() {
            var goalId = $(this).val();
            if (goalId) {
                window.location.href = "12-week-plan-tasks.php?goal_id=" + goalId + "&week=" + ' . $selected_week . ';
            }
        });
        
        // Week navigation
        $(document).on("click", ".btn-prev-week", function() {
            var currentWeek = ' . $selected_week . ';
            if (currentWeek > 1) {
                window.location.href = "12-week-plan-tasks.php?goal_id=" + ' . $selected_goal_id . ' + "&week=" + (currentWeek - 1);
            }
        });
        
        $(document).on("click", ".btn-next-week", function() {
            var currentWeek = ' . $selected_week . ';
            if (currentWeek < 12) {
                window.location.href = "12-week-plan-tasks.php?goal_id=" + ' . $selected_goal_id . ' + "&week=" + (currentWeek + 1);
            }
        });
        
        // Week selector dropdown
        $(document).on("change", "#weekSelector", function() {
            var week = $(this).val();
            if (week) {
                window.location.href = "12-week-plan-tasks.php?goal_id=" + ' . $selected_goal_id . ' + "&week=" + week;
            }
        });
        
        // Edit task modal
        $(document).on("click", ".btn-edit-task", function() {
            var taskId = $(this).data("task-id");
            var description = $(this).data("description");
            
            $("#editTaskModal #edit_task_id").val(taskId);
            $("#editTaskModal #edit_task_description").val(description);
            $("#editTaskModal").modal("show");
        });
        
        // Delete task confirmation
        $(document).on("click", ".btn-delete-task", function() {
            var taskId = $(this).data("task-id");
            var description = $(this).data("description");
            
            if (confirm("Are you sure you want to delete this task?\\n\\n" + description)) {
                $("#deleteTaskForm #delete_task_id").val(taskId);
                $("#deleteTaskForm").submit();
            }
        });
        
        // Copy tasks modal
        $(document).on("click", ".btn-copy-tasks", function() {
            $("#copyTasksModal #copy_goal_id").val(' . $selected_goal_id . ');
            $("#copyTasksModal #copy_from_week").val(' . $selected_week . ');
            $("#copyTasksModal").modal("show");
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
    'goals' => $goals,
    'selected_goal' => $selected_goal,
    'selected_goal_id' => $selected_goal_id,
    'selected_week' => $selected_week,
    'current_week_number' => $current_week_number,
    'tasks' => $tasks,
    'week_dates' => $week_dates,
    'allowed_menu_perms' => $allowed_menu_perms,
    'user_id' => $user_id
);

// Register body template
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . '12-week-plan-tasks.tpl';
$page_renderer->registerBodyTemplate($body_template_file, $template_data);

// Update base template data
$additional_base_template_data = ['page_title' => $page_title, 'module_name' => $page];
$page_renderer->updateBaseTemplateData($additional_base_template_data);

// Render the page
$page_renderer->renderPage();
?>