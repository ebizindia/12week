<?php
// SECURITY: This file should be removed from production or protected with proper authentication
// and should only be accessible to administrators during data import operations.

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);
require_once 'inc-oth.php';

// SECURITY FIX: Database credentials should be loaded from config.php, not hardcoded
// Using the same database connection as the main application
if (!defined('CONST_DB_CREDS')) {
    die('Configuration not loaded. Please ensure config.php is properly configured.');
}

// Parse database credentials from config
$db_creds = CONST_DB_CREDS;
$mysql_creds = $db_creds['mysql'] ?? [];

if (empty($mysql_creds)) {
    die('Database configuration not found.');
}

$host = $mysql_creds['host'] ?? 'localhost';
$dbname = $mysql_creds['db_name'] ?? '';
$username = $mysql_creds['db_user'] ?? '';
$password = $mysql_creds['db_password'] ?? '';

if (empty($dbname) || empty($username)) {
    die('Incomplete database configuration.');
}

$default_pswd = \eBizIndia\generatePassword();

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}

// Path to the CSV file in cPanel
$inputFileName = 'yidata.csv';

if (($handle = fopen($inputFileName, "r")) !== FALSE) {
    // Skip the header row if there is one
    $header = fgetcsv($handle);
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $timestamp = time();
    $datetime = new DateTime();
    $datetime->setTimestamp($timestamp);
    $current_datetime = $datetime->format('Y-m-d H:i:s');
    $joined = $datetime->format('Y-m-d');
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        try {
            $nameParts = explode(' ', $data[0]);

if (count($nameParts) === 3) {
    // If there are three parts
    list($firstName, $middleName, $lastName) = $nameParts;
} elseif (count($nameParts) === 2) {
    // If there are two parts
    list($firstName, $lastName) = $nameParts;
    $middleName = ""; // No middle name
} else {
    // Handle case with one part if needed
    $firstName = $nameParts[0];
    $middleName = "";
    $lastName = "";
}



            $salute = $data[2] == 'Male' ? 'Mr.' : 'Mrs.';

            $gendr = $data[2] == 'Male' ? 'M.' : 'F.';

            $city = '';

            if (is_numeric($data[5])) {
               $pin = $data[5];
               $address = '';
            }else{
              $pin = '';  
              $address = $data[5];
            }

            

            // if (preg_match('/\b([A-Za-z]+)\b(?=\s*\d{6})/', $data[7], $matches)) {
            //    $city = $matches[0];

            // }else{
            //    $city = ''; 
            // }

            $dob = date('Y-m-d', strtotime($data[1]));
           // $doj = isset($data[3]) && !empty($data[3]) ? date('Y-m-d', strtotime($data[3])) : null;
            
            // if (preg_match('/\b\d{6}\b/', $data[7], $pins)) {
            //        $pin =  $pins[0]; // Returns the first match of a 6-digit PIN code
            //    }else{
            //     $pin = '';
            //    }

             

            // Insert into the members table
            echo " Name ".$firstName;
            $sql1 = "INSERT INTO `members`(`title`, `fname`,`mname`, `lname`, `email`, `mobile`, `mobile2`, `gender`, `dob`,`residence_city`,`spouse_name` ,`residence_pin`, `residence_addrline1`,`active`, `dnd`,`created_at`, `created_from`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"; 
            $stmt1 = $pdo->prepare($sql1);

            

            $stmt1->execute([
                $salute, $firstName,$middleName, $lastName, $data[4], $data[3], $data[3],$gendr,$dob,$city,$data[11],$pin,$address,'y','n',$current_datetime,$ip_address
            ]);
            

            $memberid = $pdo->lastInsertId();

            // Insert into the users table
            // SECURITY FIX: Use strong random password instead of weak default '123456'
            $secure_random_password = \eBizIndia\generatePassword();
            $sql2 = "INSERT INTO `users`(`username`, `profile_type`, `profile_id`, `user_type`, `password`, `createdOn`, `createdFrom`) VALUES (?,?,?,?,?,?,?)";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([
                $data[4], 'member', $memberid, 1, password_hash($secure_random_password, PASSWORD_BCRYPT),$current_datetime,$ip_address
            ]);

            // Log the generated password for admin to send to user
            echo " | Generated password: " . htmlspecialchars($secure_random_password);
            
            $userid = $pdo->lastInsertId();
            
             $sql3 = "INSERT INTO `user_roles`(`user_id`, `role_id`) VALUES (?,?)"; 
            $stmt3 = $pdo->prepare($sql3);
            $stmt3->execute([
                $userid, '2'
            ]);

        } catch (PDOException $e) {
            echo "Error inserting row: " . $e->getMessage() . "<br>";
        }
    }
    fclose($handle);
    echo "Data inserted successfully!";
} else {
    die("Could not open the file $inputFileName");
}
?>
