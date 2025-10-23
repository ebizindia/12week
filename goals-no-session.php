<?php
// Temporary version without session dependencies
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Basic setup without inc.php
require_once 'config.php';

// Simple database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Use user ID 1 for testing
$user_id = 1;

// Get current cycle based on date
$today = date('Y-m-d');
$cycle_sql = "SELECT * FROM cycles 
             WHERE start_date <= :today 
             AND end_date >= :today 
             ORDER BY start_date DESC 
             LIMIT 1";
$cycle_stmt = $pdo->prepare($cycle_sql);
$cycle_stmt->execute([':today' => $today]);
$current_cycle = $cycle_stmt->fetch(PDO::FETCH_ASSOC);

// Calculate current week
$current_week = 1;
$actual_current_week = 1;
$week_start_date = "Week 1";
$week_end_date = "";

if ($current_cycle && isset($current_cycle['start_date'])) {
    try {
        $cycle_start = new DateTime($current_cycle['start_date']);
        $today = new DateTime();
        
        $days_since_start = $today->diff($cycle_start)->days;
        $calculated_week = floor($days_since_start / 7) + 1;
        $actual_current_week = max(1, min(12, $calculated_week));
        
        $current_week = isset($_GET['week']) ? (int)$_GET['week'] : $actual_current_week;
        $current_week = max(1, min(12, $current_week));
        
        // Calculate week dates
        $week_start = clone $cycle_start;
        $week_start->add(new DateInterval('P' . (($current_week - 1) * 7) . 'D'));
        $week_end = clone $week_start;
        $week_end->add(new DateInterval('P6D'));
        
        $week_start_date = $week_start->format('M j, Y');
        $week_end_date = $week_end->format('M j, Y');
    } catch (Exception $e) {
        // Use defaults if date calculation fails
    }
}

