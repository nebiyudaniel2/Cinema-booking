<?php
// booking/seats.php
require_once '../includes/config.php';

// if (!isLoggedIn()) {
//     redirect('../auth/login.php');
// }

// Get movie ID from URL
$movie_id = isset($_GET['movie']) ? intval($_GET['movie']) : 0;
if (!$movie_id) redirect('movies.php');

// Get movie details
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
if (!$movie) redirect('movies.php');

$pageTitle = "Seat Selection - " . $movie['title'];
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
        /* Seats page styles */
        .seats-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .movie-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .movie-header h1 {
            margin-bottom: 10px;
        }
        .movie-meta {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }
        .screen-container {
            background: #1a1a2e;
            padding: 30px;
            border-radius: 10px;
            margin: 30px 0;
        }
        .screen {
            background: linear-gradient(to bottom, #666, #999);
            height: 50px;
            margin: 0 auto 40px;
            width: 80%;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 3px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }
        .seats-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 10px;
            max-width: 800px;
            margin: 0 auto;
        }
        .seat {
            aspect-ratio: 1;
            background: #2ecc71;
            border-radius: 8px 8px 4px 4px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }
        .seat:hover {
            transform: scale(1.1);
        }
        .seat.selected {
            background: #e94560;
        }
        .seat.booked {
            background: #7f8c8d;
            cursor: not-allowed;
        }
        .seat.vip {
            background: #f39c12;
        }
        .legend {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .legend-color {
            width: 25px;
            height: 25px;
            border-radius: 5px;
        }
        .booking-summary {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-top: 40px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .summary-total {
            font-size: 1.5rem;
            font-weight: 700;
            color: #e94560;
            border-bottom: none;
        }
        .checkout-btn {
            width: 100%;
            padding: 18px;
            background: #e94560;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .checkout-btn:hover {
            background: #d43f57;
        }
        .checkout-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .seat-info {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <header style="background: #1a1a2e; color: white; padding: 15px 0; position: sticky; top: 0; z-index: 1000;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center;">
            <a href="../index.php" style="color: white; text-decoration: none; font-size: 1.5rem; font-weight: bold;">
                <i class="fas fa-film"></i> CineBook
            </a>
            <a href="movies.php" style="color: white; text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Back to Movies
            </a>
        </div>
    </header>

    <div class="seats-container">
        <!-- Movie Info -->
        <div class="movie-header">
            <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
            <p><?php echo substr(htmlspecialchars($movie['description']), 0, 200); ?>...</p>
            <div class="movie-meta">
                <span><i class="fas fa-tag"></i> <?php echo $movie['genre']; ?></span>
                <span><i class="far fa-clock"></i> <?php echo floor($movie['duration']/60); ?>h <?php echo $movie['duration']%60; ?>m</span>
                <span><i class="fas fa-star"></i> <?php echo $movie['rating']; ?></span>
            </div>
        </div>

        <!-- Seating Chart -->
        <div class="screen-container">
            <div class="screen">
                <i class="fas fa-film"></i> SCREEN <i class="fas fa-film"></i>
            </div>
            
            <div class="seats-grid" id="seatsGrid">
                <!-- Seats will be generated by JavaScript -->
            </div>
            
            <div class="seat-info">
                <p>Click on available seats to select. Maximum 8 seats per booking.</p>
            </div>
            
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color" style="background: #2ecc71;"></div>
                    <span>Available</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #e94560;"></div>
                    <span>Selected</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #7f8c8d;"></div>
                    <span>Booked</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #f39c12;"></div>
                    <span>VIP Seat</span>
                </div>
            </div>
        </div>

        <!-- Booking Summary -->
        <div class="booking-summary">
            <h2>Booking Summary</h2>
            <div class="summary-item">
                <span>Movie:</span>
                <span><?php echo htmlspecialchars($movie['title']); ?></span>
            </div>
            <div class="summary-item">
                <span>Selected Seats:</span>
                <span id="selectedSeatsText">None</span>
            </div>
            <div class="summary-item">
                <span>Price per Seat:</span>
                <span id="seatPrice">$12.50</span>
            </div>
            <div class="summary-item summary-total">
                <span>Total Amount:</span>
                <span id="totalAmount">$0.00</span>
            </div>
            
            <form id="bookingForm" action="checkout.php" method="POST">
                <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
                <input type="hidden" name="selected_seats" id="selectedSeatsInput" value="">
                <input type="hidden" name="total_amount" id="totalAmountInput" value="0">
                
                <button type="submit" class="checkout-btn" id="checkoutBtn" disabled>
                    <i class="fas fa-shopping-cart"></i> Proceed to Checkout
                </button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: #1a1a2e; color: white; padding: 30px 0; margin-top: 50px;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; text-align: center;">
            <p>&copy; <?php echo date('Y'); ?> CineBook. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="../js/script.js"></script>
    <script>
        // Seats page specific JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Seats page loaded');
            
            const seatsGrid = document.getElementById('seatsGrid');
            const seatPrice = 12.50;
            const maxSeats = 8;
            let selectedSeats = [];
            
            // Generate seats (A1-J12)
            for (let row = 'A'.charCodeAt(0); row <= 'J'.charCodeAt(0); row++) {
                for (let seatNum = 1; seatNum <= 12; seatNum++) {
                    const seatLetter = String.fromCharCode(row);
                    const seatId = seatLetter + seatNum;
                    
                    // Randomly mark some seats as booked (for demo)
                    const isBooked = Math.random() < 0.2; // 20% chance
                    const isVip = (row >= 'H'.charCodeAt(0)); // Last 3 rows are VIP
                    
                    const seat = document.createElement('div');
                    seat.className = 'seat';
                    seat.textContent = seatId;
                    seat.setAttribute('data-seat', seatId);
                    
                    if (isBooked) {
                        seat.classList.add('booked');
                    } else if (isVip) {
                        seat.classList.add('vip');
                    }
                    
                    if (!isBooked) {
                        seat.addEventListener('click', function() {
                            toggleSeatSelection(this);
                        });
                    }
                    
                    seatsGrid.appendChild(seat);
                }
            }
            
            function toggleSeatSelection(seatElement) {
                const seatId = seatElement.getAttribute('data-seat');
                const isVip = seatElement.classList.contains('vip');
                const price = isVip ? seatPrice * 1.5 : seatPrice;
                
                if (seatElement.classList.contains('selected')) {
                    // Deselect
                    seatElement.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(seat => seat.id !== seatId);
                } else {
                    // Check max seats limit
                    if (selectedSeats.length >= maxSeats) {
                        alert(`You can select maximum ${maxSeats} seats.`);
                        return;
                    }
                    
                    // Select seat
                    seatElement.classList.add('selected');
                    selectedSeats.push({
                        id: seatId,
                        price: price,
                        isVip: isVip
                    });
                }
                
                updateBookingSummary();
            }
            
            function updateBookingSummary() {
                const total = selectedSeats.reduce((sum, seat) => sum + seat.price, 0);
                const seatsText = selectedSeats.length > 0 
                    ? selectedSeats.map(s => s.id).join(', ')
                    : 'None';
                
                // Update display
                document.getElementById('selectedSeatsText').textContent = seatsText;
                document.getElementById('totalAmount').textContent = '$' + total.toFixed(2);
                
                // Update hidden inputs
                document.getElementById('selectedSeatsInput').value = selectedSeats.map(s => s.id).join(',');
                document.getElementById('totalAmountInput').value = total.toFixed(2);
                
                // Enable/disable checkout button
                const checkoutBtn = document.getElementById('checkoutBtn');
                if (selectedSeats.length > 0) {
                    checkoutBtn.disabled = false;
                    checkoutBtn.style.background = '#e94560';
                } else {
                    checkoutBtn.disabled = true;
                    checkoutBtn.style.background = '#ccc';
                }
            }
            
            // Form submission
            document.getElementById('bookingForm').addEventListener('submit', function(e) {
                if (selectedSeats.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one seat.');
                    return;
                }
                
                // Show loading
                const btn = document.getElementById('checkoutBtn');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                btn.disabled = true;
            });
        });
    </script>
</body>
</html>