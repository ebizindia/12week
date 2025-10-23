<?php
namespace eBizIndia;

/**
 * TwelveWeekTasks data access class with encryption support
 * Handles CRUD operations for 12-week tasks with automatic encryption/decryption
 */
class TwelveWeekTasks {
    
    /**
     * Save a task with automatic encryption of sensitive fields
     * @param array $data Task data including title, goal_id, week_number
     * @return int|false Task ID on success, false on failure
     */
    public static function saveTask($data) {
        try {
            // Validate required fields
            if (empty($data['title'])) {
                throw new \Exception("Task title is required");
            }
            
            if (empty($data['goal_id']) || empty($data['week_number'])) {
                throw new \Exception("Goal ID and week number are required");
            }
            
            if ($data['week_number'] < 1 || $data['week_number'] > 12) {
                throw new \Exception("Week number must be between 1 and 12");
            }
            
            // Encrypt title if encryption is available
            $encryptedData = $data;
            if (isset($data['title']) && !empty($data['title']) && Encryption::isAvailable()) {
                $encrypted = Encryption::encryptShared($data['title'], 'twelve_week_tasks');
                if ($encrypted === false) {
                    throw new \Exception("Failed to encrypt task title");
                }
                $encryptedData['title'] = $encrypted;
                $encryptedData['is_encrypted'] = 1;
                $encryptedData['encryption_key_id'] = 'twelve_week_tasks_shared_' . date('Ym');
            } else {
                $encryptedData['is_encrypted'] = 0;
                $encryptedData['encryption_key_id'] = null;
            }
            
            $conn = PDOConn::getInstance();
            
            if (isset($data['id']) && !empty($data['id'])) {
                // Update existing task
                $sql = "UPDATE tasks SET 
                        title = :title, 
                        week_number = :week_number,
                        weekly_target = :weekly_target,
                        is_encrypted = :is_encrypted,
                        encryption_key_id = :encryption_key_id,
                        updated_at = CURRENT_TIMESTAMP
                        WHERE id = :id AND goal_id = :goal_id";
                
                $params = [
                    ':id' => $data['id'],
                    ':title' => $encryptedData['title'],
                    ':week_number' => $encryptedData['week_number'],
                    ':weekly_target' => $encryptedData['weekly_target'] ?? 3,
                    ':goal_id' => $encryptedData['goal_id'],
                    ':is_encrypted' => $encryptedData['is_encrypted'],
                    ':encryption_key_id' => $encryptedData['encryption_key_id']
                ];
                
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute($params);
                
                return $result ? $data['id'] : false;
                
            } else {
                // Insert new task
                $sql = "INSERT INTO tasks (goal_id, week_number, title, weekly_target, is_encrypted, encryption_key_id) 
                        VALUES (:goal_id, :week_number, :title, :weekly_target, :is_encrypted, :encryption_key_id)";
                
                $params = [
                    ':goal_id' => $encryptedData['goal_id'],
                    ':week_number' => $encryptedData['week_number'],
                    ':title' => $encryptedData['title'],
                    ':weekly_target' => $encryptedData['weekly_target'] ?? 3,
                    ':is_encrypted' => $encryptedData['is_encrypted'],
                    ':encryption_key_id' => $encryptedData['encryption_key_id']
                ];
                
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute($params);
                
                return $result ? $conn->lastInsertId() : false;
            }
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'TwelveWeekTasks::saveTask',
                'data' => $data,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Get tasks for a goal with automatic decryption
     * @param int $goalId Goal ID
     * @param int|null $weekNumber Optional week filter
     * @return array Array of tasks with decrypted data
     */
    public static function getTasks($goalId, $weekNumber = null) {
        try {
            $sql = "SELECT * FROM tasks WHERE goal_id = :goal_id";
            $params = [':goal_id' => $goalId];
            
            if ($weekNumber !== null) {
                $sql .= " AND week_number = :week_number";
                $params[':week_number'] = $weekNumber;
            }
            
            $sql .= " ORDER BY week_number, created_at";
            
            $stmt = PDOConn::query($sql, $params);
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Decrypt data if encrypted
            foreach ($results as &$row) {
                if (isset($row['is_encrypted']) && $row['is_encrypted'] == 1) {
                    $decrypted = Encryption::decryptShared($row['title'], 'twelve_week_tasks');
                    if ($decrypted !== false) {
                        $row['title'] = $decrypted;
                    }
                }
            }
            
            return $results;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'TwelveWeekTasks::getTasks',
                'goal_id' => $goalId,
                'week_number' => $weekNumber,
                'error' => $e->getMessage()
            ], $e);
            return [];
        }
    }
    
