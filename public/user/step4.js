// Step 4: Receivers
let selectedReceivers = [];
let currentReceiverIndex = 0;

function loadReceivers() {
    fetch("/user/receivers")
        .then((response) => response.json())
        .then((data) => {
            if (data && data.length > 0) {
                displayReceivers(data);
            } else {
                document.getElementById("receivers-list").innerHTML =
                    '<div class="alert alert-info">' + (translations?.no_receivers_found || "No receivers found") + "</div>";
            }
        })
        .catch((error) => {
            document.getElementById("receivers-list").innerHTML =
                '<div class="alert alert-danger">' + (translations?.error_loading_receivers || "Error loading receivers") + "</div>";
        });
}

function displayReceivers(receivers) {
    const container = document.getElementById("receivers-list");
    if (!container) return;

    let receiversHTML = "";
    receivers.forEach((receiver) => {
        receiversHTML += '<div class="receiver-item mb-3 p-3 border rounded" onclick="selectReceiver(\'' + receiver.id + '\')" style="cursor: pointer;">';
        receiversHTML += '<h6>' + receiver.name + '</h6>';
        receiversHTML += '<p class="mb-1"><strong>Phone:</strong> ' + receiver.phone + '</p>';
        receiversHTML += '<p class="mb-1"><strong>City:</strong> ' + (receiver.city_name || receiver.city) + '</p>';
        receiversHTML += '<p class="mb-0"><strong>Country:</strong> ' + (receiver.country_name || receiver.country) + '</p>';
        receiversHTML += '</div>';
    });

    container.innerHTML = receiversHTML;
}

function selectReceiver(receiverId) {
    const receiverItems = document.querySelectorAll(".receiver-item");
    receiverItems.forEach((item) => item.classList.remove("selected"));

    event.target.closest(".receiver-item").classList.add("selected");

    populateReceiverForm(receiverId);
    showReceiverForm();
}

function populateReceiverForm(receiverId) {
    const receiver = selectedReceivers.find(r => r.id === receiverId);
    if (!receiver) return;

    const nameField = document.getElementById("name");
    const phoneField = document.getElementById("phone");
    const additionalPhoneField = document.getElementById("additional_phone");
    const emailField = document.getElementById("email");
    const addressField = document.getElementById("address");
    const postalCodeField = document.getElementById("postal_code");

    if (nameField) nameField.value = receiver.name || "";
    if (phoneField) phoneField.value = receiver.phone || "";
    if (additionalPhoneField) additionalPhoneField.value = receiver.additional_phone || "";
    if (emailField) emailField.value = receiver.email || "";
    if (addressField) addressField.value = receiver.address || "";
    if (postalCodeField) postalCodeField.value = receiver.postal_code || "";

    const countryField = document.getElementById("country");
    const stateField = document.getElementById("state");
    const cityField = document.getElementById("city");

    if (countryField && receiver.country_name) {
        const countryDisplayName = typeof receiver.country_name === 'string' ? receiver.country_name : 
            (receiver.country_name.ar || receiver.country_name.en || receiver.country_name);
        countryField.innerHTML = '<option value="">' + (translations?.select_country || 'Select Country') + '</option>';
        countryField.disabled = false;
    }

    if (stateField && receiver.state_name) {
        const stateDisplayName = typeof receiver.state_name === 'string' ? receiver.state_name : 
            (receiver.state_name.ar || receiver.state_name.en || receiver.state_name);
        stateField.innerHTML = '<option value="">' + (translations?.select_state || 'Select State') + '</option>';
        stateField.disabled = false;
    }

    if (cityField && receiver.city_name) {
        const cityDisplayName = typeof receiver.city_name === 'string' ? receiver.city_name : 
            (receiver.city_name.ar || receiver.city_name.en || receiver.city_name);
        cityField.innerHTML = '<option value="">' + (translations?.select_city || 'Select City') + '</option>';
        cityField.disabled = false;
    }

    if (receiver.country_id) {
        loadCountriesForExistingReceiver(receiver.country_id, countryDisplayName);
        loadStatesByCountry(receiver.country_id, receiver.state_id);
        loadReceiverCitiesForState(receiver.state_id, receiver.city_id);
    }

    ensureReceiverStateFieldVisible();
}

