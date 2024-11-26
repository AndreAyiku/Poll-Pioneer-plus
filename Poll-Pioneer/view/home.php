<?php
// Start session for authentication
session_start();
include '../actions/home_backend.php';

// Handle search functionality
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
if (!empty($searchQuery)) {
    $explorePolls = array_filter($explorePolls, function($poll) use ($searchQuery) {
        return stripos($poll['title'], $searchQuery) !== false || 
               stripos($poll['description'], $searchQuery) !== false;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Pioneer - Explore Polls</title>
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
        
        .search-container {
            padding: 2rem 2rem 0 2rem;
        }
        
        .search-wrapper {
            position: relative;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
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
        
        .section {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin-bottom: 2rem;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .section-header h2 {
            margin: 0;
            font-size: 2rem;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .see-more {
            color: #fff;
            text-decoration: none;
            padding: 0.7rem 1.5rem;
            border: 2px solid rgba(79, 172, 254, 0.5);
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .see-more:hover {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }
        
        .scroll-container {
            overflow-x: auto;
            white-space: nowrap;
            padding: 1rem 0;
            scrollbar-width: thin;
            scrollbar-color: rgba(79, 172, 254, 0.5) rgba(255, 255, 255, 0.1);
        }
        
        .scroll-container::-webkit-scrollbar {
            height: 8px;
        }
        
        .scroll-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }
        
        .scroll-container::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            border-radius: 4px;
        }
        
        .poll-card {
            display: inline-block;
            width: 300px;
            height: 280px;
            margin-right: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 0;
            vertical-align: top;
            white-space: normal;
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
            border-radius: 15px 15px 0 0;
        }

        .poll-content {
            padding: 1.2rem;
        }
        
        .poll-card h3 {
            margin: 0 0 0.8rem 0;
            font-size: 1.3rem;
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .poll-card p {
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
        
        .content-container {
            padding: 2rem;
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
    
        /* Keep the existing styles, but modify the content area */
        .content-container {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 5rem; /* Add this line */
        }
        
        .polls-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.5rem;
            width: 100%;
        }
        
        .search-container {
            width: 100%;
            max-width: 800px;
            margin-bottom: 2rem;
        }
        
        .search-wrapper {
            position: relative;
            width: 100%;
        }
        
        .search-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3.5rem;
            font-size: 1.1rem;
            border: none;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: #fff;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.4rem;
        }
        
        .no-results {
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            width: 100%;
            padding: 2rem;
            font-size: 1.2rem;
        }
        .polls-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.5rem;
            padding: 2rem;
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
                        <i class='bx bx-user-circle user-icon' onclick="toggleUserDropdown()"></i>
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

        <div class="content-container">
            <!-- Search Bar -->
            <div class="search-container">
                <div class="search-wrapper">
                    <i class='bx bx-search search-icon'></i>
                    <form action="" method="GET">
                        <input 
                            type="text" 
                            name="search" 
                            class="search-input" 
                            placeholder="Search polls by title or description"
                            value="<?php echo htmlspecialchars($searchQuery); ?>"
                        >
                    </form>
                </div>
            </div>

            <!-- Polls Grid -->
            <div class="polls-container">
                <?php if (!empty($explorePolls)): ?>
                    <?php foreach ($explorePolls as $poll): ?>
                        <a href="../view/vote.php?id=<?php echo $poll['id']; ?>" class="poll-card">
                            <img src="<?php echo htmlspecialchars($poll['image']); ?>" 
                                alt="<?php echo htmlspecialchars($poll['title']); ?>" 
                                class="poll-image"
                                onerror="this.src='../assets/images/poll-image.jpg'">
                            <div class="poll-content">
                                <h3><?php echo htmlspecialchars($poll['title']); ?></h3>
                                <p><?php echo htmlspecialchars($poll['description']); ?></p>
                                <div class="poll-stats">
                                    <?php echo htmlspecialchars($poll['stats']); ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results">
                        <?php echo $searchQuery 
                            ? "No polls found matching '" . htmlspecialchars($searchQuery) . "'" 
                            : "No polls available at the moment."; 
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleUserDropdown() {
            const dropdown = document.getElementById('user-dropdown');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('user-dropdown');
            const userIcon = document.querySelector('.user-icon');
            
            if (dropdown.classList.contains('show') && 
                !dropdown.contains(e.target) && 
                e.target !== userIcon) {
                dropdown.classList.remove('show');
            }
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>