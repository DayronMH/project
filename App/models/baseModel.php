<?php
require_once '../../config/database.php';
class baseModel
{
    protected $db;
    protected $table;


     /**
     * Constructs a new instance of the model, initializing the table name and database connection
     *
     * @param string $table
     */
    protected function __construct(string $table)
    {
        $this->table = $table;
        $this->connectToDatabase();
    }

     /**
     * Establishes a database connection using the specified database name
     *
     * @throws Exception 
     */
    private function connectToDatabase(): void
    {
        try {
            $this->db = Database::connect('mytrees');
        } catch (Exception $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

     /**
     * Closes the database connection
     */
    public function disconnect(): void
    {
        $this->db = null;
    }
}
