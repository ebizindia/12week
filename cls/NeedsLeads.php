<?php
namespace eBizIndia;
use \Exception;
use \PDO;

class NeedsLeads {
    
    protected $db_conn;
    private $last_mysql_error_code;
    private $last_sqlstate_code;
    
    public function __construct($db_conn = null) {
        $this->db_conn = $db_conn ?: \eBizIndia\PDOConn::getInstance();
        $this->last_mysql_error_code = $this->last_sqlstate_code = '';
    }
    
    public function __get($name) {
        if (in_array($name, ['last_mysql_error_code', 'last_sqlstate_code'])) {
            return $this->{$name};
        }
    }
    
    /**
     * Add a new need with encryption
     */
    public function addNeed($data) {
        $this->last_mysql_error_code = $this->last_sqlstate_code = '';
        
        try {
            // Check if encryption is available
            $useEncryption = \eBizIndia\Encryption::isAvailable();
            
            $title = $data['title'] ?? 'Business Requirement';
            $description = $data['description'];
            
            // Encrypt sensitive data if encryption is available
            if ($useEncryption) {
                $encryptedTitle = \eBizIndia\Encryption::encryptShared($title, 'needs_leads');
                $encryptedDescription = \eBizIndia\Encryption::encryptShared($description, 'needs_leads');
                
                if ($encryptedTitle === false || $encryptedDescription === false) {
                    // Fallback to unencrypted if encryption fails
                    $useEncryption = false;
                }
            }
            
            $sql = "INSERT INTO needs (user_id, title, description, is_encrypted, created_from) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db_conn->prepare($sql);
            
            $result = $stmt->execute([
                $data['user_id'],
                $useEncryption ? $encryptedTitle : $title,
                $useEncryption ? $encryptedDescription : $description,
                $useEncryption ? 1 : 0,
                $data['created_from'] ?? \eBizIndia\getRemoteIP()
            ]);
            
            if ($result) {
                return $this->db_conn->lastInsertId();
            }
            
            return false;
            
        } catch (Exception $e) {
            $this->logError('addNeed', $e, $data);
            return false;
        }
    }
    
    /**
     * Add a new lead/response with encryption
     */
    public function addLead($data) {
        $this->last_mysql_error_code = $this->last_sqlstate_code = '';
        
        try {
            // Check if encryption is available
            $useEncryption = \eBizIndia\Encryption::isAvailable();
            
            $response = $data['response'];
            
            // Encrypt sensitive data if encryption is available
            if ($useEncryption) {
                $encryptedResponse = \eBizIndia\Encryption::encryptShared($response, 'needs_leads');
                
                if ($encryptedResponse === false) {
                    // Fallback to unencrypted if encryption fails
                    $useEncryption = false;
                }
            }
            
            $sql = "INSERT INTO leads (need_id, user_id, response, is_encrypted, created_from) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db_conn->prepare($sql);
            
            $result = $stmt->execute([
                $data['need_id'],
                $data['user_id'],
                $useEncryption ? $encryptedResponse : $response,
                $useEncryption ? 1 : 0,
                $data['created_from'] ?? \eBizIndia\getRemoteIP()
            ]);
            
            if ($result) {
                return $this->db_conn->lastInsertId();
            }
            
            return false;
            
        } catch (Exception $e) {
            $this->logError('addLead', $e, $data);
            return false;
        }
    }
    
