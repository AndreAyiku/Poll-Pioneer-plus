<?php
// Start the session to access user data
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: validate_login.php"); // Redirect to login page if not logged in
    exit;
}

// Include the database connection file
require_once('../db/database.php'); // Adjust the path as necessary

// Fetch the user's ID from the session
$user_id = $_SESSION['user_id'];

// Fetch polls the user has created or participated in
try {
    // Query for polls created or participated in by the user
    $query = "
        SELECT DISTINCT polls.*
        FROM polls
        LEFT JOIN poll_votes ON polls.id = poll_votes.poll_id
        WHERE polls.creator_id = :user_id OR poll_votes.user_id = :user_id
        ORDER BY polls.created_at DESC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching polls: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Polls</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #0f3460;
            color: white;
        }
        .container {
            padding: 20px;
        }
        .poll-card {
            background-color: #16213e;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        .poll-card h3 {
            margin: 0;
        }
        .poll-card p {
            margin: 5px 0;
        }
        .poll-card .stats {
            font-size: 0.9rem;
            color: #b2becd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Polls</h1>
        <?php if (!empty($polls)): ?>
            <?php foreach ($polls as $poll): ?>
                <div class="poll-card">
                    <h3><?php echo htmlspecialchars($poll['title']); ?></h3>
                    <p><?php echo htmlspecialchars($poll['description']); ?></p>
                    <div class="stats">
                        <strong>Votes:</strong> <?php echo htmlspecialchars($poll['votes']); ?> |
                        <strong>Ends:</strong> <?php echo htmlspecialchars($poll['end_date']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have not created or participated in any polls yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
