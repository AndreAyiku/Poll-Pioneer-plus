<?php
// Include database connection
include '../db/config.php';

// Start session
session_start();

// Function to get random polls for explore section
function getExplorePollsData($conn) {
    $currentDateTime = date('Y-m-d H:i:s');
    $sql = "SELECT PollID, PollTitle, PollDescription, PollType, PollImage 
            FROM PP_Polls 
            WHERE PollEnd > '$currentDateTime'
            ORDER BY RAND()
            LIMIT 7";  // Limit to 6 random polls

    $result = $conn->query($sql);
    $polls = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Determine image URL
            $imageUrl = !empty($row['PollImage']) 
                ? "../actions/image.php?id=" . $row['PollID'] 
                : "../assets/images/poll-image.jpg";

            $polls[] = [
                'id' => $row['PollID'],
                'title' => $row['PollTitle'],
                'description' => $row['PollDescription'],
                'image' => $imageUrl,
                'stats' => generatePollStats($conn, $row['PollID'])
            ];
        }
    }

    return $polls;
}

// Function to get live polls
function getLivePollsData($conn) {
    $currentDateTime = date('Y-m-d H:i:s');
    $sql = "SELECT PollID, PollTitle, PollDescription, PollType, PollImage, 
                   PollStart, PollEnd, 
                   (SELECT COUNT(*) FROM PP_Votes WHERE PollID = p.PollID) as VoteCount
            FROM PP_Polls p
            WHERE PollStart <= '$currentDateTime' AND PollEnd > '$currentDateTime'
            LIMIT 6";  // Limit to 6 live polls

    $result = $conn->query($sql);
    $polls = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Determine image URL
            $imageUrl = !empty($row['PollImage']) 
                ? "../actions/image.php?id=" . $row['PollID'] 
                : "../assets/images/poll-image.jpg";

            $polls[] = [
                'id' => $row['PollID'],
                'title' => $row['PollTitle'],
                'description' => $row['PollDescription'],
                'image' => $imageUrl,
                'stats' => generateLivePollStats($row['VoteCount'], $row['PollStart'], $row['PollEnd'])
            ];
        }
    }

    return $polls;
}

// Generate poll stats
function generatePollStats($conn, $pollId) {
    // Get total vote count
    $sqlVotes = "SELECT COUNT(*) as VoteCount FROM PP_Votes WHERE PollID = ?";
    $stmt = $conn->prepare($sqlVotes);
    $stmt->bind_param("i", $pollId);
    $stmt->execute();
    $result = $stmt->get_result();
    $voteData = $result->fetch_assoc();

    return $voteData['VoteCount'] . " votes • Ending Soon";
}

// Generate live poll stats
function generateLivePollStats($voteCount, $startDate, $endDate) {
    // Calculate time remaining
    $now = new DateTime();
    $end = new DateTime($endDate);
    $interval = $now->diff($end);

    // Format time remaining
    if ($interval->invert) {
        return "Ended";
    } elseif ($interval->days > 0) {
        $timeRemaining = $interval->days . " days left";
    } else {
        $timeRemaining = $interval->h . " hours left";
    }

    return $voteCount . " votes • " . $timeRemaining;
}
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


// Fetch polls for sections
$explorePolls = getExplorePollsData($conn);
$livePolls = getLivePollsData($conn);
$polls = fetchAllPolls($conn, $_SESSION['user_id']); 