<?php
namespace eBizIndia;

class Gamification {
    
    /**
     * Update user statistics and check for new achievements
     */
    public static function updateUserStats($user_id, $cycle_id, $activity_type, $value = 1) {
        try {
            $conn = \eBizIndia\PDOConn::getInstance();
            
            // Initialize user stats if not exists
            $init_sql = "INSERT IGNORE INTO user_stats (user_id, cycle_id) VALUES (:user_id, :cycle_id)";
            $init_stmt = $conn->prepare($init_sql);
            $init_stmt->execute([':user_id' => $user_id, ':cycle_id' => $cycle_id]);
            
            // Update specific stat based on activity type
            switch ($activity_type) {
                case 'task_completed':
                    $update_sql = "UPDATE user_stats 
                                   SET total_tasks_completed = total_tasks_completed + :value,
                                       last_activity_date = CURDATE()
                                   WHERE user_id = :user_id AND cycle_id = :cycle_id";
                    break;
                    
                case 'goal_created':
                    $update_sql = "UPDATE user_stats 
                                   SET total_goals_created = total_goals_created + :value,
                                       last_activity_date = CURDATE()
                                   WHERE user_id = :user_id AND cycle_id = :cycle_id";
                    break;
                    
                case 'task_planned':
                    $update_sql = "UPDATE user_stats 
                                   SET total_tasks_planned = total_tasks_planned + :value,
                                       last_activity_date = CURDATE()
                                   WHERE user_id = :user_id AND cycle_id = :cycle_id";
                    break;
                    
                case 'week_completed':
                    $update_sql = "UPDATE user_stats 
                                   SET weeks_completed = :value,
                                       last_activity_date = CURDATE()
                                   WHERE user_id = :user_id AND cycle_id = :cycle_id";
                    break;
                    
                case 'perfect_week':
                    $update_sql = "UPDATE user_stats 
                                   SET perfect_weeks = perfect_weeks + :value,
                                       last_activity_date = CURDATE()
                                   WHERE user_id = :user_id AND cycle_id = :cycle_id";
                    break;
                    
                default:
                    return false;
            }
            
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->execute([
                ':user_id' => $user_id,
                ':cycle_id' => $cycle_id,
                ':value' => $value
            ]);
            
            // Update daily activity
            self::updateDailyActivity($user_id, $cycle_id, $activity_type, $value);
            
            // Update streak
            self::updateStreak($user_id, $cycle_id);
            
            // Check for new achievements
            self::checkAchievements($user_id, $cycle_id);
            
            // Update leaderboard
            self::updateLeaderboard($user_id, $cycle_id);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Gamification Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update daily activity tracking
     */
    private static function updateDailyActivity($user_id, $cycle_id, $activity_type, $value) {
        $conn = \eBizIndia\PDOConn::getInstance();
        
        $field_map = [
            'task_completed' => 'tasks_completed',
            'goal_created' => 'goals_created',
            'task_planned' => 'tasks_planned'
        ];
        
        if (!isset($field_map[$activity_type])) {
            return;
        }
        
        $field = $field_map[$activity_type];
        
        $sql = "INSERT INTO daily_activity (user_id, cycle_id, activity_date, {$field}, was_active)
                VALUES (:user_id, :cycle_id, CURDATE(), :value, 1)
                ON DUPLICATE KEY UPDATE 
                    {$field} = {$field} + :value,
                    was_active = 1";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':cycle_id' => $cycle_id,
            ':value' => $value
        ]);
    }
    
