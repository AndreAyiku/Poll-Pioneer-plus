<?php
session_start();
require_once('../db/config.php'); // Ensure this file sets up the $conn variable using mysqli

// Enable detailed error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is an administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 1) { // Role '1' for admin
    echo "Access denied. Administrator access is required.";
    exit();
}

$messages = []; // Array to store messages for success or error feedback

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = intval($_POST['user_id'] ?? 0);

    if ($action === 'delete' && $user_id > 0) {
        // Delete user
        $stmt = $conn->prepare("DELETE FROM PP_Users WHERE UserID = ?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $messages[] = "User deleted successfully.";
        } else {
            $messages[] = "Error deleting user: " . $conn->error;
        }
        $stmt->close();
    } elseif ($action === 'update' && $user_id > 0) {
        // Update user role
        $new_role = $_POST['role'] ?? '';
        if ($new_role === 1 || $new_role === 2) { // Compare roles as strings
            $stmt = $conn->prepare("UPDATE PP_Users SET Role = ? WHERE UserID = ?");
            $stmt->bind_param("si", $new_role, $user_id); // Bind role as string
            if ($stmt->execute()) {
                $messages[] = "User role updated successfully.";
            } else {
                $messages[] = "Error updating user role: " . $conn->error;
            }
            $stmt->close();
        } else {
            $messages[] = "Invalid role selected.";
        }
    } else {
        $messages[] = "Invalid action or user ID.";
    }
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
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; }
        h1 { color: #333; }
        .messages { margin-bottom: 20px; }
        .messages p { padding: 10px; background-color: #f8d7da; border: 1px solid #f5c2c7; border-radius: 4px; color: #842029; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        table th { background-color: #f2f2f2; }
        form { display: inline; }
        button { padding: 8px 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button.delete { background-color: #dc3545; }
        button:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <h1>Manage Users</h1>

    <?php if (!empty($messages)): ?>
        <div class="messages">
            <?php foreach ($messages as $message): ?>
                <p><?php echo htmlspecialchars($message); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <table>
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
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['UserID']); ?></td>
                        <td><?php echo htmlspecialchars($user['fname']); ?></td>
                        <td><?php echo htmlspecialchars($user['lname']); ?></td>
                        <td><?php echo htmlspecialchars($user['Username']); ?></td>
                        <td><?php echo htmlspecialchars($user['Email']); ?></td>
                        <td><?php echo $user['Role'] === 1 ? 'Administrator' : 'User'; ?></td>
                        <td>
                            <!-- Update Role Form -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['UserID']; ?>">
                                <select name="role">
                                    <option value="2" <?php echo $user['Role'] === 2 ? 'selected' : ''; ?>>User</option>
                                    <option value="1" <?php echo $user['Role'] === 1 ? 'selected' : ''; ?>>Administrator</option>
                                </select>
                                <button type="submit" name="action" value="update">Update</button>
                            </form>
                            <!-- Delete User Form -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['UserID']; ?>">
                                <button type="submit" name="action" value="delete" onclick="return confirm('Delete this user?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>