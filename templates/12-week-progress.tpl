<?php
$current_cycle = $this->body_template_data['current_cycle'];
$current_week = $this->body_template_data['current_week'];
$cycle_progress = $this->body_template_data['cycle_progress'];
$weekly_scores = $this->body_template_data['weekly_scores'];
$category_performance = $this->body_template_data['category_performance'];
$member_rankings = $this->body_template_data['member_rankings'];
$completion_trends = $this->body_template_data['completion_trends'];
$goal_statistics = $this->body_template_data['goal_statistics'];
$task_statistics = $this->body_template_data['task_statistics'];
$all_cycles = $this->body_template_data['all_cycles'];
$error_message = $this->body_template_data['error_message'];
$selected_cycle_id = $this->body_template_data['selected_cycle_id'];
$is_admin = $this->body_template_data['is_admin'];
?>
<style>
/* Custom Styles for Progress Report */
.progress-page-wrapper {
    padding-top: 30px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}

.page-header-section {
    background: #fff;
    border-radius: 10px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
}

.page-header-section h1 {
    color: #2c3e50;
    font-weight: 600;
}

.page-header-section .text-muted {
    color: #7f8c8d !important;
}

.cycle-selector-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.cycle-selector-card .card-body {
    padding: 20px;
}

.form-select:focus,
.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.cycle-overview-card {
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    overflow: hidden;
}

.cycle-overview-card .card-body {
    padding: 25px 30px;
}

.progress-enhanced {
    height: 10px;
    border-radius: 10px;
    background: rgba(255,255,255,0.2);
    overflow: hidden;
}

.progress-enhanced .progress-bar {
    background: white;
    border-radius: 10px;
}

.chart-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.chart-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 20px 25px;
}

.chart-card .card-header h5 {
    color: white;
    font-weight: 600;
    margin: 0;
}

.chart-card .card-body {
    padding: 25px;
}

.category-performance-item {
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.category-performance-item:last-child {
    border-bottom: none;
}

.category-performance-item:hover {
    background: #f8f9fa;
    padding-left: 10px;
    border-radius: 8px;
}

.color-indicator {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: inline-block;
    border: 3px solid #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.category-name {
    color: #2c3e50;
    font-weight: 600;
    font-size: 1rem;
}

.stat-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.stat-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 20px 25px;
}

.stat-card .card-header h5 {
    color: white;
    font-weight: 600;
    margin: 0;
}

.stat-card .card-body {
    padding: 30px 25px;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

.ranking-table-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.ranking-table-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 20px 25px;
}

.ranking-table-card .card-header h5 {
    color: white;
    font-weight: 600;
    margin: 0;
}

.table-enhanced {
    margin-bottom: 0;
}

.table-enhanced thead th {
    background: #f8f9fa;
    color: #2c3e50;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    padding: 15px;
}

.table-enhanced tbody tr {
    transition: all 0.3s ease;
}

.table-enhanced tbody tr:hover {
    background: #f8f9fa;
    transform: scale(1.01);
}

.table-enhanced tbody td {
    padding: 15px;
    vertical-align: middle;
}

.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 16px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
}

.badge-enhanced {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.85rem;
}

.badge.bg-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
}

.badge.bg-warning {
    background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%) !important;
}

.badge.bg-danger {
    background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%) !important;
}

