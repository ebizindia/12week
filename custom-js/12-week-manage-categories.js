function updateColorPreview(input, preview) {
        $(preview).css('background-color', $(input).val());
    }
    
    $('#color_code').on('input', function() {
        updateColorPreview(this, '#add_color_preview');
    });
    
    $('#edit_color_code').on('input', function() {
        updateColorPreview(this, '#edit_color_preview');
    });
    
    $('.btn-edit').click(function() {
        $('#edit_category_id').val($(this).data('category-id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_color_code').val($(this).data('color-code'));
        $('#edit_sort_order').val($(this).data('sort-order'));
        updateColorPreview('#edit_color_code', '#edit_color_preview');
        $('#editCategoryModal').modal('show');
    });
    
    $('.btn-action[class*="btn-toggle"]').click(function() {
        var id = $(this).data('category-id');
        var name = $(this).data('name');
        var status = $(this).data('current-status');
        var newStatus = status ? 0 : 1;
        var action = newStatus ? 'activate' : 'deactivate';
        
        if (confirm('Are you sure you want to ' + action + ' "' + name + '"?')) {
            $('#toggle_category_id').val(id);
            $('#toggle_new_status').val(newStatus);
            $('#toggleStatusForm').submit();
        }
    });