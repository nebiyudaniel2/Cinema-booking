<?php
// admin/bookings.php
require_once '../includes/config.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('../index.php?error=Access denied');
}

$pageTitle = "Manage Bookings";
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
        /* Reuse admin container and sidebar styles */
        .admin-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }
        
        /* Same sidebar styles as movies.php */
        .admin-sidebar {
            background: #1a1a2e;
            color: white;
            padding: 20px 0;
        }
        
        .admin-logo {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-logo h2 {
            color: #e94560;
            font-size: 1.5rem;
        }
        
        .admin-nav ul {
            list-style: none;
            padding: 0;
        }
        
        .admin-nav li {
            margin: 5px 0;
        }
        
        .admin-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            text-decoration: none;
            padding: 15px 25px;
            transition: all 0.3s;
        }
        
        .admin-nav a:hover,
        .admin-nav a.active {
            background: rgba(233, 69, 96, 0.1);
            border-left: 4px solid #e94560;
            color: #e94560;
        }
        
        .admin-nav i {
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .admin-main {
            padding: 20px;
            background: #f8f9fa;
        }
        
        .page-header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-header h1 {
            color: #1a1a2e;
            margin: 0;
        }
        
        /* Stats Cards */
        .booking-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #e94560;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Bookings Table */
        .bookings-table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .bookings-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .bookings-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #1a1a2e;
            border-bottom: 2px solid #e94560;
        }
        
        .bookings-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        .bookings-table tr:hover {
            background: #f9f9f9;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .action-btns {
            display: flex;
            gap: 8px;
        }
        
        .btn-view {
            background: #0f3460;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-cancel {
            background: #e94560;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-confirm {
            background: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Filters */
        .filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }
        
        .form-group {
            margin-bottom: 0;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .filter-btn {
            background: #e94560;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            height: 38px;
        }
        
        /* Export buttons */
        .export-btns {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .export-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .export-btn.csv {
            background: #0f3460;
        }
        
        .export-btn.pdf {
            background: #e94560;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <h2><i class="fas fa-film"></i> CineBook Admin</h2>
            </div>
            
            <nav class="admin-nav">
                <ul>
                    <li>
                        <a href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="movies.php">
                            <i class="fas fa-film"></i> Movies
                        </a>
                    </li>
                    <li>
                        <a href="bookings.php" class="active">
                            <i class="fas fa-ticket-alt"></i> Bookings
                        </a>
                    </li>
                    <li>
                        <a href="users.php">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <li>
                        <a href="../auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Page Header -->
            <div class="page-header">
                <h1><i class="fas fa-ticket-alt"></i> Manage Bookings</h1>
                <div style="color: #666;">
                    Total: <strong><?php 
                        $total = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
                        echo $total;
                    ?></strong> bookings
                </div>
            </div>

            <!-- Booking Stats -->
            <div class="booking-stats">
                <?php
                $confirmed = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'confirmed'")->fetch_assoc()['count'];
                $pending = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'")->fetch_assoc()['count'];
                $cancelled = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'cancelled'")->fetch_assoc()['count'];
                $today = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE DATE(booking_date) = CURDATE()")->fetch_assoc()['count'];
                $revenue = $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE status = 'confirmed'")->fetch_assoc()['total'];
                $revenue = $revenue ? $revenue : 0;
                ?>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $confirmed; ?></div>
                    <div class="stat-label">Confirmed Bookings</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $pending; ?></div>
                    <div class="stat-label">Pending Bookings</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $cancelled; ?></div>
                    <div class="stat-label">Cancelled Bookings</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">$<?php echo number_format($revenue, 2); ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $today; ?></div>
                    <div class="stat-label">Today's Bookings</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters">
                <div class="filter-row">
                    <div class="form-group">
                        <label>Booking ID / User</label>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search booking ID or username">
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Date Range</label>
                        <input type="date" class="form-control" id="dateFrom" value="<?php echo date('Y-m-d', strtotime('-7 days')); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>to</label>
                        <input type="date" class="form-control" id="dateTo" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <button class="filter-btn" onclick="filterBookings()">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
                
                <!-- Export Buttons -->
                <div class="export-btns">
                    <button class="export-btn" onclick="exportBookings('csv')">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                    <button class="export-btn pdf" onclick="exportBookings('pdf')">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button class="export-btn csv" onclick="exportBookings('excel')">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="bookings-table-container">
                <div class="table-responsive">
                    <table class="bookings-table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>User</th>
                                <th>Movie</th>
                                <th>Date & Time</th>
                                <th>Seats</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="bookingsTableBody">
                            <?php
                            // Get bookings with user and movie info
                            $sql = "SELECT b.*, u.username, m.title, s.screening_time 
                                   FROM bookings b
                                   JOIN users u ON b.user_id = u.id
                                   JOIN screenings s ON b.screening_id = s.id
                                   JOIN movies m ON s.movie_id = m.id
                                   ORDER BY b.booking_date DESC 
                                   LIMIT 20";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0):
                                while($booking = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <strong>#<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></strong><br>
                                    <small style="color: #666;"><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                <td><?php echo htmlspecialchars($booking['title']); ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($booking['screening_time'])); ?></td>
                                <td>
                                    <?php
                                    // Get seats for this booking (simplified)
                                    $seat_sql = "SELECT COUNT(*) as seat_count FROM booking_seats WHERE booking_id = ?";
                                    $stmt = $conn->prepare($seat_sql);
                                    $stmt->bind_param("i", $booking['id']);
                                    $stmt->execute();
                                    $seats = $stmt->get_result()->fetch_assoc();
                                    echo $seats['seat_count'] . ' seats';
                                    ?>
                                </td>
                                <td>$<?php echo number_format($booking['total_amount'], 2); ?></td>
                                <td>
                                    <span style="background: #f0f0f0; padding: 3px 8px; border-radius: 3px; font-size: 12px;">
                                        <?php echo ucfirst($booking['payment_method'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $booking['status']; ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-view" onclick="viewBooking(<?php echo $booking['id']; ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        
                                        <?php if ($booking['status'] === 'pending'): ?>
                                            <button class="btn-confirm" onclick="confirmBooking(<?php echo $booking['id']; ?>)">
                                                <i class="fas fa-check"></i> Confirm
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($booking['status'] !== 'cancelled'): ?>
                                            <button class="btn-cancel" onclick="cancelBooking(<?php echo $booking['id']; ?>)">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            else: 
                            ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-ticket-alt" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                                    <h3 style="color: #666;">No Bookings Found</h3>
                                    <p>No bookings have been made yet.</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- View Booking Modal -->
    <div id="viewBookingModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h3><i class="fas fa-eye"></i> Booking Details</h3>
                <button class="close-modal" onclick="closeViewModal()">&times;</button>
            </div>
            
            <div id="bookingDetails">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="../js/script.js"></script>
    <script>
        // View booking details
        function viewBooking(bookingId) {
            // Show loading
            const modalContent = document.getElementById('bookingDetails');
            modalContent.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading details...</p></div>';
            
            // Open modal
            document.getElementById('viewBookingModal').style.display = 'flex';
            
            // In real app, fetch booking details via AJAX
            setTimeout(() => {
                modalContent.innerHTML = `
                    <div style="padding: 20px;">
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                            <h4 style="margin: 0 0 10px; color: #1a1a2e;">Booking #${String(bookingId).padStart(6, '0')}</h4>
                            <div style="display: flex; gap: 20px; margin-top: 15px;">
                                <div>
                                    <small style="color: #666;">Status</small>
                                    <div><span class="status-badge status-confirmed" style="display: inline-block;">Confirmed</span></div>
                                </div>
                                <div>
                                    <small style="color: #666;">Amount</small>
                                    <div><strong>$75.00</strong></div>
                                </div>
                                <div>
                                    <small style="color: #666;">Date</small>
                                    <div>${new Date().toLocaleDateString()}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h5 style="color: #1a1a2e; margin-bottom: 10px;">User Information</h5>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <small style="color: #666;">Name</small>
                                    <div>John Doe</div>
                                </div>
                                <div>
                                    <small style="color: #666;">Email</small>
                                    <div>john@example.com</div>
                                </div>
                                <div>
                                    <small style="color: #666;">Phone</small>
                                    <div>+1 (123) 456-7890</div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h5 style="color: #1a1a2e; margin-bottom: 10px;">Movie Details</h5>
                            <div style="display: flex; gap: 15px;">
                                <img src="https://via.placeholder.com/80x120" alt="Movie" style="border-radius: 5px;">
                                <div>
                                    <div style="font-weight: 600; margin-bottom: 5px;">Avengers: Endgame</div>
                                    <div style="color: #666; font-size: 14px;">Action • 3h 1m • PG-13</div>
                                    <div style="margin-top: 10px;">
                                        <small style="color: #666;">Showtime:</small>
                                        <div>Today, 7:00 PM</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h5 style="color: #1a1a2e; margin-bottom: 10px;">Seats</h5>
                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                <span style="background: #e94560; color: white; padding: 5px 10px; border-radius: 20px;">A10</span>
                                <span style="background: #e94560; color: white; padding: 5px 10px; border-radius: 20px;">A11</span>
                                <span style="background: #e94560; color: white; padding: 5px 10px; border-radius: 20px;">A12</span>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h5 style="color: #1a1a2e; margin-bottom: 10px;">Payment Information</h5>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <small style="color: #666;">Method</small>
                                    <div>Credit Card</div>
                                </div>
                                <div>
                                    <small style="color: #666;">Card Number</small>
                                    <div>**** **** **** 1234</div>
                                </div>
                                <div>
                                    <small style="color: #666;">Transaction ID</small>
                                    <div>TXN-${String(Math.floor(Math.random() * 1000000)).padStart(6, '0')}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 30px;">
                            <button style="padding: 10px 20px; background: #0f3460; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                <i class="fas fa-print"></i> Print Ticket
                            </button>
                            <button style="padding: 10px 20px; background: #e94560; color: white; border: none; border-radius: 5px; cursor: pointer;" onclick="closeViewModal()">
                                Close
                            </button>
                        </div>
                    </div>
                `;
            }, 1000);
        }
        
        function closeViewModal() {
            document.getElementById('viewBookingModal').style.display = 'none';
        }
        
        // Confirm booking
        function confirmBooking(bookingId) {
            if (confirm('Are you sure you want to confirm this booking?')) {
                const btn = event.target.closest('.btn-confirm');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;
                
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-check"></i> Confirmed';
                    btn.style.background = '#6c757d';
                    btn.disabled = true;
                    
                    // Update status badge
                    const row = btn.closest('tr');
                    const statusBadge = row.querySelector('.status-badge');
                    statusBadge.className = 'status-badge status-confirmed';
                    statusBadge.textContent = 'Confirmed';
                    
                    alert('Booking confirmed successfully!');
                }, 1500);
            }
        }
        
        // Cancel booking
        function cancelBooking(bookingId) {
            if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
                const btn = event.target.closest('.btn-cancel');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;
                
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-times"></i> Cancelled';
                    btn.style.background = '#6c757d';
                    btn.disabled = true;
                    
                    // Update status badge
                    const row = btn.closest('tr');
                    const statusBadge = row.querySelector('.status-badge');
                    statusBadge.className = 'status-badge status-cancelled';
                    statusBadge.textContent = 'Cancelled';
                    
                    alert('Booking cancelled successfully!');
                }, 1500);
            }
        }
        
        // Filter bookings
        function filterBookings() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            
            const rows = document.querySelectorAll('#bookingsTableBody tr');
            
            rows.forEach(row => {
                const bookingId = row.cells[0].textContent.toLowerCase();
                const username = row.cells[1].textContent.toLowerCase();
                const movie = row.cells[2].textContent.toLowerCase();
                const date = row.cells[3].textContent.toLowerCase();
                const status = row.querySelector('.status-badge').textContent.toLowerCase();
                
                let showRow = true;
                
                // Search filter
                if (searchTerm && !bookingId.includes(searchTerm) && !username.includes(searchTerm)) {
                    showRow = false;
                }
                
                // Status filter
                if (statusFilter && status !== statusFilter) {
                    showRow = false;
                }
                
                // Date filter (simplified)
                if (dateFrom && dateTo) {
                    // In real app, implement actual date filtering
                }
                
                // Show/hide row
                row.style.display = showRow ? '' : 'none';
            });
        }
        
        // Export bookings
        function exportBookings(format) {
            const formats = {
                'csv': 'CSV',
                'pdf': 'PDF',
                'excel': 'Excel'
            };
            
            alert(`Exporting bookings as ${formats[format]}...\n\nThis would generate and download the file in a real application.`);
            
            // In real app, this would make an AJAX request to generate and download the file
        }
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('viewBookingModal');
            if (event.target === modal) {
                closeViewModal();
            }
        });
    </script>
</body>
</html>