// Get categories
$categories_sql = "SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order, name";
$categories_stmt = $pdo->query($categories_sql);
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get goals with tasks for current week
$goals_by_category = [];
if ($current_cycle) {
    $goals_sql = "SELECT g.*, c.name as category_name, c.color_code 
                  FROM goals g 
                  JOIN categories c ON g.category_id = c.id 
                  WHERE g.user_id = ? AND g.cycle_id = ? 
                  ORDER BY c.sort_order, c.name, g.created_at";
    
    $goals_stmt = $pdo->prepare($goals_sql);
    $goals_stmt->execute([$user_id, $current_cycle['id']]);
    $goals = $goals_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get tasks for current week
    foreach ($goals as $goal) {
        $tasks_sql = "SELECT * FROM tasks WHERE goal_id = ? AND week_number = ? ORDER BY created_at";
        $tasks_stmt = $pdo->prepare($tasks_sql);
        $tasks_stmt->execute([$goal['id'], $current_week]);
        $tasks = $tasks_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($tasks)) {
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
            
            $goal['tasks'] = $tasks;
            $goals_by_category[$category_id]['goals'][] = $goal;
            $goals_by_category[$category_id]['total_tasks'] += count($tasks);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>12-Week Goals - Week <?= $current_week ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <div>
                    <h1 class="h3 mb-0">My Goals</h1>
                    <p class="text-muted mb-0">Weekly view for the current 12-week cycle</p>
                </div>
                <?php if ($current_cycle): ?>
                <div class="text-right">
                    <small class="text-muted">Current Cycle:</small><br>
                    <strong><?= htmlspecialchars($current_cycle['name']) ?></strong>
                </div>
                <?php endif; ?>
            </div>

            <!-- Week Navigation -->
            <div class="card mb-4 bg-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="?week=<?= max(1, $current_week - 1) ?>" class="btn btn-outline-light btn-sm <?= $current_week <= 1 ? 'disabled' : '' ?>">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                        
                        <div class="text-center">
                            <h4 class="mb-1">
                                Week <?= $current_week ?>
                                <?php if ($current_week == $actual_current_week): ?>
                                <span class="badge bg-success ms-2">Current</span>
                                <?php endif; ?>
                            </h4>
                            <small><?= $week_start_date ?><?= !empty($week_end_date) ? ' - ' . $week_end_date : '' ?></small>
                        </div>
                        
                        <a href="?week=<?= min(12, $current_week + 1) ?>" class="btn btn-outline-light btn-sm <?= $current_week >= 12 ? 'disabled' : '' ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Goals and Tasks -->
            <?php if (empty($goals_by_category)): ?>
            <div class="text-center py-5">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <h4>No Tasks for Week <?= $current_week ?></h4>
                <p class="text-muted">No tasks are scheduled for this week.</p>
            </div>
            <?php else: ?>
            
            <?php foreach ($goals_by_category as $category_data): ?>
            <div class="card mb-4">
                <div class="card-header" style="background-color: <?= $category_data['category']['color_code'] ?>15; border-left: 4px solid <?= $category_data['category']['color_code'] ?>;">
                    <h5 class="mb-0" style="color: <?= $category_data['category']['color_code'] ?>;">
                        <i class="fas fa-tag me-2"></i><?= htmlspecialchars($category_data['category']['name']) ?>
                        <small class="text-muted ms-2">(<?= count($category_data['goals']) ?> goals, <?= $category_data['total_tasks'] ?> tasks)</small>
                    </h5>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30%;">Task</th>
                                    <th style="width: 6%;" class="text-center">M</th>
                                    <th style="width: 6%;" class="text-center">T</th>
                                    <th style="width: 6%;" class="text-center">W</th>
                                    <th style="width: 6%;" class="text-center">T</th>
                                    <th style="width: 6%;" class="text-center">F</th>
                                    <th style="width: 6%;" class="text-center">S</th>
                                    <th style="width: 6%;" class="text-center">S</th>
                                    <th style="width: 8%;" class="text-center">Total</th>
                                    <th style="width: 8%;" class="text-center">Target</th>
                                    <th style="width: 8%;" class="text-center">Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($category_data['goals'] as $goal): ?>
                                <!-- Goal Header -->
                                <tr class="table-primary">
                                    <td colspan="11" class="fw-bold">
                                        <i class="fas fa-bullseye me-2"></i><?= htmlspecialchars($goal['title']) ?>
                                    </td>
                                </tr>
                                
                                <!-- Tasks -->
                                <?php foreach ($goal['tasks'] as $task): ?>
                                <?php
                                $completed_days = ($task['mon'] ?? 0) + ($task['tue'] ?? 0) + ($task['wed'] ?? 0) + 
                                                 ($task['thu'] ?? 0) + ($task['fri'] ?? 0) + ($task['sat'] ?? 0) + ($task['sun'] ?? 0);
                                $target = $task['weekly_target'] ?? 1;
                                $score_percent = $target > 0 ? round(($completed_days / $target) * 100) : 0;
                                $score_class = $completed_days >= $target ? 'success' : ($completed_days > 0 ? 'warning' : 'danger');
                                ?>
                                <tr>
                                    <td class="align-middle" style="padding: 12px;">
                                        <div class="fw-medium" style="margin-left: 20px;">&nbsp;&nbsp;<?= htmlspecialchars($task['title']) ?></div>
                                    </td>
                                    
                                    <?php 
                                    $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
                                    foreach ($days as $day): 
                                        $is_checked = ($task[$day] ?? 0) == 1;
                                    ?>
                                    <td class="text-center align-middle" style="padding: 12px;">
                                        <input type="checkbox" class="form-check-input" 
                                               <?= $is_checked ? 'checked' : '' ?>
                                               style="transform: scale(1.3);" disabled>
                                    </td>
                                    <?php endforeach; ?>
                                    
                                    <td class="text-center align-middle" style="padding: 15px;">
                                        <span class="badge bg-info text-white px-3 py-2"><?= $completed_days ?></span>
                                    </td>
                                    
                                    <td class="text-center align-middle" style="padding: 15px;">
                                        <span class="badge bg-primary text-white px-3 py-2"><?= $target ?></span>
                                    </td>
                                    
                                    <td class="text-center align-middle" style="padding: 15px;">
                                        <span class="badge bg-<?= $score_class ?> text-white px-3 py-2"><?= $score_percent ?>%</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>