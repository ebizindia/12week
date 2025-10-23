<?php
$current_cycle = $this->body_template_data['current_cycle'];
$success_message = $this->body_template_data['success_message'];
$error_message = $this->body_template_data['error_message'];
$goals = $this->body_template_data['goals'];
$selected_goal = $this->body_template_data['selected_goal'];
$selected_goal_id = $this->body_template_data['selected_goal_id'];
$selected_week = $this->body_template_data['selected_week'];
$current_week_number = $this->body_template_data['current_week_number'];
$tasks = $this->body_template_data['tasks'];
$week_dates = $this->body_template_data['week_dates'];
$allowed_menu_perms = $this->body_template_data['allowed_menu_perms'];
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Plan Tasks</h1>
                    <p class="text-muted mb-0">Plan tasks for all 12 weeks of your goals</p>
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
                <p class="text-muted mb-4">You need to create goals before you can plan tasks.</p>
                <a href="12-week-goals.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Your First Goal
                </a>
            </div>
            
            <?php else: ?>

            <!-- Goal and Week Selection -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <label for="goalSelector" class="form-label">Select Goal:</label>
                            <select class="form-select" id="goalSelector">
                                <?php foreach ($goals as $goal): ?>
                                <option value="<?= $goal['id'] ?>" <?= $goal['id'] == $selected_goal_id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($goal['title']) ?> (<?= htmlspecialchars($goal['category_name']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <label for="weekSelector" class="form-label">Select Week:</label>
                            <select class="form-select" id="weekSelector">
                                <?php for ($week = 1; $week <= 12; $week++): ?>
                                <option value="<?= $week ?>" <?= $week == $selected_week ? 'selected' : '' ?>>
                                    Week <?= $week ?> of 12
                                    <?php if ($week == $current_week_number): ?>
                                        (Current Week)
                                    <?php endif; ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($selected_goal): ?>
            <!-- Selected Goal Info -->
            <div class="card mb-4" style="border-left: 4px solid <?= htmlspecialchars($selected_goal['color_code']) ?>;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-1" style="color: <?= htmlspecialchars($selected_goal['color_code']) ?>;">
                                <i class="fas fa-bullseye me-2"></i><?= htmlspecialchars($selected_goal['title']) ?>
                            </h5>
                            <p class="text-muted mb-0">
                                <span class="badge me-2" style="background-color: <?= htmlspecialchars($selected_goal['color_code']) ?>15; color: <?= htmlspecialchars($selected_goal['color_code']) ?>;">
                                    <?= htmlspecialchars($selected_goal['category_name']) ?>
                                </span>
                                <?php if (!empty($selected_goal['description'])): ?>
                                    <?= htmlspecialchars($selected_goal['description']) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="12-week-goals.php" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Back to Goals
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Week Navigation and Tasks -->
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-outline-secondary btn-sm me-2 btn-prev-week" 
                                        <?= $selected_week <= 1 ? 'disabled' : '' ?>>
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                
                                <div class="text-center">
                                    <h5 class="mb-0">
                                        Week <?= $selected_week ?> of 12
                                        <?php if ($selected_week == $current_week_number): ?>
                                            <span class="badge bg-primary ms-2">Current Week</span>
                                        <?php endif; ?>
                                    </h5>
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
                                <?php if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                                    <i class="fas fa-plus me-1"></i>Add Task
                                </button>
                                <?php endif; ?>
                                
                                <?php if (!empty($tasks) && (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms))): ?>
                                <button type="button" class="btn btn-outline-secondary btn-copy-tasks">
                                    <i class="fas fa-copy me-1"></i>Copy Tasks
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <?php if (empty($tasks)): ?>
                    <!-- No Tasks for This Week -->
                    <div class="text-center py-4">
                        <i class="fas fa-tasks fa-2x text-muted mb-3"></i>
                        <h5>No Tasks Planned</h5>
                        <p class="text-muted mb-3">No tasks have been planned for Week <?= $selected_week ?> yet.</p>
                        <?php if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                            <i class="fas fa-plus me-2"></i>Add Your First Task
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    
                    <!-- Tasks List -->
                    <div class="tasks-list">
                        <?php foreach ($tasks as $index => $task): ?>
                        <div class="task-item card mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-1 text-center">
                                        <span class="badge bg-secondary"><?= $index + 1 ?></span>
                                    </div>
                                    
                                    <div class="col-md-7">
                                        <h6 class="mb-2"><strong><?= htmlspecialchars($task['task_description']) ?></strong></h6>
                                        <div class="daily-checkboxes">
                                            <small class="text-muted d-block mb-1">Daily Completion:</small>
                                            <div class="d-flex gap-2">
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
                                        <small class="text-muted"><?= $completed_days ?>/7 days</small>
                                    </div>
                                    
                                    <div class="col-md-2 text-end">
                                        <div class="btn-group btn-group-sm">
                                            <?php if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
                                            <button type="button" class="btn btn-outline-secondary btn-edit-task" 
                                                    data-task-id="<?= $task['id'] ?>"
                                                    data-description="<?= htmlspecialchars($task['task_description']) ?>"
                                                    title="Edit Task">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php endif; ?>
                                            
                                            <?php if (in_array('DELETE', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
                                            <button type="button" class="btn btn-outline-danger btn-delete-task" 
                                                    data-task-id="<?= $task['id'] ?>"
                                                    data-description="<?= htmlspecialchars($task['task_description']) ?>"
                                                    title="Delete Task">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Task Modal -->
<?php if ($selected_goal && (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms))): ?>
<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add_task">
                <input type="hidden" name="goal_id" value="<?= $selected_goal_id ?>">
                <input type="hidden" name="week_number" value="<?= $selected_week ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title">Add Task for Week <?= $selected_week ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="task_description" class="form-label">Task Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="task_description" name="task_description" rows="3" 
                                  required maxlength="500" placeholder="Describe what you need to do this week..."></textarea>
                        <div class="form-text">Be specific about what you want to accomplish.</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Goal:</strong> <?= htmlspecialchars($selected_goal['title']) ?><br>
                        <strong>Week:</strong> <?= $selected_week ?> of 12
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

<!-- Edit Task Modal -->
<?php if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
<div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit_task">
                <input type="hidden" name="task_id" id="edit_task_id">
                <input type="hidden" name="goal_id" value="<?= $selected_goal_id ?>">
                <input type="hidden" name="week" value="<?= $selected_week ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_task_description" class="form-label">Task Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_task_description" name="task_description" rows="3" 
                                  required maxlength="500"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Copy Tasks Modal -->
<?php if ($selected_goal && (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms))): ?>
<div class="modal fade" id="copyTasksModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="copy_tasks">
                <input type="hidden" name="goal_id" id="copy_goal_id">
                <input type="hidden" name="from_week" id="copy_from_week">
                
                <div class="modal-header">
                    <h5 class="modal-title">Copy Tasks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="to_week" class="form-label">Copy to Week:</label>
                        <select class="form-select" name="to_week" required>
                            <option value="">Select destination week</option>
                            <?php for ($week = 1; $week <= 12; $week++): ?>
                                <?php if ($week != $selected_week): ?>
                                <option value="<?= $week ?>">Week <?= $week ?> of 12</option>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This will copy all tasks from Week <?= $selected_week ?> to the selected week. 
                        Existing tasks in the destination week will not be affected.
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Copy Tasks</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Delete Task Form (Hidden) -->
<?php if (in_array('DELETE', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
<form id="deleteTaskForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete_task">
    <input type="hidden" name="task_id" id="delete_task_id">
    <input type="hidden" name="goal_id" value="<?= $selected_goal_id ?>">
    <input type="hidden" name="week" value="<?= $selected_week ?>">
</form>
<?php endif; ?>

<script>
// Function to update task completion via AJAX
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
                progressText.textContent = completedDays + '/7 days';
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

// Auto-resize textareas
$(document).ready(function() {
    $('textarea').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});
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