# 💸 MoneyMate — Smart Expense Tracker

![MoneyMate Banner](assets/img/banner.png)

**MoneyMate** is a sleek, modern, and powerful web-based expense tracking application designed to help you take control of your finances. With a premium dark-mode interface and real-time analytics, managing your daily spending has never been this elegant.

🚀 **Live Demo**: [moneymate-smartexpensetracker.free.nf](https://moneymate-smartexpensetracker.free.nf/)


## 📸 Screenshots

| Login Page | User Dashboard |
|------------|----------------|
| ![Login](assets/img/login.png) | ![User Dashboard](assets/img/user_dashboard.png) |

| Admin Panel | Analytics & Insights |
|-------------|----------------------|
| ![Admin Panel](assets/img/admin_panel.png) | ![Analytics](assets/img/analytics.png) |


---

## ✨ Key Features

- 📊 **Dynamic Dashboard**: Visualize your spending with interactive doughnut charts powered by Chart.js.
- 🧠 **AI-Powered Insights**: Get personalized feedback on your spending habits and top categories.
- 🌓 **Dark Mode First**: A beautiful, premium dark interface with glassmorphism effects.
- 📥 **CSV Export**: Easily export your transaction history for external accounting.
- ⚡ **Admin Control**: Dedicated admin panel to monitor system-wide transactions.
- 🔒 **Secure Auth**: Integrated user registration and login system.

---

## 🛠️ Tech Stack

- **Frontend**: HTML5, Vanilla CSS3 (Custom Design System), JavaScript (ES6+)
- **Backend**: PHP 8.x
- **Database**: MySQL / MariaDB
- **Libraries**: [Chart.js](https://www.chartjs.org/) for data visualization.
- **Fonts**: [Outfit](https://fonts.google.com/specimen/Outfit) & [JetBrains Mono](https://fonts.google.com/specimen/JetBrains+Mono) via Google Fonts.

---

## 📂 Repository Structure

The project follows a professional directory structure for scalability and maintainability:

```text
MoneyMate/
├── api/             # Backend PHP endpoints (AJAX)
├── assets/          # Static assets
│   ├── css/         # Stylesheets
│   ├── js/          # Frontend logic
│   └── img/         # Images and banners
├── docs/            # Project documentation and reports
├── includes/        # Database configuration and core logic
├── index.html       # Landing / Auth page
├── dashboard.php    # User dashboard
├── admin.php        # Admin panel
└── logout.php       # Session termination
```

---

## 🚀 Getting Started

### Prerequisites

- A local server environment like **XAMPP**, **WAMP**, or **Laragon**.
- PHP 7.4 or higher.
- MySQL Database.

### Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/Fikshan7/MoneyMate.git
   ```

2. **Database Setup**:
   - Create a new database in phpMyAdmin named `moneymate`.
   - Import the provided SQL schema (if available) or create `users` and `expenses` tables.
   - Update `includes/db.php` with your database credentials.

3. **Configure Connection**:
   Open `includes/db.php` and update the connection details:
   ```php
   $conn = mysqli_connect("localhost", "your_username", "your_password", "moneymate");
   ```

4. **Run the App**:
   Move the folder to your `htdocs` directory and navigate to `http://localhost/MoneyMate` in your browser.

---

## 🌐 Deployment

This project is optimized for deployment on shared hosting services like **InfinityFree**. 

- **Hosting**: InfinityFree (Free PHP & MySQL Hosting)
- **Deployment Process**: Upload files via FTP/FileZilla and import the database via phpMyAdmin.


---

## 🤝 Contributing

Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

---

## 👤 Author

**Garvit Jain**
- GitHub: [@Fikshan7](https://github.com/Fikshan7)

