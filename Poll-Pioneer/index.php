<?php
session_start();

// Check for logout message
if(isset($_SESSION['logout_message'])) {
    echo '<div class="alert alert-success" style="background-color: rgba(0, 255, 0, 0.2); color: white; padding: 15px; text-align: center; margin-bottom: 20px;">' . 
         htmlspecialchars($_SESSION['logout_message']) . 
         '</div>';
    
    // Clear the message so it's not shown again on refresh
    unset($_SESSION['logout_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Pioneer - Home</title>
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
        
        .main-content {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 2rem;
        }
        
        .text-content {
            max-width: 800px;
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
        }
        
        .text-content h1 {
            font-size: 3.5rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .text-content h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .text-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .cta-button {
            display: inline-block;
            color: #1a1a2e;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }
        
        .socials {
            position: absolute;
            bottom: 20px;
            left: 20px;
            display: flex;
            gap: 15px;
            z-index: 1;
        }
        
        .socials a {
            color: rgba(255, 255, 255, 0.8);
            font-size: 2rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .socials a:hover {
            color: #4facfe;
            transform: translateY(-2px);
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
                    <li><a href="../Poll-Pioneer/view/home.php">Home</a></li>
                    <li><a href="../Poll-Pioneer/view/live_poll.php">Live Polls</a></li>
                    <li><a href="../Poll-Pioneer/view/create_poll.php">Create Poll</a></li>
                    <li><a href="../Poll-Pioneer/view/results.php">Results</a></li>
                    <li><a href="../Poll-Pioneer/view/about.php">About</a></li>
                    <li><a href="../Poll-Pioneer/view/contact.php">Contact</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <a href="../Poll-Pioneer/view/login.php">Login</a>
                <a href="../Poll-Pioneer/view/sign-up.php">Sign Up</a>
            </div>
        </header>

        <div class="main-content">
            <div class="text-content">
                <h1>VOICE YOUR OPINION</h1>
                <h2>SHAPE THE FUTURE WITH EVERY VOTE</h2>
                <p>Join Poll Pioneer and be part of the decision-making process. Participate in exciting polls, create your own surveys, and explore the power of collective opinion. Your voice matters in shaping our shared future!</p>
                <a href="../Poll-Pioneer/view/home.php" class="cta-button">Start Voting</a>
            </div>
        </div>

        <div class="socials">
            <a href="https://www.instagram.com/poll_pioneer" target="_blank"><i class='bx bxl-instagram'></i></a>
            <a href="https://twitter.com/PollPioneer1" target="_blank"><i class='bx bxl-twitter'></i></a>
            <a href="https://snapchat.com/t/6avFJgBG" target="_blank"><i class='bx bxl-snapchat'></i></a>
        </div>
    </div>
</body>
</html>