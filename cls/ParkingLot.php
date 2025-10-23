<?php
namespace eBizIndia;

/**
 * ParkingLot data access class with encryption support
 */
class ParkingLot {
    
    /**
     * Get all parking lot entries with decryption
     * @return array
     */
    public static function getAllEntries() {
        try {
            $stmt = PDOConn::query("
                SELECT * FROM parking_lot 
                ORDER BY `date` DESC, `name` ASC
            ");
            
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($results as &$row) {
                // Decrypt data if encrypted
                if (isset($row['is_encrypted']) && $row['is_encrypted'] == 1) {
                    $row['description'] = Encryption::decryptShared($row['description'], 'parking_lot');
                    if (isset($row['action'])) {
                        $row['action'] = Encryption::decryptShared($row['action'], 'parking_lot');
                    }
                }
            }
            
            return $results;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'ParkingLot::getAllEntries',
                'error' => $e->getMessage()
            ], $e);
            return [];
        }
    }
    
    /**
     * Get parking lot entries by date
     * @param string $date
     * @return array
     */
    public static function getEntriesByDate($date) {
        try {
            $stmt = PDOConn::query("
                SELECT * FROM parking_lot 
                WHERE `date` = :date
                ORDER BY `name` ASC
            ", [':date' => $date]);
            
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($results as &$row) {
                // Decrypt data if encrypted
                if (isset($row['is_encrypted']) && $row['is_encrypted'] == 1) {
                    $row['description'] = Encryption::decryptShared($row['description'], 'parking_lot');
                    if (isset($row['action'])) {
                        $row['action'] = Encryption::decryptShared($row['action'], 'parking_lot');
                    }
                }
            }
            
            return $results;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'ParkingLot::getEntriesByDate',
                'date' => $date,
                'error' => $e->getMessage()
            ], $e);
            return [];
        }
    }
    
    /**
     * Save parking lot entry with encryption
     * @param array $data
     * @return bool
     */
    public static function saveEntry($data) {
        try {
            // Check if encryption is available and we have sensitive data
            $sensitiveFields = ['description', 'action'];
            $hasSensitiveData = false;
            foreach ($sensitiveFields as $field) {
                if (isset($data[$field]) && !empty($data[$field])) {
                    $hasSensitiveData = true;
                    break;
                }
            }
            
            if ($hasSensitiveData && Encryption::isAvailable()) {
                // Encrypt sensitive fields
                $encryptedData = $data;
                foreach ($sensitiveFields as $field) {
                    if (isset($data[$field]) && !empty($data[$field])) {
                        $encrypted = Encryption::encryptShared($data[$field], 'parking_lot');
                        if ($encrypted === false) {
                            throw new \Exception("Failed to encrypt {$field}");
                        }
                        $encryptedData[$field] = $encrypted;
                    }
                }
                
                // Add encryption metadata
                $encryptedData['is_encrypted'] = 1;
                $encryptedData['encryption_key_id'] = 'parking_lot_shared_' . date('Ym');
                $data = $encryptedData;
            }
            
            // Prepare SQL parameters
            $params = [
                ':date' => $data['date'],
                ':name' => $data['name'],
                ':description' => $data['description'] ?? '',
                ':submitted_by' => $data['submitted_by'],
                ':submitted_date' => $data['submitted_date']
            ];
            
            // Add optional fields if they exist
            if (isset($data['action'])) {
                $params[':action'] = $data['action'];
            }
            if (isset($data['is_encrypted'])) {
                $params[':is_encrypted'] = $data['is_encrypted'];
            }
            if (isset($data['encryption_key_id'])) {
                $params[':encryption_key_id'] = $data['encryption_key_id'];
            }
            
            // Build SQL based on available fields
            $fields = ['date', 'name', 'description', 'submitted_by', 'submitted_date'];
            $values = [':date', ':name', ':description', ':submitted_by', ':submitted_date'];
            $updates = ['description = VALUES(description)', 'submitted_by = VALUES(submitted_by)', 'submitted_date = VALUES(submitted_date)'];
            
            if (isset($data['action'])) {
                $fields[] = 'action';
                $values[] = ':action';
                $updates[] = 'action = VALUES(action)';
            }
            if (isset($data['is_encrypted'])) {
                $fields[] = 'is_encrypted';
                $values[] = ':is_encrypted';
                $updates[] = 'is_encrypted = VALUES(is_encrypted)';
            }
            if (isset($data['encryption_key_id'])) {
                $fields[] = 'encryption_key_id';
                $values[] = ':encryption_key_id';
                $updates[] = 'encryption_key_id = VALUES(encryption_key_id)';
            }
            
            $sql = "INSERT INTO parking_lot (`" . implode('`, `', $fields) . "`) 
                    VALUES (" . implode(', ', $values) . ")
                    ON DUPLICATE KEY UPDATE " . implode(', ', $updates);
            
            PDOConn::query($sql, $params);
            return true;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'ParkingLot::saveEntry',
                'data' => $data,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Delete parking lot entry
     * @param int $id
     * @return bool
     */
    public static function deleteEntry($id) {
        try {
            PDOConn::query("DELETE FROM parking_lot WHERE id = :id", [':id' => $id]);
            return true;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'ParkingLot::deleteEntry',
                'id' => $id,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Get parking lot entry by ID with decryption
     * @param int $id
     * @return array|false
     */
    public static function getEntryById($id) {
        try {
            $stmt = PDOConn::query("
                SELECT * FROM parking_lot 
                WHERE id = :id
            ", [':id' => $id]);
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($result) {
                // Decrypt data if encrypted
                if (isset($result['is_encrypted']) && $result['is_encrypted'] == 1) {
                    $result['description'] = Encryption::decryptShared($result['description'], 'parking_lot');
                    if (isset($result['action'])) {
                        $result['action'] = Encryption::decryptShared($result['action'], 'parking_lot');
                    }
                }
            }
            
            return $result;
            
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'ParkingLot::getEntryById',
                'id' => $id,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
}