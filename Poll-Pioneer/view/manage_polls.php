<?php
session_start();

// Include database connection
require_once '../db/config.php'; // Adjust the path as per your directory structure

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 1) {
    die("Access denied: You must be an admin to manage polls.");
}

// Handle form submissions for deleting a poll
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_poll'])) {
    // Delete poll
    $id = intval($_POST['poll_id']);
    $stmt = $conn->prepare("DELETE FROM PP_Polls WHERE PollID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "Poll deleted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all polls
$polls = [];
$query = "SELECT * FROM PP_Polls";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $polls[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Polls</title>
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

        /* No Polls Message */
        .no-polls {
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
                        <?php if(isset($_SESSION['role'])): ?>
                            <?php if($_SESSION['role'] == 1): ?>
                                <a href="../view/admin/admin_dashboard.php">Admin Dashboard</a>
                            <?php else: ?>
                                <a href="../view/admin/User_dashboard.php">User Dashboard</a>
                            <?php endif; ?>
                
                            <a href="../actions/logout.php">Logout</a>
                        <?php else: ?>
                            <a href="../view/login.php">Login</a>
                            <a href="../view/sign-up.php">Sign Up</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-container">
            <h1>Manage Polls</h1>

            <?php if (count($polls) > 0): ?>
                <table class="polls-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($polls as $poll): ?>
                            <tr>
                                <td><?= htmlspecialchars($poll['PollID']) ?></td>
                                <td><?= htmlspecialchars($poll['PollTitle']) ?></td>
                                <td><?= htmlspecialchars($poll['PollDescription']) ?></td>
                                <td class="action-buttons">
                                    <form method="GET" action="edit_poll.php">
                                        <input type="hidden" name="poll_id" value="<?= htmlspecialchars($poll['PollID']) ?>">
                                        <button type="submit" class="edit-btn">Edit</button>
                                    </form>
                                    <form method="POST">
                                        <input type="hidden" name="poll_id" value="<?= htmlspecialchars($poll['PollID']) ?>">
                                        <button type="submit" name="delete_poll" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-polls">No polls available.</p>
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