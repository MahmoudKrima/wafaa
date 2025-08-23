// Initialize file upload for shipment image
var shipmentImageUpload = new FileUploadWithPreview("shipmentImage");

let currentStep = 1;
let selectedCompany = null;
let selectedMethod = null;
let shippingCompaniesData = [];
let selectedReceivers = [];
let currentReceiverIndex = 0;
let shipmentId = `shipment_${Date.now()}`;

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
    document
        .querySelectorAll(".step-content")
        .forEach((s) => (s.style.display = "none"));
    const el = document.getElementById(`step-${step}`);
    if (el) el.style.display = "block";

    updateStepIndicator(step);

    const btnPrev = document.getElementById("btn-prev");
    const btnNext = document.getElementById("btn-next");

    if (btnPrev) btnPrev.style.display = step === 1 ? "none" : "inline-block";

    if (step === 5) {
        if (btnNext) btnNext.style.display = "inline-block";
        populateShippingFormFields(); // Populate form fields with shipping data
    } else if (step === 6) {
        if (btnNext) btnNext.style.display = "inline-block";
        setupPaymentDetails(); // Setup payment details
    } else if (step === 7) {
        if (btnNext) btnNext.style.display = "none";
        showFinalSummary(); // Show final summary
    } else {
        if (btnNext) btnNext.style.display = "inline-block";
    }

    if (step === 3) {
        loadUserCity();
    } else if (step === 4) {
        loadReceivers();
        loadReceiverCities();
    }

    // Clear step data when going back
    if (step < currentStep) {
        clearStepData(step);
    }
}

