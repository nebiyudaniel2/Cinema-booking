<?php

require_once '../includes/config.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

$pageTitle = "Movies - Cinema Booking";
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
    
    <style>
        .page-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .page-header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        .movies-container {
            padding: 20px 0;
        }
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        .filter-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }
        .movie-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .movie-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .movie-poster {
            height: 350px;
            overflow: hidden;
            position: relative;
        }
        .movie-poster img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .movie-card:hover .movie-poster img {
            transform: scale(1.05);
        }
        .movie-rating {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .movie-info {
            padding: 20px;
        }
        .movie-title {
            font-size: 1.25rem;
            margin-bottom: 10px;
            color: #1a1a2e;
        }
        .movie-meta {
            display: flex;
            justify-content: space-between;
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .movie-genre {
            color: #e94560;
            font-weight: 500;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .movie-description {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .movie-btn {
            width: 100%;
            padding: 12px;
            background: #e94560;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .movie-btn:hover {
            background: #d43f57;
        }
        .no-movies {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }
        .page-btn {
            padding: 10px 15px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .page-btn.active {
            background: #e94560;
            color: white;
            border-color: #e94560;
        }
        .page-btn:hover:not(.active) {
            background: #f5f5f5;
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
            <nav>
                <div style="display: flex; gap: 20px; align-items: center;">
                    <a href="../index.php" style="color: white; text-decoration: none;">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <a href="movies.php" style="color: white; text-decoration: none; background: rgba(255,255,255,0.1); padding: 8px 15px; border-radius: 5px;">
                        <i class="fas fa-film"></i> Movies
                    </a>
                    <a href="mybookings.php" style="color: white; text-decoration: none;">
                        <i class="fas fa-calendar-alt"></i> My Bookings
                    </a>
                    <a href="../auth/logout.php" style="color: white; text-decoration: none; background: #e94560; padding: 8px 15px; border-radius: 5px;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Page Header -->
    <div class="page-header">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <h1><i class="fas fa-film"></i> Now Showing</h1>
            <p>Book tickets for the latest movies</p>
        </div>
    </div>

    <!-- Main Content -->
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <!-- Filters -->
        <div class="filter-section">
            <div class="filter-row">
                <div class="filter-group">
                    <label><i class="fas fa-filter"></i> Genre</label>
                    <select class="filter-select" id="genreFilter">
                        <option value="">All Genres</option>
                        <option value="action">Action</option>
                        <option value="comedy">Comedy</option>
                        <option value="drama">Drama</option>
                        <option value="sci-fi">Sci-Fi</option>
                        <option value="horror">Horror</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-sort"></i> Sort By</label>
                    <select class="filter-select" id="sortFilter">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="rating">Highest Rated</option>
                        <option value="title">Title A-Z</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-star"></i> Rating</label>
                    <select class="filter-select" id="ratingFilter">
                        <option value="">All Ratings</option>
                        <option value="PG">PG</option>
                        <option value="PG-13">PG-13</option>
                        <option value="R">R</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Movies Grid -->
        <div class="movies-container">
            <div class="movies-grid" id="moviesGrid">
                <?php
                $sql = "SELECT * FROM movies WHERE is_active = TRUE";
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0):
                    while($movie = $result->fetch_assoc()):
                ?>
                <div class="movie-card" data-genre="<?php echo strtolower($movie['genre']); ?>" data-rating="<?php echo $movie['rating']; ?>">
                    <div class="movie-poster">
                        <img src="<?php echo $movie['poster_url'] ?: 'https://via.placeholder.com/300x450'; ?>" 
                             alt="<?php echo htmlspecialchars($movie['title']); ?>">
                        <div class="movie-rating">
                            <i class="fas fa-star"></i> <?php echo $movie['rating']; ?>
                        </div>
                    </div>
                    <div class="movie-info">
                        <h3 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                        <div class="movie-meta">
                            <span><i class="far fa-clock"></i> <?php echo floor($movie['duration']/60); ?>h <?php echo $movie['duration']%60; ?>m</span>
                            <span><i class="far fa-calendar"></i> <?php echo date('Y', strtotime($movie['release_date'])); ?></span>
                        </div>
                        <p class="movie-genre"><?php echo $movie['genre']; ?></p>
                        <p class="movie-description"><?php echo substr(htmlspecialchars($movie['description']), 0, 150); ?>...</p>
                        <button class="movie-btn" onclick="bookMovie(<?php echo $movie['id']; ?>)">
                            <i class="fas fa-ticket-alt"></i> Book Tickets
                        </button>
                    </div>
                </div>
                <?php 
                    endwhile;
                else: 
                ?>
                <div class="no-movies" style="grid-column: 1/-1;">
                    <i class="fas fa-film" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
                    <h3>No Movies Available</h3>
                    <p>Check back soon for new releases!</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <button class="page-btn">Next</button>
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
                    <h3 style="color: #e94560; margin-bottom: 15px;">Quick Links</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="../index.php" style="color: white; text-decoration: none;">Home</a></li>
                        <li><a href="movies.php" style="color: white; text-decoration: none;">Movies</a></li>
                        <li><a href="mybookings.php" style="color: white; text-decoration: none;">My Bookings</a></li>
                    </ul>
                </div>
                <div>
                    <h3 style="color: #e94560; margin-bottom: 15px;">Contact</h3>
                    <p><i class="fas fa-phone"></i> (123) 456-7890</p>
                    <p><i class="fas fa-envelope"></i> info@cinebook.com</p>
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
        // Movies page specific JavaScript
        function bookMovie(movieId) {
            window.location.href = `seats.php?movie=${movieId}`;
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Movies page loaded');
            
            // Filter functionality
            const genreFilter = document.getElementById('genreFilter');
            const sortFilter = document.getElementById('sortFilter');
            const ratingFilter = document.getElementById('ratingFilter');
            const movieCards = document.querySelectorAll('.movie-card');
            
            function filterMovies() {
                const selectedGenre = genreFilter.value;
                const selectedRating = ratingFilter.value;
                
                movieCards.forEach(card => {
                    const cardGenre = card.getAttribute('data-genre');
                    const cardRating = card.getAttribute('data-rating');
                    
                    let showCard = true;
                    
                    // Filter by genre
                    if (selectedGenre && !cardGenre.includes(selectedGenre)) {
                        showCard = false;
                    }
                    
                    // Filter by rating
                    if (selectedRating && cardRating !== selectedRating) {
                        showCard = false;
                    }
                    
                    // Show/hide card
                    card.style.display = showCard ? 'block' : 'none';
                });
            }
            
            // Add event listeners to filters
            if (genreFilter) genreFilter.addEventListener('change', filterMovies);
            if (ratingFilter) ratingFilter.addEventListener('change', filterMovies);
            
            // Page buttons
            document.querySelectorAll('.page-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remove active class from all buttons
                    document.querySelectorAll('.page-btn').forEach(b => {
                        b.classList.remove('active');
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // In a real app, this would load new page of movies
                    alert(`Loading page ${this.textContent}...`);
                });
            });
        });
    </script>
</body>
</html>