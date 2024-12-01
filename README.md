# Poll Pioneer

Poll Pioneer is a web-based platform for creating, participating in, and exploring polls. It empowers users to voice their opinions, shape discussions, and analyze results in a dynamic and engaging way. Designed for scalability and user-friendliness, Poll Pioneer is your go-to platform for all things polling.

Live Demo
Experience Poll Pioneer live at Poll Pioneer Demo.

## Features

### Core Functionalities
- **Create Polls**: Users can create personalized polls with descriptions, images, and deadlines.
- **Vote on Polls**: Participate in live or scheduled polls and shape decisions.
- **View Results**:
  - Live results.
  - Results visible after voting or after the poll ends.
- **User Dashboard**:
  - Track created polls, participated polls, and total votes received.
  - Manage upcoming and expired polls.
  - View personal polling analytics.

### Additional Features
- **Role-based Access**:
  - Admin dashboard for managing users and polls.
  - User dashboard for individual statistics and activities.
- **Responsive Design**:
  - Optimized for desktop and mobile devices.
- **Secure Authentication**:
  - Login and signup with password encryption.
  - Session management with secure logout.
- **Poll Analytics**:
  - Detailed insights into poll participation, voting trends, and poll performance.

---

## Technologies Used

### Frontend
- **HTML5** for structure.
- **CSS3** for styling and responsiveness.
- **JavaScript** for interactivity.

### Backend
- **PHP** for server-side scripting.
- **MySQL** for database management.

### Other Tools
- **Boxicons** for icons and styling.
- **XAMPP** for local development environment.

---

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/poll-pioneer.git
   ```
2. Navigate to the project directory:
   ```bash
   cd poll-pioneer
   ```
3. Set up the database:
   - Import the `poll_pioneer.sql` file into your MySQL server.
   - Configure your database connection in `db/config.php`.

4. Start a local server:
   - If using XAMPP, move the project folder to the `htdocs` directory.
   - Start Apache and MySQL from the XAMPP control panel.

5. Access the application:
   - Open your browser and navigate to `http://localhost/poll-pioneer`.

---

## Usage

### For Users
1. Sign up and log in to your account.
2. Create polls or participate in existing ones.
3. Monitor results and manage your polls from the dashboard.

### For Admins
1. Log in with admin credentials.
2. Manage users, view all polls, and monitor platform activities.

---

## Project Structure

```
Poll-Pioneer/
├── actions/        # Backend actions (e.g., login, registration, polls handling)
├── assets/         # Static assets (images, CSS, icons)
├── db/             # Database connection and configuration
├── view/           # Frontend views (home, login, dashboard)
└── index.php       # Landing page
```

---

## Contributing

We welcome contributions to improve Poll Pioneer! Here's how you can get started:
1. Fork the repository.
2. Create a new branch for your feature/bugfix:
   ```bash
   git checkout -b feature-name
   ```
3. Commit your changes:
   ```bash
   git commit -m "Add feature-name"
   ```
4. Push to your branch:
   ```bash
   git push origin feature-name
   ```
5. Open a pull request on the main repository.

---

## License

This project is licensed under the MIT License. See the `LICENSE` file for details.

---

## Acknowledgments

- **Developers**: [gilberttetteh,AndreAyiku,sedemabla,jadarko55]
- **Technologies**: PHP, MySQL, JavaScript
- **Community**: All contributors and users who provided valuable feedback.

