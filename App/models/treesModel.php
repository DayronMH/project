<?php
require_once 'baseModel.php';
class TreesModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct('trees');
    }

    public function createTreeBasic(int $species_id, string $location, float $price, string $photo_url): bool
    {
        $query = "INSERT INTO `trees` (`species_id`, `location`, `price`, `photo_url`)
                  VALUES (:species_id, :location, :price, :photo_url)";
        return $this->executeNonQuery($query, [
            ':species_id' => $species_id,
            ':location' => $location,
            ':price' => $price,
            ':photo_url' => $photo_url
        ]);
        
    }

     /**
     * Updates a tree and its associated species information.
     *
     * @param int $treeId
     * @param string $specie
     * @param float $height
     * @param string $location
     * @param bool $available The new availability status of the tree
     *
     * @return array
     */
    public function editTree($treeId, $specie, $height, $location, $available) {
        try {
            $query = "UPDATE trees t 
                      JOIN species s ON t.species_id = s.id 
                      SET t.height = :height,
                          t.location = :location,
                          t.available = :available,
                          s.commercial_name = :commercial_name
                      WHERE t.id = :id";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                ':id' => $treeId,
                ':height' => $height,
                ':location' => $location,
                ':available' => $available,
                ':commercial_name' => $specie
            ]);
            
            if ($result) {
                return ['success' => 'Árbol y especie actualizados correctamente'];
            } else {
                return ['error' => 'Error al actualizar el árbol y la especie'];
            }
            
        } catch (PDOException $e) {
            return ['error' => 'Error al actualizar: ' . $e->getMessage()];
        }
    }

     /**
     * Executes a non-query SQL statement and returns a boolean indicating success
     *
     * @param string $query 
     * @param array $params
     * @return bool 
     */
    private function executeNonQuery(string $query, array $params = []): bool
    {
        try {
            $stmt = $this->db->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Database query failed: " . $e->getMessage());
        }
    }
    
     /**
     * Creates a new tree record in the database.
     *
     * @param int $species_id
     * @param int $owner_id
     * @param float $height
     * @param string $location
     * @param bool $available
     * @param float $price
     * @param string $photo_url
     * @return bool 
     */
    public function createTree(int $species_id, int $owner_id, float $height, string $location, bool $available, float $price, string $photo_url): bool
    {
        $query = "INSERT INTO `trees` (`species_id`, `owner_id`, `height`, `location`, `available`, `price`, `photo_url`)
                  VALUES (:species_id, :owner_id, :height, :location, :available, :price, :photo_url)";
        return $this->executeQuery($query, [
            ':species_id' => $species_id,
            ':owner_id' => $owner_id,
            ':height' => $height,
            ':location' => $location,
            ':available' => $available,
            ':price' => $price,
            ':photo_url' => $photo_url
        ]);
    }

     /**
     * Updates an existing tree record in the database
     *
     * @param int $id
     * @param int $species_id
     * @param int $owner_id
     * @param float $height 
     * @param string $location
     * @param bool $available
     * @param float $price 
     * @param string $photo_url
     * @return array 
     */
    public function updateTree(int $id, int $species_id, int $owner_id, float $height, string $location, bool $available, float $price, string $photo_url): array
    {
        $query = "UPDATE trees  SET species_id = :species_id,   owner_id = :owner_id,   height = :height,  location = :location,
         available = :available,  price = :price,  photo_url = :photo_url  WHERE id = :id";
        return $this->executeQuery($query, [':species_id' => $species_id, ':owner_id' => $owner_id, ':height' => $height, ':location' => $location,
         ':available' => $available, ':price' => $price, ':photo_url' => $photo_url, ':id' => $id]);
    }

     /**
     * Updates a tree's owner and availability status to reflect a purchase
     *
     * @param int $id The
     * @param int $owner_id
     * @return bool
     */
    public function buyTree(int $id, int $owner_id): bool
    {
        $query = "UPDATE trees  SET owner_id = :owner_id, available = FALSE  WHERE id = :id";
        $result = $this->executeQuery($query, [':owner_id' => $owner_id,':id' => $id]);
        return $result > 0;
    }

     /**
     * Counts the number of available trees in the database
     *
     * @return int The total number of trees currently marked as available
     */
    public function countAvailableTrees(): int
    {
        $query = "SELECT COUNT(*) as availableTreesCount
              FROM `trees` 
              WHERE available = 1";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['availableTreesCount'];
    }

     /**
     * Counts the number of trees currently marked as sold (not available) in the database
     *
     * @return int The total number of trees with 'available' status set to false
     */
    public function countSoldTrees(): int
    {
        $query = "SELECT COUNT(*) as soldTreesCount
              FROM `trees` 
              WHERE available = 0";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['soldTreesCount'];
    }

     /**
     * Retrieves a list of trees belonging to a specific species.
     *
     * @param int $species_id 
     * @return array 
     */
    public function getTreesBySpecies(int $species_id): array
    {
        $query = "SELECT * FROM Trees WHERE species_id = :species_id ORDER BY height DESC";
        return $this->executeQuery($query, [':species_id' => $species_id]);
    }

     /**
     * Retrieves a list of trees owned by a specific user.
     *
     * @param int $owner_id
     * @return array 
     */
    public function getTreesByOwner($owner_id): array
    {
        $query = "SELECT t.id, t.height, t.location, t.price, t.photo_url, t.available, s.commercial_name, s.scientific_name, u.name AS owner_name 
                  FROM Trees t 
                  JOIN Species s ON t.species_id = s.id 
                  LEFT JOIN Users u ON t.owner_id = u.id 
                  WHERE t.owner_id = :id";
        return $this->executeQuery($query, [':id' => $owner_id]);
    }
    

     /**
     * Retrieves a list of trees owned by a specific user
     *
     * @param int $owner_id 
     * @return array
     */
    public function getEditableTreeById($tree_id): array
    {
        $query = "SELECT t.id, t.height, s.commercial_name, t.location, t.available 
                  FROM trees t 
                  JOIN species s ON t.species_id = s.id 
                  WHERE t.id = :tree_id";
        return $this->executeQuery($query, [':tree_id' => $tree_id]);
    }


     /**
     * Retrieves detailed information about a specific tree
     *
     * @param int $treeId
     * @return array
     */
    public function getTreeById(int $treeId): array{
        $query = "SELECT t.id, t.height, t.location, t.price, t.photo_url, t.available, s.commercial_name, s.scientific_name, u.name AS owner_name 
                  FROM Trees t 
                  JOIN Species s ON t.species_id = s.id 
                  LEFT JOIN Users u ON t.owner_id = u.id 
                  WHERE t.id = :id";
        return $this->executeQuery($query, [':id' => $treeId]);
    }


     /**
     * Retrieves a list of all trees in the database
     *
     * @return array
     */
    public function getTrees(): array
    {
        $query = "SELECT * FROM `trees`";
        return $this->executeQuery($query);
    }

     /**
     * Deletes a tree from the database
     *
     * @param int $id
     * @return bool 
     */
    public function deleteTree(int $id): bool
    {
        $query = "DELETE FROM `trees` WHERE `id` = :id";
        return $this->executeQuery($query, [':id' => $id]);
    }


    private function executeQuery(string $query, array $params = []): mixed
    {
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database query failed: " . $e->getMessage());
        }
    }
    
     /**
     * Retrieves a list of trees currently marked as available for purchase
     *
     * @return array
     */
    public function getAvailableTrees(): array
    {
        $query = "SELECT * FROM `trees` WHERE `available` = TRUE";
        return $this->executeQuery($query);
    }

     /**
     * Retrieves a list of available trees with additional information about their species
     *
     * @return array
     */
    public function getAvailableTreesWithSpecies() {
        $query = "SELECT t.*, s.commercial_name, s.scientific_name, s.availability_date FROM Trees t 
        JOIN Species s ON t.species_id = s.id WHERE t.available = TRUE"; 
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

     /**
     * Retrieves a list of trees owned by a specific user, including species details
     *
     * @param int $userId
     * @return array 
     */
    public function getPurchasedTreesByUser(int $userId): array
    {
        $sql = "SELECT * FROM Trees t JOIN species s ON t.species_id = s.id WHERE owner_id = :userId";
        return $this->executeQuery($sql, [':userId' => $userId]);
    }

     /**
     * Retrieves a list of all trees in the database, including species details
     *
     * @return array 
     */
    public function getAllTrees(): array
    {
        $query = "SELECT * FROM `trees`";
        return $this->executeQuery($query);
    }


     /**
     * Retrieves a list of all trees with their species information (aliases included)
     *
     * @return array
     */
    public function getAllTreesWithSpecies(): array
    {
        $query = "SELECT Trees.id AS tree_id,Trees.height,Trees.location,Trees.available,Trees.price,Trees.photo_url,Species.id AS
        species_id,Species.commercial_name,Species.scientific_name,Species.availability_date FROM Trees JOIN Species ON Trees.species_id = Species.id";
        return $this->executeQuery($query);
    }

     /**
     * Retrieves a list of all trees with their species information and associated updates (if any)
     *
     * @return array
     */
    public function getAllTreesWithSpeciesAndUpdates(): array
    {
        $query = "SELECT Trees.id AS tree_id,Species.commercial_name,Tree_Updates.update_date FROM Trees
            JOIN Species ON Trees.species_id = Species.id LEFT JOIN Tree_Updates ON Trees.id = Tree_Updates.tree_id
            ORDER BY Trees.id, Tree_Updates.update_date";
        
        return $this->executeQuery($query);
    }
}
