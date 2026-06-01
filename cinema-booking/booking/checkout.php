<?php
// booking/checkout.php
require_once '../includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

// Check if we have booking data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from seat selection
    $movie_id = isset($_POST['movie_id']) ? intval($_POST['movie_id']) : 0;
    $selected_seats = isset($_POST['selected_seats']) ? explode(',', $_POST['selected_seats']) : [];
    $total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
    
    // Validate data
    if ($movie_id <= 0 || empty($selected_seats) || $total_amount <= 0) {
        redirect('movies.php?error=Invalid booking data');
    }
    
    // Get movie details
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $movie = $stmt->get_result()->fetch_assoc();
    
    if (!$movie) {
        redirect('movies.php?error=Movie not found');
    }
    
    // Store booking data in session for processing
    $_SESSION['booking_data'] = [
        'movie_id' => $movie_id,
        'movie_title' => $movie['title'],
        'selected_seats' => $selected_seats,
        'seat_count' => count($selected_seats),
        'total_amount' => $total_amount,
        'booking_time' => date('Y-m-d H:i:s')
    ];
    
} elseif (isset($_SESSION['booking_data'])) {
    // Use session data if available (for page refresh)
    $booking_data = $_SESSION['booking_data'];
    $movie_id = $booking_data['movie_id'];
    $selected_seats = $booking_data['selected_seats'];
    $total_amount = $booking_data['total_amount'];
    
    // Get movie details
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $movie = $stmt->get_result()->fetch_assoc();
} else {
    // No booking data, redirect to movies
    redirect('movies.php?error=Please select seats first');
}

