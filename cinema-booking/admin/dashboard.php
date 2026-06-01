<?php
require_once '../includes/config.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('../index.php?error=Access denied');
}

$pageTitle = "Admin Dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .admin-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }
        
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
        
        .admin-main {
            padding: 20px;
            background: #f8f9fa;
        }
        
        .admin-header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-welcome h1 {
            color: #1a1a2e;
            margin-bottom: 5px;
        }
        
        .admin-welcome p {
            color: #666;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .stat-info h3 {
            font-size: 2rem;
            color: #1a1a2e;
            margin: 0;
        }
        
        .stat-info p {
            color: #666;
            margin: 5px 0 0;
        }
        
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 992px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .chart-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .chart-card h3 {
            color: #1a1a2e;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        /* Recent Activity */
        .recent-activity {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .activity-list {
            list-style: none;
            padding: 0;
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e94560;
        }
        
        .activity-details h4 {
            margin: 0 0 5px;
            color: #1a1a2e;
        }
        
        .activity-details p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .activity-time {
            margin-left: auto;
            color: #999;
            font-size: 0.85rem;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .action-btn {
            background: white;
            border: 2px solid #e94560;
            color: #e94560;
            padding: 15px;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .action-btn:hover {
            background: #e94560;
            color: white;
        }

        @media (max-width: 768px) {
            .admin-container {
                grid-template-columns: 1fr;
            }
            
            .admin-sidebar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <h2><i class="fas fa-film"></i> CineBook Admin</h2>
            </div>
            
            <nav class="admin-nav">
                <ul>
                    <li>
                        <a href="dashboard.php" class="active">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="movies.php">
                            <i class="fas fa-film"></i> Movies
                        </a>
                    </li>
                    <li>
                        <a href="add_movie.php">
                            <i class="fas fa-plus-circle"></i> Add Movie
                        </a>
                    </li>
                    <li>
                        <a href="bookings.php">
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

        <main class="admin-main">
            <div class="admin-header">
                <div class="admin-welcome">
                    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                    <p>Here's what's happening with your cinema today.</p>
                </div>
                <div>
                    <span style="color: #666;"><?php echo date('F j, Y'); ?></span>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <?php
                // Get statistics
                $total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
                $today_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE DATE(booking_date) = CURDATE()")->fetch_assoc()['count'];
                $total_movies = $conn->query("SELECT COUNT(*) as count FROM movies WHERE is_active = 1")->fetch_assoc()['count'];
                $total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
                $revenue = $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE status = 'confirmed'")->fetch_assoc()['total'];
                $revenue = $revenue ? $revenue : 0;
                ?>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #e94560, #ff6b8b);">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_bookings; ?></h3>
                        <p>Total Bookings</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #28a745, #20c997);">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <h3>$<?php echo number_format($revenue, 2); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #0f3460, #1a73e8);">
                        <i class="fas fa-film"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_movies; ?></h3>
                        <p>Active Movies</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #6f42c1, #9c27b0);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_users; ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-grid">
                <!-- Booking Chart -->
                <div class="chart-card">
                    <h3>Bookings Overview</h3>
                    <canvas id="bookingsChart" height="250"></canvas>
                </div>
                
                <!-- Revenue Chart -->
                <div class="chart-card">
                    <h3>Revenue Sources</h3>
                    <canvas id="revenueChart" height="250"></canvas>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="recent-activity">
                <h3>Recent Activity</h3>
                <ul class="activity-list">
                    <?php
                    // Get recent bookings
                    $recent_sql = "SELECT b.*, u.username, m.title 
                                  FROM bookings b 
                                  JOIN users u ON b.user_id = u.id 
                                  JOIN screenings s ON b.screening_id = s.id
                                  JOIN movies m ON s.movie_id = m.id
                                  ORDER BY b.booking_date DESC LIMIT 5";
                    $recent_result = $conn->query($recent_sql);
                    
                    if ($recent_result->num_rows > 0):
                        while($activity = $recent_result->fetch_assoc()):
                    ?>
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div class="activity-details">
                            <h4>New Booking</h4>
                            <p><?php echo htmlspecialchars($activity['username']); ?> booked <?php echo htmlspecialchars($activity['title']); ?></p>
                        </div>
                        <div class="activity-time">
                            <?php echo date('h:i A', strtotime($activity['booking_date'])); ?>
                        </div>
                    </li>
                    <?php
                        endwhile;
                    else:
                    ?>
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="activity-details">
                            <h4>No Recent Activity</h4>
                            <p>No bookings have been made recently.</p>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="add_movie.php" class="action-btn">
                    <i class="fas fa-plus"></i> Add Movie
                </a>
                <a href="add_screening.php" class="action-btn">
                    <i class="fas fa-plus-circle"></i> Add Screening
                </a>
                <a href="reports.php" class="action-btn">
                    <i class="fas fa-download"></i> Generate Report
                </a>
                <a href="settings.php" class="action-btn">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script src="../js/script.js"></script>
    <script>
        // Dashboard Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Booking Chart
            const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
            const bookingsChart = new Chart(bookingsCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                        label: 'Bookings',
                        data: [65, 59, 80, 81, 56, 55, 40],
                        borderColor: '#e94560',
                        backgroundColor: 'rgba(233, 69, 96, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        }
                    }
                }
            });
            
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueChart = new Chart(revenueCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Ticket Sales', 'Food & Beverage', 'Merchandise', 'Other'],
                    datasets: [{
                        data: [70, 20, 5, 5],
                        backgroundColor: [
                            '#e94560',
                            '#0f3460',
                            '#28a745',
                            '#6f42c1'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // Update stats with real data
            function updateStats() {
                // In real app, you would fetch updated stats via AJAX
                console.log('Stats would be updated here via AJAX');
            }
            
            // Auto-refresh every 30 seconds
            setInterval(updateStats, 30000);
        });
    </script>
</body>
</html>