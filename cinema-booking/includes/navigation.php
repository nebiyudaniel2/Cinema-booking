<?php
// includes/navigation.php - Navigation helper functions

function getNavigation() {
    $nav = [
        'index.php' => ['icon' => 'fas fa-home', 'label' => 'Home'],
        'booking/movies.php' => ['icon' => 'fas fa-film', 'label' => 'Movies'],
    ];
    
    if (isLoggedIn()) {
        $nav['booking/mybookings.php'] = ['icon' => 'fas fa-calendar-alt', 'label' => 'My Bookings'];
        
        if (isAdmin()) {
            $nav['admin/dashboard.php'] = ['icon' => 'fas fa-cog', 'label' => 'Admin'];
        }
    }
    
    return $nav;
}

function renderNavigation() {
    $nav = getNavigation();
    $current = basename($_SERVER['PHP_SELF']);
    
    echo '<ul>';
    foreach ($nav as $url => $item) {
        $active = ($current === basename($url)) ? 'active' : '';
        echo '<li>';
        echo '<a href="' . $url . '" class="' . $active . '">';
        echo '<i class="' . $item['icon'] . '"></i> ' . $item['label'];
        echo '</a>';
        echo '</li>';
    }
    echo '</ul>';
}
?>