<?php
$page = '12-week-manage-categories';
require_once("inc.php");

// Check if user has access to this module
if (!in_array('VIEW', $allowed_menu_perms) && !in_array('ALL', $allowed_menu_perms)) {
    header("Location: " . CONST_APP_ABSURL . "/");
    exit;
}

// Check if user is admin
$is_admin = ($loggedindata[0]['profile_details']['assigned_roles'][0]['role'] === 'ADMIN');
if (!$is_admin) {
    header("Location: " . CONST_APP_ABSURL . "/");
    exit;
}

$page_title = "Manage Categories - 12-Week Year";
$page_description = "Create and manage goal categories";

$user_id = $loggedindata[0]['id'];
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $conn = \eBizIndia\PDOConn::getInstance();
        
        switch ($action) {
            case 'add_category':
                if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)) {
                    $name = trim($_POST['name'] ?? '');
                    $color_code = trim($_POST['color_code'] ?? '#6c757d');
                    $sort_order = (int)($_POST['sort_order'] ?? 0);
                    
                    if (empty($name)) {
                        throw new Exception("Category name is required.");
                    }
                    
                    // Check if category name already exists
                    $check_sql = "SELECT COUNT(*) FROM categories WHERE name = :name";
                    $check_count = \eBizIndia\PDOConn::query($check_sql, [':name' => $name])->fetchColumn();
                    
                    if ($check_count > 0) {
                        throw new Exception("A category with this name already exists.");
                    }
                    
                    // Validate color code
                    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color_code)) {
                        $color_code = '#6c757d'; // Default color
                    }
                    
                    $insert_sql = "INSERT INTO categories (name, color_code, sort_order, is_active) 
                                   VALUES (:name, :color_code, :sort_order, 1)";
                    
                    $stmt = $conn->prepare($insert_sql);
                    $stmt->execute([
                        ':name' => $name,
                        ':color_code' => $color_code,
                        ':sort_order' => $sort_order
                    ]);
                    
                    $success_message = "Category '{$name}' added successfully!";
                }
                break;
                
            case 'edit_category':
                if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)) {
                    $category_id = (int)($_POST['category_id'] ?? 0);
                    $name = trim($_POST['name'] ?? '');
                    $color_code = trim($_POST['color_code'] ?? '#6c757d');
                    $sort_order = (int)($_POST['sort_order'] ?? 0);
                    
                    if (empty($name)) {
                        throw new Exception("Category name is required.");
                    }
                    
                    // Check if category name already exists (excluding current category)
                    $check_sql = "SELECT COUNT(*) FROM categories WHERE name = :name AND id != :category_id";
                    $check_count = \eBizIndia\PDOConn::query($check_sql, [
                        ':name' => $name, 
                        ':category_id' => $category_id
                    ])->fetchColumn();
                    
                    if ($check_count > 0) {
                        throw new Exception("A category with this name already exists.");
                    }
                    
                    // Validate color code
                    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color_code)) {
                        $color_code = '#6c757d'; // Default color
                    }
                    
                    $update_sql = "UPDATE categories 
                                   SET name = :name, color_code = :color_code, sort_order = :sort_order 
                                   WHERE id = :category_id";
                    
                    $stmt = $conn->prepare($update_sql);
                    $stmt->execute([
                        ':name' => $name,
                        ':color_code' => $color_code,
                        ':sort_order' => $sort_order,
                        ':category_id' => $category_id
                    ]);
                    
                    $success_message = "Category updated successfully!";
                }
                break;
                
            case 'toggle_status':
                if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)) {
                    $category_id = (int)($_POST['category_id'] ?? 0);
                    $new_status = (int)($_POST['new_status'] ?? 0);
                    
                    // If deactivating, check if category has goals
                    if ($new_status == 0) {
                        $goals_count_sql = "SELECT COUNT(*) FROM goals WHERE category_id = :category_id";
                        $goals_count = \eBizIndia\PDOConn::query($goals_count_sql, [':category_id' => $category_id])->fetchColumn();
                        
                        if ($goals_count > 0) {
                            throw new Exception("Cannot deactivate category. It has {$goals_count} goal(s) associated with it.");
                        }
                    }
                    
                    $update_sql = "UPDATE categories SET is_active = :status WHERE id = :category_id";
                    $stmt = $conn->prepare($update_sql);
                    $stmt->execute([':status' => $new_status, ':category_id' => $category_id]);
                    
                    $status_text = $new_status ? 'activated' : 'deactivated';
                    $success_message = "Category {$status_text} successfully!";
                }
                break;
                
            case 'reorder_categories':
                if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)) {
                    $category_orders = $_POST['category_orders'] ?? [];
                    
                    $conn->beginTransaction();
                    
                    foreach ($category_orders as $category_id => $sort_order) {
                        $update_sql = "UPDATE categories SET sort_order = :sort_order WHERE id = :category_id";
                        $stmt = $conn->prepare($update_sql);
                        $stmt->execute([
                            ':sort_order' => (int)$sort_order,
                            ':category_id' => (int)$category_id
                        ]);
                    }
                    
                    $conn->commit();
                    $success_message = "Category order updated successfully!";
                }
                break;
        }
    } catch (Exception $e) {
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }
        $error_message = $e->getMessage();
    }
}

