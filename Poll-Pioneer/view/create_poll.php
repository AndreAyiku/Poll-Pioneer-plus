<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
include('../db/config.php');

// Start session to handle user authentication and messages
session_start();

// Check if the user is logged in - redirect if not
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please log in to create a poll.";
    header("Location: ../view/login.php");
    exit();
}

// Backend: Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic form validation
    if (empty($_POST['poll_title']) || empty($_POST['poll_description']) || 
        empty($_POST['poll_start']) || empty($_POST['poll_end']) || 
        !isset($_POST['poll_options']) || count($_POST['poll_options']) < 2) {
        $_SESSION['error'] = "Please fill in all required fields and provide at least two options.";
        header("Location: create_poll.php");
        exit();
    }

    // Sanitize and prepare data
    $poll_title = trim($_POST['poll_title']);
    $poll_description = trim($_POST['poll_description']);
    $poll_type = $_POST['poll_type'] ?? 'multiple-choice';
    $poll_start = $_POST['poll_start'];
    $poll_end = $_POST['poll_end'];
    $privacy = $_POST['privacy'] ?? 'public';
    $allow_multiple_responses = isset($_POST['allow_multiple_responses']) ? 1 : 0;
    $anonymous_voting = isset($_POST['anonymous_voting']) ? 1 : 0;
    $result_display = $_POST['result_display'] ?? 'live';
    $randomize_order = isset($_POST['randomize_order']) ? 1 : 0;
    $poll_options = array_map('trim', $_POST['poll_options']);

    // Validate dates
    if (strtotime($poll_start) >= strtotime($poll_end)) {
        $_SESSION['error'] = "Poll end date must be later than the start date.";
        header("Location: create_poll.php");
        exit();
    }

    try {
        $conn->begin_transaction();

        // First, insert the basic poll information
        $query = "INSERT INTO PP_Polls (
            PollTitle, PollDescription, PollType, PollStart, PollEnd, 
            Privacy, AllowMultipleResponses, AnonymousVoting, 
            ResultDisplay, RandomizeOrder, CreatedBy
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare poll insertion: " . $conn->error);
        }

        $stmt->bind_param(
            "ssssssiisis",
            $poll_title, $poll_description, $poll_type, $poll_start, $poll_end,
            $privacy, $allow_multiple_responses, $anonymous_voting,
            $result_display, $randomize_order, $_SESSION['user_id']
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to create poll: " . $stmt->error);
        }

        $poll_id = $stmt->insert_id;

        // Handle image upload if present
        if (isset($_FILES['poll_image']) && $_FILES['poll_image']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['poll_image']['type'];
            
            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("Invalid image type. Only JPG, PNG, and GIF are allowed.");
            }

            $image_data = file_get_contents($_FILES['poll_image']['tmp_name']);
            
            $update_image = "UPDATE PP_Polls SET PollImage = ? WHERE PollID = ?";
            $stmt_image = $conn->prepare($update_image);
            if (!$stmt_image) {
                throw new Exception("Failed to prepare image update: " . $conn->error);
            }

            $null = NULL;
            $stmt_image->bind_param("bi", $null, $poll_id);
            $stmt_image->send_long_data(0, $image_data);
            
            if (!$stmt_image->execute()) {
                throw new Exception("Failed to upload image: " . $stmt_image->error);
            }
        }

        // Insert poll options
        $option_query = "INSERT INTO PP_PollOptions (PollID, OptionText) VALUES (?, ?)";
        $stmt_options = $conn->prepare($option_query);
        if (!$stmt_options) {
            throw new Exception("Failed to prepare options insertion: " . $conn->error);
        }

        foreach ($poll_options as $option) {
            if (trim($option) !== '') {
                $stmt_options->bind_param("is", $poll_id, $option);
                if (!$stmt_options->execute()) {
                    throw new Exception("Failed to insert option: " . $stmt_options->error);
                }
            }
        }

        $conn->commit();
        $_SESSION['success'] = "Poll created successfully!";
        header("Location: ../view/live_poll.php?id=" . $poll_id);
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error creating poll: " . $e->getMessage();
        header("Location: create_poll.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Pioneer - Create Poll</title>
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
            margin: 0;
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

        .content-container {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .form-section {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            padding: 2.5rem;
            border-radius: 20px;
            width: 100%;
            max-width: 800px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .form-section h1 {
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 2rem;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #fff;
        }

        .form-group input[type="text"],
        .form-group input[type="datetime-local"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(0, 0, 0, 0.2);
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input[type="file"] {
            width: 100%;
            padding: 0.8rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4facfe;
            box-shadow: 0 0 0 2px rgba(79, 172, 254, 0.2);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: #4facfe;
        }

        .options-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .option-input {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .option-input input {
            flex: 1;
        }

        .remove-option {
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-option {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            color: #fff;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .create-poll {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            color: #fff;
            border: none;
            padding: 1rem 2rem;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            font-size: 1.1rem;
            margin-top: 2rem;
            transition: all 0.3s ease;
        }

        .add-option:hover,
        .create-poll:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }

        .remove-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 75, 43, 0.4);
        }

        .error-message {
            background: rgba(255, 99, 71, 0.2);
            border: 1px solid #ff6347;
            color: #fff;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .success-message {
            background: rgba(50, 205, 50, 0.2);
            border: 1px solid #32cd32;
            color: #fff;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        .user-menu {
        position: relative;
    }

    .user-icon-container {
        position: relative;
    }

    .user-icon {
        font-size: 2rem;
        color: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .user-icon:hover {
        transform: scale(1.1);
        color: #4facfe;
    }

    .user-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        min-width: 200px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        padding: 0.5rem 0;
        margin-top: 0.5rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .user-dropdown a {
        display: block;
        color: #fff;
        text-decoration: none;
        padding: 0.7rem 1.2rem;
        transition: all 0.3s ease;
    }

    .user-dropdown a:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #4facfe;
    }

    .user-dropdown.show {
        display: block;
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
    <div class="user-menu">
        <div class="user-icon-container">
            <i class='bx bx-user-circle user-icon' onclick="toggleUserDropdown()"></i>
            <div id="user-dropdown" class="user-dropdown">
                <?php if(isset($_SESSION['role'])): ?>
                    <?php if($_SESSION['role'] == 1): ?>
                        <a href="../view/admin/admin_dashboard.php">Admin Dashboard</a>
                    <?php else: ?>
                        <a href="../view/admin/User_dashboard.php">User Dashboard</a>
                    <?php endif; ?>
                   
                    <a href="../actions/logout.php">Logout</a>
                <?php else: ?>
                    <a href="../view/login.php">Login</a>
                    <a href="../view/sign-up.php">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>


        <div class="content-container">
            <section class="form-section">
                <h1>Create New Poll</h1>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <form action="create_poll.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="poll_title">Poll Title</label>
                        <input type="text" id="poll_title" name="poll_title" required>
                    </div>

                    <div class="form-group">
                        <label for="poll_description">Poll Description</label>
                        <textarea id="poll_description" name="poll_description" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="poll_type">Poll Type</label>
                        <select id="poll_type" name="poll_type" required>
                            <option value="multiple-choice">Multiple Choice</option>
                            <option value="checkboxes">Checkboxes</option>
                            <option value="star-rating">Star Rating</option>
                            <option value="likert-scale">Likert Scale</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="poll_start">Start Date</label>
                        <input type="datetime-local" id="poll_start" name="poll_start" required>
                    </div>

                    <div class="form-group">
                        <label for="poll_end">End Date</label>
                        <input type="datetime-local" id="poll_end" name="poll_end" required>
                    </div>

                    <div class="form-group">
                        <label for="poll_image">Poll Image</label>
                        <input type="file" id="poll_image" name="poll_image" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label for="privacy">Privacy Setting</label>
                        <select id="privacy" name="privacy" required>
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="allow_multiple_responses" name="allow_multiple_responses">
                            <label for="allow_multiple_responses">Allow Multiple Responses</label>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="anonymous_voting" name="anonymous_voting">
                            <label for="anonymous_voting">Anonymous Voting</label>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="randomize_order" name="randomize_order">
                            <label for="randomize_order">Randomize Option Order</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="result_display">Result Display</label>
                        <select id="result_display" name="result_display" required>
                            <option value="live">Show Results Live</option>
                            <option value="after-voting">Show After Voting</option>
                            <option value="at-end">Show at Poll End</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Poll Options</label>
                        <div id="options-container" class="options-container">
                            <div class="option-input">
                                <input type="text" name="poll_options[]" required placeholder="Option 1">
                            </div>
                            <div class="option-input">
                                <input type="text" name="poll_options[]" required placeholder="Option 2">
                            </div>
                        </div>
                        <button type="button" class="add-option" onclick="addOption()">Add Another Option</button>
                    </div>

                    <button type="submit" class="create-poll">Create Poll</button>
                </form>
            </section>
        </div>
    </div>

    <script>
        function addOption() {
            const container = document.getElementById('options-container');
            const optionCount = container.children.length + 1;
            
            const optionDiv = document.createElement('div');
            optionDiv.className = 'option-input';
            
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'poll_options[]';
            input.required = true;
            input.placeholder = `Option ${optionCount}`;
            
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'remove-option';
            removeButton.textContent = 'Remove';
            removeButton.onclick = function() {
                container.removeChild(optionDiv);
            };
            
            optionDiv.appendChild(input);
            optionDiv.appendChild(removeButton);
            container.appendChild(optionDiv);
        }

        // Set minimum datetime for start and end dates
        window.addEventListener('load', function() {
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            
            const startInput = document.getElementById('poll_start');
            const endInput = document.getElementById('poll_end');
            
            startInput.min = now.toISOString().slice(0, 16);
            endInput.min = now.toISOString().slice(0, 16);
            
            startInput.addEventListener('change', function() {
                endInput.min = this.value;
            });
        });
        function toggleUserDropdown() {
        const dropdown = document.getElementById('user-dropdown');
        dropdown.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('user-dropdown');
        const userIcon = document.querySelector('.user-icon');
        
        if (dropdown.classList.contains('show') && 
            !dropdown.contains(e.target) && 
            e.target !== userIcon) {
            dropdown.classList.remove('show');
        }
    });
    </script>
</body>
</html>