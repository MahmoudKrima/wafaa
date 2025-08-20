var firstUpload = new FileUploadWithPreview("myFirstImage");

let currentStep = 1;
let selectedCompany = null;
let selectedMethod = null;

function updateStepIndicator(step, completed = false) {
    const steps = document.querySelectorAll(".step");
    steps.forEach((s, index) => {
        const stepNumber = s.querySelector(".step-number");
        if (index + 1 < step) {
            stepNumber.className =
                "step-number bg-success text-white rounded-circle d-flex align-items-center justify-content-center";
        } else if (index + 1 === step) {
            stepNumber.className =
                "step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center";
        } else {
            stepNumber.className =
                "step-number bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center";
        }
        stepNumber.style.width = "40px";
        stepNumber.style.height = "40px";
        stepNumber.style.fontWeight = "bold";
    });
}

function showStep(step) {
    document.querySelectorAll(".step-content").forEach((s) => (s.style.display = "none"));
    const el = document.getElementById(`step-${step}`);
    if (el) el.style.display = "block";

    updateStepIndicator(step);

    const btnPrev = document.getElementById("btn-prev");
    const btnNext = document.getElementById("btn-next");

    if (btnPrev) btnPrev.style.display = step === 1 ? "none" : "inline-block";

    if (step === 5) {
        if (btnNext) btnNext.style.display = "none";
        const sc = document.getElementById("shipping_company_id");
        const sm = document.getElementById("shipping_method");
        if (sc && selectedCompany) sc.value = selectedCompany.id;
        if (sm && selectedMethod) sm.value = selectedMethod;
    } else {
        if (btnNext) btnNext.style.display = "inline-block";
    }

    if (step === 3) {
        loadUserCity();
    } else if (step === 4) {
        loadReceivers();
        loadReceiverCities();
    }
}

async function fetchShippingCompanies() {
    try {
        const response = await fetch(
            "https://ghaya-express-staging-af597af07557.herokuapp.com/api/shipping-companies?page=0&pageSize=50&orderColumn=createdAt&orderDirection=desc",
            {
                headers: {
                    accept: "*/*",
                    "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu",
                },
            }
        );

        const data = await response.json();
        if (data.results && data.results.length > 0) {
            const activeCompanies = data.results.filter((company) => company.isActive);
            displayCompanies(activeCompanies);
        } else {
            document.getElementById("companies-container").innerHTML =
                '<div class="alert alert-warning">No shipping companies found</div>';
        }
    } catch (error) {
        document.getElementById("companies-container").innerHTML =
            '<div class="alert alert-danger">Error loading shipping companies. Please try again.</div>';
    }
}

function displayCompanies(companies) {
    const container = document.getElementById("companies-container");

    if (!companies || companies.length === 0) {
        container.innerHTML =
            '<div class="alert alert-warning">No active shipping companies found</div>';
        return;
    }

    let companiesHTML = '<div class="row">';
    companies.forEach(function (company) {
        companiesHTML += '<div class="col-lg-3 col-md-4 col-sm-6 mb-3">';
        companiesHTML +=
            '<div class="card company-card h-100" data-company-id="' +
            company.id +
            '" onclick="selectCompany(this, \'' +
            company.id +
            '\')" style="cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease;">';
        companiesHTML += '<div class="card-body text-center p-3">';
        companiesHTML +=
            '<img src="' +
            (company.logoUrl || '') +
            '" alt="' +
            (company.name || '') +
            '" class="img-fluid mb-3" style="max-height: 80px; max-width: 120px;" onerror="this.src=\'https://via.placeholder.com/120x80?text=Logo\'">';
        companiesHTML += '<h6 class="card-title mb-0">' + (company.name || '') + "</h6>";
        companiesHTML += "</div>";
        companiesHTML += "</div>";
        companiesHTML += "</div>";
    });
    companiesHTML += "</div>";

    container.innerHTML = companiesHTML;
}

