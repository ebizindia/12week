<?php
$current_cycle = $this->body_template_data['current_cycle'];
$current_week = $this->body_template_data['current_week'];
$days_remaining = $this->body_template_data['days_remaining'];
$total_members = $this->body_template_data['total_members'];
$total_goals = $this->body_template_data['total_goals'];
$avg_cycle_score = $this->body_template_data['avg_cycle_score'];
$avg_week_score = $this->body_template_data['avg_week_score'];
$member_list = $this->body_template_data['member_list'];
$all_cycles = $this->body_template_data['all_cycles'];
$error_message = $this->body_template_data['error_message'];
$selected_cycle_id = $this->body_template_data['selected_cycle_id'];
?>
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
/* ============================================
   MOBILE-FIRST ADMIN DASHBOARD DESIGN
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

.admin-dashboard-wrapper {
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

.header-actions {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
    margin-top: var(--spacing-md);
}

.btn-header {
    background: var(--primary-gradient);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    padding: var(--spacing-sm) var(--spacing-md);
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-xs);
    min-height: 44px;
    transition: all 0.3s ease;
}

.btn-header.btn-secondary {
    background: linear-gradient(135deg, #bdc3c7 0%, #95a5a6 100%);
}

.btn-header:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: white;
    text-decoration: none;
}

/* ============================================
   CYCLE SELECTOR
   ============================================ */
.cycle-selector {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-md) var(--spacing-xs);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-md);
}

.cycle-selector label {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--spacing-xs);
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    font-size: 0.875rem;
}

.form-control {
    border-radius: var(--radius-md);
    border: 2px solid #e9ecef;
    padding: var(--spacing-sm);
    font-size: 0.938rem;
    min-height: 44px;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
    outline: none;
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

.cycle-overview .cycle-status {
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
   MEMBER TABLE
   ============================================ */
.member-table-card {
    background: white;
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.member-table-header {
    background: var(--primary-gradient);
    color: white;
    padding: var(--spacing-md) var(--spacing-xs);
}

.member-table-header h5 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

/* Mobile: Card Layout */
.member-list-mobile {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
    padding: var(--spacing-xs);
}

.member-card {
    background: #f8f9fa;
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    transition: all 0.3s ease;
}

.member-card:active {
    transform: scale(0.98);
    background: #f1f3f5;
}

.member-header {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-md);
}

.avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--primary-gradient);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.125rem;
    flex-shrink: 0;
}

.member-info {
    flex: 1;
}

.member-name {
    font-weight: 700;
    color: var(--text-primary);
    font-size: 1rem;
    margin: 0;
}

.member-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-md);
}

.stat-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 12px;
    border-radius: var(--radius-xl);
    font-weight: 600;
    font-size: 0.875rem;
    color: white;
}

.stat-badge.badge-primary {
    background: var(--primary-gradient);
}

.stat-badge.badge-success {
    background: var(--success-gradient);
}

.stat-badge.badge-warning {
    background: var(--warning-gradient);
}

.stat-badge.badge-danger {
    background: var(--danger-gradient);
}

.btn-view {
    background: transparent;
    border: 2px solid #667eea;
    color: #667eea;
    border-radius: var(--radius-md);
    padding: var(--spacing-sm) var(--spacing-md);
    font-weight: 600;
    font-size: 0.875rem;
    width: 100%;
    min-height: 44px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-xs);
}

