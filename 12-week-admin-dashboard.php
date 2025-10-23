<?php
$page = '12-week-admin-dashboard';
require_once("inc.php");

// Check if user has access to this module
if (!in_array('VIEW', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
    header("Location: " . CONST_APP_ABSURL . "/");
    exit;
}

// Check if user is admin
$is_admin = ($loggedindata[0]['profile_details']['assigned_roles'][0]['role'] === 'ADMIN');
if (!$is_admin) {
    header("Location: " . CONST_APP_ABSURL . "/");
    exit;
}

$page_title = "Admin Dashboard - 12-Week Year";
$page_description = "Administrative overview of all member progress";

$user_id = $loggedindata[0]['id'];

// Get specific cycle if requested
$selected_cycle_id = (int)($_GET['cycle_id'] ?? 0);

// Get current active cycle or selected cycle
if ($selected_cycle_id > 0) {
    $cycle_sql = "SELECT * FROM cycles WHERE id = :cycle_id";
    $cycle_stmt = \eBizIndia\PDOConn::query($cycle_sql, [':cycle_id' => $selected_cycle_id]);
    $current_cycle = $cycle_stmt->fetch(PDO::FETCH_ASSOC);
} else {
    // Get current cycle based on date
    $current_cycle = \eBizIndia\getCurrentCycleByDate();
}

$dashboard_data = [
    'current_cycle' => $current_cycle,
    'current_week' => 0,
    'days_remaining' => 0,
    'total_members' => 0,
    'total_goals' => 0,
    'avg_cycle_score' => 0,
    'avg_week_score' => 0,
    'member_list' => [],
    'all_cycles' => [],
    'error_message' => ''
];

if (!$current_cycle) {
    $dashboard_data['error_message'] = "No cycle found. Please create a new cycle first.";
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
    
    // Get total members with goals in this cycle
    $members_sql = "SELECT COUNT(DISTINCT user_id) as total FROM goals WHERE cycle_id = :cycle_id";
    $members_stmt = \eBizIndia\PDOConn::query($members_sql, [':cycle_id' => $current_cycle['id']]);
    $dashboard_data['total_members'] = $members_stmt->fetchColumn() ?: 0;
    
    // Get total goals in this cycle
    $goals_sql = "SELECT COUNT(*) as total FROM goals WHERE cycle_id = :cycle_id";
    $goals_stmt = \eBizIndia\PDOConn::query($goals_sql, [':cycle_id' => $current_cycle['id']]);
    $dashboard_data['total_goals'] = $goals_stmt->fetchColumn() ?: 0;
    
    // Get average cycle score
    $cycle_score_sql = "SELECT AVG(score_percentage) as avg_score 
                        FROM weekly_scores 
                        WHERE cycle_id = :cycle_id";
    $cycle_score_stmt = \eBizIndia\PDOConn::query($cycle_score_sql, [':cycle_id' => $current_cycle['id']]);
    $cycle_score_result = $cycle_score_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cycle_score_result && $cycle_score_result['avg_score'] !== null) {
        $dashboard_data['avg_cycle_score'] = round($cycle_score_result['avg_score'], 1);
    }
    
    // Get average current week score
    if ($dashboard_data['current_week'] > 0 && $dashboard_data['current_week'] <= 12) {
        $week_score_sql = "SELECT AVG(score_percentage) as avg_score 
                           FROM weekly_scores 
                           WHERE cycle_id = :cycle_id AND week_number = :week_number";
        $week_score_stmt = \eBizIndia\PDOConn::query($week_score_sql, [
            ':cycle_id' => $current_cycle['id'], 
            ':week_number' => $dashboard_data['current_week']
        ]);
        $week_score_result = $week_score_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($week_score_result && $week_score_result['avg_score'] !== null) {
            $dashboard_data['avg_week_score'] = round($week_score_result['avg_score'], 1);
        }
    }
    
    // Get member list with their progress
    $member_list_sql = "SELECT DISTINCT 
                            u.id, 
                            m.name,
                            
                            COUNT(DISTINCT g.id) as goals_count,
                            COALESCE(AVG(ws.score_percentage), 0) as cycle_score,
                            COALESCE(current_week.score_percentage, 0) as current_week_score,
                            MAX(ua.last_task_update) as last_activity
                        FROM users u
                        JOIN members m ON u.profile_id = m.id
                        JOIN goals g ON u.id = g.user_id
                        LEFT JOIN weekly_scores ws ON u.id = ws.user_id AND ws.cycle_id = g.cycle_id
                        LEFT JOIN weekly_scores current_week ON u.id = current_week.user_id 
                            AND current_week.cycle_id = g.cycle_id 
                            AND current_week.week_number = :current_week
                        LEFT JOIN user_activity_12week ua ON u.id = ua.user_id
                        WHERE g.cycle_id = :cycle_id
                        GROUP BY u.id, u.username, current_week.score_percentage
                        ORDER BY cycle_score DESC, u.username";
    
    $member_list_stmt = \eBizIndia\PDOConn::query($member_list_sql, [
        ':cycle_id' => $current_cycle['id'],
        ':current_week' => $dashboard_data['current_week']
    ]);
    $dashboard_data['member_list'] = $member_list_stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all cycles for dropdown
$all_cycles_sql = "SELECT id, name, status, start_date, end_date FROM cycles ORDER BY created_at DESC";
$all_cycles_stmt = \eBizIndia\PDOConn::query($all_cycles_sql);
$dashboard_data['all_cycles'] = $all_cycles_stmt->fetchAll(PDO::FETCH_ASSOC);

// Set template data
$template_data = array_merge($dashboard_data, [
    'page_title' => $page_title,
    'page_description' => $page_description,
    'user_id' => $user_id,
    'is_admin' => $is_admin,
    'selected_cycle_id' => $selected_cycle_id
]);

// Register body template
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . '12-week-admin-dashboard.tpl';
$page_renderer->registerBodyTemplate($body_template_file, $template_data);

// Update base template data
$additional_base_template_data = ['page_title' => $page_title, 'module_name' => $page];
$page_renderer->updateBaseTemplateData($additional_base_template_data);

// Render the page
$page_renderer->renderPage();
?>