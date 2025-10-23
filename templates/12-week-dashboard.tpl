<?php
$current_cycle = $this->body_template_data['current_cycle'];
$current_week = $this->body_template_data['current_week'];
$days_remaining = $this->body_template_data['days_remaining'];
$cycle_score = $this->body_template_data['cycle_score'];
$week_score = $this->body_template_data['week_score'];
$total_goals = $this->body_template_data['total_goals'];
$goals_by_category = $this->body_template_data['goals_by_category'];
$this_week_tasks = $this->body_template_data['this_week_tasks'];
$error_message = $this->body_template_data['error_message'];
?>
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
/* ============================================
   MOBILE-FIRST DASHBOARD DESIGN
   ============================================ */

:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --danger-gradient: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
    --warning-gradient: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
    --info-gradient: linear-gradient(135deg, #2196F3 0%, #00BCD4 100%);
    --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    
    --text-primary: #2c3e50;
    --text-secondary: #7f8c8d;
    --text-light: #95a5a6;
    
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
    --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
    --shadow-xl: 0 12px 32px rgba(0,0,0,0.16);
    
    --spacing-xs: 8px;
    --spacing-sm: 12px;
    --spacing-md: 16px;
    --spacing-lg: 24px;
    --spacing-xl: 32px;
}

* {
    -webkit-tap-highlight-color: transparent;
}

body {
    background: var(--bg-gradient);
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.container-fluid {
    padding-left: 0;
    padding-right: 0;
}

.dashboard-wrapper {
    min-height: 100vh;
    padding: 4px;
    padding-bottom: 80px;
}

/* ============================================
   PAGE HEADER
   ============================================ */
.page-header {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-sm) var(--spacing-xs);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-md);
}

.page-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 var(--spacing-xs) 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.page-header .subtitle {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
}

/* ============================================
   CYCLE OVERVIEW CARD
   ============================================ */
.cycle-overview {
    background: var(--primary-gradient);
    border-radius: var(--radius-lg);
    padding: var(--spacing-md) var(--spacing-xs);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-lg);
    color: white;
}

.cycle-overview h4 {
    font-size: 1.125rem;
    font-weight: 700;
    margin: 0 0 var(--spacing-xs) 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.cycle-status {
    font-size: 0.875rem;
    opacity: 0.95;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    margin-bottom: var(--spacing-md);
}

.cycle-period {
    background: rgba(255,255,255,0.2);
    border-radius: var(--radius-md);
    padding: var(--spacing-sm);
    backdrop-filter: blur(10px);
}

.cycle-period h6 {
    font-size: 0.75rem;
    opacity: 0.9;
    margin: 0 0 4px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.cycle-period .dates {
    font-size: 0.938rem;
    font-weight: 600;
}

/* ============================================
   STAT CARDS
   ============================================ */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-xs);
    margin-bottom: var(--spacing-md);
}

.stat-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-md) var(--spacing-sm);
    box-shadow: var(--shadow-md);
    text-align: center;
    transition: all 0.3s ease;
}

.stat-card:active {
    transform: scale(0.98);
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: var(--spacing-xs);
}