    /**
     * Get needs with optional filters
     */
    public function getNeeds($options = []) {
        try {
            $sql = "SELECT n.id, n.title, n.description, n.status, n.created_on, n.user_id, n.is_encrypted,
                           u.fname, u.lname, u.membership_no, u.active as user_active,
                           (SELECT COUNT(*) FROM leads l WHERE l.need_id = n.id) as leads_count
                    FROM needs n 
                    JOIN members u ON n.user_id = u.id";
            
            $where_conditions = [];
            $params = [];
            
            // Default filter for active needs
            if (!isset($options['include_inactive'])) {
                $where_conditions[] = "n.status = 'active'";
            }
            
            // Filter by user role (regular users see only active members)
            if (isset($options['user_role']) && $options['user_role'] === 'REGULAR') {
                $where_conditions[] = "u.active = 'y'";
            }
            
            // Filter by user ID
            if (isset($options['user_id'])) {
                $where_conditions[] = "n.user_id = ?";
                $params[] = $options['user_id'];
            }
            
            // Filter by status
            if (isset($options['status'])) {
                $where_conditions[] = "n.status = ?";
                $params[] = $options['status'];
            }
            
            if (!empty($where_conditions)) {
                $sql .= " WHERE " . implode(" AND ", $where_conditions);
            }
            
            // Order by
            $sql .= " ORDER BY n.created_on DESC";
            
            // Limit
            if (isset($options['limit'])) {
                $sql .= " LIMIT " . (int)$options['limit'];
            }
            
            $stmt = $this->db_conn->prepare($sql);
            $stmt->execute($params);
            
            $needs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Decrypt encrypted needs
            foreach ($needs as &$need) {
                if ($need['is_encrypted'] == 1) {
                    $need['title'] = \eBizIndia\Encryption::decryptShared($need['title'], 'needs_leads');
                    $need['description'] = \eBizIndia\Encryption::decryptShared($need['description'], 'needs_leads');
                    
                    // Handle decryption failures gracefully
                    if ($need['title'] === false) $need['title'] = '[Encrypted - Unable to decrypt]';
                    if ($need['description'] === false) $need['description'] = '[Encrypted - Unable to decrypt]';
                }
            }
            
            return $needs;
            
        } catch (Exception $e) {
            $this->logError('getNeeds', $e, $options);
            return [];
        }
    }
    
    /**
     * Get leads for a specific need
     */
    public function getLeadsForNeed($need_id, $user_role = 'REGULAR') {
        try {
            $sql = "SELECT l.id, l.response, l.created_on, l.user_id, l.is_encrypted,
                           u.fname, u.lname, u.membership_no, u.active as user_active
                    FROM leads l 
                    JOIN members u ON l.user_id = u.id 
                    WHERE l.need_id = ?";
            
            $params = [$need_id];
            
            // Regular users can only see leads from active members
            if ($user_role === 'REGULAR') {
                $sql .= " AND u.active = 'y'";
            }
            
            $sql .= " ORDER BY l.created_on DESC";
            
            $stmt = $this->db_conn->prepare($sql);
            $stmt->execute($params);
            
            $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Decrypt encrypted leads
            foreach ($leads as &$lead) {
                if ($lead['is_encrypted'] == 1) {
                    $lead['response'] = \eBizIndia\Encryption::decryptShared($lead['response'], 'needs_leads');
                    
                    // Handle decryption failures gracefully
                    if ($lead['response'] === false) $lead['response'] = '[Encrypted - Unable to decrypt]';
                }
            }
            
            return $leads;
            
        } catch (Exception $e) {
            $this->logError('getLeadsForNeed', $e, ['need_id' => $need_id, 'user_role' => $user_role]);
            return [];
        }
    }
    
    /**
     * Get needs with their leads
     */
    public function getNeedsWithLeads($options = []) {
        $needs = $this->getNeeds($options);
        
        foreach ($needs as &$need) {
            $need['leads'] = $this->getLeadsForNeed($need['id'], $options['user_role'] ?? 'REGULAR');
        }
        
        return $needs;
    }
    
    /**
     * Get statistics
     */
    public function getStatistics($user_role = 'REGULAR') {
        try {
            $stats = [];
            
            // Active needs count
            $sql = "SELECT COUNT(*) as count FROM needs n";
            if ($user_role === 'REGULAR') {
                $sql .= " JOIN members u ON n.user_id = u.id WHERE n.status = 'active' AND u.active = 'y'";
            } else {
                $sql .= " WHERE n.status = 'active'";
            }
            
            $stmt = $this->db_conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['active_needs'] = $result['count'] ?? 0;
            
            // Total leads count (only for active needs)
            $sql = "SELECT COUNT(*) as count FROM leads l 
                    JOIN needs n ON l.need_id = n.id 
                    WHERE n.status = 'active'";
            if ($user_role === 'REGULAR') {
                $sql .= " AND EXISTS (SELECT 1 FROM members u WHERE u.id = l.user_id AND u.active = 'y')";
            }
            
            $stmt = $this->db_conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_leads'] = $result['count'] ?? 0;
            
            // Active members count (only for active needs and their leads)
            $sql = "SELECT COUNT(DISTINCT user_id) as count FROM (
                        SELECT user_id FROM needs n WHERE n.status = 'active'";
            if ($user_role === 'REGULAR') {
                $sql .= " AND EXISTS (SELECT 1 FROM members u WHERE u.id = n.user_id AND u.active = 'y')";
            }
            $sql .= " UNION 
                        SELECT l.user_id FROM leads l 
                        JOIN needs n ON l.need_id = n.id 
                        WHERE n.status = 'active'";
            if ($user_role === 'REGULAR') {
                $sql .= " AND EXISTS (SELECT 1 FROM members u WHERE u.id = l.user_id AND u.active = 'y')";
            }
            $sql .= ") as active_users";
            
            $stmt = $this->db_conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['active_members'] = $result['count'] ?? 0;
            
            return $stats;
            
        } catch (Exception $e) {
            $this->logError('getStatistics', $e, ['user_role' => $user_role]);
            return ['active_needs' => 0, 'total_leads' => 0, 'active_members' => 0];
        }
    }
    
