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
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            color: #fff;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .form-group input, 
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

        .form-group input:focus, 
        .form-group select:focus, 
        .form-group textarea:focus {
            outline: none;
            border-color: #4facfe;
            box-shadow: 0 0 0 2px rgba(79, 172, 254, 0.2);
        }

        .option {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .option input {
            flex: 1;
            padding: 0.8rem;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(0, 0, 0, 0.2);
            color: #fff;
            transition: all 0.3s ease;
        }

        .add-option, 
        .remove-option, 
        .create-poll, 
        .publish-poll {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .add-option {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            color: #fff;
            width: 100%;
            margin-top: 1rem;
        }

        .remove-option {
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            color: #fff;
        }

        .create-poll, 
        .publish-poll {
            width: 100%;
            margin-top: 1.5rem;
            font-size: 1.1rem;
            display: block;
        }

        .create-poll {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            color: #fff;
        }

        .publish-poll {
            background: linear-gradient(45deg, #4facfe, #00f2fe);
            color: #fff;
        }

        .add-option:hover,
        .create-poll:hover,
        .publish-poll:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }

        .remove-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 75, 43, 0.4);
        }

        .poll-link {
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            font-size: 0.9rem;
            color: #fff;
        }

        input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            accent-color: #4facfe;
        }

        input[type="datetime-local"] {
            margin-bottom: 1rem;
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }
    </style>
