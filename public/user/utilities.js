let currentStep = 1;
let shipmentId = `shipment_${Date.now()}`;

// Initialize the form
function initShippingForm() {
    updateStepIndicator(currentStep);
    setupEventListeners();
    setupReceiverTypeHandling();
    fetchShippingCompanies();
}

// Setup event listeners
function setupEventListeners() {
    const btnNext = document.getElementById("btn-next");
    const btnPrev = document.getElementById("btn-prev");

    if (btnNext) {
        btnNext.addEventListener("click", handleNextStep);
    }

    if (btnPrev) {
        btnPrev.addEventListener("click", handlePrevStep);
    }

    // Add event listener for cash on delivery checkbox
    const cashOnDelivery = document.getElementById("cash_on_delivery");
    if (cashOnDelivery) {
        cashOnDelivery.addEventListener("change", handleCashOnDeliveryChange);
    }
}

// Handle next step button click
function handleNextStep() {
    if (currentStep === 1 && selectedCompany) {
        currentStep = 2;
        showStep(currentStep);
        showMethodSelection();
    } else if (currentStep === 2 && selectedMethod) {
        currentStep = 3;
        showStep(currentStep);
        setupLocationFields();
    } else if (currentStep === 3) {
        currentStep = 4;
        showStep(currentStep);
        displayCompanySummary();
    } else if (currentStep === 4) {
        if (canProceedToNextStep()) {
            currentStep = 5;
            showStep(currentStep);
            populateShippingFormFields();
        }
    } else if (currentStep === 5) {
        if (validatePackageDetails()) {
            currentStep = 6;
            showStep(currentStep);
            setupPaymentDetails();
        }
    } else if (currentStep === 6) {
        currentStep = 7;
        showStep(currentStep);
        showFinalSummary();
    }
}

// Handle previous step button click
function handlePrevStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
    }
}

// Handle cash on delivery change
function handleCashOnDeliveryChange() {
    const codDetails = document.getElementById("cod_details");
    const isCod = this.checked;
    
    if (codDetails) {
        codDetails.style.display = isCod ? "block" : "none";
    }
    
    if (isCod && selectedCompany && selectedReceivers.length > 0) {
        updateCodDisplay();
    }
}

// Display company summary
function displayCompanySummary() {
    const summaryContainer = document.getElementById("company-summary");
    if (!summaryContainer || !selectedCompany) return;

    let summaryHTML = `
        <div class="company-summary">
            <h6>${translations?.shipping_company || 'Shipping Company'}</h6>
            <div class="summary-item">
                <span class="label">${translations?.company || 'Company'}:</span>
                <span class="value">${selectedCompany.name}</span>
            </div>
            <div class="summary-item">
                <span class="label">${translations?.method || 'Method'}:</span>
                <span class="value">${selectedMethod === "local" ? (translations?.local || 'Local') : (translations?.international || 'International')}</span>
            </div>
        </div>
    `;
    summaryContainer.innerHTML = summaryHTML;
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
        if (btnNext) btnNext.style.display = "inline-block";
        populateShippingFormFields();
    } else if (step === 6) {
        if (btnNext) btnNext.style.display = "inline-block";
        setupPaymentDetails();
    } else if (step === 7) {
        if (btnNext) btnNext.style.display = "none";
        showFinalSummary();
    } else {
        if (btnNext) btnNext.style.display = "inline-block";
    }

    if (step === 3) {
        handleCompanyRequirements();
    } else if (step === 4) {
        loadReceivers();
        loadReceiverStates();
        loadReceiverCities();
        ensureReceiverStateFieldVisible();
        setupReceiverFormByShippingType();
        
        if (selectedMethod === "local") {
            setTimeout(() => {
                loadSaudiArabiaStates();
            }, 100);
        }
    }

    if (step < currentStep) {
        clearStepData(step);
    }
}

function clearStepData(step) {
    switch (step) {
        case 1:
            selectedCompany = null;
            selectedMethod = null;
            const pricingContainer = document.getElementById("company-pricing-display");
            if (pricingContainer) {
                pricingContainer.style.display = "none";
            }
            break;
        case 2:
            selectedMethod = null;
            if (currentStep >= 4) {
                setupReceiverFormByShippingType();
            }
            break;
        case 3:
            break;
        case 4:
            break;
    }
}

function setupReceiverTypeHandling() {
    const existingReceiverRadio = document.getElementById("existing_receiver");
    const newReceiverRadio = document.getElementById("new_receiver");
    const existingSection = document.getElementById("existing_receiver_section");
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
                showMultipleReceiverControls();
                ensureReceiverStateFieldVisible();
                // resetReceiverFormForExisting(); // This function is implemented in step4.js
            }
        });
    }

    if (newReceiverRadio) {
        newReceiverRadio.addEventListener("change", function () {
            if (this.checked) {
                if (existingSection) existingSection.style.display = "none";
                if (newSection) newSection.style.display = "block";
                clearReceiverForm();
                showMultipleReceiverControls();
                ensureReceiverStateFieldVisible();
                setupReceiverFormByShippingType();
            }
        });
    }

    const receiverSelect = document.getElementById("receiver_select");
    if (receiverSelect) {
        receiverSelect.addEventListener("change", function () {
            const selectedReceiverId = this.value;
            if (selectedReceiverId) {
                populateReceiverForm(selectedReceiverId);
                const newSection = document.getElementById("new_receiver_section");
                if (newSection) newSection.style.display = "block";
            } else {
                clearReceiverForm();
            }
        });
    }
}

// File upload functionality
var shipmentImageUpload = null;

// Initialize image upload when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('shipmentImage');
    if (imageInput) {
        // Create image preview container
        const previewContainer = document.createElement('div');
        previewContainer.className = 'image-preview-container mt-3';
        previewContainer.style.display = 'none';
        imageInput.parentNode.appendChild(previewContainer);
        
        // Add event listener for file selection
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewContainer.innerHTML = `
                            <div class="position-relative d-inline-block">
                                <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 200px; max-width: 100%;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                                        onclick="clearImagePreview()" style="margin: 5px;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                        previewContainer.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert('Please select an image file.');
                    this.value = '';
                }
            }
        });
    }
});

// Function to clear image preview
function clearImagePreview() {
    const imageInput = document.getElementById('shipmentImage');
    const previewContainer = document.querySelector('.image-preview-container');
    if (imageInput) imageInput.value = '';
    if (previewContainer) {
        previewContainer.innerHTML = '';
        previewContainer.style.display = 'none';
    }
}
