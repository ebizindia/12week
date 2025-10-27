<?php
namespace eBizIndia;

/**
 * Goals data access class with encryption support
 */
class Goals {
    
    /**
     * Get goal card data for a user and year
     * @param string $year
     * @param int $userId
     * @return array
     */
    public static function getGoalCard($year, $userId) {
        try {
            $stmt = PDOConn::query("
                SELECT * FROM goal_cards 
                WHERE user_id = :user_id 
                AND year = :year
            ", [':user_id' => $userId, ':year' => $year]);
            
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $goalCard = [];
            
            foreach ($results as $row) {
                // Decrypt data if encrypted
                if ($row['is_encrypted'] == 1) {
                    $row['goal'] = Encryption::decryptShared($row['goal'], 'goals');
                    $row['significance'] = Encryption::decryptShared($row['significance'], 'goals');
                    $row['action_planned'] = Encryption::decryptShared($row['action_planned'], 'goals');
                    $row['mid_review'] = Encryption::decryptShared($row['mid_review'], 'goals');
                    $row['final_review'] = Encryption::decryptShared($row['final_review'], 'goals');
                }
                
                $goalCard[$row['category']] = $row;
            }
            
            return $goalCard;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'Goals::getGoalCard',
                'user_id' => $userId,
                'year' => $year,
                'error' => $e->getMessage()
            ], $e);
            return [];
        }
    }
    
    /**
     * Save/Update goal card with encryption
     * @param array $data
     * @return bool
     */
    public static function saveGoalCard($data) {
        try {
            // Check if encryption is available
            if (!Encryption::isAvailable()) {
                throw new \Exception('Encryption not available');
            }
            
            $userId = $data[':user_id'];
            
            // Encrypt sensitive fields and store in existing columns
            $encryptedData = [];
            $fields = ['goal', 'significance', 'action_planned', 'mid_review', 'final_review'];
            
            foreach ($fields as $field) {
                $fieldKey = ':' . $field;
                if (isset($data[$fieldKey])) {
                    if (!empty($data[$fieldKey])) {
                        $encrypted = Encryption::encryptShared($data[$fieldKey], 'goals');
                        if ($encrypted === false) {
                            throw new \Exception("Failed to encrypt {$field}");
                        }
                        $encryptedData[$fieldKey] = $encrypted;
                    } else {
                        $encryptedData[$fieldKey] = '';
                    }
                }
            }
            
            // Prepare SQL with encrypted data in existing fields
            $stmt = PDOConn::query("
                INSERT INTO goal_cards 
                (user_id, year, category, goal, significance, action_planned, mid_review, final_review, is_encrypted, encryption_key_id)
                VALUES 
                (:user_id, :year, :category, :goal, :significance, :action_planned, :mid_review, :final_review, 1, :encryption_key_id)
                ON DUPLICATE KEY UPDATE
                goal = VALUES(goal),
                significance = VALUES(significance),
                action_planned = VALUES(action_planned),
                mid_review = VALUES(mid_review),
                final_review = VALUES(final_review),
                is_encrypted = 1,
                encryption_key_id = VALUES(encryption_key_id)
            ", [
                ':user_id' => $data[':user_id'],
                ':year' => $data[':year'],
                ':category' => $data[':category'],
                ':goal' => $encryptedData[':goal'] ?? '',
                ':significance' => $encryptedData[':significance'] ?? '',
                ':action_planned' => $encryptedData[':action_planned'] ?? '',
                ':mid_review' => $encryptedData[':mid_review'] ?? '',
                ':final_review' => $encryptedData[':final_review'] ?? '',
                ':encryption_key_id' => 'goals_shared_' . date('Ym')
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'Goals::saveGoalCard',
                'data' => $data,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Get all goal cards for a user (all years)
     * @param int $userId
     * @return array
     */
    public static function getAllUserGoals($userId) {
        try {
            $stmt = PDOConn::query("
                SELECT * FROM goal_cards 
                WHERE user_id = :user_id 
                ORDER BY year DESC, category ASC
            ", [':user_id' => $userId]);
            
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $goals = [];
            
            foreach ($results as $row) {
                // Decrypt data if encrypted
                if ($row['is_encrypted'] == 1) {
                    $row['goal'] = Encryption::decryptShared($row['goal'], 'goals');
                    $row['significance'] = Encryption::decryptShared($row['significance'], 'goals');
                    $row['action_planned'] = Encryption::decryptShared($row['action_planned'], 'goals');
                    $row['mid_review'] = Encryption::decryptShared($row['mid_review'], 'goals');
                    $row['final_review'] = Encryption::decryptShared($row['final_review'], 'goals');
                }
                
                $goals[$row['year']][$row['category']] = $row;
            }
            
            return $goals;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'Goals::getAllUserGoals',
                'user_id' => $userId,
                'error' => $e->getMessage()
            ], $e);
            return [];
        }
    }
    
    /**
     * Get all goal cards from all users (shared access)
     * @param string $year Optional year filter
     * @return array
     */
    public static function getAllGoalsFromAllUsers($year = null) {
        try {
            $sql = "
                SELECT gc.*, u.fname, u.lname, u.email 
                FROM goal_cards gc
                LEFT JOIN users u ON gc.user_id = u.id
            ";
            $params = [];
            
            if ($year) {
                $sql .= " WHERE gc.year = :year";
                $params[':year'] = $year;
            }
            
            $sql .= " ORDER BY gc.year DESC, u.fname ASC, u.lname ASC, gc.category ASC";
            
            $stmt = PDOConn::query($sql, $params);
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $goals = [];
            
            foreach ($results as $row) {
                // Decrypt data if encrypted
                if ($row['is_encrypted'] == 1) {
                    $row['goal'] = Encryption::decryptShared($row['goal'], 'goals');
                    $row['significance'] = Encryption::decryptShared($row['significance'], 'goals');
                    $row['action_planned'] = Encryption::decryptShared($row['action_planned'], 'goals');
                    $row['mid_review'] = Encryption::decryptShared($row['mid_review'], 'goals');
                    $row['final_review'] = Encryption::decryptShared($row['final_review'], 'goals');
                }
                
                // Add user display name
                $row['user_display_name'] = trim($row['fname'] . ' ' . $row['lname']);
                if (empty($row['user_display_name'])) {
                    $row['user_display_name'] = $row['email'] ?? 'Unknown User';
                }
                
                $goals[] = $row;
            }
            
            return $goals;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'Goals::getAllGoalsFromAllUsers',
                'year' => $year,
                'error' => $e->getMessage()
            ], $e);
            return [];
        }
    }
    
    /**
     * Delete goal card
     * @param int $userId
     * @param string $year
     * @param string $category
     * @return bool
     */
    public static function deleteGoalCard($userId, $year, $category = null) {
        try {
            $sql = "DELETE FROM goal_cards WHERE user_id = :user_id AND year = :year";
            $params = [':user_id' => $userId, ':year' => $year];
            
            if ($category) {
                $sql .= " AND category = :category";
                $params[':category'] = $category;
            }
            
            PDOConn::query($sql, $params);
            return true;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'Goals::deleteGoalCard',
                'user_id' => $userId,
                'year' => $year,
                'category' => $category,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
}