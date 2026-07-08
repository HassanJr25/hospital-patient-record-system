<?php
/**
 * Database.php
 
 */

require_once __DIR__ . '/../config/database.php';

class Database
{
   
    private static ?PDO $connection = null;

    // ---------------------------------------------------------------
    // CONSTRUCTOR:
   
    private function __construct()
    {
        // Intentionally empty - object creation is blocked from outside.
    }

    /**
     * getConnection()
   
     */
    public static function getConnection(): PDO
    {
    
        if (self::$connection === null) {

       
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

            try {
                self::$connection = new PDO($dsn, DB_USER, DB_PASS, [

                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                   
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
               
                if (function_exists('logAppError')) {
                    logAppError('Database connection failed: ' . $e->getMessage());
                } else {
                    error_log('Database connection failed: ' . $e->getMessage());
                }

                if (function_exists('showFriendlyErrorPage')) {
                    showFriendlyErrorPage();
                } else {
                    die('A system error occurred. Please try again later.');
                }
            }
        }

        return self::$connection;
    }
}
