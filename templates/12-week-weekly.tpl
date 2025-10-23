<?php
$current_cycle = $this->body_template_data['current_cycle'];
$success_message = $this->body_template_data['success_message'];
$error_message = $this->body_template_data['error_message'];
$goals = $this->body_template_data['goals'];
$tasks_by_category = $this->body_template_data['tasks_by_category'];
$selected_week = $this->body_template_data['selected_week'];
$current_week_number = $this->body_template_data['current_week_number'];
$cycle_status = $this->body_template_data['cycle_status'];
$week_stats = $this->body_template_data['week_stats'];
$week_dates = $this->body_template_data['week_dates'];
$allowed_menu_perms = $this->body_template_data['allowed_menu_perms'];
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Weekly View</h1>
                    <p class="text-muted mb-0">Focus on your current week tasks and track daily progress</p>
                </div>
                <?php if ($current_cycle): ?>
                <div class="text-right">
                    <small class="text-muted">Current Cycle:</small><br>
                    <strong><?= htmlspecialchars($current_cycle['name']) ?></strong>
                </div>
                <?php endif; ?>
            </div>

            <!-- Alert Messages -->
            <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (!$current_cycle): ?>
            <!-- No Active Cycle -->
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4>No Active Cycle</h4>
                <p class="text-muted">There is no active 12-week cycle at the moment.<br>Please contact your administrator to create a new cycle.</p>
            </div>
            
            <?php elseif (empty($goals)): ?>
            <!-- No Goals -->
            <div class="text-center py-5">
                <i class="fas fa-bullseye fa-3x text-muted mb-3"></i>
                <h4>No Goals Found</h4>
                <p class="text-muted mb-4">You need to create goals before you can track weekly tasks.</p>
                <a href="12-week-goals.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Your First Goal
                </a>
            </div>
            
            <?php else: ?>

            <!-- Week Navigation and Stats -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <button type="button" class="btn btn-outline-secondary btn-sm me-2 btn-prev-week" 
                                                <?= $selected_week <= 1 ? 'disabled' : '' ?>>
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        
                                        <div class="text-center">
                                            <h4 class="mb-0">
                                                Week <?= $selected_week ?> of 12
                                                <?php if ($selected_week == $current_week_number): ?>
                                                    <span class="badge bg-primary ms-2">Current</span>
                                                <?php endif; ?>
                                            </h4>
                                            <?php if (!empty($week_dates)): ?>
                                            <small class="text-muted"><?= $week_dates['full_range'] ?></small>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <button type="button" class="btn btn-outline-secondary btn-sm ms-2 btn-next-week" 
                                                <?= $selected_week >= 12 ? 'disabled' : '' ?>>
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 text-md-end">
                                    <div class="btn-group btn-group-sm">
                                        <?php if ($selected_week != $current_week_number && $current_week_number > 0): ?>
                                        <button type="button" class="btn btn-outline-primary btn-current-week">
                                            <i class="fas fa-calendar-day me-1"></i>Current Week
                                        </button>
                                        <?php endif; ?>
                                        
                                        <select class="form-select form-select-sm" id="weekSelector" style="width: auto;">
                                            <?php for ($week = 1; $week <= 12; $week++): ?>
                                            <option value="<?= $week ?>" <?= $week == $selected_week ? 'selected' : '' ?>>
                                                Week <?= $week ?>
                                                <?php if ($week == $current_week_number): ?>
                                                    (Current)
                                                <?php endif; ?>
                                            </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="display-6 mb-2 <?= $week_stats['completion_percentage'] >= 70 ? 'text-success' : ($week_stats['completion_percentage'] >= 50 ? 'text-warning' : 'text-danger') ?>">
                                <?= $week_stats['completion_percentage'] ?>%
                            </div>
                            <h6 class="card-title">Week Score</h6>
                            <small class="text-muted">
                                <?= $week_stats['completed_checkboxes'] ?>/<?= $week_stats['total_checkboxes'] ?> completed
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks by Category -->
            <?php if (empty($tasks_by_category)): ?>
            <!-- No Tasks for This Week -->
            <div class="text-center py-5">
                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                <h4>No Tasks Planned</h4>
                <p class="text-muted mb-4">No tasks have been planned for Week <?= $selected_week ?> yet.</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="12-week-plan-tasks.php?week=<?= $selected_week ?>" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-2"></i>Plan Tasks
                    </a>
                    <?php if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickTaskModal">
                        <i class="fas fa-plus me-2"></i>Quick Add Task
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            
            <!-- Quick Actions Bar -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="text-muted">
                        <i class="fas fa-tasks me-1"></i>
                        <?= $week_stats['total_tasks'] ?> task(s) planned for this week
                    </span>
                </div>
                <div class="btn-group btn-group-sm">
                    <a href="12-week-plan-tasks.php?week=<?= $selected_week ?>" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-plus me-1"></i>Plan More Tasks
                    </a>
                    <?php if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#quickTaskModal">
                        <i class="fas fa-plus me-1"></i>Quick Add
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tasks by Category -->
            <?php foreach ($tasks_by_category as $category_data): ?>
            <div class="card mb-4">
                <div class="card-header" style="background-color: <?= htmlspecialchars($category_data['category']['color_code']) ?>15; border-left: 4px solid <?= htmlspecialchars($category_data['category']['color_code']) ?>;">
                    <h5 class="mb-0" style="color: <?= htmlspecialchars($category_data['category']['color_code']) ?>;">
                        <i class="fas fa-tag me-2"></i><?= htmlspecialchars($category_data['category']['name']) ?>
                    </h5>
                </div>
                
                <div class="card-body">
                    <?php foreach ($category_data['goals'] as $goal_data): ?>
                    <div class="goal-section mb-4">
                        <h6 class="goal-title mb-3">
                            <i class="fas fa-bullseye me-2 text-muted"></i>
                            <?= htmlspecialchars($goal_data['goal']['title']) ?>
                        </h6>
                        
                        <div class="tasks-list">
                            <?php foreach ($goal_data['tasks'] as $task): ?>
                            <div class="task-item card mb-3 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h6 class="mb-2"><strong><?= htmlspecialchars($task['task_description']) ?></strong></h6>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="daily-checkboxes">
                                                <small class="text-muted d-block mb-1">Daily Progress:</small>
                                                <div class="d-flex gap-1">
                                                    <?php 
                                                    $days = ['mon' => 'M', 'tue' => 'T', 'wed' => 'W', 'thu' => 'T', 'fri' => 'F', 'sat' => 'S', 'sun' => 'S'];
                                                    $day_names = ['mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday'];
                                                    foreach ($days as $day => $label): 
                                                    ?>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="task_<?= $task['id'] ?>_<?= $day ?>" 
                                                               <?= $task[$day] ? 'checked' : '' ?>
                                                               onchange="updateTaskCompletion(<?= $task['id'] ?>, '<?= $day ?>', this.checked)"
                                                               title="<?= $day_names[$day] ?>">
                                                        <label class="form-check-label small" for="task_<?= $task['id'] ?>_<?= $day ?>">
                                                            <?= $label ?>
                                                        </label>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2 text-center">
                                            <?php 
                                            $completed_days = $task['mon'] + $task['tue'] + $task['wed'] + $task['thu'] + $task['fri'] + $task['sat'] + $task['sun'];
                                            $completion_percentage = round(($completed_days / 7) * 100);
                                            $progress_color = $completion_percentage >= 70 ? 'success' : ($completion_percentage >= 50 ? 'warning' : 'danger');
                                            ?>
                                            <div class="progress mb-1" style="height: 8px;">
                                                <div class="progress-bar bg-<?= $progress_color ?>" style="width: <?= $completion_percentage ?>%"></div>
                                            </div>
                                            <small class="text-muted"><?= $completed_days ?>/7</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Add Task Modal -->