    /**
     * Update user's current streak
     */
    private static function updateStreak($user_id, $cycle_id) {
        $conn = \eBizIndia\PDOConn::getInstance();
        
        // Get consecutive days of activity
        $streak_sql = "SELECT COUNT(*) as streak_days
                       FROM (
                           SELECT activity_date
                           FROM daily_activity
                           WHERE user_id = :user_id AND cycle_id = :cycle_id AND was_active = 1
                           AND activity_date >= (
                               SELECT DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                           )
                           ORDER BY activity_date DESC
                       ) recent_activity
                       WHERE activity_date >= (
                           SELECT CASE 
                               WHEN COUNT(*) = 0 THEN CURDATE()
                               ELSE DATE_SUB(CURDATE(), INTERVAL (COUNT(*) - 1) DAY)
                           END
                           FROM (
                               SELECT activity_date,
                                      ROW_NUMBER() OVER (ORDER BY activity_date DESC) as rn,
                                      DATE_SUB(activity_date, INTERVAL ROW_NUMBER() OVER (ORDER BY activity_date DESC) - 1 DAY) as grp
                               FROM daily_activity
                               WHERE user_id = :user_id AND cycle_id = :cycle_id AND was_active = 1
                               AND activity_date <= CURDATE()
                               ORDER BY activity_date DESC
                           ) grouped
                           WHERE grp = (
                               SELECT DATE_SUB(activity_date, INTERVAL ROW_NUMBER() OVER (ORDER BY activity_date DESC) - 1 DAY)
                               FROM daily_activity
                               WHERE user_id = :user_id AND cycle_id = :cycle_id AND was_active = 1
                               AND activity_date <= CURDATE()
                               ORDER BY activity_date DESC
                               LIMIT 1
                           )
                       )";
        
        // Simplified streak calculation - count consecutive days from today backwards
        $simple_streak_sql = "SELECT 
                                CASE 
                                    WHEN MAX(activity_date) = CURDATE() THEN
                                        (SELECT COUNT(*)
                                         FROM daily_activity da2
                                         WHERE da2.user_id = :user_id 
                                         AND da2.cycle_id = :cycle_id 
                                         AND da2.was_active = 1
                                         AND da2.activity_date >= (
                                             SELECT COALESCE(MIN(missing_date), CURDATE())
                                             FROM (
                                                 SELECT DATE_SUB(CURDATE(), INTERVAL seq.n DAY) as missing_date
                                                 FROM (SELECT 0 as n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30) seq
                                                 WHERE DATE_SUB(CURDATE(), INTERVAL seq.n DAY) NOT IN (
                                                     SELECT activity_date 
                                                     FROM daily_activity 
                                                     WHERE user_id = :user_id AND cycle_id = :cycle_id AND was_active = 1
                                                 )
                                                 ORDER BY missing_date DESC
                                                 LIMIT 1
                                             ) missing
                                         ))
                                    ELSE 0
                                END as current_streak
                              FROM daily_activity
                              WHERE user_id = :user_id AND cycle_id = :cycle_id AND was_active = 1";
        
        // Even simpler - just count recent consecutive days
        $basic_streak_sql = "SELECT 
                               COALESCE(
                                   (SELECT COUNT(*)
                                    FROM daily_activity
                                    WHERE user_id = :user_id 
                                    AND cycle_id = :cycle_id 
                                    AND was_active = 1
                                    AND activity_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                                    AND activity_date <= CURDATE()), 0
                               ) as current_streak";
        
        $streak_stmt = $conn->prepare($basic_streak_sql);
        $streak_stmt->execute([':user_id' => $user_id, ':cycle_id' => $cycle_id]);
        $streak_result = $streak_stmt->fetch(\PDO::FETCH_ASSOC);
        
        $current_streak = $streak_result['current_streak'] ?? 0;
        
        // Update user stats with streak
        $update_streak_sql = "UPDATE user_stats 
                              SET current_streak = :current_streak,
                                  longest_streak = GREATEST(longest_streak, :current_streak)
                              WHERE user_id = :user_id AND cycle_id = :cycle_id";
        
        $update_streak_stmt = $conn->prepare($update_streak_sql);
        $update_streak_stmt->execute([
            ':user_id' => $user_id,
            ':cycle_id' => $cycle_id,
            ':current_streak' => $current_streak
        ]);
    }
    
    /**
     * Check and award achievements
     */
    private static function checkAchievements($user_id, $cycle_id) {
        $conn = \eBizIndia\PDOConn::getInstance();
        
        // Call stored procedure to check achievements
        $check_sql = "CALL CheckUserAchievements(:user_id, :cycle_id)";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([':user_id' => $user_id, ':cycle_id' => $cycle_id]);
    }
    
