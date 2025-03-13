const inboxContainer = document.getElementById("inbox");
const searchInbox = document.getElementById("searchInbox");

// Load config
async function loadConfig() {
    try {
        const response = await fetch('http://localhost:3000/api/config');
        config = await response.json();
    } catch (error) {
        console.error("Error loading config:", error);
    }
}

// Load chats on page load and refresh every 5 seconds
document.addEventListener("DOMContentLoaded", function () {
    loadChats();
    setInterval(loadChats, 5000);
});

// Function to load both private and group chats
async function loadChats() {
    await loadConfig();

    try {
        // Fetch both private and group chats
        const [inboxResponse, groupResponse] = await Promise.all([
            fetch(config.INBOX_URL),
            fetch(config.INBOX_GROUP_URL)
        ]);

        const inboxData = await inboxResponse.json();
        const groupData = await groupResponse.json();

        if (inboxData.error || groupData.error) {
            console.error("Error:", inboxData.error || groupData.error);
            return;
        }

        // Merge and sort chats by timestamp
        const allChats = [...inboxData, ...groupData].sort((a, b) => {
            return new Date(b.message_timestamp) - new Date(a.message_timestamp);
        });

        // Display merged inbox
        displayChats(allChats);

        // Automatically select the first chat if none is selected
        if (allChats.length > 0 && !getChatIdFromURL()) {
            setTimeout(() => openChat(allChats[0].chat_id), 100);
        }

    } catch (error) {
        console.error("Fetch Error:", error);
    }
}

// Function to display chats
function displayChats(chats) {
    inboxContainer.innerHTML = ""; // Clear inbox

    chats.forEach(chat => {
        const isGroup = chat.chat_name !== undefined; // If chat_name exists, it's a group
        const profilePic = isGroup 
            ? (chat.group_picture || "images/group_img/default_group.jpg")
            : (chat.profile_picture || "images/profile_img/default_profile.jpg");

        let lastMessage = "No messages yet";

        if (chat.latest_message && chat.latest_message.trim() !== "") {
            lastMessage = chat.latest_message.length > 20 
                ? chat.latest_message.substring(0, 20) + "..." 
                : chat.latest_message;
        } 

        // Check if message is a file (image or video)
        if (chat.latest_file_type !== null) {
            if (chat.latest_file_type.includes("image")) {
                lastMessage = "Sent a photo";
            } else if (chat.latest_file_type.includes("video")) {
                lastMessage = "Sent a video";
            }
        }

        const messageTime = chat.message_timestamp ? formatDate(chat.message_timestamp) : "";

        const chatItem = document.createElement("div");
        chatItem.classList.add("last-message");

        chatItem.innerHTML = `
            <div class="img-container">
                <img src="${profilePic}" alt="${isGroup ? chat.chat_name : chat.firstName}" class="profile-pic">
            </div>
            <div class="names-msg">
                <h3>${isGroup ? chat.chat_name : chat.firstName + " " + chat.lastName}</h3>
                <h4>${lastMessage}</h4>
            </div>
            <div class="timeSent">
                <h4>${messageTime}</h4>
            </div>
        `;

        chatItem.addEventListener("click", function () {
            openChat(chat.chat_id);
        });

        inboxContainer.appendChild(chatItem);
    });
}

// Function to get chat_id from URL
function getChatIdFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get("chat_id") || null;
}

// Format timestamps
function formatDate(timestamp) {
    if (!timestamp) return "No messages yet";
    const date = new Date(timestamp);
    return date.toISOString().split("T")[0];
}

// Open chat
function openChat(chatId) {
    window.location.href = `messagePage.php?chat_id=${chatId}`;
}

searchInbox.addEventListener("input", function () {
    const query = searchInbox.value.toLowerCase();
    const chatItems = inboxContainer.querySelectorAll(".last-message");

    chatItems.forEach(chatItem => {
        const chatName = chatItem.querySelector(".names-msg h3").textContent.toLowerCase();
        if (chatName.includes(query)) {
            chatItem.style.display = "flex"; // Show matching chat
        } else {
            chatItem.style.display = "none"; // Hide non-matching chat
        }
    });
});