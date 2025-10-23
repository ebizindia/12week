$(document).ready(function() {
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
});