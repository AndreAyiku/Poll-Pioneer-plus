<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Pioneer - Results</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* CSS from home.html */
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

        .content-container {
            padding: 2rem;
            overflow-y: auto;
        }

        .featured-poll {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 2rem;
        }

        .poll-badge {
            background: #FFD700;
            color: #000;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            display: inline-block;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .poll-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .poll-description {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 1rem;
        }

        .poll-meta, .poll-stats {
            display: flex;
            gap: 2rem;
            color: rgba(79, 172, 254, 0.8);
            font-weight: 500;
        }

        .result-bars {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1rem;
        }

        .result-item {
            width: 100%;
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .result-bar-bg {
            background: rgba(255, 255, 255, 0.1);
            height: 24px;
            border-radius: 12px;
            overflow: hidden;
        }

        .result-bar-fill {
            height: 100%;
            transition: width 1s ease-in-out;
        }

        .results-grid {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            padding: 2rem;
        }

        .result-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 1.5rem;
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
        }

        .result-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }

        .result-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
        }

        .details-button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }

        .details-button:hover {
            background: rgba(255, 255, 255, 0.2);
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
            color: #666;
        }
        
        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 1.2rem;
        }
        .filter-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            justify-content: center;
        }
        .filter-button {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 0.5rem 1rem;
            border: 2px solid transparent;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .filter-button:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .filter-button.active {
        background-color: #4CAF50;
         border: 2px solid #4CAF50;
        }
        .result-bars {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .result-item {
            width: 100%;
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .result-bar-bg {
            background: rgba(255, 255, 255, 0.1);
            height: 24px;
            border-radius: 12px;
            overflow: hidden;
        }

        .result-bar-fill {
            height: 100%;
            transition: width 1s ease-in-out;
        }

        .results-grid {
            background: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            padding: 2rem;
        }

        .grid-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .view-options {
            display: flex;
            gap: 0.5rem;
        }

        .view-button {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            padding: 0.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .view-button.active {
            background: white;
            color: black;
        }

        .results-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .result-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 1.5rem;
            transition: transform 0.3s ease;
        }

        .result-card:hover {
            transform: translateY(-5px);
        }

        .result-card-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .category {
            color: #FFD700;
            font-size: 0.9rem;
        }

        .status {
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        .status.completed {
            background: rgba(76, 175, 80, 0.2);
            color: #4CAF50;
        }

        .status.live {
            background: rgba(33, 150, 243, 0.2);
            color: #2196F3;
        }

        .result-chart {
            margin: 1.5rem 0;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
        }

        .winner-section {
            text-align: center;
            margin-bottom: 1rem;
        }

        .winner-badge {
            background: #FFD700;
            color: black;
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .winner-name {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.3rem;
        }

        .winner-votes {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        .runners-up {
            margin-top: 1rem;
        }

        .runner-up {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.8rem;
        }

        .vote-bar {
            flex: 1;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        .vote-fill {
            height: 100%;
            background: #2196F3;
            border-radius: 4px;
        }

        .result-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
        }

        .stats {
            display: flex;
            gap: 1rem;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        .details-button, .vote-button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .details-button {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .vote-button {
            background: #4CAF50;
            color: white;
        }

        .details-button:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .vote-button:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="background-container">
        <header>
            <div class="logo"><a href="index.html">Poll Pioneer</a></div>
            <nav>
                <ul>
                    <li><a href="home.html">Home</a></li>
                    <li><a href="live_polls.html">Live Polls</a></li>
                    <li><a href="Create_poll.html">Create Poll</a></li>
                    <li><a href="results.html">Results</a></li>
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
                <input type="text" class="search-input" placeholder="Search polls by title, category, or description...">
            </div>
        </div>

        <div class="filter-container">
            <button class="filter-button active">All Results</button>
            <button class="filter-button">Politics</button>
            <button class="filter-button">Entertainment</button>
            <button class="filter-button">Technology</button>
            <button class="filter-button">Sports</button>
        </div>

        <div class="content-container">
            <div class="featured-poll">
                <div class="poll-badge">Featured Poll</div>
                <h2 class="poll-title">2024 Presidential Election Forecast</h2>
                <p class="poll-description">Latest polling data and predictions</p>
                <div class="poll-meta">
                    <span><i class='bx bx-poll'></i> 1.5M votes</span>
                    <span><i class='bx bx-time'></i> Updated 2h ago</span>
                    <span><i class='bx bx-bar-chart'></i> 75% confidence</span>
                </div>
                <div class="result-bars">
                    <div class="result-item">
                        <div class="result-header">
                            <span class="candidate">Gilbert Tetteh</span>
                            <span class="percentage">88%</span>
                        </div>
                        <div class="result-bar-bg">
                            <div class="result-bar-fill" style="width: 88%; background-color: #4CAF50;"></div>
                        </div>
                    </div>
                    <div class="result-item">
                        <div class="result-header">
                            <span class="candidate">Andre Ayiku</span>
                            <span class="percentage">10%</span>
                        </div>
                        <div class="result-bar-bg">
                            <div class="result-bar-fill" style="width: 10%; background-color: #2196F3;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Results Grid -->
            <div class="results-grid">
                <div class="grid-header">
                    <h2>Recent Results</h2>
                    <div class="view-options">
                        <button class="view-button active"><i class='bx bx-grid-alt'></i></button>
                        <button class="view-button"><i class='bx bx-list-ul'></i></button>
                    </div>
                </div>
                
                <div class="results-wrapper">
                    <div class="result-card">
                        <div class="result-card-header">
                            <span class="category">Technology</span>
                            <span class="status completed">Completed</span>
                        </div>
                        <h3>Best Smartphone 2024</h3>
                        <div class="result-chart">
                            <div class="winner-section">
                                <div class="winner-badge">Winner</div>
                                <div class="winner-name">iPhone 15 Pro</div>
                                <div class="winner-votes">42% (380K votes)</div>
                            </div>
                            <div class="runners-up">
                                <div class="runner-up">
                                    <span class="name">Samsung S24 Ultra</span>
                                    <div class="vote-bar">
                                        <div class="vote-fill" style="width: 35%"></div>
                                    </div>
                                    <span class="percentage">35%</span>
                                </div>
                                <div class="runner-up">
                                    <span class="name">Google Pixel 8</span>
                                    <div class="vote-bar">
                                        <div class="vote-fill" style="width: 23%"></div>
                                    </div>
                                    <span class="percentage">23%</span>
                                </div>
                            </div>
                        </div>
                        <div class="result-footer">
                            <div class="stats">
                                <span><i class='bx bx-user'></i> 850K participants</span>
                                <span><i class='bx bx-calendar'></i> Ended Oct 15</span>
                            </div>
                            <button class="details-button">Full Results</button>
                        </div>
                    </div>

                    <div class="result-card">
                        <div class="result-card-header">
                            <span class="category">Entertainment</span>
                            <span class="status live">Live</span>
                        </div>
                        <h3>Oscar Predictions 2024</h3>
                        <div class="result-chart">
                            <div class="category-results">
                                <h4>Best Picture Leader</h4>
                                <div class="leader-bar">
                                    <div class="leader-fill" style="width: 45%"></div>
                                    <span class="leader-name">Oppenheimer</span>
                                    <span class="leader-percentage">45%</span>
                                </div>
                            </div>
                            <div class="vote-progress">
                                <div class="progress-text">
                                    <span>320K votes</span>
                                    <span>5 days left</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 65%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="result-footer">
                            <button class="vote-button">Vote Now</button>
                            <button class="details-button">View Details</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
