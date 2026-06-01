<?php
// auth/register.php
require_once '../includes/config.php';

if (isLoggedIn()) {
    redirect('../index.php');
}

$pageTitle = "Register - Cinema Booking";
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
        /* Register page styles */
        .register-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .register-box {
            width: 100%;
            max-width: 500px;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .register-title {
            color: #1a1a2e;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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
        .register-btn {
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
        .register-btn:hover {
            background: #d43f57;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #e94560;
            text-decoration: none;
            font-weight: 600;
        }
        .password-strength {
            height: 5px;
            background: #ddd;
            border-radius: 3px;
            margin-top: 5px;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s;
        }
        .password-strength.weak .password-strength-bar {
            background: #e94560;
            width: 33%;
        }
        .password-strength.medium .password-strength-bar {
            background: #ffc107;
            width: 66%;
        }
        .password-strength.strong .password-strength-bar {
            background: #28a745;
            width: 100%;
        }
        .terms {
            font-size: 14px;
            color: #666;
        }
        .terms a {
            color: #e94560;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header style="background: #1a1a2e; color: white; padding: 15px 0;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center;">
            <a href="../index.php" style="color: white; text-decoration: none; font-size: 1.5rem; font-weight: bold;">
                <i class="fas fa-film"></i> CineBook
            </a>
            <a href="login.php" style="color: white; text-decoration: none;">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
        </div>
    </header>

    <div class="register-container">
        <div class="register-box">
            <h2 class="register-title">Create Your Account</h2>
            
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
            
            <form method="POST" action="register_process.php">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> First Name</label>
                        <input type="text" name="first_name" class="form-control" required placeholder="Enter first name">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Last Name</label>
                        <input type="text" name="last_name" class="form-control" required placeholder="Enter last name">
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Choose username">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" class="form-control" required placeholder="Enter email address">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" class="form-control" required 
                           placeholder="Create password (min. 6 characters)" oninput="checkPasswordStrength()">
                    <div class="password-strength" id="passwordStrength">
                        <div class="password-strength-bar"></div>
                    </div>
                    <small style="color: #666; font-size: 12px;">Password must be at least 6 characters long</small>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required 
                           placeholder="Confirm password" oninput="checkPasswordMatch()">
                    <small id="passwordMatch" style="font-size: 12px;"></small>
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="terms" required>
                        <span class="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
                    </label>
                </div>
                
                <button type="submit" class="register-btn">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
            
            <div class="login-link">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: #1a1a2e; color: white; padding: 20px 0; margin-top: 50px;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; text-align: center;">
            <p>&copy; <?php echo date('Y'); ?> CineBook. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="../js/script.js"></script>
    <script>
        // Register page specific JavaScript
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('passwordStrength');
            
            // Reset
            strengthBar.className = 'password-strength';
            
            if (password.length === 0) return;
            
            // Check strength
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Update UI
            if (strength <= 2) {
                strengthBar.classList.add('weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
            }
        }
        
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchText = document.getElementById('passwordMatch');
            
            if (confirmPassword.length === 0) {
                matchText.textContent = '';
                matchText.style.color = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchText.textContent = '✓ Passwords match';
                matchText.style.color = '#28a745';
            } else {
                matchText.textContent = '✗ Passwords do not match';
                matchText.style.color = '#e94560';
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Register page loaded');
            
            // Form validation
            const registerForm = document.querySelector('form');
            if (registerForm) {
                registerForm.addEventListener('submit', function(e) {
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    
                    // Check password match
                    if (password !== confirmPassword) {
                        e.preventDefault();
                        alert('Passwords do not match. Please check and try again.');
                        return;
                    }
                    
                    // Check password length
                    if (password.length < 6) {
                        e.preventDefault();
                        alert('Password must be at least 6 characters long.');
                        return;
                    }
                    
                    // Check terms agreement
                    const terms = document.querySelector('input[name="terms"]');
                    if (!terms.checked) {
                        e.preventDefault();
                        alert('You must agree to the Terms of Service and Privacy Policy.');
                        return;
                    }
                });
            }
        });
    </script>
</body>
</html>