<?php if (!empty($goals) && (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms))): ?>
<div class="modal fade" id="quickTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add_quick_task">
                
                <div class="modal-header">
                    <h5 class="modal-title">Quick Add Task for Week <?= $selected_week ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="goal_id" class="form-label">Select Goal <span class="text-danger">*</span></label>
                        <select class="form-select" name="goal_id" required>
                            <option value="">Choose a goal...</option>
                            <?php foreach ($goals as $goal): ?>
                            <option value="<?= $goal['id'] ?>">
                                <?= htmlspecialchars($goal['title']) ?> (<?= htmlspecialchars($goal['category_name']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="task_description" class="form-label">Task Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="task_description" rows="3" 
                                  required maxlength="500" placeholder="What do you need to do this week?"></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This task will be added to Week <?= $selected_week ?> 
                        <?php if (!empty($week_dates)): ?>
                            (<?= $week_dates['full_range'] ?>)
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Function to update task completion via AJAX (reuse from plan-tasks)
function updateTaskCompletion(taskId, day, isChecked) {
    // Show loading state
    var checkbox = document.getElementById('task_' + taskId + '_' + day);
    var originalValue = checkbox.checked;
    checkbox.disabled = true;
    
    $.post('12-week-update-task.php', {
        task_id: taskId,
        day: day,
        completed: isChecked ? 1 : 0
    })
    .done(function(response) {
        if (response.success) {
            // Update progress bar
            var progressBar = checkbox.closest('.task-item').querySelector('.progress-bar');
            var progressText = checkbox.closest('.task-item').querySelector('.progress + small');
            
            if (progressBar && progressText) {
                var percentage = response.data.completion_percentage;
                var completedDays = response.data.completed_days;
                
                progressBar.style.width = percentage + '%';
                progressBar.className = 'progress-bar bg-' + (percentage >= 70 ? 'success' : (percentage >= 50 ? 'warning' : 'danger'));
                progressText.textContent = completedDays + '/7';
            }
            
            // Update week score if available
            if (response.data.week_score !== undefined) {
                updateWeekScore(response.data.week_score);
            }
            
            // Show success message briefly
            showToast('Task updated successfully!', 'success');
        } else {
            // Revert checkbox state on error
            checkbox.checked = originalValue;
            showToast('Error: ' + response.message, 'error');
        }
    })
    .fail(function() {
        // Revert checkbox state on failure
        checkbox.checked = originalValue;
        showToast('Failed to update task. Please try again.', 'error');
    })
    .always(function() {
        checkbox.disabled = false;
    });
}

// Function to update week score display
function updateWeekScore(newScore) {
    var scoreElement = document.querySelector('.display-6');
    if (scoreElement) {
        scoreElement.textContent = newScore + '%';
        scoreElement.className = 'display-6 mb-2 ' + (newScore >= 70 ? 'text-success' : (newScore >= 50 ? 'text-warning' : 'text-danger'));
    }
}

// Simple toast notification function
function showToast(message, type) {
    var toast = $('<div class="toast-notification toast-' + type + '">' + message + '</div>');
    $('body').append(toast);
    
    setTimeout(function() {
        toast.addClass('show');
    }, 100);
    
    setTimeout(function() {
        toast.removeClass('show');
        setTimeout(function() {
            toast.remove();
        }, 300);
    }, 3000);
}
</script>

<style>
.task-item {
    transition: all 0.3s ease;
}

.task-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.daily-checkboxes .form-check-input {
    margin-top: 0.1rem;
}

.daily-checkboxes .form-check-label {
    font-size: 0.8rem;
    margin-bottom: 0;
}

.progress {
    background-color: #e9ecef;
}

.goal-section {
    border-left: 3px solid #dee2e6;
    padding-left: 15px;
}

.goal-title {
    color: #495057;
    font-weight: 600;
}

/* Toast notifications */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 12px 20px;
    border-radius: 4px;
    color: white;
    font-weight: 500;
    z-index: 9999;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
    max-width: 300px;
}

.toast-notification.show {
    opacity: 1;
    transform: translateX(0);
}

.toast-success {
    background-color: #28a745;
}

.toast-error {
    background-color: #dc3545;
}

.toast-warning {
    background-color: #ffc107;
    color: #212529;
}

/* Loading state for checkboxes */
.form-check-input:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>