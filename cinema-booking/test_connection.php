<?php
// test_connection.php - Test database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'cinema_booking';

try {
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        echo '<div class="error">❌ Connection failed: ' . $conn->connect_error . '</div>';
    } else {
        echo '<div class="success">✅ Connected successfully to MySQL server</div>';
        
        // Check if database exists
        $result = $conn->query("SHOW DATABASES LIKE '$database'");
        if ($result->num_rows > 0) {
            echo '<div class="success">✅ Database "' . $database . '" exists</div>';
            
            // Check tables
            $conn->select_db($database);
            $result = $conn->query("SHOW TABLES");
            $table_count = $result->num_rows;
            
            if ($table_count > 0) {
                echo '<div class="success">✅ Found ' . $table_count . ' table(s):</div>';
                echo '<ul>';
                while ($row = $result->fetch_array()) {
                    echo '<li>' . $row[0] . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<div class="warning">⚠️ No tables found in database</div>';
            }
        } else {
            echo '<div class="warning">⚠️ Database "' . $database . '" does not exist</div>';
        }
        
        $conn->close();
    }
    
} catch (Exception $e) {
    echo '<div class="error">❌ Exception: ' . $e->getMessage() . '</div>';
}
?>