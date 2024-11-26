<?php
// Include database configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../db/config.php');
session_start();

if (!$conn) {
    die("Database connection failed. Please check your configuration.");
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please log in to view poll results.";
    header("Location: ../view/login.php");
    exit();
}

// Function to check if a poll's results can be viewed
function canViewPollResults($conn, $poll_id, $user_id) {
    $query = "SELECT 
        p.ResultDisplay, 
        p.PollEnd,  /* Changed from EndDate */
        p.CreatedBy,
        (SELECT COUNT(*) FROM PP_Votes WHERE PollID = p.PollID) as TotalVotes,
        (SELECT COUNT(*) FROM PP_Votes WHERE PollID = p.PollID AND VoterID = ?) as UserVotes
      FROM PP_Polls p 
      WHERE PollID = ?";


    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $poll_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $poll = $result->fetch_assoc();

    // If poll creator is viewing their own poll, always allow
    if ($poll['CreatedBy'] == $user_id) {
        return [
            'can_view' => true, 
            'reason' => 'Poll Creator'
        ];
    }

    $current_time = time();
$end_time = strtotime($poll['PollEnd']);  // Changed from EndDate

    switch($poll['ResultDisplay']) {
        case 'live':
            return [
                'can_view' => true, 
                'reason' => 'Live results always visible'
            ];
        
        case 'after-voting':
            return [
                'can_view' => $poll['UserVotes'] > 0, 
                'reason' => 'Results visible after voting'
            ];
        
        case 'after-end':
            return [
                'can_view' => $current_time > $end_time, 
                'reason' => 'Results available after poll ends'
            ];
        
        default:
            return [
                'can_view' => false, 
                'reason' => 'Results not accessible'
            ];
    }
}

// Fetch all polls with their current status
function fetchAllPolls($conn, $user_id) {
    $query = "SELECT 
            p.PollID, 
            p.PollTitle, 
            p.PollDescription, 
            p.PollType, 
            p.PollImage,  
            p.ResultDisplay,
            p.PollEnd,  
            (SELECT COUNT(*) FROM PP_Votes WHERE PollID = p.PollID) as TotalVotes
           FROM PP_Polls p
           ORDER BY p.CreatedAt DESC";

    $result = $conn->query($query);
    $polls = [];

    while ($poll = $result->fetch_assoc()) {
        // Check result viewing permissions
        $view_permissions = canViewPollResults($conn, $poll['PollID'], $user_id);
        
        $poll['can_view_results'] = $view_permissions['can_view'];
        $poll['view_reason'] = $view_permissions['reason'];
        
        // Additional status checks
        $current_time = time();
        $end_time = strtotime($poll['PollEnd']);
        $poll['is_expired'] = $current_time > $end_time;
        
        $polls[] = $poll;
    }

    return $polls;
}

// Fetch specific poll results
function fetchPollResults($conn, $poll_id, $user_id) {
    // Check if user can view results
    $view_permissions = canViewPollResults($conn, $poll_id, $user_id);
    
    if (!$view_permissions['can_view']) {
        return [
            'error' => true,
            'message' => $view_permissions['reason']
        ];
    }

    // Fetch poll details
    $poll_query = "SELECT * FROM PP_Polls WHERE PollID = ?";
    $stmt = $conn->prepare($poll_query);
    $stmt->bind_param("i", $poll_id);
    $stmt->execute();
    $poll_result = $stmt->get_result();
    $poll = $poll_result->fetch_assoc();

    // Fetch poll options and vote counts
    $vote_query = "";
    switch($poll['PollType']) {
        case 'multiple-choice':
        case 'checkboxes':
            $vote_query = "
                SELECT 
                    po.OptionID, 
                    po.OptionText, 
                    COUNT(pv.VoteID) as VoteCount,
                    ROUND(COUNT(pv.VoteID) * 100.0 / (SELECT COUNT(*) FROM PP_Votes WHERE PollID = ?), 2) as VotePercentage
                FROM 
                    PP_PollOptions po
                LEFT JOIN 
                    PP_Votes pv ON po.OptionID = pv.OptionSelected
                WHERE 
                    po.PollID = ?
                GROUP BY 
                    po.OptionID, po.OptionText
                ORDER BY 
                    VoteCount DESC
            ";
            break;
        
        case 'star-rating':
        case 'likert-scale':
            $vote_query = "
                SELECT 
                    OptionSelected as Rating, 
                    COUNT(*) as VoteCount,
                    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM PP_Votes WHERE PollID = ?), 2) as VotePercentage
                FROM 
                    PP_Votes
                WHERE 
                    PollID = ?
                GROUP BY 
                    Rating
                ORDER BY 
                    Rating
            ";
            break;
    }

    $stmt = $conn->prepare($vote_query);
    $stmt->bind_param("ii", $poll_id, $poll_id);
    $stmt->execute();
    $vote_result = $stmt->get_result();
    $votes = $vote_result->fetch_all(MYSQLI_ASSOC);

    // Total votes
    $total_votes_query = "SELECT COUNT(*) as TotalVotes FROM PP_Votes WHERE PollID = ?";
    $stmt = $conn->prepare($total_votes_query);
    $stmt->bind_param("i", $poll_id);
    $stmt->execute();
    $total_votes_result = $stmt->get_result();
    $total_votes = $total_votes_result->fetch_assoc()['TotalVotes'];

    return [
        'error' => false,
        'poll' => $poll,
        'votes' => $votes,
        'total_votes' => $total_votes
    ];
}