function selectCompany(card, companyId) {
    document.querySelectorAll(".company-card").forEach((c) => {
        c.style.borderColor = "transparent";
        c.style.backgroundColor = "";
    });

    card.style.borderColor = "#007bff";
    card.style.backgroundColor = "#f8f9fa";

    const companyData = Array.from(document.querySelectorAll(".company-card"))
        .find((c) => c.dataset.companyId === companyId);

    if (companyData) {
        selectedCompany = {
            id: companyId,
            name: (companyData.querySelector(".card-title") || {}).textContent || '',
            shippingMethods: ["local", "international"],
        };
    }

    const btnNext = document.getElementById("btn-next");
    if (btnNext) btnNext.disabled = false;

    updateStepIndicator(1, true);
}

function showMethodSelection() {
    if (!selectedCompany) return;

    const companyName = document.getElementById("selected-company-name");
    if (companyName) companyName.textContent = selectedCompany.name;

    const methodOptions = document.getElementById("method-options");

    let methodsHTML = "";

    methodsHTML += '<div class="col-lg-6 col-md-6 mb-3">';
    methodsHTML +=
        '<div class="card method-option h-100" onclick="selectMethod(this, \'local\')" style="cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease;">';
    methodsHTML += '<div class="card-body text-center">';
    methodsHTML += '<div class="mb-2" style="font-size: 2rem;">🏠</div>';
    methodsHTML += '<h6 class="card-title">' + translations.local + '</h6>';
    methodsHTML += '<p class="card-text text-muted">' + translations.local_delivery + '</p>';
    methodsHTML += "</div></div></div>";

    methodsHTML += '<div class="col-lg-6 col-md-6 mb-3">';
    methodsHTML +=
        '<div class="card method-option h-100" onclick="selectMethod(this, \'international\')" style="cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease;">';
    methodsHTML += '<div class="card-body text-center">';
    methodsHTML += '<div class="mb-2" style="font-size: 2rem;">🌍</div>';
    methodsHTML += '<h6 class="card-title">' + translations.international + '</h6>';
    methodsHTML += '<p class="card-text text-muted">' + translations.worldwide_shipping + '</p>';
    methodsHTML += "</div></div></div>";

    if (methodOptions) methodOptions.innerHTML = methodsHTML;
}

function selectMethod(card, method) {
    document.querySelectorAll(".method-option").forEach((c) => {
        c.style.borderColor = "transparent";
        c.style.backgroundColor = "";
    });

    card.style.borderColor = "#007bff";
    card.style.backgroundColor = "#f8f9fa";

    selectedMethod = method;

    const btnNext = document.getElementById("btn-next");
    if (btnNext) btnNext.disabled = false;

    updateStepIndicator(2, true);
}



async function loadUserCity() {
    const citySelect = document.getElementById("user_city");
    try {
        const countryInput = document.getElementById("user_country");
        const step3Element = document.getElementById("step-3");
        const userCityId = step3Element ? step3Element.dataset.userCityId : "";
            const currentLocale = (step3Element && step3Element.dataset.appLocale) || 'en';

        const cityResponse = await fetch(
            "https://ghaya-express-staging-af597af07557.herokuapp.com/api/cities",
            {
                headers: {
                    accept: "*/*",
                    "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu",
                },
            }
        );

        const cityData = await cityResponse.json();
        let cities = [];
        if (cityData && cityData.results && cityData.results.length > 0) {
            cities = cityData.results;
        } else if (cityData && Array.isArray(cityData)) {
            cities = cityData;
        }

        if (citySelect) {
            if (cities.length > 0) {
                citySelect.innerHTML = '<option value="">' + translations.select_city + '</option>';
                let userCityFound = false;

                cities.forEach((city) => {
                    const option = document.createElement("option");
                    option.value = city._id || city.id;
                    let cityName = '';
                    if (city.name && city.name.en && city.name.ar) {
                        cityName = currentLocale === 'ar' ? city.name.ar : city.name.en;
                    } else {
                        cityName = city.name || 'Unknown City';
                    }
                    option.textContent = cityName;

                    if (userCityId && (city._id === userCityId || city.id === userCityId)) {
                        option.selected = true;
                        userCityFound = true;
                        if (city.country) {
                            displayCountryInfo(city.country);
                        }
                    }
                    citySelect.appendChild(option);
                });

                if (!userCityFound && userCityId) {
                    const noteOption = document.createElement("option");
                    noteOption.value = userCityId;
                    noteOption.textContent = "User City (Not in API)";
                    noteOption.selected = true;
                    citySelect.appendChild(noteOption);
                }
            } else {
                citySelect.innerHTML = '<option value="">' + translations.no_cities_available + '</option>';
            }
        }
    } catch (error) {
        if (citySelect) citySelect.innerHTML = '<option value="">' + translations.error_loading_cities + '</option>';
    }
}

