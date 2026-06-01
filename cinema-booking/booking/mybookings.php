<?php
// booking/mybookings.php
require_once '../includes/config.php';

// if (!isLoggedIn()) {
//     redirect('../auth/login.php');
// }

$pageTitle = "My Bookings";
include '../includes/header.php';
?>

<div class="container">
    <h1 class="mb-4"><i class="fas fa-calendar-alt"></i> My Bookings</h1>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success animate-fade">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>
    
    <div class="bookings-container">
        <?php
        // Get user's bookings
        $sql = "SELECT b.*, m.title, s.screening_time, s.hall_number 
                FROM bookings b
                JOIN screenings s ON b.screening_id = s.id
                JOIN movies m ON s.movie_id = m.id
                WHERE b.user_id = ?
                ORDER BY b.booking_date DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0):
            while($booking = $result->fetch_assoc()):
        ?>
        <div class="booking-card animate-fade">
            <div class="booking-header">
                <h3><?php echo htmlspecialchars($booking['title']); ?></h3>
                <span class="booking-status <?php echo $booking['status']; ?>">
                    <?php echo ucfirst($booking['status']); ?>
                </span>
            </div>
            
            <div class="booking-details">
                <p><i class="far fa-calendar"></i> 
                   <?php echo date('F j, Y', strtotime($booking['screening_time'])); ?></p>
                <p><i class="far fa-clock"></i> 
                   <?php echo date('g:i A', strtotime($booking['screening_time'])); ?></p>
                <p><i class="fas fa-door-open"></i> Hall <?php echo $booking['hall_number']; ?></p>
                <p><i class="fas fa-money-bill-wave"></i> $<?php echo number_format($booking['total_amount'], 2); ?></p>
            </div>
            
            <div class="booking-actions">
                <a href="#" class="btn btn-sm btn-outline">
                    <i class="fas fa-eye"></i> View Details
                </a>
                <?php if ($booking['status'] === 'confirmed'): ?>
                    <a href="#" class="btn btn-sm btn-outline">
                        <i class="fas fa-print"></i> Print Ticket
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php 
            endwhile;
        else: 
        ?>
        <div class="alert alert-info animate-fade">
            <i class="fas fa-info-circle"></i> You haven't made any bookings yet.
            <a href="movies.php" class="alert-link">Browse movies</a> to get started!
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.bookings-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.booking-card {
    background: var(--white);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-light);
    border-left: 4px solid var(--primary-color);
}

.booking-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.booking-status {
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-sm);
    font-size: 0.875rem;
    font-weight: 600;
}

.booking-status.confirmed {
    background: rgba(40, 167, 69, 0.1);
    color: var(--success-color);
}

.booking-status.pending {
    background: rgba(255, 193, 7, 0.1);
    color: var(--warning-color);
}

.booking-status.cancelled {
    background: rgba(220, 53, 69, 0.1);
    color: var(--error-color);
}

.booking-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.booking-actions {
    display: flex;
    gap: 0.5rem;
}
</style>

<?php include '../includes/footer.php'; ?>