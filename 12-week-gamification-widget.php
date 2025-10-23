<?php
// Gamification Widget - Include this in dashboard and other pages
// Usage: include '12-week-gamification-widget.php';

if (!isset($user_id) || !isset($current_cycle)) {
    return; // Skip if required variables not set
}

// Get user's gamification data
$user_stats = \eBizIndia\Gamification::getUserStats($user_id, $current_cycle['id']);
$recent_achievements = \eBizIndia\Gamification::getRecentAchievements($user_id, 7);
$all_achievements = \eBizIndia\Gamification::getUserAchievements($user_id, $current_cycle['id']);

// Get leaderboard position
$leaderboard_sql = "SELECT rank_position, total_points, completion_rate, current_streak 
                    FROM leaderboard_stats 
                    WHERE user_id = :user_id AND cycle_id = :cycle_id";
$leaderboard_stmt = \eBizIndia\PDOConn::query($leaderboard_sql, [':user_id' => $user_id, ':cycle_id' => $current_cycle['id']]);
$leaderboard_position = $leaderboard_stmt->fetch(PDO::FETCH_ASSOC);

// Get total participants for context
$participants_sql = "SELECT COUNT(*) as total_participants FROM leaderboard_stats WHERE cycle_id = :cycle_id AND is_visible = 1";
$participants_stmt = \eBizIndia\PDOConn::query($participants_sql, [':cycle_id' => $current_cycle['id']]);
$total_participants = $participants_stmt->fetchColumn() ?: 1;

// Default values if no stats yet
if (!$user_stats) {
    $user_stats = [
        'total_tasks_completed' => 0,
        'total_goals_created' => 0,
        'total_tasks_planned' => 0,
        'current_streak' => 0,
        'longest_streak' => 0,
        'perfect_weeks' => 0,
        'weeks_completed' => 0,
        'total_points' => 0
    ];
}

if (!$leaderboard_position) {
    $leaderboard_position = [
        'rank_position' => $total_participants,
        'total_points' => 0,
        'completion_rate' => 0,
        'current_streak' => 0
    ];
}
?>