// Get all categories with goal counts
$categories_sql = "SELECT c.*, COUNT(g.id) as goals_count 
                   FROM categories c 
                   LEFT JOIN goals g ON c.id = g.category_id 
                   GROUP BY c.id, c.name, c.color_code, c.sort_order, c.is_active, c.created_at 
                   ORDER BY c.sort_order, c.name";
$categories_stmt = \eBizIndia\PDOConn::query($categories_sql);
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get next sort order
$next_sort_order = 1;
if (!empty($categories)) {
    $max_sort_order = max(array_column($categories, 'sort_order'));
    $next_sort_order = $max_sort_order + 1;
}

$pageLoadJsCode = '
    // Initialize category management functionality
    CategoryManager.init();
';

$jscode = '
var CategoryManager = {
    init: function() {
        this.bindEvents();
        this.initSortable();
    },
    
    bindEvents: function() {
        // Edit category modal
        $(document).on("click", ".btn-edit-category", function() {
            var categoryId = $(this).data("category-id");
            var name = $(this).data("name");
            var colorCode = $(this).data("color-code");
            var sortOrder = $(this).data("sort-order");
            
            $("#editCategoryModal #edit_category_id").val(categoryId);
            $("#editCategoryModal #edit_name").val(name);
            $("#editCategoryModal #edit_color_code").val(colorCode);
            $("#editCategoryModal #edit_sort_order").val(sortOrder);
            $("#editCategoryModal").modal("show");
        });
        
        // Toggle status confirmation
        $(document).on("click", ".btn-toggle-status", function() {
            var categoryId = $(this).data("category-id");
            var name = $(this).data("name");
            var currentStatus = $(this).data("current-status");
            var newStatus = currentStatus == 1 ? 0 : 1;
            var action = newStatus == 1 ? "activate" : "deactivate";
            
            if (confirm("Are you sure you want to " + action + " the category: " + name + "?")) {
                $("#toggleStatusForm #toggle_category_id").val(categoryId);
                $("#toggleStatusForm #toggle_new_status").val(newStatus);
                $("#toggleStatusForm").submit();
            }
        });
        
        // Color picker preview
        $(document).on("input", "input[type=color]", function() {
            var color = $(this).val();
            $(this).closest(".input-group").find(".color-preview").css("background-color", color);
        });
        
        // Clear modals on close
        $(".modal").on("hidden.bs.modal", function() {
            $(this).find("form")[0].reset();
        });
    },
    
    initSortable: function() {
        // Initialize sortable functionality for reordering categories
        if (typeof Sortable !== "undefined") {
            var categoryList = document.getElementById("categoryList");
            if (categoryList) {
                Sortable.create(categoryList, {
                    animation: 150,
                    ghostClass: "sortable-ghost",
                    onEnd: function(evt) {
                        CategoryManager.updateCategoryOrder();
                    }
                });
            }
        }
    },
    
    updateCategoryOrder: function() {
        var categoryOrders = {};
        $("#categoryList .category-item").each(function(index) {
            var categoryId = $(this).data("category-id");
            categoryOrders[categoryId] = index + 1;
        });
        
        $("#reorderForm input[name=category_orders]").remove();
        $.each(categoryOrders, function(categoryId, sortOrder) {
            $("#reorderForm").append("<input type=\"hidden\" name=\"category_orders[" + categoryId + "]\" value=\"" + sortOrder + "\">");
        });
        
        $("#reorderForm").submit();
    }
};
';

// Set template data
$template_data = array(
    'page_title' => $page_title,
    'page_description' => $page_description,
    'success_message' => $success_message,
    'error_message' => $error_message,
    'categories' => $categories,
    'next_sort_order' => $next_sort_order,
    'allowed_menu_perms' => $allowed_menu_perms,
    'user_id' => $user_id,
    'is_admin' => $is_admin
);

// Register body template
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . '12-week-manage-categories.tpl';
$page_renderer->registerBodyTemplate($body_template_file, $template_data);

// Update base template data
$additional_base_template_data = ['page_title' => $page_title, 'module_name' => $page];
$page_renderer->updateBaseTemplateData($additional_base_template_data);

$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
// Render the page
$page_renderer->renderPage();
?>