<?php
$success_message = $this->body_template_data['success_message'];
$error_message = $this->body_template_data['error_message'];
$categories = $this->body_template_data['categories'];
$next_sort_order = $this->body_template_data['next_sort_order'];
$allowed_menu_perms = $this->body_template_data['allowed_menu_perms'];
$is_admin = $this->body_template_data['is_admin'];
?>
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
/* ============================================
   MOBILE-FIRST MANAGE CATEGORIES DESIGN
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
    
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
    --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
    
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
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    -webkit-font-smoothing: antialiased;
}

.container-fluid {
    padding-left: 0;
    padding-right: 0;
}

.categories-wrapper {
    min-height: 100vh;
    padding: 4px;
    padding-bottom: 80px;
}

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
    margin: 0 0 var(--spacing-md) 0;
}

.btn-add-category {
    background: #52a2e8;
    color: white;
    border: none;
    border-radius: var(--radius-md);
    padding: var(--spacing-sm) var(--spacing-md);
    font-weight: 600;
    font-size: 0.938rem;
    width: 100%;
    min-height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-xs);
    transition: all 0.3s ease;
}

.categories-card {
    background: white;
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.categories-header {
    background: #52a2e8;
    color: white;
    padding: var(--spacing-md) var(--spacing-xs);
}

.categories-header h5 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.categories-body {
    padding: var(--spacing-xs);
}

.category-item {
    background: white;
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    margin-bottom: var(--spacing-xs);
    box-shadow: var(--shadow-sm);
    border-left: 4px solid;
    transition: all 0.3s ease;
}

.category-header-row {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-md);
}

.color-preview {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    flex-shrink: 0;
    border: 3px solid #fff;
    box-shadow: var(--shadow-sm);
}

.category-name {
    color: var(--text-primary);
    font-weight: 700;
    font-size: 1rem;
}

.category-meta {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-xs);
    margin-bottom: var(--spacing-sm);
}

.stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    border-radius: var(--radius-xl);
    font-weight: 600;
    font-size: 0.813rem;
    color: white;
}

.stat-badge.badge-primary {
    background: #52a2e8;
}

.stat-badge.badge-success {
    background: var(--success-gradient);
}

.stat-badge.badge-secondary {
    background: linear-gradient(135deg, #bdc3c7 0%, #95a5a6 100%);
}

.category-actions {
    display: flex;
    gap: var(--spacing-xs);
}

.btn-action {
    flex: 1;
    border: 2px solid;
    border-radius: var(--radius-md);
    padding: var(--spacing-xs) var(--spacing-sm);
    font-weight: 600;
    font-size: 0.813rem;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-xs);
    transition: all 0.3s ease;
    background: white;
}

.btn-action.btn-edit {
    border-color: #667eea;
    color: #667eea;
}

.btn-action.btn-toggle-active {
    border-color: #f2994a;
    color: #f2994a;
}

.btn-action.btn-toggle-inactive {
    border-color: #11998e;
    color: #11998e;
}

.empty-state {
    padding: var(--spacing-xl);
    text-align: center;
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

.modal-content {
    border-radius: var(--radius-lg);
    border: none;
}

.modal-header-modern {
    background: #52a2e8;
    color: white;
    padding: var(--spacing-lg);
    border: none;
}

.modal-header-modern .modal-title {
    color: white;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.modal-body {
    padding: var(--spacing-lg);
}

.form-group {
    margin-bottom: var(--spacing-md);
}

.form-group label {
    font-weight: 600;
    margin-bottom: var(--spacing-xs);
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.form-control {
    border-radius: var(--radius-md);
    border: 2px solid #e9ecef;
    padding: var(--spacing-sm);
    min-height: 44px;
}

.form-control:focus {
    border-color: #667eea;
    outline: none;
}

.input-group {
    display: flex;
}

.input-group-append {
    display: flex;
}

.input-group-text {
    border: 2px solid #e9ecef;
    border-left: none;
    padding: var(--spacing-sm);
}

.color-preview-small {
    width: 24px;
    height: 24px;
    border-radius: 50%;
}

.alert-tip {
    background: #e3f2fd;
    border-left: 4px solid #2196F3;
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    margin-top: var(--spacing-md);
}

.modal-footer {
    padding: var(--spacing-lg);
    border: none;
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.btn-primary-modern {
    background: #52a2e8;
    color: white;
    border: none;
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    font-weight: 600;
    width: 100%;
    min-height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
}

.btn-secondary-modern {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    font-weight: 600;
    width: 100%;
    min-height: 48px;
}

@media (min-width: 768px) {
    .categories-wrapper {
        padding: var(--spacing-md);
    }
    
    .page-header {
        padding: var(--spacing-lg);
    }
    
    .page-header-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    
    .page-header .subtitle {
        margin-bottom: 0;
    }
    
    .btn-add-category {
        width: auto;
        min-width: 200px;
    }
    
    .modal-footer {
        flex-direction: row;
    }
    
    .btn-primary-modern,
    .btn-secondary-modern {
        width: auto;
        flex: 1;
    }
}
</style>

<div class="categories-wrapper">
    <div class="container-fluid">
        <div class="page-header">
            <div class="page-header-content">
                <div>
                    <h1>
                        <i class="fas fa-tags"></i>
                        <span>Manage Categories</span>
                    </h1>
                    <p class="subtitle">Create and organize goal categories</p>
                </div>
                <?php if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
                <button type="button" class="btn-add-category" data-toggle="modal" data-target="#addCategoryModal">
                    <i class="fas fa-plus"></i>
                    <span>Add Category</span>
                </button>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($success_message): ?>
        <div class="alert alert-success alert-modern alert-dismissible fade show">
            <i class="fas fa-check-circle"></i>
            <span><?= htmlspecialchars($success_message) ?></span>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
        <div class="alert alert-danger alert-modern alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?= htmlspecialchars($error_message) ?></span>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <?php endif; ?>

        <div class="categories-card">
            <div class="categories-header">
                <h5><i class="fas fa-list"></i> Goal Categories</h5>
            </div>
            <div class="categories-body">
                <?php if (empty($categories)): ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-tags"></i></div>
                    <h4>No Categories Yet</h4>
                    <p>Create your first goal category</p>
                    <?php if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
                    <button type="button" class="btn-add-category" data-toggle="modal" data-target="#addCategoryModal">
                        <i class="fas fa-plus"></i> Create First Category
                    </button>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <?php foreach ($categories as $category): ?>
                <div class="category-item" style="border-left-color: <?= htmlspecialchars($category['color_code']) ?>;">
                    <div class="category-header-row">
                        <div class="color-preview" style="background-color: <?= htmlspecialchars($category['color_code']) ?>;"></div>
                        <h6 class="category-name"><?= htmlspecialchars($category['name']) ?></h6>
                    </div>
                    <div class="category-meta">
                        <span class="stat-badge badge-primary">
                            <i class="fas fa-bullseye"></i> <?= $category['goals_count'] ?> goals
                        </span>
                        <span class="stat-badge <?= $category['is_active'] ? 'badge-success' : 'badge-secondary' ?>">
                            <i class="fas fa-<?= $category['is_active'] ? 'check-circle' : 'eye-slash' ?>"></i>
                            <?= $category['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                    <?php if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
                    <div class="category-actions">
                        <button type="button" class="btn-action btn-edit" 
                                data-category-id="<?= $category['id'] ?>"
                                data-name="<?= htmlspecialchars($category['name']) ?>"
                                data-color-code="<?= htmlspecialchars($category['color_code']) ?>"
                                data-sort-order="<?= $category['sort_order'] ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button type="button" class="btn-action <?= $category['is_active'] ? 'btn-toggle-active' : 'btn-toggle-inactive' ?>" 
                                data-category-id="<?= $category['id'] ?>"
                                data-name="<?= htmlspecialchars($category['name']) ?>"
                                data-current-status="<?= $category['is_active'] ?>">
                            <i class="fas fa-<?= $category['is_active'] ? 'eye-slash' : 'eye' ?>"></i>
                            <?= $category['is_active'] ? 'Hide' : 'Show' ?>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add_category">
                <div class="modal-header modal-header-modern">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Add New Category</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Category Name *</label>
                        <input type="text" class="form-control" name="name" required maxlength="100" placeholder="e.g., Business & Finance">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-palette"></i> Category Color</label>
                        <div class="input-group">
                            <input type="color" class="form-control" id="color_code" name="color_code" value="#007bff" style="max-width:80px;">
                            <div class="input-group-append">
                                <span class="input-group-text" style="padding:0 8px;border-radius:0px 12px 12px 0px;">
                                    <div class="color-preview-small" id="add_color_preview" style="background:#007bff;"></div>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-sort-numeric-down"></i> Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="<?= $next_sort_order ?>" min="1" max="999">
                    </div>
                    <div class="alert-tip">
                        <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> Categories help organize goals into groups.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary-modern" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-primary-modern"><i class="fas fa-check"></i> Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)): ?>
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit_category">
                <input type="hidden" name="category_id" id="edit_category_id">
                <div class="modal-header modal-header-modern">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Category</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Category Name *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-palette"></i> Category Color</label>
                        <div class="input-group">
                            <input type="color" class="form-control" id="edit_color_code" name="color_code" style="max-width:80px;">
                            <div class="input-group-append">
                                <span class="input-group-text" style="padding:0 8px;border-radius: 0 12px 12px 0;">
                                    <div class="color-preview-small" id="edit_color_preview"></div>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-sort-numeric-down"></i> Sort Order</label>
                        <input type="number" class="form-control" id="edit_sort_order" name="sort_order" min="1" max="999">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary-modern" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-primary-modern"><i class="fas fa-save"></i> Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="toggleStatusForm" method="POST" style="display:none;">
    <input type="hidden" name="action" value="toggle_status">
    <input type="hidden" name="category_id" id="toggle_category_id">
    <input type="hidden" name="new_status" id="toggle_new_status">
</form>
<?php endif; ?>

<script>
/*$(document).ready(function() {
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
});*/
</script>