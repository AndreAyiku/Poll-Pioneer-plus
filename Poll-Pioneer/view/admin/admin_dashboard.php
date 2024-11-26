<?php
// Start the session
session_start();

// Enhanced admin verification
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 1) {
    header("Location: ../login.php");
    exit;
}

// Include the database connection file
require_once('../../db/config.php');

// Fetch admin-specific data
$admin_id = $_SESSION['user_id'];
try {
    // Total Users Count
    $query_total_users = "SELECT COUNT(*) AS total_users FROM PP_Users WHERE Role = 2";
    $result_total_users = $conn->query($query_total_users);
    $total_users = $result_total_users->fetch_assoc()['total_users'];

    // Total Active Polls
    $query_active_polls = "SELECT COUNT(*) AS active_polls FROM PP_Polls WHERE NOW() BETWEEN PollStart AND PollEnd";
    $result_active_polls = $conn->query($query_active_polls);
    $total_active_polls = $result_active_polls->fetch_assoc()['active_polls'];

    // Recent User Registrations
    $query_recent_users = "
        SELECT UserID, Username, Email, CreatedAt 
        FROM PP_Users 
        WHERE Role = 2
        ORDER BY CreatedAt DESC 
        LIMIT 5
    ";
    $result_recent_users = $conn->query($query_recent_users);
    $recent_users = [];
    while ($row = $result_recent_users->fetch_assoc()) {
        $recent_users[] = $row;
    }

    // Recent Polls Created
    $query_recent_polls = "
        SELECT p.PollID, p.PollTitle, p.PollStart, p.PollEnd, u.Username as CreatedBy
        FROM PP_Polls p
        JOIN PP_Users u ON p.CreatedBy = u.UserID
        ORDER BY p.CreatedAt DESC
        LIMIT 5
    ";
    $result_recent_polls = $conn->query($query_recent_polls);
    $recent_polls = [];
    while ($row = $result_recent_polls->fetch_assoc()) {
        $recent_polls[] = $row;
    }

    // User Activity Summary
    $query_activity = "
        SELECT 
            COUNT(DISTINCT v.VoterID) as active_voters,
            COUNT(v.VoteID) as total_votes
        FROM PP_Votes v
        WHERE v.VotedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ";
    $result_activity = $conn->query($query_activity);
    $activity_summary = $result_activity->fetch_assoc();

    // Recent Audit Logs
    $query_audit_logs = "
        SELECT al.Action, al.Timestamp, u.Username
        FROM PP_AuditLogs al
        JOIN PP_Users u ON al.UserID = u.UserID
        ORDER BY al.Timestamp DESC
        LIMIT 10
    ";
    $result_audit_logs = $conn->query($query_audit_logs);
    $audit_logs = [];
    while ($row = $result_audit_logs->fetch_assoc()) {
        $audit_logs[] = $row;
    }

} catch (Exception $e) {
    echo "Error fetching dashboard data: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Pioneer - Admin Dashboard</title>
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

        .admin-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .admin-actions a {
            color: #fff;
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            background: rgba(79, 172, 254, 0.2);
            border: 1px solid rgba(79, 172, 254, 0.5);
        }

        .admin-actions a:hover {
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
                <h1>Admin Dashboard</h1>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p><?php echo $total_users; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Active Polls</h3>
                    <p><?php echo $total_active_polls; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Active Voters (7 days)</h3>
                    <p><?php echo $activity_summary['active_voters']; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Votes (7 days)</h3>
                    <p><?php echo $activity_summary['total_votes']; ?></p>
                </div>
            </div>

            <div class="section">
                <h2>Recent User Registrations</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['Username']); ?></td>
                                <td><?php echo htmlspecialchars($user['Email']); ?></td>
                                <td><?php echo htmlspecialchars($user['CreatedAt']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h2>Recent Polls</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Created By</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_polls as $poll): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($poll['PollTitle']); ?></td>
                                <td><?php echo htmlspecialchars($poll['CreatedBy']); ?></td>
                                <td><?php echo htmlspecialchars($poll['PollStart']); ?></td>
                                <td><?php echo htmlspecialchars($poll['PollEnd']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h2>Recent Activity Logs</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($audit_logs as $log): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($log['Username']); ?></td>
                                <td><?php echo htmlspecialchars($log['Action']); ?></td>
                                <td><?php echo htmlspecialchars($log['Timestamp']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-actions">
                <a href="../manage_users.php">Manage Users</a>
                <a href="../manage_polls.php">Manage Polls</a>
                
            </div>
        </div>
    </div>
</body>
</html>