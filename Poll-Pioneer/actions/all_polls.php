<?php
// Include the database connection file
require_once('./db/database.php'); // Make sure this path is correct

// Fetch all polls from the database
try {
    $query = "SELECT * FROM polls ORDER BY created_at DESC"; // Adjust the query if you have specific column names
    $stmt = $pdo->prepare($query);
    $stmt->execute();
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
    <title>All Polls</title>
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
        <h1>All Polls</h1>
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
            <p>No polls available at the moment.</p>
        <?php endif; ?>
    </div>
</body>
</html>
