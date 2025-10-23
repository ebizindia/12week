<?php
$page = '12-week-progress';
require_once("inc.php");

// Check if user has access to this module
if (!in_array('VIEW', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
    header("Location: " . CONST_APP_ABSURL . "/");
    exit;
}

$page_title = "Progress Report - 12-Week Year";
$page_description = "Detailed analytics and progress tracking";

$user_id = $loggedindata[0]['id'];
$is_admin = ($loggedindata[0]['profile_details']['assigned_roles'][0]['role'] === 'ADMIN');

// Get selected cycle (default to active cycle)
$selected_cycle_id = (int)($_GET['cycle_id'] ?? 0);

if ($selected_cycle_id > 0) {
    $cycle_sql = "SELECT * FROM cycles WHERE id = :cycle_id";
    $cycle_stmt = \eBizIndia\PDOConn::query($cycle_sql, [':cycle_id' => $selected_cycle_id]);
    $current_cycle = $cycle_stmt->fetch(PDO::FETCH_ASSOC);
} else {
    // Get current cycle based on date
    $current_cycle = \eBizIndia\getCurrentCycleByDate();
}

$analytics_data = [
    'current_cycle' => $current_cycle,
    'current_week' => 0,
    'cycle_progress' => 0,
    'weekly_scores' => [],
    'category_performance' => [],
    'member_rankings' => [],
    'completion_trends' => [],
    'goal_statistics' => [],
    'task_statistics' => [],
    'all_cycles' => [],
    'error_message' => ''
];

if (!$current_cycle) {
    $analytics_data['error_message'] = "No cycle found for analysis.";
} else {
    // Calculate current week and cycle progress
    $start_date = new DateTime($current_cycle['start_date']);
    $end_date = new DateTime($current_cycle['end_date']);
    $today = new DateTime();
    
    if ($today < $start_date) {
        $analytics_data['current_week'] = 0;
        $analytics_data['cycle_progress'] = 0;
    } elseif ($today > $end_date) {
        $analytics_data['current_week'] = 12;
        $analytics_data['cycle_progress'] = 100;
    } else {
        $days_passed = $start_date->diff($today)->days;
        $total_days = $start_date->diff($end_date)->days;
        $analytics_data['current_week'] = min(floor($days_passed / 7) + 1, 12);
        $analytics_data['cycle_progress'] = round(($days_passed / $total_days) * 100, 1);
    }
    
    // Get weekly scores trend
    if ($is_admin) {
        // Admin sees all members' average scores
        $weekly_scores_sql = "SELECT 
                                week_number,
                                AVG(score_percentage) as avg_score,
                                COUNT(DISTINCT user_id) as member_count,
                                MIN(score_percentage) as min_score,
                                MAX(score_percentage) as max_score
                              FROM weekly_scores 
                              WHERE cycle_id = :cycle_id 
                              GROUP BY week_number 
                              ORDER BY week_number";
        $weekly_scores_stmt = \eBizIndia\PDOConn::query($weekly_scores_sql, [':cycle_id' => $current_cycle['id']]);
    } else {
        // Members see only their own scores
        $weekly_scores_sql = "SELECT 
                                week_number,
                                score_percentage as avg_score,
                                1 as member_count,
                                score_percentage as min_score,
                                score_percentage as max_score
                              FROM weekly_scores 
                              WHERE cycle_id = :cycle_id AND user_id = :user_id 
                              ORDER BY week_number";
        $weekly_scores_stmt = \eBizIndia\PDOConn::query($weekly_scores_sql, [
            ':cycle_id' => $current_cycle['id'],
            ':user_id' => $user_id
        ]);
    }
    $analytics_data['weekly_scores'] = $weekly_scores_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get category performance
    /*if ($is_admin) {
        $category_perf_sql = "SELECT 
                                c.name as category_name,
                                c.color_code,
                                COUNT(DISTINCT g.id) as total_goals,
                                COUNT(DISTINCT g.user_id) as members_with_goals,
                                COUNT(t.id) as total_tasks,
                                SUM(t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) as completed_checkboxes,
                                COUNT(t.id) * 7 as total_checkboxes,
                                CASE 
                                    WHEN COUNT(t.id) > 0 
                                    THEN ROUND((SUM(t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) / (COUNT(t.id) * 7)) * 100, 1)
                                    ELSE 0 
                                END as completion_rate
                              FROM categories c
                              LEFT JOIN goals g ON c.id = g.category_id AND g.cycle_id = :cycle_id
                              LEFT JOIN tasks t ON g.id = t.goal_id
                              WHERE c.is_active = 1
                              GROUP BY c.id, c.name, c.color_code
                              HAVING total_goals > 0
                              ORDER BY completion_rate DESC";
        $category_perf_stmt = \eBizIndia\PDOConn::query($category_perf_sql, [':cycle_id' => $current_cycle['id']]);
    } else {
        $category_perf_sql = "SELECT 
                                c.name as category_name,
                                c.color_code,
                                COUNT(DISTINCT g.id) as total_goals,
                                1 as members_with_goals,
                                COUNT(t.id) as total_tasks,
                                SUM(t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) as completed_checkboxes,
                                COUNT(t.id) * 7 as total_checkboxes,
                                CASE 
                                    WHEN COUNT(t.id) > 0 
                                    THEN ROUND((SUM(t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) / (COUNT(t.id) * 7)) * 100, 1)
                                    ELSE 0 
                                END as completion_rate
                              FROM categories c
                              LEFT JOIN goals g ON c.id = g.category_id AND g.cycle_id = :cycle_id AND g.user_id = :user_id
                              LEFT JOIN tasks t ON g.id = t.goal_id
                              WHERE c.is_active = 1
                              GROUP BY c.id, c.name, c.color_code
                              HAVING total_goals > 0
                              ORDER BY completion_rate DESC";
        $category_perf_stmt = \eBizIndia\PDOConn::query($category_perf_sql, [
            ':cycle_id' => $current_cycle['id'],
            ':user_id' => $user_id
        ]);
    }*/
    if ($is_admin) {
        $category_perf_sql = "SELECT 
                                c.name as category_name,
                                c.color_code,
                                COUNT(DISTINCT g.id) as total_goals,
                                COUNT(DISTINCT t.id) as total_tasks,
                                SUM(CASE WHEN (t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) >= t.weekly_target THEN 1 ELSE 0 END) as completed_goals,
                                CASE 
                                    WHEN COUNT(DISTINCT t.id) > 0 
                                    THEN ROUND((SUM(CASE WHEN (t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) >= t.weekly_target THEN 1 ELSE 0 END) / COUNT(DISTINCT t.id)) * 100, 1)
                                    ELSE 0 
                                END as completion_rate
                              FROM categories c
                              LEFT JOIN goals g ON c.id = g.category_id AND g.cycle_id = :cycle_id
                              LEFT JOIN tasks t ON g.id = t.goal_id
                              WHERE c.is_active = 1
                              GROUP BY c.id, c.name, c.color_code
                              HAVING total_goals > 0
                              ORDER BY completion_rate DESC";
        $category_perf_stmt = \eBizIndia\PDOConn::query($category_perf_sql, [':cycle_id' => $current_cycle['id']]);
    } else {
        $category_perf_sql = "SELECT 
                                c.name as category_name,
                                c.color_code,
                                COUNT(DISTINCT g.id) as total_goals,
                                COUNT(DISTINCT t.id) as total_tasks,
                                SUM(CASE WHEN (t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) >= t.weekly_target THEN 1 ELSE 0 END) as completed_goals,
                                CASE 
                                    WHEN COUNT(DISTINCT t.id) > 0 
                                    THEN ROUND((SUM(CASE WHEN (t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) >= t.weekly_target THEN 1 ELSE 0 END) / COUNT(DISTINCT t.id)) * 100, 1)
                                    ELSE 0 
                                END as completion_rate
                              FROM categories c
                              LEFT JOIN goals g ON c.id = g.category_id AND g.cycle_id = :cycle_id AND g.user_id = :user_id
                              LEFT JOIN tasks t ON g.id = t.goal_id
                              WHERE c.is_active = 1
                              GROUP BY c.id, c.name, c.color_code
                              HAVING total_goals > 0
                              ORDER BY completion_rate DESC";
        $category_perf_stmt = \eBizIndia\PDOConn::query($category_perf_sql, [
            ':cycle_id' => $current_cycle['id'],
            ':user_id' => $user_id
        ]);
    }
    $analytics_data['category_performance'] = $category_perf_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get member rankings (admin only)
    if ($is_admin) {
        $member_rankings_sql = "SELECT 
                                  u.id,
                                  u.username as email,
                                  m.name,
                                  COUNT(DISTINCT g.id) as total_goals,
                                  COUNT(DISTINCT t.id) as total_tasks,
                                  COALESCE(AVG(ws.score_percentage), 0) as avg_score,
                                  SUM(t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) as completed_checkboxes,
                                  COUNT(t.id) * 7 as total_checkboxes,
                                  CASE 
                                      WHEN COUNT(t.id) > 0 
                                      THEN ROUND((SUM(t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) / (COUNT(t.id) * 7)) * 100, 1)
                                      ELSE 0 
                                  END as overall_completion,
                                  MAX(ua.last_task_update) as last_activity
                                FROM users u
                                JOIN members m ON m.id=u.profile_id
                                JOIN goals g ON u.id = g.user_id AND g.cycle_id = :cycle_id
                                LEFT JOIN tasks t ON g.id = t.goal_id
                                LEFT JOIN weekly_scores ws ON u.id = ws.user_id AND ws.cycle_id = g.cycle_id
                                LEFT JOIN user_activity_12week ua ON u.id = ua.user_id
                                GROUP BY u.id, u.username
                                ORDER BY avg_score DESC, overall_completion DESC";
        
        $member_rankings_stmt = \eBizIndia\PDOConn::query($member_rankings_sql, [':cycle_id' => $current_cycle['id']]);
        $analytics_data['member_rankings'] = $member_rankings_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get completion trends (daily completion over time)
    /*$trends_sql = "SELECT 
                     t.week_number,
                     SUM(t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) as completed_checkboxes,
                     COUNT(t.id) * 7 as total_checkboxes,
                     ROUND((SUM(t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) / (COUNT(t.id) * 7)) * 100, 1) as completion_rate
                   FROM tasks t
                   JOIN goals g ON t.goal_id = g.id
                   WHERE g.cycle_id = :cycle_id" . ($is_admin ? "" : " AND g.user_id = :user_id") . "
                   GROUP BY t.week_number
                   ORDER BY t.week_number";*/
    $trends_sql = "SELECT 
                 t.week_number,
                 SUM(CASE WHEN (t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) >= t.weekly_target THEN 1 ELSE 0 END) as completed,
                 SUM(CASE WHEN (t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) > 0 AND (t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) < t.weekly_target THEN 1 ELSE 0 END) as in_progress,
                 SUM(CASE WHEN (t.mon + t.tue + t.wed + t.thu + t.fri + t.sat + t.sun) = 0 THEN 1 ELSE 0 END) as not_started,
                 COUNT(t.id) as total_tasks
               FROM tasks t
               JOIN goals g ON t.goal_id = g.id
               WHERE g.cycle_id = :cycle_id" . ($is_admin ? "" : " AND g.user_id = :user_id") . "
               GROUP BY t.week_number
               HAVING total_tasks > 0
               ORDER BY t.week_number";
    
    $trends_params = [':cycle_id' => $current_cycle['id']];
    if (!$is_admin) {
        $trends_params[':user_id'] = $user_id;
    }
    
    $trends_stmt = \eBizIndia\PDOConn::query($trends_sql, $trends_params);
    $analytics_data['completion_trends'] = $trends_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get goal statistics
    $goal_stats_sql = "SELECT 
                         COUNT(DISTINCT g.id) as total_goals,
                         COUNT(DISTINCT g.user_id) as users_with_goals,
                         COUNT(DISTINCT t.id) as total_tasks,
                         AVG(tasks_per_goal.task_count) as avg_tasks_per_goal
                       FROM goals g
                       LEFT JOIN tasks t ON g.id = t.goal_id
                       LEFT JOIN (
                           SELECT goal_id, COUNT(*) as task_count 
                           FROM tasks 
                           GROUP BY goal_id
                       ) tasks_per_goal ON g.id = tasks_per_goal.goal_id
                       WHERE g.cycle_id = :cycle_id" . ($is_admin ? "" : " AND g.user_id = :user_id");
    
    $goal_stats_stmt = \eBizIndia\PDOConn::query($goal_stats_sql, $trends_params);
    $analytics_data['goal_statistics'] = $goal_stats_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get task statistics
    $task_stats_sql = "SELECT 
                         COUNT(*) as total_tasks,
                         SUM(mon + tue + wed + thu + fri + sat + sun) as completed_checkboxes,
                         COUNT(*) * 7 as total_checkboxes,
                         ROUND((SUM(mon + tue + wed + thu + fri + sat + sun) / (COUNT(*) * 7)) * 100, 1) as overall_completion,
                         SUM(CASE WHEN (mon + tue + wed + thu + fri + sat + sun) >= 7 THEN 1 ELSE 0 END) as fully_completed_tasks,
                         SUM(CASE WHEN (mon + tue + wed + thu + fri + sat + sun) = 0 THEN 1 ELSE 0 END) as not_started_tasks
                       FROM tasks t
                       JOIN goals g ON t.goal_id = g.id
                       WHERE g.cycle_id = :cycle_id" . ($is_admin ? "" : " AND g.user_id = :user_id");
    
    $task_stats_stmt = \eBizIndia\PDOConn::query($task_stats_sql, $trends_params);
    $analytics_data['task_statistics'] = $task_stats_stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all cycles for dropdown
$all_cycles_sql = "SELECT id, name, status, start_date, end_date FROM cycles ORDER BY created_at DESC";
$all_cycles_stmt = \eBizIndia\PDOConn::query($all_cycles_sql);
$analytics_data['all_cycles'] = $all_cycles_stmt->fetchAll(PDO::FETCH_ASSOC);

// Set template data
$template_data = array_merge($analytics_data, [
    'page_title' => $page_title,
    'page_description' => $page_description,
    'user_id' => $user_id,
    'is_admin' => $is_admin,
    'selected_cycle_id' => $selected_cycle_id    
]);

// Register body template
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . '12-week-progress.tpl';
$page_renderer->registerBodyTemplate($body_template_file, $template_data);

// Update base template data
$additional_base_template_data = ['page_title' => $page_title, 'module_name' => $page, 'dom_ready_code'=>\scriptProviderFuncs\getDomReadyJsCode($page,['weekly_scores' => $analytics_data['weekly_scores'], 'completion_trends' => $analytics_data['completion_trends']])];
$page_renderer->updateBaseTemplateData($additional_base_template_data);

$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
// Render the page
$page_renderer->renderPage();
?>