<?php
// Simple debug script to check 12-week year database setup
require_once("config.php");
require_once(CONST_INCLUDES_DIR."/ebiz-autoload.php");
require_once(CONST_INCLUDES_DIR . "general-func.php");

// Connect to database
\eBizIndia\PDOConn::connectToDB('mysql');

echo "<h2>12-Week Year Database Debug</h2>";

try {
    // Check if tables exist
    $tables_to_check = ['cycles', 'categories', 'goals', 'tasks', 'weekly_scores'];
    
    echo "<h3>Table Existence Check:</h3>";
    foreach ($tables_to_check as $table) {
        try {
            $check_sql = "SELECT COUNT(*) FROM $table";
            $stmt = \eBizIndia\PDOConn::query($check_sql);
            $count = $stmt->fetchColumn();
            echo "✅ Table '$table' exists with $count records<br>";
        } catch (Exception $e) {
            echo "❌ Table '$table' missing or error: " . $e->getMessage() . "<br>";
        }
    }
    
    echo "<h3>Categories Check:</h3>";
    try {
        $categories_sql = "SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order, name";
        $categories_stmt = \eBizIndia\PDOConn::query($categories_sql);
        $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($categories)) {
            echo "❌ No active categories found<br>";
        } else {
            echo "✅ Found " . count($categories) . " active categories:<br>";
            foreach ($categories as $category) {
                echo "- {$category['name']} (ID: {$category['id']})<br>";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error checking categories: " . $e->getMessage() . "<br>";
    }
    
    echo "<h3>Cycles Check:</h3>";
    try {
        // Get current cycle based on date
        $current_cycle = \eBizIndia\getCurrentCycleByDate();
        
        if (!$current_cycle) {
            echo "❌ No active cycle found<br>";
            
            // Check if any cycles exist
            $all_cycles_sql = "SELECT * FROM cycles ORDER BY created_at DESC";
            $all_cycles_stmt = \eBizIndia\PDOConn::query($all_cycles_sql);
            $all_cycles = $all_cycles_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($all_cycles)) {
                echo "❌ No cycles exist at all<br>";
            } else {
                echo "ℹ️ Found " . count($all_cycles) . " cycles (but none active):<br>";
                foreach ($all_cycles as $cycle) {
                    echo "- {$cycle['name']} ({$cycle['status']}) - {$cycle['start_date']} to {$cycle['end_date']}<br>";
                }
            }
        } else {
            echo "✅ Active cycle found: {$current_cycle['name']} (ID: {$current_cycle['id']})<br>";
            echo "- Start: {$current_cycle['start_date']}<br>";
            echo "- End: {$current_cycle['end_date']}<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error checking cycles: " . $e->getMessage() . "<br>";
    }
    
    echo "<h3>Database Connection:</h3>";
    echo "✅ Database connection successful<br>";
    
} catch (Exception $e) {
    echo "❌ General error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='12-week-goals.php'>← Back to Goals</a>";
?>