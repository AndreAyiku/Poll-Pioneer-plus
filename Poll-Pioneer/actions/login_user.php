<?php
session_start();


include '../db/config.php'; 

$errors = []; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Server-side validation
    if (empty($email) || empty($password)) {
        echo "Please fill in all fields.";
        exit;
    }

    // Check if the email exists in the database
    $query = "SELECT  Username, PasswordHash, Role FROM PP_Users WHERE email = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();

        // Get result
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['PasswordHash'])) {
                // Store user information in session
                $_SESSION['user_id'] = $user['UserID'];
                $_SESSION['username'] = $user['Username'];
                $_SESSION['role'] = $user['Role'];

                // Redirect based on role
                if ($user['Role'] === 1) {
                    header("Location: ../view/admin/dashboard1.php");
                } else {
                    header("Location: ../view/admin/dashboard.php");
                }
                exit();
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "Email does not exist.";
        }

        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
