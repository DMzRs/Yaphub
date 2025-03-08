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

//to get all friend and non-friend data of the current user
async function fetchFriendsData() {
    await loadConfig();

    fetch(config.DISCOVER_PEOPLE_URL)
        .then(response => response.json())
        .then(data => {
            // Update Current Friends List
            const friendsList = document.getElementById('current-friends-list');
            friendsList.innerHTML = '';
            if (data.friends.length === 0) {
                friendsList.innerHTML = '<p>No friends at the moment.</p>';
            } else {
                data.friends.forEach(friend => {
                    const profileImage = friend.profile_picture ? `${friend.profile_picture}` : 'images/profile_img/default_profile.jpg';
                    friendsList.innerHTML += `
                        <button class="current-friend" onclick="window.location.href='viewFriendProfilePage.php?user_id=${friend.user_id}'">
                            <div class="profile-current-friend">
                                <img src="${profileImage}" alt="profile">
                            </div>
                            <div class="name-current-friend">
                                <h3>${friend.firstName} ${friend.lastName}</h3>
                            </div>
                            <div class="online-or-not">
                                <span class="online-status ${friend.is_online == 1 ? 'online' : 'offline'}">
                                    ${friend.is_online == 1 ? 'Online' : 'Offline'}
                                </span>
                            </div>
                        </button>
                    `;
                });
            }

            // Update Suggested Friends List
            const suggestedFriendsList = document.getElementById('suggested-friends-list');
            suggestedFriendsList.innerHTML = '';
            if (data.nonFriends.length === 0) {
                suggestedFriendsList.innerHTML = '<p>No other users at the moment.</p>';
            } else {
                data.nonFriends.forEach(nonfriend => {
                    const profileImage = nonfriend.profile_picture ? `${nonfriend.profile_picture}` : 'images/profile_img/default_profile.jpg';
                    suggestedFriendsList.innerHTML += `
                        <div class="profile-container" data-name="${nonfriend.firstName} ${nonfriend.lastName}" onclick="window.location.href='viewFriendProfilePage.php?user_id=${nonfriend.user_id}'">
                            <div class="profile-img">
                                <img src="${profileImage}" alt="profile">
                            </div>
                            <div class="name">
                                <h2>${nonfriend.firstName} ${nonfriend.lastName}</h2>
                            </div>
                            ${nonfriend.is_pending == 0 ? `
                                <form class="addFriendForm" data-user-id="${nonfriend.user_id}" onclick="event.stopPropagation();">
                                    <button type="button" class="add-icon">
                                        <img src="images/icons/addUser_icon.png" alt="add">
                                    </button>
                                </form>
                            ` : `
                                <button class="add-icon pending-btn" disabled onclick="event.stopPropagation();">
                                    <span>Pending</span>
                                </button>
                            `}
                        </div>
                    `;
                });
            }

            // Update Friend Requests List
            const friendRequestList = document.getElementById('friend-request-list');
            friendRequestList.innerHTML = '';
            if (data.friendRequests.length === 0) {
                friendRequestList.innerHTML = '<p>No friend requests.</p>';
            } else {
                data.friendRequests.forEach(request => {
                    const profileImage = request.profile_picture ? `${request.profile_picture}` : 'images/profile_img/default_profile.jpg';
                    friendRequestList.innerHTML += `
                        <div class="fr-profile-container">
                            <div class="fr-profile-img">
                                <img src="${profileImage}" alt="profile">
                            </div>
                            <div class="fr-name">
                                <h2>${request.firstName} ${request.lastName}</h2>
                            </div>
                            <form class="handleRequestForm" data-user-id="${request.user_id}">
                                <button type="button" class="acceptBtn buttons">Accept</button>
                                <button type="button" class="rejectBtn buttons">Reject</button>
                            </form>
                        </div>
                    `;
                });
            }

            attachEventListeners();
            filterSuggestedFriends();
        })
        .catch(error => console.error('Error fetching data:', error));
}

//function for search on suggested friends search bar
function filterSuggestedFriends() {
    const searchValue = document.getElementById('search').value.toLowerCase();
    const profiles = document.querySelectorAll('#suggested-friends-list .profile-container');

    profiles.forEach(profile => {
        const nameText = profile.querySelector('.name h2').textContent.toLowerCase();

        if (nameText.includes(searchValue)) {
            profile.style.display = 'flex';
        } else {
            profile.style.display = 'none';
        }
    });
}

// onclick event handler for addfriend, accept and reject buttons
function attachEventListeners() {
    document.querySelectorAll('.addFriendForm').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const friendId = this.dataset.userId;
            addFriend(friendId);
        });

        form.querySelector('button').addEventListener('click', function () {
            const friendId = form.closest('.addFriendForm').dataset.userId;
            addFriend(friendId);
        });
    });

    document.querySelectorAll('.handleRequestForm').forEach(form => {
        const friendId = form.dataset.userId;
        form.querySelector('.acceptBtn').addEventListener('click', () => handleFriendRequest(friendId, 'accept'));
        form.querySelector('.rejectBtn').addEventListener('click', () => handleFriendRequest(friendId, 'reject'));
    });
}

//function for add friend button
async function addFriend(friendId) {
    await loadConfig();

    fetch(config.ADD_FRIEND_URL, {
        method: 'POST',
        body: new URLSearchParams({ add_friend_id: friendId }),
    })
        .then(() => fetchFriendsData())
        .catch(error => console.error('Error adding friend:', error));
}

//function for accept and reject button sent to sqlquery friendreq.php
async function handleFriendRequest(friendId, action) {
    await loadConfig();
    fetch(config.HANDLE_FRIEND_REQUEST_URL, {
        method: 'POST',
        body: new URLSearchParams({ friend_id: friendId, [action]: true }),
    })
        .then(() => fetchFriendsData())
        .catch(error => console.error(`Error ${action}ing friend request:`, error));
}

document.addEventListener('DOMContentLoaded', () => {
    fetchFriendsData();
    document.getElementById('search').addEventListener('keyup', filterSuggestedFriends);

    //interval for refresh AJAX (1 sec para realtime)
    setInterval(fetchFriendsData, 1000);
});
