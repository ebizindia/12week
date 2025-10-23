<?php
$current_cycle = $this->body_template_data['current_cycle'];
$success_message = $this->body_template_data['success_message'];
$error_message = $this->body_template_data['error_message'];
$categories = $this->body_template_data['categories'];
$goals_by_category = $this->body_template_data['goals_by_category'];
$task_counts = $this->body_template_data['task_counts'];
$allowed_menu_perms = $this->body_template_data['allowed_menu_perms'];
$current_week = $this->body_template_data['current_week'] ?? 1;
$actual_current_week = $this->body_template_data['actual_current_week'] ?? 1;
$is_current_week = $this->body_template_data['is_current_week'] ?? false;
$week_start_date = $this->body_template_data['week_start_date'] ?? 'Week ' . $current_week;
$week_end_date = $this->body_template_data['week_end_date'] ?? '';
?>
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
/* ============================================
   MOBILE-FIRST MODERN DESIGN
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

/* Reset & Base Styles */
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

.goals-page-wrapper {
    min-height: 100vh;
    padding: 4px;
    padding-bottom: 80px;
}

/* ============================================
   HEADER SECTION
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
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.page-header .subtitle {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
}

.page-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-xs);
}

.page-header-left {
    flex: 1;
}

.cycle-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--primary-gradient);
    color: white;
    padding: 6px 12px;
    border-radius: var(--radius-xl);
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
    flex-shrink: 0;
}

/* ============================================
   WEEK NAVIGATION
   ============================================ */
.week-nav {
    background: var(--primary-gradient);
    border-radius: var(--radius-lg);
    padding: var(--spacing-sm) var(--spacing-xs);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-lg);
    color: white;
}

.week-nav-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: var(--spacing-md);
}

.week-nav-btn {
    background: rgba(255,255,255,0.2);
    border: 2px solid rgba(255,255,255,0.3);
    color: white;
    border-radius: var(--radius-md);
    padding: var(--spacing-sm) var(--spacing-md);
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    min-width: 44px;
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-xs);
}

.week-nav-btn:hover, .week-nav-btn:active {
    background: rgba(255,255,255,0.3);
    border-color: rgba(255,255,255,0.5);
    transform: scale(1.05);
}

.week-info {
    text-align: center;
    flex: 1;
}

.week-number {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
    flex-wrap: wrap;
}

.current-badge {
    background: rgba(17, 153, 142, 1);
    padding: 4px 10px;
    border-radius: var(--radius-xl);
    font-size: 0.7rem;
    font-weight: 600;
}

.week-dates {
    font-size: 0.875rem;
    opacity: 0.9;
    margin-top: var(--spacing-xs);
}

