<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once '../db/config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 1) {
    header("Location: ../view/login.php?error=Access denied. Admins only.");
    exit();
}

// Fetch poll data based on the poll ID
if (!isset($_GET['poll_id'])) {
    header("Location: manage_polls.php?error=Poll ID is required.");
    exit();
}

$poll_id = intval($_GET['poll_id']);

// Fetch poll details
$query = $conn->prepare("SELECT * FROM PP_Polls WHERE PollID = ?");
$query->bind_param("i", $poll_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    header("Location: manage_polls.php?error=Poll not found.");
    exit();
}

$poll = $result->fetch_assoc();
$query->close();

// Fetch poll options
$options_query = $conn->prepare("SELECT OptionID, OptionText FROM PP_PollOptions WHERE PollID = ?");
$options_query->bind_param("i", $poll_id);
$options_query->execute();
$options_result = $options_query->get_result();
$existing_options = [];
while ($row = $options_result->fetch_assoc()) {
    $existing_options[] = $row;
}
$options_query->close();

// Handle form submission to update the poll
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gather inputs with fallback to existing poll data
    $title = $_POST['title'] ?? $poll['PollTitle'];
    $description = $_POST['description'] ?? $poll['PollDescription'];
    $type = $_POST['type'] ?? $poll['PollType'];
    $privacy = $_POST['privacy'] ?? $poll['Privacy'];
    $start_date = $_POST['start_date'] ?? $poll['PollStart'];
    $end_date = $_POST['end_date'] ?? $poll['PollEnd'];
    $voting_restrictions = $_POST['voting_restrictions'] ?? $poll['VotingRestrictions'];
    $allow_multiple_responses = isset($_POST['allow_multiple_responses']) ? 1 : $poll['AllowMultipleResponses'];
    $anonymous_voting = isset($_POST['anonymous_voting']) ? 1 : $poll['AnonymousVoting'];
    $result_display = $_POST['result_display'] ?? $poll['ResultDisplay'];
    $randomize_order = isset($_POST['randomize_order']) ? 1 : $poll['RandomizeOrder'];

    // Validate fields
    if (empty($title)) $errors[] = "Poll title is required.";
    if (empty($description)) $errors[] = "Poll description is required.";
    if (empty($type)) $errors[] = "Poll type is required.";
    if (empty($start_date) || empty($end_date)) $errors[] = "Start and end dates are required.";
    elseif (strtotime($start_date) > strtotime($end_date)) $errors[] = "Start date cannot be after the end date.";

    // Validate and update poll options
    $new_options = $_POST['options'] ?? [];
    $new_options = array_filter($new_options, fn($option) => trim($option) !== '');
    $new_options = array_values($new_options); // Reindex array

    if (empty($errors)) {
        // Begin transaction
        $conn->begin_transaction();

        try {
            // Update the poll details
            $stmt = $conn->prepare(
                "UPDATE PP_Polls 
                 SET PollTitle = ?, PollDescription = ?, PollType = ?, Privacy = ?, 
                     PollStart = ?, PollEnd = ?, VotingRestrictions = ?, 
                     AllowMultipleResponses = ?, AnonymousVoting = ?, 
                     ResultDisplay = ?, RandomizeOrder = ?
                 WHERE PollID = ?"
            );

            $stmt->bind_param(
                "ssssssssssii", 
                $title, $description, $type, $privacy, $start_date, $end_date,
                $voting_restrictions, $allow_multiple_responses, $anonymous_voting,
                $result_display, $randomize_order, $poll_id
            );
            
            $stmt->execute();
            $stmt->close();

            // Update poll options
            // 1. Update existing options
            foreach ($existing_options as $existing_option) {
                $option_id = $existing_option['OptionID'];
                $option_text = trim($new_options[$option_id] ?? '');
                if ($option_text === '') {
                    // If the option is not in the new list, delete it
                    $delete_option_query = $conn->prepare("DELETE FROM PP_PollOptions WHERE OptionID = ?");
                    $delete_option_query->bind_param("i", $option_id);
                    $delete_option_query->execute();
                    $delete_option_query->close();
                } else {
                    // Update the option
                    $update_option_query = $conn->prepare("UPDATE PP_PollOptions SET OptionText = ? WHERE OptionID = ?");
                    $update_option_query->bind_param("si", $option_text, $option_id);
                    $update_option_query->execute();
                    $update_option_query->close();
                }
            }

            // 2. Add new options
            foreach ($new_options as $index => $option_text) {
                if (!isset($existing_options[$index])) {
                    $insert_option_query = $conn->prepare("INSERT INTO PP_PollOptions (PollID, OptionText) VALUES (?, ?)");
                    $insert_option_query->bind_param("is", $poll_id, $option_text);
                    $insert_option_query->execute();
                    $insert_option_query->close();
                }
            }

            // Commit the transaction
            $conn->commit();

            // Redirect to Manage Polls on success
            header("Location: ../view/manage_polls.php?success=Poll updated successfully!");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Failed to update poll: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Poll</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
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
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
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
        .container {
            max-width: 800px;
            margin: 2rem auto;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        h1 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #fff;
        }
        label {
            display: block;
            margin: 1rem 0 0.5rem;
            color: #fff;
        }
        input[type="text"], input[type="datetime-local"], select {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            color: #fff;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
    </style>
</head>
<body>
<div class="background-container">
<header>
    <div class="logo"><a href="../view/manage_polls.php">Poll Manager</a></div>
</header>
<div class="container">
    <h1>Edit Poll</h1>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="POST">
    <label for="title">Poll Title:</label>
    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($poll['PollTitle']); ?>" required>

    <label for="description">Poll Description:</label>
    <input type="text" name="description" id="description" value="<?php echo htmlspecialchars($poll['PollDescription']); ?>" required>

    <label for="type">Poll Type:</label>
    <select name="type" id="type" required>
        <option value="multiple-choice" <?php echo ($poll['PollType'] == 'multiple-choice') ? 'selected' : ''; ?>>Multiple Choice</option>
        <option value="checkboxes" <?php echo ($poll['PollType'] == 'checkboxes') ? 'selected' : ''; ?>>Checkboxes</option>
        <option value="star-rating" <?php echo ($poll['PollType'] == 'star-rating') ? 'selected' : ''; ?>>Star Rating</option>
        <option value="likert-scale" <?php echo ($poll['PollType'] == 'likert-scale') ? 'selected' : ''; ?>>Likert Scale</option>
    </select>

    <label for="privacy">Poll Privacy:</label>
    <select name="privacy" id="privacy" required>
        <option value="public" <?php echo ($poll['Privacy'] == 'public') ? 'selected' : ''; ?>>Public</option>
        <option value="private" <?php echo ($poll['Privacy'] == 'private') ? 'selected' : ''; ?>>Private</option>
    </select>

    <label for="start_date">Start Date:</label>
    <input type="datetime-local" name="start_date" id="start_date" value="<?php echo htmlspecialchars($poll['PollStart']); ?>" required>

    <label for="end_date">End Date:</label>
    <input type="datetime-local" name="end_date" id="end_date" value="<?php echo htmlspecialchars($poll['PollEnd']); ?>" required>

    <label for="voting_restrictions">Voting Restrictions:</label>
    <input type="text" name="voting_restrictions" id="voting_restrictions" value="<?php echo htmlspecialchars($poll['VotingRestrictions']); ?>">

    <label>
        <input type="checkbox" name="allow_multiple_responses" <?php echo ($poll['AllowMultipleResponses'] ? 'checked' : ''); ?>>
        Allow Multiple Responses
    </label>

    <label>
        <input type="checkbox" name="anonymous_voting" <?php echo ($poll['AnonymousVoting'] ? 'checked' : ''); ?>>
        Allow Anonymous Voting
    </label>

    <label for="result_display">Result Display:</label>
    <select name="result_display" id="result_display" required>
        <option value="live" <?php echo ($poll['ResultDisplay'] == 'live') ? 'selected' : ''; ?>>Live</option>
        <option value="after-voting" <?php echo ($poll['ResultDisplay'] == 'after-voting') ? 'selected' : ''; ?>>After Voting</option>
        <option value="at-end" <?php echo ($poll['ResultDisplay'] == 'at-end') ? 'selected' : ''; ?>>At End</option>
    </select>

    <label>
        <input type="checkbox" name="randomize_order" <?php echo ($poll['RandomizeOrder'] ? 'checked' : ''); ?>>
        Randomize Options Order
    </label>

    <!-- Add Poll Options Here -->
    <h3>Poll Options:</h3>
    <div id="options-container">
        <?php foreach ($existing_options as $option): ?>
            <input type="text" name="options[<?php echo $option['OptionID']; ?>]" value="<?php echo htmlspecialchars($option['OptionText']); ?>" placeholder="Option <?php echo $option['OptionID']; ?>">
        <?php endforeach; ?>
        <input type="text" name="options[]" placeholder="Add new option">
    </div>

    <button type="submit">Update Poll</button>
</form>
</div>
</div>
</body>
</html>