<?php

$salesPhone = "+1 234 567 890";
$supportEmail = "support@pollpioneer.com";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Poll Pioneer</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        header {
            background-color: #1a1a1a;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header .logo a {
            color: #fff;
            text-decoration: none;
            font-size: 1.8rem;
            font-weight: bold;
        }
        header nav ul {
            list-style: none;
            display: flex;
            gap: 1rem;
            margin: 0;
            padding: 0;
        }
        header nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
        }
        .contact-header {
            background: linear-gradient(to right, #4d4d4d, #1a1a1a);
            color: #fff;
            padding: 3rem 2rem;
            text-align: center;
        }
        .contact-header h1 {
            font-size: 2.5rem;
            margin: 0;
        }
        .contact-header p {
            font-size: 1.2rem;
            margin-top: 0.5rem;
        }
        .contact-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 3rem 2rem;
        }
        .contact-card {
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            max-width: 500px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .contact-card i {
            font-size: 2rem;
            color: #1a1a1a;
            margin-bottom: 1rem;
        }
        .contact-card h3 {
            margin: 0.5rem 0;
            font-size: 1.2rem;
            color: #333;
        }
        .contact-card p {
            margin: 0.5rem 0;
            font-size: 1rem;
            color: #666;
        }
        .contact-card a {
            color: #1a1a1a;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            border: 1px solid #1a1a1a;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .contact-card a:hover {
            background-color: #1a1a1a;
            color: #fff;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="/index.php">Poll Pioneer</a>
        </div>
        <nav>
            <ul>
                <li><a href="/view/home.php">Home</a></li>
                <li><a href="/view/live_poll.php">Live Polls</a></li>
                <li><a href="/view/create_poll.php">Create Poll</a></li>
                <li><a href="/view/results.php">Results</a></li>
                <li><a href="#">About</a></li>
                <li><a href="/view/contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <section class="contact-header">
        <h1>Get in Touch</h1>
        <p>Have a question or need assistance? We're here to help.</p>
    </section>

    <section class="contact-container">
        <!-- Talk to Sales Card -->
        <div class="contact-card">
            <i class='bx bx-phone'></i>
            <h3>Talk to Sales</h3>
            <p>Interested in our services? Reach out to our sales team.</p>
            <a href="tel:<?= $salesPhone; ?>"><?= $salesPhone; ?></a>
        </div>

        <!-- Contact Support Card -->
        <div class="contact-card">
            <i class='bx bx-support'></i>
            <h3>Contact Support</h3>
            <p>Need help? Our support team is ready to assist you.</p>
            <a href="mailto:<?= $supportEmail; ?>"><?= $supportEmail; ?></a>
        </div>
    </section>
</body>
</html>
