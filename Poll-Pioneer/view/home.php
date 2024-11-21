<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Pioneer - Sections</title>
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
        
        .search-container {
            padding: 2rem 2rem 0 2rem;
        }
        
        .search-wrapper {
            position: relative;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .search-input {
            width: 100%;
            padding: 1.2rem 1.2rem 1.2rem 3.5rem;
            font-size: 1.1rem;
            border: none;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: #fff;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .search-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(79, 172, 254, 0.5);
            box-shadow: 0 0 20px rgba(79, 172, 254, 0.2);
        }
        
        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .search-icon {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.4rem;
        }
        
        .section {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin-bottom: 2rem;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .section-header h2 {
            margin: 0;
            font-size: 2rem;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .see-more {
            color: #fff;
            text-decoration: none;
            padding: 0.7rem 1.5rem;
            border: 2px solid rgba(79, 172, 254, 0.5);
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .see-more:hover {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }
        
        .scroll-container {
            overflow-x: auto;
            white-space: nowrap;
            padding: 1rem 0;
            scrollbar-width: thin;
            scrollbar-color: rgba(79, 172, 254, 0.5) rgba(255, 255, 255, 0.1);
        }
        
        .scroll-container::-webkit-scrollbar {
            height: 8px;
        }
        
        .scroll-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }
        
        .scroll-container::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            border-radius: 4px;
        }
        
        .poll-card {
            display: inline-block;
            width: 300px;
            height: 280px;
            margin-right: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 0;
            vertical-align: top;
            white-space: normal;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            cursor: pointer;
        }
        
        .poll-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(79, 172, 254, 0.3);
            box-shadow: 0 8px 32px rgba(79, 172, 254, 0.2);
        }

        .poll-image {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 15px 15px 0 0;
        }

        .poll-content {
            padding: 1.2rem;
        }
        
        .poll-card h3 {
            margin: 0 0 0.8rem 0;
            font-size: 1.3rem;
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .poll-card p {
            margin: 0;
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.5;
        }
        
        .poll-stats {
            margin-top: 2%;
            font-size: 0.9rem;
            color: rgba(79, 172, 254, 0.8);
            font-weight: 500;
        }
        
        .content-container {
            padding: 2rem;
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
                    <li><a href="#">About</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <a href="../view/login.php">Login</a>
                <a href="../view/sign-up.php">Sign Up</a>
            </div>
        </header>

        <div class="search-container">
            <div class="search-wrapper">
                <i class='bx bx-search search-icon'></i>
                <input type="text" class="search-input" placeholder="Search polls, topics, or categories...">
            </div>
        </div>

        <div class="content-container">
            <!-- Sections -->
            <div id="explore-section" class="section">
                <div class="section-header">
                    <h2>Explore</h2>
                    <a href="#" class="see-more">See More</a>
                </div>
                <div class="scroll-container"></div>
            </div>

            <div id="live-polls-section" class="section">
                <div class="section-header">
                    <h2>Live Polls</h2>
                    <a href="../view/live_poll.php" class="see-more">See More</a>
                </div>
                <div class="scroll-container"></div>
            </div>

            <div id="continue-polls-section" class="section">
                <div class="section-header">
                    <h2>Continue Polls</h2>
                    <a href="#" class="see-more">See More</a>
                </div>
                <div class="scroll-container"></div>
            </div>

            <div id="results-section" class="section">
                <div class="section-header">
                    <h2>Results</h2>
                    <a href="../view/results.php" class="see-more">See More</a>
                </div>
                <div class="scroll-container"></div>
            </div>
        </div>
    </div>

    <script>
        // Poll data structure
        const pollData = {
            explore: [
                {
                    id: 1,
                    title: "Technology Trends 2024",
                    description: "Vote on the most impactful tech trends of the year",
                    image: "../assests/images/tech.jpg",
                    stats: "1.2K votes • Ends in 2 days",
                    category: "explore"
                },
                {
                    id: 2,
                    title: "Global Climate Action",
                    description: "Share your views on climate change initiatives",
                    image: "../assests/images/climate.jpg",
                    stats: "3.5K votes • Ends in 5 days",
                    category: "explore"
                },
                {
                    id: 3,
                    title: "Future of Work",
                    description: "Remote vs hybrid vs office - what works best?",
                    image: "../assests/images/office.jpg",
                    stats: "2.8K votes • Ends in 3 days",
                    category: "explore"
                }
            ],
            livePolls: [
                {
                    id: 4,
                    title: "Current Events Poll",
                    description: "Live voting on today's breaking news",
                    image: "../assests/images/events.jpg",
                    stats: "Active Now • 245 participants",
                    category: "live"
                },
                {
                    id: 5,
                    title: "Sports Match Predictions",
                    description: "Vote on tonight's game outcomes",
                    image: "../assests/images/sports.jpg",
                    stats: "Active Now • 1.3K participants",
                    category: "live"
                }
            ],
            customPolls: [
                {
                    id: 6,
                    title: "Company Culture Survey",
                    description: "Private poll for employees only",
                    image: "../assests/images/company.jpg",
                    stats: "Private • 50 participants",
                    category: "custom"
                },
                {
                    id: 7,
                    title: "Event Planning Poll",
                    description: "Help decide the next meetup details",
                    image: "../assests/images/events.jpg",
                    stats: "Group Poll • 28 participants",
                    category: "custom"
                }
            ],
            continuePolls: [
                {
                    id: 8,
                    title: "City Development Project",
                    description: "Phase 2 voting now open",
                    image: "../assests/images/city.jpg",
                    stats: "You voted in Phase 1 • Continue to Phase 2",
                    category: "continue"
                }
            ],
            results: [
                {
                    id: 9,
                    title: "2024 Tech Predictions",
                    description: "See how your predictions compared",
                    image: "/api/placeholder/300/140",
                    stats: "Final Results • 10.2K participants",
                    category: "results"
                },
                {
                    id: 10,
                    title: "Community Project Vote",
                    description: "View the winning proposals",
                    image: "/api/placeholder/300/140",
                    stats: "Results Published • 3.4K votes",
                    category: "results"
                }
            ]
        };

        // Function to create a single poll card
        function createPollCard(poll) {
            const card = document.createElement('div');
            card.className = 'poll-card';
            card.innerHTML = `
                
                <div class="poll-content">
                    <h3>${poll.title}</h3>
                    <p>${poll.description}</p>
                    <div class="poll-stats">${poll.stats}</div>
                    <img src="${poll.image}" alt="${poll.title}" class="poll-image">
                </div>
            `;
            
            // Add click event listener
            card.addEventListener('click', () => handlePollClick(poll));
            
            return card;
        }

        // Function to handle poll card clicks
        function handlePollClick(poll) {
            console.log(`Poll clicked:`, poll);
        }

        // Function to populate a section with polls
        function populateSection(sectionId, polls) {
            const section = document.querySelector(`#${sectionId} .scroll-container`);
            if (!section) return;
            
            section.innerHTML = ''; // Clear existing content
            polls.forEach(poll => {
                section.appendChild(createPollCard(poll));
            });
        }

        // Function to add a new poll
        function addNewPoll(category, newPoll) {
            // Ensure the category exists in pollData
            if (!pollData[category]) {
                pollData[category] = [];
            }

            // Add the new poll to the appropriate category
            pollData[category].push({
                ...newPoll,
                id: generateUniqueId(),
                category: category
            });

            // Refresh the section display
            populateSection(`${category}-section`, pollData[category]);
        }

        // Function to update an existing poll
        function updatePoll(category, updatedPoll) {
            const polls = pollData[category];
            if (!polls) return;

            const index = polls.findIndex(poll => poll.id === updatedPoll.id);
            if (index !== -1) {
                polls[index] = { ...polls[index], ...updatedPoll };
                populateSection(`${category}-section`, polls);
            }
        }

        // Function to delete a poll
        function deletePoll(category, pollId) {
            const polls = pollData[category];
            if (!polls) return;

            const index = polls.findIndex(poll => poll.id === pollId);
            if (index !== -1) {
                polls.splice(index, 1);
                populateSection(`${category}-section`, polls);
            }
        }

        // Function to generate a unique ID
        function generateUniqueId() {
            return Date.now().toString(36) + Math.random().toString(36).substr(2);
        }

        // Function to filter polls
        function filterPolls(searchTerm) {
            searchTerm = searchTerm.toLowerCase();
            
            Object.keys(pollData).forEach(category => {
                const filteredPolls = pollData[category].filter(poll => 
                    poll.title.toLowerCase().includes(searchTerm) ||
                    poll.description.toLowerCase().includes(searchTerm)
                );
                populateSection(`${category}-section`, filteredPolls);
            });
        }

        // Initialize search functionality
        document.querySelector('.search-input').addEventListener('input', (e) => {
            filterPolls(e.target.value);
        });

        // Initialize all sections when the document is loaded
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize each section
            populateSection('explore-section', pollData.explore);
            populateSection('live-polls-section', pollData.livePolls);
            populateSection('custom-polls-section', pollData.customPolls);
            populateSection('continue-polls-section', pollData.continuePolls);
            populateSection('results-section', pollData.results);
        });

        // Example usage:
        /*
        // Add a new poll
        const newPoll = {
            title: "New Technology Survey",
            description: "Share your thoughts on emerging tech",
            image: "/api/placeholder/300/140",
            stats: "New • 0 participants"
        };
        addNewPoll('explore', newPoll);

        // Update an existing poll
        const updatedPoll = {
            id: 1,
            stats: "1.5K votes • Ends in 1 day"
        };
        updatePoll('explore', updatedPoll);

        // Delete a poll
        deletePoll('explore', 1);
        */
    </script>
</body>
</html>