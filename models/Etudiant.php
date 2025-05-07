<?php
require_once __DIR__ . '/../config/Database.php';

class Etudiant {
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
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Create new student
    public function create() {
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
                    verification_token=:verification_token";
        
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
        
        // Execute query
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Check if email exists
    public function emailExists() {
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
        if($row) {
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
    public function usernameExists() {
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
    public function verifyAccount() {
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
}
?>