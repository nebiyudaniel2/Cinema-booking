# CineBook - Cinema Booking System

## 📽️ Overview

**CineBook** is a complete web-based cinema booking system that allows users to browse movies, select seats, and book tickets online. The system includes both user-facing features and an administrative dashboard for cinema management.

---

## 🚀 Features

### For Users:
- **User Registration & Login** - Secure account creation with email verification
- **Browse Movies** - View movie listings with posters, descriptions, genres, and ratings
- **View Showtimes** - Check available screening times for movies
- **Interactive Seat Selection** - Visual seat map showing available/occupied seats
- **Booking Management** - View booking history and manage reservations
- **Responsive Design** - Works on desktop, tablet, and mobile devices

### For Admins:
- **Admin Dashboard** - Real-time statistics and revenue tracking
- **Movie Management** - Add, edit, and remove movies
- **Screening Management** - Create and manage showtimes and hall assignments
- **Booking Oversight** - View and manage all customer bookings
- **User Management** - Monitor registered users

---

## 🛠️ Technology Stack

| Component | Technology |
|-----------|------------|
| **Frontend** | HTML5, CSS3, JavaScript |
| **Backend** | PHP 7.4+ |
| **Database** | MySQL 5.7+ |
| **Icons** | Font Awesome 6 |
| **Charts** | Chart.js |
| **Fonts** | Google Fonts (Poppins) |
| **Server** | Apache (XAMPP/WAMP compatible) |

---

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache Web Server
- Web browser (Chrome, Firefox, Edge recommended)

---

## 📥 Installation Guide

