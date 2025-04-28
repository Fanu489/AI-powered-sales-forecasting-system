# AI-Powered Sales Forecasting System

This is a web-based intelligent system designed to help companies analyze their sales data and generate insightful reports. It allows users to upload sales records and visualize performance using charts and downloadable reports. Admins can manage users, view reports, and customize site settings from a secure dashboard.

---

## 👨‍💻 Developed by

**Fanuel Omondi**

---

## 🚀 Features

- 📥 Upload CSV sales data
- 📊 Auto-generate line and bar charts
- 📈 Forecast sales trends
- 📝 Download reports
- 👤 Manage user accounts
- ⚙️ Admin dashboard to manage reports, users, and settings
- 🔐 Secure login/logout system for admin
- 🌐 Responsive and clean UI

---

## 🛠 Tech Stack

- **Frontend**: HTML, CSS, Font Awesome
- **Backend**: PHP
- **Database**: MySQL
- **Server**: Apache (via XAMPP)

---

## 📁 Project Structure
/AI-powered-sales-forecasting-system
    ├── /views
    │    ├── dashboard.php
    │    ├── register.php
    │    ├── upload.php
    │    ├── report.php
    │    ├── logout.php
    │    ├── profile.php
    │    ├── admin_dashboard.php  (if you add admin functionality)
    ├── /assets
    │    ├── /css
    │    │    ├── main.css
    │    ├── /js
    │    │    ├── main.js
    │    ├── /images
    │    │    ├── logo.png
    ├── /config
    │    ├── config.php  (configuration settings)
    ├── /uploads
    │    ├── sales_data/ (user uploaded sales data)
    ├── /logs
    │    ├── error_log.txt
    ├── /includes
    │    ├── db.php  (Database connection)
    │    ├── header.php
    │    ├── footer.php
    │    ├── email.php  (if sending notifications)
    ├── /tests
    │    ├── test_upload.php
    │    ├── test_sales.php
    ├── index.php
    ├
    ├── .gitignore
    ├── README.md


## ⚙️ Setup Instructions

1. **Clone or download** this repository.
2. **Start Apache and MySQL** using XAMPP.
3. **Import the database** from `sales_forecast.sql` into phpMyAdmin.
4. Update your **DB credentials** in `includes/db.php`.
5. Visit the system at `http://localhost/project-root/admin/admin_login.php`.

---

## 🧪 Sample Admin Login


> ⚠️ Change the default password after first login for security.

---

## ✅ Requirements

- PHP 7.x or higher
- MySQL
- XAMPP, WAMP, or LAMP stack
- Web browser

---

## 📜 License

This project is open-source and available for educational and non-commercial use.

---

## 📬 Contact

For inquiries or feedback, reach out to:

📧 fanuelomondi489@gmail.com  
📍 Mount Kenya University

---

