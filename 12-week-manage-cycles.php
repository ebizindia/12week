<?php
$page = '12-week-manage-cycles';
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

$page_title = "Manage Cycles - 12-Week Year";
$page_description = "Create and manage 12-week cycles";

$user_id = $loggedindata[0]['id'];
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $conn = \eBizIndia\PDOConn::getInstance();
        
        switch ($action) {
            case 'create_cycle':
                if (in_array('ADD', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)) {
                    $name = trim($_POST['name'] ?? '');
                    $start_date = trim($_POST['start_date'] ?? '');
                    
                    if (empty($name)) {
                        throw new Exception("Cycle name is required.");
                    }
                    
                    if (empty($start_date)) {
                        throw new Exception("Start date is required.");
                    }
                    
                    // Validate start date is a Monday
                    $start_datetime = new DateTime($start_date);
                    if ($start_datetime->format('N') != 1) { // 1 = Monday
                        throw new Exception("Start date must be a Monday.");
                    }
                    
                    // Check if there's already a cycle with overlapping dates
                    $overlap_check_sql = "SELECT COUNT(*) FROM cycles 
                                         WHERE (start_date <= :end_date AND end_date >= :start_date)";
                    $overlap_count = \eBizIndia\PDOConn::query($overlap_check_sql, [
                        ':start_date' => $start_date,
                        ':end_date' => $end_date
                    ])->fetchColumn();
                    
                    if ($overlap_count > 0) {
                        throw new Exception("There is already a cycle with overlapping dates. Cycles cannot overlap.");
                    }
                    
                    // Calculate end date (start_date + 83 days = 12 weeks)
                    $end_datetime = clone $start_datetime;
                    $end_datetime->add(new DateInterval('P83D'));
                    $end_date = $end_datetime->format('Y-m-d');
                    
                    $insert_sql = "INSERT INTO cycles (name, start_date, end_date, created_by) 
                                   VALUES (:name, :start_date, :end_date, :created_by)";
                    
                    $stmt = $conn->prepare($insert_sql);
                    $stmt->execute([
                        ':name' => $name,
                        ':start_date' => $start_date,
                        ':end_date' => $end_date,
                        ':created_by' => $user_id
                    ]);
                    
                    $success_message = "Cycle '{$name}' created successfully! It will run from {$start_date} to {$end_date}.";
                }
                break;
                
            case 'edit_cycle':
                if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)) {
                    $cycle_id = (int)($_POST['cycle_id'] ?? 0);
                    $name = trim($_POST['name'] ?? '');
                    $start_date = trim($_POST['start_date'] ?? '');
                    
                    if (empty($name)) {
                        throw new Exception("Cycle name is required.");
                    }
                    
                    if (empty($start_date)) {
                        throw new Exception("Start date is required.");
                    }
                    
                    // Validate start date is a Monday
                    $start_datetime = new DateTime($start_date);
                    if ($start_datetime->format('N') != 1) { // 1 = Monday
                        throw new Exception("Start date must be a Monday.");
                    }
                    
                    // Calculate end date (start_date + 83 days = 12 weeks)
                    $end_datetime = clone $start_datetime;
                    $end_datetime->add(new DateInterval('P83D'));
                    $end_date = $end_datetime->format('Y-m-d');
                    
                    $update_sql = "UPDATE cycles 
                                   SET name = :name, start_date = :start_date, end_date = :end_date 
                                   WHERE id = :cycle_id";
                    
                    $stmt = $conn->prepare($update_sql);
                    $stmt->execute([
                        ':name' => $name,
                        ':start_date' => $start_date,
                        ':end_date' => $end_date,
                        ':cycle_id' => $cycle_id
                    ]);
                    
                    $success_message = "Cycle updated successfully!";
                }
                break;
                
            case 'close_cycle':
                if (in_array('EDIT', $allowed_menu_perms) || in_array('ALL', $allowed_menu_perms)) {
                    $cycle_id = (int)($_POST['cycle_id'] ?? 0);
                    
                    $update_sql = "UPDATE cycles SET status = 'completed' WHERE id = :cycle_id";
                    $stmt = $conn->prepare($update_sql);
                    $stmt->execute([':cycle_id' => $cycle_id]);
                    
                    $success_message = "Cycle closed successfully!";
                }
                break;
                
            // Reactivation removed - cycles are now automatically determined by dates
                break;
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Get all cycles
$cycles_sql = "SELECT c.*, 
               COALESCE(m.name, u.username) as created_by_name 
               FROM cycles c 
               LEFT JOIN users u ON c.created_by = u.id 
               LEFT JOIN members m ON u.profile_id = m.id AND u.profile_type = 'member'
               ORDER BY c.created_at DESC";