### Step 1: Set Up Local Server
Install XAMPP (or WAMP/MAMP) from [https://www.apachefriends.org/](https://www.apachefriends.org/)

### Step 2: Download Project
```bash
# Clone or download the project
# Place the 'cinebook' folder in your web server directory:
# - XAMPP: C:\xampp\htdocs\
# - WAMP: C:\wamp\www\
# - MAMP: /Applications/MAMP/htdocs/
```

### Step 3: Start Services
- Open XAMPP Control Panel
- Start **Apache** and **MySQL** services

### Step 4: Access the Application
Open your browser and navigate to:
```
http://localhost/cinebook/
```

### Step 5: Automatic Database Setup
**No manual database setup required!** The system automatically:
- Creates the database if it doesn't exist
- Creates all required tables
- Populates sample data on first run

---

## 🔐 Test Credentials

After installation, you can use these pre-created accounts:

| Role | Username | Password |
|------|----------|----------|
| **Admin** | `admin` | `admin123` |
| **User** | `john_doe` | `password123` |

---

## 📁 Project Structure

```
cinebook/
│
├── index.php                    # Homepage
├── auth/                        # Authentication files
│   ├── login.php               # User login
│   ├── register.php            # User registration
│   └── logout.php              # Session logout
│
├── admin/                       # Admin panel
│   ├── dashboard.php           # Admin dashboard with charts
│   ├── movies.php              # Manage movies
│   ├── add_movie.php           # Add new movie
│   ├── bookings.php            # Manage bookings
│   └── users.php               # Manage users
│
├── booking/                     # User booking flow
│   ├── movies.php              # Browse movies
│   ├── showtimes.php           # View showtimes
│   ├── seats.php               # Seat selection
│   ├── payment.php             # Payment processing
│   └── mybookings.php          # User booking history
│
├── includes/                    # Core files
│   ├── config.php              # Database configuration
│   ├── header.php              # Page header
│   └── footer.php              # Page footer
│
├── css/                         # Stylesheets
│   └── style.css               # Main CSS file
│
├── js/                          # JavaScript files
│   └── script.js               # Main JS file
│
└── images/                      # Static images
```

---

## 🗄️ Database Schema

The database contains 6 tables with proper relationships:

| Table | Description |
|-------|-------------|
| `users` | User accounts and authentication |
| `movies` | Movie catalog with details |
| `screenings` | Showtimes and hall information |
| `seats` | Individual seat availability per screening |
| `bookings` | Completed booking transactions |
| `booking_seat` | Junction table for many-to-many relationship |

---

## 🎯 How to Use

### As a User:
1. **Register** a new account or **Login** with existing credentials
2. Browse **Now Showing** movies on the homepage
3. Click **Book Now** on any movie
4. Select a **date and showtime**
5. Choose your **seats** from the interactive seat map
6. Complete **payment** (simulated)
7. View your booking in **My Bookings**

### As an Admin:
1. Login with admin credentials (`admin` / `admin123`)
2. Access the **Admin Dashboard** for overview
3. Use **Add Movie** to add new movies with posters
4. Manage existing movies from the **Movies** section
5. Monitor all **Bookings** from the admin panel

---

## 🎨 Key Features Showcase

### Homepage Features:
- Hero section with call-to-action buttons
- Featured movies grid (now showing)
- "How It Works" step-by-step guide
- "Why Choose Us" feature highlights

### Admin Dashboard:
- Real-time statistics (bookings, revenue, movies, users)
- Interactive charts (bookings trends, revenue sources)
- Recent activity log
- Quick action buttons for common tasks

### Movie Management:
- Full CRUD operations
- Image preview functionality
- Sample data quick-fill
- Form validation

---

## 🔧 Configuration

### Database Settings (includes/config.php):
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cinema_booking');
```

### Change Database Credentials:
If your MySQL has a password, update the `DB_PASS` value:
```php
define('DB_PASS', 'your_password');
```

---

## 🐛 Troubleshooting

### Common Issues and Solutions:

| Issue | Solution |
|-------|----------|
| **"Cannot connect to MySQL"** | Ensure XAMPP MySQL service is running |
| **White screen on load** | Check PHP error logs in XAMPP |
| **Database creation fails** | Verify MySQL user has CREATE DATABASE permission |
| **Login not working** | Clear browser cookies and try again |
| **Images not loading** | Check poster URL validity or use placeholder images |

### Port Conflicts:
If Apache or MySQL ports are in use:
- Apache default: Port 80
- MySQL default: Port 3306
- Change ports in XAMPP settings if needed

---

## 📊 Sample Data

The system automatically includes sample data:

### Sample Movies:
1. Avengers: Endgame (Action, 181 min)
2. The Lion King (Animation, 118 min)
3. Inception (Sci-Fi, 148 min)

### Sample Screenings:
- Multiple showtimes across different halls
- Various price points ($10 - $15)
- 100 available seats per screening

---

## 🔒 Security Features

- **Password Hashing** - bcrypt encryption for all passwords
- **SQL Injection Prevention** - Prepared statements
- **XSS Protection** - HTML escaping for dynamic content
- **Session Management** - Secure session handling
- **Role-Based Access** - User/Admin privilege separation
- **Input Validation** - Server-side validation for all forms

---

## 📱 Responsive Design

The application is fully responsive and works on:
- **Desktop** (1200px and above)
- **Tablet** (768px to 1199px)
- **Mobile** (below 768px)

---

## 🚧 Future Enhancements

- [ ] Payment gateway integration (Stripe/PayPal)
- [ ] Email notifications for booking confirmations
- [ ] Social media login (Google/Facebook)
- [ ] Movie reviews and rating system
- [ ] PDF ticket generation
- [ ] Mobile application (React Native)
- [ ] Multi-language support
- [ ] Advanced search and filtering

---

## 🤝 Contributing

Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

---

## 📄 License

This project is for educational purposes.

---

## 👨‍💻 Author

**CineBook Development Team**

---

## 🙏 Acknowledgments

- Font Awesome for icons
- Google Fonts for typography
- Chart.js for data visualization

---

## 📞 Support

For issues or questions:
1. Check the **Troubleshooting** section above
2. Ensure all requirements are met
3. Verify XAMPP services are running
4. Check PHP error logs in `xampp/php/logs/`

---

## 📝 Quick Start Commands

```bash
# 1. Start XAMPP
# Launch XAMPP Control Panel
# Start Apache and MySQL services

# 2. Access the application
# Open browser: http://localhost/cinebook/

# 3. Login with test account
# Username: admin
# Password: admin123
```

---

**🎬 Enjoy using CineBook! Book your movie tickets with ease.**
