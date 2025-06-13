<!-- Shopping Chat Bot Widget -->
<!-- CSS עבור הבוט -->
<style id="chat-bot-styles">
    :root {
        --chat-primary-color: #3498db;
        --chat-secondary-color: #2ecc71;
        --chat-dark-color: #2c3e50;
        --chat-accent-color: #e74c3c;
    }
    
     .chat-bot-toggle {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--chat-primary-color), var(--chat-secondary-color));
        border-radius: 50%;
        box-shadow: 0 4px 20px rgba(52, 152, 219, 0.4);
        cursor: pointer;
        z-index: 10001;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        transition: all 0.3s ease;
        border: none;
    }

    .chat-bot-toggle:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 25px rgba(52, 152, 219, 0.6);
    }

    .chat-bot-toggle.active {
        background: var(--chat-accent-color);
    }

    .chat-container {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 400px;
        height: 600px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        z-index: 10000;
        display: none;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #e1e5e9;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .chat-container.show {
        display: flex;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chat-header {
        background: linear-gradient(135deg, var(--chat-primary-color), var(--chat-secondary-color));
        color: white;
        padding: 20px;
        text-align: center;
        position: relative;
    }

    .chat-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .chat-header p {
        margin: 5px 0 0 0;
        font-size: 14px;
        opacity: 0.9;
    }

    .chat-close {
        position: absolute;
        top: 15px;
        left: 15px;
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        transition: background-color 0.3s;
    }

    .chat-close:hover {
        background-color: rgba(255,255,255,0.2);
    }

    .chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: #f8f9fa;
    }

    /* שינוי של קלאס message לקלאס ייחודי יותר */
    .chat-message {
        margin-bottom: 15px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .chat-message.user {
        flex-direction: row-reverse;
    }

    .chat-message-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .chat-message.bot .chat-message-avatar {
        background: var(--chat-primary-color);
        color: white;
    }

    .chat-message.user .chat-message-avatar {
        background: var(--chat-secondary-color);
        color: white;
    }

    .chat-message-content {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 18px;
        position: relative;
        line-height: 1.4;
    }

    .chat-message.bot .chat-message-content {
        background: white;
        border: 1px solid #e1e5e9;
        margin-right: 10px;
    }

    .chat-message.user .chat-message-content {
        background: var(--chat-primary-color);
        color: white;
        margin-left: 10px;
    }

    .chat-message-time {
        font-size: 11px;
        opacity: 0.7;
        margin-top: 5px;
    }

    .typing-indicator {
        display: none;
        background: white;
        border: 1px solid #e1e5e9;
        border-radius: 18px;
        padding: 12px 16px;
        margin: 0 10px 15px 45px;
    }

    .typing-indicator.show {
        display: block;
    }

    .typing-dots {
        display: flex;
        gap: 4px;
    }

    .typing-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--chat-primary-color);
        animation: typing 1.4s infinite;
    }

    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typing {
        0%, 60%, 100% { opacity: 0.3; }
        30% { opacity: 1; }
    }

    .chat-input-container {
        padding: 20px;
        background: white;
        border-top: 1px solid #e1e5e9;
    }

    .chat-input-wrapper {
        display: flex;
        gap: 10px;
        align-items: flex-end;
    }

    .chat-input {
        flex: 1;
        min-height: 20px;
        max-height: 100px;
        padding: 12px 16px;
        border: 2px solid #e1e5e9;
        border-radius: 20px;
        resize: none;
        font-family: inherit;
        font-size: 14px;
        line-height: 1.4;
        overflow-y: auto;
        transition: border-color 0.3s;
    }

    .chat-input:focus {
        outline: none;
        border-color: var(--chat-primary-color);
    }

    .chat-input::placeholder {
        color: #999;
    }

    .chat-send {
        width: 40px;
        height: 40px;
        background: var(--chat-primary-color);
        border: none;
        border-radius: 50%;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        flex-shrink: 0;
    }

    .chat-send:hover {
        background: #2980b9;
        transform: scale(1.05);
    }

    .chat-send:disabled {
        background: #bbb;
        cursor: not-allowed;
        transform: none;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        margin-top: 10px;
        flex-wrap: wrap;
    }

    .action-btn {
        padding: 8px 16px;
        border: 1px solid var(--chat-primary-color);
        background: white;
        color: var(--chat-primary-color);
        border-radius: 20px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .action-btn:hover {
        background: var(--chat-primary-color);
        color: white;
    }

    .action-btn.primary {
        background: var(--chat-primary-color);
        color: white;
    }

    .action-btn.primary:hover {
        background: #2980b9;
    }

    .action-btn.success {
        background: var(--chat-secondary-color);
        border-color: var(--chat-secondary-color);
        color: white;
    }

    .action-btn.success:hover {
        background: #27ae60;
    }

    .results-summary {
        background: #f8f9fa;
        border: 1px solid #e1e5e9;
        border-radius: 12px;
        padding: 15px;
        margin-top: 10px;
    }

    .results-header {
        font-weight: 600;
        color: var(--chat-dark-color);
        margin-bottom: 10px;
        font-size: 14px;
    }

    .best-store {
        background: var(--chat-secondary-color);
        color: white;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        margin-bottom: 10px;
    }

    .summary-stats {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #666;
    }

    /* Mobile Responsive */
@media (max-width: 768px) {
    .chat-container {
        width: 100vw;
        height: calc(100vh - 100px);
        right: 0;
        bottom: 60px;
        top: 0;
        left: 0;
        border-radius: 0;
        position: fixed;
        z-index: 10002;
    }

    .chat-bot-toggle {
        z-index: 10003;
        display: none;
    }

    .chat-bot-toggle:not(.active) {
        display: flex;
    }

    .chat-header {
        padding: 15px 20px;
        padding-top: max(15px, env(safe-area-inset-top));
        flex-shrink: 0;
    }

    .chat-close {
        top: max(10px, env(safe-area-inset-top));
        z-index: 10;
    }

    .chat-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        min-height: 0;
    }

    .chat-input-container {
        padding: 15px;
        padding-bottom: max(25px, env(safe-area-inset-bottom));
        flex-shrink: 0;
        background: white;
        border-top: 2px solid #e1e5e9;
    }

    .chat-input {
        font-size: 16px;
        min-height: 18px;
        padding: 12px 14px;
    }

    .chat-send {
        width: 45px;
        height: 45px;
        font-size: 18px;
    }
}

    .chat-input-container {
        padding-bottom: max(15px, env(safe-area-inset-bottom));
    }

        .chat-bot-toggle {
            right: 20px;
            bottom: 20px;
        }
    }

    @media (max-width: 480px) {
        .chat-container {
            width: calc(100vw - 20px);
            height: calc(100vh - 120px);
            right: 10px;
            bottom: 80px;
            border-radius: 15px;
        }

        .chat-bot-toggle {
            right: 15px;
            bottom: 15px;
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
    }
    /* מניעת גלילה של הגוף כאשר הצ'אט פתוח */
body.chat-open {
    overflow: hidden;
    position: fixed;
    width: 100%;
    height: 100%;
}

/* תיקונים נוספים למובייל */
@media (max-width: 768px) {
    .chat-container.show {
        overflow: hidden;
    }

    .chat-messages {
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }

    .chat-input {
        -webkit-appearance: none;
        -webkit-border-radius: 20px;
    }

    .chat-container {
        -webkit-transform: translate3d(0,0,0);
        transform: translate3d(0,0,0);
    }

    .chat-container.show {
        animation: slideUpMobile 0.4s ease;
    }

    @keyframes slideUpMobile {
        from {
            opacity: 0;
            transform: translateY(100%);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chat-input:focus {
        transform: translateZ(0);
        -webkit-transform: translateZ(0);
    }
}
</style>

<!-- HTML של הבוט -->
<div id="shopping-chat-bot">
    <!-- Chat Bot Toggle Button -->
    <button class="chat-bot-toggle" id="chatToggle" onclick="WiseCartBot.toggleChat()">
        🤖
    </button>

   <!-- Chat Container -->
    <div class="chat-container" id="chatContainer">
        <div class="chat-header">
            <button class="chat-close" onclick="WiseCartBot.toggleChat()">&times;</button>
            <h3>בוט רשימת קניות חכם</h3>
            <p>שלחו לי רשימת קניות ואמצא לכם את הסופר הזול ביותר</p>
        </div>

        <div class="chat-messages" id="chatMessages">
            <div class="chat-message bot">
                <div class="chat-message-avatar">🤖</div>
                <div class="chat-message-content">
                   שלום! אני הבוט החכם של WiseCart 👋<br>
                    פשוט שלחו לי רשימת קניות ואמצא עבורכם את הסופר הזול ביותר באזורכם!<br><br>
                    לדוגמה: "אני צריך במבה, גלידה ופופקורן בראשון לציון"
                    <div class="chat-message-time" id="welcomeTime"></div>
                </div>
            </div>
        </div>

        <div class="typing-indicator" id="typingIndicator">
            <div class="typing-dots">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>

        <div class="chat-input-container">
            <div class="chat-input-wrapper">
                <textarea 
                    class="chat-input" 
                    id="chatInput" 
                    placeholder="כתבי כאן את רשימת הקניות שלך..."
                    rows="1"
                    onkeypress="WiseCartBot.handleKeyPress(event)"
                    oninput="WiseCartBot.autoResize(this)"
                ></textarea>
                <button class="chat-send" id="sendButton" onclick="WiseCartBot.sendMessage()">
                    ➤
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript של הבוט -->
<script id="chat-bot-script">
// WiseCart Chat Bot Namespace
window.WiseCartBot = (function() {
    'use strict';
    
    // Private variables
    let isWaitingForResponse = false;
    let chatHistory = [];
    let lastShoppingResults = null;
    
    // Configuration
    const config = {
        apiEndpoint: 'process_shopping_list.php',
        saveEndpoint: 'save_shopping_list.php',
        sessionId: generateSessionId()
    };
    
    // Initialize
  function init() {
    updateWelcomeTime();
    
    // Auto-focus on input when chat opens
    document.addEventListener('click', function(e) {
        if (e.target.id === 'chatToggle') {
            setTimeout(() => {
                const input = document.getElementById('chatInput');
                if (input) input.focus();
            }, 300);
        }
    });
    
    // Handle mobile keyboard
    if (isMobile()) {
        setupMobileKeyboardHandling();
    }
}
    
    // Check if mobile device
function isMobile() {
    return window.innerWidth <= 768 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

// Setup mobile keyboard handling
function setupMobileKeyboardHandling() {
    const chatInput = document.getElementById('chatInput');
    const chatContainer = document.getElementById('chatContainer');
    
    if (chatInput && chatContainer) {
        chatInput.addEventListener('focus', function() {
            if (isMobile()) {
                setTimeout(() => {
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }, 300);
            }
        });
        
        chatInput.addEventListener('blur', function() {
            if (isMobile()) {
                setTimeout(() => {
                    window.scrollTo(0, 0);
                }, 100);
            }
        });
    }
}

    // Generate unique session ID
    function generateSessionId() {
        return 'chat_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    // Toggle chat window
function toggleChat() {
    const container = document.getElementById('chatContainer');
    const toggle = document.getElementById('chatToggle');
    
    if (container.classList.contains('show')) {
        container.classList.remove('show');
        toggle.classList.remove('active');
        toggle.innerHTML = '🤖';
        
        // הסרת נעילת הגלילה במובייל
        if (isMobile()) {
            document.body.classList.remove('chat-open');
        }
    } else {
        container.classList.add('show');
        toggle.classList.add('active');
        toggle.innerHTML = '✕';
        
        // נעילת גלילה במובייל
        if (isMobile()) {
            document.body.classList.add('chat-open');
        }
        
        setTimeout(() => {
            const input = document.getElementById('chatInput');
            if (input) {
                input.focus();
                // גלילה לתחתית הצ'אט
                const messages = document.getElementById('chatMessages');
                if (messages) {
                    messages.scrollTop = messages.scrollHeight;
                }
            }
        }, 100);
    }
}
    
    // Handle key press
    function handleKeyPress(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessage();
        }
    }
    
    // Auto resize textarea
    function autoResize(textarea) {
    textarea.style.height = 'auto';
    const maxHeight = isMobile() ? 80 : 100;
    textarea.style.height = Math.min(textarea.scrollHeight, maxHeight) + 'px';
}
    
    // Update welcome time
    function updateWelcomeTime() {
        const welcomeTimeElement = document.getElementById('welcomeTime');
        if (welcomeTimeElement) {
            const now = new Date();
            const timeString = now.toLocaleTimeString('he-IL', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
            welcomeTimeElement.textContent = timeString;
        }
    }
    
    // Send message
    async function sendMessage() {
        const input = document.getElementById('chatInput');
        const message = input.value.trim();
        
        if (!message || isWaitingForResponse) return;

        // Add user message
        addMessage(message, 'user');
        input.value = '';
        input.style.height = 'auto';
        
        // Show typing indicator
        showTypingIndicator();
        isWaitingForResponse = true;
        document.getElementById('sendButton').disabled = true;

        try {
            // Process message with backend
            const response = await processShoppingList(message);
            hideTypingIndicator();
            addBotResponse(response);
            
            // Store results for later use
            lastShoppingResults = response;
            
        } catch (error) {
            console.error('Chat error:', error);
            hideTypingIndicator();
            addMessage('סליחה, קרתה שגיאה. אנא נסי שוב מאוחר יותר. 😔', 'bot');
        }

        isWaitingForResponse = false;
        document.getElementById('sendButton').disabled = false;
    }
    
    // Process shopping list with backend
    async function processShoppingList(message) {
        try {
            const response = await fetch(config.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message: message,
                    userId: getCurrentUserId(),
                    sessionId: config.sessionId
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error || 'Unknown error occurred');
            }

            return data;

        } catch (error) {
            console.error('Error processing shopping list:', error);
            throw error;
        }
    }
    
    // Get current user ID
    function getCurrentUserId() {
        // התאמה למערכת ההתחברות שלך
        // יכול להיות מ-localStorage, sessionStorage, או cookie
        return localStorage.getItem('userId') || 
               sessionStorage.getItem('userId') || 
               getCookie('userId') || 
               null;
    }
    
    // Get cookie value
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }
    
    // Add message to chat - עדכנתי את הקלאסים כאן
function addMessage(content, sender) {
    const messagesContainer = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message ${sender}`; // שינוי מ-message ל-chat-message
    
    const now = new Date();
    const timeString = now.toLocaleTimeString('he-IL', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });

    messageDiv.innerHTML = `
        <div class="chat-message-avatar">${sender === 'user' ? '👤' : '🤖'}</div>
        <div class="chat-message-content">
            ${content}
            <div class="chat-message-time">${timeString}</div>
        </div>
    `;

    messagesContainer.appendChild(messageDiv);
    
    // גלילה חלקה לתחתית
    setTimeout(() => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 50);
    
    // Store in history
    chatHistory.push({
        content: content,
        sender: sender,
        timestamp: now
    });
}
    
    // Show typing indicator
    function showTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) {
            indicator.classList.add('show');
            const messagesContainer = document.getElementById('chatMessages');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }
    
    // Hide typing indicator
    function hideTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) {
            indicator.classList.remove('show');
        }
    }
    
    // Add bot response with results
    function addBotResponse(response) {
        const { foundItems, notFoundItems, bestStore, totalPrice, city, storeAddress, itemCount } = response;
        
        let content = `מצאתי ${itemCount} מוצרים עבורך! 🎉<br><br>`;
        
        if (bestStore) {
            content += `<div class="results-summary">
                <div class="results-header">📍 הסופר הזול ביותר${city ? ` ב${city}` : ''}:</div>
                <div class="best-store">
                    <strong>${bestStore}</strong><br>
                    ${storeAddress ? `${storeAddress}<br>` : ''}
                    סה"כ: ${totalPrice.toFixed(2)} ₪
                </div>
                <div class="summary-stats">
                    <span>✅ ${itemCount} מוצרים נמצאו</span>
                    ${notFoundItems.length > 0 ? `<span>❌ ${notFoundItems.length} לא נמצאו</span>` : ''}
                </div>
            </div>`;
        } else {
            content += `<div class="results-summary">
                <div class="results-header">😔 לא נמצאו מוצרים במאגר</div>
            </div>`;
        }

        if (notFoundItems.length > 0) {
            content += `<br><strong>המוצרים הבאים לא נמצאו במאגר:</strong><br>`;
            notFoundItems.forEach(item => {
                content += `• ${item}<br>`;
            });
        }

        if (foundItems.length > 0) {
            content += `<div class="action-buttons">
                <button class="action-btn primary" onclick="WiseCartBot.showFullDetails()">פירוט מלא</button>
                <button class="action-btn" onclick="WiseCartBot.shareToWhatsApp()">שיתוף לוואטסאפ</button>
            </div>`;
        }

        addMessage(content, 'bot');
    }
    
    // Show full details of the shopping list
    function showFullDetails() {
        if (!lastShoppingResults || !lastShoppingResults.foundItems.length) return;

        let content = `<strong>פירוט מלא של הרשימה:</strong><br><br>`;
        content += `<div class="results-summary">`;
        
        lastShoppingResults.foundItems.forEach((item, index) => {
            content += `
                <div style="border-bottom: 1px solid #eee; padding: 8px 0; ${index === lastShoppingResults.foundItems.length - 1 ? 'border-bottom: none;' : ''}">
                    <strong>${item.name}</strong><br>
                    <small style="color: #666;">מותג: ${item.brand || 'לא צוין'}</small><br>
                    <span style="color: var(--chat-primary-color);">
                        ${item.quantity} יח' × ${item.price} ₪ = <strong>${item.total.toFixed(2)} ₪</strong>
                    </span>
                </div>
            `;
        });
        
        content += `
            <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid var(--chat-primary-color); text-align: center;">
                <strong style="font-size: 16px;">סה"כ: ${lastShoppingResults.totalPrice.toFixed(2)} ₪</strong><br>
                <small style="color: #666;">${lastShoppingResults.bestStore}${lastShoppingResults.storeAddress ? ` - ${lastShoppingResults.storeAddress}` : ''}</small>
            </div>
        </div>`;

        addMessage(content, 'bot');
    }
    
    function debugUserInfo() {
    console.log('localStorage userId:', localStorage.getItem('userId'));
    console.log('sessionStorage userId:', sessionStorage.getItem('userId'));
    console.log('All localStorage:', localStorage);
    console.log('All sessionStorage:', sessionStorage);
    console.log('Cookies:', document.cookie);
}
    
    // Save list to user account
    async function saveToList() {
        if (!lastShoppingResults || !lastShoppingResults.foundItems.length) return;

        const userId = getCurrentUserId();
        if (!userId) {
            addMessage('עליך להתחבר כדי לשמור רשימות. <a href="EXloginPage.php" style="color: var(--chat-primary-color); text-decoration: underline;">התחברי כאן</a>', 'bot');
            return;
        }

        try {
            const response = await fetch(config.saveEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    userId: userId,
                    items: lastShoppingResults.foundItems,
                    totalPrice: lastShoppingResults.totalPrice,
                    storeId: lastShoppingResults.storeId,
                    storeName: lastShoppingResults.bestStore,
                    storeAddress: lastShoppingResults.storeAddress
                })
            });

            const data = await response.json();
            
            if (data.success) {
                addMessage(`הרשימה נשמרה בהצלחה! 💾<br>
                    <a href="EXsavedList.php" style="color: var(--chat-primary-color); text-decoration: underline;">צפי ברשימות השמורות שלך</a>`, 'bot');
            } else {
                throw new Error(data.error || 'שגיאה בשמירת הרשימה');
            }
        } catch (error) {
            console.error('Save error:', error);
            addMessage('אירעה שגיאה בשמירת הרשימה. אנא נסי שוב מאוחר יותר. 😔', 'bot');
        }
    }
    
    // Share to WhatsApp
    function shareToWhatsApp() {
        if (!lastShoppingResults || !lastShoppingResults.foundItems.length) return;

        let text = `🛒 רשימת קניות חכמה מ-WiseCart\n\n`;
        text += `📍 הסופר המומלץ: ${lastShoppingResults.bestStore}\n`;
        if (lastShoppingResults.storeAddress) {
            text += `📧 כתובת: ${lastShoppingResults.storeAddress}\n`;
        }
        text += `💰 סה"כ: ${lastShoppingResults.totalPrice.toFixed(2)} ₪\n\n`;
        
        text += `🛍️ המוצרים:\n`;
        lastShoppingResults.foundItems.forEach(item => {
            text += `• ${item.name} - ${item.quantity} יח' (${item.total.toFixed(2)} ₪)\n`;
        });

        if (lastShoppingResults.notFoundItems.length > 0) {
            text += `\n❌ מוצרים שלא נמצאו:\n`;
            lastShoppingResults.notFoundItems.forEach(item => {
                text += `• ${item}\n`;
            });
        }

        text += `\n🤖 נוצר באמצעות WiseCart - השוואת מחירים חכמה`;
        
        const url = `https://wa.me/?text=${encodeURIComponent(text)}`;
        window.open(url, '_blank');
    }
    
    // Clear chat history
    function clearChat() {
        const messagesContainer = document.getElementById('chatMessages');
        messagesContainer.innerHTML = `
            <div class="chat-message bot">
                <div class="chat-message-avatar">🤖</div>
                <div class="chat-message-content">
                    שלום! אני הבוט החכם של WiseCart 👋<br>
                    פשוט כתבי לי רשימת קניות ואני אמצא לך את הסופר הזול ביותר באזור שלך!<br><br>
                    לדוגמה: "אני צריכה חלב תנובה, לחם, ביצים, בננות בראשון לציון"
                    <div class="chat-message-time" id="welcomeTime"></div>
                </div>
            </div>
        `;
        updateWelcomeTime();
        chatHistory = [];
        lastShoppingResults = null;
    }
    
    // Public API
    return {
        init: init,
        toggleChat: toggleChat,
        handleKeyPress: handleKeyPress,
        autoResize: autoResize,
        sendMessage: sendMessage,
        showFullDetails: showFullDetails,
        saveToList: saveToList,
        shareToWhatsApp: shareToWhatsApp,
        clearChat: clearChat
    };
})();

// Initialize when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', WiseCartBot.init);
} else {
    WiseCartBot.init();
}
</script>