.go-current-btn {
    background: rgba(17, 153, 142, 0.9);
    border: none;
    color: white;
    padding: 6px 12px;
    border-radius: var(--radius-md);
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.go-current-btn:hover {
    background: rgba(17, 153, 142, 1);
    transform: scale(1.05);
}

/* ============================================
   ALERTS
   ============================================ */
.alert-modern {
    border-radius: var(--radius-md);
    border: none;
    padding: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
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
   CATEGORY CARDS
   ============================================ */
.category-card {
    background: white;
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    transition: all 0.3s ease;
}

.category-header {
    padding: var(--spacing-sm) var(--spacing-xs);
    border-left: 4px solid;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: var(--spacing-xs);
}

.category-info h3 {
    font-size: 1.125rem;
    font-weight: 700;
    margin: 0 0 var(--spacing-xs) 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.category-stats {
    display: flex;
    gap: var(--spacing-md);
    font-size: 0.813rem;
    color: var(--text-secondary);
    flex-wrap: wrap;
}

.category-stat {
    display: flex;
    align-items: center;
    gap: 4px;
}

.add-goal-btn {
    background: var(--primary-gradient);
    color: white;
    border: none;
    border-radius: var(--radius-xl);
    padding: var(--spacing-sm) var(--spacing-md);
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    min-height: 44px;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.add-goal-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

/* ============================================
   GOAL ITEMS (Mobile-First Card Layout)
   ============================================ */
.goal-item {
    border-top: 1px solid #e9ecef;
    padding: var(--spacing-sm) var(--spacing-xs);
}

.goal-item:first-child {
    border-top: none;
}

.goal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-sm);
}

.goal-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    flex: 1;
    display: flex;
    align-items: flex-start;
    gap: var(--spacing-xs);
}

.goal-actions {
    display: flex;
    gap: var(--spacing-xs);
    flex-shrink: 0;
}

.btn-icon {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: var(--radius-md);
    padding: var(--spacing-xs);
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 44px;
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
}

.btn-icon:hover, .btn-icon:active {
    transform: scale(1.05);
    box-shadow: var(--shadow-sm);
}

.btn-icon.btn-add-task {
    border-color: rgba(17, 153, 142, 1);
    color: rgba(17, 153, 142, 1);
}

.btn-icon.btn-edit {
    border-color: #667eea;
    color: #667eea;
}

.btn-icon.btn-delete {
    border-color: #dc3545;
    color: #dc3545;
}

/* Task Cards */
.task-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.task-card {
    background: #f8f9fa;
    border-radius: var(--radius-md);
    padding: var(--spacing-xs) 6px;
    transition: all 0.3s ease;
}

.task-card:hover {
    background: #f1f3f5;
}

.task-title-row {
    display: flex;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-sm);
    align-items: center;
}

.task-title-input {
    flex: 1;
    border: none;
    background: transparent;
    font-weight: 600;
    font-size: 0.985rem;
    color: var(--text-primary);
    padding: 0;
    outline: none;
}

.task-title-input:focus {
    background: white;
    padding: var(--spacing-xs);
    border-radius: var(--radius-sm);
}

.task-delete-btn {
    background: white;
    border: 1px solid #dc3545;
    color: #dc3545;
    border-radius: var(--radius-md);
    padding: 6px 12px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    min-height: 36px;
}

.task-delete-btn:hover {
    background: #dc3545;
    color: white;
}

/* Day Checkboxes - Mobile Optimized */
.days-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
    margin-bottom: var(--spacing-sm);
}

.day-checkbox {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}

.day-label {
    font-size: 0.688rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
}

.day-checkbox input[type="checkbox"] {
    width: 24px;
    height: 24px;
    cursor: pointer;
    border-radius: var(--radius-sm);
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    border: 2px solid #dee2e6;
    background: white;
    position: relative;
    transition: all 0.3s ease;
}

.day-checkbox input[type="checkbox"]:checked {
    border-color: #667eea;
    background: white;
}

.day-checkbox input[type="checkbox"]:checked::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #667eea;
    font-size: 14px;
    font-weight: bold;
}

.day-checkbox input[type="checkbox"]:hover {
    border-color: #667eea;
}

