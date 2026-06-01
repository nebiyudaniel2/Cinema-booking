<?php
// booking/booking_confirmation.php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

// Get booking ID from URL or session
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 
             (isset($_SESSION['last_booking_id']) ? $_SESSION['last_booking_id'] : 0);

if ($booking_id <= 0) {
    redirect('movies.php?error=Invalid booking');
}

// Get booking details
$sql = "SELECT b.*, m.title, m.genre, s.screening_time, s.hall_number 
        FROM bookings b
        JOIN screenings s ON b.screening_id = s.id
        JOIN movies m ON s.movie_id = m.id
        WHERE b.id = ? AND b.user_id = ?";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    redirect('movies.php?error=Booking not found');
}

$pageTitle = "Booking Confirmation";
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
        .confirmation-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        .confirmation-code {
            background: #1a1a2e;
            color: white;
            padding: 15px;
            border-radius: 10px;
            font-family: monospace;
            font-size: 1.5rem;
            letter-spacing: 5px;
            margin: 20px 0;
        }
        .booking-details {
            text-align: left;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        .action-btn {
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background: #e94560;
            color: white;
        }
        .btn-secondary {
            background: #0f3460;
            color: white;
        }
        .btn-outline {
            background: white;
            color: #e94560;
            border: 2px solid #e94560;
        }
    </style>
</head>
<body>
    <header style="background: #1a1a2e; color: white; padding: 15px 0;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <a href="../index.php" style="color: white; text-decoration: none; font-size: 1.5rem; font-weight: bold;">
                <i class="fas fa-film"></i> CineBook
            </a>
        </div>
    </header>

    <div class="confirmation-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 style="color: #28a745;">Booking Confirmed!</h1>
        <p style="font-size: 1.2rem; color: #666; margin-bottom: 20px;">
            Your tickets have been booked successfully. A confirmation email has been sent to your registered email address.
        </p>
        
        <div class="confirmation-code">
            BOOKING-<?php echo str_pad($booking_id, 6, '0', STR_PAD_LEFT); ?>
        </div>
        
        <div class="booking-details">
            <h3 style="color: #1a1a2e; margin-bottom: 20px;">Booking Details</h3>
            
            <div class="detail-row">
                <span style="font-weight: 600;">Movie:</span>
                <span><?php echo htmlspecialchars($booking['title']); ?></span>
            </div>
            
            <div class="detail-row">
                <span style="font-weight: 600;">Date & Time:</span>
                <span><?php echo date('F j, Y g:i A', strtotime($booking['screening_time'])); ?></span>
            </div>
            
            <div class="detail-row">
                <span style="font-weight: 600;">Hall Number:</span>
                <span>Hall <?php echo $booking['hall_number']; ?></span>
            </div>
            
            <div class="detail-row">
                <span style="font-weight: 600;">Booking ID:</span>
                <span>#<?php echo str_pad($booking_id, 6, '0', STR_PAD_LEFT); ?></span>
            </div>
            
            <div class="detail-row">
                <span style="font-weight: 600;">Payment Method:</span>
                <span><?php echo ucfirst(str_replace('_', ' ', $booking['payment_method'])); ?></span>
            </div>
            
            <div class="detail-row">
                <span style="font-weight: 600;">Total Amount:</span>
                <span style="color: #e94560; font-weight: 700;">$<?php echo number_format($booking['total_amount'], 2); ?></span>
            </div>
            
            <div class="detail-row">
                <span style="font-weight: 600;">Status:</span>
                <span style="color: #28a745; font-weight: 700;">Confirmed</span>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="mybookings.php" class="action-btn btn-primary">
                <i class="fas fa-calendar-alt"></i> View My Bookings
            </a>
            <a href="../index.php" class="action-btn btn-secondary">
                <i class="fas fa-home"></i> Back to Home
            </a>
            <a href="#" class="action-btn btn-outline" onclick="window.print()">
                <i class="fas fa-print"></i> Print Ticket
            </a>
        </div>
        
        <p style="margin-top: 30px; color: #666; font-size: 14px;">
            <i class="fas fa-info-circle"></i> Please arrive at least 30 minutes before the showtime.
        </p>
    </div>

    <footer style="background: #1a1a2e; color: white; padding: 30px 0; margin-top: 50px;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; text-align: center;">
            <p>&copy; <?php echo date('Y'); ?> CineBook. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Booking confirmed!');
            
            // Auto-scroll to top
            window.scrollTo(0, 0);
            
            // Print button functionality
            document.querySelector('.btn-outline').addEventListener('click', function(e) {
                e.preventDefault();
                window.print();
            });
        });
    </script>
</body>
</html>