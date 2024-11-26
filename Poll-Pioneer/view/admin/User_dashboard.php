<?php
// Start the session
session_start();

// User verification
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 2) {
    header("Location: ../login.php");
    exit;
}

// Include the database connection file
require_once('../../db/config.php');

// Fetch user-specific data
$user_id = $_SESSION['user_id'];
try {
    // Personal Poll Statistics
    $stmt = $conn->prepare("
        SELECT 
            (SELECT COUNT(*) FROM PP_Polls WHERE CreatedBy = ?) as total_created_polls,
            (SELECT COUNT(*) FROM PP_Votes WHERE VoterID = ?) as participated_polls,
            (SELECT COUNT(*) 
             FROM PP_Votes 
             INNER JOIN PP_Polls ON PP_Votes.PollID = PP_Polls.PollID 
             WHERE PP_Polls.CreatedBy = ?) as total_votes_received
    ");
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);
    $stmt->execute();
    $poll_stats = $stmt->get_result()->fetch_assoc();

    // Active Polls
    $stmt = $conn->prepare("
        SELECT 
            PollID,
            PollTitle,
            PollStart,
            PollEnd,
            (SELECT COUNT(*) FROM PP_Votes WHERE PollID = PP_Polls.PollID) as vote_count
        FROM PP_Polls
        WHERE CreatedBy = ?
        AND NOW() BETWEEN PollStart AND PollEnd
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $active_polls = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Available Polls to Participate In
    $stmt = $conn->prepare("
        SELECT 
            p.PollID,
            p.PollTitle,
            p.PollEnd,
            u.Username as CreatedBy
        FROM PP_Polls p
        INNER JOIN PP_Users u ON p.CreatedBy = u.UserID
        WHERE p.Privacy = 'public'
        AND NOW() BETWEEN p.PollStart AND p.PollEnd
        AND p.PollID NOT IN (
            SELECT PollID FROM PP_Votes WHERE VoterID = ?
        )
        AND p.CreatedBy != ?
    ");
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $available_polls = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Recent Activity
    $stmt = $conn->prepare("
        SELECT 
            'vote' as activity_type,
            v.VotedAt as activity_time,
            p.PollTitle
        FROM PP_Votes v
        INNER JOIN PP_Polls p ON v.PollID = p.PollID
        WHERE v.VoterID = ?
        UNION ALL
        SELECT 
            'creation' as activity_type,
            CreatedAt as activity_time,
            PollTitle
        FROM PP_Polls
        WHERE CreatedBy = ?
        ORDER BY activity_time DESC
        LIMIT 10
    ");
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $recent_activity = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Poll Performance Overview
    $stmt = $conn->prepare("
        SELECT 
            p.PollTitle,
            p.PollType,
            COUNT(v.VoteID) as total_votes,
            DATEDIFF(p.PollEnd, p.PollStart) as duration_days
        FROM PP_Polls p
        LEFT JOIN PP_Votes v ON p.PollID = v.PollID
        WHERE p.CreatedBy = ?
        GROUP BY p.PollID, p.PollTitle, p.PollType, p.PollStart, p.PollEnd
        ORDER BY total_votes DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $poll_performance = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Upcoming Polls
    $stmt = $conn->prepare("
        SELECT 
            PollTitle,
            PollStart,
            DATEDIFF(PollEnd, PollStart) as duration_days
        FROM PP_Polls
        WHERE CreatedBy = ?
        AND PollStart > NOW()
        ORDER BY PollStart ASC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $upcoming_polls = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Recently Expired Polls
    $stmt = $conn->prepare("
        SELECT 
            p.PollID,
            p.PollTitle,
            p.PollEnd,
            COUNT(v.VoteID) as total_votes
        FROM PP_Polls p
        LEFT JOIN PP_Votes v ON p.PollID = v.PollID
        WHERE p.CreatedBy = ?
        AND p.PollEnd < NOW()
        GROUP BY p.PollID, p.PollTitle, p.PollEnd
        ORDER BY p.PollEnd DESC
        LIMIT 5
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $expired_polls = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    echo "Error: fetching dashboard data: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Pioneer - User Dashboard</title>
    <link rel="icon" type="image/x-icon" href="../assests/images/voting-box.ico">
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

        .content-container {
            padding: 2rem;
        }

        .welcome-header {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin-bottom: 2rem;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .welcome-header h1 {
            margin: 0;
            font-size: 2rem;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(79, 172, 254, 0.3);
            box-shadow: 0 8px 32px rgba(79, 172, 254, 0.2);
        }

        .stat-card h3 {
            margin: 0 0 1rem 0;
            font-size: 1.3rem;
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-card p {
            margin: 0;
            font-size: 2rem;
            color: #4facfe;
        }

        .section {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin-bottom: 2rem;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .section h2 {
            margin: 0 0 1.5rem 0;
            font-size: 1.8rem;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background: rgba(0, 0, 0, 0.2);
            color: #4facfe;
            font-weight: 600;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .quick-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .quick-actions a {
            color: #fff;
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            background: rgba(79, 172, 254, 0.2);
            border: 1px solid rgba(79, 172, 254, 0.5);
        }

        .quick-actions a:hover {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
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
                    <li><a href="../home.php">Home</a></li>
                    <li><a href="../live_poll.php">Live Polls</a></li>
                    <li><a href="../create_poll.php">Create Poll</a></li>
                    <li><a href="../results.php">Results</a></li>
                    <li><a href="../about.php">About</a></li>
                    <li><a href="../contact.php">Contact</a></li>
                </ul>
            </nav>
        </header>

        <div class="content-container">
            <div class="welcome-header">
                <h1>User Dashboard</h1>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Your Created Polls</h3>
                    <p><?php echo $poll_stats['total_created_polls']; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Polls Participated In</h3>
                    <p><?php echo $poll_stats['participated_polls']; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Votes Received</h3>
                    <p><?php echo $poll_stats['total_votes_received']; ?></p>
                </div>
            </div>

            <div class="section">
                <h2>Your Active Polls</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Poll Title</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Votes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($active_polls as $poll): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($poll['PollTitle']); ?></td>
                                <td><?php echo htmlspecialchars($poll['PollStart']); ?></td>
                                <td><?php echo htmlspecialchars($poll['PollEnd']); ?></td>
                                <td><?php echo htmlspecialchars($poll['vote_count']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h2>Polls to Participate In</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Poll Title</th>
                            <th>Created By</th>
                            <th>Ends On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($available_polls as $poll): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($poll['PollTitle']); ?></td>
                                <td><?php echo htmlspecialchars($poll['CreatedBy']); ?></td>
                                <td><?php echo htmlspecialchars($poll['PollEnd']); ?></td>
                                <td><a href="../vote.php?poll_id=<?php echo $poll['PollID']; ?>" class="quick-actions">Vote</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h2>Recent Activity</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Activity Type</th>
                            <th>Poll Title</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_activity as $activity): ?>
                            <tr>
                                <td><?php echo ucfirst(htmlspecialchars($activity['activity_type'])); ?></td>
                                <td><?php echo htmlspecialchars($activity['PollTitle']); ?></td>
                                <td><?php echo htmlspecialchars($activity['activity_time']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h2>Poll Performance Overview</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Poll Title</th>
                            <th>Poll Type</th>
                            <th>Total Votes</th>
                            <th>Duration (Days)</th>
                            <th>Avg. Votes/Day</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($poll_performance as $poll): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($poll['PollTitle']); ?></td>
                                <td><?php echo htmlspecialchars($poll['PollType']); ?></td>
                                <td><?php echo htmlspecialchars($poll['total_votes']); ?></td>
                                <td><?php echo htmlspecialchars($poll['duration_days']); ?></td>
                                <td><?php echo number_format($poll['total_votes'] / max(1, $poll['duration_days']), 1); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="quick-actions">
                <a href="../create_poll.php">Create New Poll</a>
                
                <a href="../my_polls.php">View My Polls</a>
                
            </div>

            <div class="section">
                <h2>Upcoming Polls</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Poll Title</th>
                            <th>Start Date</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (isset($upcoming_polls) && !empty($upcoming_polls)):
                            foreach ($upcoming_polls as $poll): 
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($poll['PollTitle']); ?></td>
                                <td><?php echo htmlspecialchars($poll['PollStart']); ?></td>
                                <td><?php echo htmlspecialchars($poll['duration_days']); ?> days</td>
                                <td>Scheduled</td>
                            </tr>
                        <?php 
                            endforeach;
                        else:
                        ?>
                            <tr>
                                <td colspan="4" class="text-center">No upcoming polls scheduled</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h2>Recently Expired Polls</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Poll Title</th>
                            <th>End Date</th>
                            <th>Total Votes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (isset($expired_polls) && !empty($expired_polls)):
                            foreach ($expired_polls as $poll): 
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($poll['PollTitle']); ?></td>
                                <td><?php echo htmlspecialchars($poll['PollEnd']); ?></td>
                                <td><?php echo htmlspecialchars($poll['total_votes']); ?></td>
                                <td>
                                    <a href="../results.php?poll_id=<?php echo $poll['PollID']; ?>" class="quick-actions">View Results</a>
                                    <a href="../duplicate_poll.php?poll_id=<?php echo $poll['PollID']; ?>" class="quick-actions">Duplicate</a>
                                </td>
                            </tr>
                        <?php 
                            endforeach;
                        else:
                        ?>
                            <tr>
                                <td colspan="4" class="text-center">No recently expired polls</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>