    /**
     * Get tasks for a specific week across all goals for a user
     * @param int $userId User ID
     * @param int $cycleId Cycle ID
     * @param int $weekNumber Week number (1-12)
     * @return array Array of tasks organized by category and goal
     */
    public static function getTasksForWeek($userId, $cycleId, $weekNumber) {
        try {
            $sql = "SELECT t.*, g.title as goal_title, g.id as goal_id, 
                           c.name as category_name, c.color_code, c.sort_order as category_sort
                    FROM tasks t 
                    JOIN goals g ON t.goal_id = g.id 
                    JOIN categories c ON g.category_id = c.id 
                    WHERE g.user_id = :user_id AND g.cycle_id = :cycle_id AND t.week_number = :week_number
                    ORDER BY c.sort_order, c.name, g.title, t.created_at";
            
            $stmt = PDOConn::query($sql, [
                ':user_id' => $userId,
                ':cycle_id' => $cycleId,
                ':week_number' => $weekNumber
            ]);
            
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Decrypt data if encrypted
            foreach ($results as &$row) {
                if (isset($row['is_encrypted']) && $row['is_encrypted'] == 1) {
                    $decrypted = Encryption::decryptShared($row['title'], 'twelve_week_tasks');
                    if ($decrypted !== false) {
                        $row['title'] = $decrypted;
                    }
                }
            }
            
            return $results;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'TwelveWeekTasks::getTasksForWeek',
                'user_id' => $userId,
                'cycle_id' => $cycleId,
                'week_number' => $weekNumber,
                'error' => $e->getMessage()
            ], $e);
            return [];
        }
    }
    
    /**
     * Update task progress for a specific day
     * @param int $taskId Task ID
     * @param string $day Day of week (mon, tue, wed, thu, fri, sat, sun)
     * @param int $completed Completion status (0 or 1)
     * @param int $userId User ID (for security)
     * @return bool Success status
     */
    public static function updateTaskProgress($taskId, $day, $completed, $userId) {
        try {
            // Validate day
            $validDays = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
            if (!in_array($day, $validDays)) {
                throw new \Exception("Invalid day: $day");
            }
            
            // Validate completion status
            $completed = $completed ? 1 : 0;
            
            // Verify task belongs to user (security check)
            $check_sql = "SELECT t.id FROM tasks t 
                         JOIN goals g ON t.goal_id = g.id 
                         WHERE t.id = :task_id AND g.user_id = :user_id";
            
            $check_stmt = PDOConn::query($check_sql, [
                ':task_id' => $taskId,
                ':user_id' => $userId
            ]);
            
            if (!$check_stmt->fetch()) {
                throw new \Exception("Task not found or access denied");
            }
            
            // Update progress
            $sql = "UPDATE tasks SET $day = :completed, updated_at = CURRENT_TIMESTAMP WHERE id = :task_id";
            
            $stmt = PDOConn::query($sql, [
                ':completed' => $completed,
                ':task_id' => $taskId
            ]);
            
            return $stmt->rowCount() > 0;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'TwelveWeekTasks::updateTaskProgress',
                'task_id' => $taskId,
                'day' => $day,
                'completed' => $completed,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Get a single task by ID with automatic decryption
     * @param int $taskId Task ID
     * @param int $userId User ID (for security)
     * @return array|false Task data or false if not found
     */
    public static function getTask($taskId, $userId) {
        try {
            $sql = "SELECT t.*, g.title as goal_title, g.user_id 
                    FROM tasks t 
                    JOIN goals g ON t.goal_id = g.id 
                    WHERE t.id = :task_id AND g.user_id = :user_id";
            
            $stmt = PDOConn::query($sql, [
                ':task_id' => $taskId,
                ':user_id' => $userId
            ]);
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($result) {
                // Decrypt data if encrypted
                if (isset($result['is_encrypted']) && $result['is_encrypted'] == 1) {
                    $decrypted = Encryption::decryptShared($result['title'], 'twelve_week_tasks');
                    if ($decrypted !== false) {
                        $result['title'] = $decrypted;
                    }
                }
            }
            
            return $result;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'TwelveWeekTasks::getTask',
                'task_id' => $taskId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Delete a task
     * @param int $taskId Task ID
     * @param int $userId User ID (for security)
     * @return bool Success status
     */
    public static function deleteTask($taskId, $userId) {
        try {
            // Verify task belongs to user (security check)
            $check_sql = "SELECT t.id FROM tasks t 
                         JOIN goals g ON t.goal_id = g.id 
                         WHERE t.id = :task_id AND g.user_id = :user_id";
            
            $check_stmt = PDOConn::query($check_sql, [
                ':task_id' => $taskId,
                ':user_id' => $userId
            ]);
            
            if (!$check_stmt->fetch()) {
                return false; // Task not found or access denied
            }
            
            // Delete the task
            $sql = "DELETE FROM tasks WHERE id = :task_id";
            $stmt = PDOConn::query($sql, [':task_id' => $taskId]);
            
            return $stmt->rowCount() > 0;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'TwelveWeekTasks::deleteTask',
                'task_id' => $taskId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Get task count for a goal
     * @param int $goalId Goal ID
     * @return int Task count
     */
    public static function getTaskCount($goalId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM tasks WHERE goal_id = :goal_id";
            $stmt = PDOConn::query($sql, [':goal_id' => $goalId]);
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ? (int)$result['count'] : 0;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'TwelveWeekTasks::getTaskCount',
                'goal_id' => $goalId,
                'error' => $e->getMessage()
            ], $e);
            return 0;
        }
    }
}