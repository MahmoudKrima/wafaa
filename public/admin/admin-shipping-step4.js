(() => {
    let userData = null;

    function loadUserData(userId) {
        fetch(`/admin/admin-shipping/user-data/${userId}`)
            .then(response => response.json())
            .then(data => {
                userData = data;
                displayUserData(data);
            })
            .catch(error => {
                console.error('Error loading user data:', error);
            });
    }

    function displayUserData(user) {
        const userSelect = document.getElementById('user_select');
        if (userSelect) {
            userSelect.value = user.id;
        }
        updateFormFields(user);
    }

    function updateFormFields(user) {
        const nameField = document.getElementById('user_name');
        if (nameField) {
            nameField.value = user.name || '';
        }

        const phoneField = document.getElementById('user_phone');
        if (phoneField) {
            phoneField.value = user.phone || '';
        }

        const emailField = document.getElementById('user_email');
        if (emailField) {
            emailField.value = user.email || '';
        }

        const addressField = document.getElementById('user_address');
        if (addressField) {
            addressField.value = user.address || '';
        }

        const cityField = document.getElementById('user_city');
        if (cityField) {
            cityField.value = user.city || '';
        }

        const postalField = document.getElementById('user_postal_code');
        if (postalField) {
            postalField.value = user.postal_code || '';
        }
    }

    function validateStep() {
        if (!userData) {
            alert('Please select a user first');
            return false;
        }
        
        return true;
    }

    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get('user_id');
        
        if (userId) {
            loadUserData(userId);
        }
    });
    window.loadUserData = loadUserData;
    window.validateStep = validateStep;
})();
