<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Cinema Booking' : 'Cinema Booking'; ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- MAIN CSS - ABSOLUTE PATH -->
    <link rel="stylesheet" href="/cinema-booking/css/style.css">
    
    <style>
        /* Quick backup styles */
        body { margin: 0; font-family: 'Poppins', sans-serif; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .btn { padding: 10px 20px; background: #e94560; color: white; border: none; border-radius: 5px; text-decoration: none; }
    </style>
</head>
<body>
    <!-- Header/Navigation -->
    <header class="main-header">
        <div class="container header-container">
            <!-- Logo -->
            <div class="logo">
                <a href="/cinema-booking/index.php">
                    <i class="fas fa-film"></i> CineBook
                </a>
            </div>
            
            <!-- Navigation -->
            <nav class="main-nav">
                <ul>
                    <li><a href="/cinema-booking/index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="/cinema-booking/booking/movies.php"><i class="fas fa-ticket-alt"></i> Movies</a></li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="/cinema-booking/booking/mybookings.php"><i class="fas fa-calendar-alt"></i> My Bookings</a></li>
                        <li><a href="/cinema-booking/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php else: ?>
                        <li><a href="/cinema-booking/auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li><a href="/cinema-booking/auth/register.php" class="btn">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="main-content">