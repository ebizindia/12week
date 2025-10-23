<?php
$page = '12-week-dashboard';
require_once("inc.php");

// Check if user has access to this module
if (!in_array('VIEW', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
    header("Location: " . CONST_APP_ABSURL . "/");
    exit;
}

$page_title = "Dashboard - 12-Week Year";
$page_description = "Overview of your current 12-week cycle progress";

$user_id = $loggedindata[0]['id'];

// Get current cycle based on date
$current_cycle = \eBizIndia\getCurrentCycleByDate();

$dashboard_data = [
    'current_cycle' => $current_cycle,
    'current_week' => 0,
    'days_remaining' => 0,
    'cycle_score' => 0,
    'week_score' => 0,
    'total_goals' => 0,
    'goals_by_category' => [],
    'this_week_tasks' => [],
    'error_message' => ''
];

if (!$current_cycle) {
    $dashboard_data['error_message'] = "No active 12-week cycle found. Please contact your administrator to create a new cycle.";
} else {
    // Calculate current week and days remaining
    $start_date = new DateTime($current_cycle['start_date']);
    $end_date = new DateTime($current_cycle['end_date']);
    $today = new DateTime();
    
    if ($today < $start_date) {
        $dashboard_data['current_week'] = 0;
        $dashboard_data['days_remaining'] = $start_date->diff($today)->days;
    } elseif ($today > $end_date) {
        $dashboard_data['current_week'] = 12;
        $dashboard_data['days_remaining'] = 0;
    } else {
        $days_passed = $start_date->diff($today)->days;
        $dashboard_data['current_week'] = min(floor($days_passed / 7) + 1, 12);
        $dashboard_data['days_remaining'] = $end_date->diff($today)->days;
    }
    
    // Get total goals count
    $goals_count_sql = "SELECT COUNT(*) as total FROM goals WHERE user_id = :user_id AND cycle_id = :cycle_id";
    $goals_count_stmt = \eBizIndia\PDOConn::query($goals_count_sql, [':user_id' => $user_id, ':cycle_id' => $current_cycle['id']]);
    $dashboard_data['total_goals'] = $goals_count_stmt->fetchColumn();
    
    // Get goals by category
    $goals_by_cat_sql = "SELECT c.name, c.color_code, COUNT(g.id) as goal_count 
                         FROM categories c 
                         LEFT JOIN goals g ON c.id = g.category_id AND g.user_id = :user_id AND g.cycle_id = :cycle_id 
                         WHERE c.is_active = 1 
                         GROUP BY c.id, c.name, c.color_code 
                         HAVING goal_count > 0 
                         ORDER BY c.sort_order, c.name";
    
    $goals_by_cat_stmt = \eBizIndia\PDOConn::query($goals_by_cat_sql, [':user_id' => $user_id, ':cycle_id' => $current_cycle['id']]);
    $dashboard_data['goals_by_category'] = $goals_by_cat_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get current week's tasks (if we're in an active week) - using same method as 12-week-goals.php
    if ($dashboard_data['current_week'] > 0 && $dashboard_data['current_week'] <= 12) {
        // Get goals using the same method as 12-week-goals.php
        $goals = \eBizIndia\TwelveWeekGoals::getGoals($user_id, $current_cycle['id']);
        
        $dashboard_data['this_week_tasks'] = [];
        
        // Get tasks for current week only using the same method as 12-week-goals.php
        foreach ($goals as $goal) {
            $tasks = \eBizIndia\TwelveWeekTasks::getTasks($goal['id'], $dashboard_data['current_week']);
            
            // Add goal and category info to each task for display
            foreach ($tasks as $task) {
                // Add goal and category information to the task
                $task['goal_title'] = $goal['title'];
                $task['category_name'] = $goal['category_name'];
                $task['color_code'] = $goal['color_code'];
                
                // Add the task to the dashboard tasks array
                $dashboard_data['this_week_tasks'][] = $task;
            }
        }
        
        // Calculate this week's score using proper target-based calculation
        $total_checkboxes = 0;
        $completed_checkboxes = 0;
        
        foreach ($dashboard_data['this_week_tasks'] as $task) {
            $weekly_target = $task['weekly_target'] ?? 1;
            $completed_days = ($task['mon'] ?? 0) + ($task['tue'] ?? 0) + ($task['wed'] ?? 0) + 
                             ($task['thu'] ?? 0) + ($task['fri'] ?? 0) + ($task['sat'] ?? 0) + ($task['sun'] ?? 0);
            
            $total_checkboxes += $weekly_target;
            $completed_checkboxes += min($completed_days, $weekly_target); // Cap at target
        }
        
        if ($total_checkboxes > 0) {
            $dashboard_data['week_score'] = round(($completed_checkboxes / $total_checkboxes) * 100, 1);
        }
        
        // Debug: Log the first few tasks to verify data
        if (!empty($dashboard_data['this_week_tasks'])) {
            error_log("Dashboard: Found " . count($dashboard_data['this_week_tasks']) . " tasks for week " . $dashboard_data['current_week']);
            foreach (array_slice($dashboard_data['this_week_tasks'], 0, 3) as $i => $task) {
                error_log("Dashboard Task $i: ID={$task['id']}, Title='{$task['title']}', Goal='{$task['goal_title']}'");
            }
        } else {
            error_log("Dashboard: No tasks found for week " . $dashboard_data['current_week']);
        }
    }
    
    // Get cycle score (average of all completed weeks)
    $cycle_score_sql = "SELECT AVG(score_percentage) as avg_score 
                        FROM weekly_scores 
                        WHERE user_id = :user_id AND cycle_id = :cycle_id";
    
    $cycle_score_stmt = \eBizIndia\PDOConn::query($cycle_score_sql, [':user_id' => $user_id, ':cycle_id' => $current_cycle['id']]);
    $cycle_score_result = $cycle_score_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cycle_score_result && $cycle_score_result['avg_score'] !== null) {
        $dashboard_data['cycle_score'] = round($cycle_score_result['avg_score'], 1);
    }
}

// Set template data
$template_data = array_merge($dashboard_data, [
    'page_title' => $page_title,
    'page_description' => $page_description,
    'user_id' => $user_id,
    'allowed_menu_perms' => $allowed_menu_perms
]);

// Register body template
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . '12-week-dashboard.tpl';
$page_renderer->registerBodyTemplate($body_template_file, $template_data);

// Update base template data
$additional_base_template_data = ['page_title' => $page_title, 'module_name' => $page];
$page_renderer->updateBaseTemplateData($additional_base_template_data);

$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
// Render the page
$page_renderer->renderPage();
?>