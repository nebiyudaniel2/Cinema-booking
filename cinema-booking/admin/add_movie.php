<?php
// admin/add_movie.php
require_once '../includes/config.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('../index.php?error=Access denied');
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_movie'])) {
    // Sanitize and validate inputs
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $duration = intval($_POST['duration']);
    $genre = trim($_POST['genre']);
    $release_date = !empty($_POST['release_date']) ? $_POST['release_date'] : NULL;
    $rating = $_POST['rating'];
    $poster_url = trim($_POST['poster_url']);
    $trailer_url = trim($_POST['trailer_url']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validation
    if (empty($title)) {
        $error = 'Movie title is required';
    } elseif ($duration <= 0) {
        $error = 'Duration must be greater than 0';
    } elseif (empty($genre)) {
        $error = 'Genre is required';
    } else {
        // Prepare SQL statement
        $sql = "INSERT INTO movies (title, description, duration, genre, release_date, rating, poster_url, trailer_url, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $sql1 = "INSERT INTO screenings (movie_id, screening_time, hall_number, price, available_seats, created_at) 
                VALUES (LAST_INSERT_ID(), '2024-12-15 14:00:00', 1, 12.50, 100, NOW());";
        
        if ($stmt = $conn->prepare($sql)) {
            // Handle NULL release date properly
            if ($release_date === NULL) {
                $stmt->bind_param("ssisssssi", $title, $description, $duration, $genre, $release_date, $rating, $poster_url, $trailer_url, $is_active);
            } else {
                $stmt->bind_param("ssisssssi", $title, $description, $duration, $genre, $release_date, $rating, $poster_url, $trailer_url, $is_active);
            }
            
            if ($stmt->execute()) {
                $movie_id = $stmt->insert_id;
                $success = 'Movie added successfully! Movie ID: ' . $movie_id;
                
                // Clear form
                $_POST = array();
            } else {
                $error = 'Failed to add movie. Database error: ' . $stmt->error;
                error_log("Movie insert error: " . $stmt->error);
            }
            $stmt->close();
        } else {
            $error = 'Failed to prepare SQL statement: ' . $conn->error;
            error_log("SQL prepare error: " . $conn->error);
        }
    }
}

