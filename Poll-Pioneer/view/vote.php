<?php
// Include database configuration
include('../db/config.php');
session_start();

// Get poll ID from URL
$poll_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Function to check if user has already voted
function hasUserVoted($conn, $poll_id, $user_id) {
    $query = "SELECT COUNT(*) as vote_count FROM PP_Votes 
              WHERE PollID = ? AND VoterID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $poll_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['vote_count'] > 0;
}

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Please log in to vote.";
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    
    // Get poll details to check voting restrictions
    $poll_query = "SELECT VotingRestrictions, AllowMultipleResponses 
                   FROM PP_Polls WHERE PollID = ?";
    $stmt = $conn->prepare($poll_query);
    $stmt->bind_param("i", $poll_id);
    $stmt->execute();
    $poll_result = $stmt->get_result();
    $poll_data = $poll_result->fetch_assoc();

    // Check voting restrictions
    if ($poll_data['VotingRestrictions'] === 'one-vote-per-user' && 
        !$poll_data['AllowMultipleResponses'] && 
        hasUserVoted($conn, $poll_id, $user_id)) {
        $_SESSION['error'] = "You have already voted in this poll.";
        header("Location: vote.php?id=" . $poll_id);
        exit();
    }

    try {
        $conn->begin_transaction();

        // Insert vote
        $vote_query = "INSERT INTO PP_Votes (PollID, VoterID, OptionSelected) 
                      VALUES (?, ?, ?)";
        $stmt = $conn->prepare($vote_query);

        // Handle different poll types
        switch($_POST['poll_type']) {
            case 'multiple-choice':
                $option_id = (int)$_POST['vote'];
                $stmt->bind_param("iii", $poll_id, $user_id, $option_id);
                $stmt->execute();
                break;

            case 'checkboxes':
                foreach($_POST['vote'] as $option_id) {
                    $option_id = (int)$option_id;
                    $stmt->bind_param("iii", $poll_id, $user_id, $option_id);
                    $stmt->execute();
                }
                break;

            case 'star-rating':
            case 'likert-scale':
                $rating = (int)$_POST['vote'];
                $stmt->bind_param("iii", $poll_id, $user_id, $rating);
                $stmt->execute();
                break;
        }

        // Add to audit log
        $audit_query = "INSERT INTO PP_AuditLogs (UserID, Action) 
                       VALUES (?, CONCAT('Voted in poll ', ?))";
        $stmt = $conn->prepare($audit_query);
        $stmt->bind_param("ii", $user_id, $poll_id);
        $stmt->execute();

        $conn->commit();
        $_SESSION['success'] = "Vote recorded successfully!";
        
        // Redirect based on result display setting
        $redirect_query = "SELECT ResultDisplay FROM PP_Polls WHERE PollID = ?";
        $stmt = $conn->prepare($redirect_query);
        $stmt->bind_param("i", $poll_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $display_setting = $result->fetch_assoc()['ResultDisplay'];

        if ($display_setting === 'after-voting' || $display_setting === 'live') {
            header("Location: results.php?id=" . $poll_id);
        } else {
            header("Location: vote.php?id=" . $poll_id);
        }
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error recording vote: " . $e->getMessage();
        header("Location: vote.php?id=" . $poll_id);
        exit();
    }
}

// Fetch poll details
$query = "SELECT p.*, u.Username as CreatorName 
          FROM PP_Polls p 
          JOIN PP_Users u ON p.CreatedBy = u.UserID 
          WHERE p.PollID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $poll_id);
$stmt->execute();
$result = $stmt->get_result();
$poll = $result->fetch_assoc();

