let config = {};
const email = document.getElementById('email').value.trim();
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


async function loadConfig() {
    try {
        const response = await fetch('http://localhost:3000/api/config'); // Ensure correct API path
        config = await response.json();
    } catch (error) {
        console.error("Error loading config:", error);
    }
}

document.getElementById('saveProfileChanges').addEventListener('click', async function () {
    const formData = new FormData();
    const fileInput = document.getElementById('file');
    const currentPassword = document.getElementById('currentPassword').value.trim();
    const newPassword = document.getElementById('newPassword').value.trim();

    await loadConfig();

    if (fileInput.files.length > 0) {
        formData.append('profile_picture', fileInput.files[0]);
    }

    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('email').value.trim();
    const address = document.getElementById('address').value.trim();

    if (firstName !== '') formData.append('firstName', firstName);
    if (lastName !== '') formData.append('lastName', lastName);
    if (email !== '') {
        if (!emailRegex.test(email)) {
            closeEditProfileModal();
            showModal("Please enter a valid email address!", "error");
            return;
        }
        formData.append('email', email);
    }
    if (address !== '') formData.append('address', address);

    if (currentPassword !== '') {
        formData.append('currentPassword', currentPassword);

        if (newPassword === '') {
            closeEditProfileModal();
            showModal('Please enter a new password!', "error");
            return;
        }
        formData.append("newPassword", newPassword);
    }

    if (
        fileInput.files.length === 0 &&
        firstName === '' &&
        lastName === '' &&
        email === '' &&
        address === '' &&
        currentPassword === ''
    ) {
        closeEditProfileModal();
        showModal("No changes were made!", "error");
        return;
    }

    fetch(config.EDIT_PROFILE_URL, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        closeEditProfileModal();

        if (data.success) {
            showModal("Profile updated successfully!", "success", true);
        } else {
            showModal(data.error || "Something went wrong!", "error");
        }
    })
    .catch(error => {
        closeEditProfileModal();
        showModal("An error occurred: " + error.message, "error");
    });
});

function closeEditProfileModal() {
    var editProfileModal = bootstrap.Modal.getInstance(document.getElementById('editProfile'));
    if (editProfileModal) {
        editProfileModal.hide();
    }
}

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
    location.reload();
};