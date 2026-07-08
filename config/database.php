<?php
/**
 * database.php
 * Stores the raw connection details for MySQL.
 * The actual CONNECTION LOGIC (using PDO) lives in classes/Database.php.
 * This file only holds the settings - separating "what" from "how".
 */

define('DB_HOST', '127.0.0.1');   
define('DB_NAME', 'hospital_db'); 
define('DB_USER', 'root');      
define('DB_PASS', '');            
define('DB_CHARSET', 'utf8mb4');  
