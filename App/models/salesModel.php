<?php
require_once 'baseModel.php';

class SalesModel extends BaseModel
{
    
    public function __construct()
    {
        parent::__construct('Sales');
    }


     /**
     * Creates a new sale record in the database
     * 
     * @param int $buyerId
     * @param int $treeId 
     * @return bool 
     */
    public function createSale(int $buyerId, int $treeId): bool
    {
        $query = "INSERT INTO `Sales` (`tree_id`, `buyer_id`) VALUES (:tree_id, :buyer_id)";
        return $this->executeQuery($query, [':tree_id' => $treeId, ':buyer_id' => $buyerId]);
    }

     /**
     * Retrieves a list of sales made by a specific buyer
     * 
     * @param int $buyerId
     * @return array
     */
    public function getSalesByBuyerId(int $buyerId): array
    {
        $query = "SELECT * FROM `sales` WHERE `buyer_id` = :buyer_id";
        return $this->fetchRecords($query, [':buyer_id' => $buyerId]);
    }
 
     /**
     * Retrieves a list of sales for a specific tree
     * 
     * @param int $treeId
     * @return array 
     */
    public function getSalesByTreeId(int $treeId): array
    {
        $query = "SELECT * FROM `Sales` WHERE `tree_id` = :tree_id";
        return $this->fetchRecords($query, [':tree_id' => $treeId]);
    }

     /**
     * Executes a query and fetches the results as an associative array
     * 
     * @param string $query
     * @param array $params
     * @return array
     */
    private function fetchRecords(string $query, array $params)
    {
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

     /**
     * Executes a non-query SQL statement (e.g., INSERT, UPDATE, DELETE)
     * 
     * @param string $query
     * @param array $params
     * @return bool 
     */
    private function executeQuery(string $query, array $params): bool
    {
        try {
            $stmt = $this->db->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            return false;
        }
    }

     /**
     * Checks if a tree is available for purchase
     * 
     * @param int $treeId
     * @return array 
     */
    public function isTreeAvailable($treeId) {
        $query = "SELECT available FROM Trees WHERE id = :treeId";
        return $this->fetchRecords($query, [':tree_id' => $treeId]);
    }
    
     /**
     * Registers a new sale by creating a sale record in the database
     * 
     * @param int $userId 
     * @param int $treeId
     * @return bool 
     */
   public function registerSale($userId, $treeId) {
        $query = "INSERT INTO Sales (tree_id, buyer_id) VALUES (:treeId, :userId)";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':treeId' => $treeId,
            ':userId' => $userId
        ]);
    }
    
     /**
     * Marks a tree as sold by updating its availability status
     * 
     * @param int $treeId 
     */
    public function markTreeAsSold($treeId) {
        $query = "UPDATE Trees SET available = FALSE WHERE id = :treeId";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':treeId' => $treeId]);
    }
}