$pageTitle = "Add New Movie";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Image Preview Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <style>
        /* Admin Layout */
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
        
        .back-btn {
            background: #0f3460;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        
        /* Form Container */
        .form-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        /* Form Styles */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
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
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #e94560;
            box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.1);
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        /* Checkbox */
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
        
        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .btn-submit {
            background: #e94560;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-submit:hover {
            background: #d43f57;
        }
        
        .btn-reset {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-reset:hover {
            background: #5a6268;
        }
        
        /* Image Preview */
        .image-preview-container {
            margin-top: 20px;
        }
        
        .image-preview {
            width: 100%;
            max-width: 300px;
            height: 400px;
            border: 2px dashed #ddd;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 15px;
            background: #f8f9fa;
        }
        
        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .preview-placeholder {
            text-align: center;
            color: #999;
        }
        
        .preview-placeholder i {
            font-size: 3rem;
            margin-bottom: 10px;
            display: block;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Character Count */
        .char-count {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .char-count.warning {
            color: #e94560;
        }
        
        /* Required Field Indicator */
        .required::after {
            content: " *";
            color: #e94560;
        }
        
        /* Help Text */
        .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            display: block;
        }
        
        /* Quick Add Options */
        .quick-add-options {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
        }
        
        .quick-add-options h3 {
            margin-top: 0;
            color: #1a1a2e;
            margin-bottom: 15px;
        }
        
        .sample-movies {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .sample-movie {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .sample-movie:hover {
            border-color: #e94560;
            transform: translateY(-2px);
        }
        
        .sample-movie h4 {
            margin: 0 0 5px;
            color: #1a1a2e;
            font-size: 14px;
        }
        
        .sample-movie p {
            margin: 0;
            color: #666;
            font-size: 12px;
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
                        <a href="add_movie.php" class="active">
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
        <form method="POST" action="">
        <!-- Main Content -->
        <main class="admin-main">
            <!-- Page Header -->
            <div class="page-header">
                <h1><i class="fas fa-plus-circle"></i> Add New Movie</h1>
                <a href="movies.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Movies
                </a>
            </div>

            <!-- Alert Messages -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    <a href="add_movie.php" style="margin-left: auto; color: #155724; text-decoration: underline;">
                        Add Another Movie
                    </a>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Quick Add Options -->
            <div class="quick-add-options">
                <h3><i class="fas fa-bolt"></i> Quick Add Sample Movies</h3>
                <div class="sample-movies">
                    <div class="sample-movie" onclick="fillSampleMovie(1)">
                        <h4>Action Movie</h4>
                        <p>Avengers-style blockbuster</p>
                    </div>
                    <div class="sample-movie" onclick="fillSampleMovie(2)">
                        <h4>Romantic Comedy</h4>
                        <p>Light-hearted romance</p>
                    </div>
                    <div class="sample-movie" onclick="fillSampleMovie(3)">
                        <h4>Sci-Fi Adventure</h4>
                        <p>Futuristic sci-fi</p>
                    </div>
                    <div class="sample-movie" onclick="fillSampleMovie(4)">
                        <h4>Horror Thriller</h4>
                        <p>Suspenseful horror</p>
                    </div>
                </div>
            </div>

            <!-- Form Container -->
            <div class="form-container">
                <div class="form-card">
                    <form method="POST" action="" id="addMovieForm">
                        <!-- Movie Title -->
                        <div class="form-group">
                            <label class="required">Movie Title</label>
                            <input type="text" name="title" class="form-control" required 
                                   placeholder="Enter movie title" 
                                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="4" 
                                      placeholder="Enter movie description" 
                                      id="descriptionField"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            <div class="char-count" id="descCharCount">0/1000 characters</div>
                        </div>

                        <div class="form-row">
                            <!-- Duration -->
                            <div class="form-group">
                                <label class="required">Duration (minutes)</label>
                                <input type="number" name="duration" class="form-control" required min="1" max="300"
                                       placeholder="e.g., 120" 
                                       value="<?php echo htmlspecialchars($_POST['duration'] ?? ''); ?>">
                                <span class="help-text">Enter duration in minutes (1-300)</span>
                            </div>

                            <!-- Genre -->
                            <div class="form-group">
                                <label class="required">Genre</label>
                                <input type="text" name="genre" class="form-control" required 
                                       placeholder="e.g., Action, Comedy, Drama" 
                                       value="<?php echo htmlspecialchars($_POST['genre'] ?? ''); ?>">
                                <span class="help-text">Separate multiple genres with commas</span>
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- Release Date -->
                            <div class="form-group">
                                <label>Release Date</label>
                                <input type="date" name="release_date" class="form-control" 
                                       value="<?php echo htmlspecialchars($_POST['release_date'] ?? ''); ?>">
                            </div>

                            <!-- Rating -->
                            <div class="form-group">
                                <label class="required">Rating</label>
                                <select name="rating" class="form-control" required>
                                    <option value="G" <?php echo (($_POST['rating'] ?? 'PG-13') === 'G') ? 'selected' : ''; ?>>G - General Audiences</option>
                                    <option value="PG" <?php echo (($_POST['rating'] ?? 'PG-13') === 'PG') ? 'selected' : ''; ?>>PG - Parental Guidance</option>
                                    <option value="PG-13" <?php echo (($_POST['rating'] ?? 'PG-13') === 'PG-13') ? 'selected' : ''; ?>>PG-13 - Parents Strongly Cautioned</option>
                                    <option value="R" <?php echo (($_POST['rating'] ?? 'PG-13') === 'R') ? 'selected' : ''; ?>>R - Restricted</option>
                                    <option value="NC-17" <?php echo (($_POST['rating'] ?? 'PG-13') === 'NC-17') ? 'selected' : ''; ?>>NC-17 - Adults Only</option>
                                </select>
                            </div>
                        </div>

                        <!-- Poster URL -->
                        <div class="form-group">
                            <label>Poster URL</label>
                            <input type="url" name="poster_url" class="form-control" 
                                   placeholder="https://example.com/poster.jpg" 
                                   value="<?php echo htmlspecialchars($_POST['poster_url'] ?? ''); ?>"
                                   id="posterUrl">
                            <span class="help-text">Enter full URL to movie poster image</span>
                            
                            <!-- Image Preview -->
                            <div class="image-preview-container">
                                <div class="image-preview" id="posterPreview">
                                    <div class="preview-placeholder">
                                        <i class="fas fa-image"></i>
                                        <p>Poster preview will appear here</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Trailer URL -->
                        <div class="form-group">
                            <label>Trailer URL</label>
                            <input type="url" name="trailer_url" class="form-control" 
                                   placeholder="https://youtube.com/watch?v=..." 
                                   value="<?php echo htmlspecialchars($_POST['trailer_url'] ?? ''); ?>">
                            <span class="help-text">YouTube or other video platform URL</span>
                        </div>

                        <!-- Active Status -->
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       <?php echo (isset($_POST['is_active']) ? 'checked' : 'checked'); ?>>
                                <label for="is_active">Active (Visible to users)</label>
                            </div>
                            <span class="help-text">Uncheck to hide movie from users while keeping it in database</span>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="submit" name="add_movie" class="btn-submit">
                                <i class="fas fa-save"></i> Add Movie
                            </button>
                            <button type="reset" class="btn-reset" onclick="resetForm()">
                                <i class="fas fa-redo"></i> Clear Form
                            </button>
                            <a href="movies.php" class="btn-reset" style="text-decoration: none;">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        </form>
    </div>

    <!-- JavaScript -->
    <script>
        // Image Preview Functionality
        document.getElementById('posterUrl').addEventListener('input', function() {
            const url = this.value;
            const preview = document.getElementById('posterPreview');
            
            if (url && isValidImageUrl(url)) {
                preview.innerHTML = `<img src="${url}" alt="Poster Preview" onerror="this.onerror=null; this.src=''; showPreviewPlaceholder();">`;
            } else {
                showPreviewPlaceholder();
            }
        });
        
        function isValidImageUrl(url) {
            return /\.(jpg|jpeg|png|webp|gif)$/i.test(url);
        }
        
        function showPreviewPlaceholder() {
            const preview = document.getElementById('posterPreview');
            preview.innerHTML = `
                <div class="preview-placeholder">
                    <i class="fas fa-image"></i>
                    <p>Invalid or no image URL</p>
                </div>
            `;
        }
        
        // Character Count for Description
        const descField = document.getElementById('descriptionField');
        const charCount = document.getElementById('descCharCount');
        
        descField.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = `${length}/1000 characters`;
            
            if (length > 1000) {
                charCount.classList.add('warning');
            } else {
                charCount.classList.remove('warning');
            }
        });
        
        // Initialize character count
        descField.dispatchEvent(new Event('input'));
        
        // Sample Movies Data
        const sampleMovies = {
            1: {
                title: "Avengers: Endgame",
                description: "After the devastating events of Avengers: Infinity War, the universe is in ruins. With the help of remaining allies, the Avengers assemble once more in order to reverse Thanos' actions and restore balance to the universe.",
                duration: 181,
                genre: "Action, Adventure, Sci-Fi",
                release_date: "2019-04-26",
                rating: "PG-13",
                poster_url: "https://m.media-amazon.com/images/M/MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_.jpg",
                trailer_url: "https://www.youtube.com/watch?v=TcMBFSGVi1c"
            },
            2: {
                title: "Spider-Man: No Way Home",
                description: "A teenage called peter parker is out on a field trip and encounters a vilian and it about how he fights it.",
                duration: 108,
                genre: "Action, Adventure",
                release_date: "2021-06-01",
                rating: "PG-13",
                poster_url: "https://m.media-amazon.com/images/M/MV5BZWMyYzFjYTYtNTRjYi00OGExLWE2YzgtOGRmYjAxZTU3NzBiXkEyXkFqcGdeQXVyMzQ0MzA0NTM@._V1_FMjpg_UX1000_.jpg",
                trailer_url: "https://www.youtube.com/watch?v=RFL8b1p1ELY"
            },
            3: {
                title: "Dune",
                description: "Feature adaptation of Frank Herbert's science fiction novel about the son of a noble family entrusted with the protection of the most valuable asset and most vital element in the galaxy.",
                duration: 155,
                genre: "Sci-Fi, Adventure, Drama",
                release_date: "2021-09-15",
                rating: "PG-13",
                poster_url: "https://m.media-amazon.com/images/M/MV5BN2FjNmEyNWMtYzM0ZS00NjIyLTg5YzYtYThlMGVjNzE1OGViXkEyXkFqcGdeQXVyMTkxNjUyNQ@@._V1_.jpg",
                trailer_url: "https://www.youtube.com/watch?v=8g18jFHCLXk"
            },
            4: {
                title: "A Quiet Place",
                description: "In a post-apocalyptic world, a family is forced to live in silence while hiding from monsters with ultra-sensitive hearing.",
                duration: 90,
                genre: "Horror, Thriller, Drama",
                release_date: "2018-04-03",
                rating: "PG-13",
                poster_url: "https://m.media-amazon.com/images/M/MV5BMjI0MDMzNTQ0M15BMl5BanBnXkFtZTgwMTM5NzM3NDM@._V1_.jpg",
                trailer_url: "https://www.youtube.com/watch?v=WR7cc5t7tv8"
            }
        };
        
        // Fill form with sample data
        function fillSampleMovie(movieId) {
            const movie = sampleMovies[movieId];
            
            // Fill form fields
            document.querySelector('input[name="title"]').value = movie.title;
            document.querySelector('textarea[name="description"]').value = movie.description;
            document.querySelector('input[name="duration"]').value = movie.duration;
            document.querySelector('input[name="genre"]').value = movie.genre;
            document.querySelector('input[name="release_date"]').value = movie.release_date;
            document.querySelector('select[name="rating"]').value = movie.rating;
            document.querySelector('input[name="poster_url"]').value = movie.poster_url;
            document.querySelector('input[name="trailer_url"]').value = movie.trailer_url;
            
            // Trigger image preview
            document.getElementById('posterUrl').dispatchEvent(new Event('input'));
            
            // Trigger character count update
            descField.dispatchEvent(new Event('input'));
            
            // Scroll to form
            document.querySelector('.form-container').scrollIntoView({ behavior: 'smooth' });
            
            // Show success message
            alert(`"${movie.title}" sample data loaded! Modify as needed and click "Add Movie".`);
        }
        
        // Form validation
        document.getElementById('addMovieForm').addEventListener('submit', function(e) {
            const title = document.querySelector('input[name="title"]').value.trim();
            const duration = document.querySelector('input[name="duration"]').value;
            const genre = document.querySelector('input[name="genre"]').value.trim();
            
            let isValid = true;
            let errorMessage = '';
            
            if (!title) {
                isValid = false;
                errorMessage += '• Movie title is required\n';
            }
            
            if (!duration || duration <= 0 || duration > 300) {
                isValid = false;
                errorMessage += '• Duration must be between 1 and 300 minutes\n';
            }
            
            if (!genre) {
                isValid = false;
                errorMessage += '• Genre is required\n';
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fix the following errors:\n\n' + errorMessage);
            } else {
                // Show loading state
                const submitBtn = document.querySelector('button[name="add_movie"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding Movie...';
                submitBtn.disabled = true;
            }
        });
        
        // Reset form function
        function resetForm() {
            if (confirm('Are you sure you want to clear all form fields?')) {
                document.getElementById('addMovieForm').reset();
                showPreviewPlaceholder();
                descField.dispatchEvent(new Event('input'));
            }
        }
        
        // Initialize image preview if URL exists
        document.addEventListener('DOMContentLoaded', function() {
            const posterUrl = document.getElementById('posterUrl').value;
            if (posterUrl) {
                document.getElementById('posterUrl').dispatchEvent(new Event('input'));
            }
        });
    </script>
</body>
</html>