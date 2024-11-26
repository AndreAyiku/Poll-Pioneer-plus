<?php
session_start();
require_once('../db/config.php'); // Ensure this file sets up the $conn variable using mysqli

// Enable detailed error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is an administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 1) { // Role '1' for admin
    die("Access denied. Administrator access is required.");
}

$messages = []; // Array to store messages for success or error feedback

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = intval($_POST['user_id'] ?? 0);

    if ($action === 'delete' && $user_id > 0) {
        // Delete user
        $stmt = $conn->prepare("DELETE FROM PP_Users WHERE UserID = ?");
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $messages[] = "User deleted successfully.";
            } else {
                $messages[] = "Error deleting user: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $messages[] = "Error preparing delete statement: " . $conn->error;
        }
    } elseif ($action === 'update' && $user_id > 0) {
        // Update user role
        $new_role = intval($_POST['role'] ?? 0); // Ensure the role is an integer
        if ($new_role === 1 || $new_role === 2) { // Role must be 1 (Admin) or 2 (User)
            $stmt = $conn->prepare("UPDATE PP_Users SET Role = ? WHERE UserID = ?");
            if ($stmt) {
                $stmt->bind_param("ii", $new_role, $user_id);
                if ($stmt->execute()) {
                    $messages[] = "User role updated successfully.";
                } else {
                    $messages[] = "Error updating user role: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $messages[] = "Error preparing update statement: " . $conn->error;
            }
        } else {
            $messages[] = "Invalid role selected.";
        }
    } else {
        $messages[] = "Invalid action or user ID.";
    }

    // Redirect to the same page to refresh the displayed data
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch users
$users = [];
$query = "SELECT UserID, fname, lname, Username, Email, Role FROM PP_Users";
if ($result = $conn->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $result->free();
} else {
    $messages[] = "Error fetching users: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
            height: 100%;
        }

        .background-container {
            background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460, #533483);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        header {
            background-color: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo a {
            color: #fff;
            text-decoration: none;
            font-size: 2rem;
            font-weight: bold;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 30px rgba(79, 172, 254, 0.5);
        }

        nav ul {
            list-style-type: none;
            display: flex;
            gap: 2rem;
            padding: 0;
        }
        
        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }
        
        nav ul li a:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .user-menu {
            position: relative;
        }

        .user-icon-container {
            position: relative;
        }

        .user-icon {
            font-size: 2rem;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-icon:hover {
            transform: scale(1.1);
            color: #4facfe;
        }

        .user-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            min-width: 200px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            padding: 0.5rem 0;
            margin-top: 0.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-dropdown a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 0.7rem 1.2rem;
            transition: all 0.3s ease;
        }

        .user-dropdown a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #4facfe;
        }

        .user-dropdown.show {
            display: block;
        }

        .content-container {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        h1 {
            text-align: center;
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
        }

        /* Table Styling */
        .polls-table {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            overflow: hidden;
        }

        .polls-table th, .polls-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.9);
        }

        .polls-table th {
            background: rgba(255, 255, 255, 0.05);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .polls-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-buttons form {
            margin: 0;
        }

        .action-buttons button {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background: linear-gradient(45deg, #4facfe, #00f2fe);
            color: #1a1a2e;
        }

        .delete-btn {
            background: linear-gradient(45deg, #f45b5b, #ff4d4d);
            color: white;
        }

        .edit-btn:hover, .delete-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* No Users/Polls Message */
        .no-users {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="background-container">
    <header>
    <div class="logo">
        <a href="../index.php">Poll Pioneer</a>
    </div>
    <nav>
        <ul>
            <li><a href="../view/home.php">Home</a></li>
            <li><a href="../view/live_poll.php">Live Polls</a></li>
            <li><a href="../view/create_poll.php">Create Poll</a></li>
            <li><a href="../view/results.php">Results</a></li>
            <li><a href="../view/about.php">About</a></li>
            <li><a href="../view/contact.php">Contact</a></li>
        </ul>
    </nav>
    <div class="user-menu">
        <div class="user-icon-container">
            <i class='bx bx-user-circle user-icon' onclick="toggleUserDropdown()"></i>
            <div id="user-dropdown" class="user-dropdown">
                <a href="../view/admin/admin_dashboard.php">Admin Dashboard</a>
                <a href="../actions/logout.php">Logout</a>
            </div>
        </div>
    </div>
</header>
        <div class="content-container">
            <h1>Manage Users</h1>

            <?php if (!empty($messages)): ?>
                <div class="messages">
                    <?php foreach ($messages as $message): ?>
                        <p class="<?php echo strpos($message, 'Error') !== false ? 'error' : ''; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($users)): ?>
                <table class="polls-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['UserID']); ?></td>
                                <td><?php echo htmlspecialchars($user['fname']); ?></td>
                                <td><?php echo htmlspecialchars($user['lname']); ?></td>
                                <td><?php echo htmlspecialchars($user['Username']); ?></td>
                                <td><?php echo htmlspecialchars($user['Email']); ?></td>
                                <td><?php echo intval($user['Role']) === 1 ? 'Administrator' : 'User'; ?></td>
                                <td class="action-buttons">
                                    <!-- Update Role Form -->
                                    <form method="POST">
                                        <input type="hidden" name="user_id" value="<?php echo $user['UserID']; ?>">
                                        <select name="role">
                                            <option value="2" <?php echo intval($user['Role']) === 2 ? 'selected' : ''; ?>>User</option>
                                            <option value="1" <?php echo intval($user['Role']) === 1 ? 'selected' : ''; ?>>Administrator</option>
                                        </select>
                                        <button type="submit" name="action" value="update" class="edit-btn">Update</button>
                                    </form>
                                    <!-- Delete User Form -->
                                    <form method="POST">
                                        <input type="hidden" name="user_id" value="<?php echo $user['UserID']; ?>">
                                        <input type="hidden" name="role" value="<?php echo intval($user['Role']); ?>"> <!-- Hidden Role -->
                                        <button type="submit" name="action" value="delete" class="delete-btn" onclick="return confirm('Delete this user?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-users">No users found.</p>
            <?php endif; ?>
        </div>
    </div>
    <script>
    function toggleUserDropdown() {
        const dropdown = document.getElementById('user-dropdown');
        dropdown.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('user-dropdown');
        const userIcon = document.querySelector('.user-icon');
        
        if (dropdown.classList.contains('show') && 
            !dropdown.contains(e.target) && 
            e.target !== userIcon) {
            dropdown.classList.remove('show');
        }
    });
</script>
</body>
</html>