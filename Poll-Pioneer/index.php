<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Pioneer - Home</title>
    <link rel = "icon" type= "image/x-icon" href="../assests/images/voting-box.ico">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            color: #fff;
            height: 100%;
            overflow: hidden;
        }
        .background-container {
            background: linear-gradient(135deg, #f2f2f2, #a6a6a6, #4d4d4d, #1a1a1a);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        @keyframes gradient {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }
        header {
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1;
        }
        .logo a {
            color: #fff;
            text-decoration: none;
            font-size: 1.8rem;
            font-weight: bold;
        }
        nav ul {
            list-style-type: none;
            display: flex;
            gap: 1rem;
            padding: 0;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 1.1rem;
        }
        .auth-buttons a {
            color: #000;
            background-color: #fff;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 5px;
            margin-left: 1rem;
            font-weight: bold;
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
            background-color: rgba(0, 0, 0, 0.6);
            padding: 2rem;
            border-radius: 10px;
        }
        .text-content h1 {
            font-size: 3.5rem;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .text-content h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
            font-weight: normal;
        }
        .text-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .cta-button {
            display: inline-block;
            background-color: #fff;
            color: #000;
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .socials {
            position: absolute;
            bottom: 20px;
            left: 20px;
            display: flex;
            gap: 15px;
            z-index: 1;
        }
        .socials i {
            font-size: 24px;
            color: #fff;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .socials i:hover {
            color: #a6a6a6;
        }
    </style>
</head>
<body>
    <div class="background-container">
        <header>
            <div class="logo">
                <a href="../Poll-Pioneer/index.php">Poll Pioneer</a>
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
            <i class='bx bxl-twitter' data-tooltip="Twitter"></i>
            <i class='bx bxl-instagram' data-tooltip="Instagram"></i>
            <i class='bx bxl-snapchat' data-tooltip="Snapchat"></i>
            <i class='bx bxl-facebook' data-tooltip="Facebook"></i>
            <i class='bx bxl-reddit' data-tooltip="Reddit"></i>
            <i class='bx bxl-tiktok' data-tooltip="TikTok"></i>
        </div>
    </div>
</body>
</html>