<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Pioneer - Live Polls</title>
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

        .poll-container {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .poll-container:hover {
            background-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 5px 20px rgba(79, 172, 254, 0.4);
        }

        .poll-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        }

        .poll-stats {
            display: flex;
            gap: 2rem;
            color: rgba(79, 172, 254, 0.8);
            font-size: 1rem;
        }

        .poll-chart {
            width: 100%;
            height: 200px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #aaa;
            font-size: 1rem;
        }

        .vote-now-button {
            display: inline-block;
            padding: 0.7rem 1.5rem;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .vote-now-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
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
                    <li><a href="#">About</a></li>
                    <li><a href="#">Contact</a></li>
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
                <input type="text" class="search-input" placeholder="Search live polls...">
            </div>
        </div>

        <div class="content-container">
            <!-- Poll 1 -->
            <div class="poll-container">
                <div class="poll-header">
                    <h3>Current News Poll</h3>
                    <a href="#" class="vote-now-button">Vote Now</a>
                </div>
                <p class="poll-description">Do you believe in gender roles? Vote and let's get your opinion.</p>
                <div class="poll-stats">
                    <span>Active Now</span>
                    <span>500 participants</span>
                </div>
                <div class="poll-chart">
                    <svg width="100%" height="100%" viewBox="0 0 100 60">
                        <rect x="10" y="10" width="60" height="10" fill="#00f2fe" opacity="0.9"/>
                        <rect x="10" y="30" width="40" height="10" fill="blue" opacity="0.7"/>
                        <text x="72" y="18" fill="#fff" font-size="4">Yes: 60%</text>
                        <text x="52" y="38" fill="#fff" font-size="4">No: 40%</text>
                        <text x="10" y="55" fill="#fff" font-size="3">Total Votes: 500 | Active Users: 127 | Trend: ↑12%</text>
                    </svg>
                </div>
            </div>

            <!-- Poll 2 -->
            <div class="poll-container">
                <div class="poll-header">
                    <h3>Market Sentiment Analysis</h3>
                    <a href="#" class="vote-now-button">Vote Now</a>
                </div>
                <p class="poll-description">Vote on stock market trends and see real-time insights from other users.</p>
                <div class="poll-stats">
                    <span>Active Now</span>
                    <span>1.2K participants</span>
                </div>
                <div class="poll-chart">
                    <svg width="100%" height="100%" viewBox="0 0 100 60">
                        <circle cx="30" cy="30" r="20" fill="none" stroke="#00f2fe" stroke-width="20" stroke-dasharray="88 100" transform="rotate(-90 30 30)"/>
                        <circle cx="30" cy="30" r="20" fill="none" stroke="#FFA726" stroke-width="20" stroke-dasharray="38 100" transform="rotate(-90 30 30)" stroke-dashoffset="-88"/>
                        <text x="60" y="25" fill="#fff" font-size="4">Bullish: 70%</text>
                        <text x="60" y="35" fill="#fff" font-size="4">Bearish: 30%</text>
                        <text x="10" y="55" fill="#fff" font-size="3">Volume: 1.2K | Confidence: High | Trend: ↑5%</text>
                    </svg>
                </div>
            </div>

            <!-- Poll 3 -->
            <div class="poll-container">
                <div class="poll-header">
                    <h3>Sports Match Predictions</h3>
                    <a href="#" class="vote-now-button">Vote Now</a>
                </div>
                <p class="poll-description">FC Barcelona or Real Madrid in tonight's El Clasico?</p>
                <div class="poll-stats">
                    <span>Active Now</span>
                    <span>750 participants</span>
                </div>
                <div class="poll-chart">
                    <svg width="100%" height="100%" viewBox="0 0 100 60">
                        <rect x="10" y="10" width="55" height="10" fill="#00f2fe" opacity="0.9"/>
                        <rect x="10" y="30" width="45" height="10" fill="red" opacity="0.7"/>
                        <text x="67" y="18" fill="#fff" font-size="4">FC Barcelona: 55%</text>
                        <text x="57" y="38" fill="#fff" font-size="4">Real Madrid: 45%</text>
                        <text x="10" y="55" fill="#fff" font-size="3">Total Votes: 750 | Match Time: 2hrs | Trend: ↑3%</text>
                    </svg>
                </div>
            </div>

            <!-- Poll 4 -->
            <div class="poll-container">
                <div class="poll-header">
                    <h3>Climate Change Initiatives</h3>
                    <a href="#" class="vote-now-button">Vote Now</a>
                </div>
                <p class="poll-description">Share your views on priorities for climate action and see the breakdown of opinions.</p>
                <div class="poll-stats">
                    <span>Active Now</span>
                    <span>1.5K participants</span>
                </div>
                <div class="poll-chart">
                    <svg width="100%" height="100%" viewBox="0 0 100 60">
                        <rect x="10" y="10" width="70" height="8" fill="#00f2fe"/>
                        <rect x="10" y="25" width="50" height="8" fill="#FFA726"/>
                        <rect x="10" y="40" width="30" height="8" fill="blue"/>
                        <text x="82" y="16" fill="#fff" font-size="3">Support: 70%</text>
                        <text x="62" y="31" fill="#fff" font-size="3">Neutral: 50%</text>
                        <text x="42" y="46" fill="#fff" font-size="3">Against: 30%</text>
                        <text x="10" y="55" fill="#fff" font-size="3">Total Votes: 1.5K | Region: Global | Impact: High</text>
                    </svg>
                </div>
            </div>

            <!-- Poll 5 -->
            <div class="poll-container">
                <div class="poll-header">
                    <h3>Workplace Preferences</h3>
                    <a href="#" class="vote-now-button">Vote Now</a>
                </div>
                <p class="poll-description">Remote, hybrid, or in-office work? See the preferences across participants.</p>
                <div class="poll-stats">
                    <span>Active Now</span>
                    <span>2.3K participants</span>
                </div>
                <div class="poll-chart">
                    <svg width="100%" height="100%" viewBox="0 0 100 60">
                        <rect x="10" y="10" width="70" height="10" fill="#00f2fe"/>
                        <rect x="10" y="25" width="50" height="10" fill="#FFA726"/>
                        <rect x="10" y="40" width="30" height="10" fill="blue"/>
                        <text x="82" y="18" fill="#fff" font-size="3">Remote: 70%</text>
                        <text x="62" y="33" fill="#fff" font-size="3">Hybrid: 50%</text>
                        <text x="42" y="48" fill="#fff" font-size="3">Office: 30%</text>
                        <text x="10" y="55" fill="#fff" font-size="3">Total Votes: 2.3K | Companies: 156 | Trend: ↑8%</text>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
