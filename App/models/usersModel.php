<?php
require_once 'baseModel.php';

class UsersModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct('users');
    }

    /**
     * Creates a new user
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $role
     * @param string $phone
     * @param string $address
     * @param string $country
     * @return bool
     */
    public function createUser(string $name, string $email, string $password, string $role, string $phone, string $address, string $country): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO `users` (`name`, `email`, `password`, `role`, `phone`, `address`, `country`) 
                  VALUES (:name, :email, :password, :role, :phone, :address, :country)";
        return $this->executeNonQuery($query, [
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role,
            ':phone' => $phone,
            ':address' => $address,
            ':country' => $country
        ]);
    }

    /**
     * Creates a new friend user in the database.
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $phone
     * @param string $address
     * @param string $country
     * @return bool
     */
    public function createFriendUser(string $name, string $email, string $password, string $phone, string $address, string $country): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO `users` (`name`, `email`, `password`, `phone`, `address`, `country`) 
                  VALUES (:name, :email, :password, :phone, :address, :country)";
        return $this->executeNonQuery($query, [
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':phone' => $phone,
            ':address' => $address,
            ':country' => $country
        ]);
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
     * Handles user login by email
     *
     * @param string $email
     * @return array|false
     */
    public function handleLogin(string $email)
    {
        $query = "SELECT * FROM `users` WHERE `email` = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Counts the number of friends in the database
     *
     * @return int
     */
    public function countFriends(): int
    {
        $query = "SELECT COUNT(*) as friend_count 
              FROM `users` 
              WHERE `role` = 'friend'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['friend_count'];
    }

    /**
     * Retrieves all users
     *
     * @return array
     */
    public function getUsers(): array
    {
        $query = "SELECT * FROM `users`";
        return $this->executeQuery($query);
    }

    /**
     * Retrieves all friends
     *
     * @return array
     */
    public function getFriends()
    {
        $query = "SELECT * FROM `users` WHERE `role` = 'friend'";
        return $this->executeQuery($query);
    }


    /**
     * Retrieves all admins
     *
     * @return array
     */
    public function getAdmins(){
        $query = "SELECT * FROM `users` WHERE `role` = 'admin'";
        return $this->executeQuery($query);
    }

    /**
     * Retrieves a user by their ID
     *
     * @param int $id
     * @return array|null
     */
    public function getUserById($id): ?array
    {
        $query = "SELECT * FROM `users` WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Executes a query and returns the result set
     *
     * @param string $query
     * @param array $params
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

    /**
     * Creates the first admin user if none exists
     *
     * @param string $role
     * @return bool
     */
    public function createFirstAdmin($role): bool 
    {
        $checkAdminQuery = "SELECT COUNT(*) as admin_count FROM `users` WHERE `role` = 'admin'";
        $result = $this->executeQuery($checkAdminQuery);
        $adminExists = $result[0]['admin_count'] > 0;

        if (!$adminExists && $role === 'admin') {
            $name = "Admin";
            $email = "Admin@gmail.com";
            $password = "123";
            $phone = "123";
            $address = "123";
            $country = "123";
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO `users` (`name`, `email`, `password`, `role`, `phone`, `address`, `country`)
                  VALUES (:name, :email, :password, :role, :phone, :address, :country)";
        return $this->executeNonQuery($query, [
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role,
            ':phone' => $phone,
            ':address' => $address,
            ':country' => $country
        ]);
    }
}