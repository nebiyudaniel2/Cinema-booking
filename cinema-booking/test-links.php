<?php
// test-links.php - Test CSS and JS links
$pageTitle = "Test Links";
include 'includes/header.php';
?>

<div class="container">
    <h1 class="text-center mt-3">✅ CSS and JS Links Test</h1>
    
    <div class="form-container animate-fade">
        <h2>Test Form</h2>
        <form id="testForm" data-validate>
            <div class="form-group">
                <label for="testInput">Test Input</label>
                <input type="text" id="testInput" class="form-control" required placeholder="Type something...">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Test Submit</button>
        </form>
    </div>
    
    <div class="movies-grid mt-3">
        <div class="movie-card animate-fade">
            <div class="movie-poster">
                <img src="https://via.placeholder.com/300x450" alt="Test Movie">
            </div>
            <div class="movie-info">
                <h3 class="movie-title">Test Movie</h3>
                <div class="movie-meta">
                    <span>2h 15m</span>
                    <span class="movie-rating">PG-13</span>
                </div>
                <p class="movie-description">This is a test movie description to verify styling is working properly.</p>
                <button class="btn btn-secondary">Book Now</button>
            </div>
        </div>
    </div>
    
    <div class="alert alert-success mt-3">
        <i class="fas fa-check-circle"></i> If you can see styled buttons, forms, and cards, your CSS is working!
    </div>
    
    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle"></i> Try submitting the form without typing - JavaScript validation should work.
    </div>
</div>

<script>
// Test JavaScript is working
document.getElementById('testForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('JavaScript is working! Form submitted successfully.');
    this.reset();
});

// Test toast notification
setTimeout(function() {
    window.CineBook.showToast('JavaScript is loaded and working!', 'success');
}, 1000);
</script>

<?php include 'includes/footer.php'; ?>