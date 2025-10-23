<?php
$page = '12-week-weekly';
require_once("inc.php");

// Check if user has access to this module
if (!in_array('VIEW', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
    header("Location: " . CONST_APP_ABSURL . "/");
    exit;
}

$page_title = "Weekly View - 12-Week Year";
$page_description = "Focus on current week tasks and track daily progress";

$user_id = $loggedindata[0]['id'];
$success_message = '';
$error_message = '';

// Get current cycle based on date
$current_cycle = \eBizIndia\getCurrentCycleByDate();

if (!$current_cycle) {
    $error_message = "No active 12-week cycle found. Please contact your administrator to create a new cycle.";
}

// Get selected week (default to current week)
$selected_week = (int)($_GET['week'] ?? 0);

// Calculate current week and validate selected week
$current_week_number = 1;
$cycle_status = 'not_started';

if ($current_cycle) {
    $current_week_number = \eBizIndia\getCurrentWeekNumber($current_cycle);
    
    if ($current_week_number == 0) {
        $cycle_status = 'not_started';
    } elseif ($current_week_number > 12) {
        $cycle_status = 'completed';
        $current_week_number = 12;
    } else {
        $cycle_status = 'active';
    }
}

// If no week selected or invalid week, use current week
if ($selected_week < 1 || $selected_week > 12) {
    $selected_week = max(1, $current_week_number);
}

// Handle quick task addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add_quick_task') {
    try {
        if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)) {
            $goal_id = (int)($_POST['goal_id'] ?? 0);
            $task_description = trim($_POST['task_description'] ?? '');
            
            if (empty($task_description)) {
                throw new Exception("Task description is required.");
            }
            
            // Verify goal belongs to current user using encrypted method
            $goalExists = \eBizIndia\TwelveWeekGoals::getGoal($goal_id, $user_id);
            if (!$goalExists || $goalExists['cycle_id'] != $current_cycle['id']) {
                throw new Exception("Goal not found or you don't have permission to add tasks to it.");
            }
            
            // Use encrypted task creation
            $taskData = [
                'title' => $task_description,
                'goal_id' => $goal_id,
                'week_number' => $selected_week,
                'weekly_target' => 3 // Default weekly target
            ];
            
            $task_id = \eBizIndia\TwelveWeekTasks::saveTask($taskData);
            
            if (!$task_id) {
                throw new Exception("Failed to create task. Please try again.");
            }
            
            $success_message = "Quick task added successfully!";
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Get user's goals for the current cycle using encrypted method
$goals = [];
if ($current_cycle) {
    $goals = \eBizIndia\TwelveWeekGoals::getGoals($user_id, $current_cycle['id']);
}

// Get tasks for selected week organized by category and goal using encrypted method
$tasks_by_category = [];
if ($current_cycle && !empty($goals)) {
    $tasks = \eBizIndia\TwelveWeekTasks::getTasksForWeek($user_id, $current_cycle['id'], $selected_week);
    
    // Organize tasks by category
    foreach ($tasks as $task) {
        $category_name = $task['category_name'];
        if (!isset($tasks_by_category[$category_name])) {
            $tasks_by_category[$category_name] = [
                'category' => [
                    'name' => $category_name,
                    'color_code' => $task['color_code'],
                    'sort_order' => $task['category_sort']
                ],
                'goals' => []
            ];
        }
        
        $goal_id = $task['goal_id'];
        if (!isset($tasks_by_category[$category_name]['goals'][$goal_id])) {
            $tasks_by_category[$category_name]['goals'][$goal_id] = [
                'goal' => [
                    'id' => $goal_id,
                    'title' => $task['goal_title']
                ],
                'tasks' => []
            ];
        }
        
        $tasks_by_category[$category_name]['goals'][$goal_id]['tasks'][] = $task;
    }
}

// Calculate week statistics
$week_stats = [
    'total_tasks' => 0,
    'total_checkboxes' => 0,
    'completed_checkboxes' => 0,
    'completion_percentage' => 0
];

if (!empty($tasks_by_category)) {
    foreach ($tasks_by_category as $category) {
        foreach ($category['goals'] as $goal) {
            foreach ($goal['tasks'] as $task) {
                $week_stats['total_tasks']++;
                $week_stats['total_checkboxes'] += 7; // 7 days per task
                $week_stats['completed_checkboxes'] += $task['mon'] + $task['tue'] + $task['wed'] + $task['thu'] + $task['fri'] + $task['sat'] + $task['sun'];
            }
        }
    }
    
    if ($week_stats['total_checkboxes'] > 0) {
        $week_stats['completion_percentage'] = round(($week_stats['completed_checkboxes'] / $week_stats['total_checkboxes']) * 100, 1);
    }
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
    // Initialize weekly view functionality
    WeeklyView.init();
';

$jscode = '
var WeeklyView = {
    init: function() {
        this.bindEvents();
    },
    
    bindEvents: function() {
        // Week navigation
        $(document).on("click", ".btn-prev-week", function() {
            var currentWeek = ' . $selected_week . ';
            if (currentWeek > 1) {
                window.location.href = "12-week-weekly.php?week=" + (currentWeek - 1);
            }
        });
        
        $(document).on("click", ".btn-next-week", function() {
            var currentWeek = ' . $selected_week . ';
            if (currentWeek < 12) {
                window.location.href = "12-week-weekly.php?week=" + (currentWeek + 1);
            }
        });
        
        // Jump to current week
        $(document).on("click", ".btn-current-week", function() {
            window.location.href = "12-week-weekly.php?week=" + ' . $current_week_number . ';
        });
        
        // Week selector dropdown
        $(document).on("change", "#weekSelector", function() {
            var week = $(this).val();
            if (week) {
                window.location.href = "12-week-weekly.php?week=" + week;
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
    'goals' => $goals,
    'tasks_by_category' => $tasks_by_category,
    'selected_week' => $selected_week,
    'current_week_number' => $current_week_number,
    'cycle_status' => $cycle_status,
    'week_stats' => $week_stats,
    'week_dates' => $week_dates,
    'allowed_menu_perms' => $allowed_menu_perms,
    'user_id' => $user_id
);

// Register body template
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . '12-week-weekly.tpl';
$page_renderer->registerBodyTemplate($body_template_file, $template_data);

// Update base template data
$additional_base_template_data = ['page_title' => $page_title, 'module_name' => $page];
$page_renderer->updateBaseTemplateData($additional_base_template_data);

// Render the page
$page_renderer->renderPage();
?>