.badge.bg-info {
    background: linear-gradient(135deg, #2196F3 0%, #00BCD4 100%) !important;
}

.badge.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.badge.bg-secondary {
    background: linear-gradient(135deg, #bdc3c7 0%, #95a5a6 100%) !important;
}

.btn-enhanced {
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-enhanced:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.empty-state {
    background: #fff;
    border-radius: 12px;
    padding: 60px 30px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.empty-state-icon {
    font-size: 4rem;
    color: #cbd5e0;
    margin-bottom: 20px;
}

.alert-enhanced {
    border-radius: 10px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.progress-bar-category {
    height: 8px;
    border-radius: 10px;
    overflow: hidden;
}
</style>

<div class="progress-page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Page Header -->
                <div class="page-header-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-1">
                                <?= $is_admin ? 'Analytics Dashboard' : 'Progress Report' ?>
                            </h1>
                            <p class="text-muted mb-0">
                                <?= $is_admin ? 'Comprehensive analytics and member insights' : 'Your detailed progress and performance analytics' ?>
                            </p>
                        </div>
                        <div class="d-flex">
                            <?php if ($is_admin): ?>
                            <a href="12-week-admin-dashboard.php" class="btn btn-outline-primary btn-enhanced mr-2">
                                <i class="fas fa-users mr-2"></i>Member Overview
                            </a>
                            <a href="12-week-manage-cycles.php" class="btn btn-outline-secondary btn-enhanced">
                                <i class="fas fa-sync-alt mr-2"></i>Manage Cycles
                            </a>
                            <?php else: ?>
                            <a href="12-week-dashboard.php" class="btn btn-outline-primary btn-enhanced">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Cycle Selector -->
                <?php if (!empty($all_cycles)): ?>
                <div class="card cycle-selector-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <label for="cycleSelector" class="form-label mb-0">
                                    <i class="fas fa-filter mr-2"></i><strong>Select Cycle for Analysis:</strong>
                                </label>
                            </div>
                            <div class="col">
                                <select class="form-control" id="cycleSelector" onchange="changeCycle(this.value)">
                                    <option value="">Current Active Cycle</option>
                                    <?php foreach ($all_cycles as $cycle): ?>
                                    <option value="<?= $cycle['id'] ?>" <?= $selected_cycle_id == $cycle['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cycle['name']) ?> 
                                        (<?= date('M Y', strtotime($cycle['start_date'])) ?> - <?= date('M Y', strtotime($cycle['end_date'])) ?>)
                                        <?= $cycle['status'] === 'active' ? '- Active' : '- Completed' ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                <!-- No Data -->
                <div class="alert alert-warning alert-enhanced" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i><?= htmlspecialchars($error_message) ?>
                </div>
                
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4 class="mb-3">No Data Available</h4>
                    <p class="text-muted mb-4">No cycle data found for analysis.</p>
                    <?php if ($is_admin): ?>
                    <a href="12-week-manage-cycles.php" class="btn btn-primary btn-enhanced">
                        <i class="fas fa-plus mr-2"></i>Create New Cycle
                    </a>
                    <?php endif; ?>
                </div>
                
                <?php else: ?>

                <!-- Cycle Overview -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card cycle-overview-card text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h4 class="card-title mb-2">
                                            <i class="fas fa-sync-alt mr-2"></i><?= htmlspecialchars($current_cycle['name']) ?>
                                        </h4>
                                        <p class="card-text mb-3">
                                            <?php if ($current_week == 0): ?>
                                                <i class="fas fa-hourglass-start mr-2"></i>Cycle not started yet
                                            <?php elseif ($current_week > 12): ?>
                                                <i class="fas fa-flag-checkered mr-2"></i>Cycle completed
                                            <?php else: ?>
                                                <i class="fas fa-calendar-week mr-2"></i>Week <?= $current_week ?> of 12
                                            <?php endif; ?>
                                        </p>
                                        
                                        <!-- Progress Bar -->
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small style="opacity: 0.9;">Cycle Progress</small>
                                                <small style="opacity: 0.9;"><strong><?= $cycle_progress ?>%</strong></small>
                                            </div>
                                            <div class="progress progress-enhanced">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: <?= $cycle_progress ?>%" 
                                                     aria-valuenow="<?= $cycle_progress ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-right">
                                        <small style="opacity: 0.9;" class="d-block mb-2">
                                            <i class="far fa-calendar mr-1"></i>Cycle Period
                                        </small>
                                        <strong style="font-size: 0.9rem;">
                                            <?= date('M j, Y', strtotime($current_cycle['start_date'])) ?> 
                                            <span class="mx-2">to</span>
                                            <?= date('M j, Y', strtotime($current_cycle['end_date'])) ?>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Weekly Progress Chart -->
                <?php if (!empty($weekly_scores)): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card chart-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-line mr-2"></i>Weekly Performance Trend</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="weeklyProgressChart" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Category Performance -->
                <?php if (!empty($category_performance)): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card chart-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-tags mr-2"></i>Performance by Category</h5>
                            </div>
                            <div class="card-body">
                                <div class="category-performance">
                                    <?php foreach ($category_performance as $category): ?>
                                    <div class="category-performance-item">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator mr-3" style="background-color: <?= htmlspecialchars($category['color_code']) ?>;"></div>
                                                <div>
                                                    <div class="category-name"><?= htmlspecialchars($category['category_name']) ?></div>
                                                    <small class="text-muted">
                                                        <?= $category['completed_goals'] ?> of <?= $category['total_goals'] ?> goals completed
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <strong class="text-<?= $category['completion_rate'] >= 70 ? 'success' : ($category['completion_rate'] >= 40 ? 'warning' : 'danger') ?>">
                                                    <?= round($category['completion_rate'], 1) ?>%
                                                </strong>
                                            </div>
                                        </div>
                                        <div class="progress progress-bar-category">
                                            <div class="progress-bar bg-<?= $category['completion_rate'] >= 70 ? 'success' : ($category['completion_rate'] >= 40 ? 'warning' : 'danger') ?>" 
                                                 style="width: <?= $category['completion_rate'] ?>%">
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Completion Trends Chart -->
                <?php if (!empty($completion_trends)): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card chart-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-area mr-2"></i>Completion Trends</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="completionTrendsChart" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Member Rankings (Admin Only) -->
                <?php if ($is_admin && !empty($member_rankings)): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card ranking-table-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-users mr-2"></i>Member Rankings</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-enhanced">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Member</th>
                                                <th class="text-center">Goals</th>
                                                <th class="text-center">Completion</th>
                                                <!-- <th class="text-center">Points</th> -->
                                                <th class="text-center">Streak</th>
                                                <!-- <th class="text-center">Last Activity</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $rank=0;
                                                $p_avg=-1;
                                                $count=0;
                                                foreach ($member_rankings as $member): 
                                                    ++$count;
                                                    if($p_avg==-1 || $p_avg*10000<$member['avg_score']){
                                                        $rank=$count;
                                                    }
                                                ?>
                                            <tr>
                                                <td>
                                                    <strong class="text-primary">#<?= $rank ?></strong>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm mr-2">
                                                            <?= strtoupper(substr($member['name'], 0, 2)) ?>
                                                        </div>
                                                        <strong><?= htmlspecialchars($member['name']) ?></strong>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary text-white badge-enhanced">
                                                        <?= $member['total_goals'] ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?= $member['completion_rate'] >= 70 ? 'success' : ($member['completion_rate'] >= 40 ? 'warning' : 'danger') ?> text-white badge-enhanced">
                                                        <?= round($member['completion_rate'], 1) ?>%
                                                    </span>
                                                </td>
                                                <!-- <td class="text-center">
                                                    <strong><?= $member['total_points'] ?></strong>
                                                </td> -->
                                                <td class="text-center">
                                                    <?php if ($member['current_streak'] > 0): ?>
                                                    <span class="badge bg-danger text-white badge-enhanced">
                                                        <i class="fas fa-fire mr-1"></i><?= $member['current_streak'] ?>
                                                    </span>
                                                    <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <!-- <td class="text-center">
                                                    <?php if ($member['last_activity']): ?>
                                                        <small class="text-muted"><?= date('M j', strtotime($member['last_activity'])) ?></small>
                                                    <?php else: ?>
                                                        <small class="text-muted">Never</small>
                                                    <?php endif; ?>
                                                </td> -->
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <?php if (!empty($goal_statistics) || !empty($task_statistics)): ?>
                <div class="row">
                    <?php if (!empty($goal_statistics)): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card stat-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-bullseye mr-2"></i>Goal Statistics</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 text-center mb-4">
                                        <div class="stat-number text-primary"><?= isset($goal_statistics['total_goals']) ? $goal_statistics['total_goals'] : 0 ?></div>
                                        <div class="stat-label">Total Goals</div>
                                    </div>
                                    <div class="col-6 text-center mb-4">
                                        <div class="stat-number text-success"><?= isset($goal_statistics['completed']) ? $goal_statistics['completed'] : 0 ?></div>
                                        <div class="stat-label">Completed</div>
                                    </div>
                                    <div class="col-6 text-center">
                                        <div class="stat-number text-warning"><?= isset($goal_statistics['in_progress']) ? $goal_statistics['in_progress'] : 0 ?></div>
                                        <div class="stat-label">In Progress</div>
                                    </div>
                                    <div class="col-6 text-center">
                                        <div class="stat-number text-info"><?= isset($goal_statistics['not_started']) ? $goal_statistics['not_started'] : 0 ?></div>
                                        <div class="stat-label">Not Started</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($task_statistics)): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card stat-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-tasks mr-2"></i>Task Statistics</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 text-center mb-4">
                                        <div class="stat-number text-primary"><?= isset($task_statistics['total']) ? $task_statistics['total'] : 0 ?></div>
                                        <div class="stat-label">Total Tasks</div>
                                    </div>
                                    <div class="col-6 text-center mb-4">
                                        <div class="stat-number text-success"><?= isset($task_statistics['fully_completed_tasks']) ? $task_statistics['fully_completed_tasks'] : 0 ?></div>
                                        <div class="stat-label">Completed</div>
                                    </div>
                                    <div class="col-6 text-center">
                                        <div class="stat-number text-warning"><?= isset($task_statistics['partial']) ? $task_statistics['partial'] : 0 ?></div>
                                        <div class="stat-label">Partial</div>
                                    </div>
                                    <div class="col-6 text-center">
                                        <div class="stat-number text-danger"><?= isset($task_statistics['not_started']) ? $task_statistics['not_started'] : 0 ?></div>
                                        <div class="stat-label">Not Started</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php /*if (!empty($weekly_scores) || !empty($completion_trends)): ?>
<!--<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>-->
<script>
// Chart.js global configuration
Chart.defaults.font.family = "'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif";
Chart.defaults.color = '#6c757d';

<?php if (!empty($weekly_scores)): ?>
// Weekly Progress Chart
$(document).ready(function() {
const weeklyData = <?= json_encode(array_values($weekly_scores)) ?>;
const weeklyCtx = document.getElementById('weeklyProgressChart');

new Chart(weeklyCtx, {
    type: 'line',
    data: {
        labels: weeklyData.map(w => 'Week ' + w.week_number),
        datasets: [{
            label: 'Weekly Score (%)',
            data: weeklyData.map(w => w.score_percentage),
            borderColor: 'rgb(102, 126, 234)',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: 'rgb(102, 126, 234)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return 'Score: ' + context.parsed.y.toFixed(1) + '%';
                    }
                }
            }
        }
    }
});
});
<?php endif; ?>

<?php if (!empty($completion_trends)): ?>
// Completion Trends Chart
$(document).ready(function() {
const trendsData = <?= json_encode(array_values($completion_trends)) ?>;
const trendsCtx = document.getElementById('completionTrendsChart');

new Chart(trendsCtx, {
    type: 'bar',
    data: {
        labels: trendsData.map(t => 'Week ' + t.week_number),
        datasets: [
            {
                label: 'Completed',
                data: trendsData.map(t => t.completed),
                backgroundColor: 'rgba(17, 153, 142, 0.8)',
                borderRadius: 6
            },
            {
                label: 'In Progress',
                data: trendsData.map(t => t.in_progress),
                backgroundColor: 'rgba(242, 153, 74, 0.8)',
                borderRadius: 6
            },
            {
                label: 'Not Started',
                data: trendsData.map(t => t.not_started),
                backgroundColor: 'rgba(238, 9, 121, 0.8)',
                borderRadius: 6
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            x: {
                stacked: false,
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8
            }
        }
    }
});
});
<?php endif; ?>

// Cycle selector change
function changeCycle(cycleId) {
    if (cycleId) {
        window.location.href = '12-week-progress.php?cycle_id=' + cycleId;
    } else {
        window.location.href = '12-week-progress.php';
    }
}
</script>
<?php endif; */?>
