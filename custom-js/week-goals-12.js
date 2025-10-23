var GoalManager = {
    init: function() {
        this.bindEvents();
    },
    
    bindEvents: function() {
        // Add goal modal
        $(document).on("click", ".btn-add-goal", function() {
            var categoryId = $(this).data("category-id");
            $("#addGoalModal #category_id").val(categoryId);
            $("#addGoalModal").modal("show");
        });
        
        // Edit goal modal
        $(document).on("click", ".btn-edit-goal", function() {
            var goalId = $(this).data("goal-id");
            var title = $(this).data("title");
            var categoryId = $(this).data("category-id");
            
            $("#editGoalModal #goal_id").val(goalId);
            $("#editGoalModal #edit_title").val(title);
            $("#editGoalModal #edit_category_id").val(categoryId);
            $("#editGoalModal").modal("show");
        });
        
        // Delete goal confirmation
        $(document).on("click", ".btn-delete-goal", function() {
            var goalId = $(this).data("goal-id");
            var title = $(this).data("title");
            
            if (confirm("Are you sure you want to delete the goal: " + title + "?\n\nThis will also delete all associated tasks.")) {
                $("#deleteGoalForm #delete_goal_id").val(goalId);
                $("#deleteGoalForm").submit();
            }
        });
        
        // Clear modals on close
        $(".modal").on("hidden.bs.modal", function() {
            $(this).find("form")[0].reset();
        });
    }
};