// Handle different request types
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

if ($action == 'details' && !isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to view poll results.";
    $_SESSION['redirect_after_login'] = "results.php?action=details&id=" . (isset($_GET['id']) ? $_GET['id'] : '');
    header("Location: ../view/login.php");
    exit();
}

switch($action) {
    case 'list':
        // Fetch and display all polls
        $polls = fetchAllPolls($conn, $_SESSION['user_id']);
        break;

    case 'details':
        // Fetch specific poll results
        $poll_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $poll_results = fetchPollResults($conn, $poll_id, $_SESSION['user_id']);
        break;

    default:
        $_SESSION['error'] = "Invalid request.";
        header("Location: ../view/live_poll.php");
        exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Results</title>
    <link rel="icon" type="image/x-icon" href="../assests/images/voting-box.ico">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Reuse styles from previous results page with some modifications */
        :root {
            --bg-primary: #1a1a2e;
            --bg-secondary: #16213e;
            --accent-primary: #4facfe;
            --accent-secondary: #00f2fe;
            --text-primary: #ffffff;
            --text-secondary: rgba(255,255,255,0.7);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--bg-primary), var(--bg-secondary));
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header Styles */
        

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
        .view-results-btn {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .view-results-btn:disabled {
            background: rgba(255, 255, 255, 0.2);
            cursor: not-allowed;
        }

        /* Card Styles */
        .card {
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 0.5rem;
        }

        .card-title {
            font-size: 1.5rem;
            color: var(--accent-primary);
            font-weight: 600;
        }

        /* Results Grid */
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .result-item {
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
        }

        .result-item-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .result-item-title {
            font-weight: 600;
            color: var(--text-primary);
        }

        .result-percentage {
            color: var(--accent-primary);
        }

        .result-bar {
            height: 10px;
            background: rgba(255,255,255,0.1);
            border-radius: 5px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .result-bar-fill {
            height: 100%;
            background: linear-gradient(45deg, var(--accent-secondary), var(--accent-primary));
        }

        /* Chart Containers */
        .chart-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .chart-card {
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 1.5rem;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .chart-container {
                grid-template-columns: 1fr;
            }
        }

        /* Navigation and Interaction Styles */
        .chart-nav {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .chart-nav button {
            background: rgba(255,255,255,0.1);
            color: var(--text-primary);
            border: none;
            padding: 0.5rem 1rem;
            margin: 0 0.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .chart-nav button.active {
            background: linear-gradient(45deg, var(--accent-secondary), var(--accent-primary));
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
        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="user-icon-container">
                <i class='bx bx-user-circle user-icon' id="userIcon"></i>
                <div id="user-dropdown" class="user-dropdown">
                    <?php if(isset($_SESSION['role'])): ?>
                        <?php if($_SESSION['role'] == 1): ?>
                            <a href="../view/admin/admin_dashboard.php">Admin Dashboard</a>
                        <?php else: ?>
                            <a href="../view/admin/User_dashboard.php">User Dashboard</a>
                        <?php endif; ?>
                        
                        <a href="../actions/logout.php">Logout</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="auth-buttons">
                <a href="../view/login.php">Login</a>
                <a href="../view/sign-up.php">Sign Up</a>
            </div>
        <?php endif; ?>
    </div>
</header>
        <?php 
        // Display error messages if any
        if(isset($_SESSION['error'])) {
            echo '<div class="error-message" style="background: rgba(255,0,0,0.2); color: white; padding: 1rem; text-align: center;">' . 
                 htmlspecialchars($_SESSION['error']) . 
                 '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <div class="container">
        <?php if ($action == 'list'): ?>
            <div class="results-grid">
                <?php foreach($polls as $poll): ?>
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><?php echo htmlspecialchars($poll['PollTitle']); ?></h2>
                        </div>
                        
                        <div class="result-item-header">
                            <span>Total Votes: <?php echo $poll['TotalVotes']; ?></span>
                            <?php if($poll['can_view_results']): ?>
                                <a href="?action=details&id=<?php echo $poll['PollID']; ?>" class="view-results-btn">
                                    View Results
                                </a>
                            <?php else: ?>
                                <span class="text-muted">
                                    <?php echo htmlspecialchars($poll['view_reason']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($action == 'details' && isset($_SESSION['user_id'])): ?>
            <?php if(isset($poll_results['error']) && $poll_results['error']): ?>
                <div class="card">
                    <p class="text-error"><?php echo htmlspecialchars($poll_results['message']); ?></p>
                </div>
            <?php else: 
                $poll = $poll_results['poll'];
                $votes = $poll_results['votes'];
                $total_votes = $poll_results['total_votes'];
            ?>
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title"><?php echo htmlspecialchars($poll['PollTitle']); ?></h1>
                    </div>
                    <p><?php echo htmlspecialchars($poll['PollDescription']); ?></p>
                    <p>Total Votes: <?php echo $total_votes; ?></p>
                </div>

                <div class="chart-nav">
                    <button onclick="showChart('pie')" class="active">Pie Chart</button>
                    <button onclick="showChart('bar')">Bar Chart</button>
                </div>

                <div class="chart-container">
                    <div class="chart-card" id="pieChartContainer">
                        <h2>Pie Chart Results</h2>
                        <canvas id="pieChart"></canvas>
                    </div>
                    <div class="chart-card" id="barChartContainer" style="display:none;">
                        <h2>Bar Chart Results</h2>
                        <canvas id="barChart"></canvas>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Vote Breakdown</h2>
                    </div>
                    <div class="results-grid">
                        <?php foreach($votes as $vote): ?>
                            <div class="result-item">
                                <div class="result-item-header">
                                    <span class="result-item-title">
                                        <?php 
                                        echo htmlspecialchars(
                                            $poll['PollType'] == 'star-rating' || $poll['PollType'] == 'likert-scale' 
                                            ? "Rating " . $vote['Rating'] 
                                            : $vote['OptionText']
                                        ); 
                                        ?>
                                    </span>
                                    <span class="result-percentage">
                                        <?php echo $vote['VotePercentage'] . '%'; ?>
                                    </span>
                                </div>
                                <div class="result-bar">
                                    <div class="result-bar-fill" style="width: <?php echo $vote['VotePercentage']; ?>%"></div>
                                </div>
                                <span>Votes: <?php echo $vote['VoteCount']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        // Get references to the user icon and dropdown
    const userIcon = document.getElementById('userIcon');
    const userDropdown = document.getElementById('user-dropdown');

    // Toggle dropdown when user icon is clicked
    userIcon.addEventListener('click', function(e) {
        e.stopPropagation(); // Prevent event from propagating to window
        userDropdown.classList.toggle('show');
    });

    // Close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        // Check if the dropdown is currently shown
        if (userDropdown.classList.contains('show')) {
            // Check if the click is outside the dropdown and user icon
            if (!userDropdown.contains(e.target) && e.target !== userIcon) {
                userDropdown.classList.remove('show');
            }
        }
    });

        <?php if (isset($_SESSION['user_id'])): ?>
        // Chart.js setup
        const pieData = {
            labels: [<?php foreach($votes as $vote) { echo "'" . htmlspecialchars($vote['OptionText'] ?? "Rating " . $vote['Rating']) . "',"; } ?>],
            datasets: [{
                data: [<?php foreach($votes as $vote) { echo $vote['VoteCount'] . ","; } ?>],
                backgroundColor: [
                    '#ff6384',
                    '#36a2eb',
                    '#ffcd56',
                    '#4bc0c0',
                    '#9966ff',
                    '#ff9f40'
                ]
            }]
        };

        const barData = {
            labels: pieData.labels,
            datasets: [{
                label: 'Votes',
                data: pieData.datasets[0].data,
                backgroundColor: pieData.datasets[0].backgroundColor,
            }]
        };

        const pieConfig = {
            type: 'pie',
            data: pieData,
        };

        const barConfig = {
            type: 'bar',
            data: barData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        const pieChart = new Chart(document.getElementById('pieChart'), pieConfig);
        const barChart = new Chart(document.getElementById('barChart'), barConfig);

        function showChart(type) {
            const pieContainer = document.getElementById('pieChartContainer');
            const barContainer = document.getElementById('barChartContainer');
            const navButtons = document.querySelectorAll('.results-nav button');

            if (type === 'pie') {
                pieContainer.style.display = 'block';
                barContainer.style.display = 'none';
            } else if (type === 'bar') {
                pieContainer.style.display = 'none';
                barContainer.style.display = 'block';
            }

            navButtons.forEach(button => button.classList.remove('active'));
            document.querySelector(`.results-nav button[onclick="showChart('${type}')"]`).classList.add('active');
        }
        <?php endif; ?>
    </script>
</body>
</html>