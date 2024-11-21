<?php

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Initialize error messages
$errors = [];

// Check if form data was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //validate the username
    $username = sanitizeInput($_POST["username"]);
    if (strlen($username) < 3) {
        $errors['username'] = "Username must be at least 3 characters long.";
    }

    //validate the email
    $email = sanitizeInput($_POST["email"]);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }

    //validate the password
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $passwordPattern = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/";

    if (!preg_match($passwordPattern, $password)) {
        $errors['password'] = "Password must be 6-20 characters long, contain at least one digit, one uppercase and one lowercase letter.";
    }

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = "Passwords do not match.";
    }

    // If there are no errors, process the registration
    if (empty($errors)) {
        // Here, you can add code to save the user data to the database
        echo "Registration successful!";
    } else {
        // Display errors as JSON to handle them in JavaScript if desired
        echo json_encode($errors);
    }
}
?>
