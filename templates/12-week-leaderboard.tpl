<?php
$current_cycle = $this->body_template_data['current_cycle'];
$leaderboard = $this->body_template_data['leaderboard'];
$user_position = $this->body_template_data['user_position'];
$total_participants = $this->body_template_data['total_participants'];
$top_performers = $this->body_template_data['top_performers'];
$achievement_leaders = $this->body_template_data['achievement_leaders'];
$streak_leaders = $this->body_template_data['streak_leaders'];
$error_message = $this->body_template_data['error_message'];
?>
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
/* ============================================
   MOBILE-FIRST LEADERBOARD DESIGN
   ============================================ */

:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --danger-gradient: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
    --warning-gradient: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
    --info-gradient: linear-gradient(135deg, #2196F3 0%, #00BCD4 100%);
    --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    --gold: #FFD700;
    --silver: #C0C0C0;
    --bronze: #CD7F32;
    
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

.leaderboard-wrapper {
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

.page-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: var(--spacing-sm);
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

.btn-back {
    background: white;
    border: 2px solid #667eea;
    color: #667eea;
    border-radius: var(--radius-md);
    padding: var(--spacing-sm) var(--spacing-md);
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    min-height: 44px;
    white-space: nowrap;
    transition: all 0.3s ease;
}

.btn-back:hover,
.btn-back:active {
    background: #52a2e8;
    border-color: #52a2e8;
    color: white;
    text-decoration: none;
}

/* ============================================
   CYCLE INFO CARD
   ============================================ */
.cycle-info-card {
    background: #52a2e8;
    border-radius: var(--radius-lg);
    padding: var(--spacing-md) var(--spacing-xs);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-lg);
    color: white;
}

.cycle-info-header {
    margin-bottom: var(--spacing-md);
}

.cycle-info-header h4 {
    font-size: 1.125rem;
    font-weight: 700;
    margin: 0 0 var(--spacing-xs) 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.cycle-info-header p {
    font-size: 0.875rem;
    opacity: 0.95;
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.user-rank-badge {
    background: rgba(255,255,255,0.2);
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    text-align: center;
    backdrop-filter: blur(10px);
}

.user-rank-badge .rank-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.user-rank-badge .rank-label {
    font-size: 0.875rem;
    opacity: 0.9;
    margin: var(--spacing-xs) 0 0 0;
}

/* ============================================
   TOP PERFORMERS
   ============================================ */
.top-performers-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--spacing-xs);
    margin-bottom: var(--spacing-md);
}

.performer-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-md);
    box-shadow: var(--shadow-md);
    text-align: center;
    transition: all 0.3s ease;
}

.performer-card:active {
    transform: scale(0.98);
}

.performer-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto var(--spacing-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #f8f9fa;
}

.performer-icon img {
    width: 35px;
    height: auto;
}

.performer-category {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: var(--spacing-xs);
}

.performer-category.text-gold {
    color: var(--gold);
}

