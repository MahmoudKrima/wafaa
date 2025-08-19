var firstUpload = new FileUploadWithPreview("myFirstImage");

let currentStep = 1;
let selectedCompany = null;
let selectedMethod = null;

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
            const activeCompanies = data.results.filter(
                (company) => company.isActive
            );
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
            company.logoUrl +
            '" alt="' +
            company.name +
            '" class="img-fluid mb-3" style="max-height: 80px; max-width: 120px;" onerror="this.src=\'https://via.placeholder.com/120x80?text=Logo\'">';
        companiesHTML +=
            '<h6 class="card-title mb-0">' + company.name + "</h6>";
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

    const companyData = Array.from(
        document.querySelectorAll(".company-card")
    ).find((c) => c.dataset.companyId === companyId);
    if (companyData) {
        selectedCompany = {
            id: companyId,
            name: companyData.querySelector(".card-title").textContent,
            shippingMethods: ["local", "international"],
        };
    }

    document.getElementById("btn-next").disabled = false;

    updateStepIndicator(1, true);
}

function showMethodSelection() {
    if (!selectedCompany) return;

    const companyName = document.getElementById("selected-company-name");
    companyName.textContent = selectedCompany.name;

    const methodOptions = document.getElementById("method-options");

    let methodsHTML = "";
    methodsHTML += '<div class="col-lg-6 col-md-6 mb-3">';
    methodsHTML +=
        '<div class="card method-option h-100" onclick="selectMethod(this, \'local\')" style="cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease;">';
    methodsHTML += '<div class="card-body text-center">';
    methodsHTML += '<div class="mb-2" style="font-size: 2rem;">🏠</div>';
    methodsHTML += '<h6 class="card-title">Local</h6>';
    methodsHTML +=
        '<p class="card-text text-muted">Local delivery within the country</p>';
    methodsHTML += "</div>";
    methodsHTML += "</div>";
    methodsHTML += "</div>";

    methodsHTML += '<div class="col-lg-6 col-md-6 mb-3">';
    methodsHTML +=
        '<div class="card method-option h-100" onclick="selectMethod(this, \'international\')" style="cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease;">';
    methodsHTML += '<div class="card-body text-center">';
    methodsHTML += '<div class="mb-2" style="font-size: 2rem;">🌍</div>';
    methodsHTML += '<h6 class="card-title">International</h6>';
    methodsHTML +=
        '<p class="card-text text-muted">Worldwide shipping to all countries</p>';
    methodsHTML += "</div>";
    methodsHTML += "</div>";
    methodsHTML += "</div>";

    methodOptions.innerHTML = methodsHTML;
}

function selectMethod(card, method) {
    document.querySelectorAll(".method-option").forEach((c) => {
        c.style.borderColor = "transparent";
        c.style.backgroundColor = "";
    });

    card.style.borderColor = "#007bff";
    card.style.backgroundColor = "#f8f9fa";

    selectedMethod = method;

    document.getElementById("btn-next").disabled = false;

    updateStepIndicator(2, true);
}

function updateStepIndicator(step, completed = false) {
    const steps = document.querySelectorAll(".step");
    steps.forEach((s, index) => {
        const stepNumber = s.querySelector(".step-number");
        if (index + 1 < step) {
            stepNumber.className =
                "step-number bg-success text-white rounded-circle d-flex align-items-center justify-content-center";
            stepNumber.style.width = "40px";
            stepNumber.style.height = "40px";
            stepNumber.style.fontWeight = "bold";
        } else if (index + 1 === step) {
            stepNumber.className =
                "step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center";
            stepNumber.style.width = "40px";
            stepNumber.style.height = "40px";
            stepNumber.style.fontWeight = "bold";
        } else {
            stepNumber.className =
                "step-number bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center";
            stepNumber.style.width = "40px";
            stepNumber.style.height = "40px";
            stepNumber.style.fontWeight = "bold";
        }
    });
}

function showStep(step) {
    document
        .querySelectorAll(".step-content")
        .forEach((s) => (s.style.display = "none"));

    document.getElementById(`step-${step}`).style.display = "block";

    updateStepIndicator(step);

    const btnPrev = document.getElementById("btn-prev");
    const btnNext = document.getElementById("btn-next");

    btnPrev.style.display = step === 1 ? "none" : "inline-block";

    if (step === 5) {
        btnNext.style.display = "none";
        document.getElementById("shipping_company_id").value =
            selectedCompany.id;
        document.getElementById("shipping_method").value = selectedMethod;
    } else {
        btnNext.style.display = "inline-block";
    }

    if (step === 3) {
        loadUserCity();
    } else if (step === 4) {
        loadReceivers();
        loadReceiverCities();
    }
}

