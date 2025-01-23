<?php    
class Database {
    private static $host = "localhost";
    private static $username = "root"; 
    private static $password = "";
    private static $connection = null;

     /**
     * Establishes a database connection
     *
     * This method creates a PDO connection to the specified database and sets the error mode to exception
     * It uses a singleton pattern to ensure only one connection is established
     *
     * @param string $dbname
     * @return PDO The database connection object
     * @throws Exception If a database connection error occurs
     */
    public static function connect($dbname) {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . $dbname,
                    self::$username,
                    self::$password
                );
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error en la conexión: " . $e->getMessage());
            }
        }
        return self::$connection;
    }

     /**
     * Closes the database connection
     */
    public static function disconnect() {
        self::$connection = null;
    }
}