</head>
<body>
    <div class="background-container">
        <header>
            <div class="logo">
                <a href="index.html">Poll Pioneer</a>
            </div>
            <nav>
                <ul>
                    <li><a href="../view/home.php">Home</a></li>
                    <li><a href="../view/">Live Polls</a></li>
                    <li><a href="../view/create_poll.php">Create Poll</a></li>
                    <li><a href="results.html">Results</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <a href="../view/login.php">Login</a>
                <a href="../view/sign-up.php">Sign Up</a>
            </div>
        </header>

        <div class="content-container">
            <section class="form-section">
                <h1>Create Poll</h1>

                <!-- Basic poll setup -->
                <div class="form-group">
                    <label for="pollTitle">Poll Title:</label>
                    <input type="text" id="pollTitle" placeholder="Enter poll title">
                </div>
                <div class="form-group">
                    <label for="pollDescription">Poll Description:</label>
                    <textarea id="pollDescription" rows="3" placeholder="Enter poll description"></textarea>
                </div>
                <div class="form-group">
                    <label for="pollType">Poll Type:</label>
                    <select id="pollType">
                        <option value="multiple-choice">Multiple Choice</option>
                        <option value="checkboxes">Checkboxes</option>
                        <option value="star-rating">Star Rating</option>
                        <option value="likert-scale">Likert Scale</option>
                    </select>
                </div>
                
                <!-- Poll duration setup -->
                <div class="form-group">
                    <label for="pollDuration">Poll Duration:</label>
                    <input type="datetime-local" id="pollStart" placeholder="Start date">
                    <input type="datetime-local" id="pollEnd" placeholder="End date">
                </div>

                <!-- Privacy options -->
                <div class="form-group">
                    <label for="pollPrivacy">Privacy:</label>
                    <select id="pollPrivacy" onchange="togglePrivacyOptions()">
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                    </select>
                </div>
                <div class="poll-link" id="pollLink" style="display: none;">
                    Shareable link for private poll: <span id="generatedLink">N/A</span>
                </div>

                <!-- Voting restrictions -->
                <div class="form-group">
                    <label>Voting Restrictions:</label>
                    <select id="votingRestrictions">
                        <option value="none">None</option>
                        <option value="one-vote-per-user">One vote per user</option>
                        <option value="one-vote-per-ip">One vote per IP</option>
                        <option value="login-required">Login required</option>
                    </select>
                </div>

                <!-- Allow multiple responses -->
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="allowMultipleResponses">
                        Allow Multiple Responses
                    </label>
                </div>

                <!-- Anonymous voting -->
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="anonymousVoting">
                        Anonymous Voting
                    </label>
                </div>

                <!-- Result display options -->
                <div class="form-group">
                    <label>Result Display:</label>
                    <select id="resultDisplay">
                        <option value="live">Show live results</option>
                        <option value="after-voting">Show results after voting</option>
                        <option value="at-end">Show results at poll end</option>
                    </select>
                </div>

                <!-- Randomize option order -->
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="randomizeOrder">
                        Randomize Option Order
                    </label>
                </div>

                <!-- Options input -->
                <div class="options">
                    <div class="option">
                        <input type="text" class="option-input" placeholder="Enter option">
                        <button class="remove-option" onclick="removeOption(this)">Remove</button>
                    </div>
                </div>
                <button class="add-option" onclick="addOption()">Add Option</button>
                
                <button class="create-poll" onclick="createPoll()">Create Poll</button>
                <button class="publish-poll" onclick="publishPoll()">Publish Poll</button>
            </section>
        </div>
    </div>

    <script>
        function addOption() {
            const optionContainer = document.createElement('div');
            optionContainer.className = 'option';

            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'option-input';
            input.placeholder = 'Enter option';

            const removeButton = document.createElement('button');
            removeButton.className = 'remove-option';
            removeButton.innerText = 'Remove';
            removeButton.onclick = () => removeOption(removeButton);

            optionContainer.appendChild(input);
            optionContainer.appendChild(removeButton);

            document.querySelector('.options').appendChild(optionContainer);
            }

            function removeOption(button) {
            button.parentElement.remove();
            }

            function togglePrivacyOptions() {
            const privacy = document.getElementById('pollPrivacy').value;
            const pollLink = document.getElementById('pollLink');
            if (privacy === 'private') {
                const link = 'https://pollpioneer.com/poll/' + Math.random().toString(36).substr(2, 9);
                document.getElementById('generatedLink').textContent = link;
                pollLink.style.display = 'block';
            } else {
                pollLink.style.display = 'none';
            }
            }

            function createPoll() {
            const title = document.getElementById('pollTitle').value;
            const description = document.getElementById('pollDescription').value;
            const type = document.getElementById('pollType').value;
            const privacy = document.getElementById('pollPrivacy').value;
            const options = Array.from(document.querySelectorAll('.option-input')).map(input => input.value);
            const pollStart = document.getElementById('pollStart').value;
            const pollEnd = document.getElementById('pollEnd').value;
            const votingRestrictions = document.getElementById('votingRestrictions').value;
            const allowMultipleResponses = document.getElementById('allowMultipleResponses').checked;
            const anonymousVoting = document.getElementById('anonymousVoting').checked;
            const resultDisplay = document.getElementById('resultDisplay').value;
            const randomizeOrder = document.getElementById('randomizeOrder').checked;

            if (!title || !description || options.some(opt => !opt)) {
                alert('Please fill out all fields and options.');
                return;
            }

            const poll = {
                title,
                description,
                type,
                privacy,
                options,
                duration: { start: pollStart, end: pollEnd },
                votingRestrictions,
                allowMultipleResponses,
                anonymousVoting,
                resultDisplay,
                randomizeOrder
            };

            if (randomizeOrder) {
                poll.options = poll.options.sort(() => Math.random() - 0.5);
            }

            console.log('Poll created:', poll);
            alert('Poll created successfully! Check console for details.');

            resetForm();
            }

            function publishPoll() {
            alert('Poll published successfully!');
            }

            function resetForm() {
            document.getElementById('pollTitle').value = '';
            document.getElementById('pollDescription').value = '';
            document.getElementById('pollType').value = 'multiple-choice';
            document.getElementById('pollPrivacy').value = 'public';
            document.getElementById('pollStart').value = '';
            document.getElementById('pollEnd').value = '';
            document.getElementById('votingRestrictions').value = 'none';
            document.getElementById('allowMultipleResponses').checked = false;
            document.getElementById('anonymousVoting').checked = false;
            document.getElementById('resultDisplay').value = 'live';
            document.getElementById('randomizeOrder').checked = false;
            document.querySelector('.options').innerHTML = `
                <div class="option">
                    <input type="text" class="option-input" placeholder="Enter option">
                    <button class="remove-option" onclick="removeOption(this)">Remove</button>
                </div>
            `;
            document.getElementById('pollLink').style.display = 'none';
            }
</script>

</body>
</html>