function clearReceiverForm() {
    const fields = ["name", "phone", "additional_phone", "email", "address", "postal_code"];
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) field.value = "";
    });

    const countryField = document.getElementById("country");
    const stateField = document.getElementById("state");
    const cityField = document.getElementById("city");

    if (countryField) {
        countryField.innerHTML = '<option value="">' + (translations?.select_country || 'Select Country') + '</option>';
        countryField.disabled = false;
    }
    if (stateField) {
        stateField.innerHTML = '<option value="">' + (translations?.select_state || 'Select State') + '</option>';
        stateField.disabled = false;
    }
    if (cityField) {
        cityField.innerHTML = '<option value="">' + (translations?.select_city || 'Select City') + '</option>';
        cityField.disabled = false;
    }
}

function makeReceiverFormEditable() {
    const fields = ["name", "phone", "additional_phone", "email", "address", "postal_code", "country", "state", "city"];
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.disabled = false;
            field.readOnly = false;
        }
    });
}

function ensureReceiverStateFieldVisible() {
    const stateField = document.getElementById("state");
    if (stateField) {
        stateField.style.display = "block";
        stateField.style.visibility = "visible";
        stateField.style.opacity = "1";
        stateField.disabled = false;
        stateField.removeAttribute("readonly");
    }
}

function canProceedToNextStep() {
    return selectedReceivers.length > 0;
}

function initializeMultipleReceivers() {
    selectedReceivers = [];
    currentReceiverIndex = 0;

    const receiversContainer = document.getElementById("receivers-container");
    if (receiversContainer) {
        receiversContainer.style.display = "none";
    }

    const successMsgContainer = document.getElementById("receiver-success-msg");
    if (successMsgContainer) {
        successMsgContainer.style.display = "none";
    }

    const actionButtons = document.getElementById("receiver-action-buttons");
    if (actionButtons) {
        actionButtons.style.display = "none";
    }

    setupActionButtons();
    loadReceivers();

    const btnNext = document.getElementById("btn-next");
    if (btnNext) {
        btnNext.disabled = true;
    }
}

function setupActionButtons() {
    // Implementation for action buttons
    console.log("Setting up action buttons");
}

function showReceiverForm() {
    // Implementation for showing receiver form
    console.log("Showing receiver form");
}

// Function to load receiver states
function loadReceiverStates() {
    // Implementation for loading receiver states
    console.log("Loading receiver states");
}

// Function to load receiver cities
function loadReceiverCities() {
    // Implementation for loading receiver cities
    console.log("Loading receiver cities");
}

// Function to reset receiver type selection
function resetReceiverTypeSelection() {
    const existingReceiverRadio = document.getElementById("existing_receiver");
    const newReceiverRadio = document.getElementById("new_receiver");
    const existingSection = document.getElementById("existing_receiver_section");
    const newSection = document.getElementById("new_receiver_section");

    // Uncheck both radio buttons
    if (existingReceiverRadio) existingReceiverRadio.checked = false;
    if (newReceiverRadio) newReceiverRadio.checked = false;

    // Hide both sections
    if (existingSection) existingSection.style.display = "none";
    if (newSection) newSection.style.display = "none";

    // Reset receiver select
    const receiverSelect = document.getElementById("receiver_select");
    if (receiverSelect) receiverSelect.value = "";
}

// Function to show multiple receiver controls
function showMultipleReceiverControls() {
    // Show action buttons for adding receivers
    const actionButtons = document.getElementById("receiver-action-buttons");
    if (actionButtons) actionButtons.style.display = "block";

    // Show the receivers container if it exists
    const receiversContainer = document.getElementById("receivers-container");
    if (receiversContainer) receiversContainer.style.display = "block";

    // Show success message container
    const successMsg = document.getElementById("receiver-success-msg");
    if (successMsg) successMsg.style.display = "block";

    // Update the display to show current state
    displaySelectedReceivers();
}

