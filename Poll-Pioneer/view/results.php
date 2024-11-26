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
        header("Location: ../view/dashboard.php");
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
            padding: 2rem;
        }

        @keyframes gradient {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }

        .polls-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .poll-card {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .poll-card:hover {
            transform: scale(1.05);
        }

        .poll-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .poll-card-title {
            font-size: 1.5rem;
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .poll-card-description {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 1rem;
            flex-grow: 1;
        }

        .poll-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-votes {
            color: rgba(79, 172, 254, 0.8);
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

        .result-restriction-note {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.7);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            width: 80%;
            max-width: 1000px;
            padding: 2rem;
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1rem;
            color: white;
            font-size: 2rem;
            cursor: pointer;
        }

        .chart-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .results-nav {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .results-nav button {
            margin: 0 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .results-nav button:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .results-nav button.active {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
        }
    </style>
</head>
<body>
    <div class="background-container">
        <?php if ($action == 'list'): ?>
            <div class="polls-container">
                <?php foreach($polls as $poll): ?>
                    <div class="poll-card">
                        <div class="poll-card-header">
                            <h2 class="poll-card-title"><?php echo htmlspecialchars($poll['PollTitle']); ?></h2>
                        </div>
                        <p class="poll-card-description"><?php echo htmlspecialchars($poll['PollDescription']); ?></p>
                        <div class="poll-card-footer">
                            <span class="total-votes"><?php echo $poll['TotalVotes']; ?> Votes</span>
                            <?php if($poll['can_view_results']): ?>
                                <a href="?action=details&id=<?php echo $poll['PollID']; ?>" class="view-results-btn">
                                    View Results
                                </a>
                            <?php else: ?>
                                <button class="view-results-btn" disabled>
                                    View Results
                                </button>
                            <?php endif; ?>
                        </div>
                        <?php if (!$poll['can_view_results']): ?>
                            <div class="result-restriction-note">
                                <?php echo htmlspecialchars($poll['view_reason']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($action == 'details'): ?>
            <?php if(isset($poll_results['error']) && $poll_results['error']): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($poll_results['message']); ?>
                </div>
            <?php else: 
                $poll = $poll_results['poll'];
                $votes = $poll_results['votes'];
                $total_votes = $poll_results['total_votes'];
            ?>
                <div class="results-container">
                    <h1><?php echo htmlspecialchars($poll['PollTitle']); ?></h1>
                    <p><?php echo htmlspecialchars($poll['PollDescription']); ?></p>

                    <div class="results-nav">
                    <button onclick="showChart('pie')" class="active">Pie Chart</button>
                    <button onclick="showChart('bar')">Bar Chart</button>
                    </div>

                    <div class="chart-container">
                        <div id="pieChartContainer">
                            <h2>Pie Chart Results</h2>
                            <canvas id="pieChart"></canvas>
                        </div>
                        <div id="barChartContainer" style="display:none;">
                            <h2>Bar Chart Results</h2>
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>

                    <div class="results-table-container">
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th>Option</th>
                                    <th>Votes</th>
                                    <th>Percentage</th>
                                    <th>Visualization</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($votes as $vote): ?>
                                    <tr>
                                        <td>
                                            <?php 
                                            echo htmlspecialchars(
                                                $poll['PollType'] == 'star-rating' || $poll['PollType'] == 'likert-scale' 
                                                ? "Rating " . $vote['Rating'] 
                                                : $vote['OptionText']
                                            ); 
                                            ?>
                                        </td>
                                        <td><?php echo $vote['VoteCount']; ?></td>
                                        <td><?php echo $vote['VotePercentage'] . '%'; ?></td>
                                        <td>
                                            <div class="bar-fill" style="width: <?php echo $vote['VotePercentage']; ?>%"></div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
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
    </script>
</body>
</html>