<?php
// auth/register_process.php
session_start();

// Include database configuration
require_once '../includes/config.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']);
    
    // Initialize error array
    $errors = [];
    
    // Validation
    if (empty($first_name)) {
        $errors[] = "First name is required";
    } elseif (strlen($first_name) < 2) {
        $errors[] = "First name must be at least 2 characters";
    }
    
    if (empty($last_name)) {
        $errors[] = "Last name is required";
    } elseif (strlen($last_name) < 2) {
        $errors[] = "Last name must be at least 2 characters";
    }
    
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Username can only contain letters, numbers, and underscores";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    if (!$terms) {
        $errors[] = "You must agree to the terms and conditions";
    }
    
    // Check if username or email already exists
    if (empty($errors)) {
        $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $existing_user = $result->fetch_assoc();
            $check_sql2 = "SELECT username, email FROM users WHERE id = ?";
            $stmt2 = $conn->prepare($check_sql2);
            $stmt2->bind_param("i", $existing_user['id']);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $user_data = $result2->fetch_assoc();
            
            if ($user_data['username'] === $username) {
                $errors[] = "Username already exists";
            }
            if ($user_data['email'] === $email) {
                $errors[] = "Email already registered";
            }
        }
    }
    
    // If no errors, create user
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare SQL statement
        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        
        if ($stmt->execute()) {
            // Get the new user ID
            $user_id = $stmt->insert_id;
            
            // Store user info in session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = 'user';
            
            // Redirect to homepage with success message
            header("Location: ../index.php?success=Registration successful! Welcome to CineBook");
            exit();
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
    
    // If there are errors, redirect back to register page with errors
    if (!empty($errors)) {
        $error_string = implode("|", $errors);
        header("Location: register.php?error=" . urlencode($error_string));
        exit();
    }
} else {
    // If not POST request, redirect to register page
    header("Location: register.php");
    exit();
}
?>