// Function to display selected receivers
function displaySelectedReceivers() {
    // Implementation for displaying selected receivers
    console.log("Displaying selected receivers:", selectedReceivers);
}

// Function to setup receiver form by shipping type
function setupReceiverFormByShippingType() {
    const countryField = document.getElementById("country");
    const stateField = document.getElementById("state");
    const cityField = document.getElementById("city");
    
    if (!countryField || !stateField || !cityField) return;
    
    const isInternational = selectedMethod === "international";
    
    if (isInternational) {
        loadCountries();
        stateField.innerHTML = '<option value="">' + (translations?.select_state || 'Select State') + '</option>';
        stateField.disabled = true;
        cityField.innerHTML = '<option value="">' + (translations?.select_city || 'Select City') + '</option>';
        cityField.disabled = true;
    } else {
        countryField.innerHTML = '<option value="65fd1a1c1fdbc094e3369b2a" selected>Saudi Arabia</option>';
        countryField.disabled = true;
        loadSaudiArabiaStates();
        cityField.innerHTML = '<option value="">' + (translations?.select_city || 'Select City') + '</option>';
        cityField.disabled = true;
    }
}

// Function to load countries for existing receiver
function loadCountriesForExistingReceiver(selectedCountryId, selectedCountryName) {
    const countryField = document.getElementById("country");
    if (!countryField) return;
    
    fetch('https://ghaya-express-staging-af597af07557.herokuapp.com/api/countries?page=0&pageSize=100&orderColumn=createdAt&orderDirection=desc', {
        headers: {
            'accept': '*/*',
            'x-api-key': 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.results && data.results.length > 0) {
            countryField.innerHTML = '<option value="">' + (translations?.select_country || 'Select Country') + '</option>';
            
            data.results.forEach(country => {
                const option = document.createElement('option');
                option.value = country.id;
                option.textContent = country.name.en || country.name.ar || country.name;
                option.dataset.countryData = JSON.stringify(country);
                
                if (country.id === selectedCountryId) {
                    option.selected = true;
                }
                
                countryField.appendChild(option);
            });
            
            countryField.disabled = false;
        } else {
            countryField.innerHTML = '<option value="">' + (translations?.no_countries_found || 'No countries found') + '</option>';
            countryField.disabled = true;
        }
    })
    .catch(error => {
        countryField.innerHTML = '<option value="">' + (translations?.error_loading_countries || 'Error loading countries') + '</option>';
        countryField.disabled = true;
    });
}

// Function to load states by country
function loadStatesByCountry(countryId, selectedStateId = null) {
    const stateField = document.getElementById("state");
    if (!stateField) return;
    
    fetch(`https://ghaya-express-staging-af597af07557.herokuapp.com/api/states?page=0&pageSize=100&orderColumn=createdAt&orderDirection=desc&countryId=${countryId}`, {
        headers: {
            'accept': '*/*',
            'x-api-key': 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.results && data.results.length > 0) {
            stateField.innerHTML = '<option value="">' + (translations?.select_state || 'Select State') + '</option>';
            
            data.results.forEach((state, index) => {
                const option = document.createElement('option');
                option.value = state.id || state._id;
                
                const currentLocale = document.documentElement.lang || 'en';
                if (currentLocale === 'ar' && state.name?.ar) {
                    option.textContent = state.name.ar;
                } else if (state.name?.en) {
                    option.textContent = state.name.en;
                } else {
                    option.textContent = state.name || `State ${index + 1}`;
                }
                
                option.dataset.stateData = JSON.stringify(state);
                stateField.appendChild(option);
                
                if (selectedStateId && (state.id === selectedStateId || state._id === selectedStateId)) {
                    option.selected = true;
                }
            });
            
            stateField.disabled = false;
        } else {
            stateField.innerHTML = '<option value="">' + (translations?.no_states_found || 'No states found') + '</option>';
            stateField.disabled = true;
        }
    })
    .catch(error => {
        stateField.innerHTML = '<option value="">' + (translations?.error_loading_states || 'Error loading states') + '</option>';
        stateField.disabled = true;
    });
}

