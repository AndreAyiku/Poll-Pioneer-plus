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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css', rel='stylesheet'>
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
            color: #333;
        }

        h1, h2 {
            text-align: center;
            color: #444;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Action Buttons */
        table form {
            display: inline-block;
        }

        table form button {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 3px;
            background-color: #0275d8;
            color: white;
            border: none;
            cursor: pointer;
        }

        table form button:hover {
            background-color: #025aa5;
        }

        /* Delete Button */
        table form button[name="delete_poll"] {
            background-color: #d9534f;
        }

        table form button[name="delete_poll"]:hover {
            background-color: #c9302c;
        }

        /* Edit Button */
        table form button[name="edit_poll"] {
            background-color: #5cb85c;
        }

        table form button[name="edit_poll"]:hover {
            background-color: #4cae4c;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            table th, table td {
                font-size: 12px;
                padding: 8px;
            }

            table form button {
                font-size: 10px;
                padding: 4px 8px;
            }
        }
    </style>
</head>
<body>
    <h1>Manage Polls</h1>

    <!-- List Existing Polls -->
    <h2>Existing Polls</h2>
    <?php if (count($polls) > 0): ?>
        <table border="1">
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
                        <td>
                            <!-- Edit Form -->
                            <form method="GET" action="edit_poll.php" style="display:inline-block;">
                                <input type="hidden" name="poll_id" value="<?= htmlspecialchars($poll['PollID']) ?>">
                                <button type="submit" name="edit_poll">Edit</button>
                            </form>
                            <!-- Delete Form -->
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="poll_id" value="<?= htmlspecialchars($poll['PollID']) ?>">
                                <button type="submit" name="delete_poll" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No polls available.</p>
    <?php endif; ?>
</body>
</html>