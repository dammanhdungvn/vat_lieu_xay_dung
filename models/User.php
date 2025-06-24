<?php
/**
 * User Model
 * Handle all database operations related to users
 */

class User {
    private $conn;
    
    /**
     * Constructor
     * @param mysqli $conn Database connection
     */
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Find user by email
     * @param string $email User email
     * @return array|null User data or null
     */
    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT UserID, FullName, Email, PasswordHash, PhoneNumber, Address, IsActive FROM Users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    /**
     * Find user by ID
     * @param int $id User ID
     * @return array|null User data or null
     */
    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT UserID, FullName, Email, PhoneNumber, Address, IsActive FROM Users WHERE UserID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    /**
     * Create new user
     * @param array $userData User data
     * @return int|bool New user ID or false on failure
     */
    public function create($userData) {
        $stmt = $this->conn->prepare("INSERT INTO Users (FullName, Email, PasswordHash, PhoneNumber, Address, IsActive) VALUES (?, ?, ?, ?, ?, ?)");
        $isActive = true;
        $address = $userData['Address'] ?? null;
        
        $stmt->bind_param("sssssi", 
            $userData['FullName'], 
            $userData['Email'], 
            $userData['PasswordHash'], 
            $userData['PhoneNumber'], 
            $address, 
            $isActive
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }
    
    /**
     * Update user information
     * @param int $id User ID
     * @param array $userData User data
     * @return bool Success or failure
     */
    public function update($id, $userData) {
        $query = "UPDATE Users SET ";
        $params = [];
        $types = "";
        
        if (isset($userData['FullName'])) {
            $query .= "FullName = ?, ";
            $params[] = $userData['FullName'];
            $types .= "s";
        }
        
        if (isset($userData['PhoneNumber'])) {
            $query .= "PhoneNumber = ?, ";
            $params[] = $userData['PhoneNumber'];
            $types .= "s";
        }
        
        if (isset($userData['Address'])) {
            $query .= "Address = ?, ";
            $params[] = $userData['Address'];
            $types .= "s";
        }
        
        if (isset($userData['PasswordHash'])) {
            $query .= "PasswordHash = ?, ";
            $params[] = $userData['PasswordHash'];
            $types .= "s";
        }
        
        // Remove trailing comma and space
        $query = rtrim($query, ", ");
        
        $query .= " WHERE UserID = ?";
        $params[] = $id;
        $types .= "i";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        
        return $stmt->execute();
    }
    
    /**
     * Check if email exists
     * @param string $email Email to check
     * @return bool True if email exists
     */
    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT UserID FROM Users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
    
    /**
     * Check if phone number exists
     * @param string $phoneNumber Phone number to check
     * @return bool True if phone number exists
     */
    public function phoneNumberExists($phoneNumber) {
        if (empty($phoneNumber)) {
            return false;
        }
        
        $stmt = $this->conn->prepare("SELECT UserID FROM Users WHERE PhoneNumber = ?");
        $stmt->bind_param("s", $phoneNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
} 