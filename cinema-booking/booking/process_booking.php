<?php
// booking/process_booking.php
require_once '../includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

// Check if booking data exists in session
if (!isset($_SESSION['booking_data'])) {
    redirect('movies.php?error=No booking data found');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get booking data from session
    $booking_data = $_SESSION['booking_data'];
    $movie_id = $booking_data['movie_id'];
    $selected_seats = $booking_data['selected_seats'];
    $total_amount = $booking_data['total_amount'];
    
    // Get form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $payment_method = $_POST['payment_method'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Create booking record
        $booking_sql = "INSERT INTO bookings (user_id, screening_id, total_amount, payment_method, status) 
                       VALUES (?, ?, ?, ?, 'confirmed')";
        
        // For simplicity, we'll use screening_id = 1
        $screening_id = 1;
        
        $stmt = $conn->prepare($booking_sql);
        $stmt->bind_param("iids", $_SESSION['user_id'], $screening_id, $total_amount, $payment_method);
        $stmt->execute();
        
        $booking_id = $conn->insert_id;
        
        // In real application, you would:
        // 1. Check seat availability
        // 2. Mark seats as booked
        // 3. Create booking_seats records
        // 4. Send confirmation email
        
        // Clear booking data from session
        unset($_SESSION['booking_data']);
        
        // Commit transaction
        $conn->commit();
        
        // Store booking ID in session for confirmation page
        $_SESSION['last_booking_id'] = $booking_id;
        
        // Redirect to confirmation page
        redirect('booking_confirmation.php?booking_id=' . $booking_id);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        redirect('checkout.php?error=Booking failed: ' . urlencode($e->getMessage()));
    }
} else {
    redirect('checkout.php');
}
?>