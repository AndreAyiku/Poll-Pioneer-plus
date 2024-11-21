<?php
// Initialize error messages
$emailError = $passwordError = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $isValid = true;

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Please enter a valid email address.";
        $isValid = false;
    }

    // Password validation
    $passwordPattern = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/";
    if (!preg_match($passwordPattern, $password)) {
        $passwordError = "Password must be 6-20 characters long, contain at least one digit, one uppercase, and one lowercase letter.";
        $isValid = false;
    }

    // Redirect back to the form with error messages if validation fails
    if (!$isValid) {
        $queryString = http_build_query([
            'emailError' => $emailError,
            'passwordError' => $passwordError,
        ]);
        header("Location: teamLogin.html?$queryString");
        exit();
    }

    // If validation passes, proceed with login (replace this with actual login logic)
    echo "Login successful!";
}
?>
