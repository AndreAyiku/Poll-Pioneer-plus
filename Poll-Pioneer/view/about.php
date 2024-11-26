<!-- about.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Poll Pioneer</title>
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

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .hero-section {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 3rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .hero-text {
            flex: 1;
        }

        .hero-text h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-text p {
            font-size: 1.2rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.8);
        }

        .hero-image {
            flex: 1;
        }

        .hero-image img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .contact-section {
            display: flex;
            justify-content: space-between;
            gap: 2rem;
            margin-top: 2rem;
        }

        .contact-card {
            flex: 1;
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .contact-card:hover {
            transform: translateY(-5px);
            border-color: rgba(79, 172, 254, 0.3);
            box-shadow: 0 8px 32px rgba(79, 172, 254, 0.2);
        }

        .contact-card i {
            font-size: 2.5rem;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }

        .contact-card h3 {
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 1rem;
        }

        .contact-card p {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 1.5rem;
        }

        .contact-card a {
            display: inline-block;
            padding: 0.7rem 1.5rem;
            color: #1a1a2e;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .contact-card a:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 2rem;
        }

        .social-links a {
            color: rgba(255, 255, 255, 0.8);
            font-size: 2rem;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            color: #4facfe;
            transform: translateY(-2px);
        }

        /* About Page Specific Styles */
        .about-section {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .about-section h2 {
            font-size: 2rem;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }

        .about-section p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .about-section ul, .about-section ol {
            color: rgba(255, 255, 255, 0.8);
            margin-left: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .about-section li {
            margin-bottom: 0.5rem;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .feature-card {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: rgba(79, 172, 254, 0.3);
            box-shadow: 0 8px 32px rgba(79, 172, 254, 0.2);
        }

        .feature-card i {
            font-size: 2rem;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            color: #fff;
            margin-bottom: 0.5rem;
        }

            .feature-card p {
                color: rgba(255, 255, 255, 0.8);
                font-size: 0.9rem;
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
    <div class="auth-buttons">
        <a href="../view/login.php">Login</a>
        <a href="../view/sign-up.php">Sign Up</a>
    </div>
</header>
        <div class="container">
            <section class="hero-section">
                <div class="hero-text">
                    <h1>About Poll Pioneer</h1>
                    <p>Your ultimate platform for creating, participating in, and exploring polls and surveys. Join our community where every voice matters and every vote counts.</p>
                </div>
                <div class="hero-image">
                    <img src="../assests/images/about-hero.jpg" alt="Poll Pioneer Platform">
                </div>
            </section>

            <section class="about-section">
                <h2>What is Poll Pioneer?</h2>
                <p>Poll Pioneer is a web-based platform designed to connect people and ideas. By providing an easy-to-use interface for polls and surveys, we empower users to create meaningful discussions and gather valuable insights.</p>
                
                <div class="feature-grid">
                    <div class="feature-card">
                        <i class='bx bx-poll'></i>
                        <h3>Live Polls</h3>
                        <p>Participate in real-time polls on trending topics and see results as they come in.</p>
                    </div>
                    <div class="feature-card">
                        <i class='bx bx-customize'></i>
                        <h3>Custom Surveys</h3>
                        <p>Create personalized polls and share them with your audience or community.</p>
                    </div>
                    <div class="feature-card">
                        <i class='bx bx-bar-chart-alt-2'></i>
                        <h3>Analytics</h3>
                        <p>Access detailed insights and analytics to understand your audience better.</p>
                    </div>
                </div>
            </section>

            <section class="about-section">
                <h2>How Does it Work?</h2>
                <ol>
                    <li>Sign up for a free account to access all features</li>
                    <li>Create your first poll using our intuitive interface</li>
                    <li>Share your poll with your audience</li>
                    <li>Monitor responses and analyze results in real-time</li>
                </ol>
            </section>

            <section class="about-section">
                <h2>Join Our Community</h2>
                <p>Be part of a growing community where every opinion matters. Start creating and participating in polls today!</p>
                <div class="contact-card" style="text-align: center; max-width: 400px; margin: 2rem auto;">
                    <i class='bx bx-user-plus'></i>
                    <h3>Get Started Now</h3>
                    <p>Create your account and begin your polling journey</p>
                    <a href="sign-up.php">Sign Up Free</a>
                </div>
            </section>
        </div>
    </div>
</body>
</html>