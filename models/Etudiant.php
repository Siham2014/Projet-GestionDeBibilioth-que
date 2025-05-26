<?php
require_once __DIR__ . '/../config/Database.php';

class Etudiant
{
    // Database connection and table name
    private $conn;
    private $table_name = "etudiant";

    // Object properties
    public $id;
    public $nom;
    public $prenom;
    public $email;
    public $telephone;
    public $date_naissance;
    public $username;
    public $password;
    public $role;
    public $is_active;
    public $verification_token;
    public $email_verified_at;
    public $reset_token;
    public $reset_token_expiry;
    public $date_inscription;
    public $updated_at;

    // Constructor with database connection
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create new student
    public function create()
    {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    nom=:nom, 
                    prenom=:prenom, 
                    email=:email,
                    telephone=:telephone,
                    date_naissance=:date_naissance,
                    username=:username, 
                    password=:password,
                    role=:role,
                    is_active=:is_active,
                    verification_token=:verification_token,
                    photoprofil=:photoprofil";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->prenom = htmlspecialchars(strip_tags($this->prenom));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telephone = htmlspecialchars(strip_tags($this->telephone));
        $this->username = htmlspecialchars(strip_tags($this->username));

        // Hash the password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        // Bind parameters
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':date_naissance', $this->date_naissance);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':verification_token', $this->verification_token);
        $stmt->bindParam(':photoprofil', $this->photoprofil);
        

        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Check if email exists
    public function emailExists()
    {
        // Query to check if email exists
        $query = "SELECT id, username, password, role, is_active, verification_token, email_verified_at
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Bind parameters
        $stmt->bindParam(1, $this->email);

        // Execute query
        $stmt->execute();

        // Get record details
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // If email exists, assign values to object properties
        if ($row) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->role = $row['role'];
            $this->is_active = $row['is_active'];
            $this->verification_token = $row['verification_token'];
            $this->email_verified_at = $row['email_verified_at'];
            return true;
        }

        return false;
    }

    // Check if username exists
    public function usernameExists()
    {
        // Query to check if username exists
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = ? LIMIT 0,1";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->username = htmlspecialchars(strip_tags($this->username));

        // Bind parameters
        $stmt->bindParam(1, $this->username);

        // Execute query
        $stmt->execute();

        // Check if username exists
        return $stmt->rowCount() > 0;
    }

    // Verify account with token
    public function verifyAccount()
    {
        // Query to verify account
        $query = "UPDATE " . $this->table_name . "
                SET 
                    is_active = 1,
                    email_verified_at = NOW(),
                    verification_token = NULL
                WHERE id = :id AND verification_token = :token";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->verification_token = htmlspecialchars(strip_tags($this->verification_token));

        // Bind parameters
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':token', $this->verification_token);

        // Execute query
        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    public function findByEmail(string $email): ?Etudiant
    {
        $sql = "SELECT * FROM etudiant WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['email' => $email]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        $etudiant = new Etudiant();
        foreach ($result as $key => $value) {
            $etudiant->$key = $value;
        }

        return $etudiant;
    }

    public function generateResetToken()
    {
        $this->reset_token = bin2hex(random_bytes(32));
        $this->reset_token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $query = "UPDATE " . $this->table_name . "
                SET 
                    reset_token = :token,
                    reset_token_expiry = :expiry
                WHERE email = :email";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':token', $this->reset_token);
        $stmt->bindParam(':expiry', $this->reset_token_expiry);
        $stmt->bindParam(':email', $this->email);

        return $stmt->execute();
    }

    public function resetPassword($token, $newPassword)
    {
        $query = "UPDATE " . $this->table_name . "
                SET 
                    password = :password,
                    reset_token = NULL,
                    reset_token_expiry = NULL
                WHERE reset_token = :token 
                AND reset_token_expiry > NOW()";

        $stmt = $this->conn->prepare($query);

        $password_hash = password_hash($newPassword, PASSWORD_BCRYPT);

        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':token', $token);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    public function findByResetToken($token)
    {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE reset_token = :token 
                AND reset_token_expiry > NOW()
                LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        $etudiant = new Etudiant();
        foreach ($result as $key => $value) {
            $etudiant->$key = $value;
        }

        return $etudiant;
    }

    // Get all students
    public function getAllEtudiants()
    {
        $query = "SELECT id, username, email, date_inscription, role ,photoprofil 
        FROM " . $this->table_name . " 
        ORDER BY date_inscription DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Delete a student
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    // Find student by ID
    public function findById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update student information
    public function update($id, $username, $email, $password = null)
    {
        $query = "UPDATE " . $this->table_name . "
                SET 
                    username = :username,
                    email = :email";

        // Add password update if provided
        if ($password) {
            $query .= ", password = :password";
        }

        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":id", $id);

        // Bind password if provided
        if ($password) {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $password_hash);
        }

        return $stmt->execute();
    }

    public function getReservationsActives($etudiantId)
    {
        $query = "SELECT r.*, l.titre, l.isbn 
                 FROM reservations r 
                 JOIN livres l ON r.livre_id = l.id 
                 WHERE r.etudiant_id = :etudiant_id 
                 AND r.date_retour IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['etudiant_id' => $etudiantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNombreReservationsActives($etudiantId)
    {
        $query = "SELECT COUNT(*) as count 
                 FROM reservations 
                 WHERE etudiant_id = :etudiant_id 
                 AND date_retour IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['etudiant_id' => $etudiantId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function hasRetard($etudiantId)
    {
        $query = "SELECT COUNT(*) as count 
                 FROM reservations 
                 WHERE etudiant_id = :etudiant_id 
                 AND date_retour IS NULL 
                 AND date_limite < CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['etudiant_id' => $etudiantId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
}
?>