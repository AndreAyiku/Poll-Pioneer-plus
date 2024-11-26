<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Poll Pioneer</title>
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
        }header {
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
            <!-- Contact Page Content -->
            <section class="hero-section">
                <div class="hero-text">
                    <h1>Get in Touch</h1>
                    <p>Need help or have questions? Our dedicated team is here to assist you. Reach out to us for customer support or sales inquiries.</p>
                </div>
                <div class="hero-image">
                    <img src="../assests/images/CustomerSupport.png" alt="Customer Support">
                </div>
            </section>

            <section class="contact-section">
                <div class="contact-card">
                    <i class='bx bx-phone'></i>
                    <h3>Talk to Sales</h3>
                    <p>Looking for our services? Contact our sales team.</p>
                    <a href="tel:+233592209149">Call +233 592209149</a>
                </div>

                <div class="contact-card">
                    <i class='bx bx-support'></i>
                    <h3>Contact Support</h3>
                    <p>Facing issues? Our support team is here for you.</p>
                    <a href="mailto:pollpioneer111@gmail.com">Email Support</a>
                </div>
            </section>

            <section class="social-links">
                <a href="https://www.instagram.com/poll_pioneer" target="_blank"><i class='bx bxl-instagram'></i></a>
                <a href="https://twitter.com/PollPioneer1" target="_blank"><i class='bx bxl-twitter'></i></a>
                <a href="https://snapchat.com/t/6avFJgBG" target="_blank"><i class='bx bxl-snapchat'></i></a>
            </section>
        </div>
    </div>
</body>
</html>