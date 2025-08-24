// Location Management Functions
function loadCountries() {
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
                countryField.appendChild(option);
            });
            
            countryField.disabled = false;
            countryField.removeEventListener('change', handleCountryChange);
            countryField.addEventListener('change', handleCountryChange);
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

function handleCountryChange() {
    const countryId = this.value;
    const stateField = document.getElementById("state");
    const cityField = document.getElementById("city");
    
    if (stateField) {
        stateField.innerHTML = '<option value="">' + (translations?.select_state || 'Select State') + '</option>';
        stateField.disabled = true;
    }
    
    if (cityField) {
        cityField.innerHTML = '<option value="">' + (translations?.select_city || 'Select City') + '</option>';
        cityField.disabled = true;
    }
    
    if (countryId) {
        loadStatesByCountry(countryId);
    }
}

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
            stateField.removeEventListener('change', handleReceiverStateChange);
            stateField.addEventListener('change', handleReceiverStateChange);
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

function loadSaudiArabiaStates() {
    const stateField = document.getElementById("state");
    if (!stateField) return;
    
    const saudiArabiaId = "65fd1a1c1fdbc094e3369b2a";
    
    fetch(`https://ghaya-express-staging-af597af07557.herokuapp.com/api/states?page=0&pageSize=100&orderColumn=createdAt&orderDirection=desc&countryId=${saudiArabiaId}`, {
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
            });
            
            stateField.disabled = false;
            stateField.removeEventListener('change', handleReceiverStateChange);
            stateField.addEventListener('change', handleReceiverStateChange);
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

function handleReceiverStateChange() {
    const stateId = this.value;
    const cityField = document.getElementById("city");
    
    if (cityField) {
        cityField.innerHTML = '<option value="">' + (translations?.select_city || 'Select City') + '</option>';
        cityField.disabled = true;
    }
    
    if (stateId) {
        loadReceiverCitiesForState(stateId);
    }
}

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

function resetReceiverFormForExisting() {
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

function testLoadCountries() {
    if (selectedMethod === "international") {
        loadCountries();
    } else {
        loadSaudiArabiaStates();
    }
}

function forceTestCountries() {
    if (currentStep !== 4) {
        alert("Please go to Step 4 (Receivers) first!");
        return;
    }
    
    const countryField = document.getElementById("country");
    const stateField = document.getElementById("state");
    const cityField = document.getElementById("city");
    
    if (!countryField || !stateField || !cityField) {
        alert("Location fields not found! Make sure you're on Step 4.");
        return;
    }
    
    countryField.innerHTML = '<option value="">Select Country</option>';
    countryField.innerHTML += '<option value="test1">Test Country 1</option>';
    countryField.innerHTML += '<option value="test2">Test Country 2</option>';
    countryField.disabled = false;
    
    stateField.innerHTML = '<option value="">Select State</option>';
    stateField.innerHTML += '<option value="test1">Test State 1</option>';
    stateField.innerHTML += '<option value="test2">Test State 2</option>';
    stateField.disabled = false;
    
    cityField.innerHTML = '<option value="">Select City</option>';
    cityField.innerHTML += '<option value="test1">Test City 1</option>';
    cityField.innerHTML += '<option value="test2">Test City 2</option>';
    cityField.disabled = false;
    
    alert("Test data loaded! Check the dropdowns - they should now have test options.");
    loadCountries();
}

function testSaudiArabiaStates() {
    if (currentStep !== 4) {
        alert("Please go to Step 4 (Receivers) first!");
        return;
    }
    
    const countryField = document.getElementById("country");
    const stateField = document.getElementById("state");
    
    if (!countryField || !stateField) {
        alert("Location fields not found! Make sure you're on Step 4.");
        return;
    }
    
    countryField.innerHTML = '<option value="65fd1a1c1fdbc094e3369b2a" selected>Saudi Arabia</option>';
    countryField.disabled = true;
    
    loadSaudiArabiaStates();
}

function loadReceiverStates() {
    // Implementation for loading receiver states
}

function loadReceiverCities() {
    // Implementation for loading receiver cities
}