// Fetch poll options
$options_query = "SELECT * FROM PP_PollOptions WHERE PollID = ?";
if ($poll['RandomizeOrder']) {
    $options_query .= " ORDER BY RAND()";
} else {
    $options_query .= " ORDER BY OptionID";
}
$stmt = $conn->prepare($options_query);
$stmt->bind_param("i", $poll_id);
$stmt->execute();
$options = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote - <?php echo htmlspecialchars($poll['PollTitle']); ?></title>
    <link rel = "icon" type= "image/x-icon" href="../assests/images/voting-box.ico">
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

        .auth-buttons a {
            color: #1a1a2e;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            padding: 0.7rem 1.5rem;
            text-decoration: none;
            border-radius: 8px;
            margin-left: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .auth-buttons a:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }

        .content-container {
            flex: 1;
            padding: 2rem;
        }

        .poll-container {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin: 2rem auto;
            padding: 2rem;
            max-width: 800px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .poll-container h1 {
            font-size: 2rem;
            margin: 0 0 1rem 0;
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .poll-container p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .poll-image-container {
            margin: -2rem -2rem 2rem -2rem;
            border-radius: 20px 20px 0 0;
            overflow: hidden;
            max-height: 400px;
            position: relative;
        }

        .poll-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .poll-container h1 {
            font-size: 2rem;
            margin: 0 0 1rem 0;
            padding-top: 2rem;
            margin-top: 0;
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .poll-container p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .notice {
            background: rgba(79, 172, 254, 0.1);
            border: 1px solid rgba(79, 172, 254, 0.3);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .option {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .option:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }

        .option input[type="radio"],
        .option input[type="checkbox"] {
            margin-right: 1rem;
        }

        .option label {
            cursor: pointer;
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            padding: 1rem 0;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            font-size: 2rem;
            color: rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #4facfe;
        }

        .likert-scale {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
        }

        .scale-option {
            text-align: center;
        }

        .scale-option label {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }

        .vote-button {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            color: #fff;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 2rem;
            transition: all 0.3s ease;
        }

        .vote-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
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
            <div class="auth-buttons">
                <a href="../view/login.php">Login</a>
                <a href="../view/sign-up.php">Sign Up</a>
            </div>
        </header>

        <div class="content-container">
            <div class="poll-container">
            <?php if ($poll['PollImage']): ?>
                    <div class="poll-image-container">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($poll['PollImage']); ?>" 
                             alt="Poll Image" 
                             class="poll-image">
                    </div>
                <?php endif; ?>
                <h1><?php echo htmlspecialchars($poll['PollTitle']); ?></h1>
                <p><?php echo htmlspecialchars($poll['PollDescription']); ?></p>
                
                <?php if ($poll['AnonymousVoting']): ?>
                    <p class="notice">This is an anonymous poll - your vote will not be linked to your username.</p>
                <?php endif; ?>

                <form method="POST" action="vote.php?id=<?php echo $poll_id; ?>">
                    <input type="hidden" name="poll_type" value="<?php echo $poll['PollType']; ?>">
                    
                    <?php switch($poll['PollType']): 
                          case 'multiple-choice': ?>
                            <?php while($option = $options->fetch_assoc()): ?>
                                <div class="option">
                                    <input type="radio" name="vote" 
                                           id="option_<?php echo $option['OptionID']; ?>" 
                                           value="<?php echo $option['OptionID']; ?>" required>
                                    <label for="option_<?php echo $option['OptionID']; ?>">
                                        <?php echo htmlspecialchars($option['OptionText']); ?>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                            <?php break; ?>

                          <?php case 'checkboxes': ?>
                            <?php while($option = $options->fetch_assoc()): ?>
                                <div class="option">
                                    <input type="checkbox" name="vote[]" 
                                           id="option_<?php echo $option['OptionID']; ?>" 
                                           value="<?php echo $option['OptionID']; ?>">
                                    <label for="option_<?php echo $option['OptionID']; ?>">
                                        <?php echo htmlspecialchars($option['OptionText']); ?>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                            <?php break; ?>

                          <?php case 'star-rating': ?>
                            <div class="star-rating">
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="vote" 
                                           id="star<?php echo $i; ?>" 
                                           value="<?php echo $i; ?>" required>
                                    <label for="star<?php echo $i; ?>">★</label>
                                <?php endfor; ?>
                            </div>
                            <?php break; ?>

                          <?php case 'likert-scale': ?>
                            <div class="likert-scale">
                                <?php 
                                $labels = ['Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree'];
                                for($i = 1; $i <= 5; $i++): 
                                ?>
                                    <div class="scale-option">
                                        <input type="radio" name="vote" 
                                        
                                               id="scale<?php echo $i; ?>" 
                                               value="<?php echo $i; ?>" required>
                                        <label for="scale<?php echo $i; ?>">
                                            <?php echo $labels[$i-1]; ?>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <?php break; ?>
                    <?php endswitch; ?>

                    <button type="submit" class="vote-button">Submit Vote</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>