// Function to load receiver cities for state
function loadReceiverCitiesForState(stateId, selectedCityId = null) {
    const cityField = document.getElementById("city");
    if (!cityField) return;
    
    fetch(`https://ghaya-express-staging-af597af07557.herokuapp.com/api/cities?page=0&pageSize=100&orderColumn=createdAt&orderDirection=desc&stateId=${stateId}`, {
        headers: {
            'accept': '*/*',
            'x-api-key': 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.results && data.results.length > 0) {
            cityField.innerHTML = '<option value="">' + (translations?.select_city || 'Select City') + '</option>';
            
            data.results.forEach((city, index) => {
                const option = document.createElement('option');
                option.value = city.id || city._id;
                
                const currentLocale = document.documentElement.lang || 'en';
                if (currentLocale === 'ar' && city.name?.ar) {
                    option.textContent = city.name.ar;
                } else if (city.name?.en) {
                    option.textContent = city.name.en;
                } else {
                    option.textContent = city.name || `City ${index + 1}`;
                }
                
                option.dataset.cityData = JSON.stringify(city);
                cityField.appendChild(option);
                
                if (selectedCityId && (city.id === selectedCityId || city._id === selectedCityId)) {
                    option.selected = true;
                }
            });
            
            cityField.disabled = false;
        } else {
            cityField.innerHTML = '<option value="">' + (translations?.no_cities_available || 'No cities available') + '</option>';
            cityField.disabled = true;
        }
    })
    .catch(error => {
        cityField.innerHTML = '<option value="">' + (translations?.error_loading_cities || 'Error loading cities') + '</option>';
        cityField.disabled = true;
    });
}

// Function to populate receiver form
function populateReceiverForm(receiverId) {
    const receiver = selectedReceivers.find(r => r.id === receiverId);
    if (!receiver) return;

    const nameField = document.getElementById("name");
    const phoneField = document.getElementById("phone");
    const additionalPhoneField = document.getElementById("additional_phone");
    const emailField = document.getElementById("email");
    const addressField = document.getElementById("address");
    const postalCodeField = document.getElementById("postal_code");

    if (nameField) nameField.value = receiver.name || "";
    if (phoneField) phoneField.value = receiver.phone || "";
    if (additionalPhoneField) additionalPhoneField.value = receiver.additional_phone || "";
    if (emailField) emailField.value = receiver.email || "";
    if (addressField) addressField.value = receiver.address || "";
    if (postalCodeField) postalCodeField.value = receiver.postal_code || "";

    const countryField = document.getElementById("country");
    const stateField = document.getElementById("state");
    const cityField = document.getElementById("city");

    if (countryField && receiver.country_name) {
        const countryDisplayName = typeof receiver.country_name === 'string' ? receiver.country_name : 
            (receiver.country_name.ar || receiver.country_name.en || receiver.country_name);
        countryField.innerHTML = '<option value="">' + (translations?.select_country || 'Select Country') + '</option>';
        countryField.disabled = false;
    }

    if (stateField && receiver.state_name) {
        const stateDisplayName = typeof receiver.state_name === 'string' ? receiver.state_name : 
            (receiver.state_name.ar || receiver.state_name.en || receiver.state_name);
        stateField.innerHTML = '<option value="">' + (translations?.select_state || 'Select State') + '</option>';
        stateField.disabled = false;
    }

    if (cityField && receiver.city_name) {
        const cityDisplayName = typeof receiver.city_name === 'string' ? receiver.city_name : 
            (receiver.city_name.ar || receiver.city_name.en || receiver.city_name);
        cityField.innerHTML = '<option value="">' + (translations?.select_city || 'Select City') + '</option>';
        cityField.disabled = false;
    }

    if (receiver.country_id) {
        loadCountriesForExistingReceiver(receiver.country_id, countryDisplayName);
        loadStatesByCountry(receiver.country_id, receiver.state_id);
        loadReceiverCitiesForState(receiver.state_id, receiver.city_id);
    }

    ensureReceiverStateFieldVisible();
}
