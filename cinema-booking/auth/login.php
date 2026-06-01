<?php
// auth/login.php
require_once '../includes/config.php';

if (isLoggedIn()) {
    redirect('../index.php');
}

$pageTitle = "Login - Cinema Booking";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Login page specific styles */
        .login-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo i {
            font-size: 3rem;
            color: #e94560;
        }
        .login-title {
            color: #1a1a2e;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-control:focus {
            outline: none;
            border-color: #e94560;
        }
        .login-btn {
            width: 100%;
            padding: 14px;
            background: #e94560;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-btn:hover {
            background: #d43f57;
        }
        .login-links {
            text-align: center;
            margin-top: 20px;
        }
        .login-links a {
            color: #e94560;
            text-decoration: none;
        }
        .social-login {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .social-btn {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .social-btn.google {
            color: #DB4437;
        }
        .social-btn.facebook {
            color: #4267B2;
        }
    </style>
</head>
<body>
    <!-- Simple header for login page -->
    <header style="background: #1a1a2e; color: white; padding: 15px 0;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center;">
            <a href="../index.php" style="color: white; text-decoration: none; font-size: 1.5rem; font-weight: bold;">
                <i class="fas fa-film"></i> CineBook
            </a>
        </div>
    </header>

    <div class="login-container">
        <div class="login-box">
            <div class="login-logo">
                <i class="fas fa-film"></i>
            </div>
            <h2 class="login-title">Login to Your Account</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <!-- CHANGE THIS: Form submits to itself, not to login_process.php -->
            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username or Email</label>
                    <input type="text" name="username" class="form-control" required placeholder="Enter username or email">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Enter password">
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 5px;">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="#" style="color: #e94560; text-decoration: none; font-size: 14px;">Forgot Password?</a>
                </div>
                
                <button type="submit" name="login" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="login-links">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
            
            <div class="social-login">
                <p style="text-align: center; color: #666; margin-bottom: 15px;">or login with:</p>
                <button class="social-btn google">
                    <i class="fab fa-google"></i> Continue with Google
                </button>
                <button class="social-btn facebook">
                    <i class="fab fa-facebook"></i> Continue with Facebook
                </button>
            </div>
        </div>
    </div>

    <!-- Simple footer -->
    <footer style="background: #1a1a2e; color: white; padding: 20px 0; margin-top: 50px;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; text-align: center;">
            <p>&copy; <?php echo date('Y'); ?> CineBook. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="../js/script.js"></script>
    <script>
        // Login page specific JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Login page loaded');
            
            // Form validation
            const loginForm = document.querySelector('form');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    const inputs = this.querySelectorAll('input[required]');
                    let valid = true;
                    
                    inputs.forEach(input => {
                        if (!input.value.trim()) {
                            valid = false;
                            input.style.borderColor = '#e94560';
                        } else {
                            input.style.borderColor = '#ddd';
                        }
                    });
                    
                    if (!valid) {
                        e.preventDefault();
                        alert('Please fill in all required fields.');
                    }
                });
            }
            
            // Social login buttons
            document.querySelectorAll('.social-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    alert('Social login feature coming soon!');
                });
            });
        });
    </script>
    
    <?php
    // ADD THIS: Login processing code at the bottom of the file
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validation
        if (empty($username) || empty($password)) {
            echo "<script>alert('Please enter username and password');</script>";
        } else {
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
                    
                    // Set remember me cookie (30 days)
                    if ($remember) {
                        $cookie_value = json_encode([
                            'user_id' => $user['id'],
                            'token' => bin2hex(random_bytes(32))
                        ]);
                        setcookie('remember_me', $cookie_value, time() + (30 * 24 * 60 * 60), '/');
                    }
                    
                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        echo "<script>
                            alert('Welcome Admin! Redirecting to dashboard...');
                            window.location.href = '../admin/dashboard.php';
                        </script>";
                    } else {
                        echo "<script>
                            alert('Login successful! Redirecting to homepage...');
                            window.location.href = '../index.php';
                        </script>";
                    }
                    
                    exit();
                } else {
                    echo "<script>alert('Invalid password');</script>";
                }
            } else {
                echo "<script>alert('User not found');</script>";
            }
        }
    }
    ?>
</body>
</html>