.performer-category.text-success {
    background: var(--success-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.performer-category.text-danger {
    background: var(--danger-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.performer-value {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: var(--spacing-xs);
}

.performer-value.text-gold {
    color: var(--gold);
}

.performer-value.text-success {
    background: var(--success-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.performer-value.text-danger {
    background: var(--danger-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.performer-name {
    color: var(--text-primary);
    font-weight: 600;
    font-size: 0.938rem;
}

/* ============================================
   LEADERBOARD SECTION
   ============================================ */
.leaderboard-card {
    background: white;
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.leaderboard-header {
    background: #52a2e8;
    color: white;
    padding: var(--spacing-md) var(--spacing-xs);
}

.leaderboard-header h5 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.leaderboard-list {
    padding: var(--spacing-xs);
}

.leaderboard-item {
    background: white;
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    margin-bottom: var(--spacing-xs);
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.leaderboard-item:active {
    transform: scale(0.98);
}

.leaderboard-item.user-item {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
}

.leaderboard-item-header {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-sm);
}

.rank-badge {
    flex-shrink: 0;
    width: 50px;
    text-align: center;
}

.medal-icon {
    width: 40px;
    height: auto;
}

.rank-number {
    background: linear-gradient(135deg, #bdc3c7 0%, #95a5a6 100%);
    color: white;
    border-radius: var(--radius-md);
    padding: 8px 12px;
    font-weight: 700;
    font-size: 1.125rem;
    display: inline-block;
}

.member-details {
    flex: 1;
}

.member-name {
    font-weight: 700;
    color: var(--text-primary);
    font-size: 1rem;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.member-name.text-primary {
    color: #667eea;
}

.badge-you {
    background: var(--primary-gradient);
    color: white;
    padding: 4px 10px;
    border-radius: var(--radius-xl);
    font-size: 0.75rem;
    font-weight: 600;
}

.member-badges {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-xs);
}

.stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: var(--radius-xl);
    font-weight: 600;
    font-size: 0.75rem;
    color: white;
}

.stat-badge img {
    width: 14px;
    height: auto;
}

.stat-badge.badge-primary {
    background: var(--primary-gradient);
}

.stat-badge.badge-success {
    background: var(--success-gradient);
}

.stat-badge.badge-danger {
    background: var(--danger-gradient);
}

.stat-badge.badge-info {
    background: var(--info-gradient);
}

.completion-bar {
    margin-top: var(--spacing-sm);
}

.completion-bar-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin-bottom: 4px;
    display: flex;
    justify-content: space-between;
}

.progress-bar-container {
    height: 8px;
    border-radius: var(--radius-xl);
    background: #e9ecef;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: var(--success-gradient);
    border-radius: var(--radius-xl);
    transition: width 0.3s ease;
}

/* ============================================
   SIDE CARDS
   ============================================ */
.side-card {
    background: white;
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.side-card-header {
    background: #52a2e8;
    color: white;
    padding: var(--spacing-sm) var(--spacing-xs);
}

.side-card-header h6 {
    font-size: 0.938rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.side-card-header img {
    width: 18px;
    height: auto;
}

.side-card-body {
    padding: var(--spacing-sm) var(--spacing-xs);
}

.leader-item {
    padding: var(--spacing-sm) 0;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: var(--spacing-sm);
}

.leader-item:last-child {
    border-bottom: none;
}

.leader-info {
    flex: 1;
}

.leader-name {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.938rem;
    margin-bottom: 2px;
}

.leader-rank {
    font-size: 0.813rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 4px;
}

.leader-rank img {
    width: 14px;
    height: auto;
}

.motivation-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto var(--spacing-md);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.motivation-icon img {
    width: 40px;
    height: auto;
}

.motivation-icon i {
    font-size: 2rem;
}

.motivation-title {
    font-weight: 700;
    color: var(--text-primary);
    font-size: 1rem;
    margin-bottom: var(--spacing-xs);
}

.motivation-text {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin: 0;
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

.empty-icon img {
    width: 80px;
    height: auto;
}

.empty-state h4 {
    color: var(--text-primary);
    font-weight: 700;
    margin-bottom: var(--spacing-sm);
    font-size: 1.25rem;
}

.empty-state p {
    color: var(--text-secondary);
    margin: 0;
    font-size: 0.938rem;
}

.empty-mini {
    padding: var(--spacing-xl) var(--spacing-md);
    text-align: center;
}

.empty-mini .empty-icon {
    font-size: 2.5rem;
}

.empty-mini .empty-icon img {
    width: 60px;
}

.empty-mini h5 {
    color: var(--text-primary);
    font-weight: 700;
    margin-bottom: var(--spacing-xs);
}

.empty-mini p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.875rem;
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
    .leaderboard-wrapper {
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
    
    .cycle-info-card {
        padding: var(--spacing-lg);
    }
    
    .cycle-info-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .cycle-info-header h4 {
        font-size: 1.5rem;
    }
    
    .top-performers-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: var(--spacing-md);
    }
    
    .performer-icon {
        width: 70px;
        height: 70px;
    }
    
    .performer-icon img {
        width: 40px;
    }
    
    .performer-value {
        font-size: 2rem;
    }
    
    .leaderboard-header {
        padding: var(--spacing-lg);
    }
    
    .leaderboard-header h5 {
        font-size: 1.125rem;
    }
    
    .leaderboard-list {
        padding: var(--spacing-md);
    }
    
    .leaderboard-item {
        padding: var(--spacing-lg);
    }
    
    .rank-badge {
        width: 70px;
    }
    
    .medal-icon {
        width: 50px;
    }
    
    .side-card-header {
        padding: var(--spacing-md);
    }
    
    .side-card-body {
        padding: var(--spacing-md);
    }
    
    .leader-item {
        padding: var(--spacing-md) 0;
    }
}

@media (min-width: 992px) {
    .leaderboard-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: var(--spacing-lg);
    }
    
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: var(--spacing-lg);
    }
    
    .main-content {
        min-width: 0;
    }
    
    .sidebar {
        position: sticky;
        top: var(--spacing-lg);
        align-self: flex-start;
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

.performer-card,
.leaderboard-item,
.side-card {
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

<div class="leaderboard-wrapper">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <div>
                    <h1>
                        <i class="fas fa-trophy"></i>
                        <span>Leaderboard</span>
                    </h1>
                    <p class="subtitle">Performance rankings and motivation</p>
                </div>
                <a href="12-week-dashboard.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <span class="d-none d-sm-inline">Dashboard</span>
                </a>
            </div>
        </div>

        <?php if ($error_message): ?>
        <!-- No Data -->
        <div class="alert alert-warning alert-modern" role="alert">
            <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="alert-content"><?= htmlspecialchars($error_message) ?></div>
        </div>
        
        <div class="empty-state">
            <div class="empty-icon">
                <img class="img-fluid" src="custom-images/trophy-gold.png" alt="trophy"/>
            </div>
            <h4>No Leaderboard Available</h4>
            <p>No active cycle found for leaderboard rankings.</p>
        </div>
        
        <?php else: ?>

        <!-- Cycle Info -->
        <div class="cycle-info-card">
            <div class="cycle-info-content">
                <div class="cycle-info-header">
                    <h4>
                        <img class="img-fluid" src="custom-images/trophy-gold.png" alt="trophy" style="width:20px; height:auto;">
                        <?= htmlspecialchars($current_cycle['name']) ?> Leaderboard
                    </h4>
                    <p>
                        <img class="img-fluid" src="custom-images/users-white.png" alt="members" style="width:18px; height:auto;">
                        <?= $total_participants ?> participants competing
                    </p>
                </div>
                
                <?php if ($user_position): ?>
                <div class="user-rank-badge">
                    <h3 class="rank-number">#<?= $user_position['rank_position'] ?></h3>
                    <p class="rank-label">Your Current Rank</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Performers -->
        <?php if (!empty($top_performers)): ?>
        <div class="top-performers-grid">
            <?php foreach ($top_performers as $performer): ?>
            <div class="performer-card">
                <div class="performer-icon">
                    <img src="custom-images/<?= htmlspecialchars($performer['icon']) ?>" alt="<?= htmlspecialchars($performer['category']) ?>"/>
                </div>
                <h5 class="performer-category text-<?= htmlspecialchars($performer['color']) ?>">
                    <?= htmlspecialchars($performer['category']) ?>
                </h5>
                <div class="performer-value text-<?= htmlspecialchars($performer['color']) ?>">
                    <?= $performer['category'] === 'Completion Leader' ? round($performer['value'], 1) . '%' : $performer['value'] ?>
                </div>
                <p class="performer-name"><?= htmlspecialchars($performer['name']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="content-grid">
            <div class="main-content">
                <!-- Main Leaderboard -->
                <div class="leaderboard-card">
                    <div class="leaderboard-header">
                        <h5>
                            <img class="img-fluid" src="custom-images/trophy-gold.png" alt="trophy" style="height:20px; width:auto;">
                            Overall Rankings
                        </h5>
                    </div>
                    
                    <?php if (empty($leaderboard)): ?>
                    <div class="empty-mini">
                        <div class="empty-icon">
                            <img class="img-fluid" src="custom-images/trophy-white.png" alt="trophy"/>
                        </div>
                        <h5>No Rankings Available Yet</h5>
                        <p>Start completing tasks to appear on the leaderboard!</p>
                    </div>
                    <?php else: ?>
                    
                    <div class="leaderboard-list">
                        <?php foreach ($leaderboard as $member): ?>
                        <div class="leaderboard-item <?= $member['user_id'] == $user_id ? 'user-item' : '' ?>">
                            <div class="leaderboard-item-header">
                                <div class="rank-badge">
                                    <?php if ($member['rank_position'] == 1): ?>
                                    <img class="medal-icon" src="custom-images/medal-gold.png" alt="gold"/>
                                    <?php elseif ($member['rank_position'] == 2): ?>
                                    <img class="medal-icon" src="custom-images/medal-silver.png" alt="silver"/>
                                    <?php elseif ($member['rank_position'] == 3): ?>
                                    <img class="medal-icon" src="custom-images/medal-bronze.png" alt="bronze"/>
                                    <?php else: ?>
                                    <div class="rank-number">#<?= $member['rank_position'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="member-details">
                                    <h6 class="member-name <?= $member['user_id'] == $user_id ? 'text-primary' : '' ?>">
                                        <?= htmlspecialchars($member['name']) ?>
                                        <?php if ($member['user_id'] == $user_id): ?>
                                        <span class="badge-you">You</span>
                                        <?php endif; ?>
                                    </h6>
                                    <div class="member-badges">
                                        <span class="stat-badge badge-primary">
                                            <img src="custom-images/star-white.png" alt="points">
                                            <?= $member['total_points'] ?> pts
                                        </span>
                                        <span class="stat-badge badge-success">
                                            <img src="custom-images/check-circle.png" alt="completion">
                                            <?= round($member['completion_rate'], 1) ?>%
                                        </span>
                                        <?php if ($member['current_streak'] > 0): ?>
                                        <span class="stat-badge badge-danger">
                                            <img src="custom-images/fire-white.png" alt="streak">
                                            <?= $member['current_streak'] ?> <?= $member['current_streak']==1?'day':'days' ?>
                                        </span>
                                        <?php endif; ?>
                                        <span class="stat-badge badge-info">
                                            <img src="custom-images/trophy-gold.png" alt="achievements">
                                            <?= $member['achievements_count'] ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="completion-bar">
                                <div class="completion-bar-label">
                                    <span>Completion</span>
                                    <span><?= round($member['completion_rate'], 1) ?>%</span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar" style="width: <?= min($member['completion_rate'], 100) ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="sidebar">
                <!-- Achievement Leaders -->
                <?php if (!empty($achievement_leaders)): ?>
                <div class="side-card">
                    <div class="side-card-header">
                        <h6>
                            <img src="custom-images/trophy-gold.png" alt="trophy">
                            Achievement Leaders
                        </h6>
                    </div>
                    <div class="side-card-body">
                        <?php foreach ($achievement_leaders as $leader): ?>
                        <div class="leader-item">
                            <div class="leader-info">
                                <div class="leader-name"><?= htmlspecialchars($leader['name']) ?></div>
                                <div class="leader-rank">
                                    <img src="custom-images/award-grey.png" alt="rank">
                                    Rank #<?= $leader['rank_position'] ?>
                                </div>
                            </div>
                            <span class="stat-badge badge-warning">
                                <img src="custom-images/trophy-white.png" alt="achievements">
                                <?= $leader['achievements_count'] ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Streak Leaders -->
                <?php if (!empty($streak_leaders)): ?>
                <div class="side-card">
                    <div class="side-card-header">
                        <h6>
                            <img src="custom-images/fire-red.png" alt="fire">
                            Streak Leaders
                        </h6>
                    </div>
                    <div class="side-card-body">
                        <?php foreach ($streak_leaders as $leader): ?>
                        <div class="leader-item">
                            <div class="leader-info">
                                <div class="leader-name"><?= htmlspecialchars($leader['name']) ?></div>
                                <div class="leader-rank">
                                    <img src="custom-images/award-grey.png" alt="rank">
                                    Rank #<?= $leader['rank_position'] ?>
                                </div>
                            </div>
                            <span class="stat-badge badge-danger">
                                <img src="custom-images/fire-white.png" alt="streak">
                                <?= $leader['current_streak'] ?> <?= $leader['current_streak']==1?'day':'days' ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Motivation Card -->
                <div class="side-card">
                    <div class="side-card-header">
                        <h6>
                            <img src="custom-images/lightbulb.png" alt="motivation" style="height:20px; width:auto;">
                            Stay Motivated!
                        </h6>
                    </div>
                    <div class="side-card-body">
                        <?php if ($user_position): ?>
                            <?php if ($user_position['rank_position'] <= 3): ?>
                            <div style="text-align: center;">
                                <div class="motivation-icon" style="background: rgba(255, 215, 0, 0.1);">
                                    <img src="custom-images/crown-yellow.png" alt="crown"/>
                                </div>
                                <p class="motivation-title">You're in the top 3!</p>
                                <p class="motivation-text">Amazing work! Keep up the momentum and maintain your elite position!</p>
                            </div>
                            
                            <?php elseif ($user_position['rank_position'] <= 10): ?>
                            <div style="text-align: center;">
                                <div class="motivation-icon" style="background: rgba(255, 215, 0, 0.1);">
                                    <img src="custom-images/star-white.png" alt="star"/>
                                </div>
                                <p class="motivation-title">You're in the top 10!</p>
                                <p class="motivation-text">Great work! Push a little harder to break into the top 3!</p>
                            </div>
                            
                            <?php else: ?>
                            <div style="text-align: center;">
                                <div class="motivation-icon" style="background: rgba(102, 126, 234, 0.1);">
                                    <i class="fas fa-rocket" style="color: #667eea; font-size: 2rem;"></i>
                                </div>
                                <p class="motivation-title">Keep pushing forward!</p>
                                <p class="motivation-text">Complete more tasks and maintain your streak to climb the rankings!</p>
                            </div>
                            <?php endif; ?>
                        <?php else: ?>
                        <div style="text-align: center;">
                            <div class="motivation-icon" style="background: rgba(17, 153, 142, 0.1);">
                                <i class="fas fa-play-circle" style="color: #11998e; font-size: 2rem;"></i>
                            </div>
                            <p class="motivation-title">Start your journey!</p>
                            <p class="motivation-text">Complete your first task to join the leaderboard and compete with your team!</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>
</div>

<script>
// Optional: Add smooth scroll or additional interactions if needed
</script>