<?php
// auth/login_process.php
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validate
    if (empty($username) || empty($password)) {
        redirect('login.php?error=Please fill all fields');
    }
    
    // Check if user exists by username or email
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect to homepage
            redirect('../index.php');
        } else {
            redirect('login.php?error=Invalid password');
        }
    } else {
        redirect('login.php?error=User not found');
    }
} else {
    redirect('login.php');
}
?>