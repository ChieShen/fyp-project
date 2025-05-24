let currentChatID = null;
let userID = null;
let isEditing = false;
let selectedMessageID = null;
let selectedMessageContent = "";

const chatHistory = document.querySelector(".chatHistory");
const messageForm = document.querySelector(".messageForm");
const textarea = document.getElementById("newMessage");
const chatTitle = document.querySelector(".chatTitle .title");
const contextMenu = document.getElementById("messageContextMenu");
const pinnedBox = document.getElementById("pinnedMessagesBox");
const pinnedList = document.getElementById("pinnedList");

// Auto-load the first chat on page load
window.addEventListener("DOMContentLoaded", () => {
    const firstLink = document.querySelector(".chatLink");
    if (firstLink) {
        userID = firstLink.dataset.userid;
        currentChatID = firstLink.dataset.chatid;
        chatTitle.textContent = firstLink.textContent.trim();
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

let pinnedMessageIDs = [];

function loadMessages(chatID) {
    if (!chatID) return;
    fetch(`/FYP2025/SPAMS/server/controllers/LoadMessagesController.php?chatID=${chatID}`)
        .then(res => res.json())
        .then(data => {
            chatHistory.innerHTML = "";

            pinnedMessageIDs = data.pinnedMessages.map(m => m.messageID);
            displayPinnedMessages(data.pinnedMessages);

            data.messages.forEach(msg => {
                const msgDiv = document.createElement("div");
                msgDiv.className = msg.isSender ? "userMessage" : "otherMessage";
                msgDiv.dataset.messageid = msg.messageID;
                msgDiv.id = `message-${msg.messageID}`;

                msgDiv.innerHTML = `
                    <span class="${msg.isSender ? "userName" : "otherName"}">${msg.name}</span>
                    <span class="${msg.isSender ? "userMsgContent" : "otherMsgContent"}">${msg.content}</span>
                `;

                if (msg.isSender) {
                    msgDiv.addEventListener("contextmenu", function (e) {
                        e.preventDefault();
                        showContextMenu(e.pageX, e.pageY, msg.messageID, msg.content);
                    });
                }

                chatHistory.appendChild(msgDiv);
            });

            chatHistory.scrollTop = chatHistory.scrollHeight;
        });
}

function showContextMenu(x, y, messageID, content) {
    selectedMessageID = messageID;
    selectedMessageContent = content;

    const pinMenu = document.getElementById("pinMsg");

    // Use the global pinnedMessageIDs to check if message is pinned
    const isPinned = pinnedMessageIDs.includes(messageID);

    pinMenu.textContent = isPinned ? "Unpin" : "Pin";

    contextMenu.style.top = y + "px";
    contextMenu.style.left = x + "px";
    contextMenu.style.display = "block";
}


// Handle message submission
messageForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const content = textarea.value.trim();
    if (!content || !currentChatID || !userID) return;

    if (isEditing) {
        handleMessageAction("edit", { content });
        isEditing = false;
        document.getElementById("sendMessage").textContent = "Send";
        textarea.value = "";
        textarea.style.height = '30px';
        return;
    }

    fetch("/FYP2025/SPAMS/server/controllers/SendMessageController.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ chatID: currentChatID, userID: userID, content })
    })
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
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

// Chat switching
document.querySelectorAll(".chatLink").forEach(link => {
    link.addEventListener("click", function (e) {
        e.preventDefault();
        userID = this.dataset.userid;
        currentChatID = this.dataset.chatid;

        document.querySelectorAll('.chatLink').forEach(link => link.classList.remove('active'));
        this.classList.add('active');

        loadMessages(currentChatID);
    });
});

// Polling
setInterval(() => {
    if (currentChatID) loadMessages(currentChatID);
}, 5000);

window.addEventListener("click", () => {
    contextMenu.style.display = "none";
});

// Message actions
function handleMessageAction(action, extraData = {}) {
    const payload = {
        action,
        messageID: selectedMessageID,
        chatID: currentChatID,
        ...extraData
    };

    fetch("/FYP2025/SPAMS/server/controllers/MessageActionController.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (action === "edit") {
                    textarea.value = "";
                    document.getElementById("sendMessage").textContent = "Send";
                    isEditing = false;
                }
                if (action === "pin" || action === "unpin") {
                    displayPinnedMessages(data.pinnedMessages || []);
                }
                loadMessages(currentChatID);
            }
        });
}

// Edit message
document.getElementById("editMsg").addEventListener("click", () => {
    textarea.value = selectedMessageContent;
    textarea.focus();
    isEditing = true;
    document.getElementById("sendMessage").textContent = "Update";
});

// Delete message
document.getElementById("deleteMsg").addEventListener("click", () => {
    handleMessageAction("delete");
});

// Pin message
document.getElementById("pinMsg").addEventListener("click", () => {
    handleMessageAction("pin");
});

// Show pinned box on title click
chatTitle.addEventListener("click", () => {
    pinnedBox.style.display = pinnedBox.style.display === "none" ? "block" : "none";
});

// Display multiple pinned messages
function displayPinnedMessages(pinnedMessages) {
    pinnedList.innerHTML = "";

    if (!pinnedMessages.length) {
        pinnedBox.style.display = "none";
        return;
    }

    pinnedMessages.forEach(msg => {
        const li = document.createElement("li");
        li.textContent = msg.content;
        li.dataset.messageid = msg.messageID;

        // Scroll to message on click
        li.addEventListener("click", () => {
            const target = document.getElementById(`message-${msg.messageID}`);
            if (target) {
                target.scrollIntoView({ behavior: "smooth", block: "center" });
                pinnedBox.style.display = "none";
            }
        });

        // Unpin button
        const unpinBtn = document.createElement("button");
        unpinBtn.textContent = "Unpin";
        unpinBtn.style.marginLeft = "10px";
        unpinBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            selectedMessageID = msg.messageID;
            handleMessageAction("unpin");
        });

        li.appendChild(unpinBtn);
        pinnedList.appendChild(li);
    });

    pinnedBox.style.display = "block";
}

