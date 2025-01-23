<?php
require_once 'baseModel.php';
require_once '../views/targetPage.php';

class treesUpdatesModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct('tree_updates');
    }

    public function getTreePhoto($treeId){
        $query = "SELECT photo_url FROM trees WHERE id = :tree_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tree_id', $treeId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $result['photo_url'];
        }
        return null;
    }

    /**
     * Creates a new tree update entry in the database
     *
     * @param int $tree_id
     * @param float $height
     * @param string $image_url
     * @param string $status
     * @param string $updateDate
     * @return bool
     */
    public function createTreeUpdate(int $tree_id, float $height, string $image_url, string $status, string $updateDate): bool
{
    try {
        // Update the tree's height and photo URL
        $updatePhotoQuery = "UPDATE `trees` SET `height` = :height, `photo_url` = :image_url WHERE id = :tree_id";
        $success = $this->executeNonQuery($updatePhotoQuery, [
            ':height' => $height,
            ':image_url' => $image_url,
            ':tree_id' => $tree_id,
        ]);

        if (!$success) {
            setTargetMessage("error", "Error updating tree photo in database.");
            return false; // Early exit on failure
        }

        // Insert the tree update record
        $createUpdateQuery = "INSERT INTO `tree_updates` (`tree_id`, `height`, `image_url`, `status`, `update_date`)  
                              VALUES (:tree_id, :height, :image_url, :status, :updateDate)";
        $success = $this->executeNonQuery($createUpdateQuery, [
            ':tree_id' => $tree_id,
            ':height' => $height,
            ':image_url' => $image_url,
            ':status' => $status,
            ':updateDate' => $updateDate,
        ]);

        if (!$success) {
            setTargetMessage("error", "Error inserting tree update into database.");
            return false; // Early exit on failure
        }

        return true; // Both operations succeeded
    } catch (Exception $e) {
        throw new Exception("Error creating tree update: " . $e->getMessage());
    }
}

        
    

    /**
     * Executes a non-query SQL statement
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
     * Retrieves all updates for a specific tree by its ID
     *
     * @param int $tree_id
     * @return array
     */
    public function getTreeUpdatesByTreeId(int $tree_id): array
    {
        $query = "SELECT * FROM `tree_updates` WHERE `tree_id` = :tree_id ORDER BY `update_date` DESC";
        return $this->executeQuery($query, [':tree_id' => $tree_id]);
    }

    /**
     * Retrieves the latest update for a specific tree by its ID
     *
     * @param int $tree_id
     * @return array|null The latest tree update or null if not found
     */
    public function getLatestTreeUpdate(int $tree_id): ?array
    {
        $query = "SELECT * FROM `tree_updates` WHERE `tree_id` = :tree_id ORDER BY `update_date` DESC LIMIT 1";
        $result = $this->executeQuery($query, [':tree_id' => $tree_id]);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Retrieves all outdated trees that haven't been updated in over 30 days.
     *
     * @return array
     */
    public function getOutdatedTrees(): array
    {
        $query = "SELECT Trees.id AS tree_id, Species.commercial_name, Tree_Updates.update_date
            FROM Trees JOIN Species ON Trees.species_id = Species.id
            JOIN Tree_Updates ON Trees.id = Tree_Updates.tree_id
            WHERE Tree_Updates.update_date < NOW() - INTERVAL 30 DAY";
        return $this->executeQuery($query);
    }

    /**
     * Executes a query and returns the result set
     *
     * @param string 
     * @param array 
     * @return array
     */
    private function executeQuery(string $query, array $params = []): array
    {
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database query failed: " . $e->getMessage());
        }
    }
}
