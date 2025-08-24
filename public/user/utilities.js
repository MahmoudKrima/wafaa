let currentStep = 1;
let shipmentId = `shipment_${Date.now()}`;
let selectedMethod = null; // Add missing variable declaration
let selectedReceivers = []; // Add missing variable declaration

// Add missing updateStepIndicator function
function updateStepIndicator(step) {
    const steps = document.querySelectorAll(".step");
    steps.forEach((s, i) => {
        const bubble = s.querySelector(".step-number");
        if (!bubble) return;
        bubble.classList.remove("is-current", "is-done");
        if (i + 1 < step) bubble.classList.add("is-done");
        else if (i + 1 === step) bubble.classList.add("is-current");
    });
}

// Initialize the form
function initShippingForm() {
    updateStepIndicator(currentStep);
    setupEventListeners();
    setupReceiverTypeHandling();
    // Don't call fetchShippingCompanies here - let step1.js handle it
    console.log('Shipping form initialized, companies will be loaded by step1.js');
}

// Setup event listeners
function setupEventListeners() {
    const btnNext = document.getElementById("btn-next");
    const btnPrev = document.getElementById("btn-prev");

    if (btnNext) {
        // Only add listener if it doesn't already have one from step1.js
        if (!btnNext.hasAttribute('data-step1-listener')) {
            btnNext.addEventListener("click", handleNextStep);
            console.log('Next button event listener added in utilities.js');
        } else {
            console.log('Next button already has listener from step1.js');
        }
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

// Validate step before proceeding
function validateStep(step) {
    console.log('Validating step:', step);
    switch (step) {
        case 1:
            if (!window.selectedCompany) {
                alert('Please select a shipping company before proceeding.');
                return false;
            }
            console.log('Step 1 validation passed');
            return true;
        case 2:
            if (!window.selectedMethod) {
                alert('Please select a shipping method before proceeding.');
                return false;
            }
            return true;
        case 3:
            return true;
        case 4:
            if (typeof window.canProceedToNextStep === 'function') {
                return window.canProceedToNextStep();
            }
            return true;
        case 5:
            if (typeof window.validatePackageDetails === 'function') {
                return window.validatePackageDetails();
            }
            return true;
        case 6:
            return true;
        default:
            return true;
    }
}

// Handle next step button click
function handleNextStep() {
    console.log('Next button clicked, current step:', currentStep);
    if (!validateStep(currentStep)) {
        console.log('Step validation failed');
        return;
    }
    
    if (currentStep === 1 && window.selectedCompany) {
        console.log('Moving from step 1 to step 2');
        currentStep = 2;
        showStep(currentStep);
        if (typeof window.showMethodSelection === 'function') {
            window.showMethodSelection();
        } else {
            console.log('showMethodSelection function not found, waiting for step2.js to load...');
            setTimeout(() => {
                if (typeof window.showMethodSelection === 'function') {
                    window.showMethodSelection();
                } else {
                    console.error('showMethodSelection function still not available');
                }
            }, 100);
        }
    } else if (currentStep === 2 && window.selectedMethod) {
        currentStep = 3;
        showStep(currentStep);
        if (typeof window.setupLocationFields === 'function') {
            window.setupLocationFields();
        } else {
            console.log('setupLocationFields function not found');
        }
    } else if (currentStep === 3) {
        currentStep = 4;
        showStep(currentStep);
        displayCompanySummary();
    } else if (currentStep === 4) {
        if (typeof window.canProceedToNextStep === 'function' && window.canProceedToNextStep()) {
            currentStep = 5;
            showStep(currentStep);
            if (typeof window.populateShippingFormFields === 'function') {
                window.populateShippingFormFields();
            } else {
                console.log('populateShippingFormFields function not found');
            }
        }
    } else if (currentStep === 5) {
        if (typeof window.validatePackageDetails === 'function' && window.validatePackageDetails()) {
            currentStep = 6;
            showStep(currentStep);
            if (typeof window.setupPaymentDetails === 'function') {
                window.setupPaymentDetails();
            } else {
                console.log('setupPaymentDetails function not found');
            }
        }
    } else if (currentStep === 6) {
        currentStep = 7;
        showStep(currentStep);
        if (typeof window.showFinalSummary === 'function') {
            window.showFinalSummary();
        } else {
            console.log('showFinalSummary function not found');
        }
    }
}

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
    
    if (isCod && window.selectedCompany && selectedReceivers.length > 0) {
        if (typeof updateCodDisplay === 'function') {
            updateCodDisplay();
        }
    }
}

function displayCompanySummary() {
    const summaryContainer = document.getElementById("company-summary");
    if (!summaryContainer || !window.selectedCompany) return;

    let summaryHTML = `
        <div class="company-summary">
                    <h6>${window.translations?.shipping_company || 'Shipping Company'}</h6>
        <div class="summary-item">
            <span class="label">${window.translations?.company || 'Company'}:</span>
                <span class="value">${window.selectedCompany.name}</span>
            </div>
                    <div class="summary-item">
            <span class="label">${window.translations?.method || 'Method'}:</span>
            <span class="value">${window.selectedMethod === "local" ? (window.translations?.local || 'Local') : (window.translations?.international || 'International')}</span>
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
        if (typeof populateShippingFormFields === 'function') {
            populateShippingFormFields();
        }
    } else if (step === 6) {
        if (btnNext) btnNext.style.display = "inline-block";
        if (typeof setupPaymentDetails === 'function') {
            setupPaymentDetails();
        }
    } else if (step === 7) {
        if (btnNext) btnNext.style.display = "none";
        if (typeof showFinalSummary === 'function') {
            showFinalSummary();
        }
    } else {
        if (btnNext) btnNext.style.display = "inline-block";
    }

    if (step === 3) {
        if (typeof handleCompanyRequirements === 'function') {
            handleCompanyRequirements();
        }
    } else if (step === 4) {
        if (typeof loadReceivers === 'function') loadReceivers();
        if (typeof loadReceiverStates === 'function') loadReceiverStates();
        if (typeof loadReceiverCities === 'function') loadReceiverCities();
        if (typeof ensureReceiverStateFieldVisible === 'function') ensureReceiverStateFieldVisible();
        if (typeof setupReceiverFormByShippingType === 'function') setupReceiverFormByShippingType();
        
        if (selectedMethod === "local") {
            setTimeout(() => {
                if (typeof loadSaudiArabiaStates === 'function') {
                    loadSaudiArabiaStates();
                }
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
            window.selectedCompany = null;
            window.selectedMethod = null;
            const pricingContainer = document.getElementById("company-pricing-display");
            if (pricingContainer) {
                pricingContainer.style.display = "none";
            }
            break;
        case 2:
            window.selectedMethod = null;
            if (currentStep >= 4) {
                if (typeof window.setupReceiverFormByShippingType === 'function') {
                    window.setupReceiverFormByShippingType();
                }
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
                if (typeof clearReceiverForm === 'function') clearReceiverForm();
                if (typeof showMultipleReceiverControls === 'function') showMultipleReceiverControls();
                if (typeof ensureReceiverStateFieldVisible === 'function') ensureReceiverStateFieldVisible();
                // resetReceiverFormForExisting(); // This function is implemented in step4.js
            }
        });
    }

    if (newReceiverRadio) {
        newReceiverRadio.addEventListener("change", function () {
            if (this.checked) {
                if (existingSection) existingSection.style.display = "none";
                if (newSection) newSection.style.display = "block";
                if (typeof clearReceiverForm === 'function') clearReceiverForm();
                if (typeof showMultipleReceiverControls === 'function') showMultipleReceiverControls();
                if (typeof ensureReceiverStateFieldVisible === 'function') ensureReceiverStateFieldVisible();
                if (typeof setupReceiverFormByShippingType === 'function') setupReceiverFormByShippingType();
            }
        });
    }

    const receiverSelect = document.getElementById("receiver_select");
    if (receiverSelect) {
        receiverSelect.addEventListener("change", function () {
            const selectedReceiverId = this.value;
            if (selectedReceiverId) {
                if (typeof populateReceiverForm === 'function') populateReceiverForm(selectedReceiverId);
                const newSection = document.getElementById("new_receiver_section");
                if (newSection) newSection.style.display = "block";
            } else {
                if (typeof clearReceiverForm === 'function') clearReceiverForm();
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

// Make functions globally accessible
window.initShippingForm = initShippingForm;
window.showStep = showStep;
window.updateStepIndicator = updateStepIndicator;
window.clearStepData = clearStepData;
window.handleNextStep = handleNextStep;
window.handlePrevStep = handlePrevStep;
window.setupReceiverTypeHandling = setupReceiverTypeHandling;
window.setupEventListeners = setupEventListeners;
