<?php
session_start();
require_once '../db/config.php';

header('Content-Type: application/json');

// Ensure the database connection is valid
if (!isset($connection) || !$connection) {
    die(json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if all fields are filled
    if (empty($fname) || empty($lname) || empty($username) || empty($email) || empty($password)) {
        echo json_encode([
            "success" => false,
            "message" => "All fields are required."
        ]);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid email format."
        ]);
        exit;
    }

    try {
        // Check if the email or username already exists
        $stmt = $connection->prepare("SELECT * FROM pp_users WHERE email = ? OR Username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode([
                "success" => false,
                "message" => "Email or Username is already registered."
            ]);
            exit;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 2; // Regular user role

        // Insert user into the database
        $stmt = $connection->prepare("INSERT INTO pp_users (fname, Username, lname, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $fname, $username, $lname, $email, $hashedPassword);

        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Registration successful! Please log in."
            ]);
            header("Location: ../view/login.php");
            exit;
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Error executing query: " . $stmt->error
            ]);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "An error occurred: " . $e->getMessage()
        ]);
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
}

$connection->close();
?>