$(document).ready(function() {
     GoalManager.init();
    // Auto-select category when adding goal from specific category
    $('#addGoalModal').on('show.bs.modal', function(e) {
        var categoryId = $(e.relatedTarget).data('category-id');
        if (categoryId) {
            $('#add_category_select').val(categoryId);
        }
    });
    
    // Edit goal modal
    /*$(document).on("click", ".btn-edit-goal", function() {
        var goalId = $(this).data("goal-id");
        var title = $(this).data("title");
        var categoryId = $(this).data("category-id");
        
        $("#editGoalModal #goal_id").val(goalId);
        $("#editGoalModal #edit_title").val(title);
        $("#editGoalModal #edit_category_id").val(categoryId);
        $("#editGoalModal").modal("show");
    });*/
    
    // Delete goal confirmation
    /*$(document).on("click", ".btn-delete-goal", function() {
        var goalId = $(this).data("goal-id");
        var title = $(this).data("title");
        
        if (confirm("Are you sure you want to delete the goal: " + title + "?\n\nThis will also delete all associated tasks.")) {
            $("#deleteGoalForm #delete_goal_id").val(goalId);
            $("#deleteGoalForm").submit();
        }
    });*/
    
    // No need for toggle functionality - tasks are always visible
    
    // Add task modal
    $(document).on("click", ".btn-add-task", function() {
        var goalId = $(this).data("goal-id");
        var goalTitle = $(this).data("goal-title");
        
        $("#task_goal_id").val(goalId);
        $("#task_goal_title").text(goalTitle);
        $("#addTaskModal").modal("show");
    });
    
    // Add task form submission
    $("#addTaskForm").on("submit", function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'add_task',
            goal_id: $("#task_goal_id").val(),
            title: $("#task_title").val(),
            week_number: $("#task_week").val(),
            weekly_target: $("#task_target").val()
        };
        
        $.ajax({
            url: '12-week-goals.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $("#addTaskModal").modal("hide");
                    
                    // Show success message and reload page to show new task
                    showAlert('success', 'Task added successfully!');
                    setTimeout(function() {
                        window.location=window.location
                    }, 1000);
                } else {
                    showAlert('danger', response.error || 'Failed to add task');
                }
            },
            error: function() {
                showAlert('danger', 'Error adding task. Please try again.');
            }
        });
    });
    
    /*// Handle daily checkbox changes
    $(document).on("change", ".f", function() {
        var taskId = $(this).data("task-id");
        var day = $(this).data("day");
        var completed = $(this).is(":checked") ? 1 : 0;
        var checkbox = $(this);
        
        $.ajax({
            url: '12-week-goals.php',
            method: 'POST',
            data: {
                action: 'update_day_completion',
                task_id: taskId,
                day: day,
                completed: completed
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update totals and scores immediately
                    updateTaskTotalsAndScore(taskId);
                } else {
                    // Revert checkbox if update failed
                    checkbox.prop('checked', !completed);
                    showAlert('danger', response.error || 'Failed to update task completion');
                }
            },
            error: function() {
                // Revert checkbox if update failed
                checkbox.prop('checked', !completed);
                showAlert('danger', 'Error updating task completion. Please try again.');
            }
        });
    });*/
    
    // Function to update task totals and scores
    /*function updateTaskTotalsAndScore(taskId) {
        // Count checked checkboxes for this task
        var checkedCount = 0;
        var target = 1; // default
        
        $('input[data-task-id="' + taskId + '"]').each(function() {
            if ($(this).is(':checked')) {
                checkedCount++;
            }
            // Get target from the first checkbox data attribute
            if (target === 1) {
                target = parseInt($(this).data('target')) || 1;
            }
        });
        
        // Calculate score
        var scorePercent = target > 0 ? Math.round((checkedCount / target) * 100) : 0;
        var scoreClass = checkedCount >= target ? 'success' : (checkedCount > 0 ? 'warning' : 'danger');
        
        // Update total badge
        $('#total-' + taskId).text(checkedCount);
        
        // Update score badge
        var scoreBadge = $('#score-' + taskId);
        scoreBadge.removeClass('badge-score-success badge-score-warning badge-score-danger')
                  .addClass('badge-score-' + scoreClass + ' text-white')
                  .text(scorePercent + '%');
    }*/
    
    // Delete task
    $(document).on("click", ".btn-delete-task", function() {
        var taskId = $(this).data("task-id");
        var goalId = $(this).data("goal-id");
        var title = $(this).data("title");
        
        if (confirm("Are you sure you want to delete the task: " + title + "?")) {
            $.ajax({
                url: '12-week-goals.php',
                method: 'POST',
                data: {
                    action: 'delete_task',
                    task_id: taskId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', 'Task deleted successfully!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showAlert('danger', response.error || 'Failed to delete task');
                    }
                },
                error: function() {
                    showAlert('danger', 'Error deleting task. Please try again.');
                }
            });
        }
    });
    
    $(document).on("change", ".task-title-input", function() {
        var taskId = $(this).data("task-id");
        var newTitle = $(this).val().trim();
        var originalTitle = $(this).data("original-title");
        var input = $(this);
        
        // Don't save if title is empty
        if (newTitle === '') {
            showAlert('danger', 'Task title cannot be empty');
            input.val(originalTitle);
            return;
        }
        
        // Don't save if title hasn't changed
        if (newTitle === originalTitle) {
            return;
        }
        
        // Save to database
        $.ajax({
            url: '12-week-goals.php',
            method: 'POST',
            data: {
                action: 'update_task_title',
                task_id: taskId,
                title: newTitle
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update the original title data attribute
                    input.data("original-title", newTitle);
                    showAlert('success', 'Task title updated successfully');
                } else {
                    // Revert to original title
                    input.val(originalTitle);
                    showAlert('danger', response.error || 'Failed to update task title');
                }
            },
            error: function() {
                // Revert to original title
                input.val(originalTitle);
                showAlert('danger', 'Error updating task title. Please try again.');
            }
        });
    });



    // Tasks are now displayed directly in PHP, no need for AJAX loading
    
    
    
    // Clear modals on close
    $(".modal").on("hidden.bs.modal", function() {
        $(this).find("form")[0].reset();
    });
    
    // Initialize week navigation
    initWeekNavigation();
});
// Show alert message
    function showAlert(type, message) {
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">';
        alertHtml += '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-triangle') + ' me-2"></i>' + message;
        alertHtml += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        alertHtml += '</div>';
        
        $(".container-fluid .row .col-12").prepend(alertHtml);
        
        // Auto-remove after 5 seconds
        setTimeout(function() {
            $(".alert").fadeOut();
        }, 5000);
    }
    
    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
// Week Navigation Functions
function initWeekNavigation() {
    var currentWeek = parseInt(week_goals_12.current_week);
    var actualCurrentWeek = parseInt(week_goals_12.actual_current_week);
    
    // Update navigation button states
    $("#prevWeek").prop('disabled', currentWeek <= 1);
    $("#nextWeek").prop('disabled', currentWeek >= 12);
    
    // Add "Go to Current Week" button if not viewing current week
    /*if (currentWeek !== actualCurrentWeek) {
        var currentWeekBtn = '<button type="button" class="btn btn-success btn-sm ms-2" onclick="goToCurrentWeek()">' +
                           '<i class="fas fa-calendar-day"></i> Go to Current Week (' + actualCurrentWeek + ')' +
                           '</button>';
        $("#nextWeek").after(currentWeekBtn);
    }*/
}

function changeWeek(direction) {
    var currentWeek = parseInt(week_goals_12.current_week);
    var newWeek = currentWeek + direction;
    
    if (newWeek >= 1 && newWeek <= 12) {
        window.location.href = '12-week-goals.php?week=' + newWeek;
    }
}