// Function to clear step data when going back
function clearStepData(step) {
    switch (step) {
        case 1:
            // Clear company selection
            selectedCompany = null;
            selectedMethod = null;
            break;
        case 2:
            // Clear method selection
            selectedMethod = null;
            break;
        case 3:
            // Clear user information (if any editable fields)
            break;
        case 4:
            // Clear receiver data
            selectedReceivers = [];
            currentReceiverIndex = 0;
            clearReceiverForm();
            resetReceiverTypeSelection();

            // Hide receiver containers
            const receiversContainer = document.getElementById(
                "receivers-container"
            );
            const successMsg = document.getElementById("receiver-success-msg");
            const actionButtons = document.getElementById(
                "receiver-action-buttons"
            );

            if (receiversContainer) receiversContainer.style.display = "none";
            if (successMsg) successMsg.style.display = "none";
            if (actionButtons) actionButtons.style.display = "none";

            // Disable next button
            const btnNext = document.getElementById("btn-next");
            if (btnNext) btnNext.disabled = true;

            // Refresh receivers list after clearing
            loadReceivers();
            break;
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
            shippingCompaniesData = data.results;
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
            (company.logoUrl || "") +
            '" alt="' +
            (company.name || "") +
            '" class="img-fluid mb-3" style="max-height: 80px; max-width: 120px;" onerror="this.src=\'https://via.placeholder.com/120x80?text=Logo\'">';
        companiesHTML +=
            '<h6 class="card-title mb-0">' + (company.name || "") + "</h6>";
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

    const companyData = shippingCompaniesData.find(
        (company) => company.id === companyId
    );

    if (companyData) {
        selectedCompany = {
            id: companyData.id,
            name: companyData.name,
            serviceName: companyData.serviceName,
            localPrice: companyData.localPrice,
            codPrice: companyData.codPrice,
            maxWeight: companyData.maxWeight,
            extraWeightPrice: companyData.extraWeightPrice,
            hasState: companyData.hasState,
            isEnglish: companyData.isEnglish,
            color: companyData.color,
            logoUrl: companyData.logoUrl,
            iconUrl: companyData.iconUrl,
            shippingMethods: companyData.shippingMethods.filter(
                (method) =>
                    method !== "cashOnDelivery" &&
                    (method === "local" || method === "international")
            ),
            returnFees: companyData.returnFees,
            fuelPercentage: companyData.fuelPercentage,
            shipmentFees: companyData.shipmentFees,
            shipmentCodFees: companyData.shipmentCodFees,
            shipmentExtraWeightFees: companyData.shipmentExtraWeightFees,
            shipmentReturnFees: companyData.shipmentReturnFees,
            isAuthorizationRequired: companyData.isAuthorizationRequired,
        };

        console.log("Selected company data:", selectedCompany);

        // Display company pricing information
        displayCompanyPricing();
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

    if (selectedCompany.shippingMethods.includes("local")) {
        methodsHTML += '<div class="col-lg-6 col-md-6 mb-3">';
        methodsHTML +=
            '<div class="card method-option h-100" onclick="selectMethod(this, \'local\')" style="cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease;">';
        methodsHTML += '<div class="card-body text-center">';
        methodsHTML += '<div class="mb-2" style="font-size: 2rem;">🏠</div>';
        methodsHTML += '<h6 class="card-title">' + translations.local + "</h6>";
        methodsHTML +=
            '<p class="card-text text-muted">' +
            translations.local_delivery +
            "</p>";
        methodsHTML += "</div></div></div>";
    }

    if (selectedCompany.shippingMethods.includes("international")) {
        methodsHTML += '<div class="col-lg-6 col-md-6 mb-3">';
        methodsHTML +=
            '<div class="card method-option h-100" onclick="selectMethod(this, \'international\')" style="cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease;">';
        methodsHTML += '<div class="card-body text-center">';
        methodsHTML += '<div class="mb-2" style="font-size: 2rem;">🌍</div>';
        methodsHTML +=
            '<h6 class="card-title">' + translations.international + "</h6>";
        methodsHTML +=
            '<p class="card-text text-muted">' +
            translations.worldwide_shipping +
            "</p>";
        methodsHTML += "</div></div></div>";
    }

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
        const currentLocale =
            (step3Element && step3Element.dataset.appLocale) || "en";

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
                citySelect.innerHTML =
                    '<option value="">' +
                    translations.select_city +
                    "</option>";
                let userCityFound = false;

                cities.forEach((city) => {
                    const option = document.createElement("option");
                    option.value = city._id || city.id;
                    let cityName = "";
                    if (city.name && city.name.en && city.name.ar) {
                        cityName =
                            currentLocale === "ar"
                                ? city.name.ar
                                : city.name.en;
                    } else {
                        cityName = city.name || "Unknown City";
                    }
                    option.textContent = cityName;

                    if (
                        userCityId &&
                        (city._id === userCityId || city.id === userCityId)
                    ) {
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
                citySelect.innerHTML =
                    '<option value="">' +
                    translations.no_cities_available +
                    "</option>";
            }
        }
    } catch (error) {
        if (citySelect)
            citySelect.innerHTML =
                '<option value="">' +
                translations.error_loading_cities +
                "</option>";
    }
}

function displayCountryInfo(countryData) {
    const countryInput = document.getElementById("user_country");
    const step3Element = document.getElementById("step-3");

    if (countryInput && countryData) {
        const currentLocale =
            (step3Element && step3Element.dataset.appLocale) || "en";

        let countryName = "";
        if (countryData.name && countryData.name.en && countryData.name.ar) {
            countryName =
                currentLocale === "ar"
                    ? countryData.name.ar
                    : countryData.name.en;
        } else {
            countryName =
                (countryData.name && countryData.name.en) ||
                countryData.name ||
                "Unknown Country";
        }
        countryInput.value = countryName;
    }
}

async function loadReceivers() {
    try {
        const receiverSelect = document.getElementById("receiver_select");
        const receiversContainer = document.getElementById(
            "receivers-container"
        );

        const response = await fetch("/receivers", {
            headers: {
                accept: "*/*",
                "Content-Type": "application/json",
            },
        });

        const data = await response.json();

        if (receiverSelect) {
            if (data && data.length > 0) {
                receiverSelect.innerHTML =
                    '<option value="">' +
                    translations.choose_receiver +
                    "</option>";

                // Filter out already selected receivers
                const availableReceivers = data.filter(
                    (receiver) =>
                        !selectedReceivers.some(
                            (selected) => selected.id === receiver.id
                        )
                );

                availableReceivers.forEach((receiver) => {
                    const option = document.createElement("option");
                    option.value = receiver.id;
                    option.textContent = receiver.name;
                    option.dataset.receiver = JSON.stringify(receiver);
                    receiverSelect.appendChild(option);
                });
            } else {
                receiverSelect.innerHTML =
                    '<option value="">' +
                    translations.no_receivers_found +
                    "</option>";
            }
        }

        displaySelectedReceivers();
    } catch (error) {
        console.error("Error loading receivers:", error);
        const el = document.getElementById("receiver_select");
        if (el)
            el.innerHTML =
                '<option value="">' +
                translations.error_loading_receivers +
                "</option>";
    }
}

function displaySelectedReceivers() {
    const receiversContainer = document.getElementById("receivers-container");
    if (!receiversContainer) return;

    if (selectedReceivers.length === 0) {
        receiversContainer.innerHTML =
            '<div class="alert alert-info">No receivers added yet. Add receivers to continue.</div>';
        return;
    }

    let receiversHTML = `
        <div class="alert alert-info mb-3">
            <h6 class="mb-2"><i class="fas fa-shipping-fast"></i> Shipment Receivers Summary</h6>
            <p class="mb-2">Total Receivers: <strong>${selectedReceivers.length}</strong></p>
            <p class="mb-0">Status: <span class="badge bg-primary">In Progress</span></p>
        </div>
    `;

    receiversHTML += '<div class="row">';
    selectedReceivers.forEach((receiver, index) => {
        receiversHTML += `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card receiver-card" data-receiver-index="${index}">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-user"></i> Receiver ${
                            index + 1
                        }</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> ${receiver.name || "N/A"}</p>
                        <p><strong>Phone:</strong> ${
                            receiver.phone || "N/A"
                        }</p>
                        <p><strong>City:</strong> ${
                            receiver.cityName || "N/A"
                        }</p>
                        <p><strong>Country:</strong> ${
                            receiver.countryName || "N/A"
                        }</p>
                        ${
                            receiver.address
                                ? `<p><strong>Address:</strong> ${receiver.address}</p>`
                                : ""
                        }
                        ${
                            receiver.email
                                ? `<p><strong>Email:</strong> ${receiver.email}</p>`
                                : ""
                        }
                        <div class="btn-group btn-group-sm w-100 mt-2">
                            <button type="button" class="btn btn-outline-primary" onclick="editReceiver(${index})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="removeReceiver(${index})">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    receiversHTML += "</div>";

    // Add action buttons
    receiversHTML += `
        <div class="row mt-3">
            <div class="col-12 text-center">
                <div class="btn-group">
                    <button type="button" id="add-another-receiver-btn" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Another Receiver
                    </button>
                    <button type="button" id="finish-receivers-btn" class="btn btn-success">
                        <i class="fas fa-check"></i> Finish Adding Receivers (${selectedReceivers.length})
                    </button>
                </div>
            </div>
        </div>
    `;

    receiversContainer.innerHTML = receiversHTML;

    // Setup event listeners for the new buttons
    setupReceiverButtons();
}

// Simple function to setup receiver buttons
function setupReceiverButtons() {
    const addAnotherBtn = document.getElementById("add-another-receiver-btn");
    const finishBtn = document.getElementById("finish-receivers-btn");

    if (addAnotherBtn) {
        addAnotherBtn.onclick = function () {
            clearReceiverForm();
            const newSection = document.getElementById("new_receiver_section");
            if (newSection) newSection.style.display = "block";
            newSection?.scrollIntoView({ behavior: "smooth" });

            // Hide success message
            const successMsg = document.getElementById("receiver-success-msg");
            if (successMsg) successMsg.style.display = "none";
        };
    }

    if (finishBtn) {
        finishBtn.onclick = function () {
            if (selectedReceivers.length > 0) {
                // Enable next step button
                const btnNext = document.getElementById("btn-next");
                if (btnNext) btnNext.disabled = false;

                // Show success message
                const successMsg = document.getElementById(
                    "receiver-success-msg"
                );
                if (successMsg) {
                    successMsg.innerHTML = `
                        <div class="alert alert-success">
                            <strong>All receivers added successfully!</strong>
                            <p class="mb-0">You have added ${selectedReceivers.length} receiver(s) to this shipment. You can now proceed to the next step.</p>
                        </div>
                    `;
                    successMsg.style.display = "block";
                }

                // Scroll to next step button
                const nextBtn = document.getElementById("btn-next");
                if (nextBtn) nextBtn.scrollIntoView({ behavior: "smooth" });
            }
        };
    }
}

function getShipmentSummary() {
    if (selectedReceivers.length === 0) return null;

    return {
        totalReceivers: selectedReceivers.length,
        shipmentId: `shipment_${Date.now()}`,
        receivers: selectedReceivers,
        status: "pending",
        createdAt: new Date().toISOString(),
        shippingCompany: selectedCompany?.name || "Not Selected",
        shippingMethod: selectedMethod || "Not Selected",
    };
}

function validateShipmentCompleteness() {
    if (selectedReceivers.length === 0) {
        return {
            isValid: false,
            message: "At least one receiver must be added to the shipment",
        };
    }

    if (!selectedCompany) {
        return { isValid: false, message: "Shipping company must be selected" };
    }

    if (!selectedMethod) {
        return { isValid: false, message: "Shipping method must be selected" };
    }

    for (let i = 0; i < selectedReceivers.length; i++) {
        const receiver = selectedReceivers[i];
        if (
            !receiver.name ||
            !receiver.phone ||
            !receiver.city ||
            !receiver.country
        ) {
            return {
                isValid: false,
                message: `Receiver ${
                    i + 1
                } is missing required fields (Name, Phone, City, Country)`,
            };
        }
    }

    return {
        isValid: true,
        message: "Shipment is complete and ready for submission",
    };
}

function canProceedToNextStep() {
    const validation = validateShipmentCompleteness();
    if (!validation.isValid) {
        showError(validation.message);
        return false;
    }
    return true;
}

function addReceiver() {
    const name = document.getElementById("name")?.value;
    const phone = document.getElementById("phone")?.value;
    const city = document.getElementById("city")?.value;
    const country = document.getElementById("country")?.value;
    const address = document.getElementById("address")?.value;
    const additionalPhone = document.getElementById("additional_phone")?.value;
    const email = document.getElementById("email")?.value;

    // Required fields validation
    if (!name || !phone || !city || !country || !address) {
        showError(
            "Please fill in all required fields: Name, Phone, City, Country, and Address"
        );
        return;
    }

    // Phone format validation (05 + 8 digits)
    const phoneRegex = /^05\d{8}$/;
    if (!phoneRegex.test(phone)) {
        showError(
            "Phone must start with 05 followed by 8 digits (e.g., 0512345678)"
        );
        return;
    }

    // Additional phone validation (if provided)
    if (additionalPhone && !phoneRegex.test(additionalPhone)) {
        showError(
            "Additional phone must start with 05 followed by 8 digits (e.g., 0512345678)"
        );
        return;
    }

    // Email validation (if provided)
    if (email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError("Please enter a valid email address");
            return;
        }
    }

    // Check if this combination already exists in user's saved receivers
    const existingReceivers = Array.from(
        document.querySelectorAll("#receiver_select option")
    );
    const isDuplicateInSaved = existingReceivers.some((option) => {
        if (!option.dataset.receiver) return false;
        try {
            const savedReceiver = JSON.parse(option.dataset.receiver);
            return savedReceiver.name === name && savedReceiver.phone === phone;
        } catch (e) {
            return false;
        }
    });

    if (isDuplicateInSaved) {
        showError(
            "A receiver with this name and phone already exists in your saved receivers. Please use the existing receiver instead."
        );
        return;
    }

    // Get city and country names
    const citySelect = document.getElementById("city");
    const cityOption = citySelect?.querySelector(`option[value="${city}"]`);
    const cityName = cityOption?.textContent || city;

    const countryInput = document.getElementById("country");
    const countryName = countryInput?.value || country;

    // Create receiver object
    const newReceiver = {
        id: `new_${Date.now()}`,
        name: name,
        phone: phone,
        additional_phone:
            document.getElementById("additional_phone")?.value || "",
        address: document.getElementById("address")?.value || "",
        city: city,
        cityName: cityName,
        country: country,
        countryName: countryName,
        postal_code: document.getElementById("postal_code")?.value || "",
        email: document.getElementById("email")?.value || "",
        isNew: true,
        shipmentId: shipmentId, // Use the global shipment ID
    };

    // Add to selected receivers
    selectedReceivers.push(newReceiver);

    // Update display
    displaySelectedReceivers();

    // Show success message
    showSuccess(
        `Receiver "${name}" added successfully! You now have ${selectedReceivers.length} receiver(s) in this shipment.`
    );

    // Reset form and hide sections for next receiver
    clearReceiverForm();
    resetReceiverTypeSelection();

    // Refresh the receivers list to hide already selected ones
    loadReceivers();

    // Enable next step button if at least one receiver is added
    const btnNext = document.getElementById("btn-next");
    if (btnNext && selectedReceivers.length > 0) {
        btnNext.disabled = false;
    }
}

function editReceiver(index) {
    if (index < 0 || index >= selectedReceivers.length) return;

    currentReceiverIndex = index;
    const receiver = selectedReceivers[index];

    const nameField = document.getElementById("name");
    const phoneField = document.getElementById("phone");
    const additionalPhoneField = document.getElementById("additional_phone");
    const addressField = document.getElementById("address");
    const cityField = document.getElementById("city");
    const countryField = document.getElementById("country");
    const postalCodeField = document.getElementById("postal_code");
    const emailField = document.getElementById("email");

    if (nameField) nameField.value = receiver.name || "";
    if (phoneField) phoneField.value = receiver.phone || "";
    if (additionalPhoneField)
        additionalPhoneField.value = receiver.additional_phone || "";
    if (addressField) addressField.value = receiver.address || "";
    if (cityField) cityField.value = receiver.city || "";
    if (countryField) countryField.value = receiver.country || "";
    if (postalCodeField) postalCodeField.value = receiver.postal_code || "";
    if (emailField) emailField.value = receiver.email || "";

    displaySelectedReceivers();

    const formSection = document.getElementById("new_receiver_section");
    if (formSection) {
        formSection.scrollIntoView({ behavior: "smooth" });
    }
}

function removeReceiver(index) {
    if (index < 0 || index >= selectedReceivers.length) return;

    const receiver = selectedReceivers[index];

    // Remove receiver without confirmation
    selectedReceivers.splice(index, 1);

    // Show success message
    showSuccess(`Receiver "${receiver.name}" removed successfully`);

    if (currentReceiverIndex >= selectedReceivers.length) {
        currentReceiverIndex = Math.max(0, selectedReceivers.length - 1);
    }

    displaySelectedReceivers();

    if (selectedReceivers.length === 0) {
        clearReceiverForm();
    }

    // Refresh the receivers list to show removed receiver again
    loadReceivers();
}

function addExistingReceiver() {
    const receiverSelect = document.getElementById("receiver_select");
    const selectedReceiverId = receiverSelect?.value;

    // Simple validation - just check if a receiver is selected
    if (!selectedReceiverId) {
        showError("Please select a receiver from the list");
        return;
    }

    // Get receiver data
    const selectedOption = receiverSelect.querySelector(
        `option[value="${selectedReceiverId}"]`
    );
    if (!selectedOption || !selectedOption.dataset.receiver) {
        return; // Don't show alert, just return silently
    }

    const receiverData = JSON.parse(selectedOption.dataset.receiver);

    // Create receiver object - use current form values in case user edited them
    const newReceiver = {
        id: receiverData.id,
        name: document.getElementById("name")?.value || receiverData.name,
        phone: document.getElementById("phone")?.value || receiverData.phone,
        additional_phone:
            document.getElementById("additional_phone")?.value ||
            receiverData.additional_phone ||
            "",
        address:
            document.getElementById("address")?.value ||
            receiverData.address ||
            "",
        city:
            document.getElementById("city")?.value ||
            receiverData.city?.city_id ||
            "",
        cityName:
            document.getElementById("city")?.selectedOptions?.[0]?.text ||
            receiverData.city?.name?.en ||
            receiverData.city?.name?.ar ||
            "Unknown City",
        country:
            document.getElementById("country")?.value ||
            receiverData.city?.country_name_en ||
            receiverData.city?.country_name_ar ||
            "",
        countryName:
            document.getElementById("country")?.value ||
            receiverData.city?.country_name_en ||
            receiverData.city?.country_name_ar ||
            "Unknown Country",
        postal_code:
            document.getElementById("postal_code")?.value ||
            receiverData.postal_code ||
            "",
        email:
            document.getElementById("email")?.value || receiverData.email || "",
        isNew: false,
        shipmentId: shipmentId,
    };

    selectedReceivers.push(newReceiver);

    receiverSelect.value = "";

    displaySelectedReceivers();

    showSuccess(
        `Receiver "${newReceiver.name}" added from existing list! You now have ${selectedReceivers.length} receiver(s) in this shipment.`
    );

    resetReceiverTypeSelection();

    // Refresh the receivers list to hide already selected ones
    loadReceivers();

    const btnNext = document.getElementById("btn-next");
    if (btnNext && selectedReceivers.length > 0) {
        btnNext.disabled = false;
    }
}

async function loadReceiverCities() {
    const citySelect = document.getElementById("city");
    try {
        const step3Element = document.getElementById("step-3");
        const currentLocale =
            (step3Element && step3Element.dataset.appLocale) || "en";

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
                citySelect.innerHTML =
                    '<option value="">' +
                    translations.select_city +
                    "</option>";

                cities.forEach((city) => {
                    const option = document.createElement("option");
                    option.value = city._id || city.id;

                    let cityName = "";
                    if (city.name && city.name.en && city.name.ar) {
                        cityName =
                            currentLocale === "ar"
                                ? city.name.ar
                                : city.name.en;
                    } else {
                        cityName = city.name || "Unknown City";
                    }

                    option.textContent = cityName;
                    citySelect.appendChild(option);
                });
            } else {
                citySelect.innerHTML =
                    '<option value="">' +
                    translations.no_cities_available +
                    "</option>";
            }
        }
    } catch (error) {
        console.error("Error loading receiver cities:", error);
        if (citySelect)
            citySelect.innerHTML =
                '<option value="">' +
                translations.error_loading_cities +
                "</option>";
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

        const selectedCity = cities.find(
            (city) => city._id === cityId || city.id === cityId
        );

        if (selectedCity && selectedCity.country) {
            const countryInput = document.getElementById("country");
            const step3Element = document.getElementById("step-3");
            const currentLocale =
                (step3Element && step3Element.dataset.appLocale) || "en";

            let countryName = "";
            if (
                selectedCity.country.name &&
                selectedCity.country.name.en &&
                selectedCity.country.name.ar
            ) {
                countryName =
                    currentLocale === "ar"
                        ? selectedCity.country.name.ar
                        : selectedCity.country.name.en;
            } else if (selectedCity.country.en && selectedCity.country.ar) {
                countryName =
                    currentLocale === "ar"
                        ? selectedCity.country.ar
                        : selectedCity.country.en;
            } else {
                countryName =
                    selectedCity.country.name ||
                    selectedCity.country ||
                    "Unknown Country";
            }

            if (countryInput) countryInput.value = countryName;
        }
    } catch (error) {}
}

function populateReceiverForm(receiverId) {
    const receiverSelect = document.getElementById("receiver_select");
    const selectedOption = receiverSelect
        ? receiverSelect.querySelector(`option[value="${receiverId}"]`)
        : null;

    if (selectedOption && selectedOption.dataset.receiver) {
        const receiver = JSON.parse(selectedOption.dataset.receiver);

        const nameField = document.getElementById("name");
        const phoneField = document.getElementById("phone");
        const additional_phoneField =
            document.getElementById("additional_phone");
        const addressField = document.getElementById("address");
        const cityField = document.getElementById("city");
        const countryField = document.getElementById("country");
        const postal_codeField = document.getElementById("postal_code");
        const emailField = document.getElementById("email");

        // Values - make all fields editable for existing receivers
        if (nameField) {
            nameField.value = receiver.name || "";
            nameField.disabled = false;
        }
        if (phoneField) {
            phoneField.value = receiver.phone || "";
            phoneField.disabled = false;
        }
        if (additional_phoneField) {
            additional_phoneField.value = receiver.additional_phone || "";
            additional_phoneField.disabled = false;
        }
        if (addressField) {
            addressField.value = receiver.address || "";
            addressField.disabled = false;
        }
        if (postal_codeField) {
            postal_codeField.value = receiver.postal_code || "";
            postal_codeField.disabled = false;
        }
        if (emailField) {
            emailField.value = receiver.email || "";
            emailField.disabled = false;
        }
        if (cityField && receiver.city && receiver.city.city_id) {
            cityField.value = receiver.city.city_id;
            cityField.disabled = false;
            if (
                countryField &&
                receiver.city.country_name_en &&
                receiver.city.country_name_ar
            ) {
                const step3Element = document.getElementById("step-3");
                const currentLocale =
                    (step3Element && step3Element.dataset.appLocale) || "en";
                countryField.value =
                    currentLocale === "ar"
                        ? receiver.city.country_name_ar
                        : receiver.city.country_name_en;
                countryField.disabled = false;
            } else {
                displayReceiverCountry(receiver.city.city_id);
            }
        }
    } else {
        showError("No receiver data found. Please try selecting again.");
    }
}

function clearReceiverForm() {
    const ids = [
        "name",
        "phone",
        "additional_phone",
        "city",
        "country",
        "address",
        "postal_code",
        "email",
    ];
    ids.forEach((id) => {
        const el = document.getElementById(id);
        if (!el) return;
        if (el.tagName === "SELECT") {
            el.value = "";
        } else {
            el.value = "";
        }
    });

    const nameField = document.getElementById("name");
    const phoneField = document.getElementById("phone");
    if (nameField) nameField.disabled = false;
    if (phoneField) phoneField.disabled = false;
}

function makeReceiverFormEditable() {
    const fields = [
        "name",
        "phone",
        "additional_phone",
        "city",
        "country",
        "address",
        "postal_code",
        "email",
    ];

    fields.forEach((fieldId) => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.removeAttribute("readonly");
            field.removeAttribute("disabled");
            field.classList.add("form-control");
            field.style.display = "block";
            field.style.visibility = "visible";
            field.style.opacity = "1";
        }
    });
}

function setupReceiverTypeHandling() {
    const existingReceiverRadio = document.getElementById("existing_receiver");
    const newReceiverRadio = document.getElementById("new_receiver");
    const existingSection = document.getElementById(
        "existing_receiver_section"
    );
    const newSection = document.getElementById("new_receiver_section");

    // Initially hide both sections - user must choose
    if (existingSection) existingSection.style.display = "none";
    if (newSection) newSection.style.display = "none";

    if (existingReceiverRadio) {
        existingReceiverRadio.addEventListener("change", function () {
            if (this.checked) {
                if (existingSection) existingSection.style.display = "block";
                if (newSection) newSection.style.display = "none";
                clearReceiverForm();

                // Show multiple receiver controls
                showMultipleReceiverControls();
            }
        });
    }

    if (newReceiverRadio) {
        newReceiverRadio.addEventListener("change", function () {
            if (this.checked) {
                if (existingSection) existingSection.style.display = "none";
                if (newSection) newSection.style.display = "block";
                clearReceiverForm();

                // Show multiple receiver controls
                showMultipleReceiverControls();
            }
        });
    }

    const receiverSelect = document.getElementById("receiver_select");
    if (receiverSelect) {
        receiverSelect.addEventListener("change", function () {
            const selectedReceiverId = this.value;
            if (selectedReceiverId) {
                populateReceiverForm(selectedReceiverId);
                // Show the new receiver section to display the populated data
                const newSection = document.getElementById(
                    "new_receiver_section"
                );
                if (newSection) newSection.style.display = "block";
            } else {
                clearReceiverForm();
            }
        });
    }

    const receiverCitySelect = document.getElementById("city");
    if (receiverCitySelect) {
        receiverCitySelect.addEventListener("change", function () {
            const selectedCityId = this.value;
            if (selectedCityId) displayReceiverCountry(selectedCityId);
        });
    }
}

// Function to show multiple receiver controls
function showMultipleReceiverControls() {
    // Show action buttons for adding receivers
    const actionButtons = document.getElementById("receiver-action-buttons");
    if (actionButtons) {
        actionButtons.style.display = "block";
    }

    // Show the receivers container if it exists
    const receiversContainer = document.getElementById("receivers-container");
    if (receiversContainer) {
        receiversContainer.style.display = "block";
    }

    // Show success message container
    const successMsg = document.getElementById("receiver-success-msg");
    if (successMsg) {
        successMsg.style.display = "block";
    }

    // Update the display to show current state
    displaySelectedReceivers();
}

// Function to show error messages
function showError(message) {
    // Try to use toastr if available
    if (typeof toastr !== "undefined") {
        toastr.error(message);
    } else {
        // Fallback: show error below the form
        const errorContainer = document.getElementById("receiver-error-msg");
        if (errorContainer) {
            errorContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> ${message}
                </div>
            `;
            errorContainer.style.display = "block";

            // Auto-hide after 5 seconds
            setTimeout(() => {
                errorContainer.style.display = "none";
            }, 5000);
        }
    }
}

// Function to show success messages
function showSuccess(message) {
    // Try to use toastr if available
    if (typeof toastr !== "undefined") {
        toastr.success(message);
    } else {
        // Fallback: show success below the form
        const successContainer = document.getElementById(
            "receiver-success-msg"
        );
        if (successContainer) {
            successContainer.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> ${message}
                </div>
            `;
            successContainer.style.display = "block";

            // Auto-hide after 3 seconds
            setTimeout(() => {
                successContainer.style.display = "none";
            }, 3000);
        }
    }
}

// Function to reset receiver type selection
function resetReceiverTypeSelection() {
    const existingReceiverRadio = document.getElementById("existing_receiver");
    const newReceiverRadio = document.getElementById("new_receiver");
    const existingSection = document.getElementById(
        "existing_receiver_section"
    );
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

// Simple function to setup action buttons
function setupActionButtons() {
    const addReceiverBtn = document.getElementById("add-receiver-btn");
    const addExistingReceiverBtn = document.getElementById(
        "add-existing-receiver-btn"
    );

    if (addReceiverBtn) {
        addReceiverBtn.onclick = addReceiver;
    }

    if (addExistingReceiverBtn) {
        addExistingReceiverBtn.onclick = addExistingReceiver;
    }
}

// Function to get all selected receivers data
function getAllReceiversData() {
    return selectedReceivers.map((receiver) => ({
        id: receiver.id,
        name: receiver.name,
        phone: receiver.phone,
        additional_phone: receiver.additional_phone,
        address: receiver.address,
        city: receiver.city,
        cityName: receiver.cityName,
        country: receiver.country,
        countryName: receiver.countryName,
        postal_code: receiver.postal_code,
        email: receiver.email,
        isNew: receiver.isNew,
    }));
}

function testReceiverPopulation() {
    const receiverSelect = document.getElementById("receiver_select");
    const selectedOption = receiverSelect
        ? receiverSelect.querySelector("option:checked")
        : null;

    if (selectedOption && selectedOption.value) {
        if (selectedOption.dataset.receiver) {
            populateReceiverForm(selectedOption.value);
        } else {
            showError("No receiver data in dataset");
        }
    } else {
        showError("No receiver selected");
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
                setupLocationFields(); // Setup location fields based on selected company
            } else if (currentStep === 3) {
                currentStep = 4;
                showStep(currentStep);
                displayCompanySummary(); // Display company summary information
            } else if (currentStep === 4) {
                // Validate shipment completeness before proceeding
                if (canProceedToNextStep()) {
                    currentStep = 5;
                    showStep(currentStep);
                    populateShippingFormFields(); // Populate form fields with shipping data
                } else {
                    // Stay on current step if validation fails
                    return;
                }
            } else if (currentStep === 5) {
                // Validate package details before proceeding to payment
                if (validatePackageDetails()) {
                    currentStep = 6;
                    showStep(currentStep);
                    setupPaymentDetails(); // Setup payment details
                } else {
                    // Stay on current step if validation fails
                    return;
                }
            } else if (currentStep === 6) {
                // Proceed to final summary
                currentStep = 7;
                showStep(currentStep);
                showFinalSummary(); // Show final summary
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

    initializeMultipleReceivers();
});

function initializeMultipleReceivers() {
    selectedReceivers = [];
    currentReceiverIndex = 0;

    // Hide all receiver-related containers initially
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

    // Setup action button event listeners
    setupActionButtons();

    // Load initial receivers list
    loadReceivers();

    // Disable next button until receivers are added
    const btnNext = document.getElementById("btn-next");
    if (btnNext) {
        btnNext.disabled = true;
    }
}

// Package Type Handling Functions
function setupPackageTypeHandling() {
    // Get package type select dropdown
    const packageTypeSelect = document.getElementById("package_type");

    if (!packageTypeSelect) return;

    // Add event listener for package type selection
    packageTypeSelect.addEventListener("change", function () {
        const selectedValue = this.value;
        if (selectedValue === "boxes") {
            // Show dimensions section for boxes
            showDimensionsSection();
        } else if (selectedValue === "documents") {
            // Hide dimensions section for documents
            hideDimensionsSection();
        }
    });
}

function showDimensionsSection() {
    const dimensionsSection = document.getElementById("dimensions_section");
    if (dimensionsSection) {
        dimensionsSection.style.display = "block";

        // Make dimension fields required for boxes
        const lengthField = document.getElementById("length");
        const widthField = document.getElementById("width");
        const heightField = document.getElementById("height");

        if (lengthField) lengthField.required = true;
        if (widthField) widthField.required = true;
        if (heightField) heightField.required = true;
    }
}

function hideDimensionsSection() {
    const dimensionsSection = document.getElementById("dimensions_section");
    if (dimensionsSection) {
        dimensionsSection.style.display = "none";

        // Remove required attribute for documents
        const lengthField = document.getElementById("length");
        const widthField = document.getElementById("width");
        const heightField = document.getElementById("height");

        if (lengthField) lengthField.required = false;
        if (widthField) widthField.required = false;
        if (heightField) heightField.required = false;
    }
}

// Function to validate package details before form submission
function validatePackageDetails() {
    const packageType = document.getElementById("package_type");
    const packageNumber = document.getElementById("package_number");
    const weight = document.getElementById("weight");

    // Check if all required elements exist
    if (!packageType) {
        showError("Package type element not found");
        return false;
    }

    if (!packageNumber) {
        showError("Package number element not found");
        return false;
    }

    if (!weight) {
        showError("Weight element not found");
        return false;
    }

    if (!packageType.value) {
        showError("Please select a package type (Boxes or Documents)");
        return false;
    }

    if (!packageNumber.value || packageNumber.value < 1) {
        showError("Please enter a valid number (minimum 1)");
        return false;
    }

    if (!weight.value || weight.value <= 0) {
        showError("Please enter a valid weight in kg");
        return false;
    }

    // Validate dimensions for boxes
    if (packageType.value === "boxes") {
        const length = document.getElementById("length");
        const width = document.getElementById("width");
        const height = document.getElementById("height");

        // Check if dimension elements exist
        if (!length || !width || !height) {
            showError("Dimension elements not found");
            return false;
        }

        if (!length.value || !width.value || !height.value) {
            showError(
                "Please enter dimensions (length, width, height) for the boxes"
            );
            return false;
        }

        if (length.value <= 0 || width.value <= 0 || height.value <= 0) {
            showError("Dimensions must be greater than 0");
            return false;
        }
    }

    // Validate terms and conditions acceptance
    const acceptTerms = document.getElementById("accept_terms");
    if (!acceptTerms || !acceptTerms.checked) {
        showError("Please accept the terms and conditions to continue");
        return false;
    }

    return true;
}

// Function to get package details for form submission
function getPackageDetails() {
    const packageType = document.getElementById("package_type");
    const packageNumber = document.getElementById("package_number");
    const weight = document.getElementById("weight");
    const packageDescription = document.getElementById("package_description");

    // Check if all required elements exist
    if (!packageType || !packageNumber || !weight) {
        return null;
    }

    const packageData = {
        type: packageType ? packageType.value : "",
        number: packageNumber ? parseInt(packageNumber.value) : 1,
        weight: weight ? parseFloat(weight.value) : 0,
        description: packageDescription ? packageDescription.value : "",
    };

    // Add dimensions for boxes
    if (packageType && packageType.value === "boxes") {
        const length = document.getElementById("length");
        const width = document.getElementById("width");
        const height = document.getElementById("height");

        // Check if dimension elements exist
        if (length && width && height) {
            packageData.dimensions = {
                length: parseFloat(length.value) || 0,
                width: parseFloat(width.value) || 0,
                height: parseFloat(height.value) || 0,
            };
        } else {
            console.error("Dimension elements not found for boxes");
        }
    }

    return packageData;
}

// Payment Details Functions
function setupPaymentDetails() {
    const codCheckbox = document.getElementById("cash_on_delivery");
    const codDetails = document.getElementById("cod_details");

    if (!codCheckbox) return;

    // Add event listener for COD checkbox
    codCheckbox.addEventListener("change", function () {
        if (this.checked) {
            showCodDetails();
        } else {
            hideCodDetails();
        }
    });

    // Initialize COD details display
    if (codCheckbox.checked) {
        showCodDetails();
    }
}

function showCodDetails() {
    const codDetails = document.getElementById("cod_details");
    if (!codDetails) return;

    codDetails.style.display = "block";

    // Calculate and display COD information
    updateCodDisplay();
}

function hideCodDetails() {
    const codDetails = document.getElementById("cod_details");
    if (codDetails) {
        codDetails.style.display = "none";
    }
}

function updateCodDisplay() {
    if (!selectedCompany) return;

    const codPrice = selectedCompany.codPrice || 0;
    const packageData = getPackageDetails();
    const baseAmount = packageData ? calculateBaseShippingCost(packageData) : 0;

    // Calculate total COD cost for all receivers
    const totalCodCost = codPrice * selectedReceivers.length;
    const totalWithCod = baseAmount + totalCodCost;

    // Update COD price display
    const codPriceDisplay = document.getElementById("cod_price_display");
    if (codPriceDisplay) {
        codPriceDisplay.textContent = `$${codPrice.toFixed(2)} per receiver`;
    }

    // Update total with COD display
    const totalWithCodDisplay = document.getElementById(
        "total_with_cod_display"
    );
    if (totalWithCodDisplay) {
        totalWithCodDisplay.textContent = `$${totalWithCod.toFixed(
            2
        )} (Base: $${baseAmount.toFixed(2)} + COD: $${totalCodCost.toFixed(
            2
        )})`;
    }
}

// Final Summary Functions
function showFinalSummary() {
    const summaryContainer = document.getElementById("final-shipment-summary");
    if (!summaryContainer) return;

    const summaryHTML = generateFinalSummaryHTML();
    summaryContainer.innerHTML = summaryHTML;
}

function generateFinalSummaryHTML() {
    if (!selectedCompany || !selectedMethod) {
        return '<div class="alert alert-danger">Error: Missing shipping company or method data</div>';
    }

    const packageData = getPackageDetails();
    if (!packageData) {
        showError(
            "Failed to get package details. Please check all required fields."
        );
        return '<div class="alert alert-danger">Error: Failed to get package details</div>';
    }

    const codCheckbox = document.getElementById("cash_on_delivery");
    const isCod = codCheckbox && codCheckbox.checked;
    const codPrice = isCod ? selectedCompany.codPrice || 0 : 0;

    // Calculate base shipping cost
    const baseAmount = calculateBaseShippingCost(packageData);

    let summaryHTML = `
        <div class="final-shipment-summary">
            <div class="summary-section">
                <h6><i class="fas fa-building"></i> {{ __('admin.shipping_company') }}</h6>
                <div class="summary-item">
                    <span class="label">Company:</span>
                    <span class="value">${selectedCompany.name}</span>
                </div>
                <div class="summary-item">
                    <span class="label">Service:</span>
                    <span class="value">${selectedCompany.serviceName}</span>
                </div>
                <div class="summary-item">
                    <span class="label">Method:</span>
                    <span class="value">${selectedMethod}</span>
                </div>
            </div>
            
            <div class="summary-section">
                <h6><i class="fas fa-user"></i> {{ __('admin.user_information') }}</h6>
                <div class="summary-item">
                    <span class="label">Name:</span>
                    <span class="value">${
                        document.getElementById("user_name")?.value || "N/A"
                    }</span>
                </div>
                <div class="summary-item">
                    <span class="label">Phone:</span>
                    <span class="value">${
                        document.getElementById("user_phone")?.value || "N/A"
                    }</span>
                </div>
            </div>
            
            <div class="summary-section">
                <h6><i class="fas fa-users"></i> {{ __('admin.receivers') }} (${
                    selectedReceivers.length
                })</h6>
    `;

    // Add each receiver with their individual costs
    selectedReceivers.forEach((receiver, index) => {
        const receiverCost = calculateReceiverCost(receiver, packageData);
        summaryHTML += `
            <div class="receiver-summary mb-3 p-3" style="background: #f8f9fa; border-radius: 5px;">
                <h6 class="mb-2">Receiver ${index + 1}: ${receiver.name}</h6>
                <div class="summary-item">
                    <span class="label">Phone:</span>
                    <span class="value">${receiver.phone}</span>
                </div>
                <div class="summary-item">
                    <span class="label">City:</span>
                    <span class="value">${
                        receiver.cityName || receiver.city
                    }</span>
                </div>
                <div class="summary-item">
                    <span class="label">Country:</span>
                    <span class="value">${
                        receiver.countryName || receiver.country
                    }</span>
                </div>
                <div class="summary-item">
                    <span class="label">Bill of Lading:</span>
                    <span class="value">BL-${generateBillOfLading(
                        receiver,
                        index + 1
                    )}</span>
                </div>
                <div class="summary-item">
                    <span class="label">Individual Cost:</span>
                    <span class="value">$${receiverCost.toFixed(2)}</span>
                </div>
                ${
                    isCod && selectedCompany.codPrice
                        ? `
                <div class="summary-item">
                    <span class="label">Cost Breakdown:</span>
                    <span class="value">
                        Shipping: $${(
                            receiverCost - selectedCompany.codPrice
                        ).toFixed(2)} + 
                        COD: $${selectedCompany.codPrice.toFixed(2)}
                    </span>
                </div>
                `
                        : ""
                }
            </div>
        `;
    });

    summaryHTML += `
            </div>
            
            <div class="summary-section">
                <h6><i class="fas fa-box"></i> {{ __('admin.shipping_details') }}</h6>
                <div class="summary-item">
                    <span class="label">Package Type:</span>
                    <span class="value">${packageData.type}</span>
                </div>
                <div class="summary-item">
                    <span class="label">Number:</span>
                    <span class="value">${packageData.number}</span>
                </div>
                <div class="summary-item">
                    <span class="label">Weight:</span>
                    <span class="value">${packageData.weight} kg</span>
                </div>
                <div class="summary-item">
                    <span class="label">Dimensions:</span>
                    <span class="value">${
                        packageData.dimensions &&
                        packageData.dimensions.length &&
                        packageData.dimensions.width &&
                        packageData.dimensions.height
                            ? `${packageData.dimensions.length} × ${packageData.dimensions.width} × ${packageData.dimensions.height} cm`
                            : "N/A"
                    }</span>
                </div>
                <div class="summary-item">
                    <span class="label">Description:</span>
                    <span class="value">${
                        packageData.description || "N/A"
                    }</span>
                </div>
            </div>
            
            <div class="summary-section">
                <h6><i class="fas fa-credit-card"></i> {{ __('admin.payment_details') }}</h6>
                <div class="summary-item">
                    <span class="label">Cash on Delivery:</span>
                    <span class="value">${isCod ? "Yes" : "No"}</span>
                </div>
                ${
                    isCod
                        ? `
                <div class="summary-item">
                    <span class="label">COD Fee:</span>
                    <span class="value">$${codPrice.toFixed(2)}</span>
                </div>
                `
                        : ""
                }
            </div>
            
            <div class="summary-total">
                <h5><i class="fas fa-calculator"></i> {{ __('admin.cost_breakdown') }}</h5>
                <div class="summary-item">
                    <span class="label">Weight:</span>
                    <span class="value">${packageData.weight} kg</span>
                </div>
                <div class="summary-item">
                    <span class="label">Base Price per kg:</span>
                    <span class="value">$${
                        selectedCompany.localPrice || 0
                    }</span>
                </div>
                ${
                    packageData.weight > 1 && selectedCompany.extraWeightPrice
                        ? `
                <div class="summary-item">
                    <span class="label">Extra Weight Fee:</span>
                    <span class="value">$${(
                        (packageData.weight - 1) *
                        selectedCompany.extraWeightPrice
                    ).toFixed(2)}</span>
                </div>
                `
                        : ""
                }
                ${
                    selectedCompany.shipmentFees
                        ? `
                <div class="summary-item">
                    <span class="label">Shipment Fees:</span>
                    <span class="value">$${selectedCompany.shipmentFees.toFixed(
                        2
                    )}</span>
                </div>
                `
                        : ""
                }
                ${
                    selectedCompany.fuelPercentage
                        ? `
                <div class="summary-item">
                    <span class="label">Fuel Surcharge (${
                        selectedCompany.fuelPercentage
                    }%):</span>
                    <span class="value">$${(
                        baseAmount *
                        (selectedCompany.fuelPercentage / 100)
                    ).toFixed(2)}</span>
                </div>
                `
                        : ""
                }
                <div class="summary-item">
                    <span class="label">Base Shipping Cost:</span>
                    <span class="value">$${baseAmount.toFixed(2)}</span>
                </div>
                ${
                    isCod
                        ? `
                <div class="summary-item">
                    <span class="label">COD Fee per Receiver:</span>
                    <span class="value">$${selectedCompany.codPrice.toFixed(
                        2
                    )}</span>
                </div>
                <div class="summary-item">
                    <span class="label">Total COD Fees (${
                        selectedReceivers.length
                    } receivers):</span>
                    <span class="value">$${(
                        selectedCompany.codPrice * selectedReceivers.length
                    ).toFixed(2)}</span>
                </div>
                `
                        : ""
                }
                <div class="summary-item">
                    <span class="label">Total Cost:</span>
                    <span class="value"><strong>$${(
                        baseAmount +
                        (isCod
                            ? selectedCompany.codPrice *
                              selectedReceivers.length
                            : 0)
                    ).toFixed(2)}</strong></span>
                </div>
            </div>
        </div>
    `;

    return summaryHTML;
}

// Function to display company pricing information
function displayCompanyPricing() {
    if (!selectedCompany) return;

    const companiesContainer = document.getElementById("companies-container");
    if (!companiesContainer) return;

    // Add pricing information below the company selection
    const pricingHTML = `
        <div class="company-pricing-info mt-4 p-3 bg-light rounded">
            <h6 class="text-primary mb-3">
                <i class="fas fa-dollar-sign"></i> Pricing Information
            </h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="pricing-item">
                        <strong>Base Price per kg:</strong> 
                        <span class="text-success">$${
                            selectedCompany.localPrice || 0
                        }</span>
                    </div>
                    ${
                        selectedCompany.extraWeightPrice
                            ? `
                    <div class="pricing-item">
                        <strong>Extra Weight Price:</strong> 
                        <span class="text-success">$${selectedCompany.extraWeightPrice}/kg</span>
                    </div>
                    `
                            : ""
                    }
                </div>
                <div class="col-md-6">
                    ${
                        selectedCompany.shipmentFees
                            ? `
                    <div class="pricing-item">
                        <strong>Shipment Fees:</strong> 
                        <span class="text-success">$${selectedCompany.shipmentFees}</span>
                    </div>
                    `
                            : ""
                    }
                    ${
                        selectedCompany.fuelPercentage
                            ? `
                    <div class="pricing-item">
                        <strong>Fuel Surcharge:</strong> 
                        <span class="text-warning">${selectedCompany.fuelPercentage}%</span>
                    </div>
                    `
                            : ""
                    }
                </div>
            </div>
            ${
                selectedCompany.codPrice
                    ? `
            <div class="mt-3 p-2 bg-info text-white rounded">
                <i class="fas fa-info-circle"></i> 
                <strong>Cash on Delivery Available:</strong> Additional fee of $${selectedCompany.codPrice} per receiver
            </div>
            `
                    : ""
            }
        </div>
    `;

    // Insert pricing info after the company cards
    const existingPricing = companiesContainer.querySelector(
        ".company-pricing-info"
    );
    if (existingPricing) {
        existingPricing.remove();
    }

    companiesContainer.insertAdjacentHTML("beforeend", pricingHTML);
}

// Function to generate unique Bill of Lading number
function generateBillOfLading(receiver, receiverIndex) {
    const timestamp = Date.now();
    const date = new Date();
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    const receiverId = receiver.id || receiverIndex;

    // Format: YYYYMMDD-HHMMSS-RECEIVERID
    return `${year}${month}${day}-${timestamp}-${receiverId}`;
}

// Function to calculate base shipping cost
function calculateBaseShippingCost(packageData) {
    if (!selectedCompany || !packageData) return 0;

    let baseCost = 0;
    const weight = parseFloat(packageData.weight) || 0;

    // Get base price per kg from company
    const basePricePerKg = selectedCompany.localPrice || 0;

    // Calculate base cost for the weight
    baseCost = weight * basePricePerKg;

    // Add extra weight fees if applicable
    if (selectedCompany.extraWeightPrice && weight > 1) {
        const extraWeight = weight - 1; // Extra weight beyond 1kg
        const extraCost = extraWeight * selectedCompany.extraWeightPrice;
        baseCost += extraCost;
    }

    // Add any additional fees
    if (selectedCompany.shipmentFees) {
        baseCost += selectedCompany.shipmentFees;
    }

    // Add fuel percentage if applicable
    if (selectedCompany.fuelPercentage) {
        const fuelCost = baseCost * (selectedCompany.fuelPercentage / 100);
        baseCost += fuelCost;
    }

    return baseCost;
}

function calculateReceiverCost(receiver, packageData) {
    if (!selectedCompany || !packageData) return 0;

    // Calculate shipping cost based on weight and company pricing
    let baseCost = 0;
    const weight = parseFloat(packageData.weight) || 0;

    // Get base price per kg from company
    const basePricePerKg = selectedCompany.localPrice || 0;

    // Calculate base cost for the weight
    baseCost = weight * basePricePerKg;

    // Add extra weight fees if applicable
    if (selectedCompany.extraWeightPrice && weight > 1) {
        const extraWeight = weight - 1; // Extra weight beyond 1kg
        const extraCost = extraWeight * selectedCompany.extraWeightPrice;
        baseCost += extraCost;
    }

    // Add any additional fees
    if (selectedCompany.shipmentFees) {
        baseCost += selectedCompany.shipmentFees;
    }

    // Add fuel percentage if applicable
    if (selectedCompany.fuelPercentage) {
        const fuelCost = baseCost * (selectedCompany.fuelPercentage / 100);
        baseCost += fuelCost;
    }

    // Divide by number of receivers if multiple
    const receiverCount = selectedReceivers.length;
    const baseCostPerReceiver =
        receiverCount > 0 ? baseCost / receiverCount : baseCost;

    // Add COD fee per receiver if COD is selected
    const codCheckbox = document.getElementById("cash_on_delivery");
    if (codCheckbox && codCheckbox.checked && selectedCompany.codPrice) {
        return baseCostPerReceiver + selectedCompany.codPrice;
    }

    return baseCostPerReceiver;
}

// Update the validateForm function to handle the new flow
function validateForm() {
    // First validate package details
    if (!validatePackageDetails()) {
        return false;
    }

    // Validate terms and conditions
    const acceptTerms = document.getElementById("accept_terms");
    if (!acceptTerms || !acceptTerms.checked) {
        showError("Please accept the terms and conditions to continue");
        return false;
    }

    // Validate that at least one receiver is selected
    if (selectedReceivers.length === 0) {
        showError("Please add at least one receiver to the shipment");
        return false;
    }

    // Validate shipping company and method
    if (!selectedCompany || !selectedMethod) {
        showError("Please select a shipping company and method");
        return false;
    }

    // If all validations pass, populate the form with final data
    populateFinalFormData();
    return true;
}

// Update the populateFinalFormData function to include payment details
function populateFinalFormData() {
    // Get package details
    const packageData = getPackageDetails();
    if (!packageData) {
        showError(
            "Failed to get package details. Please check all required fields."
        );
        return;
    }

    // Get payment details
    const codCheckbox = document.getElementById("cash_on_delivery");
    const isCod = codCheckbox && codCheckbox.checked;
    const codPrice = isCod ? selectedCompany?.codPrice || 0 : 0;

    // Create a hidden field for package data if it doesn't exist
    let packageDataField = document.getElementById("package_data_hidden");
    if (!packageDataField) {
        packageDataField = document.createElement("input");
        packageDataField.type = "hidden";
        packageDataField.name = "package_data";
        packageDataField.id = "package_data_hidden";
        document.querySelector("form").appendChild(packageDataField);
    }

    // Create a hidden field for payment data if it doesn't exist
    let paymentDataField = document.getElementById("payment_data_hidden");
    if (!paymentDataField) {
        paymentDataField = document.createElement("input");
        paymentDataField.type = "hidden";
        paymentDataField.name = "payment_data";
        paymentDataField.id = "payment_data_hidden";
        document.querySelector("form").appendChild(paymentDataField);
    }

    // Set the package data
    packageDataField.value = JSON.stringify(packageData);

    // Set the payment data
    paymentDataField.value = JSON.stringify({
        isCod: isCod,
        codPrice: codPrice,
        totalAmount:
            baseAmount + (isCod ? codPrice * selectedReceivers.length : 0),
    });

    // Ensure all other hidden fields are populated
    populateShippingFormFields();
}

// Function to populate hidden form fields with shipping data
function populateShippingFormFields() {
    // Check if required elements exist
    const shippingCompanyIdField = document.getElementById(
        "shipping_company_id"
    );
    const shippingMethodField = document.getElementById("shipping_method");
    const selectedReceiversHiddenField = document.getElementById(
        "selected_receivers_hidden"
    );

    if (
        !shippingCompanyIdField ||
        !shippingMethodField ||
        !selectedReceiversHiddenField
    ) {
        console.error("Required form fields not found");
        return;
    }

    // Populate hidden fields with selected data
    shippingCompanyIdField.value = selectedCompany.id;
    shippingMethodField.value = selectedMethod;

    // Populate selected receivers data
    const receiversData = JSON.stringify(selectedReceivers);
    selectedReceiversHiddenField.value = receiversData;

    // Setup package type handling
    setupPackageTypeHandling();
}

// Function to get final shipping data for form submission
function getFinalShippingData() {
    if (!selectedCompany || !selectedMethod) return null;

    return {
        companyId: selectedCompany.id,
        companyName: selectedCompany.name,
        serviceName: selectedCompany.serviceName,
        shippingMethod: selectedMethod,
        receivers: getAllReceiversData(),
        codPrice: selectedCompany.codPrice || 0,
    };
}

// Function to show final shipment summary (legacy function for compatibility)
function showFinalShipmentSummary(shippingData) {
    // This function is now replaced by showFinalSummary
    // Keep for compatibility with existing code
    console.log("Final shipment data:", shippingData);
}

// Function to handle location fields based on selected company
function setupLocationFields() {
    if (!selectedCompany) return;

    const stateField = document.getElementById("state");
    const cityField = document.getElementById("city");
    const countryField = document.getElementById("country");

    if (selectedCompany.hasState) {
        // Company requires state selection
        if (stateField) {
            stateField.style.display = "block";
            stateField.required = true;
        }
        if (cityField) {
            cityField.style.display = "block";
            cityField.required = true;
        }
        if (countryField) {
            countryField.style.display = "block";
            countryField.required = true;
        }
    } else {
        // Company only needs city and country
        if (stateField) {
            stateField.style.display = "none";
            stateField.required = false;
            stateField.value = "";
        }
        if (cityField) {
            cityField.style.display = "block";
            cityField.required = true;
        }
        if (countryField) {
            countryField.style.display = "block";
            countryField.required = true;
        }
    }

    // Handle language restrictions based on isEnglish
    if (!selectedCompany.isEnglish) {
        // Only Arabic allowed
        const languageNote = document.getElementById("language-note");
        if (languageNote) {
            languageNote.style.display = "block";
            languageNote.textContent = "يرجى إدخال البيانات باللغة العربية فقط";
        }
    } else {
        // Both Arabic and English allowed
        const languageNote = document.getElementById("language-note");
        if (languageNote) {
            languageNote.style.display = "none";
        }
    }
}

// Function to display company summary information
function displayCompanySummary() {
    if (!selectedCompany) return;

    const summaryContainer = document.getElementById("company-summary");
    if (!summaryContainer) return;

    let summaryHTML = `
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">${selectedCompany.name} - ${
        selectedMethod === "local" ? "Local" : "International"
    } Shipping</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Service:</strong> ${
                            selectedCompany.serviceName || "N/A"
                        }</p>
                        <p><strong>Method:</strong> ${selectedMethod}</p>
                        <p><strong>Location Type:</strong> ${
                            selectedCompany.hasState
                                ? "Country + State + City"
                                : "Country + City"
                        }</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Language Support:</strong> ${
                            selectedCompany.isEnglish
                                ? "English & Arabic"
                                : "Arabic Only"
                        }</p>
                        <p><strong>COD Available:</strong> ${
                            selectedCompany.codPrice ? "Yes" : "No"
                        }</p>
                        ${
                            selectedCompany.codPrice
                                ? `<p><strong>COD Fee:</strong> $${selectedCompany.codPrice}</p>`
                                : ""
                        }
                    </div>
                </div>
            </div>
        </div>
    `;

    summaryContainer.innerHTML = summaryHTML;
}