    /**
     * Update leaderboard stats
     */
    private static function updateLeaderboard($user_id, $cycle_id) {
        $conn = \eBizIndia\PDOConn::getInstance();
        
        // Calculate user's current stats
        $stats_sql = "SELECT 
                        us.total_points,
                        us.current_streak,
                        COUNT(ua.id) as achievements_count,
                        COALESCE(AVG(ws.score_percentage), 0) as completion_rate
                      FROM user_stats us
                      LEFT JOIN user_achievements ua ON us.user_id = ua.user_id
                      LEFT JOIN weekly_scores ws ON us.user_id = ws.user_id AND us.cycle_id = ws.cycle_id
                      WHERE us.user_id = :user_id AND us.cycle_id = :cycle_id
                      GROUP BY us.user_id, us.cycle_id";
        
        $stats_stmt = $conn->prepare($stats_sql);
        $stats_stmt->execute([':user_id' => $user_id, ':cycle_id' => $cycle_id]);
        $stats = $stats_stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($stats) {
            // Update leaderboard entry
            $leaderboard_sql = "INSERT INTO leaderboard_stats 
                                (user_id, cycle_id, total_points, completion_rate, current_streak, achievements_count)
                                VALUES (:user_id, :cycle_id, :total_points, :completion_rate, :current_streak, :achievements_count)
                                ON DUPLICATE KEY UPDATE
                                    total_points = VALUES(total_points),
                                    completion_rate = VALUES(completion_rate),
                                    current_streak = VALUES(current_streak),
                                    achievements_count = VALUES(achievements_count)";
            
            $leaderboard_stmt = $conn->prepare($leaderboard_sql);
            $leaderboard_stmt->execute([
                ':user_id' => $user_id,
                ':cycle_id' => $cycle_id,
                ':total_points' => $stats['total_points'],
                ':completion_rate' => $stats['completion_rate'],
                ':current_streak' => $stats['current_streak'],
                ':achievements_count' => $stats['achievements_count']
            ]);
        }
        
        // Update rankings for all users in this cycle
        self::updateRankings($cycle_id);
    }
    
    /**
     * Update rankings for all users in a cycle
     */
    private static function updateRankings($cycle_id) {
        $conn = \eBizIndia\PDOConn::getInstance();
        
        $ranking_sql = "UPDATE leaderboard_stats ls
                        JOIN (
                            SELECT user_id,
                                   ROW_NUMBER() OVER (ORDER BY total_points DESC, completion_rate DESC, current_streak DESC) as new_rank
                            FROM leaderboard_stats
                            WHERE cycle_id = :cycle_id AND is_visible = 1
                        ) rankings ON ls.user_id = rankings.user_id
                        SET ls.rank_position = rankings.new_rank
                        WHERE ls.cycle_id = :cycle_id";
        
        $ranking_stmt = $conn->prepare($ranking_sql);
        $ranking_stmt->execute([':cycle_id' => $cycle_id]);
    }
    
    /**
     * Get user's achievements
     */
    public static function getUserAchievements($user_id, $cycle_id = null) {
        $where_clause = $cycle_id ? "AND ua.cycle_id = :cycle_id" : "";
        
        $sql = "SELECT a.*, ua.earned_at, ua.cycle_id as earned_cycle_id
                FROM user_achievements ua
                JOIN achievements a ON ua.achievement_id = a.id
                WHERE ua.user_id = :user_id {$where_clause}
                ORDER BY ua.earned_at DESC";
        
        $params = [':user_id' => $user_id];
        if ($cycle_id) {
            $params[':cycle_id'] = $cycle_id;
        }
        
        $stmt = \eBizIndia\PDOConn::query($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get user's statistics
     */
    public static function getUserStats($user_id, $cycle_id) {
        $sql = "SELECT * FROM user_stats WHERE user_id = :user_id AND cycle_id = :cycle_id";
        $stmt = \eBizIndia\PDOConn::query($sql, [':user_id' => $user_id, ':cycle_id' => $cycle_id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get leaderboard for a cycle
     */
    public static function getLeaderboard($cycle_id, $limit = 10) {
        $sql = "SELECT ls.*, u.username, m.name
                FROM leaderboard_stats ls
                JOIN users u ON ls.user_id = u.id
                JOIN members m ON u.profile_id=m.id
                WHERE ls.cycle_id = :cycle_id AND ls.is_visible = 1
                ORDER BY ls.rank_position ASC
                LIMIT :limit";
        
        $stmt = \eBizIndia\PDOConn::getInstance()->prepare($sql);
        $stmt->bindValue(':cycle_id', $cycle_id, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get recent achievements for motivation
     */
    public static function getRecentAchievements($user_id, $days = 7) {
        $sql = "SELECT a.*, ua.earned_at
                FROM user_achievements ua
                JOIN achievements a ON ua.achievement_id = a.id
                WHERE ua.user_id = :user_id 
                AND ua.earned_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                ORDER BY ua.earned_at DESC";
        
        $stmt = \eBizIndia\PDOConn::query($sql, [':user_id' => $user_id, ':days' => $days]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>