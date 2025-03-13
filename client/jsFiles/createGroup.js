let config = {}; // Config object to store API path

// to ensure that config is loaded
async function loadConfig() {
    try {
        const response = await fetch('http://localhost:3000/api/config'); // Ensure correct API path
        config = await response.json();
    } catch (error) {
        console.error("Error loading config:", error);
    }
}

document.addEventListener("DOMContentLoaded", async function () {
    const searchInput = document.getElementById("searchMember");
    const usersList = document.querySelector(".show-users");
    const addedMembersList = document.querySelector(".added-members");
    const createGroupBtn = document.querySelector(".createGroupbtn");
    const errorContainer = document.querySelector(".error_message");
    let selectedUsers = [];

    await loadConfig();

    // function to get URL parameters
    function getURLParams() {
        const params = new URLSearchParams(window.location.search);
        return params.get("preselected_users") ? params.get("preselected_users").split(",") : [];
    }

    // load users and preselect if needed
    function loadUsers() {
        fetch(config.SHOW_USERS_CREATE_GROUP_URL)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error("Error:", data.error);
                    usersList.innerHTML = "<p>Failed to load users.</p>";
                    return;
                }

                usersList.innerHTML = "";
                const preselectedUsers = getURLParams();
                console.log(preselectedUsers);

                data.friends.forEach(user => {
                    const userDiv = document.createElement("div");
                    userDiv.classList.add("user");
                    userDiv.dataset.userId = user.id;

                    userDiv.innerHTML = `
                        <img class="profilePic" src="${user.profile_img}" alt="Profile">
                        <h2>${user.first_name} ${user.last_name}</h2>
                        <button class="selectBtn"><img src="images/icons/select_icon.png" alt="Select"></button>
                    `;

                    // if there are preselected users
                    if (preselectedUsers.includes(user.id.toString())) {
                        selectUser(userDiv);
                    } else {
                        usersList.appendChild(userDiv);
                    }  
                });
            })
            .catch(error => {
                console.error("Error fetching users:", error);
                usersList.innerHTML = "<p>Something went wrong. Please try again later.</p>";
            });
    }

    loadUsers();

    // function to select a user
    function selectUser(userDiv) {
        const userId = userDiv.dataset.userId;
        const userName = userDiv.querySelector("h2").textContent;
        const userImage = userDiv.querySelector(".profilePic").src;

        if (!selectedUsers.includes(userId)) {
            selectedUsers.push(userId);

            userDiv.remove();

            // add to added members
            const addedUserDiv = document.createElement("div");
            addedUserDiv.classList.add("added-user");
            addedUserDiv.dataset.userId = userId;
            addedUserDiv.innerHTML = `
                <img class="profilePic" src="${userImage}" alt="profile">
                <h2>${userName}</h2>
                <button class="removeBtn"><img src="images/icons/remove_icon.png" alt="remove"></button>
            `;
            addedMembersList.appendChild(addedUserDiv);
        }
    }

    // search for users
    searchInput.addEventListener("input", function () {
        const searchValue = searchInput.value.toLowerCase();
        document.querySelectorAll(".show-users .user").forEach(user => {
            const userName = user.querySelector("h2").textContent.toLowerCase();
            user.style.display = userName.includes(searchValue) ? "flex" : "none";
        });
    });

    // add user to group manually
    usersList.addEventListener("click", function (event) {
        if (event.target.closest(".selectBtn")) {
            const userDiv = event.target.closest(".user");
            selectUser(userDiv);
        }
    });

    // remove user from group
    addedMembersList.addEventListener("click", function (event) {
        if (event.target.closest(".removeBtn")) {
            const userDiv = event.target.closest(".added-user");
            const userId = userDiv.dataset.userId;
            const userName = userDiv.querySelector("h2").textContent;
            const userImage = userDiv.querySelector(".profilePic").src;

            // remove selected users
            selectedUsers = selectedUsers.filter(id => id !== userId);
            const originalUserDiv = document.createElement("div");
            originalUserDiv.classList.add("user");
            originalUserDiv.dataset.userId = userId;
            originalUserDiv.innerHTML = `
                <img class="profilePic" src="${userImage}" alt="profile">
                <h2>${userName}</h2>
                <button class="selectBtn"><img src="images/icons/select_icon.png" alt="Select"></button>
            `;
            usersList.appendChild(originalUserDiv);

            userDiv.remove();
        }
    });

    // submit group creation
    createGroupBtn.addEventListener("click", function () {
        const groupName = document.getElementById("groupName").value.trim();
        
        // clear previous error messages
        errorContainer.innerHTML = "";
        
        if (groupName === "") { 
            errorContainer.innerHTML = `<p class="error">Please enter a group name!</p>`;
            return;
        }
        if (selectedUsers.length < 2) {
            errorContainer.innerHTML = `<p class="error">Please add at least 3 members!</p>`;
            return;
        }
        if (groupName.length < 3) {
            errorContainer.innerHTML = `<p class="error">Group name is too short!</p>`;
            return;
        }
        if (groupName.length > 10) {
            errorContainer.innerHTML = `<p class="error">Group name is too long!</p>`;
            return;
        }
        
        fetch(config.CREATE_GROUP_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ groupName, members: selectedUsers })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showModal("Group created successfully!", "success", true);
            } else {
                showModal("Error creating group: " + data.error, "error");
            }
        })
        .catch(error => console.error("Error:", error));
    });
});


// Function to show the custom alert modal
function showModal(message, type = "error", reload = false) {
    const alertMessage = document.getElementById("alertMessage");
    alertMessage.innerHTML = message;
    alertMessage.style.color = type === "success" ? "green" : "red";
    document.getElementById("customAlertModal").style.display = "flex";
    document.getElementById("modalOkButton").setAttribute("data-reload", reload);
}

// Close modal when clicking OK
document.getElementById("modalOkButton").onclick = function () {
    document.getElementById("customAlertModal").style.display = "none";
    window.location.reload();
};