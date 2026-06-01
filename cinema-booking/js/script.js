// ===== CINEMA BOOKING SYSTEM - MAIN JAVASCRIPT =====

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cinema Booking System initialized');
    
    // Initialize all components
    initMobileMenu();
    initDropdowns();
    initAnimations();
    initFormValidation();
    initSeatSelection();
    initCountdownTimers();
    
    // Add smooth scrolling for anchor links
    initSmoothScroll();
    
    // Add active class to current page in navigation
    highlightCurrentPage();
});

// 1. MOBILE MENU TOGGLE
function initMobileMenu() {
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav ul');
    
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.classList.toggle('active');
            
            // Change icon
            const icon = this.querySelector('i');
            if (mainNav.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.main-nav') && !event.target.closest('.menu-toggle')) {
                mainNav.classList.remove('active');
                menuToggle.classList.remove('active');
                menuToggle.querySelector('i').classList.remove('fa-times');
                menuToggle.querySelector('i').classList.add('fa-bars');
            }
        });
    }
}

// 2. DROPDOWN MENUS
function initDropdowns() {
    const dropdowns = document.querySelectorAll('.user-menu, .has-dropdown');
    
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('mouseenter', function() {
            const menu = this.querySelector('.dropdown-menu');
            if (menu) menu.style.display = 'block';
        });
        
        dropdown.addEventListener('mouseleave', function() {
            const menu = this.querySelector('.dropdown-menu');
            if (menu) menu.style.display = 'none';
        });
    });
    
    // Mobile dropdowns
    if (window.innerWidth < 768) {
        const dropdownToggles = document.querySelectorAll('.user-dropdown, .dropdown-toggle');
        
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const dropdown = this.closest('.user-menu, .has-dropdown');
                const menu = dropdown.querySelector('.dropdown-menu');
                
                if (menu) {
                    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                }
            });
        });
    }
}

// 3. ANIMATIONS
function initAnimations() {
    // Add animation class to elements when they enter viewport
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe elements with animation classes
    document.querySelectorAll('.movie-card, .step, .form-container').forEach(el => {
        observer.observe(el);
    });
}

// 4. FORM VALIDATION
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showFormError(this, 'Please fill in all required fields correctly.');
            }
        });
    });
    
    // Real-time validation
    const inputs = document.querySelectorAll('.form-control[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            clearFieldError(this);
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    
    // Clear previous error
    clearFieldError(field);
    
    // Required validation
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'This field is required');
        isValid = false;
    }
    
    // Email validation
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showFieldError(field, 'Please enter a valid email address');
            isValid = false;
        }
    }
    
    // Password confirmation
    if (field.id === 'confirm_password' && value) {
        const password = document.getElementById('password');
        if (password && value !== password.value) {
            showFieldError(field, 'Passwords do not match');
            isValid = false;
        }
    }
    
    return isValid;
}

function showFieldError(field, message) {
    field.classList.add('error');
    
    // Create error message element
    let errorEl = field.nextElementSibling;
    if (!errorEl || !errorEl.classList.contains('field-error')) {
        errorEl = document.createElement('div');
        errorEl.className = 'field-error';
        field.parentNode.insertBefore(errorEl, field.nextSibling);
    }
    
    errorEl.textContent = message;
    errorEl.style.color = 'var(--error-color)';
    errorEl.style.fontSize = '0.875rem';
    errorEl.style.marginTop = '0.25rem';
}

function clearFieldError(field) {
    field.classList.remove('error');
    const errorEl = field.nextElementSibling;
    if (errorEl && errorEl.classList.contains('field-error')) {
        errorEl.remove();
    }
}

function showFormError(form, message) {
    // Remove existing error
    const existingError = form.querySelector('.form-error');
    if (existingError) existingError.remove();
    
    // Create new error
    const errorEl = document.createElement('div');
    errorEl.className = 'alert alert-error form-error';
    errorEl.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    form.insertBefore(errorEl, form.firstChild);
    
    // Scroll to error
    errorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// 5. SEAT SELECTION
function initSeatSelection() {
    const seatContainers = document.querySelectorAll('.seats-grid');
    
    seatContainers.forEach(container => {
        const seats = container.querySelectorAll('.seat.available');
        const selectedSeatsDisplay = document.getElementById('selected-seats');
        const totalPriceDisplay = document.getElementById('total-price');
        const seatPrice = parseFloat(document.getElementById('seat-price')?.value || 10);
        const maxSeats = parseInt(document.getElementById('max-seats')?.value || 8);
        
        let selectedSeats = [];
        
        seats.forEach(seat => {
            seat.addEventListener('click', function() {
                const seatNumber = this.getAttribute('data-seat');
                
                if (this.classList.contains('selected')) {
                    // Deselect seat
                    this.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(s => s !== seatNumber);
                } else {
                    // Check max seats limit
                    if (selectedSeats.length >= maxSeats) {
                        alert(`You can select maximum ${maxSeats} seats at a time.`);
                        return;
                    }
                    
                    // Select seat
                    this.classList.add('selected');
                    selectedSeats.push(seatNumber);
                }
                
                // Update display
                updateSeatSelection();
            });
        });
        
        function updateSeatSelection() {
            const totalPrice = selectedSeats.length * seatPrice;
            
            if (selectedSeatsDisplay) {
                selectedSeatsDisplay.textContent = selectedSeats.join(', ') || 'None selected';
            }
            
            if (totalPriceDisplay) {
                totalPriceDisplay.textContent = `$${totalPrice.toFixed(2)}`;
            }
            
            // Update hidden input
            const seatsInput = document.getElementById('selected-seats-input');
            if (seatsInput) {
                seatsInput.value = selectedSeats.join(',');
            }
        }
        
        // Initialize display
        updateSeatSelection();
    });
}

// 6. COUNTDOWN TIMERS
function initCountdownTimers() {
    const timers = document.querySelectorAll('.countdown-timer');
    
    timers.forEach(timer => {
        const endTime = timer.getAttribute('data-end-time');
        if (!endTime) return;
        
        const countdownDate = new Date(endTime).getTime();
        
        const countdownFunction = setInterval(() => {
            const now = new Date().getTime();
            const distance = countdownDate - now;
            
            if (distance < 0) {
                clearInterval(countdownFunction);
                timer.innerHTML = "Time's up!";
                timer.classList.add('expired');
                
                // Optional: trigger an action when timer expires
                const onExpire = timer.getAttribute('data-on-expire');
                if (onExpire === 'reload') {
                    setTimeout(() => location.reload(), 2000);
                }
                
                return;
            }
            
            // Calculate time units
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Format display
            let display = '';
            if (days > 0) display += `${days}d `;
            if (hours > 0 || days > 0) display += `${hours}h `;
            if (minutes > 0 || hours > 0) display += `${minutes}m `;
            display += `${seconds}s`;
            
            timer.textContent = display;
            
            // Add warning class when less than 5 minutes
            if (distance < 5 * 60 * 1000) {
                timer.classList.add('warning');
            }
            
        }, 1000);
    });
}

// 7. SMOOTH SCROLLING
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Skip empty or # links
            if (href === '#' || href === '') return;
            
            const targetElement = document.querySelector(href);
            if (targetElement) {
                e.preventDefault();
                
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
                
                // Update URL without page reload
                history.pushState(null, null, href);
            }
        });
    });
}