async function loadUserCity() {
    try {
        const citySelect = document.getElementById("user_city");
        const countrySelect = document.getElementById("user_country");
        const step3Element = document.getElementById("step-3");
        const userCityId = step3Element.dataset.userCityId;
        
        
        // Load cities
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
        console.log(cityData);
        let cities = [];
        if (cityData && cityData.results && cityData.results.length > 0) {
            cities = cityData.results;
        } else if (cityData && Array.isArray(cityData)) {
            cities = cityData;
        }
        
        if (cities.length > 0) {
            citySelect.innerHTML = '<option value="">Select City</option>';
            let userCityFound = false;
            
            cities.forEach((city) => {
                console.log(city);
                const option = document.createElement("option");
                option.value = city._id || city.id;
                let cityName = '';
                if (city.name.en && city.name.ar) {
                    cityName = city.name.en;
                } else {
                    cityName = city.name || 'Unknown City';
                }
                
                option.textContent = cityName;
                console.log(userCityId);
                 
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
            citySelect.innerHTML = '<option value="">No cities available</option>';
        }
        
    } catch (error) {
        citySelect.innerHTML = '<option value="">Error loading cities</option>';
    }
}

function displayCountryInfo(countryData) {
    const countryInput = document.getElementById("user_country");
    const step3Element = document.getElementById("step-3");
    
    if (countryInput && countryData) {
        const currentLocale = step3Element.dataset.appLocale || 'en';
        
        let countryName = '';
        if (countryData.name.en && countryData.name.ar) {
            countryName = currentLocale === 'ar' ? countryData.name.ar : countryData.name.en;
        } else {
            countryName = countryData.name.en || 'Unknown Country';
        }
        countryInput.value = countryName;
    }
}

async function loadReceivers() {
    try {
        const receiverSelect = document.getElementById("receiver_select");
        
        // Fetch receivers from your API endpoint
        const response = await fetch(
            "/api/user/receivers", // You'll need to create this endpoint
            {
                headers: {
                    accept: "*/*",
                    "Content-Type": "application/json",
                },
            }
        );

        const data = await response.json();
        console.log('Receivers API response:', data);
        
        if (data && data.length > 0) {
            receiverSelect.innerHTML = '<option value="">Choose Receiver</option>';
            
            data.forEach((receiver) => {
                const option = document.createElement("option");
                option.value = receiver.id;
                option.textContent = receiver.name;
                receiverSelect.appendChild(option);
            });
        } else {
            receiverSelect.innerHTML = '<option value="">No receivers found</option>';
        }
    } catch (error) {
        console.error("Error loading receivers:", error);
        document.getElementById("receiver_select").innerHTML = '<option value="">Error loading receivers</option>';
    }
}

async function loadReceiverCities() {
    try {
        const citySelect = document.getElementById("receiver_city");
        
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
        console.log('Receiver Cities API response:', data);
        
        let cities = [];
        if (data && data.results && data.results.length > 0) {
            cities = data.results;
        } else if (data && Array.isArray(data)) {
            cities = data;
        }
        
        if (cities.length > 0) {
            citySelect.innerHTML = '<option value="">Select City</option>';
            
            cities.forEach((city) => {
                const option = document.createElement("option");
                option.value = city._id || city.id;
                
                let cityName = '';
                if (city.name && city.name.en && city.name.ar) {
                    cityName = city.name.en; // Default to English
                } else {
                    cityName = city.name || 'Unknown City';
                }
                
                option.textContent = cityName;
                citySelect.appendChild(option);
            });
        } else {
            citySelect.innerHTML = '<option value="">No cities available</option>';
        }
    } catch (error) {
        console.error("Error loading receiver cities:", error);
        document.getElementById("receiver_city").innerHTML = '<option value="">Error loading cities</option>';
    }
}

// Handle receiver type selection
function setupReceiverTypeHandling() {
    const existingReceiverRadio = document.getElementById('existing_receiver');
    const newReceiverRadio = document.getElementById('new_receiver');
    const existingSection = document.getElementById('existing_receiver_section');
    const newSection = document.getElementById('new_receiver_section');
    
    existingReceiverRadio.addEventListener('change', function() {
        if (this.checked) {
            existingSection.style.display = 'block';
            newSection.style.display = 'none';
        }
    });
    
    newReceiverRadio.addEventListener('change', function() {
        if (this.checked) {
            existingSection.style.display = 'none';
            newSection.style.display = 'block';
        }
    });
    
    // Handle city selection for new receiver
    const receiverCitySelect = document.getElementById('receiver_city');
    receiverCitySelect.addEventListener('change', function() {
        const selectedCityId = this.value;
        if (selectedCityId) {
            // Find the selected city and display its country
            displayReceiverCountry(selectedCityId);
        }
    });
}

function displayReceiverCountry(cityId) {
    // This function will be called when a city is selected for the new receiver
    // You can implement the logic to find the city and display its country
    console.log('Receiver city selected:', cityId);
    // TODO: Implement country display logic
}



document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("btn-next").addEventListener("click", function () {
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

    document.getElementById("btn-prev").addEventListener("click", function () {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });
    
    // Setup receiver type handling
    setupReceiverTypeHandling();
    
    fetchShippingCompanies();
});