function displayCountryInfo(countryData) {
    const countryInput = document.getElementById("user_country");
    const step3Element = document.getElementById("step-3");

    if (countryInput && countryData) {
        const currentLocale = (step3Element && step3Element.dataset.appLocale) || 'en';

        let countryName = '';
        if (countryData.name && countryData.name.en && countryData.name.ar) {
            countryName = currentLocale === 'ar' ? countryData.name.ar : countryData.name.en;
        } else {
            countryName = (countryData.name && countryData.name.en) || countryData.name || 'Unknown Country';
        }
        countryInput.value = countryName;
    }
}

async function loadReceivers() {
    try {
        const receiverSelect = document.getElementById("receiver_select");

        const response = await fetch("/receivers", {
            headers: {
                accept: "*/*",
                "Content-Type": "application/json",
            },
        });

        const data = await response.json();

        if (receiverSelect) {
            if (data && data.length > 0) {
                receiverSelect.innerHTML = '<option value="">' + translations.choose_receiver + '</option>';

                data.forEach((receiver) => {
                    const option = document.createElement("option");
                    option.value = receiver.id;
                    option.textContent = receiver.name;
                    option.dataset.receiver = JSON.stringify(receiver);
                    receiverSelect.appendChild(option);
                });
            } else {
                receiverSelect.innerHTML = '<option value="">' + translations.no_receivers_found + '</option>';
            }
        }
    } catch (error) {
        console.error("Error loading receivers:", error);
        const el = document.getElementById("receiver_select");
        if (el) el.innerHTML = '<option value="">' + translations.error_loading_receivers + '</option>';
    }
}

async function loadReceiverCities() {
    const citySelect = document.getElementById("city");
    try {
        const step3Element = document.getElementById("step-3");
        const currentLocale = (step3Element && step3Element.dataset.appLocale) || 'en';
        
        const cityResponse = await fetch(
            "https://ghaya-express-staging-af597af07557.herokuapp.com/api/cities",
            {
                headers: {
                    accept: "*/*",
                    "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu",
                },
            }
        );

        const cityData = await cityResponse.json();

        let cities = [];
        if (cityData && cityData.results && cityData.results.length > 0) {
            cities = cityData.results;
        } else if (cityData && Array.isArray(cityData)) {
            cities = cityData;
        }

        if (citySelect) {
            if (cities.length > 0) {
                citySelect.innerHTML = '<option value="">' + translations.select_city + '</option>';

                cities.forEach((city) => {
                    const option = document.createElement("option");
                    option.value = city._id || city.id;

                    let cityName = '';
                    if (city.name && city.name.en && city.name.ar) {
                        cityName = currentLocale === 'ar' ? city.name.ar : city.name.en;
                    } else {
                        cityName = city.name || 'Unknown City';
                    }

                    option.textContent = cityName;
                    citySelect.appendChild(option);
                });
            } else {
                citySelect.innerHTML = '<option value="">' + translations.no_cities_available + '</option>';
            }
        }
    } catch (error) {
        console.error("Error loading receiver cities:", error);
        if (citySelect) citySelect.innerHTML = '<option value="">' + translations.error_loading_cities + '</option>';
    }
}