$pageTitle = "Checkout - " . $movie['title'];
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
        /* Checkout page styles */
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .checkout-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .checkout-header h1 {
            margin-bottom: 10px;
        }
        .checkout-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        @media (max-width: 768px) {
            .checkout-content {
                grid-template-columns: 1fr;
            }
        }
        .booking-summary {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
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
        .payment-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .form-section {
            margin-bottom: 30px;
        }
        .form-section h3 {
            color: #1a1a2e;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e94560;
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
            box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.1);
        }
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .payment-method {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-method:hover {
            border-color: #e94560;
        }
        .payment-method.selected {
            border-color: #e94560;
            background: rgba(233, 69, 96, 0.05);
        }
        .payment-method i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }
        .payment-btn {
            width: 100%;
            padding: 18px;
            background: #28a745;
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
        .payment-btn:hover {
            background: #218838;
        }
        .payment-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .timer {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-top: 20px;
            border: 1px solid #ffeaa7;
        }
        .timer span {
            font-weight: 700;
            color: #e94560;
        }
        .error-message {
            color: #e94560;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        .seat-badge {
            background: #e94560;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            margin-right: 5px;
            margin-bottom: 5px;
            display: inline-block;
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
            <div style="display: flex; gap: 20px; align-items: center;">
                <a href="movies.php" style="color: white; text-decoration: none;">
                    <i class="fas fa-film"></i> Movies
                </a>
                <a href="mybookings.php" style="color: white; text-decoration: none;">
                    <i class="fas fa-calendar-alt"></i> My Bookings
                </a>
                <a href="../auth/logout.php" style="color: white; text-decoration: none; background: #e94560; padding: 8px 15px; border-radius: 5px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <div class="checkout-container">
        <!-- Checkout Header -->
        <div class="checkout-header">
            <h1><i class="fas fa-shopping-cart"></i> Complete Your Booking</h1>
            <p>You're almost there! Please review your booking and enter payment details.</p>
        </div>

        <!-- Checkout Content -->
        <div class="checkout-content">
            <!-- Booking Summary -->
            <div class="booking-summary">
                <h2 style="color: #1a1a2e; margin-bottom: 25px;">Booking Summary</h2>
                
                <div class="summary-item">
                    <span>Movie:</span>
                    <span style="font-weight: 600;"><?php echo htmlspecialchars($movie['title']); ?></span>
                </div>
                
                <div class="summary-item">
                    <span>Genre:</span>
                    <span><?php echo htmlspecialchars($movie['genre']); ?></span>
                </div>
                
                <div class="summary-item">
                    <span>Duration:</span>
                    <span><?php echo floor($movie['duration']/60); ?>h <?php echo $movie['duration']%60; ?>m</span>
                </div>
                
                <div class="summary-item">
                    <span>Selected Seats:</span>
                    <span>
                        <?php foreach ($selected_seats as $seat): ?>
                            <span class="seat-badge"><?php echo htmlspecialchars($seat); ?></span>
                        <?php endforeach; ?>
                    </span>
                </div>
                
                <div class="summary-item">
                    <span>Number of Seats:</span>
                    <span><?php echo count($selected_seats); ?></span>
                </div>
                
                <div class="summary-item">
                    <span>Price per Seat:</span>
                    <span>$<?php echo number_format($total_amount / count($selected_seats), 2); ?></span>
                </div>
                
                <div class="summary-item summary-total">
                    <span>Total Amount:</span>
                    <span id="totalAmount">$<?php echo number_format($total_amount, 2); ?></span>
                </div>
                
                <!-- Timer -->
                <div class="timer">
                    <i class="fas fa-clock"></i> Complete your booking within: 
                    <span id="countdown">10:00</span>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="payment-form">
                <h2 style="color: #1a1a2e; margin-bottom: 25px;">Payment Details</h2>
                
                <form id="paymentForm" method="POST" action="process_booking.php">
                    <!-- Hidden fields -->
                    <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
                    <input type="hidden" name="selected_seats" value="<?php echo implode(',', $selected_seats); ?>">
                    <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
                    
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3><i class="fas fa-user"></i> Personal Information</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="first_name" class="form-control" required 
                                       value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>">
                                <div class="error-message" id="firstNameError"></div>
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                                <div class="error-message" id="lastNameError"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" required 
                                   value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>">
                            <div class="error-message" id="emailError"></div>
                        </div>
                        
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" class="form-control" required placeholder="+1 (123) 456-7890">
                            <div class="error-message" id="phoneError"></div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-section">
                        <h3><i class="fas fa-credit-card"></i> Payment Method</h3>
                        
                        <div class="payment-methods">
                            <div class="payment-method selected" data-method="credit_card">
                                <i class="far fa-credit-card"></i>
                                <span>Credit Card</span>
                            </div>
                            <div class="payment-method" data-method="debit_card">
                                <i class="far fa-credit-card"></i>
                                <span>Debit Card</span>
                            </div>
                            <div class="payment-method" data-method="paypal">
                                <i class="fab fa-paypal"></i>
                                <span>PayPal</span>
                            </div>
                            <div class="payment-method" data-method="netbanking">
                                <i class="fas fa-university"></i>
                                <span>Net Banking</span>
                            </div>
                        </div>
                        <input type="hidden" name="payment_method" id="paymentMethod" value="credit_card">
                    </div>

                    <!-- Card Details (shown only for card payments) -->
                    <div class="form-section" id="cardDetails">
                        <h3><i class="fas fa-credit-card"></i> Card Details</h3>
                        
                        <div class="form-group">
                            <label>Name on Card</label>
                            <input type="text" name="card_name" class="form-control" required>
                            <div class="error-message" id="cardNameError"></div>
                        </div>
                        
                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" name="card_number" class="form-control" required 
                                   placeholder="1234 5678 9012 3456" maxlength="19">
                            <div class="error-message" id="cardNumberError"></div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="month" name="expiry_date" class="form-control" required>
                                <div class="error-message" id="expiryError"></div>
                            </div>
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="text" name="cvv" class="form-control" required 
                                       placeholder="123" maxlength="4" pattern="[0-9]{3,4}">
                                <div class="error-message" id="cvvError"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" name="terms" required>
                            <span>I agree to the <a href="#" style="color: #e94560;">Terms & Conditions</a> and <a href="#" style="color: #e94560;">Cancellation Policy</a></span>
                        </label>
                        <div class="error-message" id="termsError"></div>
                    </div>

                    <!-- Payment Button -->
                    <button type="submit" class="payment-btn" id="submitBtn">
                        <i class="fas fa-lock"></i> Pay $<?php echo number_format($total_amount, 2); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: #1a1a2e; color: white; padding: 40px 0; margin-top: 60px;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
                <div>
                    <h3 style="color: #e94560; margin-bottom: 15px;"><i class="fas fa-film"></i> CineBook</h3>
                    <p>Your one-stop destination for movie tickets and entertainment.</p>
                </div>
                <div>
                    <h3 style="color: #e94560; margin-bottom: 15px;">Secure Payment</h3>
                    <p style="font-size: 14px; color: #ccc;">
                        <i class="fas fa-shield-alt"></i> Your payment is secured with 256-bit SSL encryption
                    </p>
                </div>
                <div>
                    <h3 style="color: #e94560; margin-bottom: 15px;">Need Help?</h3>
                    <p><i class="fas fa-phone"></i> (123) 456-7890</p>
                    <p><i class="fas fa-envelope"></i> support@cinebook.com</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                <p>&copy; <?php echo date('Y'); ?> CineBook. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="../js/script.js"></script>
    <script>
        // Checkout page specific JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Checkout page loaded');
            
            // Timer countdown (10 minutes)
            let timeLeft = 10 * 60; // 10 minutes in seconds
            const countdownElement = document.getElementById('countdown');
            
            function updateTimer() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                
                countdownElement.textContent = 
                    `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    alert('Booking time expired. Please start again.');
                    window.location.href = 'movies.php';
                } else if (timeLeft <= 60) {
                    countdownElement.style.color = '#e94560';
                    countdownElement.parentElement.style.background = '#f8d7da';
                    countdownElement.parentElement.style.borderColor = '#f5c6cb';
                }
                
                timeLeft--;
            }
            
            const timerInterval = setInterval(updateTimer, 1000);
            updateTimer(); // Initial call
            
            // Payment method selection
            const paymentMethods = document.querySelectorAll('.payment-method');
            const paymentMethodInput = document.getElementById('paymentMethod');
            const cardDetails = document.getElementById('cardDetails');
            
            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    // Remove selected class from all
                    paymentMethods.forEach(m => m.classList.remove('selected'));
                    
                    // Add selected class to clicked
                    this.classList.add('selected');
                    
                    // Update hidden input
                    const methodType = this.getAttribute('data-method');
                    paymentMethodInput.value = methodType;
                    
                    // Show/hide card details
                    if (methodType === 'credit_card' || methodType === 'debit_card') {
                        cardDetails.style.display = 'block';
                    } else {
                        cardDetails.style.display = 'none';
                    }
                });
            });
            
            // Card number formatting
            const cardNumberInput = document.querySelector('input[name="card_number"]');
            if (cardNumberInput) {
                cardNumberInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                    let formatted = value.replace(/(\d{4})/g, '$1 ').trim();
                    e.target.value = formatted.substring(0, 19);
                });
            }
            
            // CVV validation
            const cvvInput = document.querySelector('input[name="cvv"]');
            if (cvvInput) {
                cvvInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }
            
            // Form validation
            const paymentForm = document.getElementById('paymentForm');
            if (paymentForm) {
                paymentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    let isValid = true;
                    
                    // Reset errors
                    document.querySelectorAll('.error-message').forEach(el => {
                        el.style.display = 'none';
                    });
                    
                    // Validate personal information
                    const firstName = document.querySelector('input[name="first_name"]');
                    if (!firstName.value.trim()) {
                        document.getElementById('firstNameError').textContent = 'First name is required';
                        document.getElementById('firstNameError').style.display = 'block';
                        isValid = false;
                    }
                    
                    const lastName = document.querySelector('input[name="last_name"]');
                    if (!lastName.value.trim()) {
                        document.getElementById('lastNameError').textContent = 'Last name is required';
                        document.getElementById('lastNameError').style.display = 'block';
                        isValid = false;
                    }
                    
                    const email = document.querySelector('input[name="email"]');
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!email.value.trim()) {
                        document.getElementById('emailError').textContent = 'Email is required';
                        document.getElementById('emailError').style.display = 'block';
                        isValid = false;
                    } else if (!emailRegex.test(email.value)) {
                        document.getElementById('emailError').textContent = 'Please enter a valid email';
                        document.getElementById('emailError').style.display = 'block';
                        isValid = false;
                    }
                    
                    const phone = document.querySelector('input[name="phone"]');
                    if (!phone.value.trim()) {
                        document.getElementById('phoneError').textContent = 'Phone number is required';
                        document.getElementById('phoneError').style.display = 'block';
                        isValid = false;
                    }
                    
                    // Validate card details if card payment
                    if (paymentMethodInput.value === 'credit_card' || paymentMethodInput.value === 'debit_card') {
                        const cardName = document.querySelector('input[name="card_name"]');
                        if (!cardName.value.trim()) {
                            document.getElementById('cardNameError').textContent = 'Name on card is required';
                            document.getElementById('cardNameError').style.display = 'block';
                            isValid = false;
                        }
                        
                        const cardNumber = document.querySelector('input[name="card_number"]');
                        const cardDigits = cardNumber.value.replace(/\s+/g, '');
                        if (cardDigits.length < 16) {
                            document.getElementById('cardNumberError').textContent = 'Invalid card number';
                            document.getElementById('cardNumberError').style.display = 'block';
                            isValid = false;
                        }
                        
                        const expiryDate = document.querySelector('input[name="expiry_date"]');
                        if (!expiryDate.value) {
                            document.getElementById('expiryError').textContent = 'Expiry date is required';
                            document.getElementById('expiryError').style.display = 'block';
                            isValid = false;
                        } else {
                            const [year, month] = expiryDate.value.split('-');
                            const expiry = new Date(year, month - 1);
                            const today = new Date();
                            if (expiry < today) {
                                document.getElementById('expiryError').textContent = 'Card has expired';
                                document.getElementById('expiryError').style.display = 'block';
                                isValid = false;
                            }
                        }
                        
                        const cvv = document.querySelector('input[name="cvv"]');
                        if (!cvv.value.trim()) {
                            document.getElementById('cvvError').textContent = 'CVV is required';
                            document.getElementById('cvvError').style.display = 'block';
                            isValid = false;
                        } else if (cvv.value.length < 3 || cvv.value.length > 4) {
                            document.getElementById('cvvError').textContent = 'Invalid CVV';
                            document.getElementById('cvvError').style.display = 'block';
                            isValid = false;
                        }
                    }
                    
                    // Validate terms
                    const terms = document.querySelector('input[name="terms"]');
                    if (!terms.checked) {
                        document.getElementById('termsError').textContent = 'You must agree to the terms';
                        document.getElementById('termsError').style.display = 'block';
                        isValid = false;
                    }
                    
                    // If valid, submit form
                    if (isValid) {
                        // Show loading state
                        const submitBtn = document.getElementById('submitBtn');
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing Payment...';
                        submitBtn.disabled = true;
                        
                        // Simulate payment processing
                        setTimeout(() => {
                            paymentForm.submit();
                        }, 2000);
                    }
                });
            }
            
            // Auto-fill current month for expiry
            const expiryInput = document.querySelector('input[name="expiry_date"]');
            if (expiryInput && !expiryInput.value) {
                const today = new Date();
                const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);
                expiryInput.value = nextMonth.toISOString().slice(0, 7);
            }
        });
    </script>
</body>
</html>