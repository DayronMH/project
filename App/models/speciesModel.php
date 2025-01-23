<?php
require_once 'baseModel.php';

class speciesModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct('species');
    }


     /**
     * Creates a new species in the database
     *
     * @param string $commercial_name
     * @param string $scientific_name
     * @return bool
     */
    public function createSpecie(string $commercial_name, string $scientific_name): bool
    {
        date_default_timezone_set('America/Costa_Rica');
        $date = date('Y-m-d H:i:s');
        $query = "INSERT INTO `species` (`commercial_name`, `scientific_name`, `availability_date`)
                  VALUES (:commercial_name, :scientific_name, :availability_date)";

        return $this->executeNonQuery($query, [
            ':commercial_name' => $commercial_name,
            ':scientific_name' => $scientific_name,
            ':availability_date' => $date
        ]);
    }

    /**
     * Retrieves a list of all species in the database
     *
     * @return array
     */
    public function getAllSpecies(): array
    {
        $query = "SELECT id, commercial_name, scientific_name FROM Species";
        return $this->executeQuery($query);
    }

     /**
     * Retrieves a specific species by its ID
     *
     * @param int $speciesId
     * @return array
     */
    public function getSpeciesById(int $speciesId)
    {
        $query = "SELECT id, commercial_name, scientific_name FROM Species WHERE id = :species_id";
        return $this->executeQuery($query, [':species_id' => $speciesId]);
    }

     /**
     * Retrieves a list of all commercial names of species
     *
     * @return array
     */
    public function getCommercialNames()
    {
        $query = "SELECT id, commercial_name FROM Species";
        return $this->executeQuery($query);
    }

     /**
     * Retrieves a list of all scientific names of species
     *
     * @return array
     */
    public function getScientificNames()
    {
        $query = "SELECT id, scientific_name FROM Species ";
        return $this->executeQuery($query);
    }

     /**
     * Deletes a species and its associated trees
     *
     * @param int $id
     * @return array 
     */
    public function deleteSpecie(int $id): array
    {
        try {
            if ($this->hasTreesAssociated($id)) {
                $deleteRelatedTreesQuery = "DELETE FROM `trees` WHERE `species_id` = :species_id";
                $relatedTreesDeleted = $this->executeQuery($deleteRelatedTreesQuery, [':species_id' => $id]);
                
                if (!$relatedTreesDeleted) {
                    return [
                        'error' => 'No se pudieron eliminar los árboles asociados a esta especie'
                    ];
                }
            }
            $deleteSpeciesQuery = "DELETE FROM `species` WHERE `id` = :id";
            $speciesDeleted = $this->executeQuery($deleteSpeciesQuery, [':id' => $id]);
            
            if ($speciesDeleted) {
                return [
                    'success' => 'La especie y sus árboles asociados fueron eliminados correctamente'
                ];
            } else {
                return [
                    'error' => 'No se pudo eliminar la especie'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al eliminar especie: " . $e->getMessage());
            return [
                'error' => 'Ocurrió un error al procesar la eliminación'
            ];
        }
    }

     /**
     * Checks if a species has associated trees
     *
     * @param int $speciesId
     * @return bool
     */
    public function hasTreesAssociated($speciesId): bool 
    {
        try {
            $query = "SELECT COUNT(*) FROM `trees` WHERE `species_id` = :species_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':species_id' => $speciesId]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar árboles asociados: " . $e->getMessage());
            return false;
        }
    }

     /**
     * Updates the commercial name and scientific name of a species
     *
     * @param int $speciesId
     * @param string $commercialName
     * @param string $scientificName
     * @return bool
     */
    public function editSpecies($speciesId, $commercialName, $scientificName)
    {
        $stmt = $this->db->prepare("UPDATE species SET commercial_name = ?, scientific_name = ? WHERE id = ?");
        return $stmt->execute([$commercialName, $scientificName, $speciesId]);
    }

     /**
     * Executes a non-query SQL statement (e.g., INSERT, UPDATE, DELETE)
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
     * Executes an SQL query and returns the result set
     *
     * @param string $query 
     * @param array $params
     * @return array
     */
    private function executeQuery(string $query, array $params = null)
    {
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params ?? []);
            return $params ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
         }
    }
}
