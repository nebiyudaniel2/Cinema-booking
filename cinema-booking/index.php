<?php
require_once 'includes/config.php';
$pageTitle = "Cinema Booking - Home";
include 'includes/header.php';
?>

<div class="hero">
    <div class="hero-content">
        <h1 class="animate-fade">Welcome to <span style="color: #e94560;">CineBook</span></h1>
        <p class="animate-fade">Book tickets for the latest movies in just a few clicks</p>
        
        <?php if (!isLoggedIn()): ?>
            <a href="auth/register.php" class="btn btn-primary btn-lg animate-fade">
                <i class="fas fa-user-plus"></i> Get Started - It's Free!
            </a>
            <a href="auth/login.php" class="btn btn-outline btn-lg animate-fade ml-2">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
        <?php else: ?>
            <a href="booking/movies.php" class="btn btn-primary btn-lg animate-fade">
                <i class="fas fa-ticket-alt"></i> Book Tickets Now
            </a>
            <a href="booking/mybookings.php" class="btn btn-outline btn-lg animate-fade ml-2">
                <i class="fas fa-calendar-alt"></i> My Bookings
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <!-- Featured Movies -->
    <section class="featured-movies mt-5">
        <h2 class="text-center mb-4">Now Showing</h2>
        
        <div class="movies-grid">
            <?php
            $sql = "SELECT * FROM movies WHERE is_active = TRUE LIMIT 4";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0):
                while($movie = $result->fetch_assoc()):
            ?>
            <div class="movie-card animate-fade">
                <div class="movie-poster">
                    <img src="<?php echo $movie['poster_url']?>" 
                         alt="<?php echo htmlspecialchars($movie['title']); ?>">
                </div>
                <div class="movie-info">
                    <h3 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                    <div class="movie-meta">
                        <span><i class="far fa-clock"></i> <?php echo floor($movie['duration']/60); ?>h <?php echo $movie['duration']%60; ?>m</span>
                        <span class="movie-rating"><?php echo htmlspecialchars($movie['rating']); ?></span>
                    </div>
                    <p class="movie-genre"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($movie['genre']); ?></p>
                    <a href="booking/movies.php" class="btn btn-secondary btn-block">
                        <i class="fas fa-ticket-alt"></i> Book Now
                    </a>
                </div>
            </div>
            <?php 
                endwhile;
            else: 
            ?>
            <div class="alert alert-info" style="grid-column: 1/-1;">
                <i class="fas fa-info-circle"></i> No movies currently showing. Check back soon!
            </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="booking/movies.php" class="btn btn-primary">
                <i class="fas fa-film"></i> View All Movies
            </a>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works mt-5 pt-5">
        <h2 class="text-center mb-4">📱 How It Works</h2>
        
        <div class="steps-container">
            <div class="step animate-fade">
                <div class="step-icon">
                    <i class="fas fa-film"></i>
                    <span class="step-number">1</span>
                </div>
                <h3>Choose a Movie</h3>
                <p>Browse our collection of latest blockbusters and classic films</p>
            </div>
            
            <div class="step animate-fade">
                <div class="step-icon">
                    <i class="fas fa-couch"></i>
                    <span class="step-number">2</span>
                </div>
                <h3>Select Your Seats</h3>
                <p>Pick the perfect seats with our interactive seating chart</p>
            </div>
            
            <div class="step animate-fade">
                <div class="step-icon">
                    <i class="fas fa-credit-card"></i>
                    <span class="step-number">3</span>
                </div>
                <h3>Secure Payment</h3>
                <p>Pay safely with multiple payment options</p>
            </div>
            
            <div class="step animate-fade">
                <div class="step-icon">
                    <i class="fas fa-ticket-alt"></i>
                    <span class="step-number">4</span>
                </div>
                <h3>Get Your Tickets</h3>
                <p>Receive e-tickets instantly via email or download</p>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="why-us mt-5 pt-5">
        <h2 class="text-center mb-4">⭐ Why Choose CineBook?</h2>
        
        <div class="features-grid">
            <div class="feature-card animate-fade">
                <div class="feature-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>Instant Booking</h3>
                <p>Book tickets in under 2 minutes with our streamlined process</p>
            </div>
            
            <div class="feature-card animate-fade">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure Payments</h3>
                <p>Your payments are protected with bank-level security</p>
            </div>
            
            <div class="feature-card animate-fade">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Mobile Friendly</h3>
                <p>Book from any device - desktop, tablet, or mobile</p>
            </div>
            
            <div class="feature-card animate-fade">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>24/7 Support</h3>
                <p>Our customer support team is always here to help</p>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>