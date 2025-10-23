<?php
namespace eBizIndia;

/**
 * TwelveWeekGoals data access class with encryption support
 * Handles CRUD operations for 12-week goals with automatic encryption/decryption
 */
class TwelveWeekGoals {
    
    /**
     * Save a goal with automatic encryption of sensitive fields
     * @param array $data Goal data including title, user_id, cycle_id, category_id
     * @return int|false Goal ID on success, false on failure
     */
    public static function saveGoal($data) {
        try {
            // Validate required fields
            if (empty($data['title'])) {
                throw new \Exception("Goal title is required");
            }
            
            if (empty($data['user_id']) || empty($data['cycle_id']) || empty($data['category_id'])) {
                throw new \Exception("User ID, Cycle ID, and Category ID are required");
            }
            
            // Encrypt title if encryption is available
            $encryptedData = $data;
            if (isset($data['title']) && !empty($data['title']) && Encryption::isAvailable()) {
                $encrypted = Encryption::encryptShared($data['title'], 'twelve_week_goals');
                if ($encrypted === false) {
                    throw new \Exception("Failed to encrypt goal title");
                }
                $encryptedData['title'] = $encrypted;
                $encryptedData['is_encrypted'] = 1;
                $encryptedData['encryption_key_id'] = 'twelve_week_goals_shared_' . date('Ym');
            } else {
                $encryptedData['is_encrypted'] = 0;
                $encryptedData['encryption_key_id'] = null;
            }
            
            $conn = PDOConn::getInstance();
            
            if (isset($data['id']) && !empty($data['id'])) {
                // Update existing goal
                $sql = "UPDATE goals SET 
                        title = :title, 
                        category_id = :category_id,
                        is_encrypted = :is_encrypted,
                        encryption_key_id = :encryption_key_id,
                        updated_at = CURRENT_TIMESTAMP
                        WHERE id = :id AND user_id = :user_id AND cycle_id = :cycle_id";
                
                $params = [
                    ':id' => $data['id'],
                    ':title' => $encryptedData['title'],
                    ':category_id' => $encryptedData['category_id'],
                    ':user_id' => $encryptedData['user_id'],
                    ':cycle_id' => $encryptedData['cycle_id'],
                    ':is_encrypted' => $encryptedData['is_encrypted'],
                    ':encryption_key_id' => $encryptedData['encryption_key_id']
                ];
                
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute($params);
                
                return $result ? $data['id'] : false;
                
            } else {
                // Insert new goal
                $sql = "INSERT INTO goals (user_id, cycle_id, category_id, title, is_encrypted, encryption_key_id) 
                        VALUES (:user_id, :cycle_id, :category_id, :title, :is_encrypted, :encryption_key_id)";
                
                $params = [
                    ':user_id' => $encryptedData['user_id'],
                    ':cycle_id' => $encryptedData['cycle_id'],
                    ':category_id' => $encryptedData['category_id'],
                    ':title' => $encryptedData['title'],
                    ':is_encrypted' => $encryptedData['is_encrypted'],
                    ':encryption_key_id' => $encryptedData['encryption_key_id']
                ];
                
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute($params);
                
                return $result ? $conn->lastInsertId() : false;
            }
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'TwelveWeekGoals::saveGoal',
                'data' => $data,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Get goals for a user and cycle with automatic decryption
     * @param int $userId User ID
     * @param int $cycleId Cycle ID
     * @param int|null $categoryId Optional category filter
     * @return array Array of goals with decrypted data
     */
    public static function getGoals($userId, $cycleId, $categoryId = null) {
        try {
            $sql = "SELECT g.*, c.name as category_name, c.color_code 
                    FROM goals g 
                    JOIN categories c ON g.category_id = c.id 
                    WHERE g.user_id = :user_id AND g.cycle_id = :cycle_id";
            
            $params = [
                ':user_id' => $userId,
                ':cycle_id' => $cycleId
            ];
            
            if ($categoryId !== null) {
                $sql .= " AND g.category_id = :category_id";
                $params[':category_id'] = $categoryId;
            }
            
            $sql .= " ORDER BY c.sort_order, c.name, g.title";
            
            $stmt = PDOConn::query($sql, $params);
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Decrypt data if encrypted
            foreach ($results as &$row) {
                if (isset($row['is_encrypted']) && $row['is_encrypted'] == 1) {
                    $decrypted = Encryption::decryptShared($row['title'], 'twelve_week_goals');
                    if ($decrypted !== false) {
                        $row['title'] = $decrypted;
                    }
                }
            }
            
            return $results;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'TwelveWeekGoals::getGoals',
                'user_id' => $userId,
                'cycle_id' => $cycleId,
                'category_id' => $categoryId,
                'error' => $e->getMessage()
            ], $e);
            return [];
        }
    }
    
