<?php
// Include the database connection configuration
include '../db/config.php'; 

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Start session to retrieve the logged-in user's ID
session_start();
if (!isset($_SESSION['user_id'])) {
    die('You must be logged in to view your polls.');
}

// Get the current user's ID from the session
$currentUserID = $_SESSION['user_id'];


// Query to fetch polls created by the current user
$sql = "SELECT PollID, PollTitle, PollDescription, PollType, PollEnd 
        FROM PP_Polls 
        WHERE CreatedBy = '$currentUserID'";

$result = $conn->query($sql); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assests/images/voting-box.ico">
    <title>Poll Pioneer - My Polls</title>
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
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .auth-buttons a:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }

        .content-container {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        .polls-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 1rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .poll-container {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .poll-container:hover {
            background-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 5px 20px rgba(79, 172, 254, 0.4);
            transform: translateY(-5px);
        }

        .poll-header {
            margin-bottom: 1rem;
        }

        .poll-header h3 {
            margin: 0;
            font-size: 1.8rem;
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .poll-description {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 1rem;
        }

        .poll-stats {
            display: flex;
            gap: 2rem;
            color: rgba(79, 172, 254, 0.8);
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .poll-chart {
            width: 100%;
            height: 150px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #aaa;
            font-size: 1rem;
        }

        .search-container {
            padding: 2rem 2rem 0 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .search-wrapper {
            position: relative;
            width: 100%;
            max-width: 600px;
        }

        .search-input {
            width: 100%;
            padding: 1.2rem 1.2rem 1.2rem 3.5rem;
            font-size: 1.1rem;
            border: none;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: #fff;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .search-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(79, 172, 254, 0.5);
            box-shadow: 0 0 20px rgba(79, 172, 254, 0.2);
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .search-icon {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.4rem;
        }
        .polls-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 1rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .poll-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            height: 280px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            cursor: pointer;
        }
        
        .poll-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(79, 172, 254, 0.3);
            box-shadow: 0 8px 32px rgba(79, 172, 254, 0.2);
        }

        .poll-image {
            width: 100%;
            height: 140px;
            object-fit: cover;
        }

        .poll-content {
            padding: 1.2rem;
        }
        
        .poll-content h3 {
            margin: 0 0 0.8rem 0;
            font-size: 1.3rem;
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .poll-content p {
            margin: 0;
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.5;
        }
        
        .poll-stats {
            margin-top: 2%;
            font-size: 0.9rem;
            color: rgba(79, 172, 254, 0.8);
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="background-container">
        <header>
            <div class="logo"><a href="#">Poll Pioneer</a></div>
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

        <div class="search-container">
            <div class="search-wrapper">
                <i class='bx bx-search search-icon'></i>
                <input type="text" class="search-input" placeholder="Search polls...">
            </div>
        </div>

        <div class="content-container">
            <div class="polls-grid">
            <?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Check if poll has an image
        $hasImage = !empty($row['PollImage']);
        $imageUrl = $hasImage ? "../actions/image.php?id=" . $row['PollID'] : "../assets/images/poll-image.jpg";
        ?>
        <a href="../view/vote.php?id=<?php echo $row['PollID']; ?>" class="poll-card">
            <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                 alt="<?php echo htmlspecialchars($row['PollTitle']); ?>" 
                 class="poll-image"
                 onerror="this.src='../assets/images/poll-image.jpg'">
            <div class="poll-content">
                <h3><?php echo htmlspecialchars($row['PollTitle']); ?></h3>
                <p><?php echo htmlspecialchars($row['PollDescription']); ?></p>
                <div class="poll-stats">
                    Active Now • Click to vote
                </div>
            </div>
        </a>
        <?php
    }
} else {
    echo "<p>No active polls available at the moment.</p>";
}
?>
            </div>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>