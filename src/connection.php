<?php
/**
 * Encapsulates a connection to the database 
 * 
 * @author  Arturo Mora-Rioja
 * @version 1.0 August 2020
 */

require_once 'config.php';

class DB extends Config 
{
    /**
     * Opens a connection to the database
     * 
     * @returns a connection object, or false if the connection was unsuccessful
     */
    public function connect(): PDO|bool
    {

        $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DB_NAME . ';charset=utf8';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $pdo = @new PDO($dsn, self::USERNAME, self::PASSWORD, $options); 
            return $pdo;
        } catch (\PDOException $e) {
            return false;
        }    
    }

    /**
     * Closes a connection to the database
     * 
     * @param the connection object to disconnect
     */
    public function disconnect($pdo): void 
    {
        $pdo = null;
    }
}