    /**
     * Get a single goal by ID with automatic decryption
     * @param int $goalId Goal ID
     * @param int $userId User ID (for security)
     * @return array|false Goal data or false if not found
     */
    public static function getGoal($goalId, $userId) {
        try {
            $sql = "SELECT g.*, c.name as category_name, c.color_code 
                    FROM goals g 
                    JOIN categories c ON g.category_id = c.id 
                    WHERE g.id = :goal_id AND g.user_id = :user_id";
            
            $stmt = PDOConn::query($sql, [
                ':goal_id' => $goalId,
                ':user_id' => $userId
            ]);
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($result) {
                // Decrypt data if encrypted
                if (isset($result['is_encrypted']) && $result['is_encrypted'] == 1) {
                    $decrypted = Encryption::decryptShared($result['title'], 'twelve_week_goals');
                    if ($decrypted !== false) {
                        $result['title'] = $decrypted;
                    }
                }
            }
            
            return $result;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'TwelveWeekGoals::getGoal',
                'goal_id' => $goalId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Delete a goal (and all its tasks)
     * @param int $goalId Goal ID
     * @param int $userId User ID (for security)
     * @return bool Success status
     */
    public static function deleteGoal($goalId, $userId) {
        try {
            $conn = PDOConn::getInstance();
            
            // Start transaction
            $conn->beginTransaction();
            
            // First delete all tasks for this goal
            $delete_tasks_sql = "DELETE FROM tasks WHERE goal_id = :goal_id";
            $stmt = $conn->prepare($delete_tasks_sql);
            $stmt->execute([':goal_id' => $goalId]);
            
            // Then delete the goal (with user verification)
            $delete_goal_sql = "DELETE FROM goals WHERE id = :goal_id AND user_id = :user_id";
            $stmt = $conn->prepare($delete_goal_sql);
            $result = $stmt->execute([
                ':goal_id' => $goalId,
                ':user_id' => $userId
            ]);
            
            if ($stmt->rowCount() > 0) {
                $conn->commit();
                return true;
            } else {
                $conn->rollback();
                return false;
            }
            
        } catch (\Exception $e) {
            if (isset($conn)) {
                $conn->rollback();
            }
            ErrorHandler::logError([
                'function' => 'TwelveWeekGoals::deleteGoal',
                'goal_id' => $goalId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Get goals count for a user and cycle
     * @param int $userId User ID
     * @param int $cycleId Cycle ID
     * @return int Goals count
     */
    public static function getGoalsCount($userId, $cycleId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM goals WHERE user_id = :user_id AND cycle_id = :cycle_id";
            $stmt = PDOConn::query($sql, [
                ':user_id' => $userId,
                ':cycle_id' => $cycleId
            ]);
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ? (int)$result['count'] : 0;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'TwelveWeekGoals::getGoalsCount',
                'user_id' => $userId,
                'cycle_id' => $cycleId,
                'error' => $e->getMessage()
            ], $e);
            return 0;
        }
    }
}