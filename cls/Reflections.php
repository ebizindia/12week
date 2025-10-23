<?php
namespace eBizIndia;

/**
 * Reflections data access class with encryption support
 */
class Reflections {
    
    /**
     * Get reflection data for a user and month with decryption
     * @param int $userId
     * @param string $monthYear (format: Y-m)
     * @return array|false
     */
    public static function getReflectionData($userId, $monthYear) {
        try {
            // Get main reflection data
            $stmt = PDOConn::query("
                SELECT * FROM reflections_main 
                WHERE user_id = :user_id 
                AND DATE_FORMAT(entry_date, '%Y-%m') = :month_year
            ", [':user_id' => $userId, ':month_year' => $monthYear]);
            $mainReflection = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $reflectionsByCategory = [];
            if ($mainReflection) {
                // Decrypt main reflection data if encrypted
                if ($mainReflection['is_encrypted'] == 1) {
                    $mainReflection['energy_vampire'] = Encryption::decrypt($mainReflection['energy_vampire'], $userId);
                    $mainReflection['reluctant_topic'] = Encryption::decrypt($mainReflection['reluctant_topic'], $userId);
                    $mainReflection['current_issue'] = Encryption::decrypt($mainReflection['current_issue'], $userId);
                    $mainReflection['lookforward_text'] = Encryption::decrypt($mainReflection['lookforward_text'], $userId);
                    $mainReflection['insight_text'] = Encryption::decrypt($mainReflection['insight_text'], $userId);
                }
                
                // Get category reflections
                $stmt = PDOConn::query("
                    SELECT rc.* 
                    FROM reflections_category rc
                    WHERE rc.reflection_id = :reflection_id
                ", [':reflection_id' => $mainReflection['id']]);
                $categoryReflections = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                // Decrypt and organize category reflections
                foreach ($categoryReflections as $reflection) {
                    // Decrypt category reflection data if encrypted
                    if ($reflection['is_encrypted'] == 1) {
                        $reflection['situation'] = Encryption::decrypt($reflection['situation'], $userId);
                        $reflection['significance'] = Encryption::decrypt($reflection['significance'], $userId);
                        $reflection['feelings'] = Encryption::decrypt($reflection['feelings'], $userId);
                    }
                    
                    $reflectionsByCategory[$reflection['category']] = array_merge(
                        $reflection,
                        [
                            'physical_health_score' => $mainReflection['physical_health_score'],
                            'mental_health_score' => $mainReflection['mental_health_score'],
                            'financial_health_score' => $mainReflection['financial_health_score'],
                            'community_score' => $mainReflection['community_score'],
                            'energy_vampire' => $mainReflection['energy_vampire'],
                            'reluctant_topic' => $mainReflection['reluctant_topic'],
                            'current_issue' => $mainReflection['current_issue']
                        ]
                    );
                }
            }

            // Get meetings with decryption
            $stmt = PDOConn::query("
                SELECT * FROM reflection_meetings 
                WHERE user_id = :user_id 
                AND DATE_FORMAT(entry_date, '%Y-%m') = :month_year
            ", [':user_id' => $userId, ':month_year' => $monthYear]);
            $meetings = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Decrypt meeting data if encrypted
            foreach ($meetings as &$meeting) {
                if ($meeting['is_encrypted'] == 1) {
                    $meeting['person_met'] = Encryption::decrypt($meeting['person_met'], $userId);
                }
            }

            // Structure the response to match the expected format in the template
            return [
                'reflections' => $reflectionsByCategory,
                'lookforward' => [
                    'lookforward_text' => $mainReflection['lookforward_text'] ?? null
                ],
                'insight' => [
                    'insight_text' => $mainReflection['insight_text'] ?? null
                ],
                'meetings' => $meetings,
                'main_reflection' => $mainReflection
            ];
        } catch (\Exception $e) {
            ErrorHandler::logError([
                'function' => 'Reflections::getReflectionData',
                'user_id' => $userId,
                'month_year' => $monthYear,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
    
    /**
     * Save reflection data with encryption
     * @param array $data
     * @return bool
     */
    public static function saveReflectionData($data) {
        try {
            // Check if encryption is available
            if (!Encryption::isAvailable()) {
                throw new \Exception('Encryption not available');
            }
            
            $conn = PDOConn::getInstance();
            $conn->beginTransaction();
            
            $userId = $data['user_id'];
            $keyId = 'user_' . $userId . '_' . date('Ym');

            // Encrypt main reflection text fields
            $encryptedMainFields = [];
            $mainTextFields = ['energy_vampire', 'reluctant_topic', 'current_issue', 'lookforward', 'insight'];
            
            foreach ($mainTextFields as $field) {
                $dataKey = $field === 'lookforward' ? 'lookforward' : ($field === 'insight' ? 'insight' : $field);
                if (isset($data[$dataKey]) && !empty($data[$dataKey])) {
                    $encrypted = Encryption::encrypt($data[$dataKey], $userId);
                    if ($encrypted === false) {
                        throw new \Exception("Failed to encrypt {$field}");
                    }
                    $encryptedMainFields[$field] = $encrypted;
                } else {
                    $encryptedMainFields[$field] = '';
                }
            }

            // Save main reflection data with encryption
            $mainParams = [
                ':user_id' => $userId,
                ':entry_date' => $data['entry_date'],
                ':physical_health_score' => !empty($data['health_scores']['physical']) ? $data['health_scores']['physical'] : null,
                ':mental_health_score' => !empty($data['health_scores']['mental']) ? $data['health_scores']['mental'] : null,
                ':financial_health_score' => !empty($data['health_scores']['financial']) ? $data['health_scores']['financial'] : null,
                ':community_score' => !empty($data['health_scores']['community']) ? $data['health_scores']['community'] : null,
                ':energy_vampire' => $encryptedMainFields['energy_vampire'],
                ':reluctant_topic' => $encryptedMainFields['reluctant_topic'],
                ':current_issue' => $encryptedMainFields['current_issue'],
                ':lookforward_text' => $encryptedMainFields['lookforward'],
                ':insight_text' => $encryptedMainFields['insight'],
                ':is_encrypted' => 1,
                ':encryption_key_id' => $keyId,
                ':created_by' => $userId,
                ':created_on' => date('Y-m-d H:i:s'),
                ':created_from_ip' => $_SERVER['REMOTE_ADDR']
            ];

            PDOConn::query("
                INSERT INTO reflections_main 
                (user_id, entry_date, physical_health_score, mental_health_score, 
                financial_health_score, community_score, energy_vampire, 
                reluctant_topic, current_issue, lookforward_text, insight_text,
                is_encrypted, encryption_key_id, created_by, created_on, created_from_ip)
                VALUES 
                (:user_id, :entry_date, :physical_health_score, :mental_health_score,
                :financial_health_score, :community_score, :energy_vampire,
                :reluctant_topic, :current_issue, :lookforward_text, :insight_text,
                :is_encrypted, :encryption_key_id, :created_by, :created_on, :created_from_ip)
                ON DUPLICATE KEY UPDATE
                physical_health_score = VALUES(physical_health_score),
                mental_health_score = VALUES(mental_health_score),
                financial_health_score = VALUES(financial_health_score),
                community_score = VALUES(community_score),
                energy_vampire = VALUES(energy_vampire),
                reluctant_topic = VALUES(reluctant_topic),
                current_issue = VALUES(current_issue),
                lookforward_text = VALUES(lookforward_text),
                insight_text = VALUES(insight_text),
                is_encrypted = 1,
                encryption_key_id = VALUES(encryption_key_id),
                last_updated_by = :created_by,
                last_updated_on = :created_on,
                last_updated_from_ip = :created_from_ip
            ", $mainParams);

            // Get reflection_id (either newly inserted or existing)
            $stmt = PDOConn::query("
                SELECT id FROM reflections_main 
                WHERE user_id = :user_id 
                AND DATE_FORMAT(entry_date, '%Y-%m') = DATE_FORMAT(:entry_date, '%Y-%m')
            ", [
                ':user_id' => $userId,
                ':entry_date' => $data['entry_date']
            ]);
            $reflectionId = $stmt->fetchColumn();

            // Save category reflections with encryption
            foreach ($data['reflections'] as $category => $reflection) {
                // Encrypt category reflection fields
                $encryptedCategoryFields = [];
                $categoryTextFields = ['situation', 'significance', 'feelings'];
                
                foreach ($categoryTextFields as $field) {
                    if (isset($reflection[$field]) && !empty($reflection[$field])) {
                        $encrypted = Encryption::encrypt($reflection[$field], $userId);
                        if ($encrypted === false) {
                            throw new \Exception("Failed to encrypt category {$field}");
                        }
                        $encryptedCategoryFields[$field] = $encrypted;
                    } else {
                        $encryptedCategoryFields[$field] = '';
                    }
                }
                
                $categoryParams = [
                    ':reflection_id' => $reflectionId,
                    ':category' => $category,
                    ':situation' => $encryptedCategoryFields['situation'],
                    ':significance' => $encryptedCategoryFields['significance'],
                    ':feelings' => $encryptedCategoryFields['feelings'],
                    ':is_encrypted' => 1,
                    ':encryption_key_id' => $keyId,
                    ':created_by' => $userId,
                    ':created_on' => date('Y-m-d H:i:s'),
                    ':created_from_ip' => $_SERVER['REMOTE_ADDR']
                ];

                PDOConn::query("
                    INSERT INTO reflections_category 
                    (reflection_id, category, situation, significance, feelings,
                    is_encrypted, encryption_key_id, created_by, created_on, created_from_ip)
                    VALUES 
                    (:reflection_id, :category, :situation, :significance, :feelings,
                    :is_encrypted, :encryption_key_id, :created_by, :created_on, :created_from_ip)
                    ON DUPLICATE KEY UPDATE
                    situation = VALUES(situation),
                    significance = VALUES(significance),
                    feelings = VALUES(feelings),
                    is_encrypted = 1,
                    encryption_key_id = VALUES(encryption_key_id),
                    last_updated_by = :created_by,
                    last_updated_on = :created_on,
                    last_updated_from_ip = :created_from_ip
                ", $categoryParams);
            }

            // Save meetings with encryption
            if (!empty($data['meetings'])) {
                // First delete existing meetings for this month
                PDOConn::query("
                    DELETE FROM reflection_meetings 
                    WHERE user_id = :user_id 
                    AND DATE_FORMAT(entry_date, '%Y-%m') = DATE_FORMAT(:entry_date, '%Y-%m')
                ", [
                    ':user_id' => $userId,
                    ':entry_date' => $data['entry_date']
                ]);

                // Then insert new meetings with encryption
                foreach ($data['meetings'] as $person) {
                    if (!empty(trim($person))) {
                        $encryptedPerson = Encryption::encrypt($person, $userId);
                        if ($encryptedPerson === false) {
                            throw new \Exception("Failed to encrypt meeting person");
                        }
                        
                        PDOConn::query("
                            INSERT INTO reflection_meetings 
                            (user_id, entry_date, person_met, is_encrypted, encryption_key_id,
                            created_by, created_on, created_from_ip)
                            VALUES 
                            (:user_id, :entry_date, :person_met, :is_encrypted, :encryption_key_id,
                            :created_by, :created_on, :created_from_ip)
                        ", [
                            ':user_id' => $userId,
                            ':entry_date' => $data['entry_date'],
                            ':person_met' => $encryptedPerson,
                            ':is_encrypted' => 1,
                            ':encryption_key_id' => $keyId,
                            ':created_by' => $userId,
                            ':created_on' => date('Y-m-d H:i:s'),
                            ':created_from_ip' => $_SERVER['REMOTE_ADDR']
                        ]);
                    }
                }
            }

            $conn->commit();
            return true;
        } catch (\Exception $e) {
            if ($conn && $conn->inTransaction()) {
                $conn->rollBack();
            }
            ErrorHandler::logError([
                'function' => 'Reflections::saveReflectionData',
                'data' => $data,
                'error' => $e->getMessage()
            ], $e);
            return false;
        }
    }
}