<?php
// includes/config.php - Database configuration with auto-setup
session_start();

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// XAMPP Default Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Empty password for XAMPP default
define('DB_NAME', 'cinema_booking');

// First, connect without database to check/create it
$setup_conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($setup_conn->connect_error) {
    die("<div style='padding: 50px; text-align: center;'>
        <h2 style='color: #e94560;'>❌ Database Server Connection Failed</h2>
        <p>Cannot connect to MySQL server at " . DB_HOST . "</p>
        <p>Error: " . $setup_conn->connect_error . "</p>
        <p>Please make sure:</p>
        <ol style='text-align: left; max-width: 500px; margin: 20px auto;'>
            <li>XAMPP is installed and running</li>
            <li>MySQL service is started in XAMPP Control Panel</li>
            <li>Username and password are correct (default: root with no password)</li>
        </ol>
        <a href='../setup.php' style='display: inline-block; padding: 12px 24px; background: #e94560; color: white; text-decoration: none; border-radius: 5px; margin: 20px;'>
            🚀 Run Manual Setup
        </a>
    </div>");
}

// Check if database exists
$result = $setup_conn->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
$database_exists = $result->num_rows > 0;

if (!$database_exists) {
    // Database doesn't exist, create it
    echo "<div style='padding: 20px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; margin: 20px;'>
            <h3>🔧 Setting up Database...</h3>
            <p>Creating database and tables. This may take a moment...</p>
          </div>";
    
    // Create database
    if ($setup_conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci") === TRUE) {
        echo "<div style='padding: 10px; background: #d4edda; margin: 10px;'>✅ Database created successfully</div>";
    } else {
        die("<div style='padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 50px;'>
                <h2>❌ Failed to create database</h2>
                <p>Error: " . $setup_conn->error . "</p>
                <a href='../setup.php' style='display: inline-block; padding: 10px 20px; background: #e94560; color: white; text-decoration: none; border-radius: 5px;'>
                    Try Manual Setup
                </a>
            </div>");
    }
}

// Now connect to the database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("<div style='padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 50px;'>
            <h2>❌ Database Connection Failed</h2>
            <p>Error: " . $conn->connect_error . "</p>
        </div>");
}

// Set charset
$conn->set_charset("utf8mb4");

// Check if tables exist, if not create them
if (!$database_exists) {
    echo "<div style='padding: 10px; background: #d1ecf1; margin: 10px;'>🔄 Creating tables and sample data...</div>";
    
    // Create tables
    $tables_sql = array(
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('user', 'admin') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "CREATE TABLE IF NOT EXISTS movies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            duration INT NOT NULL,
            genre VARCHAR(100),
            release_date DATE,
            poster_url VARCHAR(500) DEFAULT 'https://via.placeholder.com/300x450?text=Movie+Poster',
            trailer_url VARCHAR(500),
            rating VARCHAR(10),
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "CREATE TABLE IF NOT EXISTS screenings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            movie_id INT NOT NULL,
            screening_time DATETIME NOT NULL,
            hall_number INT NOT NULL,
            price DECIMAL(8,2) NOT NULL,
            available_seats INT DEFAULT 100,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "CREATE TABLE IF NOT EXISTS seats (
            id INT AUTO_INCREMENT PRIMARY KEY,
            screening_id INT NOT NULL,
            seat_number VARCHAR(10) NOT NULL,
            is_booked BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_seat (screening_id, seat_number),
            FOREIGN KEY (screening_id) REFERENCES screenings(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            screening_id INT NOT NULL,
            booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            total_amount DECIMAL(10,2) NOT NULL,
            status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'confirmed',
            payment_method VARCHAR(50),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (screening_id) REFERENCES screenings(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "CREATE TABLE IF NOT EXISTS booking_seats (
            id INT AUTO_INCREMENT PRIMARY KEY,
            booking_id INT NOT NULL,
            seat_id INT NOT NULL,
            FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
            FOREIGN KEY (seat_id) REFERENCES seats(id) ON DELETE CASCADE,
            UNIQUE KEY unique_booking_seat (booking_id, seat_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
    
    foreach ($tables_sql as $sql) {
        if (!$conn->query($sql)) {
            echo "<div style='padding: 10px; background: #f8d7da; margin: 10px;'>❌ Error creating table: " . $conn->error . "</div>";
        }
    }
    
    echo "<div style='padding: 10px; background: #d4edda; margin: 10px;'>✅ Basic tables created</div>";
    
    // Insert sample data only if tables are empty
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        echo "<div style='padding: 10px; background: #d1ecf1; margin: 10px;'>📊 Adding sample data...</div>";
        
        // Insert sample users
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $user_password = password_hash('password123', PASSWORD_DEFAULT);
        
        $conn->query("INSERT INTO users (username, email, password, role) VALUES
            ('admin', 'admin@cinema.com', '$admin_password', 'admin'),
            ('john_doe', 'john@example.com', '$user_password', 'user')");
        
        // Insert sample movies
        $conn->query("INSERT INTO movies (title, description, duration, genre, release_date, rating) VALUES
            ('Avengers: Endgame', 'After the devastating events of Avengers: Infinity War, the universe is in ruins.', 181, 'Action', '2019-04-26', 'PG-13'),
            ('The Lion King', 'Simba idolizes his father, King Mufasa, and takes to heart his own royal destiny.', 118, 'Animation', '2019-07-19', 'PG'),
            ('Inception', 'A thief who steals corporate secrets through dream-sharing technology.', 148, 'Sci-Fi', '2010-07-16', 'PG-13')");
        
        // Insert sample screenings
        $conn->query("INSERT INTO screenings (movie_id, screening_time, hall_number, price, available_seats) VALUES
            (1, NOW() + INTERVAL 1 DAY, 1, 12.50, 100),
            (1, NOW() + INTERVAL 2 DAY, 1, 12.50, 100),
            (2, NOW() + INTERVAL 1 DAY, 2, 10.00, 100)");
        
        // Generate some seats
        $screenings = $conn->query("SELECT id FROM screenings");
        while ($screening = $screenings->fetch_assoc()) {
            for ($i = 1; $i <= 20; $i++) { // Create 20 seats per screening for testing
                $conn->query("INSERT INTO seats (screening_id, seat_number) VALUES ({$screening['id']}, 'A$i')");
            }
        }
        
        echo "<div style='padding: 10px; background: #d4edda; margin: 10px;'>✅ Sample data added</div>";
    }
    
    echo "<div style='padding: 15px; background: #d4edda; margin: 20px; border-radius: 5px;'>
            <h3>🎉 Setup Complete!</h3>
            <p>You can now use the cinema booking system.</p>
            <p><strong>Test Credentials:</strong></p>
            <ul>
                <li>Admin: <code>admin</code> / <code>admin123</code></li>
                <li>User: <code>john_doe</code> / <code>password123</code></li>
            </ul>
            <p><a href='../index.php' style='color: #155724; font-weight: bold;'>➡️ Continue to Homepage</a></p>
          </div>";
}

$setup_conn->close();

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($data) {
    global $conn;
    return htmlspecialchars(strip_tags(trim($data)));
}

function displayError($message) {
    return "<div class='alert alert-error'>$message</div>";
}

function displaySuccess($message) {
    return "<div class='alert alert-success'>$message</div>";
}

// For debugging
function debug($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}


// Alternative simpler function
function base_url($path = '') {
    $url = "http://" . $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__FILE__));
    $url = str_replace('/includes', '', $url);
    $url = rtrim($url, '/') . '/';
    return $url . ltrim($path, '/');
}
?>
<?php
// includes/config.php - Add this function

function getBaseUrl() {
    // Check if we're in a subdirectory
    $currentPath = $_SERVER['PHP_SELF'];
    $pathInfo = pathinfo($currentPath);
    $hostName = $_SERVER['HTTP_HOST'];
    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
    
    // If the file is in a subdirectory (like auth/, booking/, etc.)
    $subdirectory = '';
    if (strpos($currentPath, '/auth/') !== false || 
        strpos($currentPath, '/booking/') !== false ||
        strpos($currentPath, '/admin/') !== false) {
        $subdirectory = '../';
    }
    
    return $subdirectory;
}
function requireAdmin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        redirect('../index.php?error=Access denied. Admin privileges required.');
    }
}
?>