function goToCurrentWeek() {
    var actualCurrentWeek = parseInt(week_goals_12.actual_current_week);
    window.location.href = '12-week-goals.php?week=' + actualCurrentWeek;
}

// Handle target input changes
$(document).on("change", ".task-target-input", function() {
    var taskId = $(this).data("task-id");
    var newTarget = parseInt($(this).val()) || 1;
    
    // Validate target (1-7)
    if (newTarget < 1) newTarget = 1;
    if (newTarget > 7) newTarget = 7;
    $(this).val(newTarget);
    
    // Update data attribute for calculations
    $('input[data-task-id="' + taskId + '"]').attr('data-target', newTarget).data('target', newTarget);
    // Recalculate score with new target
    updateTaskTotalsAndScore(taskId);
    
    // Save to database
    $.ajax({
        url: '12-week-goals.php',
        method: 'POST',
        data: {
            action: 'update_task_target',
            task_id: taskId,
            weekly_target: newTarget
        },
        dataType: 'json',
        success: function(response) {
            if (!response.success) {
                showAlert('danger', response.error || 'Failed to update target');
            }
        },
        error: function() {
            showAlert('danger', 'Error updating target. Please try again.');
        }
    });
});

// Handle checkbox changes with immediate UI updates
$(document).on("change", ".task-day-checkbox", function() {
    var taskId = $(this).data("task-id");
    var day = $(this).data("day");
    var completed = $(this).is(":checked") ? 1 : 0;
    var target = parseInt($(this).data("target")) || 1;
    var checkbox = $(this);
    
    // Update UI immediately for better user experience
    updateTaskTotalsAndScore(taskId);
    
    // Send AJAX request to save to database
    $.ajax({
        url: '12-week-goals.php',
        method: 'POST',
        data: {
            action: 'update_day_completion',
            task_id: taskId,
            day: day,
            completed: completed
        },
        dataType: 'json',
        success: function(response) {
            if (!response.success) {
                // Revert checkbox if save failed
                checkbox.prop('checked', !completed);
                updateTaskTotalsAndScore(taskId);
                showAlert('danger', response.error || 'Failed to save changes');
            }
        },
        error: function() {
            // Revert checkbox if AJAX failed
            checkbox.prop('checked', !completed);
            updateTaskTotalsAndScore(taskId);
            showAlert('danger', 'Error saving changes. Please try again.');
        }
    });
});

// Function to update totals and scores immediately
/*function updateTaskTotalsAndScore(taskId) {
    // Count checked checkboxes for this task
    var checkedCount = 0;
    var target = 1;
    
    $('input[data-task-id="' + taskId + '"]').each(function() {
        if ($(this).is(':checked')) {
            checkedCount++;
        }
        // Get target from first checkbox
        if (target === 1) {
            target = parseInt($(this).data("target")) || 1;
        }
    });
    
    // Calculate score
    var scorePercent = Math.round((checkedCount / target) * 100);
    var scoreClass = checkedCount >= target ? 'success' : (checkedCount > 0 ? 'warning' : 'danger');
    
    // Update total badge
    var totalBadge = $('#total-' + taskId);
    if (totalBadge.length) {
        totalBadge.text(checkedCount);
    }
    
    // Update score badge
    var scoreBadge = $('#score-' + taskId);
    if (scoreBadge.length) {
        scoreBadge.removeClass('bg-success bg-warning bg-danger')
                  .addClass('bg-' + scoreClass + ' text-white')
                  .text(scorePercent + '%');
    }
    
    console.log('Task ' + taskId + ': ' + checkedCount + '/' + target + ' = ' + scorePercent + '%');
}*/

function updateTaskTotalsAndScore(taskId) {
        // Count checked checkboxes for this task
        var checkedCount = 0;
        var target = 1; // default
        
        $('input[data-task-id="' + taskId + '"]').each(function() {
            if ($(this).is(':checked')) {
                checkedCount++;
            }
            // Get target from the first checkbox data attribute
            if (target === 1) {
                target = parseInt($(this).data('target')) || 1;
            }
        });
        
        // Calculate score
        var scorePercent = target > 0 ? Math.round((checkedCount / target) * 100) : 0;
        var scoreClass = checkedCount >= target ? 'success' : (checkedCount > 0 ? 'warning' : 'danger');

        // Update total badge
        $('#total-' + taskId).text(checkedCount);
        
        // Update score badge
        var scoreBadge = $('#score-' + taskId);
        scoreBadge.removeClass('badge-success badge-warning badge-danger')
                  .addClass('badge-' + scoreClass + ' text-white')
                  .text(scorePercent + '%');
    }