    /**
     * Update need status
     */
    public function updateNeedStatus($need_id, $status, $user_id = null) {
        $this->last_mysql_error_code = $this->last_sqlstate_code = '';
        
        try {
            if ($user_id !== null) {
                // Ensure only the owner can update
                $sql = "UPDATE needs SET status = ?, updated_on = NOW(), updated_from = ?, updated_by = ? WHERE id = ? AND user_id = ?";
                $params = [$status, \eBizIndia\getRemoteIP(), $user_id, $need_id, $user_id];
            } else {
                // Admin can update any need
                $sql = "UPDATE needs SET status = ?, updated_on = NOW(), updated_from = ?, updated_by = ? WHERE id = ?";
                $params = [$status, \eBizIndia\getRemoteIP(), $user_id, $need_id];
            }
            
            $stmt = $this->db_conn->prepare($sql);
            $result = $stmt->execute($params);
            
            // Check if any rows were affected
            if ($result && $stmt->rowCount() > 0) {
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            $this->logError('updateNeedStatus', $e, ['need_id' => $need_id, 'status' => $status, 'user_id' => $user_id]);
            return false;
        }
    }
    
    /**
     * Archive a need (mark as closed)
     */
    public function archiveNeed($need_id, $user_id) {
        return $this->updateNeedStatus($need_id, 'closed', $user_id);
    }
    
    /**
     * Delete a need (and its leads due to CASCADE)
     */
    public function deleteNeed($need_id, $user_id = null) {
        $this->last_mysql_error_code = $this->last_sqlstate_code = '';
        
        try {
            $sql = "DELETE FROM needs WHERE id = ?";
            $params = [$need_id];
            
            // If user_id is provided, ensure only the owner can delete
            if ($user_id !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $user_id;
            }
            
            $stmt = $this->db_conn->prepare($sql);
            return $stmt->execute($params);
            
        } catch (Exception $e) {
            $this->logError('deleteNeed', $e, ['need_id' => $need_id, 'user_id' => $user_id]);
            return false;
        }
    }
    
    /**
     * Delete a lead
     */
    public function deleteLead($lead_id, $user_id = null) {
        $this->last_mysql_error_code = $this->last_sqlstate_code = '';
        
        try {
            $sql = "DELETE FROM leads WHERE id = ?";
            $params = [$lead_id];
            
            // If user_id is provided, ensure only the owner can delete
            if ($user_id !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $user_id;
            }
            
            $stmt = $this->db_conn->prepare($sql);
            return $stmt->execute($params);
            
        } catch (Exception $e) {
            $this->logError('deleteLead', $e, ['lead_id' => $lead_id, 'user_id' => $user_id]);
            return false;
        }
    }
    
    /**
     * Get active members for dropdown
     */
    public function getActiveMembers() {
        try {
            $sql = "SELECT id, fname, lname, membership_no FROM members WHERE active = 'y' ORDER BY fname, lname";
            $stmt = $this->db_conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $this->logError('getActiveMembers', $e, []);
            return [];
        }
    }
    
    /**
     * Log errors
     */
    private function logError($method, $exception, $data = []) {
        $error_details = [
            'at' => date('Y-m-d H:i:s'),
            'class' => __CLASS__,
            'method' => $method,
            'exception_msg' => $exception->getMessage(),
            'data' => $data
        ];
        
        if ($this->db_conn) {
            $error_details['mysql_error'] = $this->db_conn->errorInfo();
            $this->last_mysql_error_code = $error_details['mysql_error'][1] ?? '';
            $this->last_sqlstate_code = $error_details['mysql_error'][0] ?? '';
        }
        
        \eBizIndia\logErrorInFile(time(), $_SERVER['REQUEST_URI'] ?? '', json_encode($error_details));
    }
}
?>