/* Task Stats */
.task-stats {
    display: flex;
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.task-stat {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    font-size: 0.875rem;
}

.stat-label {
    color: var(--text-secondary);
    font-weight: 500;
}

.stat-value {
    font-weight: 700;
}

.badge-modern {
    padding: 4px 10px;
    border-radius: var(--radius-xl);
    font-size: 0.813rem;
    font-weight: 600;
    color: white;
    transition: background 0.3s ease;
}

.badge-total {
    background: var(--info-gradient);
}

.badge-success {
    background: var(--success-gradient);
}

.badge-warning {
    background: var(--warning-gradient);
}

.badge-danger {
    background: var(--danger-gradient);
}

.target-input {
    width: 60px;
    border: 2px solid #e9ecef;
    border-radius: var(--radius-sm);
    padding: 4px 8px;
    text-align: center;
    font-weight: 600;
    font-size: 0.875rem;
}

.target-input:focus {
    border-color: #667eea;
    outline: none;
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
}

.empty-state p {
    color: var(--text-secondary);
    margin-bottom: var(--spacing-lg);
}

.empty-mini {
    padding: var(--spacing-lg);
    text-align: center;
}

.empty-mini .empty-icon {
    font-size: 2rem;
}

.empty-mini p {
    margin: 0;
    color: var(--text-secondary);
}

/* ============================================
   MODALS - Mobile Optimized
   ============================================ */
.modal-content {
    border-radius: var(--radius-lg);
    border: none;
    box-shadow: var(--shadow-xl);
}

.modal-header-modern {
    background: var(--primary-gradient);
    color: white;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    padding: var(--spacing-lg);
    border: none;
}

.modal-header-modern .modal-title {
    color: white;
    font-weight: 700;
    font-size: 1.125rem;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.modal-header-modern .close {
    color: white;
    opacity: 0.9;
    font-size: 1.5rem;
    padding: 0;
    margin: 0;
    min-width: 44px;
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-body {
    padding: var(--spacing-lg);
}

.form-group {
    margin-bottom: var(--spacing-md);
}

.form-group label {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--spacing-xs);
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.form-control {
    border-radius: var(--radius-md);
    border: 2px solid #e9ecef;
    padding: var(--spacing-sm);
    font-size: 1rem;
    min-height: 44px;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
    outline: none;
}

.modal-footer {
    padding: var(--spacing-lg);
    border: none;
}

.btn-primary-modern {
    background: var(--primary-gradient);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    padding: var(--spacing-md) var(--spacing-lg);
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    width: 100%;
    min-height: 48px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
}

.btn-primary-modern:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.alert-tip {
    background: #e3f2fd;
    border-left: 4px solid #2196F3;
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    margin-top: var(--spacing-md);
}

.alert-tip .tip-icon {
    color: #2196F3;
    font-size: 1.125rem;
    margin-right: var(--spacing-xs);
}

/* ============================================
   TABLET+ RESPONSIVE ENHANCEMENTS
   ============================================ */
@media (min-width: 768px) {
    .goals-page-wrapper {
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
    
    .week-number {
        font-size: 2rem;
    }
    
    .week-nav {
        padding: var(--spacing-lg);
    }
    
    .week-nav-btn {
        padding: var(--spacing-md) var(--spacing-lg);
        font-size: 1rem;
    }
    
    .category-header {
        padding: var(--spacing-lg);
    }
    
    .goal-item {
        padding: var(--spacing-md);
    }
    
    .days-grid {
        grid-template-columns: repeat(7, 1fr);
        gap: var(--spacing-xs);
        max-width: 500px;
    }
    
    .day-checkbox input[type="checkbox"] {
        width: 28px;
        height: 28px;
    }
    
    .day-label {
        font-size: 0.75rem;
    }
    
    .task-card {
        padding: var(--spacing-md);
    }
    
    .modal-dialog {
        max-width: 600px;
    }
}

@media (min-width: 992px) {
    .goals-page-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: var(--spacing-lg);
    }
    
    /* Two column layout for categories on desktop */
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
        gap: var(--spacing-md);
    }
    
    .category-card {
        margin-bottom: 0;
    }
    
    .goal-item {
        padding: var(--spacing-lg);
    }
    
    .task-card {
        padding: var(--spacing-lg);
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

.category-card {
    animation: slideIn 0.3s ease-out;
}

/* Touch feedback */
@media (hover: none) and (pointer: coarse) {
    .btn-icon:active,
    .add-goal-btn:active,
    .week-nav-btn:active,
    .btn-primary-modern:active {
        opacity: 0.8;
    }
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

/* Focus states */
button:focus-visible,
input:focus-visible,
select:focus-visible {
    outline: 3px solid #667eea;
    outline-offset: 2px;
}
</style>

<div class="goals-page-wrapper">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-header-left">
                    <h1>
                        <i class="fas fa-bullseye"></i>
                        <span>My Goals</span>
                    </h1>
                    <p class="subtitle">Track your progress week by week</p>
                </div>
                <?php if ($current_cycle): ?>
                <span class="cycle-badge">
                    <i class="fas fa-sync-alt"></i>
                    <span><?= htmlspecialchars($current_cycle['name']) ?></span>
                </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Week Navigation -->
        <div class="week-nav">
            <div class="week-nav-header">
                <button type="button" class="week-nav-btn" id="prevWeek" onclick="changeWeek(-1)">
                    <i class="fas fa-chevron-left"></i>
                    <span class="d-none d-sm-inline">Prev</span>
                </button>
                
                <div class="week-info">
                    <h2 class="week-number">
                        <i class="fas fa-calendar-week"></i>
                        Week <span id="currentWeekNum"><?= $current_week ?></span>
                        <?php if ($is_current_week): ?>
                        <span class="current-badge">Current</span>
                        <?php else: ?>
                        <button type="button" class="go-current-btn" onclick="goToCurrentWeek()">
                            <i class="fas fa-calendar-day"></i> Week <?= $actual_current_week ?>
                        </button>
                        <?php endif; ?>
                    </h2>
                    <div class="week-dates"><?= $week_start_date ?><?= !empty($week_end_date) ? ' - ' . $week_end_date : '' ?></div>
                </div>
                
                <button type="button" class="week-nav-btn" id="nextWeek" onclick="changeWeek(1)">
                    <span class="d-none d-sm-inline">Next</span>
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if ($success_message): ?>
        <div class="alert alert-success alert-modern alert-dismissible fade show" role="alert">
            <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
            <div class="alert-content"><?= htmlspecialchars($success_message) ?></div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
        <div class="alert alert-danger alert-modern alert-dismissible fade show" role="alert">
            <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="alert-content"><?= htmlspecialchars($error_message) ?></div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php endif; ?>

        <?php if (!$current_cycle): ?>
        <!-- No Active Cycle -->
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-calendar-times"></i>
            </div>
            <h4>No Active Cycle</h4>
            <p>There is no active 12-week cycle at the moment.<br>Please contact your administrator to create a new cycle.</p>
        </div>
        <?php else: ?>

        <?php if (empty($goals_by_category)): ?>
        <!-- No Goals Yet -->
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-bullseye"></i>
            </div>
            <h4>No Goals Yet</h4>
            <p>Start by adding your first goal for this 12-week cycle.</p>
            <?php if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
            <button type="button" class="add-goal-btn" data-category-id="" data-toggle="modal" data-target="#addGoalModal">
                <i class="fas fa-plus"></i>
                <span>Add Your First Goal</span>
            </button>
            <?php endif; ?>
        </div>
        <?php else: ?>

        <!-- Categories and Goals -->
        <div class="categories-grid">
            <?php foreach ($categories as $category): ?>
                <?php 
                $category_goals = $goals_by_category[$category['id']] ?? null;
                $has_goals = $category_goals && !empty($category_goals['goals']);
                ?>
                
                <div class="category-card">
                    <div class="category-header" style="border-left-color: <?= htmlspecialchars($category['color_code']) ?>;">
                        <div class="category-info">
                            <h3 style="color: <?= htmlspecialchars($category['color_code']) ?>;">
                                <i class="fas fa-tag"></i>
                                <?= htmlspecialchars($category['name']) ?>
                            </h3>
                            <?php if ($has_goals): ?>
                            <div class="category-stats">
                                <span class="category-stat">
                                    <i class="fas fa-bullseye"></i>
                                    <?= count($category_goals['goals']) ?> goals
                                </span>
                                <span class="category-stat">
                                    <i class="fas fa-tasks"></i>
                                    <?= $category_goals['total_tasks'] ?> tasks
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
                        <button type="button" class="add-goal-btn" data-category-id="<?= $category['id'] ?>" data-toggle="modal" data-target="#addGoalModal">
                            <i class="fas fa-plus"></i>
                            <span class="d-none d-sm-inline">Add Goal</span>
                        </button>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!$has_goals): ?>
                    <div class="empty-mini">
                        <div class="empty-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <p>No goals in this category yet.</p>
                    </div>
                    <?php else: ?>
                    
                    <!-- Goals List -->
                    <?php foreach ($category_goals['goals'] as $goal): ?>
                    <div class="goal-item">
                        <div class="goal-header">
                            <h4 class="goal-title">
                                <i class="fas fa-bullseye"></i>
                                <?= htmlspecialchars($goal['title']) ?>
                            </h4>
                            <div class="goal-actions">
                                <button type="button" class="btn-icon btn-add-task" 
                                        data-goal-id="<?= $goal['id'] ?>"
                                        data-goal-title="<?= htmlspecialchars($goal['title']) ?>"
                                        data-toggle="modal"
                                        data-target="#addTaskModal"
                                        title="Add Task">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <?php if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
                                <button type="button" class="btn-icon btn-edit btn-edit-goal" 
                                        data-goal-id="<?= $goal['id'] ?>"
                                        data-title="<?= htmlspecialchars($goal['title']) ?>"
                                        data-category-id="<?= $goal['category_id'] ?>"
                                        data-toggle="modal"
                                        data-target="#editGoalModal"
                                        title="Edit Goal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php endif; ?>
                                <?php if (in_array('DELETE', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
                                <button type="button" class="btn-icon btn-delete btn-delete-goal" 
                                        data-goal-id="<?= $goal['id'] ?>"
                                        data-title="<?= htmlspecialchars($goal['title']) ?>"
                                        title="Delete Goal">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if (empty($goal['tasks'])): ?>
                        <p class="text-muted" style="font-size: 0.875rem; margin: 0;">No tasks yet. Add a task to track this goal.</p>
                        <?php else: ?>
                        
                        <!-- Tasks List -->
                        <div class="task-list">
                            <?php 
                            // Sort tasks by week number
                            usort($goal['tasks'], function($a, $b) {
                                return $a['week_number'] - $b['week_number'];
                            });
                            ?>
                            <?php foreach ($goal['tasks'] as $task): ?>
                            <?php
                            $completed_days = ($task['mon'] ?? 0) + ($task['tue'] ?? 0) + ($task['wed'] ?? 0) + 
                                             ($task['thu'] ?? 0) + ($task['fri'] ?? 0) + ($task['sat'] ?? 0) + ($task['sun'] ?? 0);
                            $target = $task['weekly_target'] ?? 1;
                            $score_percent = $target > 0 ? round(($completed_days / $target) * 100) : 0;
                            $score_class = $completed_days >= $target ? 'success' : ($completed_days > 0 ? 'warning' : 'danger');
                            ?>
                            <div class="task-card">
                                <div class="task-title-row">
                                    <input type="text" 
                                           class="task-title-input" 
                                           value="<?= htmlspecialchars($task['title']) ?>" 
                                           data-task-id="<?= $task['id'] ?>"
                                           data-original-title="<?= htmlspecialchars($task['title']) ?>"
                                           placeholder="Task name">
                                    <button type="button" class="task-delete-btn btn-delete-task" 
                                            data-task-id="<?= $task['id'] ?>" 
                                            data-goal-id="<?= $goal['id'] ?>" 
                                            data-title="<?= htmlspecialchars($task['title']) ?>"
                                            title="Delete Task">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Days Checkboxes -->
                                <div class="days-grid">
                                    <?php 
                                    $days = [
                                        'mon' => 'M', 
                                        'tue' => 'T', 
                                        'wed' => 'W', 
                                        'thu' => 'T', 
                                        'fri' => 'F', 
                                        'sat' => 'S', 
                                        'sun' => 'S'
                                    ];
                                    foreach ($days as $day => $label): 
                                        $is_checked = ($task[$day] ?? 0) == 1;
                                    ?>
                                    <label class="day-checkbox">
                                        <span class="day-label"><?= $label ?></span>
                                        <input type="checkbox" 
                                               class="task-day-checkbox" 
                                               id="task_<?= $task['id'] ?>_<?= $day ?>" 
                                               data-task-id="<?= $task['id'] ?>" 
                                               data-day="<?= $day ?>" 
                                               data-target="<?= $target ?>"
                                               <?= $is_checked ? 'checked' : '' ?>>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- Task Stats -->
                                <div class="task-stats">
                                    <div class="task-stat">
                                        <span class="stat-label">Total:</span>
                                        <span class="badge-modern badge-total" id="total-<?= $task['id'] ?>"><?= $completed_days ?></span>
                                    </div>
                                    <div class="task-stat">
                                        <span class="stat-label">Target:</span>
                                        <input type="number" 
                                               class="target-input task-target-input" 
                                               value="<?= $target ?>" 
                                               min="1" 
                                               max="7" 
                                               data-task-id="<?= $task['id'] ?>">
                                    </div>
                                    <div class="task-stat">
                                        <span class="stat-label">Score:</span>
                                        <span class="badge-modern badge-<?= $score_class ?>" id="score-<?= $task['id'] ?>"><?= $score_percent ?>%</span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Add Goal Modal -->
<?php if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
<div class="modal fade" id="addGoalModal" tabindex="-1" role="dialog" aria-labelledby="addGoalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add_goal">
                
                <div class="modal-header modal-header-modern">
                    <h5 class="modal-title" id="addGoalModalLabel">
                        <i class="fas fa-plus-circle"></i>
                        Add New Goal
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">
                            <i class="fas fa-bullseye"></i>
                            Goal Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="title" name="title" required maxlength="200" placeholder="Enter your goal title">
                    </div>
                    
                    <div class="form-group">
                        <label for="add_category_select">
                            <i class="fas fa-tag"></i>
                            Category <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="add_category_select" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="alert-tip">
                        <i class="fas fa-lightbulb tip-icon"></i>
                        <strong>Tip:</strong> Set specific, measurable goals to track your progress effectively.
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn-primary-modern">
                        <i class="fas fa-check"></i>
                        Add Goal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Edit Goal Modal -->
<?php if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
<div class="modal fade" id="editGoalModal" tabindex="-1" role="dialog" aria-labelledby="editGoalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit_goal">
                <input type="hidden" name="goal_id" id="goal_id">
                
                <div class="modal-header modal-header-modern">
                    <h5 class="modal-title" id="editGoalModalLabel">
                        <i class="fas fa-edit"></i>
                        Edit Goal
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_title">
                            <i class="fas fa-bullseye"></i>
                            Goal Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="edit_title" name="title" required maxlength="200">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_category_id">
                            <i class="fas fa-tag"></i>
                            Category <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="edit_category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn-primary-modern">
                        <i class="fas fa-save"></i>
                        Update Goal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addTaskForm">
                <div class="modal-header modal-header-modern">
                    <h5 class="modal-title" id="addTaskModalLabel">
                        <i class="fas fa-plus-circle"></i>
                        Add New Task
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <input type="hidden" id="task_goal_id" name="goal_id">
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-bullseye"></i>
                            Goal: <span id="task_goal_title" style="color: #667eea; font-weight: 700;"></span>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label for="task_title">
                            <i class="fas fa-tasks"></i>
                            Task Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="task_title" name="title" required maxlength="200" placeholder="Enter task description">
                    </div>
                    
                    <div class="form-group">
                        <label for="task_week">
                            <i class="fas fa-calendar-week"></i>
                            Week Number <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="task_week" name="week_number" required>
                            <option value="">Select Week</option>
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>" <?= $i == $current_week ? 'selected' : '' ?>>Week <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="task_target">
                            <i class="fas fa-bullseye"></i>
                            Weekly Target <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="task_target" name="weekly_target" required>
                            <option value="1">1 day per week</option>
                            <option value="2">2 days per week</option>
                            <option value="3" selected>3 days per week</option>
                            <option value="4">4 days per week</option>
                            <option value="5">5 days per week</option>
                            <option value="6">6 days per week</option>
                            <option value="7">7 days per week (daily)</option>
                        </select>
                        <small class="form-text text-muted">How many days per week should this task be completed?</small>
                    </div>
                    
                    <div class="alert-tip">
                        <i class="fas fa-lightbulb tip-icon"></i>
                        <strong>Tip:</strong> Break down your goals into actionable weekly tasks for better tracking.
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn-primary-modern">
                        <i class="fas fa-check"></i>
                        Add Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Goal Form (Hidden) -->
<?php if (in_array('DELETE', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
<form id="deleteGoalForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete_goal">
    <input type="hidden" name="goal_id" id="delete_goal_id">
</form>
<?php endif; ?>

<script>
/*$(document).ready(function() {
    // Add goal button click handler
    $('.add-goal-btn').on('click', function() {
        var categoryId = $(this).data('category-id');
        if (categoryId) {
            $('#add_category_select').val(categoryId);
        }
    });
    
    // Edit goal button click handler
    $('.btn-edit').on('click', function() {
        var goalId = $(this).data('goal-id');
        var title = $(this).data('title');
        var categoryId = $(this).data('category-id');
        
        $('#goal_id').val(goalId);
        $('#edit_title').val(title);
        $('#edit_category_id').val(categoryId);
    });
    
    // Add task button click handler
    $('.btn-add-task').on('click', function() {
        var goalId = $(this).data('goal-id');
        var goalTitle = $(this).data('goal-title');
        
        $('#task_goal_id').val(goalId);
        $('#task_goal_title').text(goalTitle);
        $('#task_week').val(<?= $current_week ?>);
    });
    
    // Delete goal button click handler
    $('.btn-delete').on('click', function() {
        var goalId = $(this).data('goal-id');
        var title = $(this).data('title');
        
        if (confirm('Are you sure you want to delete the goal "' + title + '"?\n\nThis will also delete all tasks associated with this goal.')) {
            $('#delete_goal_id').val(goalId);
            $('#deleteGoalForm').submit();
        }
    });
    
    // Task delete button handler
    $('.task-delete-btn').on('click', function() {
        var taskId = $(this).data('task-id');
        var title = $(this).data('title');
        
        if (confirm('Are you sure you want to delete the task "' + title + '"?')) {
            // Submit delete task form
            $('<form method="POST">' +
              '<input type="hidden" name="action" value="delete_task">' +
              '<input type="hidden" name="task_id" value="' + taskId + '">' +
              '</form>').appendTo('body').submit();
        }
    });
    
    // Task title input change handler
    $('.task-title-input').on('blur', function() {
        var $input = $(this);
        var taskId = $input.data('task-id');
        var newTitle = $input.val().trim();
        var originalTitle = $input.data('original-title');
        
        if (newTitle && newTitle !== originalTitle) {
            // Submit update
            $.post('', {
                action: 'update_task_title',
                task_id: taskId,
                title: newTitle
            }, function(response) {
                $input.data('original-title', newTitle);
            }).fail(function() {
                alert('Failed to update task title');
                $input.val(originalTitle);
            });
        } else if (!newTitle) {
            $input.val(originalTitle);
        }
    });
    
    // Task checkbox change handler
    $('.task-day-checkbox').on('change', function() {
        var $checkbox = $(this);
        var taskId = $checkbox.data('task-id');
        var day = $checkbox.data('day');
        var isChecked = $checkbox.prop('checked');
        
        // Get target from the input field (not from data attribute)
        var target = parseInt($('.task-target-input[data-task-id="' + taskId + '"]').val());
        
        console.log('Checkbox changed - Task:', taskId, 'Day:', day, 'Checked:', isChecked, 'Target:', target);
        
        // Immediate visual feedback - update stats right away
        updateTaskStats(taskId, target);
        
        // Submit update to server
        $.post('', {
            action: 'update_task_day',
            task_id: taskId,
            day: day,
            value: isChecked ? 1 : 0
        }, function(response) {
            // Update confirmed - stats already updated above
            console.log('Server update successful');
        }).fail(function() {
            alert('Failed to update task');
            // Revert checkbox on failure
            $checkbox.prop('checked', !isChecked);
            // Revert stats
            updateTaskStats(taskId, target);
        });
    });
    
    // Task target input change handler
    $('.task-target-input').on('change', function() {
        var $input = $(this);
        var taskId = $input.data('task-id');
        var newTarget = parseInt($input.val());
        
        console.log('Target changed - Task:', taskId, 'New Target:', newTarget);
        
        if (newTarget >= 1 && newTarget <= 7) {
            // Immediately update stats with new target
            updateTaskStats(taskId, newTarget);
            
            // Submit update to server
            $.post('', {
                action: 'update_task_target',
                task_id: taskId,
                target: newTarget
            }, function(response) {
                console.log('Target update successful');
            }).fail(function() {
                alert('Failed to update target');
            });
        }
    });
    
    // Function to update task statistics
    function updateTaskStats(taskId, target) {
        console.log('=== updateTaskStats START ===');
        console.log('Task ID:', taskId, '| Target:', target);
        
        var completedDays = 0;
        var $checkboxes = $('.task-day-checkbox[data-task-id="' + taskId + '"]');
        
        console.log('Found checkboxes:', $checkboxes.length);
        
        $checkboxes.each(function(index) {
            var isChecked = $(this).prop('checked');
            console.log('  Checkbox', index + 1, ':', isChecked ? 'CHECKED' : 'unchecked');
            if (isChecked) {
                completedDays++;
            }
        });
        
        console.log('Total completed days:', completedDays);
        
        var scorePercent = target > 0 ? Math.round((completedDays / target) * 100) : 0;
        
        // Determine color class based on completion
        var scoreClass;
        if (completedDays >= target) {
            scoreClass = 'success';  // Green - target met or exceeded
        } else if (completedDays > 0) {
            scoreClass = 'warning';  // Orange - partial completion
        } else {
            scoreClass = 'danger';   // Red - no completion
        }
        
        console.log('Score:', scorePercent + '% | Color:', scoreClass);
        
        // Update total badge
        var $totalBadge = $('#total-' + taskId);
        console.log('Total badge found:', $totalBadge.length > 0);
        $totalBadge.text(completedDays);
        
        // Update score badge with color change
        var $scoreBadge = $('#score-' + taskId);
        console.log('Score badge found:', $scoreBadge.length > 0);
        console.log('Current badge classes:', $scoreBadge.attr('class'));
        
        // Remove all color classes, keep badge-modern
        $scoreBadge.removeClass('badge-success badge-warning badge-danger');
        // Add new color class
        $scoreBadge.addClass('badge-' + scoreClass);
        // Ensure badge-modern class is present
        if (!$scoreBadge.hasClass('badge-modern')) {
            console.log('Adding missing badge-modern class');
            $scoreBadge.addClass('badge-modern');
        }
        $scoreBadge.text(scorePercent + '%');
        
        console.log('New badge classes:', $scoreBadge.attr('class'));
        console.log('=== updateTaskStats END ===\n');
    }
    
    // Add task form submission
    $('#addTaskForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'add_task',
            goal_id: $('#task_goal_id').val(),
            title: $('#task_title').val(),
            week_number: $('#task_week').val(),
            weekly_target: $('#task_target').val()
        };
        
        $.post('', formData, function(response) {
            location.reload();
        }).fail(function() {
            alert('Failed to add task');
        });
    });
});*/

// Week navigation functions
function changeWeek(direction) {
    var currentWeek = parseInt($('#currentWeekNum').text());
    var newWeek = currentWeek + direction;
    
    if (newWeek >= 1 && newWeek <= 12) {
        window.location.href = '12-week-goals.php?week=' + newWeek;
    }
}

function goToCurrentWeek() {
    window.location.href = '12-week-goals.php?week=<?= $actual_current_week ?>';
}

// Swipe gesture support for mobile week navigation
var touchStartX = 0;
var touchEndX = 0;

document.addEventListener('touchstart', function(e) {
    touchStartX = e.changedTouches[0].screenX;
}, false);

document.addEventListener('touchend', function(e) {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
}, false);

function handleSwipe() {
    var swipeThreshold = 100;
    var diff = touchEndX - touchStartX;
    
    if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
            // Swipe right - previous week
            changeWeek(-1);
        } else {
            // Swipe left - next week
            changeWeek(1);
        }
    }
}
</script>
