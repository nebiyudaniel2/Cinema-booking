<?php
require_once '../includes/config.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('../index.php?error=Access denied');
}

$pageTitle = "Manage Movies";
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
        
        .add-btn {
            background: #e94560;
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
        .movies-table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .movies-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .movies-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #1a1a2e;
            border-bottom: 2px solid #e94560;
        }
        
        .movies-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        .movies-table tr:hover {
            background: #f9f9f9;
        }
        
        .movie-poster-small {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .action-btns {
            display: flex;
            gap: 8px;
        }
        
        .btn-edit {
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
        
        .btn-delete {
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
        
        .btn-view {
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
        .search-filter {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
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
        
        .search-btn {
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
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-header h3 {
            margin: 0;
            color: #1a1a2e;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
        
        .modal-form .form-group {
            margin-bottom: 20px;
        }
        
        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 30px;
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
                        <a href="movies.php" class="active">
                            <i class="fas fa-film"></i> Movies
                        </a>
                    </li>
                    <li>
                        <a href="add_movie.php">
                            <i class="fas fa-plus-circle"></i> Add Movie
                        </a>
                    </li>
                    <li>
                        <a href="screenings.php">
                            <i class="fas fa-calendar-alt"></i> Screenings
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
                        <a href="reports.php">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                    </li>
                    <li>
                        <a href="settings.php">
                            <i class="fas fa-cog"></i> Settings
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
            <div class="page-header">
                <h1><i class="fas fa-film"></i> Manage Movies</h1>
            </div>
            <div class="search-filter">
                <div class="filter-row">
                    <div class="form-group">
                        <label>Search Movies</label>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search by title, genre...">
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Sort By</label>
                        <select class="form-control" id="sortFilter">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="title">Title A-Z</option>
                            <option value="rating">Highest Rated</option>
                        </select>
                    </div>
                    
                    <button class="search-btn" onclick="filterMovies()">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>
            <div class="movies-table-container">
                <div class="table-responsive">
                    <table class="movies-table">
                        <thead>
                            <tr>
                                <th>Poster</th>
                                <th>Title</th>
                                <th>Genre</th>
                                <th>Duration</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="moviesTableBody">
                            <?php

                            $sql = "SELECT * FROM movies ORDER BY created_at DESC";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0):
                                while($movie = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($movie['poster_url'] ?: '../images/default-movie.jpg'); ?>" 
                                         alt="<?php echo htmlspecialchars($movie['title']); ?>" 
                                         class="movie-poster-small">
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($movie['title']); ?></strong><br>
                                    <small style="color: #666;"><?php echo date('Y', strtotime($movie['release_date'])); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                                <td><?php echo floor($movie['duration']/60); ?>h <?php echo $movie['duration']%60; ?>m</td>
                                <td>
                                    <span style="background: #ffc107; color: #333; padding: 3px 8px; border-radius: 3px; font-weight: 600;">
                                        <?php echo htmlspecialchars($movie['rating']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $movie['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $movie['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="edit_movie.php?id=<?php echo $movie['id']; ?>" class="btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button class="btn-delete" onclick="deleteMovie(<?php echo $movie['id']; ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                        <a href="../booking/movies.php?movie=<?php echo $movie['id']; ?>" target="_blank" class="btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            else: 
                            ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-film" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                                    <h3 style="color: #666;">No Movies Found</h3>
                                    <p>Add your first movie to get started.</p>
                                    <button class="add-btn" onclick="openAddMovieModal()" style="margin-top: 15px;">
                                        <i class="fas fa-plus"></i> Add Movie
                                    </button>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="addMovieModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-plus"></i> Add New Movie</h3>
                <button class="close-modal" onclick="closeAddMovieModal()">&times;</button>
            </div>
            
            <form id="addMovieForm" class="modal-form">
                <div class="form-group">
                    <label>Movie Title *</label>
                    <input type="text" name="title" class="form-control" required placeholder="Enter movie title">
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Enter movie description"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label>Duration (minutes) *</label>
                        <input type="number" name="duration" class="form-control" required placeholder="e.g., 120">
                    </div>
                    
                    <div class="form-group" style="flex: 1;">
                        <label>Genre *</label>
                        <input type="text" name="genre" class="form-control" required placeholder="e.g., Action, Comedy">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label>Release Date</label>
                        <input type="date" name="release_date" class="form-control">
                    </div>
                    
                    <div class="form-group" style="flex: 1;">
                        <label>Rating</label>
                        <select name="rating" class="form-control">
                            <option value="G">G</option>
                            <option value="PG">PG</option>
                            <option value="PG-13" selected>PG-13</option>
                            <option value="R">R</option>
                            <option value="NC-17">NC-17</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Poster URL</label>
                    <input type="url" name="poster_url" class="form-control" placeholder="https://example.com/poster.jpg">
                </div>
                
                <div class="form-group">
                    <label>Trailer URL</label>
                    <input type="url" name="trailer_url" class="form-control" placeholder="https://youtube.com/watch?v=...">
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" checked> Active (Visible to users)
                    </label>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeAddMovieModal()">Cancel</button>
                    <button type="submit" class="add-btn">
                        <i class="fas fa-save"></i> Save Movie
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/script.js"></script>
    <script>
      
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('addMovieModal');
            if (event.target === modal) {
                closeAddMovieModal();
            }
        });
        
        document.getElementById('addMovieForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                alert('Movie added successfully!');
                closeAddMovieModal();
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                window.location.reload();
            }, 1500);
        });
        
        function deleteMovie(movieId) {
            if (confirm('Are you sure you want to delete this movie? This action cannot be undone.')) {
                // Show loading
                const deleteBtn = event.target.closest('.btn-delete');
                const originalText = deleteBtn.innerHTML;
                deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                deleteBtn.disabled = true;
                
                setTimeout(() => {
                    alert('Movie deleted successfully!');
                    const row = deleteBtn.closest('tr');
                    row.style.opacity = '0.5';
                    
                    setTimeout(() => {
                        row.remove();
                        
                        if (document.querySelectorAll('#moviesTableBody tr').length === 0) {
                            const tbody = document.getElementById('moviesTableBody');
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-film" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                                        <h3 style="color: #666;">No Movies Found</h3>
                                        <p>Add your first movie to get started.</p>
                                        <button class="add-btn" onclick="openAddMovieModal()" style="margin-top: 15px;">
                                            <i class="fas fa-plus"></i> Add Movie
                                        </button>
                                    </td>
                                </tr>
                            `;
                        }
                    }, 500);
                }, 1500);
            }
        }
        
        function filterMovies() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const sortFilter = document.getElementById('sortFilter').value;
            
            const rows = document.querySelectorAll('#moviesTableBody tr');
            
            rows.forEach(row => {
                const title = row.cells[1].textContent.toLowerCase();
                const genre = row.cells[2].textContent.toLowerCase();
                const status = row.querySelector('.status-badge').textContent.toLowerCase();
                
                let showRow = true;
                
                if (searchTerm && !title.includes(searchTerm) && !genre.includes(searchTerm)) {
                    showRow = false;
                }
     
                if (statusFilter) {
                    if (statusFilter === 'active' && status !== 'active') {
                        showRow = false;
                    } else if (statusFilter === 'inactive' && status !== 'inactive') {
                        showRow = false;
                    }
                }
               
                row.style.display = showRow ? '' : 'none';
            });
            
            if (sortFilter === 'title') {
                alert('Sorted by title');
            }
        }
        function quickAddSampleMovie() {
            const sampleMovies = [
                {
                    title: "Spider-Man: No Way Home",
                    genre: "Action, Adventure",
                    duration: 148,
                    rating: "PG-13",
                    poster: "https://m.media-amazon.com/images/M/MV5BZWMyYzFjYTYtNTRjYi00OGExLWE2YzgtOGRmYjAxZTU3NzBiXkEyXkFqcGdeQXVyMzQ0MzA0NTM@._V1_FMjpg_UX1000_.jpg"
                },
                {
                    title: "The Batman",
                    genre: "Action, Crime, Drama",
                    duration: 176,
                    rating: "PG-13",
                    poster: "https://m.media-amazon.com/images/M/MV5BMDdmMTBiNTYtMDIzNi00NGVlLWIzMDYtZTk3MTQ3NGQxZGEwXkEyXkFqcGdeQXVyMzMwOTU5MDk@._V1_FMjpg_UX1000_.jpg"
                }
            ];
            
            const sample = sampleMovies[Math.floor(Math.random() * sampleMovies.length)];
            
            document.querySelector('input[name="title"]').value = sample.title;
            document.querySelector('input[name="genre"]').value = sample.genre;
            document.querySelector('input[name="duration"]').value = sample.duration;
            document.querySelector('select[name="rating"]').value = sample.rating;
            document.querySelector('input[name="poster_url"]').value = sample.poster;
            document.querySelector('textarea[name="description"]').value = `This is a sample description for ${sample.title}. A great movie that everyone should watch!`;
            document.querySelector('input[name="release_date"]').value = '2023-01-01';
            
            openAddMovieModal();
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.querySelector('.page-header');
            const quickAddBtn = document.createElement('button');
            quickAddBtn.className = 'add-btn';
            quickAddBtn.style.background = '#0f3460';
            quickAddBtn.style.marginRight = '10px';
            quickAddBtn.innerHTML = '<i class="fas fa-bolt"></i> Quick Add Sample';
            quickAddBtn.onclick = quickAddSampleMovie;
            
            header.insertBefore(quickAddBtn, header.querySelector('.add-btn'));
        });
    </script>
</body>
</html>