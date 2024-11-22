<?php

// Include database connection (update with your connection file or details)
include '../db/config.php';

$errors = []; // Array to hold all error messages when registration fails

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get and sanitize inputs to prevent sql injections
    $firstName = mysqli_real_escape_string($conn, $_POST['fname']);
    $lastName = mysqli_real_escape_string($conn, $_POST['lname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);

    // Server-side validation
    if (empty($firstName)) $errors[] = "First name is required.";   // first name validation

    if (empty($lastName)) $errors[] = "Last name is required.";    // last name validation

    if (empty($username)) $errors[] = "Create a Username";    // username validation

    // Email validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    } else {
        // Check for duplicate email
        $emailCheckQuery = "SELECT * FROM PP_Users WHERE email = ?";

        $stmt = $conn->prepare($emailCheckQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = "Email already registered.";
        $stmt->close();
    }

    // Password validation
   
    // Proceed if no errors
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into the database using prepared statements
        $insertQuery = "INSERT INTO PP_Users (fname, lname, Username, email, passwordHash) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        if ($stmt) 
        {$stmt->bind_param("sssss", $firstName, $lastName, $username, $email, $hashedPassword);
        if ($stmt->execute()) {
            header("Location: ../view/login.php");
        } else {
            echo "Error: " . $stmt->error;
        }}
        $stmt->close();
    } else {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
    $conn->close();
}