async function displayReceiverCountry(cityId) {
    try {
        const response = await fetch(
            "https://ghaya-express-staging-af597af07557.herokuapp.com/api/cities",
            {
                headers: {
                    accept: "*/*",
                    "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu",
                },
            }
        );
        
        const data = await response.json();
        let cities = [];
        if (data && data.results && data.results.length > 0) {
            cities = data.results;
        } else if (data && Array.isArray(data)) {
            cities = data;
        }
        
        const selectedCity = cities.find(city => (city._id === cityId || city.id === cityId));

        if (selectedCity && selectedCity.country) {
            const countryInput = document.getElementById("country");
            const step3Element = document.getElementById("step-3");
            const currentLocale = (step3Element && step3Element.dataset.appLocale) || 'en';

            let countryName = '';
            if (selectedCity.country.name && selectedCity.country.name.en && selectedCity.country.name.ar) {
                countryName = currentLocale === 'ar' ? selectedCity.country.name.ar : selectedCity.country.name.en;
            } else if (selectedCity.country.en && selectedCity.country.ar) {
                countryName = currentLocale === 'ar' ? selectedCity.country.ar : selectedCity.country.en;
            } else {
                countryName = selectedCity.country.name || selectedCity.country || 'Unknown Country';
            }

            if (countryInput) countryInput.value = countryName;
        }
    } catch (error) {
        console.error("Error displaying receiver country:", error);
    }
}

function populateReceiverForm(receiverId) {
    const receiverSelect = document.getElementById('receiver_select');
    const selectedOption = receiverSelect ? receiverSelect.querySelector(`option[value="${receiverId}"]`) : null;

    if (selectedOption && selectedOption.dataset.receiver) {
        const receiver = JSON.parse(selectedOption.dataset.receiver);

        // Fields
        const nameField = document.getElementById('name');
        const phoneField = document.getElementById('phone');
        const additional_phoneField = document.getElementById('additional_phone');
        const addressField = document.getElementById('address');
        const cityField = document.getElementById('city');
        const countryField = document.getElementById('country');
        const postal_codeField = document.getElementById('postal_code');
        const emailField = document.getElementById('email');

        // Values
        if (nameField) {
            nameField.value = receiver.name || '';
            nameField.disabled = true;
        }
        if (phoneField) {
            phoneField.value = receiver.phone || '';
            phoneField.disabled = true;
        }
        if (additional_phoneField) {
            additional_phoneField.value = receiver.additional_phone || '';
            additional_phoneField.disabled = false;
        }
        if (addressField) {
            addressField.value = receiver.address || '';
            addressField.disabled = false;
        }
        if (postal_codeField) {
            postal_codeField.value = receiver.postal_code || '';
            postal_codeField.disabled = false;
        }
        if (emailField) {
            emailField.value = receiver.email || '';
            emailField.disabled = false;
        }
        if (cityField && receiver.city && receiver.city.city_id) {
            cityField.value = receiver.city.city_id;
            cityField.disabled = false;
            if (countryField && receiver.city.country_name_en && receiver.city.country_name_ar) {
                const step3Element = document.getElementById('step-3');
                const currentLocale = (step3Element && step3Element.dataset.appLocale) || 'en';
                countryField.value = currentLocale === 'ar' ? receiver.city.country_name_ar : receiver.city.country_name_en;
                countryField.disabled = false;
            } else {
                displayReceiverCountry(receiver.city.city_id);
            }
        }
    } else {
        console.error('No receiver data found for ID:', receiverId);
    }
}

