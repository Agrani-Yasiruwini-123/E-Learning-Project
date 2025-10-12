<?php
$pageTitle = "Our Courses";
require 'includes/header.php';
require 'config/database.php';


$search_term = $_GET['search'] ?? '';
$sql = "SELECT c.*, u.username as instructor_name 
        FROM courses c 
        JOIN users u ON c.instructor_id = u.user_id";

if (!empty($search_term)) {
    $sql .= " WHERE c.course_title LIKE ? OR c.course_description LIKE ?";
}
?>

<!-- Section: Page Header -->
<section class="page-header text-center py-5 bg-light">
    <div class="container">
        <h1 class="fw-bold">Explore Our Courses</h1>
        <p class="lead text-muted">Find the perfect course to help you achieve your goals.</p>
    </div>
</section>

<!-- Section: Courses Grid & Filters -->
<section class="py-5">
    <div class="container">
        <!-- Filter Bar -->
        <div class="filter-bar card p-3 mb-4 shadow-sm">
            <form action="courses.php" method="GET">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-9 col-md-12">
                        <input type="text" name="search" class="form-control" placeholder="Search for courses..." value="<?php echo htmlspecialchars($search_term); ?>">
                    </div>
                    <div class="col-lg-3 col-md-12">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </div>
            </form>
        </div>
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


        <!-- Courses Grid -->
        <div class="row">
            <?php
            $stmt = $conn->prepare($sql);
            if (!empty($search_term)) {
                $search_param = "%{$search_term}%";
                $stmt->bind_param("ss", $search_param, $search_param);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='col-lg-4 col-md-6 mb-4 d-flex align-items-stretch'>";
                    echo "<div class='card h-100 shadow-sm border-0 course-card'>";
                    echo "<div class='course-card-img-container'>";
                    echo "<img src='" . htmlspecialchars($row['course_thumbnail']) . "' class='card-img-top' alt='" . htmlspecialchars($row['course_title']) . "'>";
                    echo "<span class='badge bg-primary course-category-badge'>" . htmlspecialchars($row['category']) . "</span>";
                    echo "</div>";
                    echo "<div class='card-body d-flex flex-column'>";
                    echo "<h5 class='card-title'>" . htmlspecialchars($row['course_title']) . "</h5>";
                    echo "<p class='card-text text-muted flex-grow-1'>" . htmlspecialchars(substr($row['course_description'], 0, 100)) . "...</p>";
                    echo "<p class='small text-muted mb-0'>By " . htmlspecialchars($row['instructor_name']) . "</p>";
                    echo "</div>";
                    echo "<div class='card-footer bg-white border-0 text-center py-3'>";
                    echo "<a href='course-overview.php?id=" . $row['course_id'] . "' class='btn btn-primary'>View Details</a>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p class='text-center text-muted'>No courses found matching your criteria.</p>";
            }
            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>