.stat-number.text-primary {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-number.text-success {
    background: var(--success-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-number.text-info {
    background: var(--info-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-number.text-warning {
    background: var(--warning-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-title {
    color: var(--text-primary);
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 4px;
}

.stat-description {
    color: var(--text-secondary);
    font-size: 0.75rem;
}

/* ============================================
   CONTENT CARDS
   ============================================ */
.content-card {
    background: white;
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.content-card-header {
    background: var(--primary-gradient);
    color: white;
    padding: var(--spacing-md) var(--spacing-xs);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: var(--spacing-sm);
}

.content-card-header h5 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.btn-header-action {
    background: rgba(255,255,255,0.2);
    border: 2px solid rgba(255,255,255,0.3);
    color: white;
    border-radius: var(--radius-md);
    padding: 6px 12px;
    font-weight: 600;
    font-size: 0.813rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    white-space: nowrap;
    min-height: 36px;
    transition: all 0.3s ease;
}

.btn-header-action:hover,
.btn-header-action:active {
    background: rgba(255,255,255,0.3);
    border-color: rgba(255,255,255,0.5);
    color: white;
    text-decoration: none;
}

.content-card-body {
    padding: var(--spacing-sm) var(--spacing-xs);
}

/* ============================================
   GOALS LIST
   ============================================ */
.category-item {
    padding: var(--spacing-sm) 0;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: var(--spacing-sm);
    transition: all 0.3s ease;
}

.category-item:last-child {
    border-bottom: none;
}

.category-item:active {
    background: #f8f9fa;
}

.category-info {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    flex: 1;
}

.category-badge {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    flex-shrink: 0;
    border: 2px solid #fff;
    box-shadow: var(--shadow-sm);
}

.category-name {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.938rem;
}

.category-count {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 6px 12px;
    border-radius: var(--radius-xl);
    font-weight: 700;
    font-size: 0.875rem;
    min-width: 40px;
    text-align: center;
}

/* ============================================
   TASKS LIST
   ============================================ */
.task-list {
    max-height: 400px;
    overflow-y: auto;
}

.task-item {
    padding: var(--spacing-sm);
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.3s ease;
    border-radius: var(--radius-md);
}

.task-item:last-child {
    border-bottom: none;
}

.task-item:active {
    background: #f8f9fa;
}

.task-title {
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: var(--spacing-xs);
    font-size: 0.938rem;
}

.task-meta {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-xs);
    margin-bottom: var(--spacing-xs);
}

.task-badge {
    padding: 4px 10px;
    border-radius: var(--radius-xl);
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.task-goal {
    font-size: 0.813rem;
    color: var(--text-secondary);
}

.task-progress {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: 4px 10px;
    background: #f8f9fa;
    border-radius: var(--radius-xl);
    font-size: 0.813rem;
    color: var(--text-secondary);
}

.task-progress i {
    color: #11998e;
}

/* ============================================
   QUICK ACTIONS
   ============================================ */
.quick-actions-card {
    background: white;
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.quick-actions-header {
    background: var(--primary-gradient);
    color: white;
    padding: var(--spacing-md) var(--spacing-xs);
}

.quick-actions-header h5 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.quick-actions-body {
    padding: var(--spacing-md) var(--spacing-xs);
}

.actions-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--spacing-sm);
}

.action-btn {
    background: white;
    border: 2px solid;
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
    min-height: 56px;
    transition: all 0.3s ease;
}

.action-btn.btn-progress {
    border-color: #11998e;
    color: #11998e;
}

.action-btn.btn-progress:hover,
.action-btn.btn-progress:active {
    background: var(--success-gradient);
    border-color: #11998e;
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.action-btn.btn-leaderboard {
    border-color: #2196F3;
    color: #2196F3;
}

.action-btn.btn-leaderboard:hover,
.action-btn.btn-leaderboard:active {
    background: var(--info-gradient);
    border-color: #2196F3;
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* ============================================
   EMPTY STATES
   ============================================ */
.empty-state {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    text-align: center;
    box-shadow: var(--shadow-md);
}

.empty-icon {
    font-size: 3rem;
    color: #cbd5e0;
    margin-bottom: var(--spacing-md);
}

.empty-state h4 {
    color: var(--text-primary);
    font-weight: 700;
    margin-bottom: var(--spacing-sm);
    font-size: 1.25rem;
}

.empty-state p {
    color: var(--text-secondary);
    margin-bottom: var(--spacing-lg);
    font-size: 0.938rem;
}

.empty-mini {
    padding: var(--spacing-xl) var(--spacing-md);
    text-align: center;
}

.empty-mini .empty-icon {
    font-size: 2.5rem;
}

.empty-mini p {
    color: var(--text-secondary);
    margin-bottom: var(--spacing-md);
    font-size: 0.875rem;
}

.btn-primary-action {
    background: var(--primary-gradient);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    padding: var(--spacing-sm) var(--spacing-lg);
    font-weight: 600;
    font-size: 0.938rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    min-height: 44px;
    transition: all 0.3s ease;
}

.btn-primary-action:hover,
.btn-primary-action:active {
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-view-all {
    background: transparent;
    border: 2px solid #667eea;
    color: #667eea;
    border-radius: var(--radius-md);
    padding: var(--spacing-xs) var(--spacing-md);
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    min-height: 36px;
    transition: all 0.3s ease;
}

.btn-view-all:hover,
.btn-view-all:active {
    background: var(--primary-gradient);
    border-color: #667eea;
    color: white;
    text-decoration: none;
}

/* ============================================
   ALERTS
   ============================================ */
.alert-modern {
    border-radius: var(--radius-md);
    border: none;
    padding: var(--spacing-md);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: flex-start;
    gap: var(--spacing-sm);
}

.alert-modern .alert-icon {
    font-size: 1.25rem;
    flex-shrink: 0;
}

.alert-modern .alert-content {
    flex: 1;
}

/* ============================================
   SCROLLBAR STYLING
   ============================================ */
.task-list::-webkit-scrollbar {
    width: 6px;
}

.task-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: var(--radius-xl);
}

.task-list::-webkit-scrollbar-thumb {
    background: #667eea;
    border-radius: var(--radius-xl);
}

.task-list::-webkit-scrollbar-thumb:hover {
    background: #764ba2;
}

/* ============================================
   TABLET+ RESPONSIVE ENHANCEMENTS
   ============================================ */
@media (min-width: 768px) {
    .dashboard-wrapper {
        padding: var(--spacing-md);
    }
    
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .page-header {
        padding: var(--spacing-lg);
    }
    
    .page-header h1 {
        font-size: 2rem;
    }
    
    .cycle-overview {
        padding: var(--spacing-lg);
    }
    
    .cycle-overview-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .cycle-overview h4 {
        font-size: 1.5rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: var(--spacing-md);
    }
    
    .stat-card {
        padding: var(--spacing-lg);
    }
    
    .stat-number {
        font-size: 2.5rem;
    }
    
    .content-card-header {
        padding: var(--spacing-lg);
    }
    
    .content-card-header h5 {
        font-size: 1.125rem;
    }
    
    .content-card-body {
        padding: var(--spacing-lg);
    }
    
    .category-item {
        padding: var(--spacing-md) 0;
    }
    
    .task-item {
        padding: var(--spacing-md);
    }
    
    .quick-actions-header {
        padding: var(--spacing-lg);
    }
    
    .quick-actions-body {
        padding: var(--spacing-lg);
    }
    
    .actions-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-md);
    }
}

@media (min-width: 992px) {
    .dashboard-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: var(--spacing-lg);
    }
    
    .content-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-lg);
    }
    
    .content-card {
        height: 100%;
        margin-bottom: 0;
    }
}

/* ============================================
   ANIMATIONS
   ============================================ */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card,
.content-card,
.quick-actions-card {
    animation: slideIn 0.3s ease-out;
}

/* ============================================
   ACCESSIBILITY
   ============================================ */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0,0,0,0);
    white-space: nowrap;
    border: 0;
}

button:focus-visible,
a:focus-visible {
    outline: 3px solid #667eea;
    outline-offset: 2px;
}
</style>

<div class="dashboard-wrapper">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1>
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </h1>
            <p class="subtitle">Overview of your current cycle progress</p>
        </div>

        <?php if ($error_message): ?>
        <!-- No Active Cycle -->
        <div class="alert alert-warning alert-modern" role="alert">
            <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="alert-content"><?= htmlspecialchars($error_message) ?></div>
        </div>
        
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-calendar-times"></i>
            </div>
            <h4>No Active Cycle</h4>
            <p>There is no active 12-week cycle at the moment.<br>Please contact your administrator to create a new cycle.</p>
        </div>
        
        <?php else: ?>
        
        <!-- Gamification Widget -->
        <?php include '12-week-gamification-widget.php'; ?>
        
        <!-- Cycle Overview -->
        <div class="cycle-overview">
            <div class="cycle-overview-content">
                <div>
                    <h4>
                        <i class="fas fa-sync-alt"></i>
                        <?= htmlspecialchars($current_cycle['name']) ?>
                    </h4>
                    <div class="cycle-status">
                        <?php if ($current_week == 0): ?>
                            <i class="fas fa-hourglass-start"></i>
                            <span>Cycle starts in <?= $days_remaining ?> day(s)</span>
                        <?php elseif ($current_week > 12): ?>
                            <i class="fas fa-flag-checkered"></i>
                            <span>Cycle completed</span>
                        <?php else: ?>
                            <i class="fas fa-calendar-week"></i>
                            <span>Week <?= $current_week ?> of 12 • <?= $days_remaining ?> days remaining</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="cycle-period">
                    <h6>
                        <i class="far fa-calendar"></i>
                        Cycle Period
                    </h6>
                    <div class="dates">
                        <?= date('M j, Y', strtotime($current_cycle['start_date'])) ?><br class="d-md-none">
                        <span class="d-none d-md-inline mx-1">to</span>
                        <?= date('M j, Y', strtotime($current_cycle['end_date'])) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number text-primary"><?= $cycle_score ?>%</div>
                <h6 class="stat-title">Cycle Score</h6>
                <p class="stat-description">Average of weeks</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-number text-success"><?= $week_score ?>%</div>
                <h6 class="stat-title">Week Score</h6>
                <p class="stat-description">
                    <?php if ($current_week > 0 && $current_week <= 12): ?>
                        Week <?= $current_week ?>
                    <?php else: ?>
                        No active week
                    <?php endif; ?>
                </p>
            </div>
            
            <div class="stat-card">
                <div class="stat-number text-info"><?= $total_goals ?></div>
                <h6 class="stat-title">Total Goals</h6>
                <p class="stat-description">Active goals</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-number text-warning"><?= count($this_week_tasks) ?></div>
                <h6 class="stat-title">Week Tasks</h6>
                <p class="stat-description">To complete</p>
            </div>
        </div>

        <div class="content-grid">
            <!-- Goals Overview -->
            <div class="content-card">
                <div class="content-card-header">
                    <h5>
                        <i class="fas fa-bullseye"></i>
                        Your Goals
                    </h5>
                    <a href="12-week-goals.php" class="btn-header-action">
                        <i class="fas fa-arrow-right"></i>
                        <span class="d-none d-sm-inline">Manage</span>
                    </a>
                </div>
                <div class="content-card-body">
                    <?php if (empty($goals_by_category)): ?>
                    <div class="empty-mini">
                        <div class="empty-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <p>No goals created yet</p>
                        <a href="12-week-goals.php" class="btn-primary-action">
                            <i class="fas fa-plus"></i>
                            Add Your First Goal
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="categories-list">
                        <?php foreach ($goals_by_category as $category): ?>
                        <div class="category-item">
                            <div class="category-info">
                                <span class="category-badge" style="background-color: <?= htmlspecialchars($category['color_code']) ?>;"></span>
                                <span class="category-name"><?= htmlspecialchars($category['name']) ?></span>
                            </div>
                            <span class="category-count"><?= $category['goal_count'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- This Week's Tasks -->
            <div class="content-card">
                <div class="content-card-header">
                    <h5>
                        <i class="fas fa-tasks"></i>
                        <?php if ($current_week > 0 && $current_week <= 12): ?>
                            Week <?= $current_week ?> Tasks
                        <?php else: ?>
                            Weekly Tasks
                        <?php endif; ?>
                    </h5>
                    <?php if ($current_week > 0 && $current_week <= 12 && !empty($this_week_tasks)): ?>
                    <a href="12-week-goals.php" class="btn-header-action">
                        <i class="fas fa-arrow-right"></i>
                        <span class="d-none d-sm-inline">View All</span>
                    </a>
                    <?php endif; ?>
                </div>
                <div class="content-card-body">
                    <?php if ($current_week <= 0): ?>
                    <div class="empty-mini">
                        <div class="empty-icon">
                            <i class="fas fa-hourglass-start"></i>
                        </div>
                        <p>Cycle hasn't started yet</p>
                    </div>
                    <?php elseif ($current_week > 12): ?>
                    <div class="empty-mini">
                        <div class="empty-icon">
                            <i class="fas fa-flag-checkered"></i>
                        </div>
                        <p>Cycle has completed</p>
                    </div>
                    <?php elseif (empty($this_week_tasks)): ?>
                    <div class="empty-mini">
                        <div class="empty-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <p>No tasks planned for this week</p>
                        <a href="12-week-goals.php" class="btn-primary-action">
                            <i class="fas fa-plus"></i>
                            Add Tasks
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="task-list">
                        <?php foreach (array_slice($this_week_tasks, 0, 5) as $task): ?>
                        <div class="task-item">
                            <div class="task-title"><?= htmlspecialchars($task['title']) ?></div>
                            <div class="task-meta">
                                <span class="task-badge" style="background-color: <?= htmlspecialchars($task['color_code']) ?>15; color: <?= htmlspecialchars($task['color_code']) ?>;">
                                    <?= htmlspecialchars($task['category_name']) ?>
                                </span>
                                <span class="task-goal"><?= htmlspecialchars($task['goal_title']) ?></span>
                            </div>
                            <div>
                                <?php 
                                $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
                                $completed = 0;
                                foreach ($days as $day) {
                                    if ($task[$day]) $completed++;
                                }
                                ?>
                                <span class="task-progress">
                                    <i class="fas fa-check-circle"></i>
                                    <?= $completed ?>/7 days
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if (count($this_week_tasks) > 5): ?>
                        <div style="text-align: center; margin-top: var(--spacing-md);">
                            <a href="12-week-goals.php" class="btn-view-all">
                                View all <?= count($this_week_tasks) ?> tasks
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions-card mt-3">
            <div class="quick-actions-header">
                <h5>
                    <i class="fas fa-bolt"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="quick-actions-body">
                <div class="actions-grid">
                    <a href="12-week-progress.php" class="action-btn btn-progress">
                        <i class="fas fa-chart-line"></i>
                        <span>View Progress</span>
                    </a>
                    <a href="12-week-leaderboard.php" class="action-btn btn-leaderboard">
                        <i class="fas fa-trophy"></i>
                        <span>Leaderboard</span>
                    </a>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<script>
// Optional: Add smooth scroll or additional interactions if needed
</script>