<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Include the database connection file
require_once('../db/config.php'); // Adjust the path as necessary

// Fetch the user's ID from the session
$user_id = $_SESSION['user_id'];

// Fetch summary data for the dashboard
try {
    // Fetch the total polls created by the user
    $query_created = "SELECT COUNT(*) AS total_created FROM polls WHERE creator_id = :user_id";
    $stmt_created = $pdo->prepare($query_created);
    $stmt_created->execute(['user_id' => $user_id]);
    $result_created = $stmt_created->fetch(PDO::FETCH_ASSOC);
    $total_created = $result_created['total_created'];

    // Fetch the total polls participated in by the user
    $query_participated = "
        SELECT COUNT(DISTINCT poll_id) AS total_participated 
        FROM poll_votes 
        WHERE user_id = :user_id
    ";
    $stmt_participated = $pdo->prepare($query_participated);
    $stmt_participated->execute(['user_id' => $user_id]);
    $result_participated = $stmt_participated->fetch(PDO::FETCH_ASSOC);
    $total_participated = $result_participated['total_participated'];

    // Fetch the total votes cast by the user
    $query_votes = "SELECT COUNT(*) AS total_votes FROM poll_votes WHERE user_id = :user_id";
    $stmt_votes = $pdo->prepare($query_votes);
    $stmt_votes->execute(['user_id' => $user_id]);
    $result_votes = $stmt_votes->fetch(PDO::FETCH_ASSOC);
    $total_votes = $result_votes['total_votes'];

} catch (PDOException $e) {
    echo "Error fetching dashboard data: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        .summary-card {
            background-color: #16213e;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        .summary-card h3 {
            margin: 0;
            font-size: 1.5rem;
        }
        .summary-card p {
            margin: 5px 0;
            font-size: 1.2rem;
        }
        .section {
            margin-top: 20px;
        }
        .section h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        .action-link {
            display: inline-block;
            background-color: #00f2fe;
            color: #16213e;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 10px;
        }
        .action-link:hover {
            background-color: #4facfe;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Your Dashboard</h1>
        <div class="summary">
            <div class="summary-card">
                <h3>Total Polls Created</h3>
                <p><?php echo $total_created; ?></p>
            </div>
            <div class="summary-card">
                <h3>Total Polls Participated In</h3>
                <p><?php echo $total_participated; ?></p>
            </div>
            <div class="summary-card">
                <h3>Total Votes Cast</h3>
                <p><?php echo $total_votes; ?></p>
            </div>
        </div>

        <div class="section">
            <h2>Actions</h2>
            <a href="create_poll.php" class="action-link">Create a New Poll</a>
            <a href="continue_polls.php" class="action-link">View Polls You Participated In</a>
            <a href="results.php" class="action-link">View Poll Results</a>
        </div>
    </div>
</body>
</html>
