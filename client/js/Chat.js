let currentChatID = null;
let userID = null;
const chatHistory = document.querySelector(".chatHistory");
const messageForm = document.querySelector(".messageForm");
const textarea = document.getElementById("newMessage");
const chatTitle = document.querySelector(".chatTitle .title");

// Auto-load the first chat on page load
window.addEventListener("DOMContentLoaded", () => {
    const firstLink = document.querySelector(".chatLink");
    if (firstLink) {
        userID = firstLink.dataset.userid;
        currentChatID = firstLink.dataset.chatid;
        chatTitle.textContent = firstLink.textContent.trim(); // Set title
        firstLink.classList.add('active');
        loadMessages(currentChatID);
    }
});

// Auto-resize textarea
textarea.addEventListener('input', function () {
    this.style.height = '30px';
    this.style.height = this.scrollHeight + 'px';
});

// Scroll to bottom on load
window.addEventListener('load', function () {
    if (chatHistory) {
        chatHistory.scrollTop = chatHistory.scrollHeight;
    }
});

// Load messages from server for a given chat ID
function loadMessages(chatID) {
    if (!chatID) return;
    fetch(`/FYP2025/SPAMS/server/controllers/LoadMessagesController.php?chatID=${chatID}`)
        .then(res => res.json())
        .then(data => {
            chatHistory.innerHTML = '';
            data.forEach(msg => {
                const msgDiv = document.createElement("div");
                msgDiv.className = msg.isSender ? "userMessage" : "otherMessage";
                msgDiv.innerHTML = `
                    <span class="${msg.isSender ? "userName" : "otherName"}">${msg.name}</span>
                    <span class="${msg.isSender ? "userMsgContent" : "otherMsgContent"}">${msg.content}</span>
                `;
                chatHistory.appendChild(msgDiv);
            });
            chatHistory.scrollTop = chatHistory.scrollHeight;
        });
}

// Handle sending message
messageForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const content = textarea.value.trim();
    if (!content || !currentChatID || !userID) return;

    fetch("/FYP2025/SPAMS/server/controllers/SendMessageController.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ chatID: currentChatID, userID: userID, content })
    })
        .then(async res => {
            const text = await res.text(); // Get raw text
            try {
                return JSON.parse(text);   // Try to parse JSON
            } catch (e) {
                console.error("Server returned invalid JSON:", text);
                throw e;
            }
        })
        .then(data => {
            if (data.success) {
                textarea.value = "";
                textarea.style.height = '30px';
                loadMessages(currentChatID);
            }
        });
});

// Handle clicking on a chat
document.querySelectorAll(".chatLink").forEach(link => {
    link.addEventListener("click", function (e) {
        e.preventDefault();

        // Update user ID and chat ID from clicked link
        userID = this.dataset.userid;
        currentChatID = this.dataset.chatid;

        console.log(userID);
        console.log(currentChatID);

        // Update active class
        document.querySelectorAll('.chatLink').forEach(link => link.classList.remove('active'));
        this.classList.add('active');

        loadMessages(currentChatID);
    });
});

// Polling setup
setInterval(() => {
    if (currentChatID) loadMessages(currentChatID);
}, 5000);