$cycles_stmt = \eBizIndia\PDOConn::query($cycles_sql);
$cycles = $cycles_stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate additional data for each cycle
foreach ($cycles as &$cycle) {
    $start_date = new DateTime($cycle['start_date']);
    $end_date = new DateTime($cycle['end_date']);
    $today = new DateTime();
    
    // Calculate current week
    if ($today < $start_date) {
        $cycle['current_week'] = 0;
        $cycle['status_text'] = 'Not Started';
        $cycle['days_remaining'] = $start_date->diff($today)->days;
    } elseif ($today > $end_date) {
        $cycle['current_week'] = 12;
        $cycle['status_text'] = 'Completed';
        $cycle['days_remaining'] = 0;
    } else {
        $days_passed = $start_date->diff($today)->days;
        $cycle['current_week'] = min(floor($days_passed / 7) + 1, 12);
        $cycle['status_text'] = 'Week ' . $cycle['current_week'] . ' of 12';
        $cycle['days_remaining'] = $end_date->diff($today)->days;
    }
    
    // Get member count for this cycle
    $member_count_sql = "SELECT COUNT(DISTINCT user_id) as member_count 
                         FROM goals 
                         WHERE cycle_id = :cycle_id";
    $member_count_stmt = \eBizIndia\PDOConn::query($member_count_sql, [':cycle_id' => $cycle['id']]);
    $cycle['member_count'] = $member_count_stmt->fetchColumn() ?: 0;
    
    // Get goals count for this cycle
    $goals_count_sql = "SELECT COUNT(*) as goals_count 
                        FROM goals 
                        WHERE cycle_id = :cycle_id";
    $goals_count_stmt = \eBizIndia\PDOConn::query($goals_count_sql, [':cycle_id' => $cycle['id']]);
    $cycle['goals_count'] = $goals_count_stmt->fetchColumn() ?: 0;
}

$pageLoadJsCode = '
    // Initialize cycle management functionality
    CycleManager.init();
';

$jscode = '
var CycleManager = {
    init: function() {
        this.bindEvents();
    },
    
    bindEvents: function() {
        // Edit cycle modal
        $(document).on("click", ".btn-edit-cycle", function() {
            var cycleId = $(this).data("cycle-id");
            var name = $(this).data("name");
            var startDate = $(this).data("start-date");
            
            $("#editCycleModal #edit_cycle_id").val(cycleId);
            $("#editCycleModal #edit_name").val(name);
            $("#editCycleModal #edit_start_date").val(startDate);
            $("#editCycleModal").modal("show");
        });
        
        // Close cycle confirmation
        $(document).on("click", ".btn-close-cycle", function() {
            var cycleId = $(this).data("cycle-id");
            var name = $(this).data("name");
            
            if (confirm("Are you sure you want to close the cycle: " + name + "?\\n\\nThis will mark it as completed and members will no longer be able to add goals or tasks.")) {
                $("#closeCycleForm #close_cycle_id").val(cycleId);
                $("#closeCycleForm").submit();
            }
        });
        
        // Reactivation removed - cycles are now automatically determined by dates
        
        // Clear modals on close
        $(".modal").on("hidden.bs.modal", function() {
            $(this).find("form")[0].reset();
        });
        
        // Validate start date is Monday
        $(document).on("change", "input[type=date]", function() {
            var selectedDate = new Date($(this).val());
            var dayOfWeek = selectedDate.getDay(); // 0 = Sunday, 1 = Monday, etc.
            
            if (dayOfWeek !== 1) { // Not Monday
                alert("Start date must be a Monday. Please select a Monday.");
                $(this).focus();
            }
        });
    }
};
';

// Set template data
$template_data = array(
    'page_title' => $page_title,
    'page_description' => $page_description,
    'success_message' => $success_message,
    'error_message' => $error_message,
    'cycles' => $cycles,
    'allowed_menu_perms' => $allowed_menu_perms,
    'user_id' => $user_id,
    'is_admin' => $is_admin
);

// Register body template
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . '12-week-manage-cycles.tpl';
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