function clearReceiverForm() {
    const ids = [
        'name',
        'phone',
        'additional_phone',
        'city',
        'country',
        'address',
        'postal_code',
        'email'
    ];
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        if (el.tagName === 'SELECT') {
            el.value = '';
        } else {
            el.value = '';
        }
    });

    const nameField = document.getElementById('name');
    const phoneField = document.getElementById('phone');
    if (nameField) nameField.disabled = false;
    if (phoneField) phoneField.disabled = false;
}

function makeReceiverFormEditable() {
    const fields = [
        'name',
        'phone',
        'additional_phone',
        'city',
        'country',
        'address',
        'postal_code',
        'email'
    ];

    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.removeAttribute('readonly');
            field.removeAttribute('disabled');
            field.classList.add('form-control');
            field.style.display = 'block';
            field.style.visibility = 'visible';
            field.style.opacity = '1';
        }
    });
}

function setupReceiverTypeHandling() {
    const existingReceiverRadio = document.getElementById('existing_receiver');
    const newReceiverRadio = document.getElementById('new_receiver');
    const existingSection = document.getElementById('existing_receiver_section');
    const newSection = document.getElementById('new_receiver_section');

    if (existingSection) existingSection.style.display = 'block';
    if (newSection) newSection.style.display = 'block';

    if (existingReceiverRadio) {
        existingReceiverRadio.addEventListener('change', function () {
            if (this.checked) {
                if (existingSection) existingSection.style.display = 'block';
                if (newSection) newSection.style.display = 'block';
                const newReceiverLabel = document.querySelector('#new_receiver_section');
                if (newReceiverLabel) newReceiverLabel.style.display = 'block';
            }
        });
    }

    if (newReceiverRadio) {
        newReceiverRadio.addEventListener('change', function () {
            if (this.checked) {
                if (existingSection) existingSection.style.display = 'none';
                if (newSection) newSection.style.display = 'block';
                const newReceiverLabel = document.querySelector('#new_receiver_section');
                if (newReceiverLabel) newReceiverLabel.style.display = 'block';
                clearReceiverForm();
            }
        });
    }

    const receiverSelect = document.getElementById('receiver_select');
    if (receiverSelect) {
        receiverSelect.addEventListener('change', function () {
            const selectedReceiverId = this.value;
            if (selectedReceiverId) {
                populateReceiverForm(selectedReceiverId);
            } else {
                clearReceiverForm();
            }
        });
    }

    const receiverCitySelect = document.getElementById('city');
    if (receiverCitySelect) {
        receiverCitySelect.addEventListener('change', function () {
            const selectedCityId = this.value;
            if (selectedCityId) displayReceiverCountry(selectedCityId);
        });
    }
}

function testReceiverPopulation() {
    const receiverSelect = document.getElementById('receiver_select');
    const selectedOption = receiverSelect ? receiverSelect.querySelector('option:checked') : null;

    if (selectedOption && selectedOption.value) {
        if (selectedOption.dataset.receiver) {
            populateReceiverForm(selectedOption.value);
        } else {
            console.error('No receiver data in dataset');
        }
    } else {
        console.log('No receiver selected');
    }
}
document.addEventListener("DOMContentLoaded", function () {
    const btnNext = document.getElementById("btn-next");
    const btnPrev = document.getElementById("btn-prev");

    if (btnNext) {
        btnNext.addEventListener("click", function () {
            if (currentStep === 1 && selectedCompany) {
                currentStep = 2;
                showStep(currentStep);
                showMethodSelection();
            } else if (currentStep === 2 && selectedMethod) {
                currentStep = 3;
                showStep(currentStep);
            } else if (currentStep === 3) {
                currentStep = 4;
                showStep(currentStep);
            } else if (currentStep === 4) {
                currentStep = 5;
                showStep(currentStep);
            }
        });
    }

    if (btnPrev) {
        btnPrev.addEventListener("click", function () {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });
    }

    setupReceiverTypeHandling();
    fetchShippingCompanies();
});
