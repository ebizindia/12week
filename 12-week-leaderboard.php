<?php
$page = '12-week-leaderboard';
require_once("inc.php");

// Check if user has access to this module
if (!in_array('VIEW', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
    header("Location: " . CONST_APP_ABSURL . "/");
    exit;
}

$page_title = "Leaderboard - 12-Week Year";
$page_description = "Performance rankings and motivation stats";

$user_id = $loggedindata[0]['id'];

// Get current cycle based on date
$current_cycle = \eBizIndia\getCurrentCycleByDate();

$leaderboard_data = [
    'current_cycle' => $current_cycle,
    'leaderboard' => [],
    'user_position' => null,
    'total_participants' => 0,
    'top_performers' => [],
    'achievement_leaders' => [],
    'streak_leaders' => [],
    'error_message' => ''
];

if (!$current_cycle) {
    $leaderboard_data['error_message'] = "No active 12-week cycle found.";
} else {
    // Get leaderboard
    $leaderboard_data['leaderboard'] = \eBizIndia\Gamification::getLeaderboard($current_cycle['id'], 20);
    
    // Get total participants
    $participants_sql = "SELECT COUNT(*) FROM leaderboard_stats WHERE cycle_id = :cycle_id AND is_visible = 1";
    $participants_stmt = \eBizIndia\PDOConn::query($participants_sql, [':cycle_id' => $current_cycle['id']]);
    $leaderboard_data['total_participants'] = $participants_stmt->fetchColumn() ?: 0;
    
    // Get user's position
    $user_position_sql = "SELECT ls.*, u.username 
                          FROM leaderboard_stats ls
                          JOIN users u ON ls.user_id = u.id
                          WHERE ls.user_id = :user_id AND ls.cycle_id = :cycle_id";
    $user_position_stmt = \eBizIndia\PDOConn::query($user_position_sql, [
        ':user_id' => $user_id, 
        ':cycle_id' => $current_cycle['id']
    ]);
    $leaderboard_data['user_position'] = $user_position_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get top performers by category
    $top_performers_sql = "(SELECT 
                             'Points Leader' as category,
                             u.username, m.name,
                             ls.total_points as value,
                             'trophy-gold.png' as icon,
                             'warning' as color
                           FROM leaderboard_stats ls
                           JOIN users u ON ls.user_id = u.id
                           JOIN members m ON u.profile_id=m.id
                           WHERE ls.cycle_id = :cycle_id AND ls.is_visible = 1
                           ORDER BY ls.total_points DESC
                           LIMIT 1)
                           
                           UNION ALL
                           
                           (SELECT 
                             'Completion Leader' as category,
                             u.username, m.name,
                             ls.completion_rate as value,
                             'chart-line-green.png' as icon,
                             'success' as color
                           FROM leaderboard_stats ls
                           JOIN users u ON ls.user_id = u.id
                           JOIN members m ON u.profile_id=m.id
                           WHERE ls.cycle_id = :cycle_id AND ls.is_visible = 1
                           ORDER BY ls.completion_rate DESC
                           LIMIT 1)
                           
                           UNION ALL
                           
                           (SELECT 
                             'Streak Leader' as category,
                             u.username, m.name, 
                             ls.current_streak as value,
                             'fire-red.png' as icon,
                             'danger' as color
                           FROM leaderboard_stats ls
                           JOIN users u ON ls.user_id = u.id
                           JOIN members m ON u.profile_id=m.id
                           WHERE ls.cycle_id = :cycle_id AND ls.is_visible = 1
                           ORDER BY ls.current_streak DESC
                           LIMIT 1)";
    
    $top_performers_stmt = \eBizIndia\PDOConn::query($top_performers_sql, [':cycle_id' => $current_cycle['id']]);
    $leaderboard_data['top_performers'] = $top_performers_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get achievement leaders
    $achievement_leaders_sql = "SELECT 
                                  u.username, m.name,
                                  ls.achievements_count,
                                  ls.total_points,
                                  ls.rank_position
                                FROM leaderboard_stats ls
                                JOIN users u ON ls.user_id = u.id
                                JOIN members m ON u.profile_id=m.id
                                WHERE ls.cycle_id = :cycle_id AND ls.is_visible = 1
                                ORDER BY ls.achievements_count DESC, ls.total_points DESC
                                LIMIT 5";
    
    $achievement_leaders_stmt = \eBizIndia\PDOConn::query($achievement_leaders_sql, [':cycle_id' => $current_cycle['id']]);
    $leaderboard_data['achievement_leaders'] = $achievement_leaders_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get streak leaders
    $streak_leaders_sql = "SELECT 
                             u.username, m.name,
                             ls.current_streak,
                             ls.total_points,
                             ls.rank_position
                           FROM leaderboard_stats ls
                           JOIN users u ON ls.user_id = u.id
                           JOIN members m ON u.profile_id=m.id
                           WHERE ls.cycle_id = :cycle_id AND ls.is_visible = 1 AND ls.current_streak > 0
                           ORDER BY ls.current_streak DESC, ls.total_points DESC
                           LIMIT 5";
    
    $streak_leaders_stmt = \eBizIndia\PDOConn::query($streak_leaders_sql, [':cycle_id' => $current_cycle['id']]);
    $leaderboard_data['streak_leaders'] = $streak_leaders_stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Set template data
$template_data = array_merge($leaderboard_data, [
    'page_title' => $page_title,
    'page_description' => $page_description,
    'user_id' => $user_id
]);

// Register body template
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . '12-week-leaderboard.tpl';
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