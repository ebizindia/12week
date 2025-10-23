<?php
$success_message = $this->body_template_data['success_message'];
$error_message = $this->body_template_data['error_message'];
$cycles = $this->body_template_data['cycles'];
$allowed_menu_perms = $this->body_template_data['allowed_menu_perms'];
$is_admin = $this->body_template_data['is_admin'];
?>
<style>
/* Custom Styles for Manage Cycles */
.cycles-page-wrapper {
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

.cycle-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    overflow: hidden;
}

.cycle-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.cycle-card.active-cycle {
    border: 2px solid #007bff;
}

.cycle-card .card-header {
    border: none;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 15px 20px;
}

.cycle-card .card-header.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.cycle-card .card-body {
    padding: 25px;
}

.cycle-card .card-title {
    color: #2c3e50;
    font-weight: 600;
    font-size: 1.3rem;
    margin-bottom: 20px;
}

.stats-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.stats-box .stat-item {
    text-align: center;
}

.stats-box .stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.stats-box .stat-label {
    font-size: 0.85rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.progress-enhanced {
    height: 8px;
    border-radius: 10px;
    background: #e9ecef;
    overflow: hidden;
}

.progress-enhanced .progress-bar {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
}

.badge-enhanced {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.85rem;
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

.modal-content-enhanced {
    border-radius: 12px;
    border: none;
}

.modal-header-enhanced {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px 12px 0 0;
}

.modal-header-enhanced .close {
    color: white;
    opacity: 0.8;
}

.modal-header-enhanced .close:hover {
    opacity: 1;
}

.card-footer-actions {
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    padding: 15px;
}

.btn-group-enhanced .btn {
    border-radius: 6px !important;
    margin: 0 2px;
}
</style>

<div class="cycles-page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Page Header -->
                <div class="page-header-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-1">Manage Cycles</h1>
                            <p class="text-muted mb-0">Create and manage your 12-week performance cycles</p>
                        </div>
                        <?php if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
                        <button type="button" class="btn btn-primary btn-enhanced" data-toggle="modal" data-target="#createCycleModal" id="btnCreateCycle">
                            <i class="fas fa-plus mr-2"></i>Create New Cycle
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show alert-enhanced" role="alert">
                    <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success_message) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show alert-enhanced" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i><?= htmlspecialchars($error_message) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>

                <!-- Cycles List -->
                <?php if (empty($cycles)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h4 class="mb-3">No Cycles Created Yet</h4>
                    <p class="text-muted mb-4">Get started by creating your first 12-week cycle to track goals and progress.</p>
                    <?php if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
                    <button type="button" class="btn btn-primary btn-enhanced" data-toggle="modal" data-target="#createCycleModal">
                        <i class="fas fa-plus mr-2"></i>Create First Cycle
                    </button>
                    <?php endif; ?>
                </div>
                <?php else: ?>

                <div class="row">
                    <?php foreach ($cycles as $cycle): ?>
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card cycle-card h-100 <?= $cycle['status'] === 'active' ? 'active-cycle' : '' ?>">
                            <?php if ($cycle['status'] === 'active'): ?>
                            <div class="card-header bg-primary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-star mr-2"></i>Active Cycle</h6>
                                    <span class="badge badge-light text-primary"><?= htmlspecialchars($cycle['status_text']) ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($cycle['name']) ?></h5>
                                
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1"><i class="far fa-calendar mr-1"></i>Duration</small>
                                    <div>
                                        <strong><?= date('M j, Y', strtotime($cycle['start_date'])) ?></strong>
                                        <span class="text-muted mx-2">→</span>
                                        <strong><?= date('M j, Y', strtotime($cycle['end_date'])) ?></strong>
                                    </div>
                                </div>
                                
                                <div class="stats-box">
                                    <div class="row">
                                        <div class="col-4 stat-item border-right">
                                            <div class="stat-value text-primary"><?= $cycle['member_count'] ?></div>
                                            <div class="stat-label">Members</div>
                                        </div>
                                        <div class="col-4 stat-item border-right">
                                            <div class="stat-value text-success"><?= $cycle['goals_count'] ?></div>
                                            <div class="stat-label">Goals</div>
                                        </div>
                                        <div class="col-4 stat-item">
                                            <div class="stat-value text-info"><?= $cycle['days_remaining'] ?></div>
                                            <div class="stat-label">Days Left</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted"><i class="fas fa-chart-line mr-1"></i>Progress</small>
                                        <small class="font-weight-bold">
                                            <?php if ($cycle['current_week'] > 0): ?>
                                                Week <?= $cycle['current_week'] ?> of 12
                                            <?php else: ?>
                                                Not Started
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <div class="progress progress-enhanced">
                                        <?php 
                                        $progress = $cycle['current_week'] > 0 ? ($cycle['current_week'] / 12) * 100 : 0;
                                        ?>
                                        <div class="progress-bar" style="width: <?= $progress ?>%"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <?php if ($cycle['status'] === 'active'): ?>
                                        <span class="badge badge-success badge-enhanced">
                                            <i class="fas fa-check-circle mr-1"></i>Active
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary badge-enhanced">
                                            <i class="fas fa-check mr-1"></i>Completed
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="border-top pt-3">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-user mr-1"></i><?= htmlspecialchars($cycle['created_by_name'] ?: 'Unknown') ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="far fa-clock mr-1"></i><?= date('M j, Y', strtotime($cycle['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                            
                            <div class="card-footer-actions">
                                <div class="btn-group-enhanced d-flex">
                                    <a href="12-week-admin-dashboard.php?cycle_id=<?= $cycle['id'] ?>" class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="fas fa-chart-line mr-1"></i>View
                                    </a>
                                    
                                    <?php if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
                                    <button type="button" class="btn btn-outline-secondary btn-sm flex-fill btn-edit-cycle" 
                                            data-cycle-id="<?= $cycle['id'] ?>"
                                            data-name="<?= htmlspecialchars($cycle['name']) ?>"
                                            data-start-date="<?= $cycle['start_date'] ?>">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                    
                                    <?php if ($cycle['status'] === 'active'): ?>
                                    <button type="button" class="btn btn-outline-warning btn-sm flex-fill btn-close-cycle" 
                                            data-cycle-id="<?= $cycle['id'] ?>"
                                            data-name="<?= htmlspecialchars($cycle['name']) ?>">
                                        <i class="fas fa-stop mr-1"></i>Close
                                    </button>
                                    <?php else: ?>
                                    <button type="button" class="btn btn-outline-success btn-sm flex-fill btn-reactivate-cycle" 
                                            data-cycle-id="<?= $cycle['id'] ?>"
                                            data-name="<?= htmlspecialchars($cycle['name']) ?>">
                                        <i class="fas fa-play mr-1"></i>Reactivate
                                    </button>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </div>
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

<!-- Create Cycle Modal -->
<?php if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
<div class="modal fade" id="createCycleModal" tabindex="-1" role="dialog" aria-labelledby="createCycleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-content-enhanced">
            <form method="POST" id="createCycleForm">
                <input type="hidden" name="action" value="create_cycle">
                
                <div class="modal-header modal-header-enhanced">
                    <h5 class="modal-title" id="createCycleModalLabel">
                        <i class="fas fa-plus-circle mr-2"></i>Create New Cycle
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-tag mr-1"></i>Cycle Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required maxlength="100" placeholder="e.g., Q1 2025 Cycle">
                    </div>
                    
                    <div class="form-group">
                        <label for="start_date"><i class="far fa-calendar-alt mr-1"></i>Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Start date must be a Monday. End date will be automatically calculated (12 weeks).
                        </small>
                    </div>
                    
                    <div class="alert alert-info alert-enhanced">
                        <i class="fas fa-lightbulb mr-2"></i>
                        <strong>Tip:</strong> A 12-week cycle helps teams focus on short-term goals with measurable outcomes.
                    </div>
                </div>
                
                <div class="modal-footer">
                    
                    <button type="submit" class="btn btn-primary btn-enhanced">
                        <i class="fas fa-check mr-2"></i>Create Cycle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Edit Cycle Modal -->
<?php if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
<div class="modal fade" id="editCycleModal" tabindex="-1" role="dialog" aria-labelledby="editCycleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-content-enhanced">
            <form method="POST" id="editCycleForm">
                <input type="hidden" name="action" value="edit_cycle">
                <input type="hidden" name="cycle_id" id="edit_cycle_id">
                
                <div class="modal-header modal-header-enhanced">
                    <h5 class="modal-title" id="editCycleModalLabel">
                        <i class="fas fa-edit mr-2"></i>Edit Cycle
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name"><i class="fas fa-tag mr-1"></i>Cycle Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required maxlength="100">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_start_date"><i class="far fa-calendar-alt mr-1"></i>Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Start date must be a Monday. The end date will be automatically recalculated.
                        </small>
                    </div>
                    
                    <div class="alert alert-warning alert-enhanced">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Warning:</strong> Changing the start date will affect all goals and tasks in this cycle. 
                        Make sure to inform members about the changes.
                    </div>
                </div>
                
                <div class="modal-footer">
                    
                    <button type="submit" class="btn btn-primary btn-enhanced">
                        <i class="fas fa-save mr-2"></i>Update Cycle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Hidden Forms for Actions -->
<?php if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
<form id="closeCycleForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="close_cycle">
    <input type="hidden" name="cycle_id" id="close_cycle_id">
</form>

<form id="reactivateCycleForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="reactivate_cycle">
    <input type="hidden" name="cycle_id" id="reactivate_cycle_id">
</form>
<?php endif; ?>

<script>
/*$(document).ready(function() {
    console.log('Cycle management page loaded');
    
    // Set minimum date to today for new cycles
    var today = new Date().toISOString().split('T')[0];
    $('#start_date').attr('min', today);
    
    // Helper function to get next Monday
    function getNextMonday() {
        var today = new Date();
        var daysUntilMonday = (1 + 7 - today.getDay()) % 7;
        if (daysUntilMonday === 0 && today.getDay() !== 1) {
            daysUntilMonday = 7;
        }
        var nextMonday = new Date(today.getTime() + daysUntilMonday * 24 * 60 * 60 * 1000);
        return nextMonday.toISOString().split('T')[0];
    }
    
    // Set default start date to next Monday
    $('#start_date').val(getNextMonday());
    
    // Test modal functionality
    $('#btnCreateCycle').click(function() {
        console.log('Create cycle button clicked');
        $('#createCycleModal').modal('show');
    });
    
    // Edit cycle modal
    $('.btn-edit-cycle').click(function() {
        console.log('Edit cycle button clicked');
        var cycleId = $(this).data('cycle-id');
        var name = $(this).data('name');
        var startDate = $(this).data('start-date');
        
        $('#editCycleModal #edit_cycle_id').val(cycleId);
        $('#editCycleModal #edit_name').val(name);
        $('#editCycleModal #edit_start_date').val(startDate);
        $('#editCycleModal').modal('show');
    });
    
    // Close cycle confirmation
    $('.btn-close-cycle').click(function() {
        var cycleId = $(this).data('cycle-id');
        var name = $(this).data('name');
        
        if (confirm('Are you sure you want to close the cycle: ' + name + '?\n\nThis will mark it as completed and members will no longer be able to add goals or tasks.')) {
            $('#closeCycleForm #close_cycle_id').val(cycleId);
            $('#closeCycleForm').submit();
        }
    });
    
    // Reactivate cycle confirmation
    $('.btn-reactivate-cycle').click(function() {
        var cycleId = $(this).data('cycle-id');
        var name = $(this).data('name');
        
        if (confirm('Are you sure you want to reactivate the cycle: ' + name + '?\n\nThis will make it the active cycle again.')) {
            $('#reactivateCycleForm #reactivate_cycle_id').val(cycleId);
            $('#reactivateCycleForm').submit();
        }
    });
    
    // Validate start date is Monday
    $('input[type=date]').change(function() {
        var selectedDate = new Date($(this).val());
        var dayOfWeek = selectedDate.getDay(); // 0 = Sunday, 1 = Monday, etc.
        
        if (dayOfWeek !== 1) { // Not Monday
            alert('Start date must be a Monday. Please select a Monday.');
            $(this).focus();
        }
    });
    
    // Form submission handling
    $('#createCycleForm').submit(function(e) {
        console.log('Create cycle form submitted');
        // Let the form submit normally
    });
    
    $('#editCycleForm').submit(function(e) {
        console.log('Edit cycle form submitted');
        // Let the form submit normally
    });
});*/
</script>
