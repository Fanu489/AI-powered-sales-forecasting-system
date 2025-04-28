<?php 
// Include header only once
include 'header.php'; 
?>

<div class="d-flex">
    <!-- Sidebar Navigation -->
    <div class="sidebar bg-dark text-white p-3" style="width: 250px; height: 100vh; position: fixed;">
        <h2 class="text-white mb-4"><i class="bi bi-lightning-charge-fill"></i> AI Forecast</h2>
        <ul class="nav flex-column">
            <li><a class="nav-link text-white" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a class="nav-link text-white" href="upload.php"><i class="bi bi-cloud-upload"></i> Upload Data</a></li>
            <li><a class="nav-link text-white" href="report.php"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a></li>
            <li><a class="nav-link text-white" href="analytics.php"><i class="bi bi-bar-chart"></i> Analytics</a></li>
            <li><a class="nav-link text-white" href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
            <li><a class="nav-link text-white" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
            <li><a class="nav-link text-white" href="notifications.php"><i class="bi bi-bell"></i> Notifications</a></li>
            <li><a class="nav-link text-primary fw-bold" href="help.php"><i class="bi bi-question-circle"></i> Help</a></li>
            <li><a class="nav-link text-white" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content d-flex flex-column justify-content-between" style="margin-left: 250px; min-height: 100vh; width: 100%;">
        <div class="container text-white py-4">
            <h2 class="text-center mb-4">AI Chatbot Help Center</h2>

            <div class="card shadow rounded-4" id="chat-container">
                <div class="card-body" id="chat-box" style="height: 400px; overflow-y: auto; background-color: #1e1e1e;">
                    <div class="bot-message mb-3">🤖 Hello! How can I help you today?</div>
                </div>

                <div class="d-flex p-3 border-top bg-dark-subtle">
                    <input type="text" id="chat-input" class="form-control me-2" placeholder="Ask a question...">
                    <button onclick="sendMessage()" class="btn btn-primary me-2">Send</button>
                    <button onclick="startVoice()" class="btn btn-warning" title="Voice Input">
                        <i class="bi bi-mic-fill"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include 'footer.php'; ?>
    </div>
</div>

<style>
    body {
        background-color: #121212;
        color: #fff;
    }
    .sidebar a {
        display: block;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 5px;
        text-decoration: none;
    }
    .sidebar a:hover, .sidebar a.active {
        background-color: #0d6efd;
        color: #fff !important;
    }
    .card {
        background-color: #2c2c2c;
        border: 1px solid #333;
    }
    .user-message {
        text-align: right;
        background-color: #0d6efd;
        padding: 10px;
        margin: 5px;
        border-radius: 15px 0 15px 15px;
        color: #fff;
    }
    .bot-message {
        text-align: left;
        background-color: #444;
        padding: 10px;
        margin: 5px;
        border-radius: 0 15px 15px 15px;
        animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>

<script>
function sendMessage() {
    const input = document.getElementById("chat-input");
    const message = input.value.trim();
    if (!message) return;

    const chatBox = document.getElementById("chat-box");

    const userMsg = document.createElement("div");
    userMsg.className = "user-message";
    userMsg.textContent = "🧑‍💻 " + message;
    chatBox.appendChild(userMsg);

    const botTyping = document.createElement("div");
    botTyping.className = "bot-message";
    botTyping.textContent = "🤖 Typing...";
    chatBox.appendChild(botTyping);
    chatBox.scrollTop = chatBox.scrollHeight;

    setTimeout(() => {
        fetch('chatbot.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'message=' + encodeURIComponent(message)
        })
        .then(response => response.json())
        .then(data => {
            botTyping.innerHTML = data.reply;
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(error => {
            console.error('Error:', error);
            botTyping.innerHTML = "🤖 Sorry, there was an issue with the server.";
        });
    }, 800);

    input.value = "";
}

function startVoice() {
    if (!('webkitSpeechRecognition' in window)) {
        alert("Your browser doesn't support speech recognition.");
        return;
    }

    const recognition = new webkitSpeechRecognition();
    recognition.lang = 'en-US';
    recognition.onresult = function(event) {
        const transcript = event.results[0][0].transcript;
        document.getElementById("chat-input").value = transcript;
        sendMessage();
    };
    recognition.onerror = function(event) {
        alert("Voice error: " + event.error);
    };
    recognition.start();
}
</script>
