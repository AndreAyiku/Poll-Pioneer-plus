<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Page</title>
    <link rel = "icon" type= "image/x-icon" href="../assests/images/voting-box.ico">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            margin: 0 auto;
            padding: 0;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('../assests/images/voter.jpg') no-repeat;
            background-size: cover;
        }
        .wrapper {
            position: relative;
            width: 320px;
            background: grey;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .wrapper h1 {
            text-align: center;
        }
        .input-box {
            position: relative;
            margin-bottom: 10px;
        }
        .input-box input {
            width: 100%;
            padding: 10px 35px 10px 10px;
            border-radius: 90px;
            border: 1px solid white;
            box-sizing: border-box;
        }
        .input-box i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }
        .wrapper .button {
            width: 100%;
            border-radius: 90px;
            font-size: 19px;
            padding: 10px;
            border: none;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        .remember {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .remember input {
            margin-right: 5px;
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
        }
        .error {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Login</h1>
        <form action="../actions/login_user.php" method="POST">
            <div class="input-box">
                <input type="email" id="email" placeholder="Username" name="email" required>
                <i class='bx bx-user-circle'></i>
                <div id="emailError" class="error"></div>
            </div>

            <div class="input-box">
                <input type="password" id="password" placeholder="Password" name="password" required>
                <i class='bx bx-lock'></i>
                <div id="passwordError" class="error"></div>
            </div>

            <button type="submit" class="button">Login</button>

            <div class="remember">
                <input type="checkbox" id="rememberMe" name="rememberMe">
                <label for="rememberMe">Remember me</label>
            </div>

            <div class="signup-link">
                Don't have an account? <a href="../view/sign-up.php">Sign Up</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password');
            
            let isValid = true;
            
            // Email validation
            if (!validateEmail(email)) {
                document.getElementById('emailError').textContent = 'Please enter a valid email address.';
                isValid = false;
            } else {
                document.getElementById('emailError').textContent = '';
            }
            
            // Password validation
            if (!CheckPassword(password)) {
                document.getElementById('passwordError').textContent = 'Password must be 6-20 characters long, contain at least one digit, one uppercase and one lowercase letter.';
                isValid = false;
            } else {
                document.getElementById('passwordError').textContent = '';
            }
            
            if (isValid) {
                alert('Login successful!'); // Replace with actual login logic
            }
        });

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        function CheckPassword(inputtxt) { 
            var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/; 
            if(inputtxt.value.match(passw)) { 
                return true;
            } else { 
                return false;
            }
        }
    </script>
</body>
</html>
        
   