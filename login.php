<?php
$pageTitle = "Login";
require 'includes/header.php';

$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';



    require 'config/database.php';
    $sql = "SELECT user_id, role, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'instructor') {
                header("Location: instructor/dashboard.php");
                exit();
            } else {
                header("Location: index.php");
                exit();
            }
        } else {
            $error_message = "Invalid credentials. Please try again.";
        }
    } else {
        $error_message = "Invalid credentials. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!-- 💬 Simple Chatbot UI -->
<div id="chatbot-container">
  <div id="chatbot-header">💬 Chatbot</div>
  <div id="chatbot-messages"></div>
  <div id="chatbot-input-area">
    <input type="text" id="chatbot-input" placeholder="Type a message..." />
    <button id="chatbot-send">Send</button>
  </div>
</div>

<style>
  /* Chatbot Styles */
  #chatbot-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 300px;
    background: white;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    font-family: Arial, sans-serif;
    display: flex;
    flex-direction: column;
  }

  #chatbot-header {
    background: #4a90e2;
    color: white;
    padding: 10px;
    text-align: center;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    font-weight: bold;
  }

  #chatbot-messages {
    padding: 10px;
    height: 250px;
    overflow-y: auto;
    font-size: 14px;
  }

  .chatbot-message {
    margin-bottom: 10px;
  }

  .chatbot-user {
    text-align: right;
    color: #333;
  }

  .chatbot-bot {
    text-align: left;
    color: #4a90e2;
  }

  #chatbot-input-area {
    display: flex;
    border-top: 1px solid #ddd;
  }

  #chatbot-input {
    flex: 1;
    padding: 8px;
    border: none;
    border-radius: 0 0 0 10px;
    outline: none;
  }

  #chatbot-send {
    padding: 8px 12px;
    background: #4a90e2;
    color: white;
    border: none;
    border-radius: 0 0 10px 0;
    cursor: pointer;
  }

  #chatbot-send:hover {
    background: #3b7ac7;
  }
</style>

<script>
  const chatbotMessages = document.getElementById('chatbot-messages');
  const chatbotInput = document.getElementById('chatbot-input');
  const chatbotSend = document.getElementById('chatbot-send');

  // Basic bot replies
    function getBotReply(message) {
      const msg = message.toLowerCase();
      if (msg.includes('hello') || msg.includes('hi')) {
        return "Hello 👋! How can I help you today?";
      } else if (msg.includes('help')) {
        return "Sure! You can ask me about our website, services, or general info.";
      } else if (msg.includes('bye')) {
        return "Goodbye! Have a nice day 😊";
      } else {
        return "I'm just a simple bot 🤖 — try saying 'hello' or 'help'.";
      }
    }

  function addMessage(text, sender) {
    const div = document.createElement('div');
    div.classList.add('chatbot-message');
    div.classList.add(sender === 'user' ? 'chatbot-user' : 'chatbot-bot');
    div.textContent = text;
    chatbotMessages.appendChild(div);
    chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
  }

  chatbotSend.addEventListener('click', () => {
    const text = chatbotInput.value.trim();
    if (text === '') return;
    addMessage(text, 'user');
    chatbotInput.value = '';
    setTimeout(() => {
      const reply = getBotReply(text);
      addMessage(reply, 'bot');
    }, 400);
  });

  chatbotInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
      chatbotSend.click();
    }
  });
</script>


<div class="auth-section">
    <div class="container">
        <div class="row align-items-center justify-content-center min-vh-100 py-5">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                    <div class="row g-0">
                        <!-- Left Column: Image -->
                        <div class="col-lg-6 d-none d-lg-block">
                            <div class="auth-image-container">
                                <!-- You can replace this with your own image -->
                            </div>
                        </div>

                        <!-- Right Column: Login Form -->
                        <div class="col-lg-6">
                            <div class="card-body p-4 p-sm-5">
                                <div class="text-center mb-4">
                                    <h1 class="h3 fw-bold">Welcome Back!</h1>
                                    <p class="text-muted">Sign in to continue to EDUMA</p>
                                </div>

                                <!-- Display Error Message if any -->
                                <?php if (!empty($error_message)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $error_message; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Login Form -->
                                <form action="login.php" method="POST">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" required placeholder="name@example.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                                            <label class="form-check-label" for="rememberMe">
                                                Remember me
                                            </label>
                                        </div>
                                        <a href="forgot-password.php" class="small">Forgot Password?</a>
                                    </div>
                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-primary btn-lg">Login</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>