<!-- Gamification Widget -->
<div class="gamification-widget">
    <!-- Achievement Notifications -->
    <?php if (!empty($recent_achievements)): ?>
    <div class="achievement-notifications mb-3">
        <?php foreach (array_slice($recent_achievements, 0, 2) as $achievement): ?>
        <div class="alert alert-success alert-dismissible fade show achievement-alert" role="alert">
            <div class="d-flex align-items-center">
                <i class="<?= htmlspecialchars($achievement['icon']) ?> fa-2x text-<?= htmlspecialchars($achievement['badge_color']) ?> me-3"></i>
                <div>
                    <strong>Achievement Unlocked!</strong><br>
                    <span class="fw-bold"><?= htmlspecialchars($achievement['name']) ?></span><br>
                    <small class="text-muted"><?= htmlspecialchars($achievement['description']) ?></small>
                    <span class="badge bg-warning ms-2">+<?= $achievement['points'] ?> points</span>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Stats Cards Row -->
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="card bg-primary text-white h-100">
                <div class="card-body text-center p-3">
                    <i class="fas fa-trophy fa-2x mb-2"></i>
                    <div class="h4 mb-0"><?= $user_stats['total_points'] ?></div>
                    <small>Total Points</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="card bg-success text-white h-100">
                <div class="card-body text-center p-3">
                    <i class="fas fa-fire fa-2x mb-2"></i>
                    <div class="h4 mb-0"><?= $user_stats['current_streak'] ?></div>
                    <small>Day Streak</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="card bg-info text-white h-100">
                <div class="card-body text-center p-3">
                    <i class="fas fa-medal fa-2x mb-2"></i>
                    <div class="h4 mb-0"><?= count($all_achievements) ?></div>
                    <small>Achievements</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="card bg-warning text-white h-100">
                <div class="card-body text-center p-3">
                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                    <div class="h4 mb-0">#<?= $leaderboard_position['rank_position'] ?></div>
                    <small>Rank</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress and Achievements Row -->
    <div class="row">
        <!-- Performance Stats -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Performance Stats</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h5 text-primary"><?= $user_stats['total_tasks_completed'] ?></div>
                            <small class="text-muted">Tasks Done</small>
                        </div>
                        <div class="col-4">
                            <div class="h5 text-success"><?= $user_stats['total_goals_created'] ?></div>
                            <small class="text-muted">Goals Set</small>
                        </div>
                        <div class="col-4">
                            <div class="h5 text-info"><?= $user_stats['perfect_weeks'] ?></div>
                            <small class="text-muted">Perfect Weeks</small>
                        </div>
                    </div>
                    
                    <hr class="my-3">
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Leaderboard Position</small>
                        <span class="badge bg-<?= $leaderboard_position['rank_position'] <= 3 ? 'warning' : 'secondary' ?>">
                            #<?= $leaderboard_position['rank_position'] ?> of <?= $total_participants ?>
                        </span>
                    </div>
                    
                    <div class="progress mb-2" style="height: 8px;">
                        <?php 
                        $position_percentage = 100 - (($leaderboard_position['rank_position'] - 1) / max($total_participants - 1, 1)) * 100;
                        ?>
                        <div class="progress-bar bg-warning" style="width: <?= $position_percentage ?>%"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Longest Streak: <?= $user_stats['longest_streak'] ?> days</small>
                        <small class="text-muted"><?= round($leaderboard_position['completion_rate'], 1) ?>% completion</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Achievements -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-trophy me-2"></i>Achievements</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#achievementsModal">
                        View All
                    </button>
                </div>
                <div class="card-body">
                    <?php if (empty($all_achievements)): ?>
                    <div class="text-center py-3">
                        <i class="fas fa-trophy fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Complete tasks to unlock achievements!</p>
                    </div>
                    <?php else: ?>
                    <div class="achievements-list">
                        <?php foreach (array_slice($all_achievements, 0, 3) as $achievement): ?>
                        <div class="d-flex align-items-center mb-2">
                            <i class="<?= htmlspecialchars($achievement['icon']) ?> text-<?= htmlspecialchars($achievement['badge_color']) ?> me-2"></i>
                            <div class="flex-grow-1">
                                <div class="fw-medium small"><?= htmlspecialchars($achievement['name']) ?></div>
                                <small class="text-muted"><?= date('M j', strtotime($achievement['earned_at'])) ?></small>
                            </div>
                            <span class="badge bg-<?= htmlspecialchars($achievement['badge_color']) ?> badge-sm">
                                +<?= $achievement['points'] ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if (count($all_achievements) > 3): ?>
                        <div class="text-center">
                            <small class="text-muted">+<?= count($all_achievements) - 3 ?> more achievements</small>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Achievements Modal -->
<div class="modal fade" id="achievementsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Your Achievements</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php if (empty($all_achievements)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                    <h5>No Achievements Yet</h5>
                    <p class="text-muted">Complete tasks and reach milestones to unlock achievements!</p>
                </div>
                <?php else: ?>
                <div class="row">
                    <?php foreach ($all_achievements as $achievement): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card border-<?= htmlspecialchars($achievement['badge_color']) ?>">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="<?= htmlspecialchars($achievement['icon']) ?> fa-2x text-<?= htmlspecialchars($achievement['badge_color']) ?> me-3"></i>
                                    <div>
                                        <h6 class="mb-0"><?= htmlspecialchars($achievement['name']) ?></h6>
                                        <small class="text-muted"><?= date('M j, Y', strtotime($achievement['earned_at'])) ?></small>
                                    </div>
                                    <span class="badge bg-<?= htmlspecialchars($achievement['badge_color']) ?> ms-auto">
                                        +<?= $achievement['points'] ?>
                                    </span>
                                </div>
                                <p class="card-text small text-muted mb-0">
                                    <?= htmlspecialchars($achievement['description']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.gamification-widget .card {
    transition: transform 0.2s ease;
}

.gamification-widget .card:hover {
    transform: translateY(-2px);
}

.achievement-alert {
    border-left: 4px solid #28a745;
    background: linear-gradient(90deg, rgba(40,167,69,0.1) 0%, rgba(255,255,255,1) 100%);
}

.badge-sm {
    font-size: 0.7rem;
}

.achievements-list {
    max-height: 200px;
    overflow-y: auto;
}
</style>

<script>
// Auto-hide achievement notifications after 10 seconds
$(document).ready(function() {
    setTimeout(function() {
        $('.achievement-alert').fadeOut('slow');
    }, 10000);
});
</script>