.btn-view:hover,
.btn-view:active {
    background: var(--primary-gradient);
    border-color: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Desktop: Table Layout (hidden on mobile) */
.member-table-desktop {
    display: none;
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
    margin: 0;
    color: var(--text-secondary);
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
   TABLET+ RESPONSIVE ENHANCEMENTS
   ============================================ */
@media (min-width: 768px) {
    .admin-dashboard-wrapper {
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
    
    .page-header-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: var(--spacing-lg);
    }
    
    .header-actions {
        flex-direction: row;
        margin-top: 0;
    }
    
    .cycle-selector {
        padding: var(--spacing-lg);
    }
    
    .cycle-selector-content {
        display: flex;
        align-items: center;
        gap: var(--spacing-lg);
    }
    
    .cycle-selector label {
        margin-bottom: 0;
        white-space: nowrap;
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
    
    /* Switch to table layout on desktop */
    .member-list-mobile {
        display: none;
    }
    
    .member-table-desktop {
        display: block;
    }
    
    .table-enhanced {
        margin-bottom: 0;
        width: 100%;
    }
    
    .table-enhanced thead th {
        background: #f8f9fa;
        color: var(--text-primary);
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        padding: var(--spacing-md);
        font-size: 0.875rem;
    }
    
    .table-enhanced tbody tr {
        transition: all 0.3s ease;
    }
    
    .table-enhanced tbody tr:hover {
        background: #f8f9fa;
    }
    
    .table-enhanced tbody td {
        padding: var(--spacing-md);
        vertical-align: middle;
    }
    
    .table-enhanced .member-header {
        margin-bottom: 0;
    }
    
    .table-enhanced .stat-badge {
        display: inline-block;
    }
    
    .table-enhanced .btn-view {
        width: auto;
        min-width: 100px;
    }
}

@media (min-width: 992px) {
    .admin-dashboard-wrapper {
        max-width: 1400px;
        margin: 0 auto;
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

.stat-card,
.member-card {
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
input:focus-visible,
select:focus-visible,
a:focus-visible {
    outline: 3px solid #667eea;
    outline-offset: 2px;
}
</style>

<div class="admin-dashboard-wrapper">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <div>
                    <h1>
                        <i class="fas fa-users-cog"></i>
                        <span>Admin Dashboard</span>
                    </h1>
                    <p class="subtitle">Overview of all member progress in 12-week cycles</p>
                </div>
                <div class="header-actions">
                    <a href="12-week-manage-cycles.php" class="btn-header">
                        <i class="fas fa-sync-alt"></i>
                        <span>Manage Cycles</span>
                    </a>
                    <a href="12-week-manage-categories.php" class="btn-header btn-secondary">
                        <i class="fas fa-tags"></i>
                        <span>Categories</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Cycle Selector -->
        <?php if (!empty($all_cycles)): ?>
        <div class="cycle-selector">
            <div class="cycle-selector-content">
                <label for="cycleSelector">
                    <i class="fas fa-filter"></i>
                    <span>Select Cycle:</span>
                </label>
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
        <?php endif; ?>

        <?php if ($error_message): ?>
        <!-- No Cycle -->
        <div class="alert alert-warning alert-modern" role="alert">
            <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="alert-content"><?= htmlspecialchars($error_message) ?></div>
        </div>
        
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-calendar-times"></i>
            </div>
            <h4>No Cycle Found</h4>
            <p>Create a new cycle to start tracking member progress.</p>
            <a href="12-week-manage-cycles.php" class="btn-header">
                <i class="fas fa-plus"></i>
                <span>Create New Cycle</span>
            </a>
        </div>
        
        <?php else: ?>
        
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
                <div class="stat-number text-primary"><?= $avg_cycle_score ?>%</div>
                <h6 class="stat-title">Cycle Score</h6>
                <p class="stat-description">Group average</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-number text-success"><?= $avg_week_score ?>%</div>
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
                <div class="stat-number text-info"><?= $total_members ?></div>
                <h6 class="stat-title">Members</h6>
                <p class="stat-description">With goals</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-number text-warning"><?= $total_goals ?></div>
                <h6 class="stat-title">Goals</h6>
                <p class="stat-description">Total active</p>
            </div>
        </div>

        <!-- Member Progress -->
        <div class="member-table-card">
            <div class="member-table-header">
                <h5>
                    <i class="fas fa-chart-line"></i>
                    Member Progress
                </h5>
            </div>
            
            <?php if (empty($member_list)): ?>
            <div class="empty-mini">
                <div class="empty-icon">
                    <i class="fas fa-users"></i>
                </div>
                <p>No members have created goals in this cycle yet.</p>
            </div>
            <?php else: ?>
            
            <!-- Mobile: Card Layout -->
            <div class="member-list-mobile">
                <?php foreach ($member_list as $member): ?>
                    <?php 
                    $cycle_score = round($member['cycle_score'], 1);
                    $cycle_color = $cycle_score >= 70 ? 'success' : ($cycle_score >= 50 ? 'warning' : 'danger');
                    $week_score = round($member['current_week_score'], 1);
                    $week_color = $week_score >= 70 ? 'success' : ($week_score >= 50 ? 'warning' : 'danger');
                    ?>
                <div class="member-card">
                    <div class="member-header">
                        <div class="avatar">
                            <?= strtoupper(substr($member['name'], 0, 2)) ?>
                        </div>
                        <div class="member-info">
                            <h6 class="member-name"><?= htmlspecialchars($member['name']) ?></h6>
                        </div>
                    </div>
                    
                    <div class="member-stats">
                        <div class="stat-item">
                            <span class="stat-label">Cycle Score</span>
                            <span class="stat-badge badge-<?= $cycle_color ?>"><?= $cycle_score ?>%</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Week <?= $current_week ?></span>
                            <span class="stat-badge badge-<?= $week_color ?>"><?= $week_score ?>%</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Goals</span>
                            <span class="stat-badge badge-primary"><?= $member['goals_count'] ?></span>
                        </div>
                    </div>
                    
                    <button type="button" class="btn-view" onclick="viewMemberDetails(<?= $member['id'] ?>)">
                        <i class="fas fa-eye"></i>
                        <span>View Details</span>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Desktop: Table Layout -->
            <div class="member-table-desktop">
                <table class="table table-enhanced">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th class="text-center">Cycle Score</th>
                            <th class="text-center">Week <?= $current_week ?> Score</th>
                            <th class="text-center">Goals</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($member_list as $member): ?>
                            <?php 
                            $cycle_score = round($member['cycle_score'], 1);
                            $cycle_color = $cycle_score >= 70 ? 'success' : ($cycle_score >= 50 ? 'warning' : 'danger');
                            $week_score = round($member['current_week_score'], 1);
                            $week_color = $week_score >= 70 ? 'success' : ($week_score >= 50 ? 'warning' : 'danger');
                            ?>
                        <tr>
                            <td>
                                <div class="member-header">
                                    <div class="avatar">
                                        <?= strtoupper(substr($member['name'], 0, 2)) ?>
                                    </div>
                                    <div class="member-info">
                                        <h6 class="member-name"><?= htmlspecialchars($member['name']) ?></h6>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="stat-badge badge-<?= $cycle_color ?>"><?= $cycle_score ?>%</span>
                            </td>
                            <td class="text-center">
                                <span class="stat-badge badge-<?= $week_color ?>"><?= $week_score ?>%</span>
                            </td>
                            <td class="text-center">
                                <span class="stat-badge badge-primary"><?= $member['goals_count'] ?></span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn-view" onclick="viewMemberDetails(<?= $member['id'] ?>)">
                                    <i class="fas fa-eye"></i>
                                    <span>View</span>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<script>
function changeCycle(cycleId) {
    if (cycleId) {
        window.location.href = '12-week-admin-dashboard.php?cycle_id=' + cycleId;
    } else {
        window.location.href = '12-week-admin-dashboard.php';
    }
}

function viewMemberDetails(userId) {
    alert('Member details view will be implemented in the next phase. User ID: ' + userId);
}
</script>