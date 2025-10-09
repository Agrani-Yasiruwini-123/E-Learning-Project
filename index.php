<?php
$pageTitle = "Home";
require 'includes/header.php';
?>

<!-- Section 1: Hero -->
<section class="hero-section text-white text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">Unlock Your Potential</h1>
        <p class="lead my-4">Join thousands of learners and gain new skills with expert-led courses.</p>
        <a href="courses.php" class="btn btn-primary btn-lg">Explore Courses</a>
        <a href="#features" class="btn btn-outline-light btn-lg">Learn More</a>
    </div>
</section>

<!-- Section 2: Features/Benefits -->
<section id="features" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose EDUMA?</h2>
            <p class="lead text-muted">We provide the best learning experience for you.</p>
        </div>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="feature-icon bg-primary text-white mb-3">
                    <i class="fas fa-video"></i>
                </div>
                <h3 class="h5">Expert-Led Courses</h3>
                <p class="text-muted">Learn from industry professionals with real-world experience.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon bg-primary text-white mb-3">
                    <i class="fas fa-infinity"></i>
                </div>
                <h3 class="h5">Lifetime Access</h3>
                <p class="text-muted">Enroll once and get unlimited lifetime access to course materials.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon bg-primary text-white mb-3">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3 class="h5">Learn Anywhere</h3>
                <p class="text-muted">Access your courses on any device, anytime, anywhere.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Featured Courses -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Explore Our Featured Courses</h2>
            <p class="lead text-muted">Handpicked courses to kickstart your learning journey.</p>
        </div>
    </div>
</section>
<!-- 💬 Simple Chatbot UI -->
<div id="chatbot-container">
  <div id="chatbot-header">💬 Chatbot</div>
  <div id="chatbot-messages"></div>
  <div id="chatbot-input-area">
    <input type="text" id="chatbot-input" placeholder="Type a message..." />
    <button id="chatbot-send">Send</button>
  </div>
</div>
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


<?php require 'includes/footer.php'; ?>