// 8. HIGHLIGHT CURRENT PAGE
function highlightCurrentPage() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.main-nav a');
    
    navLinks.forEach(link => {
        const linkPath = link.getAttribute('href');
        
        if (linkPath === currentPath || 
            (currentPath.includes(linkPath) && linkPath !== '/')) {
            link.classList.add('active');
        }
    });
}

// 9. LOADING SPINNER
function showLoading() {
    // Remove existing spinner
    hideLoading();
    
    // Create spinner
    const spinner = document.createElement('div');
    spinner.className = 'loading-spinner';
    spinner.innerHTML = `
        <div class="spinner">
            <i class="fas fa-spinner fa-spin"></i>
        </div>
    `;
    
    // Add styles
    const style = document.createElement('style');
    style.textContent = `
        .loading-spinner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .loading-spinner .spinner {
            color: var(--primary-color);
            font-size: 3rem;
        }
    `;
    
    document.head.appendChild(style);
    document.body.appendChild(spinner);
}

function hideLoading() {
    const spinner = document.querySelector('.loading-spinner');
    if (spinner) spinner.remove();
}

// 10. TOAST NOTIFICATIONS
function showToast(message, type = 'info') {
    // Remove existing toasts
    hideToast();
    
    // Create toast
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-${getToastIcon(type)}"></i>
            <span>${message}</span>
        </div>
        <button class="toast-close">&times;</button>
    `;
    
    // Add styles
    const style = document.createElement('style');
    style.textContent = `
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: var(--white);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-heavy);
            display: flex;
            align-items: center;
            gap: 1rem;
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
            max-width: 400px;
        }
        .toast-success {
            border-left: 4px solid var(--success-color);
        }
        .toast-error {
            border-left: 4px solid var(--error-color);
        }
        .toast-warning {
            border-left: 4px solid var(--warning-color);
        }
        .toast-info {
            border-left: 4px solid var(--info-color);
        }
        .toast-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .toast-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-light);
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    
    document.head.appendChild(style);
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s ease-out reverse';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
    
    // Close button
    toast.querySelector('.toast-close').addEventListener('click', () => {
        toast.remove();
    });
}

function hideToast() {
    const toast = document.querySelector('.toast');
    if (toast) toast.remove();
}

function getToastIcon(type) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

// 11. AJAX HELPER FUNCTION
function ajaxRequest(url, options = {}) {
    const defaults = {
        method: 'GET',
        data: null,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    const config = { ...defaults, ...options };
    
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        
        xhr.open(config.method, url);
        
        // Set headers
        Object.entries(config.headers).forEach(([key, value]) => {
            xhr.setRequestHeader(key, value);
        });
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    resolve(response);
                } catch (e) {
                    resolve(xhr.responseText);
                }
            } else {
                reject(new Error(`Request failed with status ${xhr.status}`));
            }
        };
        
        xhr.onerror = function() {
            reject(new Error('Network error'));
        };
        
        xhr.send(config.data ? JSON.stringify(config.data) : null);
    });
}

// 12. THEME TOGGLE (Optional)
function initThemeToggle() {
    const themeToggle = document.getElementById('theme-toggle');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // Check localStorage for saved theme
    const savedTheme = localStorage.getItem('theme') || (prefersDark ? 'dark' : 'light');
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    if (themeToggle) {
        // Set initial icon
        updateThemeIcon(savedTheme);
        
        themeToggle.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
            
            showToast(`Switched to ${newTheme} theme`, 'info');
        });
    }
}

function updateThemeIcon(theme) {
    const icon = document.querySelector('#theme-toggle i');
    if (icon) {
        icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }
}

// Initialize theme on load
initThemeToggle();

// Export functions for use in other modules
window.CineBook = {
    showLoading,
    hideLoading,
    showToast,
    hideToast,
    ajaxRequest,
    validateForm,
    validateField
};